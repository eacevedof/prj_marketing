<?php
namespace App\Restrict\Login\Application\Dtos;

use App\Shared\Infrastructure\Components\Session\SessionComponent;

final readonly class LoginDto
{

    public function __construct(
        private ?SessionComponent $session,
        private string $email,
        private string $password
    )
    {}

    public static function fromPrimitives(array $primitives): self
    {
        return new self(
            $primitives["session"] ?? null,
            (string)($primitives["email"] ?? ""),
            (string)($primitives["password"] ?? ""),
        );
    }

    public function session():?SessionComponent
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