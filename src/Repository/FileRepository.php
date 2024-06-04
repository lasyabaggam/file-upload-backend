<?php

namespace App\Repository;

use App\Entity\File;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<File>
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function findWithPagination(int $page, int $limit, string $sort): array
    {
        $queryBuilder = $this->createQueryBuilder('f');

        $sort = explode(':', $sort);
        $queryBuilder->addOrderBy('f.' . $sort[0], $sort[1]);

        $queryBuilder->setFirstResult(($page - 1) * $limit)->setMaxResults($limit);

        $paginator = new Paginator($queryBuilder->getQuery());
        $total = $paginator->count();

        return [
            'total' => $total,
            'per_page' => $limit,
            'current_page' => $page,
            'total_pages' => ceil($total / $limit),
            'data' => iterator_to_array($paginator),
        ];
    }

    //    /**
    //     * @return File[] Returns an array of File objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?File
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
