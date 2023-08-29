<?php

namespace App\Repository;

use App\Entity\ModuleFormationUtilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ModuleFormationUtilisateur>
 *
 * @method ModuleFormationUtilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModuleFormationUtilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModuleFormationUtilisateur[]    findAll()
 * @method ModuleFormationUtilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleFormationUtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModuleFormationUtilisateur::class);
    }
}
