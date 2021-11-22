<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Models\ExampleModel 
 * @file ExampleModel.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Models\Base;

use App\Models\AppModel;
use App\Enums\ModelType;

final class UserModel extends AppModel
{
    public function __construct()
    {
        $this->fields = [
            "id" => [
                "label" => __("ID"),
                ModelType::REQUEST_KEY => "id",
                "config" => [
                    "type" => ModelType::INT,
                    "length" => 11
                ]
            ],
            "email" => [
                "label" => __("Email"),
                ModelType::REQUEST_KEY => "email",
                "config" => [
                    "type" => ModelType::STRING,
                    "length" => 100
                ]
            ],
            "secret" => [
                "label" => __("Password"),
                ModelType::REQUEST_KEY => "password",
                "config" => [
                    "type" => ModelType::STRING,
                    "length" => 100
                ]
            ],
            "phone" => [
                "label" => __("Phone"),
                ModelType::REQUEST_KEY => "phone",
                "config" => [
                    "type" => ModelType::STRING,
                    "length" => 20
                ]
            ],
            
            "fullname" => [
                "label" => __("Fullname"),
                ModelType::REQUEST_KEY => "fullname",
                "config" => [
                    "type" => ModelType::STRING,
                    "length" => 100
                ]
            ],

            "address" => [
                "label" => __("Address"),
                ModelType::REQUEST_KEY => "address",
                "config" => [
                    "type" => ModelType::STRING,
                    "length" => 250
                ]
            ],

            "birthdate" => [
                "label" => __("Birthdate"),
                ModelType::REQUEST_KEY => "birthdate",
                "config" => [
                    "type" => ModelType::DATE
                ]
            ],

            "id_parent" => [
                "label" => __("User admin"),
                ModelType::REQUEST_KEY => "id_parent",
                "config" => [
                    "type" => ModelType::INT,
                    "length" => 11
                ]
            ],

            "id_gender" => [
                "label" => __("Gender"),
                ModelType::REQUEST_KEY => "id_gender",
                "config" => [
                    "type" => ModelType::INT,
                    "length" => 11
                ]
            ],

            "id_nationality" => [
                "label" => __("Nationality"),
                ModelType::REQUEST_KEY => "id_nationality",
                "config" => [
                    "type" => ModelType::INT,
                    "length" => 11
                ]
            ],

            "id_country" => [
                "label" => __("Country"),
                ModelType::REQUEST_KEY => "id_country",
                "config" => [
                    "type" => ModelType::INT,
                    "length" => 11
                ]
            ],

            "id_language" => [
                "label" => __("Language"),
                ModelType::REQUEST_KEY => "id_language",
                "config" => [
                    "type" => ModelType::INT,
                    "length" => 11
                ]
            ],

            "id_profile" => [
                "label" => __("Role"),
                ModelType::REQUEST_KEY => "id_profile",
                "config" => [
                    "type" => ModelType::INT,
                    "length" => 11
                ]
            ],

            "uuid" => [
                "label" => __("Code"),
                "config" => [
                    "type" => ModelType::STRING,
                    "length" => 50,
                    "default" => "uuid()"
                ]
            ],

        ];//fileds

        $this->pks = [
            "id", "uuid"
        ];

    }// construct

}//ExampleModel
