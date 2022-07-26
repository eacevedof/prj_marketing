<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Promotions\Domain\PromotionUiEntity
 * @file PromotionUiEntity.php v1.0.0
 * @date %DATE% SPAIN
 */
namespace App\Restrict\Promotions\Domain;

use App\Shared\Domain\Entities\AppEntity;
use App\Shared\Domain\Enums\EntityType;

final class PromotionUiEntity extends AppEntity
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
            "label" => __("tr_uuid"),
            EntityType::REQUEST_KEY => "uuid",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 50,
            ]
        ],
       
        "id_owner" => [
            "label" => __("tr_id_owner"),
            EntityType::REQUEST_KEY => "id_owner",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "code_erp" => [
            "label" => __("tr_code_erp"),
            EntityType::REQUEST_KEY => "code_erp",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 25,
            ]
        ],
       
        "description" => [
            "label" => __("tr_description"),
            EntityType::REQUEST_KEY => "description",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 250,
            ]
        ],
       
        "id_promotion" => [
            "label" => __("tr_id_promotion"),
            EntityType::REQUEST_KEY => "id_promotion",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "input_email" => [
            "label" => __("tr_input_email"),
            EntityType::REQUEST_KEY => "input_email",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "pos_email" => [
            "label" => __("tr_pos_email"),
            EntityType::REQUEST_KEY => "pos_email",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "input_name1" => [
            "label" => __("tr_input_name1"),
            EntityType::REQUEST_KEY => "input_name1",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "pos_name1" => [
            "label" => __("tr_pos_name1"),
            EntityType::REQUEST_KEY => "pos_name1",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "input_name2" => [
            "label" => __("tr_input_name2"),
            EntityType::REQUEST_KEY => "input_name2",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "pos_name2" => [
            "label" => __("tr_pos_name2"),
            EntityType::REQUEST_KEY => "pos_name2",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "input_language" => [
            "label" => __("tr_input_language"),
            EntityType::REQUEST_KEY => "input_language",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "pos_language" => [
            "label" => __("tr_pos_language"),
            EntityType::REQUEST_KEY => "pos_language",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "input_country" => [
            "label" => __("tr_input_country"),
            EntityType::REQUEST_KEY => "input_country",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "pos_country" => [
            "label" => __("tr_pos_country"),
            EntityType::REQUEST_KEY => "pos_country",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "input_phone1" => [
            "label" => __("tr_input_phone1"),
            EntityType::REQUEST_KEY => "input_phone1",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "pos_phone1" => [
            "label" => __("tr_pos_phone1"),
            EntityType::REQUEST_KEY => "pos_phone1",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "input_birthdate" => [
            "label" => __("tr_input_birthdate"),
            EntityType::REQUEST_KEY => "input_birthdate",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "pos_birthdate" => [
            "label" => __("tr_pos_birthdate"),
            EntityType::REQUEST_KEY => "pos_birthdate",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "input_gender" => [
            "label" => __("tr_input_gender"),
            EntityType::REQUEST_KEY => "input_gender",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "pos_gender" => [
            "label" => __("tr_pos_gender"),
            EntityType::REQUEST_KEY => "pos_gender",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "input_address" => [
            "label" => __("tr_input_address"),
            EntityType::REQUEST_KEY => "input_address",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "pos_address" => [
            "label" => __("tr_pos_address"),
            EntityType::REQUEST_KEY => "pos_address",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
        "input_is_mailing" => [
            "label" => __("tr_input_is_mailing"),
            EntityType::REQUEST_KEY => "input_is_mailing",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],

        "pos_is_mailing" => [
            "label" => __("tr_pos_is_mailing"),
            EntityType::REQUEST_KEY => "pos_is_mailing",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],

        "input_is_terms" => [
            "label" => __("tr_input_is_terms"),
            EntityType::REQUEST_KEY => "input_is_terms",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],

        "pos_is_terms" => [
            "label" => __("tr_pos_is_terms"),
            EntityType::REQUEST_KEY => "pos_is_terms",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
        ];

        $this->pks = [
            "id", "uuid"
        ];

    }// construct

}//PromotionUiEntity
