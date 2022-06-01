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
       
        "id_language" => [
            "label" => __("Language"),
            EntityType::REQUEST_KEY => "id_language",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "id_country" => [
            "label" => __("Country"),
            EntityType::REQUEST_KEY => "id_country",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "phone1" => [
            "label" => __("Mobile"),
            EntityType::REQUEST_KEY => "phone1",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 20,
            ]
        ],
       
        "email" => [
            "label" => __("Email"),
            EntityType::REQUEST_KEY => "email",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "birthdate" => [
            "label" => __("Birthdate"),
            EntityType::REQUEST_KEY => "birthdate",
            "config" => [
                "type" => EntityType::DATE,
                
            ]
        ],
       
        "name1" => [
            "label" => __("First name"),
            EntityType::REQUEST_KEY => "name1",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 30,
            ]
        ],
       
        "name2" => [
            "label" => __("Last name"),
            EntityType::REQUEST_KEY => "name2",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 30,
            ]
        ],
       
        "id_gender" => [
            "label" => __("Gender"),
            EntityType::REQUEST_KEY => "id_gender",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "address" => [
            "label" => __("Address"),
            EntityType::REQUEST_KEY => "address",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],

        "is_mailing" => [
            "label" => __("I would like to receive promotions and raffles in my email"),
            EntityType::REQUEST_KEY => "is_mailing",
            "config" => [
                "type" => EntityType::INT,
                "length" => 2,
            ]
        ],

        "is_terms" => [
            "label" => __("I have red and accept legal terms and conditions"),
            EntityType::REQUEST_KEY => "is_terms",
            "config" => [
                "type" => EntityType::INT,
                "length" => 2,
            ]
        ],
        ];

        $this->pks = [
            "id", "uuid"
        ];

    }// construct
}
