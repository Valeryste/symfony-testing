<?php

namespace App\Entity;

use App\Interface\TimeStampsInterface;
use App\Trait\TimeStampsTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class BaseEntity implements TimeStampsInterface
{
    use TimeStampsTrait;
}