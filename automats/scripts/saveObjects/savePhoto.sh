#! /bin/sh

PATHLOG=/opt/nfs/production/repository/logs/scripts/saveObjects/

if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

LOGFILE=${PATHLOG}/$(date +%Y-%m-%d_%H-%M)-photo1.log

/usr/bin/php5 /opt/nfs/production/automats/scripts/saveObjects/savePhoto.php >> ${LOGFILE}
