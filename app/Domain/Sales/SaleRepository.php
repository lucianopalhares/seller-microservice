<?php

namespace App\Domain\Sales;

interface SaleRepository
{
    public function save(Sale $sale): Sale;

    public function findBySeller(int $sellerId): array;

    public function findAll(): array;
}
