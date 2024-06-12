<?php

namespace App\Service;

use League\Csv\Reader;

class FileValidatorService
{
    private $file;

    private $filename;

    public function validate(Reader $file, string $filename)
    {
        $this->file = $file;
        $this->filename = $filename;

        $this->validateHeaders();
        $this->validateRecords();
        $this->hasCsvExtension();
    }

    public function validateHeaders()
    {

        $headers = $this->file->getHeader();

        $expectedHeaders = ['product', 'category', 'price', 'color'];

        if (count(array_diff($expectedHeaders, $headers)) === 0)
            throw new \Exception('CSV file does not proper headers', 400);
    }

    public function hasCsvExtension() {
        $extension = pathinfo($this->filename, PATHINFO_EXTENSION);

        if(strtolower($extension) === 'csv')
            throw new \InvalidArgumentException('Uploaded file format is invalid', 400);
    }

    public function validateRecords() {
        if ($this->file->count() === 0)
            throw new \InvalidArgumentException('CSV file does not contain any records', 400);
    }
}
