<?php

namespace App\Repository;

use App\Entity\ModuleFormation;
use App\Entity\ModuleFormationUtilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ModuleFormation>
 *
 * @method ModuleFormation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModuleFormation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModuleFormation[]    findAll()
 * @method ModuleFormation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleFormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ModuleFormation::class);
    }

    public function getModulesParCursusId(int $cursusId)
    {
        $qb = $this->createQueryBuilder('m');
        $qb
            ->join('m.listeCursus', 'c', Join::WITH, 'c.id = :cursusId')
            ->setParameter('cursusId', $cursusId)
        ;
        return $qb->getQuery()->getResult();
    }
}
