<?php

namespace App\ScyLabs\NeptuneBundle\Repository;

use App\ScyLabs\NeptuneBundle\Entity\ZoneType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ZoneType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ZoneType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ZoneType[]    findAll()
 * @method ZoneType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ZoneTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ZoneType::class);
    }

//    /**
//     * @return ZoneType[] Returns an array of ZoneType objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('z')
            ->andWhere('z.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('z.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ZoneType
    {
        return $this->createQueryBuilder('z')
            ->andWhere('z.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
