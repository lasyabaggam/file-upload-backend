<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FileRepository::class)]
class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $file_name = null;

    #[ORM\Column]
    private ?int $total_rows = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @var Collection<int, FileContent>
     */
    #[ORM\OneToMany(targetEntity: FileContent::class, mappedBy: 'file')]
    private Collection $fileContents;

    public function __construct()
    {
        $this->fileContents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->file_name;
    }

    public function setFileName(string $file_name): static
    {
        $this->file_name = $file_name;

        return $this;
    }

    public function getTotalRows(): ?int
    {
        return $this->total_rows;
    }

    public function setTotalRows(int $total_rows): static
    {
        $this->total_rows = $total_rows;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, FileContent>
     */
    public function getFileContents(): Collection
    {
        return $this->fileContents;
    }

    public function addFileContent(FileContent $fileContent): static
    {
        if (!$this->fileContents->contains($fileContent)) {
            $this->fileContents->add($fileContent);
            $fileContent->setFile($this);
        }

        return $this;
    }

    public function removeFileContent(FileContent $fileContent): static
    {
        if ($this->fileContents->removeElement($fileContent)) {
            // set the owning side to null (unless already changed)
            if ($fileContent->getFile() === $this) {
                $fileContent->setFile(null);
            }
        }

        return $this;
    }
}
