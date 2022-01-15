<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Services\Dbs\CoreQueriesService
 * @file CoreQueriesService.php 1.0.0
 * @date 15-01-2018 19:00 SPAIN
 * @observations
 */
namespace App\Services\Dbs;

use App\Services\AppService;

final class CoreQueriesService extends AppService
{

    public function get_fields_min(string $dbname, string $table): string
    {
        return "
        /*CoreQueriesService.get_fields_min*/
        SELECT information_schema.columns.column_name field_name
        FROM information_schema.columns 
        WHERE 1
        AND table_schema = '$dbname'
        AND table_name = '$table'        
        ";
    }//get_fields

    public function get_fields(string $dbname, string $table): string
    {
        return "
        /*CoreQueriesService.get_fields*/
        SELECT DISTINCT table_name,LOWER(column_name) AS field_name
        ,LOWER(DATA_TYPE) AS field_type
        ,IF(pkfields.field_name IS null,0,1) is_pk
        ,character_maximum_length AS field_length
        ,numeric_precision ntot
        ,numeric_scale ndec
        ,extra 
        FROM information_schema.columns 
        LEFT JOIN 
        (
            SELECT key_column_usage.column_name field_name
            FROM information_schema.key_column_usage
            WHERE 1
            AND table_schema = '$dbname'
            AND constraint_name = 'PRIMARY'
            AND table_name = '$table'
        ) AS pkfields
        ON information_schema.columns.column_name = pkfields.field_name
        WHERE table_name='$table'
        AND table_schema='$dbname'
        ORDER BY ordinal_position ASC
        ";
    }//get_fields

    public function get_tables(string $dbname, ?string $table=null): string
    {
        $sql = "
        /*CoreQueriesService.get_tables*/
        SELECT table_name 
        FROM information_schema.tables 
        WHERE 1
        AND table_schema='$dbname'
        ";
        if($table) $sql .= " AND table_name='$table'";

        $sql .= " ORDER BY 1";
        //pr($sql,"sql");
        return $sql;
    }//get_tables

    public function get_field(string $dbname, string $table, string $field): string
    {
        return "
        /*CoreQueriesService.get_fields*/
        SELECT DISTINCT table_name,LOWER(column_name) AS field_name
        ,LOWER(DATA_TYPE) AS field_type
        ,IF(pkfields.field_name IS null,0,1) is_pk
        ,character_maximum_length AS field_length
        ,numeric_precision ntot
        ,numeric_scale ndec
        ,extra 
        FROM information_schema.columns 
        LEFT JOIN 
        (
            SELECT key_column_usage.column_name field_name
            FROM information_schema.key_column_usage
            WHERE 1
            AND table_schema = '$dbname'
            AND constraint_name = 'PRIMARY'
            AND table_name = '$table'
        ) AS pkfields
        ON information_schema.columns.column_name = pkfields.field_name
        WHERE table_name='$table'
        AND table_schema='$dbname'
        AND LOWER(column_name) = '$field'
        ORDER BY ordinal_position ASC
        ";
    }//get_fields


}//CoreQueriesService