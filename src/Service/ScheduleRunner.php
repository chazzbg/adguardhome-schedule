<?php

namespace App\Service;

use Ahc\Cron\Expression;
use App\ApiClient\MultiClient;
use App\Entity\Rule;
use Doctrine\Persistence\ManagerRegistry;

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
        private readonly Tracer $tracer
    ){

    }

    public function run($dryRun = true, $force = false)
    {
        $ruleRepo = $this->registry->getRepository(Rule::class);
        $rules = $ruleRepo->findActiveRules();

        /** @var Rule $rule */
        foreach ($rules as $rule) {
            $this->client->setServers($rule->getServers());
            $cronExpr = $this->compiler->compile($rule);
                if($force || !Expression::isDue($cronExpr, new \DateTime())){
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