<?php

namespace App\Controller;

use App\Entity\File;
use App\Repository\FileRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name: 'api_')]
class FileController extends AbstractController
{
    private $repository;

    private $fileUploadService;

    public function __construct(EntityManagerInterface $interface, FileUploadService $fileUploadService)
    {
        $this->repository = $interface->getRepository(File::class);
        $this->fileUploadService = $fileUploadService;
    }

    #[Route('/files', name: 'files', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);
        $sort = $request->query->get('sort', 'created_at:asc');

        $files = $this->repository->findWithPagination($page, $limit, $sort);

        return $this->json($files);
    }

    #[Route('/upload', name: 'file_upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        $file = $request->files->get('file');
        if (!$file) return $this->json(['error' => 'No file provided']);

        $mimeType = $file->getMimeType();
        // if ($mimeType !== 'text/csv') return $this->json(['error' => 'Invalid file type']);

        try {
            $this->fileUploadService->handleFileUpload($file);
        } catch (\Exception $e) {
            dd($e);
            return $this->json(['error' => $e->getMessage()]);
        }

        return $this->json(['message' => 'File uploaded successfully']);
    }

}
