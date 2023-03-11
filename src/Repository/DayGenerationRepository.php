<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\DayGeneration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DayGeneration>
 *
 * @method DayGeneration|null find($id, $lockMode = null, $lockVersion = null)
 * @method DayGeneration|null findOneBy(array $criteria, array $orderBy = null)
 * @method DayGeneration[]    findAll()
 * @method DayGeneration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DayGenerationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DayGeneration::class);
    }

    public function save(DayGeneration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DayGeneration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return DayGeneration[]|null
     */
    public function findGenerationBetweenDates(\DateTime $startDate, \DateTime $endDate, Client $client = null): ?array
    {
        $startDateStr = date_format($startDate, 'Y-m-d');
        $endDateStr = date_format($endDate, 'Y-m-d');

        $queryBuilder = $this->createQueryBuilder('d')
            ->where('d.date BETWEEN :startDate AND :endDate')
            ->orderBy('d.date', 'DESC')
            ->setParameters([
                'startDate' => $startDateStr,
                'endDate' => $endDateStr,
            ])
        ;

        if (!is_null($client?->getId())) {
            $queryBuilder->andWhere('d.client = '.$client->getId());
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return DayGeneration[]|null
     */
    public function findLastYearGeneration(Client $client): ?array
    {
        $start = new \DateTime('-13 months');
        $start->setDate((int) $start->format('Y'), (int) $start->format('m'), 1);

        $end = new \DateTime('-1 month');
        $end->setDate((int) $end->format('Y'), (int) $end->format('m'), (int) $end->format('t'));

        $yearGenerations = $this->findGenerationBetweenDates($start, $end, $client);

        return $yearGenerations;
    }
}
