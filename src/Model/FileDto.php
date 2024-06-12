<?php 

namespace App\Model;

final class FileDto
{
    public ?int $page = 1;
    
    public ?int $limit = 10;
    
    public ?string $sort = 'id:asc';
    
    public function __construct(
        public ?string $table,

        public ?string $file_id,

        public ?string $search
    ) {
    }
}