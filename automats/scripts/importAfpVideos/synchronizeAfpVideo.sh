#!/bin/sh

#synchronisation des vidé anglaise de la DEDIBOX
#ATTENTION : les serveurs de DEV, BETA OU PROD doivent etre identifier par ssh au niveau de la DEDIBOX

#Chemin dynamique en fonction des environnements
#Initialise la variable ENV_PATH
CURRENT_PATH=`dirname $0`
. ${CURRENT_PATH}/../../inc/env.conf

#chemin absolu du dossier des logs
PATHLOG=${ENV_PATH}repository/logs/scripts/importAfpVideos

#chemin absolu du fichier de log
LOGFILE=${PATHLOG}/$(date +%Y-%m-%d)-synchronize.log

#on regarde si le dossier existe, sinon on le cree
if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

/usr/bin/php5 ${ENV_PATH}automats/scripts/importAfpVideos/synchronizeAfpVideo.php >> ${LOGFILE}

