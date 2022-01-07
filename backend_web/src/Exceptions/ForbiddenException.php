<?php

namespace App\Exceptions;
use \Exception;
use App\Enums\ExceptionType;

final class ForbiddenException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message, ExceptionType::CODE_FORBIDDEN);
    }
}