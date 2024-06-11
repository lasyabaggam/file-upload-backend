<?php

namespace App\Repository\Traits;

use App\Entity\FileContent;
use App\Entity\File;
use App\Model\FileDto;
use Doctrine\ORM\Tools\Pagination\Paginator;

trait FetchRecordsTrait
{
    public function search(FileDto $data)
    {
        $qb =  $this->createQueryBuilder($data->table);

        if ($data->file_id) {
            $qb->select("$data->table")
                ->andWhere("$data->table.file = :file_id")
                ->setParameter('file_id', $data->file_id);
        }

        if ($data->search) {
            $qb =  $this->searchAllFields($qb, $data->search, $data->table);
        }
        $sort = explode(':', $data->sort);
        $qb->orderBy("$data->table.$sort[0]", $sort[1])
            ->setFirstResult(($data->page - 1) * $data->limit)
            ->setMaxResults($data->limit);

            $paginator = new Paginator($qb->getQuery());
        $total = $paginator->count();

        return [
            'total' => $total,
            'per_page' => $data->limit,
            'current_page' => ($total) ? $data->page : 0,
            'total_pages' => ceil($total / $data->limit),
            'data' => iterator_to_array($paginator),
        ];
    }

    //dynamically search through all the fields in the table
    public function searchAllFields($qb, string $keyword, string $table)
    {
        // Get entity fields to dynamically retrieve all fields
        $fields = $this->getClassMetadata()->getFieldNames();

        $orExpr = $qb->expr()->orX();

        foreach ($fields as $fieldName) {
            $orExpr->add($qb->expr()->like("$table." . $fieldName, ':keyword'));
        }

        $qb->andWhere($orExpr)
            ->setParameter('keyword', '%' . $keyword . '%');

        return $qb;
    }
}
