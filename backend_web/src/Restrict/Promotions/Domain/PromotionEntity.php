<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Promotions\Domain\PromotionEntity
 * @file PromotionEntity.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 */
namespace App\Restrict\Promotions\Domain;

use App\Shared\Domain\Entities\AppEntity;
use App\Shared\Domain\Enums\EntityType;

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

            "date_from" => [
                "label" => __("Date from"),
                EntityType::REQUEST_KEY => "date_from",
                "config" => [
                    "type" => EntityType::DATETIME,
                ]
            ],

            "date_to" => [
                "label" => __("Date to"),
                EntityType::REQUEST_KEY => "date_to",
                "config" => [
                    "type" => EntityType::DATETIME,
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

            "bgcolor" => [
                "label" => __("Bg color"),
                EntityType::REQUEST_KEY => "bgcolor",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 10,
                ]
            ],

            "bgimage_xs" => [
                "label" => __("Bg image xs"),
                EntityType::REQUEST_KEY => "bgimage_xs",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 500,
                ]
            ],

            "bgimage_sm" => [
                "label" => __("Bg image sm"),
                EntityType::REQUEST_KEY => "bgimage_sm",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 500,
                ]
            ],

            "bgimage_md" => [
                "label" => __("Bg image md"),
                EntityType::REQUEST_KEY => "bgimage_md",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 500,
                ]
            ],

            "bgimage_lg" => [
                "label" => __("Bg image lg"),
                EntityType::REQUEST_KEY => "bgimage_lg",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 500,
                ]
            ],

            "bgimage_xl" => [
                "label" => __("Bg image xl"),
                EntityType::REQUEST_KEY => "bgimage_xl",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 500,
                ]
            ],

            "bgimage_xxl" => [
                "label" => __("Bg image xxl"),
                EntityType::REQUEST_KEY => "bgimage_xxl",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 500,
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

            "max_confirmed" => [
                "label" => __("Max confirmed"),
                EntityType::REQUEST_KEY => "max_confirmed",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 3,
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
