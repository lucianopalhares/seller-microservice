<?php

namespace App\Application\Sellers\Services;

use App\Domain\Sellers\SellerRepository;
use App\Domain\Sellers\Seller;
use Illuminate\Support\Facades\Log;

/**
 * Classe responsável pelos serviços relacionados aos vendedores.
 */
class SellerService
{
    /**
     * Repositório de vendedores.
     *
     * @var SellerRepository
     */
    private SellerRepository $sellerRepository;

    /**
     * Mensagem de erro.
     *
     * @var string
     */
    private string $error;

    /**
     * Vendedor atual.
     *
     * @var Seller
     */
    private Seller $seller;

    /**
     * Lista de vendedores.
     *
     * @var array
     */
    private array $sellers;

    /**
     * Construtor da classe SellerService.
     *
     * @param SellerRepository $sellerRepository
     */
    public function __construct(SellerRepository $sellerRepository)
    {
        $this->sellerRepository = $sellerRepository;
    }

    /**
     * Define o vendedor atual.
     *
     * @param Seller $seller
     * @return void
     */
    public function setSeller(Seller $seller): void
    {
        $this->seller = $seller;
    }

    /**
     * Obtém o vendedor atual.
     *
     * @return Seller
     */
    public function getSeller(): Seller
    {
        return $this->seller;
    }

    /**
     * Define a lista de vendedores.
     *
     * @param array $sellers
     * @return void
     */
    public function setSellers(array $sellers): void
    {
        $this->sellers = $sellers;
    }

    /**
     * Obtém os vendedores.
     *
     * @return array
     */
    public function getSellers(): array
    {
        return $this->sellers;
    }

    /**
     * Define a mensagem de erro.
     *
     * @param string $error
     * @return void
     */
    public function setError(string $error): void
    {
        $this->error = $error;
    }

    /**
     * Obtém a mensagem de erro.
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * Verifica se existe uma mensagem de erro.
     *
     * @return bool
     */
    public function errorExists(): bool
    {
        return empty($this->error) === false;
    }

    /**
     * Cria um novo vendedor no sistema.
     *
     * @param string $name Nome do vendedor.
     * @param string $email E-mail do vendedor.
     * @return bool
     */
    public function createSeller(string $name, string $email): bool
    {
        try {
            $seller = new Seller(0, $name, $email);
            $saved = $this->sellerRepository->save($seller);

            $this->setSeller($saved);

            return true;
        } catch (\Exception $e) {
            Log::channel('seller_microservice')->info($e->getMessage(), ['name' => $name, 'email' => $email]);
            return false;
        }
    }

    /**
     * Obtém todos os vendedores com seus detalhes e comissões totais.
     *
     * @return bool
     */
    public function fetchAllSellersWithCommission(): bool
    {
        try {
            $sellers = $this->sellerRepository->fetchAllSellersWithCommission();

            $this->setSellers($sellers);

            return true;
        } catch (\Exception $e) {
            Log::channel('seller_microservice')->info($e->getMessage());
            return false;
        }
    }
}
