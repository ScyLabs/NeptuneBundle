<?php

namespace Scylabs\NeptuneBundle\Repository;

use Scylabs\NeptuneBundle\Entity\FieldDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FieldDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method FieldDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method FieldDetail[]    findAll()
 * @method FieldDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FieldDetailRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FieldDetail::class);
    }

//    /**
//     * @return FieldDetail[] Returns an array of FieldDetail objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FieldDetail
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
