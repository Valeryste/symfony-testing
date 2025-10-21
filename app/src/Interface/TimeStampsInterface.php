<?php

namespace App\Interface;

interface TimeStampsInterface
{
    public function getCreatedAt(): ?\DateTimeImmutable;
    public function setCreatedAt(\DateTimeImmutable $created_at): self;

    public function getUpdatedAt(): ?\DateTimeImmutable;
    public function setUpdatedAt(?\DateTimeImmutable $updated_at): self;

    public function getDeletedAt(): ?\DateTimeImmutable;
    public function setDeletedAt(?\DateTimeImmutable $deleted_at): self;
}