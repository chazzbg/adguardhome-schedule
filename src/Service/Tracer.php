<?php

namespace App\Service;

use App\Entity\Rule;
use App\Entity\Server;
use App\Entity\Trace;
use App\Enums\RuleAction;
use App\Repository\TraceRepository;
use Doctrine\Persistence\ManagerRegistry;

class Tracer
{
    public function __construct(private ManagerRegistry $registry)
    {
    }

    public function trace(Rule $rule, array $result, RuleAction $action, bool $error = false)
    {
        /** @var TraceRepository $repo */
        $repo = $this->registry->getRepository(Trace::class);

        $action = match ($action){
          RuleAction::ACTION_BLOCK => 'block',
          RuleAction::ACTION_UNBLOCK => 'unblock'
        };
        $trace = new  Trace();
        $trace->setCreatedAt(new \DateTime())
            ->setAct($action)
            ->setRule($rule)
            ->setServices($rule->getServices())
            ->setClients($rule->getClients())
            ->setServers(
                $rule->getServers()->map(function (Server $server) {
                    return $server->getHost();
                })->toArray()
            )
            ->setResult($result)
        ->setError($error);

        $repo->save($trace);
    }
}