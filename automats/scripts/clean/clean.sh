#! /bin/sh

PATHLOG=/opt/nfs/production/repository/logs/scripts/clean/


if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

LOGFILE=${PATHLOG}/$(date +%Y-%m-%d_%H-%M)-clean-TME.log

echo "COPY LOGS export/task" >> ${LOGFILE}
cp -rf /opt/nfs/production/repository/logs/exports /opt/nfs/production/repository/ARCHIVES/logs/ >> ${LOGFILE}
cp -rf /opt/nfs/production/repository/logs/tasks /opt/nfs/production/repository/ARCHIVES/logs/ >> ${LOGFILE}

echo "CLEAN LOGS" >> ${LOGFILE}
find /opt/nfs/production/repository/logs -type f -name "*.log" -mtime +2 -a -print -delete >> ${LOGFILE}

echo "CLEAN IMPORTS" >> ${LOGFILE}
/usr/bin/php5 /opt/nfs/production/automats/scripts/clean/cleanImport.php >> ${LOGFILE}

echo "CLEAN BACKUPs" >> ${LOGFILE}
find /opt/nfs/production/_backup_ -type f -name "*.tgz" -mtime +10 -a -print -delete >> ${LOGFILE}

echo "CLEAN FEEDS" >> ${LOGFILE}
# ajouter ici les repertoires qui ont besoin d etre purges regulierement
find -P /opt/nfs/production/feeds/afpmobile_c7adb7f5c11343824e018d4fc7041fe0 -type f -mtime +20 -a -print -delete >> ${LOGFILE}
find -P /opt/nfs/production/feeds/wmedia_b2b7963509c6e85d42cfe7158e5decc5 -type f -mtime +20 -a -print -delete >> ${LOGFILE}
find -P /opt/nfs/production/feeds/spinetix_ab0949f7a32eceb571f57fc8df3e828e -type f -mtime +20 -a -print -delete >> ${LOGFILE}


echo "CLEAN Smarty Cache files Begin : `date +%Y-%m-%d_%H-%M`" >> ${LOGFILE}
find /tmp/wcm/Smarty/templates_c/ -type f -mtime +1 -a -print -delete >> ${LOGFILE}
echo "CLEAN Smarty Cache files Finish : `date +%Y-%m-%d_%H-%M`" >> ${LOGFILE}
