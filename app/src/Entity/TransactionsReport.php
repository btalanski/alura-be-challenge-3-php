<?php

namespace App\Entity;

use App\Repository\TransactionsReportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionsReportRepository::class)]
class TransactionsReport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $file_name;

    #[ORM\Column(type: 'date')]
    private $report_date;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $file_size;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->file_name;
    }

    public function setFileName(string $file_name): self
    {
        $this->file_name = $file_name;

        return $this;
    }

    public function getReportDate(): ?\DateTimeInterface
    {
        return $this->report_date;
    }

    public function setReportDate(\DateTimeInterface $report_date): self
    {
        $this->report_date = $report_date;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getFileSize(): ?int
    {
        return $this->file_size;
    }

    public function setFileSize(?int $file_size): self
    {
        $this->file_size = $file_size;

        return $this;
    }
}
