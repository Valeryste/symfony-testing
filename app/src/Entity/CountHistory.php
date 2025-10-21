<?php

namespace App\Entity;

use App\Repository\CountHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountHistoryRepository::class)]
#[ORM\Table('count_history')]
class CountHistory extends BaseEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'countHistory')]
    #[ORM\JoinColumn(name: 'product_id', nullable: false)]
    private ?Product $product_id = null;

    #[ORM\Column]
    private ?int $count = null;

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

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }
}
