#!/bin/sh

siteId=6

PATHLOG=/opt/nfs/beta/repository/logs/scripts/migration/

if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

LOGFILE=${PATHLOG}/$(date +%Y-%m-%d_%H-%M)-migration.log

echo "Begins News: "$(date +%Y-%m-%d_%H:%M:%S)  >> ${LOGFILE}
/usr/bin/php -c /etc/php5/cli/php.ini -f /opt/nfs/beta/backoffice/migration.php -- 6 10 76 >> ${LOGFILE}
echo "Ends : "$(date +%Y-%m-%d_%H:%M:%S)  >> ${LOGFILE}
