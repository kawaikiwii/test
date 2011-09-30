#!/bin/sh

PATHLOG=/opt/nfs/production/repository/logs/scripts/generateObjects/

if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

LOGFILE=${PATHLOG}/photo.log

/usr/bin/php5 /opt/nfs/production/automats/scripts/generateObjects/check_photo_carre.php >> ${LOGFILE}
