<?php
date_default_timezone_set("UTC");
ob_start();
debug_print_backtrace();

if (!is_file("../boot/IndexMain.php")) exit("Boot\IndexMain not found!");
include_once ("../boot/IndexMain.php");

use Boot\IndexMain;
try {
    (new IndexMain())->exec();
}
catch (Exception | Throwable $ex) {
    IndexMain::debug($ex);
    IndexMain::on_error($ex);
}
ob_end_flush();
