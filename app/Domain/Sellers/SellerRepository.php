<?php

namespace App\Domain\Sellers;

interface SellerRepository
{
    public function save(Seller $seller): Seller;

    public function findById(int $id): ?Seller;

    public function findAll(): array;

    public function getAllSellersWithTotalCommission(): array;
}
