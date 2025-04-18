<?php

namespace App\Repository;

use App\Entity\FicheFrais;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FicheFrais>
 *
 * @method FicheFrais|null find($id, $lockMode = null, $lockVersion = null)
 * @method FicheFrais|null findOneBy(array $criteria, array $orderBy = null)
 * @method FicheFrais[]    findAll()
 * @method FicheFrais[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FicheFraisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FicheFrais::class);
    }

    public function findByYear($year): ?array
    {
        return $this->createQueryBuilder('ff')
            ->where('ff.mois LIKE :valYear')
            ->setParameter('valYear', $year.'%')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getTop3MontantsValides($mois): array
    {
        return $this->createQueryBuilder('ff')
            ->andWhere('ff.mois = :mois')
            ->setParameter('mois', $mois)
            ->orderBy('ff.montantValid', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }


//    /**
//     * @return FicheFrais[] Returns an array of FicheFrais objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FicheFrais
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
