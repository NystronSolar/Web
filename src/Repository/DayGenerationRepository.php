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

//    /**
//     * @return DayGeneration[] Returns an array of DayGeneration objects
//     */
//    public function findByExampleField($value): array
//    {
//    }

//    public function findOneBySomeField($value): ?DayGeneration
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
