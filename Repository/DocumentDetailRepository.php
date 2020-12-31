<?php

namespace ScyLabs\NeptuneBundle\Repository;

use ScyLabs\NeptuneBundle\Entity\DocumentDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocumentDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentDetail[]    findAll()
 * @method DocumentDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentDetail::class);
    }
    public function findByLangAndParentIsActiveAndNotRemoved(string $lang){
        return $this->createQueryBuilder('d')
            ->innerJoin('d.document','dc')
            ->where('d.lang = :lang')
            ->andWhere('dc.remove = false')
            ->andWhere('dc.active = true')
            ->setParameter('lang',$lang)
            ->getQuery()
            ->getResult()
            ;
    }
    public function findOneByParentAndLang($parentId,$lang){
        return $this->createQueryBuilder('d')
            ->innerJoin('d.document','dd')
            ->where('d.lang = :lang')
            ->andWhere('dd.id = :parentId')
            ->setParameter('lang',$lang)
            ->setParameter('parentId',$parentId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

//    /**
//     * @return DocumentDetail[] Returns an array of DocumentDetail objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('z')
            ->andWhere('z.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('z.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DocumentDetail
    {
        return $this->createQueryBuilder('z')
            ->andWhere('z.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
