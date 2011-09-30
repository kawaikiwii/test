#!/bin/bash

#synchronisation des vidé anglaise de la DEDIBOX
#ATTENTION : les serveurs de DEV, BETA OU PROD doivent etre identifier par ssh au niveau de la DEDIBOX

#Chemin dynamique en fonction des environnements
#Initialise la variable ENV_PATH
CURRENT_PATH=`dirname $0`
. ${CURRENT_PATH}/../../inc/env.conf

#chemin absolu du dossier des logs
PATHLOG=${ENV_PATH}repository/logs/scripts/importAfpVideos/

#chemin absolu du fichier de log
LOGFILE=${PATHLOG}/$(date +%Y-%m-%d)-synchronize.log

echo "Begins french videos synchronisation : "$(date +%Y-%m-%d_%H:%M:%S)
TEST=`rsync -azv -e ssh rnews@88.190.12.180:/var/www/incomings/afp/videos/$(date +%Y)/$(date +%m)/$(date +%d)/*FR.xml ${ENV_PATH}backoffice/business/import/in/AFP-VIDEO/fr/`

if [[ ${TEST} ]]
then
	echo 'success'
else
	echo 'error'
fi

#rsync -azv -e ssh rnews@88.190.12.180:/var/www/incomings/afp/videos/$(date +%Y)/$(date +%m)/$(date +%d)/*FR.xml ${ENV_PATH}backoffice/business/import/in/AFP-VIDEO/fr/ >> ${LOGFILE}
echo "Ends french videos synchronisation : "$(date +%Y-%m-%d_%H:%M:%S)

