<?php
//autoload.php 2.0.0

$sPathRoot = dirname(__FILE__);
if(!defined("IS_DEBUG_ALLOWED")) {
    define("IS_DEBUG_ALLOWED", 1);
}

$sPathInclude = get_include_path().PATH_SEPARATOR.$sPathRoot;
set_include_path($sPathInclude);

spl_autoload_register(function ($sNameSpacePath) {
    if (!strstr($sNameSpacePath, "TheFramework\\"))
        return;

    include("array_helpers.php");
    $arExplode = explode("\\", $sNameSpacePath);
    $sClassName = end($arExplode);

    if(isset($arHelpers[$sClassName])) {
        $sFileName = $arHelpers[$sClassName].".php";
        if(stream_resolve_include_path($sFileName)) {
            include_once($sFileName);
        }
    }
});
