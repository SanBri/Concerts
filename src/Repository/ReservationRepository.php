<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findReservationsByUser($userId, $status)
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.user = :user', 'r.status = :status')
            ->setParameters([
                'user' => $userId,
                'status' => $status,
                ])
            ->orderBy('r.id', 'DESC');
        $query = $qb->getQuery();
        return $query->execute();
    }

    public function findAllReservationsForThisConcert($concert)
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.concert = :concert')
            ->setParameter('concert', $concert);
        $query = $qb->getQuery();
        return $query->execute();
    }
}
