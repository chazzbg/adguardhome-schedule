<?php

namespace App\Service;

use Ahc\Cron\Expression;
use App\ApiClient\MultiClient;
use App\Entity\Rule;
use App\Enums\RuleAction;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class ScheduleRunner
{

    /**
     * @param ExpressionCompiler $compiler
     * @param ManagerRegistry $registry
     * @param MultiClient $client
     */
    public function __construct(
        private readonly ExpressionCompiler  $compiler,
        private readonly ManagerRegistry $registry,
        private readonly MultiClient $client,
        private readonly Tracer $tracer,
        private readonly LoggerInterface $logger
    ){

    }

    public function run($force = false)
    {

        $ruleRepo = $this->registry->getRepository(Rule::class);
        $rules = $ruleRepo->findActiveRules();
        if(!$rules){
            $this->logger->info('No Rules to be executed!');
        }

        /** @var Rule $rule */
        foreach ($rules as $rule) {
            $cronExpr = $this->compiler->compile($rule);
            if($force || Expression::isDue($cronExpr, new \DateTime())){
                $this->client->setServers($rule->getServers());
                    $this->logger->info('Executing rule ID:'.$rule->getId());
                    try {
                        if (empty($rule->getClients())) {
                            $result = $this->applyRuleGlobal($rule);
                        } else {
                            $result = $this->applyRuleClients($rule);
                        }
                        $this->tracer->trace($rule, $result);
                    } catch (\Exception $e) {
                        $this->logger->error('Executing rule ID:'.$rule->getId(), $e->getTrace());
                        $this->tracer->trace($rule, [
                            'error'=> $e->getMessage()
                        ], true);
                    }
                    $rule->setAppliedAt(new \DateTime());
                    $ruleRepo->save($rule);
                }
        }

        $this->registry->getManager()->flush();

    }

    /**
     * @param Rule $rule
     * @return array
     */
    private function applyRuleGlobal(Rule $rule): array
    {
        $remoteServices = $this->client->listBlockedServices();
        $currentServices = [];
        foreach ($remoteServices as $serviceList) {
            $currentServices = array_merge($currentServices, $serviceList);
        }
        $currentServices = array_unique($currentServices);
        $toApply = [];
        if($rule->getAct() == RuleAction::ACTION_BLOCK){
            $toApply = array_merge($currentServices, $rule->getServices());
        } elseif($rule->getAct() == RuleAction::ACTION_UNBLOCK) {
            $toApply = array_diff($currentServices, $rule->getServices());
        }

        $toApply = array_unique($toApply);

        return $this->client->blockServices($toApply);
    }

    /**
     * @param Rule $rule
     * @return array
     */
    private function applyRuleClients(Rule $rule): array
    {
        $result = [];
        foreach ($rule->getClients() as $ruleClient) {
            $hostClient = $this->client->getClient($ruleClient['name']);
            $body = [];
            foreach ($hostClient as $host => $client) {
                $toApply = [];
                if($rule->getAct() == RuleAction::ACTION_BLOCK){
                    $toApply = array_merge($client['blocked_services'], $rule->getServices());
                } elseif($rule->getAct() == RuleAction::ACTION_UNBLOCK) {
                    $toApply = array_diff($client['blocked_services'], $rule->getServices());
                }
                $client['blocked_services'] = $toApply;
                $client['use_global_blocked_services'] = !$client['blocked_services'];
                $body[$host] = $client;
            }
            $result[$ruleClient['name']] = $this->client->updateClient($ruleClient['name'], $body);
        }
        return $result;
    }
}