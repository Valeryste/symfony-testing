<?php

namespace App\Exception;

class UserActiveException extends \Exception
{
    public function __construct(string $message = "User account is no active", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}