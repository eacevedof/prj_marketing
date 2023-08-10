<?php

namespace App\Open\Home\Application;

use App\Checker\Application\CheckerService;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Shared\Domain\Bus\Event\IEventDispatcher;
use App\Shared\Infrastructure\Services\AppService;
use App\Open\Home\Domain\Events\ContactEmailSentEvent;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;

final class ContactSendService extends AppService implements IEventDispatcher
{
    private FieldsValidator $fieldsValidator;

    public function __construct(array $input)
    {
        $this->input = [
            "email" => trim($input["email"] ?? ""),
            "name" => trim($input["name"] ?? ""),
            "subject" => trim($input["subject"] ?? ""),
            "message" => trim($input["message"] ?? ""),
        ];
        $this->_loadFieldsValidator();
    }

    private function _loadFieldsValidator(): void
    {
        $this->fieldsValidator = VF::getFieldValidator($this->input);
        $this->fieldsValidator
            ->addRule("name", "name", function ($data) {
                $value = $data["value"];
                if (!$value) {
                    return __("Empty value is not allowed");
                }

                if (!is_string($value)) {
                    return __("Invalid {0} format", __("Name"));
                }

                if (strlen($value) < 5 || strlen($value) > 25) {
                    return __("{0} must be greater than {1} and lighter than {2}", __("Name"), 5, 25);
                }

                if (!CheckerService::isNameFormatOk($value)) {
                    return __("Invalid {0} format", __("Name"));
                }
            })
            ->addRule("email", "email", function ($data) {
                $value = $data["value"];
                if (!$value) {
                    return __("Empty value is not allowed");
                }
                if (!is_string($value)) {
                    return __("Invalid {0} format", __("Email"));
                }
                if (strlen($value) < 5 || strlen($value) > 35) {
                    return __("{0} must be greater than {1} and lighter than {2}", __("Email"), 5, 35);
                }
                if (!CheckerService::isValidEmail($value)) {
                    return __("Invalid {0} format", __("Email"));
                }
            })
            ->addRule("subject", "subject", function ($data) {
                $value = $data["value"];
                if (!$value) {
                    return __("Empty value is not allowed");
                }
                if (!is_string($value)) {
                    return __("Invalid {0} format", __("Subject"));
                }
                if (strlen($value) < 10 || strlen($value) > 50) {
                    return __("{0} must be greater than {1} and lighter than {2}", __("Subject"), 10, 50);
                }
            })
            ->addRule("message", "message", function ($data) {
                $value = $data["value"];
                if (!$value) {
                    return __("Empty value is not allowed");
                }
                if (!is_string($value)) {
                    return __("Invalid {0} format", __("Message"));
                }
                if (strlen($value) < 10 || strlen($value) > 2000) {
                    return __("{0} must be greater than {1} and lighter than {2}", __("Message"), 10, 2000);
                }
            });
    }

    private function _dispatchEvents(): void
    {
        $payload = $this->input;
        EventBus::instance()->publish(...[
            ContactEmailSentEvent::fromPrimitives(1, [
                "emailuuid" => uniqid(),
                "email" => $payload["email"],
                "name" => $payload["name"],
                "subject" => $payload["subject"],
                "message" => $payload["message"],
            ]),
        ]);
    }

    public function __invoke(): array
    {
        if ($errors = $this->fieldsValidator->getErrors()) {
            $this->_setErrors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }
        $this->_dispatchEvents();
        return [
            "description" => __("Thank you! Your information has been sent successfully."),
        ];
    }
}
