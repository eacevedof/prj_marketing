<?php
/*
php	backend/console/run.php <command-alias> param1 param2 .. paramN
./cmd <command-alias> <parameters>
mapping:
    <command-alias> => <namespace-class>
*/
return [
    //commands
    "help"                   => [
        "service"   =>  "App\\Console\\Application\\HelpService",
        "info"      =>  "",
    ],

    "users"                  => [
        "service"   =>  "App\\Console\\Application\\Restrict\\UsersService",
        "info"      =>  "",
    ],

    "build-module"           => [
        "service"   =>  "App\\Console\\Application\\Dev\\Builder\\ModuleBuilderService",
        "info"      =>  "",
    ],

    "get-translation"        => [
        "service"   =>  "App\\Console\\Application\\Dev\\Translation\\TranslationService",
        "info"      =>  "",
    ],
];