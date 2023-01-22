<?php

namespace App\Service;

use Ahc\Cron\Expression;
use App\ApiClient\MultiClient;
use App\Entity\Rule;
use App\Enums\RuleAction;
use App\Repository\RuleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Psr\Log\LoggerInterface;

class ScheduleRunner
{

    private RuleRepository|ObjectRepository $repo;
    private bool $forceBlock = false;
    private bool $forceUnblock = false;

    /**
     * @param ExpressionCompiler $compiler
     * @param ManagerRegistry $registry
     * @param MultiClient $client
     * @param Tracer $tracer
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly ExpressionCompiler  $compiler,
        private readonly ManagerRegistry $registry,
        private readonly MultiClient $client,
        private readonly Tracer $tracer,
        private readonly LoggerInterface $logger
    ){

    }

    public function run($forceBlock = false, $forceUnblock = false)
    {

        $this->forceBlock = $forceBlock;
        $this->forceUnblock = $forceUnblock;

        $this->repo = $this->registry->getRepository(Rule::class);
        $rules = $this->repo->findActiveRules();

        if(!$rules){
            $this->logger->info('No Rules to be executed!');
            return;
        }

        $this->applyRules($rules, RuleAction::ACTION_BLOCK);
        $this->applyRules($rules, RuleAction::ACTION_UNBLOCK);
    }

    /**
     * @param Rule $rule
     * @param RuleAction $action
     * @return array
     */
    private function applyRuleGlobal(Rule $rule, RuleAction $action): array
    {
        $remoteServices = $this->client->listBlockedServices();
        $currentServices = [];
        foreach ($remoteServices as $serviceList) {
            $currentServices = array_merge($currentServices, $serviceList);
        }
        $currentServices = array_unique($currentServices);
        $toApply = [];
        if($action === RuleAction::ACTION_BLOCK){
            $toApply = array_merge($currentServices, $rule->getServices());
        } elseif($action === RuleAction::ACTION_UNBLOCK) {
            $toApply = array_diff($currentServices, $rule->getServices());
        }

        $toApply = array_unique($toApply);

        return $this->client->blockServices($toApply);
    }

    /**
     * @param Rule $rule
     * @param RuleAction $action
     * @return array
     */
    private function applyRuleClients(Rule $rule, RuleAction $action): array
    {
        $result = [];
        foreach ($rule->getClients() as $ruleClient) {
            $hostClient = $this->client->getClient($ruleClient);
            $body = [];
            foreach ($hostClient as $host => $client) {
                $toApply = [];
                if($action == RuleAction::ACTION_BLOCK){
                    $toApply = array_merge($client['blocked_services'] ?? [], $rule->getServices());
                } elseif($action == RuleAction::ACTION_UNBLOCK) {
                    $toApply = array_diff($client['blocked_services'] ?? [], $rule->getServices());
                }
                $client['blocked_services'] = $toApply;
                $client['use_global_blocked_services'] = !$client['blocked_services'];
                $body[$host] = $client;
            }
            $result[$ruleClient] = $this->client->updateClient($ruleClient, $body);
        }
        return $result;
    }

    /**
     * @param $rules
     * @param RuleAction $action
     * @return void
     */
    private function applyRules($rules, RuleAction $action): void
    {
        /** @var Rule $rule */
        foreach ($rules as $rule) {
            $cronExpr = $this->compiler->compile($rule, $action);
            if (($action == RuleAction::ACTION_BLOCK && $this->forceBlock) ||
                ($action == RuleAction::ACTION_UNBLOCK && $this->forceUnblock) ||
                Expression::isDue($cronExpr, new \DateTime())) {
                $this->client->setServers($rule->getServers());
                $this->logger->info('Executing rule ID:' . $rule->getId());
                try {
                    if (empty($rule->getClients())) {
                        $result = $this->applyRuleGlobal($rule, $action);
                    } else {
                        $result = $this->applyRuleClients($rule, $action);
                    }
                    $this->tracer->trace($rule, $result, $action);
                } catch (\Exception $e) {
                    $this->logger->error('Executing rule ID:' . $rule->getId(), $e->getTrace());
                    $this->tracer->trace($rule, [
                        'error' => $e->getMessage()
                    ], $action, true);
                }
                $rule->setAppliedAt(new \DateTime());
                $this->repo->save($rule);
            }
        }

        $this->registry->getManager()->flush();
    }
}