<?php
/*
 * https://book.cakephp.org/phinx/0/en/migrations.html
 * phinx create CreateBaseArray
 * phinx migrate
 *
 * vendor/bin/phinx init .
 * $EDITOR phinx.yml
 * mkdir -p db/migrations db/seeds
 * vendor/bin/phinx create MyFirstMigration
 * vendor/bin/phinx migrate -e development
 *
 * cd $PATHWEB/db; phinx migrate -e testing
*/
$dir = __DIR__;
include_once "$dir/../vendor/autoload.php";
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
            "name" => "db_mypromos",
            "user" => "root",
            "pass" => "1234",
            "port" => 3306,
            "charset" => "utf8",
        ],
        "development" => [
            "adapter" => "mysql",
            "host" => "host.docker.internal",
            "name" => "db_mypromos",
            "user" => "root",
            "pass" => "1234",
            "port" => 3306,
            "charset" => "utf8",
        ],
        "testing" => [
            "adapter" => "mysql",
            "host" => "host.docker.internal",
            "name" => "db_mypromos_test",
            "user" => "root",
            "pass" => "1234",
            "port" => 3306,
            "charset" => "utf8",
        ]
    ],
    "version_order" => "creation"
];

