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
    public int $id;
    public string $email = "";
    public string $password = "";

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
        ];

        $this->pks = [
            "id"
        ];

    }// construct

}//ExampleModel
