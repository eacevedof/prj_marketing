<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Queries\Domain\Query_actionsEntity
 * @file Query_actionsEntity.php v1.0.0
 * @date %DATE% SPAIN
 */
namespace App\Restrict\Queries\Domain;

use App\Shared\Domain\Entities\AppEntity;
use App\Shared\Domain\Enums\EntityType;

final class QueryActionsEntity extends AppEntity
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

            "id_query" => [
                "label" => __("tr_id_query"),
                EntityType::REQUEST_KEY => "id_query",
                "config" => [
                    "type" => EntityType::INT,
                    "length" => 10,
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
       ];

        $this->pks = [
            "id", "uuid"
        ];

    }// construct

}//Query_actionsEntity
