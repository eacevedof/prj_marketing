<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Users\Domain\UserPreferencesEntity
 * @file UserPreferencesEntity.php v1.0.0
 * @date %DATE% SPAIN
 */
namespace App\Restrict\Users\Domain;

use App\Shared\Domain\Entities\AppEntity;
use App\Shared\Domain\Enums\EntityType;

final class UserPreferencesEntity extends AppEntity
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

            "id_user" => [
                "label" => __("User"),
                EntityType::REQUEST_KEY => "id_user",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "pref_key" => [
                "label" => __("Key"),
                EntityType::REQUEST_KEY => "pref_key",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 250,
                ]
            ],

            "pref_value" => [
                "label" => __("Value"),
                EntityType::REQUEST_KEY => "pref_value",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 2000,
                ]
            ],
        ];

        $this->pks = [
            "id", "uuid"
        ];

    }// construct

}//UserPreferencesEntity
