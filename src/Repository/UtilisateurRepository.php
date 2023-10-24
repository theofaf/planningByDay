<?php

namespace App\Repository;

use App\Entity\Etablissement;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 * @implements PasswordUpgraderInterface<Utilisateur>
 *
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findAll()
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Utilisateur) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }


    /**
     * @param Etablissement|int|null $etablissement Etablissement à partir duquel on souhaite récupérer des {@see Utilisateur}
     * @return array|null Retourne l'ensemble des utilisateurs d'un même {@see Etablissement}, excepté {@see Utilisateur} passé en paramètre
     */
    public function recupererUtilisateursMemeEtablissement(
        Etablissement|int|null $etablissement,
    ): ?array {
        if ($etablissement instanceof Etablissement) {
            $etablissement = $etablissement->getId();
        }

        $qb = $this->createQueryBuilder('u');
        $qb
            ->join('u.etablissement', 'e')
            ->andWhere($qb->expr()->eq('e.id', $etablissement));

        return $qb->getQuery()->getResult();
    }

    public function findAllExceptSupportRole(): array
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.roles NOT LIKE :role')
            ->setParameter('role', '%ROLE_SUPPORT%')
            ->getQuery();

        return $qb->getResult();
    }
}
