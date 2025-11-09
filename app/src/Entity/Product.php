<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table('products')]
class Product extends BaseEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(options: ['unsigned' => true])]
    private ?int $count = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, options: ['unsigned' => true])]
    private ?float $price = null;

    #[ORM\Column(name: 'is_active', nullable: false, options: ['default' => true])]
    private bool $isActive = true;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinTable(name: 'category_product')]
    private Collection $categories;

    /**
     * @var Collection<int, CountHistory>
     */
    #[ORM\OneToMany(targetEntity: CountHistory::class, mappedBy: 'product', cascade: ['persist'], orphanRemoval: true)]
    private Collection $countHistory;

    /**
     * @var Collection<int, PriceHistory>
     */
    #[ORM\OneToMany(targetEntity: PriceHistory::class, mappedBy: 'product', cascade: ['persist'], orphanRemoval: true)]
    private Collection $priceHistory;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->countHistory = new ArrayCollection();
        $this->priceHistory = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addProduct($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeProduct($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, CountHistory>
     */
    public function getCountHistory(): Collection
    {
        return $this->countHistory;
    }

    public function addCountHistory(CountHistory $countHistory): self
    {
        if (!$this->countHistory->contains($countHistory)) {
            $this->countHistory->add($countHistory);
            $countHistory->setProduct($this);
        }

        return $this;
    }

    public function removeCountHistory(CountHistory $countHistory): self
    {
        if ($this->countHistory->removeElement($countHistory)) {
            if ($countHistory->getProduct() === $this) {
                $countHistory->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PriceHistory>
     */
    public function getPriceHistory(): Collection
    {
        return $this->priceHistory;
    }

    public function addPriceHistory(PriceHistory $priceHistory): self
    {
        if (!$this->priceHistory->contains($priceHistory)) {
            $this->priceHistory->add($priceHistory);
            $priceHistory->setProduct($this);
        }

        return $this;
    }

    public function removePriceHistory(PriceHistory $priceHistory): self
    {
        if ($this->priceHistory->removeElement($priceHistory)) {
            if ($priceHistory->getProduct() === $this) {
                $priceHistory->setProduct(null);
            }
        }

        return $this;
    }

    public function removeAllCategories(): self
    {
        foreach ($this->categories as $category) {
            $category->removeProduct($this);
        }

        $this->categories->clear();

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }
}
