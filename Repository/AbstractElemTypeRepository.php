<?php

namespace Scylabs\NeptuneBundle\Repository;

use Scylabs\NeptuneBundle\Entity\AbstractElemType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AbstractElemType|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractElemType|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbstractElemType[]    findAll()
 * @method AbstractElemType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbstractElemTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AbstractElemType::class);
    }

//    /**
//     * @return AbstractElemType[] Returns an array of AbstractElemType objects
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
    public function findOneBySomeField($value): ?AbstractElemType
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
