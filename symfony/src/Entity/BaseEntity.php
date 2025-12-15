<?php

namespace App\Entity;

use App\Interface\TimeStampsInterface;
use App\Trait\TimeStampsTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable]
abstract class BaseEntity implements TimeStampsInterface
{
    use TimeStampsTrait;
}