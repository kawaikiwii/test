#!/bin/sh

PATHLOG=/opt/nfs/production/repository/logs/scripts/saveObjects/

if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

LOGFILE=${PATHLOG}/$(date +%Y-%m-%d_%H-%M)-save2

OBJECT="photo" # news event slideshow video  forecast"

for i in 0 1

do
    /usr/bin/php5 /opt/nfs/production/automats/scripts/saveObjects/save2.php ${OBJECT} $i >> ${LOGFILE}-$i.log
done

