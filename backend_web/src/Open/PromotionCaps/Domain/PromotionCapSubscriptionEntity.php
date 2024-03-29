<?php

namespace App\Open\PromotionCaps\Domain;

use App\Shared\Domain\Enums\EntityType;
use App\Shared\Domain\Entities\AppEntity;

final class PromotionCapSubscriptionEntity extends AppEntity
{
    public function __construct()
    {
        $this->fields = [
            "id" => [
                "label" => __("Nº"),
                EntityType::REQUEST_KEY => "id",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "uuid" => [
                "label" => __("Code"),
                EntityType::REQUEST_KEY => "uuid",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 50,
                ]
            ],

            "id_owner" => [
                "label" => __("Owner"),
                EntityType::REQUEST_KEY => "id_owner",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "code_erp" => [
                "label" => __("External code"),
                EntityType::REQUEST_KEY => "code_erp",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 25,
                ]
            ],

            "description" => [
                "label" => __("Description"),
                EntityType::REQUEST_KEY => "description",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 250,
                ]
            ],

            "id_promotion" => [
                "label" => __("Promotion"),
                EntityType::REQUEST_KEY => "id_promotion",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "id_promouser" => [
                "label" => __("Subscriber"),
                EntityType::REQUEST_KEY => "id_promouser",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "date_subscription" => [
                "label" => __("Subscription date"),
                EntityType::REQUEST_KEY => "date_subscription",
                "config" => [
                    "type" => EntityType::DATETIME,

                ]
            ],

            "date_confirm" => [
                "label" => __("Confirm date"),
                EntityType::REQUEST_KEY => "date_confirm",
                "config" => [
                    "type" => EntityType::DATETIME,

                ]
            ],

            "date_execution" => [
                "label" => __("Execution date"),
                EntityType::REQUEST_KEY => "date_execution",
                "config" => [
                    "type" => EntityType::DATETIME,

                ]
            ],

            "code_execution" => [
                "label" => __("Voucher code"),
                EntityType::REQUEST_KEY => "code_execution",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 15,
                ]
            ],

            "exec_user" => [
                "label" => __("Validation user"),
                EntityType::REQUEST_KEY => "exec_user",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "subs_status" => [
                "label" => __("Status"),
                EntityType::REQUEST_KEY => "subs_status",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "remote_ip" => [
                "label" => __("Remote IP"),
                EntityType::REQUEST_KEY => "remote_ip",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 50,
                ]
            ],

            "notes" => [
                "label" => __("Notes"),
                EntityType::REQUEST_KEY => "notes",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 300,
                ]
            ],
        ];

        $this->pks = [
            "id", "uuid"
        ];

    }// construct

}//PromotionCapSubscriptionEntity
