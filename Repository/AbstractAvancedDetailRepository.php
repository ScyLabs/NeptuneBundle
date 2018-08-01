<?php

namespace Scylabs\NeptuneBundle\Repository;

use Scylabs\NeptuneBundle\Entity\AbstractAvancedDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AbstractAvancedDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractAvancedDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbstractAvancedDetail[]    findAll()
 * @method AbstractAvancedDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbstractAvancedDetailRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AbstractAvancedDetail::class);
    }

//    /**
//     * @return AbstractAvancedDetail[] Returns an array of AbstractAvancedDetail objects
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
    public function findOneBySomeField($value): ?AbstractAvancedDetail
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
