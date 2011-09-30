#! /bin/sh

PATHLOG=../../../repository/logs/scripts/reindexObjects/
#OBJECT="article channel event exportRule folder forecast location news newsletter notice organisation otvEmission otvFinale otvPortrait person photo relaxTask site slideshow video work"
#OBJECT="article channel exportRule folder  location  newsletter  organisation otvEmission otvFinale otvPortrait person  relaxTask site work"
#OBJECT="slideshow video news photo event notice forecast"
OBJECT="event"

if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

/usr/bin/php5 ./reindexObjects.php ${OBJECT} > ${PATHLOG}/$(date +%Y-%m-%d_%H-%M)-reindexObject.log
