<?php

namespace App\Repository;

use App\Entity\Rule;
use App\Enums\RuleAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rule>
 *
 * @method Rule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rule[]    findAll()
 * @method Rule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rule::class);
    }

    public function save(Rule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Rule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findActiveRules()
    {
        $date = new \DateTimeImmutable();

        $queryBuilder = $this->createQueryBuilder('r');
        $dow = strtolower($date->format('l'));
        $queryBuilder
            ->where($queryBuilder->expr()->eq("r.{$dow}", true))
            ->andWhere($queryBuilder->expr()->eq('r.enabled', true))
            ->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->isNull('r.appliedAt'),
                $queryBuilder->expr()->lt('r.appliedAt', ':date')
            ))
        ->setParameter(':date', $date->modify("-2 min"));
        ;


        return $queryBuilder->getQuery()->execute();
    }

}
