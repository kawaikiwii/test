#!/bin/sh

#Envoi de nos vidéos à MSN Singapore
#Une tache s'occupe de créer les xmls associés aux vidéos, ce sont ces xmls que l'on parse, et que l'on balance a leur web service REST

#Chemin dynamique en fonction des environnements
#Initialise la variable ENV_PATH
CURRENT_PATH=`dirname $0`
. ${CURRENT_PATH}/../../inc/env.conf

#chemin absolu du dossier des logs
PATHLOG=${ENV_PATH}repository/logs/scripts/exportMsnSingapore

#chemin absolu du fichier de log
LOGFILE=${PATHLOG}/$(date +%Y-%m-%d)-exportMsnSingapore.log

#on regarde si le dossier existe, sinon on le cree
if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi

/usr/bin/php5 ${ENV_PATH}automats/scripts/exportMsnSingapore/exportMsnSingapore.php >> ${LOGFILE}
