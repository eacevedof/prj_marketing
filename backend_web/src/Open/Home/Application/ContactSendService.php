<?php
namespace App\Open\Home\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Domain\Entities\FieldsValidator;


final class ContactSendService extends AppService
{
    private FieldsValidator $validator;

    public function __construct(array $input)
    {
        $this->input = [
            "name" => $input["name"] ?? "",
            "email" => $input["email"] ?? "",
            "subject" => $input["subject"] ?? "",
            "message" => $input["message"] ?? "",
        ];
    }

    private function _load_validator(): void
    {
        $this->validator = VF::get($this->input);
        $this->validator->add_rule("")
    }

    public function __invoke(): void
    {

    }
}