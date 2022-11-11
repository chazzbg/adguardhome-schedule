<?php

namespace App\Service;

use App\Entity\Rule;
use App\Entity\Server;
use App\Entity\Trace;
use App\Repository\TraceRepository;
use Doctrine\Persistence\ManagerRegistry;

class Tracer
{
    public function __construct(private ManagerRegistry $registry)
    {
    }

    public function trace(Rule $rule, array $result)
    {
        /** @var TraceRepository $repo */
        $repo = $this->registry->getRepository(Trace::class);

        $trace = new  Trace();
        $trace->setCreatedAt(new \DateTime())
            ->setRule($rule)
            ->setServices($rule->getServices())
            ->setClients($rule->getClients())
            ->setServers(
                $rule->getServers()->map(function (Server $server) {
                    return $server->getHost();
                })->toArray()
            )
            ->setResult($result);

        $repo->save($trace, true);
    }
}