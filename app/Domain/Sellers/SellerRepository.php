<?php

namespace App\Domain\Sellers;

/**
 * Interface SellerRepository
 *
 * Define os métodos necessários para o repositório de vendedores.
 */
interface SellerRepository
{
    /**
     * Salva um vendedor no repositório.
     *
     * @param Seller $seller Objeto do tipo Seller a ser salvo.
     * @return Seller Retorna o vendedor salvo.
     */
    public function save(Seller $seller): Seller;

    /**
     * Busca um vendedor pelo ID.
     *
     * @param int $id ID do vendedor a ser buscado.
     * @return Seller|null Retorna o vendedor correspondente ou null se não encontrado.
     */
    public function findById(int $id): ?Seller;

    /**
     * Retorna todos os vendedores cadastrados.
     *
     * @return array Lista de todos os vendedores.
     */
    public function findAll(): array;

    /**
     * Busca todos os vendedores juntamente com suas comissões totais.
     *
     * @return array Lista de vendedores com seus respectivos dados e comissões.
     */
    public function fetchAllSellersWithCommission(): array;
}
