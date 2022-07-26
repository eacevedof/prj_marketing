<?php
namespace App\Open\Home\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\IEventDispatcher;
use App\Checker\Application\CheckerService;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Open\Home\Domain\Events\ContactEmailSentEvent;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class ContactSendService extends AppService implements IEventDispatcher
{
    private FieldsValidator $validator;

    public function __construct(array $input)
    {
        $this->input = [
            "email" => trim($input["email"] ?? ""),
            "name" => trim($input["name"] ?? ""),
            "subject" => trim($input["subject"] ?? ""),
            "message" => trim($input["message"] ?? ""),
        ];
        $this->_load_validator();
    }

    private function _load_validator(): void
    {
        $this->validator = VF::get($this->input);
        $this->validator
            ->add_rule("name", "name", function ($data) {
                $value = $data["value"];
                if (!$value)
                    return __("Empty value is not allowed");

                if (!is_string($value))
                    return __("Invalid {0} format", __("Name"));

                if (strlen($value)<5 || strlen($value)>25)
                    return __("{0} must be greater than {1} and lighter than {2}", __("Name"), 5, 25);

                if (!CheckerService::name_format($value))
                    return __("Invalid {0} format", __("Name"));
            })
            ->add_rule("email", "email", function ($data) {
                $value = $data["value"];
                if (!$value)
                    return __("Empty value is not allowed");
                if (!is_string($value))
                    return __("Invalid {0} format", __("Email"));
                if (strlen($value)<5 || strlen($value)>35)
                    return __("{0} must be greater than {1} and lighter than {2}", __("Email"), 5, 35);
                if (!CheckerService::is_valid_email($value))
                    return __("Invalid {0} format", __("Email"));
            })
            ->add_rule("subject", "subject", function ($data) {
                $value = $data["value"];
                if (!$value)
                    return __("Empty value is not allowed");
                if (!is_string($value))
                    return __("Invalid {0} format", __("Subject"));
                if (strlen($value)<10 || strlen($value)>50)
                    return __("{0} must be greater than {1} and lighter than {2}", __("Subject"), 10, 50);
            })
            ->add_rule("message", "message", function ($data) {
                $value = $data["value"];
                if (!$value)
                    return __("Empty value is not allowed");
                if (!is_string($value))
                    return __("Invalid {0} format", __("Message"));
                if (strlen($value)<10 || strlen($value)>2000)
                    return __("{0} must be greater than {1} and lighter than {2}", __("Message"), 10, 2000);
            });
    }

    private function _dispatch(): void
    {
        $payload = $this->input;
        EventBus::instance()->publish(...[
            ContactEmailSentEvent::from_primitives(1, [
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
        if ($errors = $this->validator->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }
        $this->_dispatch();
        return [
            "description" => __("Thank you! Your information has been sent successfully."),
        ];
    }
}