<?php

namespace App\Controller;

use App\Entity\File;
use App\Model\FileDto;
use App\Entity\FileContent;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;

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
    public function index(
        #[MapQueryString] FileDto $fileDto
    ): JsonResponse
    {
        $fileRepository = $this->interface->getRepository(File::class);
        $files = $fileRepository->findWithPagination($fileDto);

        return $this->json($files);
    }

    #[Route('/upload', name: 'file_upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        $file = $request->files->get('file');
        if (!$file) return $this->json(['error' => 'No file provided']);

        $mimeType = $file->getMimeType();
        if ($mimeType !== 'text/csv') return $this->json(['error' => 'Invalid file type']);

        try {
            $file_id = $this->fileUploadService->handleFileUpload($file);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()]);
        }

        return $this->json([
            'file_id' => $file_id,
            'message' => 'File uploaded successfully'
        ]);
    }

    #[Route('/files/{file_id}/contents', name: 'file_contents', methods: 'GET')]
    public function getFileContents(int $file_id,
        #[MapQueryString] FileDto $fileContentDto,
        ): JsonResponse
    {
        $fileRepository = $this->interface->getRepository(File::class);
        $file = $fileRepository->find($file_id);

        if (!$file) return $this->json(['error' => 'No file found']);
        
        $fileContentRepository = $this->interface->getRepository(FileContent::class);
        $content = $fileContentRepository->findWithPagination($fileContentDto, $file_id);

        return $this->json($content);
    }
}
