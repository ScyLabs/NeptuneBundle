<?php

namespace Scylabs\NeptuneBundle\Repository;

use Scylabs\NeptuneBundle\Entity\AbstractElem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AbstractElem|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractElem|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbstractElem[]    findAll()
 * @method AbstractElem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbstractElemRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AbstractElem::class);
    }

//    /**
//     * @return AbstractElem[] Returns an array of AbstractElem objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AbstractElem
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
