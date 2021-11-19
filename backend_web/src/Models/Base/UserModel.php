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
use App\Enums\Model;

final class UserModel extends AppModel
{
    public function __construct()
    {
        $this->fields = [
            "id" => [
                "label" => __("ID"),
                "postkey" => "id",
                "config" => [
                    "type" => Model::INT,
                    "length" => 11
                ]
            ],
            "email" => [
                "label" => __("Email"),
                "postkey" => "email",
                "config" => [
                    "type" => Model::STRING,
                    "length" => 100
                ]
            ],
            "secret" => [
                "label" => __("Password"),
                "postkey" => "password",
                "config" => [
                    "type" => Model::STRING,
                    "length" => 100
                ]
            ],
            "phone" => [
                "label" => __("Phone"),
                "postkey" => "phone",
                "config" => [
                    "type" => Model::STRING,
                    "length" => 20
                ]
            ],
            
            "fullname" => [
                "label" => __("Fullname"),
                "postkey" => "fullname",
                "config" => [
                    "type" => Model::STRING,
                    "length" => 100
                ]
            ],

            "address" => [
                "label" => __("Address"),
                "postkey" => "address",
                "config" => [
                    "type" => Model::STRING,
                    "length" => 250
                ]
            ],

            "birthdate" => [
                "label" => __("Birthdate"),
                "postkey" => "birthdate",
                "config" => [
                    "type" => Model::DATETIME
                ]
            ],

            "id_parent" => [
                "label" => __("User admin"),
                "postkey" => "id_parent",
                "config" => [
                    "type" => Model::INT,
                    "length" => 11
                ]
            ],

            "id_gender" => [
                "label" => __("Gender"),
                "postkey" => "id_gender",
                "config" => [
                    "type" => Model::INT,
                    "length" => 11
                ]
            ],

            "id_nationality" => [
                "label" => __("Nationality"),
                "postkey" => "id_nationality",
                "config" => [
                    "type" => Model::INT,
                    "length" => 11
                ]
            ],

            "id_country" => [
                "label" => __("Country"),
                "postkey" => "id_country",
                "config" => [
                    "type" => Model::INT,
                    "length" => 11
                ]
            ],

            "id_language" => [
                "label" => __("Language"),
                "postkey" => "id_language",
                "config" => [
                    "type" => Model::INT,
                    "length" => 11
                ]
            ],

            "id_profile" => [
                "label" => __("Role"),
                "postkey" => "id_profile",
                "config" => [
                    "type" => Model::INT,
                    "length" => 11
                ]
            ],

            "uuid" => [
                "label" => __("Code"),
                "config" => [
                    "type" => Model::STRING,
                    "length" => 50,
                    "default" => "uuid()"
                ]
            ],

        ];//fileds

        $this->pks = [
            "id"
        ];

    }// construct

}//ExampleModel
