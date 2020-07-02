<?php

namespace ScyLabs\NeptuneBundle\Repository;

use ScyLabs\NeptuneBundle\Entity\ElementDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ElementDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ElementDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ElementDetail[]    findAll()
 * @method ElementDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElementDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ElementDetail::class);
    }
    public function findByLangAndParentIsActiveAndNotRemoved(string $lang){
        return $this->createQueryBuilder('d')
            ->innerJoin('d.element','e')
            ->where('d.lang = :lang')
            ->andWhere('e.remove = false')
            ->andWhere('e.active = true')
            ->setParameter('lang',$lang)
            ->getQuery()
            ->getResult()
            ;
    }
    public function findOneByParentAndLang($parentId,$lang){
        return $this->createQueryBuilder('d')
            ->innerJoin('d.element','e')
            ->where('d.lang = :lang')
            ->andWhere('e.id = :parentId')
            ->setParameter('lang',$lang)
            ->setParameter('parentId',$parentId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
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
