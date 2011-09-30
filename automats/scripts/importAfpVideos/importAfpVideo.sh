#!/bin/sh

#Chemin dynamique en fonction des environnements
#Initialise la variable ENV_PATH
CURRENT_PATH=`dirname $0`
. ${CURRENT_PATH}/../../inc/env.conf

PATHLOG=${ENV_PATH}repository/logs/scripts/importAfpVideos/

if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

LOGFILE=${PATHLOG}/$(date +%Y-%m-%d_%H-%M)-import.log

echo "Begins Videos: "$(date +%Y-%m-%d_%H:%M:%S)  >> ${LOGFILE}

/usr/bin/php -c /etc/php5/cli/php.ini -f ${ENV_PATH}backoffice/business/import/importAfpVideos.php >> ${LOGFILE}

echo "\nEnds : "$(date +%Y-%m-%d_%H:%M:%S)  >> ${LOGFILE}
