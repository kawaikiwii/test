#!/bin/bash

if [ ! -e /tmp/rsync.en.cours.syncweb.sh ]
then
        touch /tmp/rsync.en.cours.syncweb.sh
	echo "rsync des photos"
	rsync -az /var/www/repository.afprelaxnews.com/illustration/photo/* 193.110.140.78:/var/www/beta/repository/illustration/photo/
	#echo "rsync des videos"
	#rsync -az 193.110.140.65:/var/www/uploads/* /opt/nfs/production/repository/illustration/video/
        rm /tmp/rsync.en.cours.syncweb.sh
else
	echo "WARNING : Une autre instance de Rsync est en cours"|mail -a "From : <nobody@relaxnews.com>" -s "Rsync en attente sur AFPRELAX-WEB01" jlaplanche@relaxnews.com
fi
