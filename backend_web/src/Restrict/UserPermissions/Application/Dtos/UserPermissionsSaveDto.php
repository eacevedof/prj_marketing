<?php

namespace App\Restrict\UserPermissions\Application\Dtos;

final readonly class UserPermissionsSaveDto
{
    public function __construct(
        private string $userUuid,
        private int    $id,
        private string $uuid,
        private int    $idUser,
        private string $jsonRw,
    ) {
    }

    public static function fromPrimitives(array $primitives): self
    {
        return new self(
            trim((string) ($primitives["userUuid"] ?? "")),
            (int) ($primitives["id"] ?? ""),
            trim((string) ($primitives["uuid"] ?? "")),
            (int) ($primitives["idUser"] ?? ""),
            trim((string) ($primitives["jsonRw"] ?? "")),
        );
    }

    public function userUuid(): string
    {
        return $this->userUuid;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function idUser(): int
    {
        return $this->idUser;
    }

    public function jsonRw(): string
    {
        return $this->jsonRw;
    }
}
