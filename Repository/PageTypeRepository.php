<?php

namespace App\ScyLabs\NeptuneBundle\Repository;

use App\ScyLabs\NeptuneBundle\Entity\PageType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PageType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageType[]    findAll()
 * @method PageType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PageType::class);
    }

//    /**
//     * @return PageType[] Returns an array of PageType objects
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
    public function findOneBySomeField($value): ?PageType
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
