#!/bin/sh

PATHLOG=/opt/nfs/production/repository/logs/scripts/alertExpireAccounts/

if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

LOGFILE=${PATHLOG}/$(date +%Y-%m-%d)-alertexpireaccounts.log

/usr/bin/php5 /opt/nfs/production/automats/scripts/alertExpireAccounts/notConnected.php 		>> ${LOGFILE}