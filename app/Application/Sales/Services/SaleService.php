<?php

namespace App\Application\Sales\Services;

use App\Domain\Sales\SaleRepository;
use App\Domain\Sales\Sale;
use App\Domain\Sellers\SellerRepository;
use App\Domain\Sellers\Exceptions\SellerNotFoundException;

class SaleService
{
    private SaleRepository $saleRepository;
    private SellerRepository $sellerRepository;

    public function __construct(SaleRepository $saleRepository, SellerRepository $sellerRepository)
    {
        $this->saleRepository = $saleRepository;
        $this->sellerRepository = $sellerRepository;
    }

    public function createSale(int $sellerId, float $value): Sale
    {
        $seller = $this->sellerRepository->findById($sellerId);

        if (!$seller) {
            throw new SellerNotFoundException("Vendedor não encontrado.");
        }

        if ($value <= 0) {
            throw new \InvalidArgumentException("O valor da venda deve ser maior que zero.");
        }

        $commission = round($value * 0.085, 2);

        $sale = new Sale(0, $seller, $value, $commission);
        return $this->saleRepository->save($sale);
    }

    public function getSalesBySeller(int $sellerId): array
    {
        return $this->saleRepository->findBySeller($sellerId);
    }
}
