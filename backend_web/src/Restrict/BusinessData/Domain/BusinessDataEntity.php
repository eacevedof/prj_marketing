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
            "label" => __("tr_id"),
            EntityType::REQUEST_KEY => "id",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "uuid" => [
            "label" => __("tr_uuid"),
            EntityType::REQUEST_KEY => "uuid",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 50,
            ]
        ],
       
        "id_user" => [
            "label" => __("tr_id_user"),
            EntityType::REQUEST_KEY => "id_user",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "slug" => [
            "label" => __("tr_slug"),
            EntityType::REQUEST_KEY => "slug",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 250,
            ]
        ],
       
        "user_logo_1" => [
            "label" => __("tr_user_logo_1"),
            EntityType::REQUEST_KEY => "user_logo_1",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "user_logo_2" => [
            "label" => __("tr_user_logo_2"),
            EntityType::REQUEST_KEY => "user_logo_2",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "user_logo_3" => [
            "label" => __("tr_user_logo_3"),
            EntityType::REQUEST_KEY => "user_logo_3",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "url_favicon" => [
            "label" => __("tr_url_favicon"),
            EntityType::REQUEST_KEY => "url_favicon",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "head_bgcolor" => [
            "label" => __("tr_head_bgcolor"),
            EntityType::REQUEST_KEY => "head_bgcolor",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 10,
            ]
        ],
       
        "head_color" => [
            "label" => __("tr_head_color"),
            EntityType::REQUEST_KEY => "head_color",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 10,
            ]
        ],
       
        "head_bgimage" => [
            "label" => __("tr_head_bgimage"),
            EntityType::REQUEST_KEY => "head_bgimage",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 10,
            ]
        ],
       
        "body_bgcolor" => [
            "label" => __("tr_body_bgcolor"),
            EntityType::REQUEST_KEY => "body_bgcolor",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 10,
            ]
        ],
       
        "body_color" => [
            "label" => __("tr_body_color"),
            EntityType::REQUEST_KEY => "body_color",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 10,
            ]
        ],
       
        "body_bgimage" => [
            "label" => __("tr_body_bgimage"),
            EntityType::REQUEST_KEY => "body_bgimage",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "site" => [
            "label" => __("tr_site"),
            EntityType::REQUEST_KEY => "site",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "url_social_fb" => [
            "label" => __("tr_url_social_fb"),
            EntityType::REQUEST_KEY => "url_social_fb",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "url_social_ig" => [
            "label" => __("tr_url_social_ig"),
            EntityType::REQUEST_KEY => "url_social_ig",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "url_social_twitter" => [
            "label" => __("tr_url_social_twitter"),
            EntityType::REQUEST_KEY => "url_social_twitter",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 100,
            ]
        ],
       
        "url_social_tiktok" => [
            "label" => __("tr_url_social_tiktok"),
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
