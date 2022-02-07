<?php
// vendor/bin/phinx migrate --configuration db/phinx.php
return [
    "paths" => [
        "migrations" => "./migrations",
        "seeds" => "./seeds"
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_database" => env("DB_ENV"),
        "production" => [
            "adapter" => "mysql",
            "host" => "host.docker.internal",
            "name" => "db_marketing",
            "user" => "root",
            "pass" => "1234",
            "port" => 3306,
            "charset" => env("DB_CHARSET", "utf8"),
            "table_prefix" => ""
        ],
        "development" => [
            "adapter" => env("DB_CONNECTION"),
            "host" => env("DB_HOST"),
            "name" => env("DB_DATABASE"),
            "user" => env("DB_USERNAME"),
            "pass" => env("DB_PASSWORD"),
            "port" => env("DB_PORT", 3306),
            "charset" => env("DB_CHARSET", "utf8"),
            "table_prefix" => ""
        ]
    ],
    "version_order" => "creation"
];