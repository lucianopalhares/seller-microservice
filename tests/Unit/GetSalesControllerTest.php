<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Application\Sales\Services\SaleService;

class GetSalesControllerTest extends TestCase
{
    public $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    /**
     * A basic unit test example.
     */
    public function testSales(): void
    {
        $this->saleService->fetchAllSales();

        $sales = $this->saleService->getSales();

        if (empty($sales)) {
            echo 'erro';
            return;
        }

        $sales2 = [];

        foreach ($sales as $sale) {
            try {
                $sales2[] = [
                    'id' => $sale->id
                ];
            } catch (\Exception $e) {
                // Registrar erro específico ao publicar a mensagem
                echo 'erro2';
            }
        }

        echo 'foi';
    }
}
