<?php

namespace App\Restrict\Users\Application\Dtos;

final readonly class UserUpdateDto
{
    public function __construct(
        private int    $id,
        private string $uuid,
        private string $address,
        private string $birthdate,
        private string $email,
        private string $fullname,
        private int    $idCountry,
        private int    $idLanguage,
        private int    $idParent,
        private int    $idProfile,
        private string $secret,
        private string $secret2,
        private string $phone,
    ) {
    }

    public static function fromPrimitives(array $primitives): self
    {
        return new self(
            (int) ($primitives["id"] ?? ""),
            trim((string) ($primitives["uuid"] ?? "")),
            trim((string) ($primitives["address"] ?? "")),
            trim((string) ($primitives["birthdate"] ?? "")),
            trim((string) ($primitives["email"] ?? "")),
            trim((string) ($primitives["fullname"] ?? "")),
            (int) ($primitives["idCountry"] ?? ""),
            (int) ($primitives["idLanguage"] ?? ""),
            (int) ($primitives["idParent"] ?? ""),
            (int) ($primitives["idProfile"] ?? ""),
            trim((string) ($primitives["secret"] ?? "")),
            trim((string) ($primitives["secret2"] ?? "")),
            trim((string) ($primitives["phone"] ?? "")),
        );
    }

    public function id(): string
    {
        return $this->id;
    }
    public function uuid(): string
    {
        return $this->uuid;
    }
    public function address(): string
    {
        return $this->address;
    }
    public function birthdate(): string
    {
        return $this->birthdate;
    }
    public function email(): string
    {
        return $this->email;
    }
    public function fullname(): string
    {
        return $this->fullname;
    }
    public function idCountry(): int
    {
        return $this->idCountry;
    }
    public function idLanguage(): int
    {
        return $this->idLanguage;
    }
    public function idParent(): int
    {
        return $this->idParent;
    }
    public function idProfile(): int
    {
        return $this->idProfile;
    }
    public function secret(): string
    {
        return $this->secret;
    }
    public function secret2(): string
    {
        return $this->secret2;
    }
    public function phone(): string
    {
        return $this->phone;
    }
}
