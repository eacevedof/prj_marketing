<?php
if (!is_file("../boot/ConsoleMain.php"))
    exit("Boot\ConsoleMain not found!");

debug_print_backtrace();
include_once ("../boot/ConsoleMain.php");

use Boot\ConsoleMain;
try {
    (new ConsoleMain($argv))->exec();
}
catch (Exception | Throwable $ex) {
    print_r("\n".$ex->getMessage()."\n\n");
    ConsoleMain::on_error($ex);
    //ConsoleMain::debug($ex);
}