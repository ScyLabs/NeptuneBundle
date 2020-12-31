<?php

namespace ScyLabs\NeptuneBundle\Repository;

use ScyLabs\NeptuneBundle\Entity\VideoDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VideoDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method VideoDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method VideoDetail[]    findAll()
 * @method VideoDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoDetail::class);
    }

//    /**
//     * @return VideoDetail[] Returns an array of VideoDetail objects
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
    public function findOneBySomeField($value): ?VideoDetail
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
