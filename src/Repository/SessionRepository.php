<?php

namespace App\Repository;

use App\Entity\Classe;
use App\Entity\Etablissement;
use App\Entity\Session;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function getSessionsParEtablissementId(int $etablissementId)
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->join('s.salle', 'salle')
            ->join('salle.batiment', 'bat')
            ->join('bat.etablissement', 'e')
            ->andWhere($qb->expr()->eq('e.id', ':etablissementId'))
            ->setParameter('etablissementId', $etablissementId)
        ;

        return $qb->getQuery()->getResult();
    }

    public function getSessionsFiltres(?Utilisateur $utilisateur, ?Classe $classe, ?Etablissement $etablissement)
    {
        $qb = $this->createQueryBuilder('s');

        if (null !== $utilisateur?->getId()) {
            $qb
                ->andWhere($qb->expr()->eq('s.utilisateur', ':utilisateurId'))
                ->setParameter('utilisateurId', $utilisateur->getId())
            ;
        }

        if (null !== $classe?->getId()) {
            $qb
                ->andWhere($qb->expr()->eq('s.classe', ':classeId'))
                ->setParameter('classeId', $classe->getId())
            ;
        }

        if (null !== $etablissement?->getId()) {
            $qb
                ->join('s.salle', 'salle')
                ->join('salle.batiment', 'bat')
                ->join('bat.etablissement', 'e')
                ->andWhere($qb->expr()->eq('e.id', ':etablissementId'))
                ->setParameter('etablissementId', $etablissement->getId())
            ;
        }

        return $qb->getQuery()->getResult();
    }
}
