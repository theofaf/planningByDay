<?php

namespace App\Repository;

use App\Entity\Utilisatuer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisatuer>
 *
 * @method Utilisatuer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisatuer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisatuer[]    findAll()
 * @method Utilisatuer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisatuerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisatuer::class);
    }

//    /**
//     * @return Utilisatuer[] Returns an array of Utilisatuer objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Utilisatuer
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
