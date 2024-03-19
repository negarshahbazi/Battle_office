<?php

namespace App\Repository;

use App\Entity\AddressBiling;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AddressBiling>
 *
 * @method AddressBiling|null find($id, $lockMode = null, $lockVersion = null)
 * @method AddressBiling|null findOneBy(array $criteria, array $orderBy = null)
 * @method AddressBiling[]    findAll()
 * @method AddressBiling[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddressBilingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AddressBiling::class);
    }

    //    /**
    //     * @return AddressBiling[] Returns an array of AddressBiling objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?AddressBiling
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
