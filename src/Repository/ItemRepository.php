<?php

namespace App\Repository;

use App\Entity\Item;
use App\Entity\User;
use App\Enum\ItemStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<\App\Entity\Item>
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.status = :status')
            ->setParameter('status', ItemStatus::AVAILABLE)
            ->orderBy('i.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByCreatedBy(User $created_by): array
    {
         return $this->createQueryBuilder('i')
            ->andWhere('i.created_by = :created_by')
            // ->andWhere('i.status = :status')
            ->setParameter('created_by', $created_by->getId(), 'uuid')
            // ->setParameter('status', ItemStatus::AVAILABLE)
            ->orderBy('i.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function search(string $term, ?User $created_by = null): array
    {
        $qb = $this->createQueryBuilder('i')
            ->andWhere('i.name LIKE :term OR i.description LIKE :term')
            // ->andWhere('i.status = :status')
            ->setParameter('term', '%' . $term . '%')
            // ->setParameter('status', ItemStatus::AVAILABLE)
            ->orderBy('i.created_at', 'DESC');

        if ($created_by) {
            $qb->andWhere('i.created_by = :created_by')
                ->setParameter('created_by', $created_by->getId(), 'uuid');
        }

        return $qb->getQuery()->getResult();
    }

    public function findByLessee(User $lessee): array
    {
        return $this->createQueryBuilder('i')
            ->join('i.leases', 'l')
            ->andWhere('l.lessee = :lessee')
            ->setParameter('lessee', $lessee->getId(), 'uuid')
            ->orderBy('l.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Item[] Returns an array of Item objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Item
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
