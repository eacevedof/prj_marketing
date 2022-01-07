<?php

namespace App\Exceptions;
use \Exception;
use App\Enums\ExceptionType;

final class NotFoundException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message, ExceptionType::CODE_NOT_FOUND);
    }
}