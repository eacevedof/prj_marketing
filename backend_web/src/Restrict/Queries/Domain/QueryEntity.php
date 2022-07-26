<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Queries\Domain\QueryEntity
 * @file QueryEntity.php v1.0.0
 * @date %DATE% SPAIN
 */
namespace App\Restrict\Queries\Domain;

use App\Shared\Domain\Entities\AppEntity;
use App\Shared\Domain\Enums\EntityType;

final class QueryEntity extends AppEntity
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

            "description" => [
                "label" => __("Description"),
                EntityType::REQUEST_KEY => "description",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 250,
                ]
            ],

            "query" => [
                "label" => __("tr_query"),
                EntityType::REQUEST_KEY => "query",
                "config" => [
                    "type" => EntityType::STRING,
                ]
            ],

            "total" => [
                "label" => __("tr_total"),
                EntityType::REQUEST_KEY => "total",
                "config" => [
                    "type" => EntityType::INT,
                ]
            ],

            "module" => [
                "label" => __("tr_module"),
                EntityType::REQUEST_KEY => "module",
                "config" => [
                    "type" => EntityType::STRING,
                    "length" => 50,
                ]
            ],
       ];

        $this->pks = [
            "id", "uuid"
        ];

    }// construct

}//QueryEntity
