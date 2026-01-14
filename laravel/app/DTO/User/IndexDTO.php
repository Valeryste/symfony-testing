<?php

namespace App\DTO\User;

use App\DTO\BaseDTO;
use App\Traits\WithPagination;

class IndexDTO extends BaseDTO
{
    use WithPagination;

    public function __construct(array $data)
    {
        $this->initializePagination($data);
    }
}
