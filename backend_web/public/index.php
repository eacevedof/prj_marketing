<?php
ob_start();
debug_print_backtrace();
session_name("MARKETINGID");
session_start();

use Boot\Index;
include_once ("../boot/index.php");

try {
    //throw new Exception("example");
    (new Index())->exec();
}
catch (Exception $ex) {
    Index::on_error($ex);
}
catch (Throwable $ex) {
    Index::on_error($ex);
}
ob_end_flush();
