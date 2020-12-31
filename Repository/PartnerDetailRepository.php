<?php

namespace ScyLabs\NeptuneBundle\Repository;

use ScyLabs\NeptuneBundle\Entity\PartnerDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PartnerDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method PartnerDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method PartnerDetail[]    findAll()
 * @method PartnerDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartnerDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartnerDetail::class);
    }
    public function findByLangAndParentIsActiveAndNotRemoved(string $lang){
        return $this->createQueryBuilder('d')
            ->innerJoin('d.partner','p')
            ->where('d.lang = :lang')
            ->andWhere('p.remove = false')
            ->andWhere('p.active = true')
            ->setParameter('lang',$lang)
            ->getQuery()
            ->getResult()
            ;
    }
    public function customFindOneByParentAndLang($parentId,$lang){
        return $this->createQueryBuilder('d')
            ->innerJoin('d.partner','p')
            ->where('d.lang = :lang')
            ->andWhere('p.id = :parentId')
            ->setParameter('lang',$lang)
            ->setParameter('parentId',$parentId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

//    /**
//     * @return PartnerDetail[] Returns an array of PartnerDetail objects
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
    public function findOneBySomeField($value): ?PartnerDetail
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
