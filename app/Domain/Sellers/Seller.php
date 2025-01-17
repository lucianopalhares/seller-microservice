<?php

namespace App\Domain\Sellers;

class Seller
{
    private int $id;
    private string $name;
    private string $email;
    private float $totalCommission = 0.0;

    public function __construct(int $id, string $name, string $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }

    public function getId(): int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }

    // Novo getter para a comissão total
    public function getTotalCommission(): float
    {
        return $this->totalCommission;
    }

    // Novo setter para a comissão total
    public function setTotalCommission(float $totalCommission): void
    {
        $this->totalCommission = $totalCommission;
    }
}
