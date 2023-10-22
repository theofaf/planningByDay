<?php

namespace App\Repository;

use App\Entity\Batiment;
use App\Entity\Salle;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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

    public function getSallesWithDisponibilites(Batiment $batiment, DateTime $dateDebut, DateTime $dateFin)
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->andWhere($qb->expr()->gte("DATE_FORMAT(session.dateDebut, '%d/%m/%Y')", 'dateDebut' ))
            ->andWhere($qb->expr()->lte("DATE_FORMAT(session.dateFin, '%d/%m/%Y')", ':dateFin'))
            ->andWhere($qb->expr()->eq('s.batiment', ':batiment'))
            ->join('s.sessions', 'session', Join::WITH , 'session.id IN (s.sessions)')
            ->setParameter('batiment', $batiment->getId())
            ->setParameter('dateDebut', $dateDebut->format('d/m/Y'))
            ->setParameter('dateFin', $dateFin->format('d/m/Y'))
        ;
        return $qb->getQuery()->getResult();
    }
}
