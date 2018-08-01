<?php

namespace Scylabs\NeptuneBundle\Repository;

use Scylabs\NeptuneBundle\Entity\ElementDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ElementDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ElementDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ElementDetail[]    findAll()
 * @method ElementDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElementDetailRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ElementDetail::class);
    }

//    /**
//     * @return ElementDetail[] Returns an array of ElementDetail objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ElementDetail
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
