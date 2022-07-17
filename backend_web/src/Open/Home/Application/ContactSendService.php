<?php
namespace App\Open\Home\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Entities\FieldsValidator;

final class ContactSendService extends AppService
{
    public function __construct(array $input)
    {
        $this->input = [
            "name" => $input["name"] ?? "",
            "email" => $input["email"] ?? "",
            "subject" => $input["subject"] ?? "",
            "message" => $input["message"] ?? "",
        ];
    }

}