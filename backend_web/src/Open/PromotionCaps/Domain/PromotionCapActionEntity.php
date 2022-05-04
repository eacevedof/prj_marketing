<?php
namespace App\Open\PromotionCaps\Domain;;

use App\Shared\Domain\Entities\AppEntity;
use App\Shared\Domain\Enums\EntityType;

final class PromotionCapActionEntity extends AppEntity
{
    public function __construct()
    {
        $this->fields = [
        "id" => [
            "label" => __("tr_id"),
            EntityType::REQUEST_KEY => "id",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
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
       
        "id_type" => [
            "label" => __("Type"),
            EntityType::REQUEST_KEY => "id_type",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "url_req" => [
            "label" => __("Req url"),
            EntityType::REQUEST_KEY => "url_req",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 300,
            ]
        ],
       
        "url_ref" => [
            "label" => __("From url"),
            EntityType::REQUEST_KEY => "url_ref",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 300,
            ]
        ],
       
        "remote_ip" => [
            "label" => __("Remote IP"),
            EntityType::REQUEST_KEY => "remote_ip",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 15,
            ]
        ],
       ];

        $this->pks = [
            "id", "uuid"
        ];

    }// construct

}//PromotionCapActionEntity
