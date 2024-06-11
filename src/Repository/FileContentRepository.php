<?php

namespace App\Repository;

use App\Entity\FileContent;
use App\Model\FileDto;
use App\Model\FileContentInterface;
use App\Repository\Traits\FetchRecordsTrait;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<FileContent>
 */
class FileContentRepository extends ServiceEntityRepository implements FileContentInterface
{
    use FetchRecordsTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileContent::class);
    }

    public function findWithPagination(FileDto $data, int $file_id): array
    {
        $data->table = 'file_content';
        $data->file_id = $file_id;
        return $this->search($data);
    }
}
