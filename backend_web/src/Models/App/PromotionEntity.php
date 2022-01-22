<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Models\ExampleEntity
 * @file ExampleEntity.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Models\App;

use App\Models\AppEntity;
use App\Enums\EntityType;

final class PromotionEntity extends AppEntity
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
                "label" => __("Cod. Promo"),
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

            "slug" => [
                "label" => __("Slug"),
                EntityType::REQUEST_KEY => "slug",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 250,
                ]
            ],

            "content" => [
                "label" => __("Content"),
                EntityType::REQUEST_KEY => "content",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 2000,
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

            "date_from" => [
                "label" => __("Date from"),
                EntityType::REQUEST_KEY => "date_from",
                "config" => [
                    "type" => EntityType::DATE,
                ]
            ],

            "date_to" => [
                "label" => __("Date to"),
                EntityType::REQUEST_KEY => "date_to",
                "config" => [
                    "type" => EntityType::DATE,
                ]
            ],

            "url_social" => [
                "label" => __("Url social"),
                EntityType::REQUEST_KEY => "url_social",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 250,
                ]
            ],

            "url_design" => [
                "label" => __("Url design"),
                EntityType::REQUEST_KEY => "url_design",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 250,
                ]
            ],

            "is_active" => [
                "label" => __("Enabled"),
                EntityType::REQUEST_KEY => "is_active",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 3,
                ]
            ],

            "invested" => [
                "label" => __("Invested"),
                EntityType::REQUEST_KEY => "invested",
                "config" => [
                    "type" => EntityType::DECIMAL,
                    "length" => 10,
                ]
            ],

            "returned" => [
                "label" => __("Inv returned"),
                EntityType::REQUEST_KEY => "returned",
                "config" => [
                    "type" => EntityType::DECIMAL,
                    "length" => 10,
                ]
            ],

            "notes" => [
                "label" => __("Notes"),
                EntityType::REQUEST_KEY => "notes",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 300,
                ]
            ],
       ];

        $this->pks = [
            "id", "uuid"
        ];

    }// construct

}//PromotionEntity
