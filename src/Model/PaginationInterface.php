<?php

namespace App\Model;

interface PaginationInterface
{
    public function findWithPagination(FileDto $data): array;
}

interface FileContentInterface
{
    public function findWithPagination(FileDto $data, int $file_id): array;
}