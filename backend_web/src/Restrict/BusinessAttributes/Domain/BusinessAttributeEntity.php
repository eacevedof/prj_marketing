<?php
namespace App\Restrict\BusinessAttributes\Domain;

use App\Shared\Domain\Entities\AppEntity;
use App\Shared\Domain\Enums\EntityType;

final class BusinessAttributeEntity extends AppEntity
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
       
        "id_user" => [
            "label" => __("tr_id_user"),
            EntityType::REQUEST_KEY => "id_user",
            "config" => [
                "type" => EntityType::INT,
                "length" => 10,
            ]
        ],
       
        "attr_key" => [
            "label" => __("tr_attr_key"),
            EntityType::REQUEST_KEY => "attr_key",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 250,
            ]
        ],
       
        "attr_value" => [
            "label" => __("tr_attr_value"),
            EntityType::REQUEST_KEY => "attr_value",
            "config" => [
                "type" => EntityType::STRING,
                "length" => 2000,
            ]
        ],
       ];

        $this->pks = [
            "id","id_user", "attr_key"
        ];

    }// construct

}//Business_attributeEntity
