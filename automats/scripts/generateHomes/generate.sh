#!/bin/sh

PATHLOG=/opt/nfs/production/repository/logs/scripts/generateHomes/

if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

LOGFILE=${PATHLOG}/$(date +%Y-%m-%d)-homes.log

/usr/bin/php5 /opt/nfs/production/automats/scripts/generateHomes/generateNews.php 		>> ${LOGFILE}
/usr/bin/php5 /opt/nfs/production/automats/scripts/generateHomes/generateSlideshow.php 	>> ${LOGFILE}
/usr/bin/php5 /opt/nfs/production/automats/scripts/generateHomes/generateVideo.php 		>> ${LOGFILE}
/usr/bin/php5 /opt/nfs/production/automats/scripts/generateHomes/generateEvent.php 		>> ${LOGFILE}
/usr/bin/php5 /opt/nfs/production/automats/scripts/generateHomes/generateMainHomes.php  >> ${LOGFILE}