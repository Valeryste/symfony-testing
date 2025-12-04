<?php

namespace App\Interface;

interface TimeStampsInterface
{
    public function getCreatedAt(): ?\DateTime;
    public function setCreatedAt(\DateTime $createdAt): self;

    public function getUpdatedAt(): ?\DateTime;
    public function setUpdatedAt(?\DateTime $updatedAt): self;

    public function getDeletedAt(): ?\DateTime;
    public function setDeletedAt(?\DateTime $deletedAt): self;
}