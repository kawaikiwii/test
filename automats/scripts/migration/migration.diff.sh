#!/bin/sh

siteId=6

PATHLOG=/opt/nfs/production/repository/logs/scripts/migration/

if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

LOGFILE=${PATHLOG}/$(date +%Y-%m-%d)-migration.log

echo "###########################################################################################################"  >> ${LOGFILE}
echo "Begins News differential: "$(date +%Y-%m-%d_%H:%M:%S)  >> ${LOGFILE}

/usr/bin/php -c /etc/php5/cli/php.ini -f /opt/nfs/production/backoffice/migration.php -- $siteId 9 80 >> ${LOGFILE}

echo "Ends : "$(date +%Y-%m-%d_%H:%M:%S)  >> ${LOGFILE}
