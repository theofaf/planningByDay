<?php

namespace App\Repository;

use App\Entity\Batiment;
use App\Entity\Etablissement;
use App\Entity\Salle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Salle>
 *
 * @method Salle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Salle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Salle[]    findAll()
 * @method Salle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SalleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Salle::class);
    }

    public function recupererSallesAvecNbPlaceMini(
        ?int $nbPlaceMinimun = 0,
        ?Batiment $batiment = null
    ) {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->andWhere($qb->expr()->gte('s.nbPlace', ':nbPlaceMinimum'))
            ->setParameter('nbPlaceMinimum', $nbPlaceMinimun)
        ;

        if (null !== $batiment) {
            $qb
                ->andWhere($qb->expr()->eq('s.batiment', ':batiment'))
                ->setParameter('batiment', $batiment->getId())
            ;
        }

        return $qb->getQuery()->getResult();
    }
}
