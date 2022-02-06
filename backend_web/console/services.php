<?php
/*
php	backend/console/run.php <command-alias> param1 param2 .. paramN
./cmd <command-alias> <parameters>
mapping:
    <command-alias> => <namespace-class>
*/
return [
    //commands
    "help"                   => "App\\Console\\Application\\HelpService",
    "users"                  => "App\\Console\\Application\\Restrict\\UsersService",
    "build-module"           => "App\\Console\\Application\\Dev\\Builder\\ModuleBuilderService",
];