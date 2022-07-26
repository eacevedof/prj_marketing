<?php
namespace App\Open\PromotionCaps\Domain;

use App\Shared\Domain\Entities\AppEntity;
use App\Shared\Domain\Enums\EntityType;

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
            "label" => __("tr_id_promouser"),
            EntityType::REQUEST_KEY => "id_promouser",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "date_subscription" => [
            "label" => __("tr_date_subscription"),
            EntityType::REQUEST_KEY => "date_subscription",
            "config" => [
                "type" => EntityType::DATETIME,
                
            ]
        ],
       
        "date_confirm" => [
            "label" => __("tr_date_confirm"),
            EntityType::REQUEST_KEY => "date_confirm",
            "config" => [
                "type" => EntityType::DATETIME,
                
            ]
        ],
       
        "date_execution" => [
            "label" => __("tr_date_execution"),
            EntityType::REQUEST_KEY => "date_execution",
            "config" => [
                "type" => EntityType::DATETIME,
                
            ]
        ],
       
        "code_execution" => [
            "label" => __("tr_code_execution"),
            EntityType::REQUEST_KEY => "code_execution",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 15,
            ]
        ],
       
        "exec_user" => [
            "label" => __("tr_exec_user"),
            EntityType::REQUEST_KEY => "exec_user",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "subs_status" => [
            "label" => __("tr_subs_status"),
            EntityType::REQUEST_KEY => "subs_status",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "remote_ip" => [
            "label" => __("tr_remote_ip"),
            EntityType::REQUEST_KEY => "remote_ip",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 15,
            ]
        ],
       
        "notes" => [
            "label" => __("tr_notes"),
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
