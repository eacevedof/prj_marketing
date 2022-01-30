<?php
ob_start();
debug_print_backtrace();
session_name("MARKETINGID");
session_start();

use Boot\IndexMain;
if (!is_file("../boot/IndexMain.php")) exit("Boot\IndexMain not found!");
include_once ("../boot/IndexMain.php");

try {
    //throw new Exception("example");
    (new IndexMain())->exec();
}
catch (Exception $ex) {
    IndexMain::on_error($ex);
}
catch (Throwable $ex) {
    IndexMain::on_error($ex);
}
ob_end_flush();
