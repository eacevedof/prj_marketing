<?php

namespace App\Shared\Infrastructure\Exceptions;
use \Exception;
use App\Shared\Domain\Enums\ExceptionType;

final class RepositoryException extends Exception
{
    public function __construct(string $message, $code=ExceptionType::CODE_REQUESTED_RANGE_NOT_SATISFIABLE)
    {
        parent::__construct($message, $code);
    }
}