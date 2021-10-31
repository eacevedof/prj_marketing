<?php
/*
php	backend/console/run.php <command-alias> param1 param2 .. paramN
./cmd <command-alias> <parameters>
mapping:
    <command-alias> => <namespace-class>
*/
return [
    //commands
    "get-user-password"                   => "App\\Services\\Console\\Restrict\\UsersService",
];