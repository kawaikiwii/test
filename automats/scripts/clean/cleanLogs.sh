#! /bin/sh

PATHLOG=/opt/nfs/beta/repository/logs/scripts/cleanLogs/


if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

find /opt/nfs/beta/repository/logs -type f -mtime +2 -a -print -delete > ${PATHLOG}/$(date +%Y-%m-%d_%H-%M)-$USER.log
