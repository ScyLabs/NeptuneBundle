<?php

namespace ScyLabs\NeptuneBundle\Repository;

use ScyLabs\NeptuneBundle\Entity\ZoneDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ZoneDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ZoneDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ZoneDetail[]    findAll()
 * @method ZoneDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ZoneDetailRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ZoneDetail::class);
    }
    public function findByLangAndParentIsActiveAndNotRemoved(string $lang){
        return $this->createQueryBuilder('d')
            ->innerJoin('d.zone','z')
            ->where('d.lang = :lang')
            ->andWhere('z.remove = false')
            ->andWhere('z.active = true')
            ->setParameter('lang',$lang)
            ->getQuery()
            ->getResult()
            ;
    }
    public function findOneByParentAndLang($parentId,$lang){
        return $this->createQueryBuilder('d')
            ->innerJoin('d.zone','z')
            ->where('d.lang = :lang')
            ->andWhere('z.id = :parentId')
            ->setParameter('lang',$lang)
            ->setParameter('parentId',$parentId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

//    /**
//     * @return ZoneDetail[] Returns an array of ZoneDetail objects
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
    public function findOneBySomeField($value): ?ZoneDetail
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
