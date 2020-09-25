<?php

namespace App\Repository;

use App\Entity\Concert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Concert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Concert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Concert[]    findAll()
 * @method Concert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConcertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Concert::class);
    }

    public function findComingConcerts($status)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.status = :status')
            ->setParameter('status', $status)
            ->orderBy('c.date', 'DESC');
        $query = $qb->getQuery();
        return $query->execute();
    }

}
