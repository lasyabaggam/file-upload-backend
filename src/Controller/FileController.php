<?php

namespace App\Controller;

use App\Entity\File;
use App\Model\FileDto;
use App\Entity\FileContent;
use App\Service\FileUploadService;
use App\Service\FileValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use League\Csv\Reader;


#[Route('/api', name: 'api_')]
class FileController extends AbstractController
{
    private $fileInterface;

    private $fileUploadService;

    private $fileValidator;

    public function __construct(EntityManagerInterface $fileInterface, FileUploadService $fileUploadService, FileValidatorService $fileValidator)
    {
        $this->fileInterface = $fileInterface;
        $this->fileUploadService = $fileUploadService;
        $this->fileValidator = $fileValidator;
    }

    #[Route('/files', name: 'files', methods: ['GET'])]
    public function index(
        #[MapQueryString] FileDto $fileDto
    ): JsonResponse {
        $fileRepository = $this->fileInterface->getRepository(File::class);
        $files = $fileRepository->findWithPagination($fileDto);

        return $this->json($files);
    }

    #[Route('/upload', name: 'file_upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        $file = $request->files->get('file');
        
        if (!$file) return $this->json(['error' => 'No file provided']);

        try {
            $fileName = $file->getClientOriginalName();
            
            $csv = Reader::createFromPath($file->getRealPath(), 'r')
                        ->setHeaderOffset(0)
                        ->skipEmptyRecords();

            $this->fileValidator->validate($csv, $fileName);

            $file_id = $this->fileUploadService->handleFileUpload($csv, $fileName);

            return $this->json([
                'file_id' => $file_id,
                'message' => 'File uploaded successfully'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'error_code' => $e->getCode()
            ], $e->getCode());
        }
    }

    #[Route('/files/{file_id}/contents', name: 'file_contents', methods: 'GET')]
    public function getFileContents(
        int $file_id,
        #[MapQueryString] FileDto $fileContentDto,
    ): JsonResponse {
        $fileRepository = $this->fileInterface->getRepository(File::class);
        $file = $fileRepository->find($file_id);

        if (!$file) return $this->json(['error' => 'No file found']);

        $fileContentRepository = $this->fileInterface->getRepository(FileContent::class);
        $content = $fileContentRepository->findWithPagination($fileContentDto, $file_id);

        return $this->json($content);
    }
}
