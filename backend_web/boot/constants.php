<?php
//constants.php 20200721
if (!defined("DS")) define("DS", DIRECTORY_SEPARATOR);
define("PATH_ROOT", dirname(__DIR__));
define("PATH_PUBLIC", PATH_ROOT.DS."public");
define("PATH_VENDOR", PATH_ROOT.DS."vendor");
define("PATH_SRC", PATH_ROOT.DS."src");
define("PATH_DISK_CACHE", PATH_ROOT.DS."cache");
define("PATH_SRC_CONFIG", PATH_ROOT.DS."config");
define("PATH_LOGS", PATH_ROOT.DS."logs");
define("PATH_CONSOLE", PATH_ROOT.DS."console");