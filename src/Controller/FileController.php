<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\FileContent;
use App\Service\FileUploadService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name: 'api_')]
class FileController extends AbstractController
{
    private $interface;

    private $fileUploadService;

    public function __construct(EntityManagerInterface $interface, FileUploadService $fileUploadService)
    {
        $this->interface = $interface;
        $this->fileUploadService = $fileUploadService;
    }

    #[Route('/files', name: 'files', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 1000);
        $sort = $request->query->get('sort', 'created_at:desc');

        $repository = $this->interface->getRepository(File::class);
        $files = $repository->findWithPagination($page, $limit, $sort);

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
            $file_id = $this->fileUploadService->handleFileUpload($file);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()]);
        }

        return $this->json([
            'file_id'=> $file_id,
            'message' => 'File uploaded successfully'
        ]);
    }

    #[Route('/files/{file_id}/contents', name: 'file_contents', methods: 'GET')]
    public function getFileContents(int $file_id, Request $request): JsonResponse
    {
        $fileRepository = $this->interface->getRepository(File::class);
        $file = $fileRepository->find($file_id);

        if (!$file) return $this->json(['error' => 'No file found']);

        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 100000);
        $sort = $request->query->get('sort', 'id:asc');
        $search = $request->query->get('search');

        $fileContentRepository = $this->interface->getRepository(FileContent::class);
        $content = $fileContentRepository->findWithPagination($file_id, $page, $limit, $sort, $search);

        return $this->json($content);
    }

}
