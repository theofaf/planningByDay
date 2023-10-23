<?php

namespace App\Repository;

use App\Entity\Batiment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Batiment>
 *
 * @method Batiment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Batiment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Batiment[]    findAll()
 * @method Batiment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BatimentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Batiment::class);
    }

    public function findBatimentByFiltres(?string $libelle, ?string $ville, ?string $codePostal)
    {
        $qb = $this->createQueryBuilder('b');

        if (null !== $libelle) {
            $qb
                ->andWhere($qb->expr()->like('b.libelle', ':libelle'))
                ->setParameter('libelle', '%'.$libelle.'%')
            ;
        }

        if (null !== $ville) {
            $qb
                ->andWhere($qb->expr()->like('b.ville', ':ville'))
                ->setParameter('ville', '%'.$ville.'%')
            ;
        }

        if (null !== $codePostal) {
            $qb
                ->andWhere($qb->expr()->like('b.codePostal', ':codePostal'))
                ->setParameter('codePostal', '%'.$codePostal.'%')
            ;
        }

        return $qb->getQuery()->getResult();
    }
}
