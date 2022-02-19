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
        "info"      =>  "como parametro se le pasa el nombre de la tabla",
    ],

    "get-translation"        => [
        "service"   =>  "App\\Console\\Application\\Dev\\Translation\\TranslationService",
        "info"      =>  "--not-used los que hay en el .po pero no se invocan\n\t--repeated los que estan duplicados en el po".
                        "\n\tsin parametros recupera todas las traducciones que no existen en default.po",
    ],
];