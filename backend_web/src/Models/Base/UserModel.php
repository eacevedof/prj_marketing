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
                "in_post" => "id",
                "config" => [
                    "type" => Model::INT,
                    "length" => 11
                ]
            ],
            "email" => [
                "label" => __("Email"),
                "in_post" => "email",
                "config" => [
                    "type" => Model::STRING,
                    "length" => 100
                ]
            ],
            "secret" => [
                "label" => __("Password"),
                "in_post" => "password",
                "config" => [
                    "type" => Model::STRING,
                    "length" => 100
                ]
            ],
            "phone" => [
                "label" => __("Phone"),
                "in_post" => "phone",
                "config" => [
                    "type" => Model::STRING,
                    "length" => 20
                ]
            ],
            "fullname" => [
                "label" => __("Fullname"),
                "in_post" => "fullname",
                "config" => [
                    "type" => Model::STRING,
                    "length" => 100
                ]
            ],

            "address" => [
                "label" => __("Address"),
                "in_post" => "address",
                "config" => [
                    "type" => Model::STRING,
                    "length" => 250
                ]
            ],

            "birthdate" => [
                "label" => __("Birthdate"),
                "in_post" => "birthdate",
                "config" => [
                    "type" => Model::DATETIME
                ]
            ],

            "id_parent" => [
                "label" => __("User admin"),
                "in_post" => "id_parent",
                "config" => [
                    "type" => Model::INT,
                    "length" => 11
                ]
            ],

            "id_gender" => [
                "label" => __("Gender"),
                "in_post" => "id_gender",
                "config" => [
                    "type" => Model::INT,
                    "length" => 11
                ]
            ],

            "id_nationality" => [
                "label" => __("Nationality"),
                "in_post" => "id_nationality",
                "config" => [
                    "type" => Model::INT,
                    "length" => 11
                ]
            ],

            "id_country" => [
                "label" => __("Country"),
                "in_post" => "id_country",
                "config" => [
                    "type" => Model::INT,
                    "length" => 11
                ]
            ],

            "id_language" => [
                "label" => __("Language"),
                "in_post" => "id_language",
                "config" => [
                    "type" => Model::INT,
                    "length" => 11
                ]
            ],

            "id_profile" => [
                "label" => __("Role"),
                "in_post" => "id_profile",
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
