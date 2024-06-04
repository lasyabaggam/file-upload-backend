<?php

namespace App\Repository;

use App\Entity\FileContent;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<FileContent>
 */
class FileContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileContent::class);
    }

    public function findWithPagination(int $file_id, int $page, int $limit, string $sort, string $search = null): array
    {
        $queryBuilder = $this->createQueryBuilder('fc')
                             ->andWhere('fc.file = :file_id')
                             ->setParameter('file_id', $file_id);

        if ($search) {
            $queryBuilder->andWhere('fc.product LIKE :search OR fc.category LIKE :search OR fc.color LIKE :search')
                         ->setParameter('search', '%' . $search . '%');
        }

        $sort = explode(':', $sort);
        $queryBuilder->addOrderBy('fc.' . $sort[0], $sort[1]);

        $queryBuilder->setFirstResult(($page - 1) * $limit)->setMaxResults($limit);

        $paginator = new Paginator($queryBuilder->getQuery());
        $total = $paginator->count();
        
        return [
            'total' => $total,
            'per_page' => $limit,
            'current_page' => ($total) ? $page : 0,
            'total_pages' => ceil($total / $limit),
            'data' => iterator_to_array($paginator),
        ];
    }

    //    /**
    //     * @return FileContent[] Returns an array of FileContent objects
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

    //    public function findOneBySomeField($value): ?FileContent
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
