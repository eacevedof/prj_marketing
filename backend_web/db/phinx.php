<?php
/*
 * phinx create CreateBaseArray
 * phinx migrate
 */
$dir = __DIR__;

include_once "$dir/migrations/AbsMigration.php";

return [
    "paths" => [
        "migrations" => "$dir/migrations",
        "seeds" => "$dir/seeds"
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_environment" => "development",
        "production" => [
            "adapter" => "mysql",
            "host" => "host.docker.internal",
            "name" => "db_marketing",
            "user" => "root",
            "pass" => "1234",
            "port" => 3306,
            "charset" => "utf8",
        ],
        "development" => [
            "adapter" => "mysql",
            "host" => "host.docker.internal",
            "name" => "db_marketing",
            "user" => "root",
            "pass" => "1234",
            "port" => 3306,
            "charset" => "utf8",
        ],
        "testing" => [
            "adapter" => "mysql",
            "host" => "host.docker.internal",
            "name" => "db_marketing",
            "user" => "root",
            "pass" => "1234",
            "port" => 3306,
            "charset" => "utf8",
        ]
    ],
    "version_order" => "creation"
];

