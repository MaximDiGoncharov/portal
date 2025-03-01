#!/usr/bin/bash


if  ! pgrep -f "php8.1.*recieve_imap_process_by_smtp()" ; then 
 /bin/nohup /bin/php8.1 -r "require_once '/home/maxim/repos/portal/init.php';recieve_imap_process_by_smtp();" &> /dev/null < /dev/null &
fi