<?php

namespace App\Repository;

use App\Entity\Session;
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
}
