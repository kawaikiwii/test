#!/bin/sh

PATHLOG=/opt/nfs/production/repository/logs/scripts/saveObjects/

if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

LOGFILE=${PATHLOG}/$(date +%Y-%m-%d_%H-%M)-save

OBJECT="photo" # news event slideshow video  forecast"

for i in 0 1 2 3 4 5 6 7 8 9 10 11 12 13 14
#for i in 0 1
do
    /usr/bin/php5 /opt/nfs/production/automats/scripts/saveObjects/save.php ${OBJECT} $i >> ${LOGFILE}-$i.log &
done

