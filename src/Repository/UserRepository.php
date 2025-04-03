<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Permet de mettre à jour automatiquement le mot de passe haché.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Retourne les utilisateurs ayant un rôle donné.
     * ⚠️ Version compatible SQLite (filtrage PHP).
     */
    public function findByRole(string $role): array
    {
        $users = $this->findAll();

        return array_filter($users, function (User $user) use ($role) {
            return in_array($role, $user->getRoles());
        });
    }

    /**
     * Retourne les utilisateurs visibles pour un admin (pas lui-même, pas les super-admins).
     */
    public function findAllVisibleForAdmin(User $admin): array
    {
        $users = $this->createQueryBuilder('u')
            ->andWhere('u != :admin')
            ->setParameter('admin', $admin)
            ->getQuery()
            ->getResult();

        return array_filter($users, function (User $user) {
            return (
                in_array('ROLE_CLIENT', $user->getRoles()) ||
                in_array('ROLE_ADMIN', $user->getRoles())
            ) && !in_array('ROLE_SUPER_ADMIN', $user->getRoles());
        });
    }

    // 🚧 Fonctions de base générées (commentées)
    // public function findByExampleField($value): array { ... }
    // public function findOneBySomeField($value): ?User { ... }
}
