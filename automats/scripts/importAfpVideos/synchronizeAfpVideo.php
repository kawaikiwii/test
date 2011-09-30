<?php
//SYNCHRO FR AFP
echo "Begins french videos synchronisation : ".date("Y-m-d H:i:s")."\n";
$retourfr = system('rsync -rltz -e ssh rnews@88.190.12.180:/var/www/incomings/afp/videos/'.date('Y').'/'.date('m').'/'.date('d').'/*FR.xml '.dirname(__FILE__).'/../../../backoffice/business/import/in/AFP-VIDEO/fr/',$syncFr);
if($syncFr==0){
	echo "\tSynchronisation xml fr ... OK\n";
}else{
	echo "\tSynchronisation xml fr ... Echec\n";
}
echo "Ends french videos synchronisation : ".date('Y-m-d H:i:s')."\n";
//SYNCHRO FR PARIS MODES
echo "Begins french videos Paris modes synchronisation : ".date("Y-m-d H:i:s")."\n";
$retourfr = system('rsync -rltz -e ssh rnews@88.190.12.180:/var/www/incomings/afp/videos/'.date('Y').'/'.date('m').'/'.(date('d')-1).'/PMV*FR.xml '.dirname(__FILE__).'/../../../backoffice/business/import/in/AFP-VIDEO/fr/',$syncFr);
if($syncFr==0){
        echo "\tSynchronisation xml paris modes fr ... OK\n";
}else{
        echo "\tSynchronisation xml paris modes fr ... Echec\n";
}
echo "Ends french videos Paris modes synchronisation : ".date('Y-m-d H:i:s')."\n";

//SYNCHRO EN AFP
echo "Begins english videos synchronisation : ".date('Y-m-d H:i:s')."\n";
system('rsync -azv -e ssh rnews@88.190.12.180:/var/www/incomings/afp/videos/'.date('Y').'/'.date('m').'/'.date('d').'/*EN.xml '.dirname(__FILE__).'/../../../backoffice/business/import/in/AFP-VIDEO/en/',$syncEn);
if($syncEn==0){
	echo "\tSynchronisation xml en ... OK\n";
}else{
	echo "\tSynchronisation xml en ... Echec\n";
}
echo "Ends english videos synchronisation : ".date('Y-m-d H:i:s')."\n";
//SYNCHRO EN PARIS MODES
echo "Begins english videos Paris modes synchronisation : ".date('Y-m-d H:i:s')."\n";
system('rsync -azv -e ssh rnews@88.190.12.180:/var/www/incomings/afp/videos/'.date('Y').'/'.date('m').'/'.(date('d')-1).'/PMV*EN.xml '.dirname(__FILE__).'/../../../backoffice/business/import/in/AFP-VIDEO/en/',$syncEn);
if($syncEn==0){
        echo "\tSynchronisation xml paris modes en ... OK\n";
}else{
        echo "\tSynchronisation xml paris modes en ... Echec\n";
}
echo "Ends english videos Paris modes synchronisation : ".date('Y-m-d H:i:s')."\n";

echo 'Begins photos synchronisation : '.date('Y-m-d H:i:s').'\n';
system('rsync -azv -e ssh rnews@88.190.12.180:/var/www/incomings/afp/videos/'.date('Y').'/'.date('m').'/'.date('d').'/jpg/ '.dirname(__FILE__).'/../../../backoffice/business/import/in/AFP-VIDEO/photos/',$syncPhotos);
if($syncPhotos==0){
	echo '\tSynchronisation photos ... OK\n';
}else{
	echo '\tSynchronisation photos ... Echec\n';
}
echo 'Ends photos synchronisation : '.date('Y-m-d H:i:s').'\n';

?>
