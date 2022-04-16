<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $source_bank_name;

    #[ORM\Column(type: 'string', length: 255)]
    private $source_bank_branch;

    #[ORM\Column(type: 'string', length: 255)]
    private $source_bank_account;

    #[ORM\Column(type: 'string', length: 255)]
    private $destination_bank_name;

    #[ORM\Column(type: 'string', length: 255)]
    private $destination_bank_branch;

    #[ORM\Column(type: 'string', length: 255)]
    private $destination_bank_account;

    #[ORM\Column(type: 'decimal', precision: 19, scale: 2)]
    private $transaction_amount;

    #[ORM\Column(type: 'datetime')]
    private $transaction_datetime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSourceBankName(): ?string
    {
        return $this->source_bank_name;
    }

    public function setSourceBankName(string $source_bank_name): self
    {
        $this->source_bank_name = $source_bank_name;

        return $this;
    }

    public function getSourceBankBranch(): ?string
    {
        return $this->source_bank_branch;
    }

    public function setSourceBankBranch(string $source_bank_branch): self
    {
        $this->source_bank_branch = $source_bank_branch;

        return $this;
    }

    public function getSourceBankAccount(): ?string
    {
        return $this->source_bank_account;
    }

    public function setSourceBankAccount(string $source_bank_account): self
    {
        $this->source_bank_account = $source_bank_account;

        return $this;
    }

    public function getDestinationBankName(): ?string
    {
        return $this->destination_bank_name;
    }

    public function setDestinationBankName(string $destination_bank_name): self
    {
        $this->destination_bank_name = $destination_bank_name;

        return $this;
    }

    public function getDestinationBankBranch(): ?string
    {
        return $this->destination_bank_branch;
    }

    public function setDestinationBankBranch(string $destination_bank_branch): self
    {
        $this->destination_bank_branch = $destination_bank_branch;

        return $this;
    }

    public function getDestinationBankAccount(): ?string
    {
        return $this->destination_bank_account;
    }

    public function setDestinationBankAccount(string $destination_bank_account): self
    {
        $this->destination_bank_account = $destination_bank_account;

        return $this;
    }

    public function getTransactionAmount(): ?string
    {
        return $this->transaction_amount;
    }

    public function setTransactionAmount(string $transaction_amount): self
    {
        $this->transaction_amount = $transaction_amount;

        return $this;
    }

    public function getTransactionDatetime(): ?\DateTimeInterface
    {
        return $this->transaction_datetime;
    }

    public function setTransactionDatetime(\DateTimeInterface $transaction_datetime): self
    {
        $this->transaction_datetime = $transaction_datetime;

        return $this;
    }
}
