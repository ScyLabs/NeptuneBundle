<?php

namespace Scylabs\NeptuneBundle\Repository;

use Scylabs\NeptuneBundle\Entity\PageDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PageDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageDetail[]    findAll()
 * @method PageDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageDetailRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PageDetail::class);
    }

//    /**
//     * @return PageDetail[] Returns an array of PageDetail objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PageDetail
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
