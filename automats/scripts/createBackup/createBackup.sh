#! /bin/sh

PATHLOG=/opt/nfs/production/repository/logs/scripts/backup/

if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

LOGFILE=${PATHLOG}/$(date +%Y-%m-%d_%H-%M)-backup.log

BASEPATH=/opt/nfs/production/
TGZDATE=$(date +%Y.%m.%d-%H.%M.00)

for arg in $*
do
 	BCKPATH=${BASEPATH}$arg
	tar -czf ${BASEPATH}_backup_/${TGZDATE}.${arg}.tgz `find ${BCKPATH} -type f -mtime -1 -print` >> ${LOGFILE}
done
