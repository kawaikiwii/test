#! /bin/sh

PATHLOG=/opt/nfs/beta/repository/logs/scripts/cleanImport/


if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

/usr/bin/php5 /opt/nfs/beta/automats/scripts/cleanImport/cleanImport.php > ${PATHLOG}/$(date +%Y-%m-%d_%H-%M)-cleanImport.log

