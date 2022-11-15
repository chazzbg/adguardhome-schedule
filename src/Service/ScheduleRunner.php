<?php

namespace App\Service;

use Ahc\Cron\Expression;
use App\ApiClient\MultiClient;
use App\Entity\Rule;
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

    public function run($dryRun = true, $force = false)
    {
        $ruleRepo = $this->registry->getRepository(Rule::class);
        $rules = $ruleRepo->findActiveRules();
        if(!$rules){
            $this->logger->info('No Rules to be executed!');
        }

        /** @var Rule $rule */
        foreach ($rules as $rule) {
            $this->client->setServers($rule->getServers());
            $cronExpr = $this->compiler->compile($rule);
                if($force || Expression::isDue($cronExpr, new \DateTime())){
                    $this->logger->info('Executing rule ID:'.$rule->getId());
                    try {
                        if (empty($rule->getClients())) {
                            $result = $this->client->blockServices($rule->getServices());
                        } else {
                            $result = [];
                            foreach ($rule->getClients() as $client) {
                                $result[$client] = $this->client->updateClient($client, $rule->getServices());
                            }
                        }
                        $this->tracer->trace($rule, $result);
                    } catch (\Exception $e) {
                        $this->logger->error('Executing rule ID:'.$rule->getId(), $e->getTrace());
                        $this->tracer->trace($rule, [
                            'error'=> $e->getMessage()
                        ]);
                    }
                    $rule->setAppliedAt(new \DateTime());
                    $ruleRepo->save($rule, true);
                }
        }

    }
}