<?php
namespace App\Restrict\Login\Application\Dtos;

use App\Shared\Infrastructure\Components\Session\SessionComponent;

final readonly class LoginDto
{
    public function __construct(
        private string $email,
        private string $password,
        private ?SessionComponent $session,
    )
    {}

    public static function fromPrimitives(array $primitives): self
    {
        return new self(
            (string) ($primitives["email"] ?? ""),
            (string) ($primitives["password"] ?? ""),
            $primitives["session"] ?? null,
        );
    }

    public function session(): ?SessionComponent
    {
        return $this->session;
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