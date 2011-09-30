#! /bin/sh

PATHLOG=../../../repository/logs/scripts/reindexObjects/
#OBJECT="article channel event exportRule folder forecast location news newsletter notice organisation otvEmission otvFinale otvPortrait person photo relaxTask site slideshow video work"
OBJECT="photo poll"

if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

/usr/bin/php5 ./deindexObjects.php ${OBJECT} > ${PATHLOG}/$(date +%Y-%m-%d_%H-%M)-deindexObject.log
