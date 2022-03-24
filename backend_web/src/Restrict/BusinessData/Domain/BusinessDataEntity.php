<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\BusinessData\Domain\BusinessDataEntity
 * @file BusinessDataEntity.php v1.0.0
 * @date %DATE% SPAIN
 */
namespace App\Restrict\BusinessData\Domain;

use App\Shared\Domain\Entities\AppEntity;
use App\Shared\Domain\Enums\EntityType;

final class BusinessDataEntity extends AppEntity
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
            "label" => __("uuid"),
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

        "id_tz" => [
            "label" => __("Timezone"),
            EntityType::REQUEST_KEY => "id_tz",
            "config" => [
                "type" => EntityType::INT,
                "length" => 5,
            ]
        ],

        "business_name" => [
            "label" => __("Business data"),
            EntityType::REQUEST_KEY => "business_name",
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
       
        "user_logo_1" => [
            "label" => __("Url logo sm"),
            EntityType::REQUEST_KEY => "user_logo_1",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "user_logo_2" => [
            "label" => __("Url logo md"),
            EntityType::REQUEST_KEY => "user_logo_2",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "user_logo_3" => [
            "label" => __("Url logo lg"),
            EntityType::REQUEST_KEY => "user_logo_3",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "url_favicon" => [
            "label" => __("Url favicon"),
            EntityType::REQUEST_KEY => "url_favicon",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "head_bgcolor" => [
            "label" => __("Head bg color"),
            EntityType::REQUEST_KEY => "head_bgcolor",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 10,
            ]
        ],
       
        "head_color" => [
            "label" => __("Head color"),
            EntityType::REQUEST_KEY => "head_color",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 10,
            ]
        ],
       
        "head_bgimage" => [
            "label" => __("Head bg image"),
            EntityType::REQUEST_KEY => "head_bgimage",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],

        "body_bgcolor" => [
            "label" => __("Body bg color"),
            EntityType::REQUEST_KEY => "body_bgcolor",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 10,
            ]
        ],
       
        "body_color" => [
            "label" => __("Body color"),
            EntityType::REQUEST_KEY => "body_color",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 10,
            ]
        ],
       
        "body_bgimage" => [
            "label" => __("Url body bg image"),
            EntityType::REQUEST_KEY => "body_bgimage",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "url_business" => [
            "label" => __("Url site"),
            EntityType::REQUEST_KEY => "url_business",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "url_social_fb" => [
            "label" => __("Url Facebook"),
            EntityType::REQUEST_KEY => "url_social_fb",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "url_social_ig" => [
            "label" => __("Url Instagram"),
            EntityType::REQUEST_KEY => "url_social_ig",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "url_social_twitter" => [
            "label" => __("Url Twitter"),
            EntityType::REQUEST_KEY => "url_social_twitter",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "url_social_tiktok" => [
            "label" => __("Url TikTok"),
            EntityType::REQUEST_KEY => "url_social_tiktok",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       ];

        $this->pks = [
            "id", "uuid"
        ];

    }// construct

}//BusinessDataEntity
