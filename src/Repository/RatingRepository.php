<?php

namespace App\Repository;

use App\Entity\Rating;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rating>
 *
 * @method Rating|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rating|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rating[]    findAll()
 * @method Rating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rating::class);
    }

    /**
     * Trouve tous les avis donnés PAR un utilisateur
     */
    public function findRatingsByRater(User $rater): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.rater = :rater')
            ->setParameter('rater', $rater)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve tous les avis reçus PAR un utilisateur
     */
    public function findRatingsForUser(User $rated): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.rated = :rated')
            ->setParameter('rated', $rated)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve tous les utilisateurs qui peuvent être notés (sauf soi-même)
     */
    public function findUsersToRate(User $currentUser): array
    {
        return $this->getEntityManager()
            ->getRepository(User::class)
            ->createQueryBuilder('u')
            ->andWhere('u.id != :currentUser')
            ->setParameter('currentUser', $currentUser->getId())
            ->orderBy('u.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Vérifie si un utilisateur a déjà noté un autre utilisateur
     */
    public function hasUserRated(User $rater, User $rated): bool
    {
        $result = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.rater = :rater')
            ->andWhere('r.rated = :rated')
            ->setParameter('rater', $rater)
            ->setParameter('rated', $rated)
            ->getQuery()
            ->getSingleScalarResult();

        return $result > 0;
    }

    /**
     * Calcule la moyenne des notes pour un utilisateur
     */
    public function getAverageRating(User $user): float
    {
        $result = $this->createQueryBuilder('r')
            ->select('AVG(r.stars)')
            ->andWhere('r.rated = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? round($result, 1) : 0.0;
    }

    /**
     * Compte le nombre total d'avis reçus par un utilisateur
     */
    public function countRatingsForUser(User $user): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.rated = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
