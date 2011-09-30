#!/bin/sh

PATHLOG=/opt/nfs/production/repository/logs/scripts/generateObjects/

if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

LOGFILE=${PATHLOG}/$(date +%Y-%m-%d_%H-%M)-save

OBJECT="event" # news event slideshow video prevision notice"

#for i in 0 1 2 3 4 5 6 7 8 9 10 11 12 13 14
for i in 0 1
do
    /usr/bin/php5 /opt/nfs/production/automats/scripts/generateObjects/generateObjects.php ${OBJECT} $i >> ${LOGFILE}-$i.log &
done
#/bin/sh /opt/nfs/production/automats/scripts/generateObjects/generateAll.sh
#/usr/bin/php5 /opt/nfs/production/automats/scripts/generateObjects/generateObjects.php ${OBJECT} > ${PATHLOG}/$(date +%Y-%m-%d_%H-%M)-generateObjects.log