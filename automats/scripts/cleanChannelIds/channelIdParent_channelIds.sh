#!/bin/bash

#Chemin dynamique en fonction des environnements
#Initialise la variable ENV_PATH
CURRENT_PATH=`dirname $0`
. ${CURRENT_PATH}/../../inc/env.conf

#chemin du dossier de logs
PATHLOG=${ENV_PATH}repository/logs/scripts/cleanChannelIds/

#chemin du script
CLEAN_PATH=${ENV_PATH}automats/scripts/cleanChannelIds/

#on regarde si le dossier existe
if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

/usr/bin/php5 ${CLEAN_PATH}channelIdParent_channelIds2.php $FILE_NAME $DATE_FILE >> ${PATHLOG}/$(date +%Y-%m-%d)-cleanChannelIds.log



