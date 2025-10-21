<?php

namespace App\Entity;

use App\Interface\TimeStampsInterface;
use App\Trait\TimeStampsTrait;

abstract class BaseEntity implements TimeStampsInterface
{
    use TimeStampsTrait;
}