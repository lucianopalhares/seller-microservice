<?php

namespace App\Domain\Sales;

use App\Domain\Sellers\Seller;

/**
 * Classe que representa uma Venda no domínio do sistema.
 */
class Sale
{
    /**
     * ID da venda.
     *
     * @var int
     */
    private int $id;

    /**
     * Vendedor associado à venda.
     *
     * @var Seller
     */
    private Seller $seller;

    /**
     * Valor da venda.
     *
     * @var float
     */
    private float $value;

    /**
     * Comissão gerada pela venda.
     *
     * @var float
     */
    private float $commission;

    /**
     * Data da venda.
     *
     * @var string
     */
    private string $sale_date;

    /**
     * Construtor da classe Sale.
     *
     * @param int $id ID da venda.
     * @param Seller $seller Vendedor associado à venda.
     * @param float $value Valor da venda.
     * @param float $commission Comissão gerada pela venda.
     * @param string|null $sale_date Data da venda (opcional). Se não fornecida, será usada a data atual.
     */
    public function __construct(int $id, Seller $seller, float $value, float $commission, ?string $sale_date = null)
    {
        $this->id = $id;
        $this->seller = $seller;
        $this->value = $value;
        $this->commission = $commission;
        $this->sale_date = $sale_date ?? now()->format('Y-m-d');
    }

    /**
     * Obtém o ID da venda.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Define o ID da venda.
     *
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Obtém o vendedor associado à venda.
     *
     * @return Seller
     */
    public function getSeller(): Seller
    {
        return $this->seller;
    }

    /**
     * Obtém o valor da venda.
     *
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * Obtém a comissão gerada pela venda.
     *
     * @return float
     */
    public function getCommission(): float
    {
        return $this->commission;
    }

    /**
     * Define a comissão gerada pela venda.
     *
     * @param float $commission
     * @return void
     */
    public function setCommission(float $commission): void
    {
        $this->commission = $commission;
    }

    /**
     * Obtém a data da venda.
     *
     * @return string
     */
    public function getSaleDate(): string
    {
        return $this->sale_date;
    }

    /**
     * Define a data da venda.
     *
     * @param string $sale_date
     * @return void
     */
    public function setSaleDate(string $sale_date): void
    {
        $this->sale_date = $sale_date;
    }
}
