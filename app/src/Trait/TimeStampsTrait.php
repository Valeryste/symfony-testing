<?php

namespace App\Trait;
use Doctrine\ORM\Mapping as ORM;

trait TimeStampsTrait
{
    #[ORM\Column(name: 'created_at', options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected ?\DateTime $createdAt = null;

    #[ORM\Column(name: 'updated_at', nullable: true)]
    protected ?\DateTime $updatedAt = null;

    #[ORM\Column(name: 'deleted_at', nullable: true)]
    protected ?\DateTime $deletedAt = null;

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if (!isset($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
}

    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function isDeleted(): bool
    {
        return null !== $this->deletedAt;
    }
}