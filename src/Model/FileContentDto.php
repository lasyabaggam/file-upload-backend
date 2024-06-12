<?php

namespace App\Model;

final class FileContentDto
{
    public function __construct(
        public string $product,

        public string $category,

        public string $color,

        public int $price
    ) {
    }
}
