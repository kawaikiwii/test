#!/bin/bash

#Chemin dynamique en fonction des environnements
#Initialise la variable ENV_PATH
CURRENT_PATH=`dirname $0`
. ${CURRENT_PATH}/../../inc/env.conf

#chemin du dossier de logs
PATHLOG=${ENV_PATH}repository/logs/scripts/cleanImport/

#chemin des scripts de nettoyage
CLEAN_PATH=${ENV_PATH}automats/scripts/cleanImport/

#on regarde si le dossier existe, sinon on le créé
if test -d ${PATHLOG}
  then echo -n ""
  else mkdir ${PATHLOG}
fi
#chemin vers les newsMLs
VIDEOS_PATH=${ENV_PATH}backoffice/business/import/in/AFP-VIDEO/

#variable qui stocke le contenu du dossier des newsML fr
CONTENT=`ls -ltc ${VIDEOS_PATH}fr/*.xml | awk -F" " '{print $9}'`
#si la commande ls retourne quelque chose
if [[ -n $CONTENT ]]
then 
	echo $(date +%Y-%m-%d_%T) >> ${PATHLOG}/$(date +%Y-%m-%d)-cleanImportAfpVideoEn.log
	#on traitre chaque fichier trouve
	for line in $CONTENT
	do
		#on extrait la date de derniere modification du fichier
		DATE_FILE=$(date '+%Y-%m-%d' -r $line)
		#on extrait son nom
		FILE_NAME=$(echo $line | awk -F"/" '{print $11}' | awk -F"." '{print $1}')
		#on verifie que la date n est pas vide (sous peine de delete total des images !!!
		if [[ -n $DATE_FILE  ]]
		then
			#on verifie que le nom de fichier n'est pas vide (pour eviter tous les comportements problematiques)
			if [[ -n $FILE_NAME ]]
			then
				#Si le nom de fichier et la date sont ok, on execute le fichier php correspondant en passant les parametre date et nom
				/usr/bin/php5 ${CLEAN_PATH}cleanImportAfpVideo.php $FILE_NAME $DATE_FILE >> ${PATHLOG}/$(date +%Y-%m-%d)-cleanImportAfpVideoFr.log
			fi
		fi
	done
	echo $(date +%Y-%m-%d_%T) >> ${PATHLOG}/$(date +%Y-%m-%d)-cleanImportAfpVideoEn.log
fi

CONTENT=`ls -ltc ${VIDEOS_PATH}en/*.xml | awk -F" " '{print $9}'`
#si la commande ls retourne quelque chose
if [[ -n $CONTENT ]]
then
	echo $(date +%Y-%m-%d_%T) >> ${PATHLOG}/$(date +%Y-%m-%d)-cleanImportAfpVideoEn.log
        #on traitre chaque fichier trouve
        for line in $CONTENT
        do
                #on extrait la date de derniere modification du fichier
                DATE_FILE=$(date '+%Y-%m-%d' -r $line)
                #on extrait son nom
                FILE_NAME=$(echo $line | awk -F"/" '{print $11}' | awk -F"." '{print $1}')
                #on verifie que la date n est pas vide (sous peine de delete total des images !!!
                if [[ -n $DATE_FILE  ]]
                then
                        #on verifie que le nom de fichier n'est pas vide (pour eviter tous les comportements problematiques)
                        if [[ -n $FILE_NAME ]]
                        then
                                #Si le nom de fichier et la date sont ok, on execute le fichier php correspondant en passant les parametre date et nom
				/usr/bin/php5 ${CLEAN_PATH}cleanImportAfpVideo.php $FILE_NAME $DATE_FILE >> ${PATHLOG}/$(date +%Y-%m-%d)-cleanImportAfpVideoEn.log
                        fi
                fi
        done
	echo $(date +%Y-%m-%d_%T) >> ${PATHLOG}/$(date +%Y-%m-%d)-cleanImportAfpVideoEn.log
fi

