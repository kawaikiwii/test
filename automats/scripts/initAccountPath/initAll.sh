#! /bin/sh

PATHLOG=/opt/nfs/production/repository/logs/scripts/initAccounts/
OBJECT="account"

if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

/usr/bin/php5 /opt/nfs/production/automats/scripts/initAccountPath/initAccounts.php ${OBJECT} > ${PATHLOG}/$(date +%Y-%m-%d_%H-%M)-initAccounts.log
