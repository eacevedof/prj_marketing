<?php
ob_start();
debug_print_backtrace();

if (!is_file("../boot/ConsoleMain.php"))
    exit("Boot\ConsoleMain not found!");

include_once ("../boot/ConsoleMain.php");

use Boot\ConsoleMain;
try {
    (new ConsoleMain($argv))->exec();
}
catch (Exception | Throwable $ex) {
    ConsoleMain::on_error($ex);
}
ob_end_flush();
