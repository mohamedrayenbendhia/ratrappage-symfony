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
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

    /**
     * Trouve les utilisateurs par rôle
     */
    public function findUsersByRole(string $role): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%"' . $role . '"%')
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve tous les utilisateurs sauf l'utilisateur connecté
     */
    public function findAllExceptCurrentUser(int $currentUserId): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id != :currentUserId')
            ->setParameter('currentUserId', $currentUserId)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les utilisateurs par rôle sauf l'utilisateur connecté
     */
    public function findUsersByRoleExceptCurrent(string $role, int $currentUserId): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->andWhere('u.id != :currentUserId')
            ->setParameter('role', '%"' . $role . '"%')
            ->setParameter('currentUserId', $currentUserId)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Crée un QueryBuilder pour la recherche avec pagination
     */
    public function createSearchQueryBuilder(int $currentUserId, string $search = '', string $roleFilter = '', string $restrictToRole = null)
    {
        $qb = $this->createQueryBuilder('u')
            ->andWhere('u.id != :currentUserId')
            ->setParameter('currentUserId', $currentUserId);

        // Restriction de rôle pour les ADMIN
        if ($restrictToRole) {
            $qb->andWhere('u.roles LIKE :restrictRole')
               ->setParameter('restrictRole', '%"' . $restrictToRole . '"%');
        }

        // Recherche par email ou nom
        if (!empty($search)) {
            $qb->andWhere('u.email LIKE :search OR u.name LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        // Filtre par rôle
        if (!empty($roleFilter)) {
            $qb->andWhere('u.roles LIKE :roleFilter')
               ->setParameter('roleFilter', '%"' . $roleFilter . '"%');
        }

        return $qb->orderBy('u.createdAt', 'DESC');
    }

    /**
     * Trouve tous les clients sauf l'utilisateur donné (objet User)
     * Exclut les admins et super admins
     */
    public function findUsersExceptCurrent(User $currentUser): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id != :currentUserId')
            ->andWhere('u.roles NOT LIKE :adminRole')
            ->andWhere('u.roles NOT LIKE :superAdminRole')
            ->setParameter('currentUserId', $currentUser->getId())
            ->setParameter('adminRole', '%"ROLE_ADMIN"%')
            ->setParameter('superAdminRole', '%"ROLE_SUPER_ADMIN"%')
            ->orderBy('u.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve seulement les clients (utilisateurs avec ROLE_USER uniquement)
     */
    public function findClientsExceptCurrent(User $currentUser): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id != :currentUserId')
            ->andWhere('(u.roles = :emptyRole OR u.roles LIKE :userRole)')
            ->andWhere('u.roles NOT LIKE :adminRole')
            ->andWhere('u.roles NOT LIKE :superAdminRole')
            ->setParameter('currentUserId', $currentUser->getId())
            ->setParameter('emptyRole', '[]')
            ->setParameter('userRole', '%"ROLE_USER"%')
            ->setParameter('adminRole', '%"ROLE_ADMIN"%')
            ->setParameter('superAdminRole', '%"ROLE_SUPER_ADMIN"%')
            ->orderBy('u.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les stats mensuelles pour une année donnée:
     * - registrations: nombre d'inscriptions par mois
     * - actives: nombre d'utilisateurs actifs par mois (non bloqués avec au moins une connexion)
     */
    public function getMonthlyUserStats(): array
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $meta = $em->getClassMetadata(User::class);

        $table = $meta->getTableName();
        $idCol = $meta->getSingleIdentifierColumnName();
        $createdCol = $meta->getColumnName('createdAt');
        $lastConnCol = $meta->getColumnName('lastConnexion');
        $blockedCol = $meta->getColumnName('isBlocked');

        $currentYear = (int) (new \DateTimeImmutable())->format('Y');

        $platform = $conn->getDatabasePlatform();
        $quote = fn (string $id) => $platform->quoteIdentifier($id);

        $tableExpr = $quote($table) . ' u';
        $idExpr = 'u.' . $quote($idCol);
        $createdExpr = 'u.' . $quote($createdCol);
        $lastConnExpr = 'u.' . $quote($lastConnCol);
        $blockedExprCol = 'u.' . $quote($blockedCol);

        switch ($platform->getName()) {
            case 'postgresql':
                $monthCreated = 'EXTRACT(MONTH FROM ' . $createdExpr . ')';
                $yearCreated = 'EXTRACT(YEAR FROM ' . $createdExpr . ') = :year';
                $monthLast = 'EXTRACT(MONTH FROM ' . $lastConnExpr . ')';
                $yearLast = 'EXTRACT(YEAR FROM ' . $lastConnExpr . ') = :year';
                $blockedFalse = 'false';
                $yearParamKey = 'year';
                $yearParamVal = $currentYear;
                break;
            case 'sqlite':
                $monthCreated = 'CAST(strftime(\'%m\', ' . $createdExpr . ') AS INTEGER)';
                $yearCreated = 'strftime(\'%Y\', ' . $createdExpr . ') = :year_str';
                $monthLast = 'CAST(strftime(\'%m\', ' . $lastConnExpr . ') AS INTEGER)';
                $yearLast = 'strftime(\'%Y\', ' . $lastConnExpr . ') = :year_str';
                $blockedFalse = '0';
                $yearParamKey = 'year_str';
                $yearParamVal = (string) $currentYear;
                break;
            default: // mysql & mariadb
                $monthCreated = 'MONTH(' . $createdExpr . ')';
                $yearCreated = 'YEAR(' . $createdExpr . ') = :year';
                $monthLast = 'MONTH(' . $lastConnExpr . ')';
                $yearLast = 'YEAR(' . $lastConnExpr . ') = :year';
                $blockedFalse = '0';
                $yearParamKey = 'year';
                $yearParamVal = $currentYear;
                break;
        }

        $sqlRegistrations = sprintf(
            'SELECT %s AS month, COUNT(%s) AS registrations FROM %s WHERE %s GROUP BY month ORDER BY month ASC',
            $monthCreated,
            $idExpr,
            $tableExpr,
            $yearCreated
        );

        $params = [$yearParamKey => $yearParamVal];

        $registrationsRaw = $conn->executeQuery($sqlRegistrations, $params)->fetchAllAssociative();

        $whereActive = sprintf(
            '%s IS NOT NULL AND %s = %s AND %s',
            $lastConnExpr,
            $blockedExprCol,
            $blockedFalse,
            $yearLast
        );

        $sqlActives = sprintf(
            'SELECT %s AS month, COUNT(%s) AS actives FROM %s WHERE %s GROUP BY month ORDER BY month ASC',
            $monthLast,
            $idExpr,
            $tableExpr,
            $whereActive
        );

        $activesRaw = $conn->executeQuery($sqlActives, $params)->fetchAllAssociative();

        // Remplissage des 12 mois
        $registrations = array_fill(1, 12, 0);
        foreach ($registrationsRaw as $row) {
            $m = (int) $row['month'];
            if ($m >= 1 && $m <= 12) {
                $registrations[$m] = (int) $row['registrations'];
            }
        }

        $actives = array_fill(1, 12, 0);
        foreach ($activesRaw as $row) {
            $m = (int) $row['month'];
            if ($m >= 1 && $m <= 12) {
                $actives[$m] = (int) $row['actives'];
            }
        }

        return [
            'registrations' => $registrations,
            'actives' => $actives,
        ];
    }

//    /**
//     * @return User[] Returns an array of User objects
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

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * Obtient les statistiques générales des utilisateurs
     */
    public function getGeneralStats(): array
    {
        $totalUsers = $this->count([]);
        $activeUsers = $this->count(['isBlocked' => false]);
        $blockedUsers = $this->count(['isBlocked' => true]);
        
        $admins = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.roles LIKE :admin OR u.roles LIKE :superAdmin')
            ->setParameter('admin', '%"ROLE_ADMIN"%')
            ->setParameter('superAdmin', '%"ROLE_SUPER_ADMIN"%')
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'total' => $totalUsers,
            'active' => $activeUsers,
            'blocked' => $blockedUsers,
            'admins' => (int) $admins,
            'clients' => $totalUsers - (int) $admins,
        ];
    }
}
