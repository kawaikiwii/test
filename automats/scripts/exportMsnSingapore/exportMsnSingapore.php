<?php
$dir = "/opt/nfs/production/feeds/msnsingapore_da42c243c0037ab8645b50d3a2364976/videos";
echo "Début : ".date("Y-m-d H:i:s")."\n";
//on ouvre le dossier contenant les vidéos 
if ($handle = opendir($dir)) {
//on parcours le dossier
    while (false !== ($file = readdir($handle))) {
	//si différent du dossier courant et parent
        if ($file != "." && $file != "..") {
		
            	//on ouvre le fichier
		$lecture_fichier = fopen($dir."/".$file, "r");
		if ($lecture_fichier) {
			echo "Traitement du fichier ".$file."\n";
			//on initialise vide le contenu du fichier (ce que l'on va recuperer
			$file_content = "";
			//boucle de parcours de fichier
 			while (($buffer = fgets($lecture_fichier)) !== false) {
        			$file_content .= $buffer;
    			}
    			if (!feof($lecture_fichier)) {
        			echo "Erreur: fgets() a échoué\n";
    			}
			//on balance la sauce
			send_xml($file_content);
    			fclose($lecture_fichier);
		}else{
			echo "Erreur d'ouverture de fichier";
		}
		
        }
    }
    closedir($handle);
}
echo "Fin : ".date("Y-m-d H:i:s")."\n";
//fonction REST
function send_xml($xml = ""){
	$curl_post_data = array(
        	"videoXml" => $xml,
	);
	$service_url = "https://catalog.video.msn.com/admin/services/storeVideoandfiles.aspx";
	$curl = curl_init($service_url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, true); 
	curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($curl, CURLOPT_USERPWD, "spajot@relaxnews.com:iZCO62hs");
	if (strpos($service_url, 'https://') === 0) {
        	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); 
        	// On ne vérifie que l'existence de la page
	}

	$curl_response = curl_exec($curl);
	if (!$curl_response) {
       		echo 'Erreur';
        	return FALSE;
	}
	curl_close($curl);

	$xml = new SimpleXMLElement($curl_response);
	echo "Retour du service : ".$xml."\n";
}

?>
