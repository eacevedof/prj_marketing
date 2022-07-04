<?php
//ini_set("sendmail_path", "/usr/bin/msmtp -t -i -X ./mail.log -d >> ./msmtp.log");
$r = mail("eacevedof@gmail.com","titulo","mensaje");

print_r("result: $r .");
//phpinfo();