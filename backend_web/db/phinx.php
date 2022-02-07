<?php
/**
 *  vendor/bin/phinx migrate --configuration db/phinx.php
 *
 */
return [
    "paths" => [
        "migrations" => "./migrations",
        "seeds" => "./seeds"
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_database" => "local",
        "production" => [
            "adapter" => "mysql",
            "host" => "host.docker.internal",
            "name" => "db_marketing",
            "user" => "root",
            "pass" => "1234",
            "port" => 3306,
            "charset" => "utf8",
            "table_prefix" => ""
        ],
        "local" => [
            "adapter" => "mysql",
            "host" => "host.docker.internal",
            "name" => "db_marketing",
            "user" => "root",
            "pass" => "1234",
            "port" => 3306,
            "charset" => "utf8",
            "table_prefix" => ""
        ],
    ],
    "version_order" => "creation"
];