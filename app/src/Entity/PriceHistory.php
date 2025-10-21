<?php

namespace App\Entity;

use App\Repository\PriceHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriceHistoryRepository::class)]
#[ORM\Table('price_history')]
class PriceHistory extends BaseEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'priceHistories')]
    #[ORM\JoinColumn(name: 'product_id', nullable: false)]
    private ?Product $product_id = null;

    #[ORM\Column]
    private ?int $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductId(): ?Product
    {
        return $this->product_id;
    }

    public function setProductId(?Product $product_id): self
    {
        $this->product_id = $product_id;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }
}
