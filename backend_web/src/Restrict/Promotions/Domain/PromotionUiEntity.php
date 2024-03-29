<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Promotions\Domain\PromotionUiEntity
 * @file PromotionUiEntity.php v1.0.0
 * @date %DATE% SPAIN
 */

namespace App\Restrict\Promotions\Domain;

use App\Shared\Domain\Enums\EntityType;
use App\Shared\Domain\Entities\AppEntity;

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

            "id_promotion" => [
                "label" => __("Promotion"),
                EntityType::REQUEST_KEY => "id_promotion",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "input_email" => [
                "label" => __("Email"),
                EntityType::REQUEST_KEY => "input_email",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "pos_email" => [
                "label" => __("Pos. Email"),
                EntityType::REQUEST_KEY => "pos_email",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "input_name1" => [
                "label" => __("First name"),
                EntityType::REQUEST_KEY => "input_name1",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "pos_name1" => [
                "label" => __("Pos. First name"),
                EntityType::REQUEST_KEY => "pos_name1",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "input_name2" => [
                "label" => __("Last name"),
                EntityType::REQUEST_KEY => "input_name2",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "pos_name2" => [
                "label" => __("Pos. Last name"),
                EntityType::REQUEST_KEY => "pos_name2",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "input_language" => [
                "label" => __("Language"),
                EntityType::REQUEST_KEY => "input_language",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "pos_language" => [
                "label" => __("Pos. Language"),
                EntityType::REQUEST_KEY => "pos_language",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "input_country" => [
                "label" => __("Country"),
                EntityType::REQUEST_KEY => "input_country",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "pos_country" => [
                "label" => __("Pos. Country"),
                EntityType::REQUEST_KEY => "pos_country",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "input_phone1" => [
                "label" => __("Phone"),
                EntityType::REQUEST_KEY => "input_phone1",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "pos_phone1" => [
                "label" => __("Pos. Phone"),
                EntityType::REQUEST_KEY => "pos_phone1",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "input_birthdate" => [
                "label" => __("Birthdate"),
                EntityType::REQUEST_KEY => "input_birthdate",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "pos_birthdate" => [
                "label" => __("Pos. Birthdate"),
                EntityType::REQUEST_KEY => "pos_birthdate",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "input_gender" => [
                "label" => __("Gender"),
                EntityType::REQUEST_KEY => "input_gender",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "pos_gender" => [
                "label" => __("Pos. Gender"),
                EntityType::REQUEST_KEY => "pos_gender",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "input_address" => [
                "label" => __("Address"),
                EntityType::REQUEST_KEY => "input_address",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "pos_address" => [
                "label" => __("Pos. Address"),
                EntityType::REQUEST_KEY => "pos_address",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],
            "input_is_mailing" => [
                "label" => __("Allow mailing"),
                EntityType::REQUEST_KEY => "input_is_mailing",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "pos_is_mailing" => [
                "label" => __("Pos. Allow mailing"),
                EntityType::REQUEST_KEY => "pos_is_mailing",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "input_is_terms" => [
                "label" => __("Read terms and conditions"),
                EntityType::REQUEST_KEY => "input_is_terms",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
                ]
            ],

            "pos_is_terms" => [
                "label" => __("Pos. Read terms and conditions"),
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
