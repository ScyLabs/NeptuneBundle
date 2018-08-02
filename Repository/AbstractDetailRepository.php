<?php

namespace ScyLabs\NeptuneBundle\Repository;

use ScyLabs\NeptuneBundle\Entity\AbstractDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AbstractDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbstractDetail[]    findAll()
 * @method AbstractDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbstractDetailRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AbstractDetail::class);
    }

//    /**
//     * @return AbstractDetail[] Returns an array of AbstractDetail objects
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
    public function findOneBySomeField($value): ?AbstractDetail
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
