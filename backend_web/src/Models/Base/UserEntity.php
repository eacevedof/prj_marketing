<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Models\ExampleEntity
 * @file ExampleEntity.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Models\Base;

use App\Models\AppEntity;
use App\Enums\EntityType;

final class UserEntity extends AppEntity
{
    public function __construct()
    {
        $this->fields = [
            "id" => [
                "label" => __("ID"),
                EntityType::REQUEST_KEY => "id",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 11
                ]
            ],
            "email" => [
                "label" => __("Email"),
                EntityType::REQUEST_KEY => "email",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 100
                ]
            ],
            "secret" => [
                "label" => __("Password"),
                EntityType::REQUEST_KEY => "password",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 100
                ]
            ],
            "phone" => [
                "label" => __("Phone"),
                EntityType::REQUEST_KEY => "phone",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 20
                ]
            ],
            
            "fullname" => [
                "label" => __("Fullname"),
                EntityType::REQUEST_KEY => "fullname",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 100
                ]
            ],

            "address" => [
                "label" => __("Address"),
                EntityType::REQUEST_KEY => "address",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 250
                ]
            ],

            "birthdate" => [
                "label" => __("Birthdate"),
                EntityType::REQUEST_KEY => "birthdate",
                "config" => [
                    "type" => EntityType::DATE
                ]
            ],

            "id_parent" => [
                "label" => __("Superior"),
                EntityType::REQUEST_KEY => "id_parent",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 11
                ]
            ],

            "id_gender" => [
                "label" => __("Gender"),
                EntityType::REQUEST_KEY => "id_gender",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 11
                ]
            ],

            "id_nationality" => [
                "label" => __("Nationality"),
                EntityType::REQUEST_KEY => "id_nationality",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 11
                ]
            ],

            "id_country" => [
                "label" => __("Country"),
                EntityType::REQUEST_KEY => "id_country",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 11
                ]
            ],

            "id_language" => [
                "label" => __("Language"),
                EntityType::REQUEST_KEY => "id_language",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 11
                ]
            ],

            "id_profile" => [
                "label" => __("Profile"),
                EntityType::REQUEST_KEY => "id_profile",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 11
                ]
            ],

            "uuid" => [
                "label" => __("Code"),
                EntityType::REQUEST_KEY => "uuid",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 50
                ]
            ],

        ];//fileds

        $this->pks = [
            "id", "uuid"
        ];

    }// construct

}//UserEntity
