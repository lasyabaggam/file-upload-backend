<?php

// src/Service/FileUploadService.php
namespace App\Service;

use App\Entity\File;
use DateTimeImmutable;
use League\Csv\Reader;
use App\Entity\FileContent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploadService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handleFileUpload(UploadedFile $file): void
    {
        $fileName = $file->getClientOriginalName();
        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setHeaderOffset(0);

        $fileEntity = new File();
        $fileEntity->setFileName($fileName);
        $fileEntity->setTotalRows($csv->count());
        $fileEntity->setCreatedAt(new DateTimeImmutable());
        $this->entityManager->persist($fileEntity);
        $this->entityManager->flush();

        foreach ($csv as $record) {
            $fileContent = new FileContent();
            $fileContent->setFile($fileEntity);
            $fileContent->setProduct($record['product']);
            $fileContent->setCategory($record['category']);
            $fileContent->setColor($record['color']);
            $fileContent->setPrice($record['price']);
            $this->entityManager->persist($fileContent);
        }

        $this->entityManager->flush();
    }
}
