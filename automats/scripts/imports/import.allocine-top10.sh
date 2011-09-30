#!/bin/sh

PATHLOG=/opt/nfs/production/repository/logs/scripts/import/allocine

if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

LOGFILE=${PATHLOG}/$(date +%Y-%m-%d_%H-%M)-allocine-top10.log
echo "BEGIN" >> ${LOGFILE}
/usr/bin/php /opt/nfs/production/automats/scripts/imports/import.php allocine_top10 >> ${LOGFILE}
echo "END" >> ${LOGFILE}
