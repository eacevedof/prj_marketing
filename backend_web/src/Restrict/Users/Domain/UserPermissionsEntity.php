<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Models\ExampleEntity
 * @file ExampleEntity.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Restrict\Users\Domain;

use App\Shared\Domain\Entities\AppEntity;
use App\Shared\Domain\Enums\EntityType;

final class UserPermissionsEntity extends AppEntity
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

            "id_user" => [
                "label" => __("User"),
                EntityType::REQUEST_KEY => "id_user",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "json_rw" => [
                "label" => __("Permission JSON"),
                EntityType::REQUEST_KEY => "json_rw",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 2000,
                ]
            ],
        ];

        $this->pks = [
            "id", "uuid"
        ];
    }

}//UserPermissionsEntity
