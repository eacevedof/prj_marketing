<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Promotions\Domain\PromotionEntity
 * @file PromotionEntity.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 */

namespace App\Restrict\Promotions\Domain;

use App\Shared\Domain\Enums\EntityType;
use App\Shared\Domain\Entities\AppEntity;

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

            "id_tz" => [
                "label" => __("Timezone"),
                EntityType::REQUEST_KEY => "id_tz",
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

            "date_execution" => [
                "label" => __("Date limit"),
                EntityType::REQUEST_KEY => "date_execution",
                "config" => [
                    "type" => EntityType::DATETIME,
                ]
            ],

            "content" => [
                "label" => __("Terms and conditions"),
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
                "label" => __("Max. confirmed"),
                EntityType::REQUEST_KEY => "max_confirmed",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "is_raffleable" => [
                "label" => __("Raffleable"),
                EntityType::REQUEST_KEY => "is_raffleable",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 2,
                ]
            ],

            "is_cumulative" => [
                "label" => __("Cumulative"),
                EntityType::REQUEST_KEY => "is_cumulative",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 2,
                ]
            ],

            "is_published" => [
                "label" => __("Published"),
                EntityType::REQUEST_KEY => "is_published",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 2,
                ]
            ],

            "is_launched" => [
                "label" => __("Launched"),
                EntityType::REQUEST_KEY => "is_launched",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 2,
                ]
            ],

            "tags" => [
                "label" => __("Tags"),
                EntityType::REQUEST_KEY => "tags",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 500,
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

            "num_viewed" => [
                "label" => __("Viewed"),
                EntityType::REQUEST_KEY => "num_viewed",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "num_subscribed" => [
                "label" => __("Subscribed"),
                EntityType::REQUEST_KEY => "num_subscribed",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "num_confirmed" => [
                "label" => __("Confirmed"),
                EntityType::REQUEST_KEY => "num_confirmed",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "num_executed" => [
                "label" => __("Executed"),
                EntityType::REQUEST_KEY => "num_executed",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "disabled_date" => [
                "label" => __("Disabled date"),
                EntityType::REQUEST_KEY => "disabled_date",
                "config" => [
                    "type" => EntityType::DATETIME,
                ]
            ],

            "disabled_user" => [
                "label" => __("Disabled by"),
                EntityType::REQUEST_KEY => "disabled_user",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 15,
                ]
            ],

            "disabled_reason" => [
                "label" => __("Disabled reason"),
                EntityType::REQUEST_KEY => "disabled_reason",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 1000,
                ]
            ],
        ];

        $this->pks = [
            "id", "uuid"
        ];

    }// construct

}//PromotionEntity
