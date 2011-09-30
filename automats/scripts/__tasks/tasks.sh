#!/bin/bash
if [ ! -e "/tmp/automats.scripts.__tasks.tasks.sh" ]; then
 #echo "touch tmp"
 touch /tmp/automats.scripts.__tasks.tasks.sh
 PHP_EXE='/usr/bin/php5';
 PHP_INI='/etc/php5/cli/php.ini';
 PHP_FILE='/opt/nfs/production/automats/scripts/__tasks/tasks.php';
 $PHP_EXE  -q -c $PHP_INI -f $PHP_FILE
 #echo "rm tmp"
 rm /tmp/automats.scripts.__tasks.tasks.sh
else
 echo "send mail"
 echo "/opt/nfs/production/automats/scripts/__tasks/tasks.php deja en cours de traitement. pas de relance de la cron"|mail -s "WARNING:/opt/nfs/production/automats/scripts/__tasks/tasks.php" sfoutrel@bcstechno.com
fi
