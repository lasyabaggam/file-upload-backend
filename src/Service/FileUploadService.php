<?php

// src/Service/FileUploadService.php
namespace App\Service;

use App\Entity\File;
use DateTimeImmutable;
use App\Entity\FileContent;
use Doctrine\ORM\EntityManagerInterface;

class FileUploadService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, FileValidatorService $validator)
    {
        $this->entityManager = $entityManager;
    }

    public function handleFileUpload($csv, string $fileName)
    {   
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

        return $fileEntity->getId();
    }
}
