<?php

namespace App\Restrict\Login\Application\Dtos;

final readonly class LoginDto
{
    public function __construct(
        private string $email,
        private string $password
    ) {
    }

    public static function fromPrimitives(array $primitives): self
    {
        return new self(
            (string) ($primitives["email"] ?? ""),
            (string) ($primitives["password"] ?? ""),
        );
    }

    public function email(): string
    {
        return  $this->email;
    }

    public function password(): string
    {
        return  $this->password;
    }
}
