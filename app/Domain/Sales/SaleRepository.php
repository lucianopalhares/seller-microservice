<?php

namespace App\Domain\Sales;

/**
 * Interface SaleRepository
 *
 * Define os métodos para manipulação de vendas no repositório.
 */
interface SaleRepository
{
    /**
     * Salva uma venda no repositório.
     *
     * @param Sale $sale Objeto da venda a ser salva.
     * @return Sale Retorna a venda salva.
     */
    public function save(Sale $sale): Sale;

    /**
     * Busca vendas por um vendedor específico.
     *
     * @param int $sellerId ID do vendedor.
     * @return array Retorna uma lista de vendas associadas ao vendedor.
     */
    public function findBySeller(int $sellerId): array;

    /**
     * Busca todas as vendas no repositório.
     *
     * @return array Retorna uma lista com todas as vendas.
     */
    public function findAll(): array;
}
