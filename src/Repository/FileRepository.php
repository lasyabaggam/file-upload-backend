<?php

namespace App\Repository;

use App\Entity\File;
use App\Model\FileDto;
use App\Model\FileInterface;
use App\Repository\Traits\FetchRecordsTrait;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<File>
 */
class FileRepository extends ServiceEntityRepository implements FileInterface
{
    use FetchRecordsTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function findWithPagination(FileDto $data): array
    {
        $data->table = 'file';
        
        return $this->search($data);
    }
}
