PATHLOG=/opt/nfs/production/repository/logs/scripts/import/airquality/
LOGFILE=${PATHLOG}$(date +%Y-%m-%d_%H-%M)-airquality.log
echo "Create News Barometre Carburants : "$(date +%Y-%m-%d_%H:%M:%S)  >> ${LOGFILE}
/usr/bin/php /opt/nfs/production/automats/scripts/imports/import.php airquality >> ${LOGFILE}
echo "Ends : "$(date +%Y-%m-%d_%H:%M:%S)  >> ${LOGFILE}