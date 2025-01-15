<?php

namespace App\Application\Sellers\Services;

use App\Domain\Sellers\SellerRepository;
use App\Domain\Sellers\Seller;

class SellerService
{
    private SellerRepository $sellerRepository;

    public function __construct(SellerRepository $sellerRepository)
    {
        $this->sellerRepository = $sellerRepository;
    }

    /**
     * Creates a new seller in the system.
     *
     * @param string $name
     * @param string $email
     * @return Seller
     */
    public function createSeller(string $name, string $email): Seller
    {
        $seller = new Seller(0, $name, $email);
        return $this->sellerRepository->save($seller);
    }

    /**
     * Retrieves all sellers with their details and total commissions.
     *
     * @return array
     */
    public function getAllSellers(): array
    {
        return $this->sellerRepository->findAll();
    }

    public function getAllSellersWithCommission(): array
    {
        return $this->sellerRepository->getAllSellersWithTotalCommission();
    }
}
