<?php

namespace App\ScyLabs\NeptuneBundle\Repository;

use App\ScyLabs\NeptuneBundle\Entity\ElementType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ElementType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ElementType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ElementType[]    findAll()
 * @method ElementType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElementTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ElementType::class);
    }

//    /**
//     * @return ElementType[] Returns an array of ElementType objects
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
    public function findOneBySomeField($value): ?ElementType
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
