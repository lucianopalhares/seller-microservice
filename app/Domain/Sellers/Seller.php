<?php

namespace App\Domain\Sellers;

/**
 * Classe Seller
 *
 * Representa um vendedor no domínio da aplicação.
 */
class Seller
{
    /**
     * @var int ID do vendedor.
     */
    private int $id;

    /**
     * @var string Nome do vendedor.
     */
    private string $name;

    /**
     * @var string E-mail do vendedor.
     */
    private string $email;

    /**
     * @var float Comissão total acumulada pelo vendedor.
     */
    private float $totalCommission = 0.0;

    /**
     * Construtor da classe Seller.
     *
     * @param int $id ID do vendedor.
     * @param string $name Nome do vendedor.
     * @param string $email E-mail do vendedor.
     */
    public function __construct(int $id, string $name, string $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * Obtém o ID do vendedor.
     *
     * @return int ID do vendedor.
     */
    public function getId(): int { return $this->id; }

    /**
     * Define o ID do vendedor.
     *
     * @param int $id Novo ID do vendedor.
     * @return void
     */
    public function setId(int $id): void { $this->id = $id; }

    /**
     * Obtém o nome do vendedor.
     *
     * @return string Nome do vendedor.
     */
    public function getName(): string { return $this->name; }

    /**
     * Obtém o e-mail do vendedor.
     *
     * @return string E-mail do vendedor.
     */
    public function getEmail(): string { return $this->email; }

    /**
     * Obtém o valor total de comissão acumulada pelo vendedor.
     *
     * @return float Valor da comissão total.
     */
    public function getTotalCommission(): float
    {
        return $this->totalCommission;
    }

    /**
     * Define o valor total de comissão acumulada pelo vendedor.
     *
     * @param float $totalCommission Valor da nova comissão total.
     * @return void
     */
    public function setTotalCommission(float $totalCommission): void
    {
        $this->totalCommission = $totalCommission;
    }
}
