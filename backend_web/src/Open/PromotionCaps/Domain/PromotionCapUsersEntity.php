<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Open\PromotionCaps\Domain\PromotionCapUsersEntity
 * @file PromotionCapUsersEntity.php v1.0.0
 * @date %DATE% SPAIN
 */
namespace App\Open\PromotionCaps\Domain;

use App\Shared\Domain\Entities\AppEntity;
use App\Shared\Domain\Enums\EntityType;

final class PromotionCapUsersEntity extends AppEntity
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
       
        "uuid" => [
            "label" => __("tr_uuid"),
            EntityType::REQUEST_KEY => "uuid",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 50,
            ]
        ],
       
        "id_owner" => [
            "label" => __("tr_id_owner"),
            EntityType::REQUEST_KEY => "id_owner",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "code_erp" => [
            "label" => __("tr_code_erp"),
            EntityType::REQUEST_KEY => "code_erp",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 25,
            ]
        ],
       
        "description" => [
            "label" => __("tr_description"),
            EntityType::REQUEST_KEY => "description",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 250,
            ]
        ],
       
        "id_promotion" => [
            "label" => __("tr_id_promotion"),
            EntityType::REQUEST_KEY => "id_promotion",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "id_language" => [
            "label" => __("tr_id_language"),
            EntityType::REQUEST_KEY => "id_language",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "id_country" => [
            "label" => __("tr_id_country"),
            EntityType::REQUEST_KEY => "id_country",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "phone1" => [
            "label" => __("tr_phone1"),
            EntityType::REQUEST_KEY => "phone1",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 20,
            ]
        ],
       
        "email" => [
            "label" => __("tr_email"),
            EntityType::REQUEST_KEY => "email",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "birthdate" => [
            "label" => __("tr_birthdate"),
            EntityType::REQUEST_KEY => "birthdate",
            "config" => [
                "type" => EntityType::DATETIME,
                
            ]
        ],
       
        "name1" => [
            "label" => __("tr_name1"),
            EntityType::REQUEST_KEY => "name1",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 15,
            ]
        ],
       
        "name2" => [
            "label" => __("tr_name2"),
            EntityType::REQUEST_KEY => "name2",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 15,
            ]
        ],
       
        "id_gender" => [
            "label" => __("tr_id_gender"),
            EntityType::REQUEST_KEY => "id_gender",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "address" => [
            "label" => __("tr_address"),
            EntityType::REQUEST_KEY => "address",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       ];

        $this->pks = [
            "id", "uuid"
        ];

    }// construct
}
