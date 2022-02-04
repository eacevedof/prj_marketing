<?php

namespace App\Shared\Infrastructure\Exceptions;
use \Exception;
use App\Shared\Infrastructure\Enums\ExceptionType;

final class ForbiddenException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message, ExceptionType::CODE_FORBIDDEN);
    }
}