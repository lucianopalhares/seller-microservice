<?php

namespace App\Domain\Sales;

use App\Domain\Sellers\Seller;

class Sale
{
    private int $id;
    private Seller $seller;
    private float $value;
    private float $commission;
    private string $sale_date;

    public function __construct(int $id, Seller $seller, float $value, float $commission, ?string $sale_date = null)
    {
        $this->id = $id;
        $this->seller = $seller;
        $this->value = $value;
        $this->commission = $commission;
        $this->sale_date = $sale_date ?? now()->format('Y-m-d');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getSeller(): Seller
    {
        return $this->seller;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getCommission(): float
    {
        return $this->commission;
    }

    public function setCommission($commission): void
    {
        $this->commission = $commission;
    }

    public function getSaleDate(): string
    {
        return $this->sale_date;
    }

    public function setSaleDate($sale_date): void
    {
        $this->sale_date = $sale_date;
    }
}
