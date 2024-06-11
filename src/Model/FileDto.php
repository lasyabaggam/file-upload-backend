<?php 

namespace App\Model;

final class FileDto
{
    public function __construct(
        public ?string $sort,

        public ?string $search,

        public ?int $limit,

        public ?int $page,

        public ?string $table,

        public ?string $file_id
    ) {
    }
}