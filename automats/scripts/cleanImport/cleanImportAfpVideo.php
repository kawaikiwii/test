<?php
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');

//deux precautions vallent mieux qu une ...
if(count($argv) == 3 && $argv[1]!='' && $argv[2]!=''){
	$db = new wcmDatabase($config['wcm.businessDB.connectionString']);
	//variables de connexion a la base de donnees
	$USERNAME = 'admin';
	$PASSWORD = 'kzq!2007';
	//connexion a la dedibox
	$ftp_server = "88.190.12.180";
	$ftp_user = "rnews";
	$ftp_pass = "jUJ7hdhrg1";

	//connexion a la dedibox
	$afpftp_server = "88.190.12.180";
	$afpftp_user = "rnews-afp";
	$afpftp_pass = "7amakabR";


	$repository=explode("-",$argv[2]);

	//on cree une nouvelle session
	if (!$session->login($USERNAME, $PASSWORD)) {
		echo "\n";
		echo "Connexion impossible\n";
		echo "\n";
		echo " Fin : ".date("d-m-Y H:i:s")."\n";
		echo "\n";
		exit();
	}

	//creation de l objet video
	$video = new video();
	echo "TRAITEMENT DE LA VIDEO ".$argv[1]."\n";

	//on initialise l'objet video a partir de l import_feed_id contenu dans $argv[1]
	if (!$video->refreshByImportFeedId($argv[1]))
	{
		//Si la video n'existe pas en base de donnees, c'est qu'elle a ete importe et supprime
		//on supprime alors tous les fichiers local et distant sur les serveurs, pas la peine de supprimer en bdd (deja fait)

		echo "\tLa video ".$argv[1]." a ete supprime de la base de donnees ...\n";

		//suppression des fichiers de la video sur le serveur local (DEV,BETA ou PROD):
		//fr
		echo "\tSuppression des fichiers associes locaux ...\n";

		//on verifie si il s'agit d'un fichier francais ou anglais
		if(strpos($argv[1],'_TFR') !== false || strpos($argv[1],'_FR') !== false){
			//Recherche du xml francais
			if(file_exists(dirname(__FILE__)."/../../../backoffice/business/import/in/AFP-VIDEO/fr/".$argv[1].".xml")){
				echo "\tRecherche xml fr ... OK\n";
				//on fait la suppression
				system("rm -rf ".dirname(__FILE__)."/../../../backoffice/business/import/in/AFP-VIDEO/fr/".$argv[1].".xml",$supprXmlFr);
				if($supprXmlFr==0){
					echo "\tSuppression xml fr ... OK\n";
				}else{
					echo "\tSuppression xml fr ... Echec\n";
				}

			}else{
				echo "\tRecherche xml fr ... ECHEC\n";
			}

		}else if(strpos($argv[1],'_TEN') !== false || strpos($argv[1],'_EN') !== false){
			//Recherche du xml anglais
			if(file_exists(dirname(__FILE__)."/../../../backoffice/business/import/in/AFP-VIDEO/en/".$argv[1].".xml")){
				echo "\tRecherche xml en ... OK\n";
				//on fait la suppression
				system("rm -rf ".dirname(__FILE__)."/../../../backoffice/business/import/in/AFP-VIDEO/en/".$argv[1].".xml",$supprXmlEn);
				if($supprXmlEn==0){
					echo "\tSuppression xml en ... OK\n";
				}else{
					echo "\tSuppression xml en ... Echec\n";
				}

			}else{
				echo "\tRecherche xml en ... ECHEC\n";
			}

		}
		//Recherche et suppression de la vignette importee dans AFP-VIDEO/in/photos
		if(file_exists(dirname(__FILE__)."/../../../backoffice/business/import/in/AFP-VIDEO/photos/".$argv[1].".jpg")){
			echo "\tRecherche jpg ... OK\n";
			//on fait la suppression
			system("rm -rf ".dirname(__FILE__)."/../../../backoffice/business/import/in/AFP-VIDEO/photos/".$argv[1].".jpg",$supprJpg);
			if($supprJpg==0){
				echo "\tSuppression jpg ... OK\n";
			}else{
				echo "\tSuppression jpg ... Echec\n";
			}

		}else{
			echo "\tRecherche jpg ... ECHEC\n";
		}

		//RECHERCHE ET SUPPRESSION DES FICHIERS DISTANTS
		$conn_id = ftp_connect($ftp_server) or die("\tConnexion serveur ftp ... ECHEC");
		$conn_afp_id = ftp_connect($ftp_server) or die("\tConnexion serveur ftp (pour AFP) ... ECHEC");
		ftp_pasv($conn_id, false);
		ftp_pasv($conn_afp_id, false);

		echo "\tConnexion serveur FTP ... OK\n";

		//Tentative d'identification
		if (@ftp_login($conn_id, $ftp_user, $ftp_pass) && @ftp_login($conn_afp_id, $afpftp_user, $afpftp_pass)){
			echo "\tAuthentification serveur FTP en tant que rnews et rnews-afp ... OK\n";
			$path=explode("-",$argv[2]);
			//on verifie que l'explode a bien fonctionne
			if($path[0]!='' && $path[1]!='' && $path[2]!=''){

				//on recupere un tableau de fichier present dans le dossier (celui ou se trouvent les xmls)
				$files = ftp_nlist($conn_id, 'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2]);
				if(in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/'.$argv[1].'.xml',$files)){
					echo "\tRecherche xml ... OK\n";
					if (ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/'.$argv[1].'.xml')) {
						echo "\tSuppression xml ... OK\n";
					}else{
						echo "\tSuppression xml ... ECHEC\n";
					}
				}else{
					echo "\tRecherche xml -> incomings/afp/videos/".$path[0]."/".$path[1]."/".$path[2]."/".$argv[1].".xml ... ECHEC\n";
				}
				//Equivalent pour incoming(depot de l'afp)
				//Login en tant que afp-rnews pour avoir les droits de suppression dans incoming
				$files = ftp_nlist($conn_afp_id, '.');
				if(in_array($argv[1].'.xml',$files)){
					echo "\tRecherche xml (depot AFP) ... OK\n";
					if (ftp_delete($conn_afp_id,$argv[1].'.xml')) {
						echo "\tSuppression xml (depot AFP) ... OK\n";
					}else{
						echo "\tSuppression xml (depot AFP) ... ECHEC\n";
					}
				}else{
					echo "\tRecherche xml (depot AFP) ... ECHEC\n";
				}

				//on recherche les autres types de fichiers =>qt-hi (480x360.mp4 et 640x360.mp4)
				$files = ftp_nlist($conn_id, 'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/qt-hi');
				if(in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/qt-hi/'.$argv[1].'.480x360.mp4',$files) && in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/qt-hi/'.$argv[1].'.640x360.mp4',$files)){
					echo "\tRecherche qt-hi(480x360, 640x360) ... OK\n";
					if (ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/qt-hi/'.$argv[1].'.480x360.mp4') && ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/qt-hi/'.$argv[1].'.640x360.mp4')) {
						echo "\tSuppression qt-hi ... OK\n";
					}else{
						echo "\tSuppression qt-hi ... ECHEC\n";
					}
				}else{
					echo "\tRecherche qt-hi(480x360, 640x360)... ECHEC\n";
				}
				//Equivalent pour incoming(depot de l'afp)
				//Login en tant que afp-rnews pour avoir les droits de suppression dans incoming
				$files = ftp_nlist($conn_afp_id, './qt-hi');
				if(in_array('qt-hi/'.$argv[1].'.480x360.mp4',$files) && in_array('qt-hi/'.$argv[1].'.640x360.mp4',$files)){
					echo "\tRecherche qt-hi(480x360, 640x360) (depot AFP) ... OK\n";
					if (ftp_delete($conn_afp_id,'./qt-hi/'.$argv[1].'.480x360.mp4') && ftp_delete($conn_afp_id,'./qt-hi/'.$argv[1].'.640x360.mp4')) {
						echo "\tSuppression ".$argv[1]." qt-hi (depot AFP) ... OK\n";
					}else{
						echo "\tSuppression ".$argv[1]." qt-hi (depot AFP) ... ECHEC\n";
					}
				}else{
					echo "\tRecherche qt-hi(480x360, 640x360) (depot AFP) ... ECHEC\n";
				}

				//on recherche les autres types de fichiers => mpg (1280x720.mp4 et .mpg)
				$files = ftp_nlist($conn_id, 'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mpg');
				if(in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mpg/'.$argv[1].'.1280x720.mp4',$files) && in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mpg/'.$argv[1].'.mpg',$files)){
					echo "\tRecherche mpg(1280x720, mpg) ... OK\n";
					if (ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mpg/'.$argv[1].'.1280x720.mp4') && ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mpg/'.$argv[1].'.mpg')) {
						echo "\tSuppression mpg ... OK\n";
					}else{
						echo "\tSuppression mpg ... ECHEC\n";
					}
				}else{
					echo "\tRecherche mpg(1280x720, mpg)... ECHEC\n";
				}
				//Equivalent pour incoming(depot de l'afp)
				//Login en tant que afp-rnews pour avoir les droits de suppression dans incoming
				$files = ftp_nlist($conn_afp_id, './mpg');
				if(in_array('mpg/'.$argv[1].'.1280x720.mp4',$files) && in_array('mpg/'.$argv[1].'.mpg',$files)){
					echo "\tRecherche mpg(1280x720, mpg) (depot AFP) ... OK\n";
					if (ftp_delete($conn_afp_id,'./mpg/'.$argv[1].'.1280x720.mp4') && ftp_delete($conn_afp_id,'./mpg/'.$argv[1].'.mpg')) {
						echo "\tSuppression mpg (depot AFP) ... OK\n";
					}else{
						echo "\tSuppression mpg (depot AFP) ... ECHEC\n";
					}
				}else{
					echo "\tRecherche mpg(1280x720, mpg) (depot AFP) ... ECHEC\n";
				}

				//on recherche les autres types de fichiers => mp-hi (640x360.wmv et 480x360.wmv)
				$files = ftp_nlist($conn_id, 'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mp-hi');
				if(in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mp-hi/'.$argv[1].'.640x360.wmv',$files) && in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mp-hi/'.$argv[1].'.480x360.wmv',$files)){
					echo "\tRecherche mp-hi(640x360.wmv, 480x360.wmv) ... OK\n";
					if (ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mp-hi/'.$argv[1].'.640x360.wmv') && ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mp-hi/'.$argv[1].'.480x360.wmv')) {
						echo "\tSuppression mp-hi ... OK\n";
					}else{
						echo "\tSuppression mp-hi ... ECHEC\n";
					}
				}else{
					echo "\tRecherche mp-hi(640x360.wmv, 480x360.wmv)... ECHEC\n";
				}
				//Equivalent pour incoming(depot de l'afp)
				//Login en tant que afp-rnews pour avoir les droits de suppression dans incoming
				$files = ftp_nlist($conn_afp_id, './mp-hi');
				if(in_array('mp-hi/'.$argv[1].'.640x360.wmv',$files) && in_array('mp-hi/'.$argv[1].'.480x360.wmv',$files)){
					echo "\tRecherche mp-hi(640x360.wmv, 480x360.wmv) (depot AFP) ... OK\n";
					if (ftp_delete($conn_afp_id,'./mp-hi/'.$argv[1].'.640x360.wmv') && ftp_delete($conn_afp_id,'./mp-hi/'.$argv[1].'.480x360.wmv')) {
						echo "\tSuppression mp-hi (depot AFP) ... OK\n";
					}else{
						echo "\tSuppression mp-hi (depot AFP) ... ECHEC\n";
					}
				}else{
					echo "\tRecherche mp-hi(640x360.wmv, 480x360.wmv) (depot AFP) ... ECHEC\n";
				}

				//on recherche les autres types de fichiers => jpg
				$files = ftp_nlist($conn_id, 'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/jpg');
				if(in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/jpg/'.$argv[1].'.jpg',$files)){
					echo "\tRecherche jpg ... OK\n";
					if (ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/jpg/'.$argv[1].'.jpg')) {
						echo "\tSuppression jpg ... OK\n";
					}else{
						echo "\tSuppression jpg ... OK\n";
					}
				}else{
					echo "\tRecherche jpg ... ECHEC\n";
				}
				//Equivalent pour incoming(depot de l'afp)
				//Login en tant que afp-rnews pour avoir les droits de suppression dans incoming
				$files = ftp_nlist($conn_afp_id, './jpg');
				if(in_array('jpg/'.$argv[1].'.jpg',$files)){
					echo "\tRecherche jpg (depot AFP) ... OK\n";
					if (ftp_delete($conn_afp_id,'./jpg/'.$argv[1].'.jpg')) {
						echo "\tSuppression jpg (depot AFP) ... OK\n";
					}else{
						echo "\tSuppression jpg (depot AFP) ... ECHEC\n";
					}
				}else{
					echo "\tRecherche jpg (depot AFP) ... ECHEC\n";
				}

				//on recherche les autres types de fichiers => iformat
				$files = ftp_nlist($conn_id, 'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/iformat');
				if(in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/iformat/'.$argv[1].'.mp4',$files)){
					echo "\tRecherche iformat ... OK\n";
					if (ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/iformat/'.$argv[1].'.mp4')) {
						echo "\tSuppression iformat ... OK\n";
					}else{
						echo "\tSuppression iformat ... ECHEC\n";
					}
				}else{
					echo "\tRecherche iformat ... ECHEC\n";
				}
				//Equivalent pour incoming(depot de l'afp)
				//Login en tant que afp-rnews pour avoir les droits de suppression dans incoming
				$files = ftp_nlist($conn_afp_id, './iformat');
				if(in_array('iformat/'.$argv[1].'.mp4',$files)){				
					echo "\tRecherche iformat (depot AFP) ... OK\n";
					if (ftp_delete($conn_afp_id,'./iformat/'.$argv[1].'.mp4')) {
						echo "\tSuppression iformat (depot AFP) ... OK\n";
					}else{
						echo "\tSuppression iformat (depot AFP) ... ECHEC\n";
					}
				}else{
					echo "\tRecherche iformat (depot AFP) ... ECHEC\n";
				}

				//on recherche les autres types de fichiers => flv (640x360.f4v et 480x360.f4v)
				$files = ftp_nlist($conn_id, 'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/flv');
				if(in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/flv/'.$argv[1].'.640x360.f4v',$files) && in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/flv/'.$argv[1].'.480x360.f4v',$files)){
					echo "\tRecherche flv ... OK\n";
					if (ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/flv/'.$argv[1].'.640x360.f4v') && ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/flv/'.$argv[1].'.480x360.f4v')) {
						echo "\tSuppression flv ... OK\n";
					}else{
						echo "\tSuppression flv ... ECHEC\n";
					}
				}else{
					echo "\tRecherche flv ... ECHEC\n";
				}
				//Equivalent pour incoming(depot de l'afp)
				//Login en tant que afp-rnews pour avoir les droits de suppression dans incoming
				$files = ftp_nlist($conn_afp_id, './flv');
				if(in_array('flv/'.$argv[1].'.640x360.f4v',$files) && in_array('flv/'.$argv[1].'.480x360.f4v',$files)){
					echo "\tRecherche flv (depot AFP) ... OK\n";
					if (ftp_delete($conn_afp_id,'./flv/'.$argv[1].'.640x360.f4v') && ftp_delete($conn_afp_id,'./flv/'.$argv[1].'.480x360.f4v')) {
						echo "\tSuppression flv (depot AFP) ... OK\n";
					}else{
						echo "\tSuppression flv (depot AFP) ... ECHEC\n";
					}
				}else{
					echo "\tRecherche flv (depot AFP) ... ECHEC\n";
				}

				//on recherche les autres types de fichiers => 3gp
				$files = ftp_nlist($conn_id, 'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/3gp');
				if(in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/3gp/'.$argv[1].'.3g2',$files)){
					echo "\tRecherche 3gp ... OK\n";
					if (ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/3gp/'.$argv[1].'.3g2')) {
						echo "\tSuppression 3gp ... OK\n";
					}else{
						echo "\tSuppression 3gp ... ECHEC\n";
					}
				}else{
					echo "\tRecherche 3gp ... ECHEC\n";
				}
				//Equivalent pour incoming(depot de l'afp)
				//Login en tant que afp-rnews pour avoir les droits de suppression dans incoming
				$files = ftp_nlist($conn_afp_id, './3gp');
				if(in_array('3gp/'.$argv[1].'.3g2',$files)){
					echo "\tRecherche 3gp (depot AFP) ... OK\n";
					if (ftp_delete($conn_afp_id,'./3gp/'.$argv[1].'.3g2')) {
						echo "\tSuppression 3gp (depot AFP) ... OK\n";
					}else{
						echo "\tSuppression 3gp (depot AFP) ... ECHEC\n";
					}
				}else{
					echo "\tRecherche 3gp (depot AFP) ... ECHEC\n";
				}

			}else{
				echo "\tGeneration du chemin pour la suppression distante ... ECHEC\n";
			}


		}else{
			echo "\tAuthentification serveur FTP ... ECHEC\n";
		}

		// Fermeture de la connexion
		ftp_close($conn_id);
		ftp_close($conn_afp_id);


	}else{
		echo "\tLa video ".$video->import_feed_id." est presente dans la base de donnees...\n";
		if((date("Y-m-d H:i:s", mktime(date("H"),date("i"),date("s"),date("n"),date("j")-5,date("Y")))>$video->createdAt) && $video->workflowState=='draft_import') {
			echo "\tLa video ".$video->import_feed_id." n'a pas ete traite depuis plus de 5 jours (".date("Y-m-d H:i:s", mktime(date("H"),date("i"),date("s"),date("n"),date("j")-5,date("Y"))).")...\n";
			//SUPRRESSION EN BDD DE L'OBJET VIDEO	
				$photos = $video->getRelatedsByClassAndKind("photo");
				echo "\tRecherche photos associes ...\n";
				foreach ($photos as $photo)
				{
					$photosToDelete = new photo(null, $photo["relation"]["destinationId"]);
					//on verifie si la photo existe bien dans la bdd
					if($photosToDelete->id){
						//on check si les la photo n'est pas dé associé un autre objet. Si elle est associé un autre objet, on ne la supprime pas
						$bizrelation = new bizrelation();
						$where = "destinationId=".$photosToDelete->id." AND destinationClass='photo'";
						if ($bizrelation->beginEnum($where, "rank")) {
							$result = $bizrelation->enumCount();
							if($result == 1 && $photosToDelete->source == 'AFP-VIDEO'){
								if ($photosToDelete->delete()) {
									echo "Suppression photos ".$photo["relation"]["destinationId"]." BDD ... OK\n";
								}else{
									echo "Suppression photos ".$photo["relation"]["destinationId"]." BDD ... ECHEC\n";
								}
							}else{
								echo "Suppression photos ".$photo["relation"]["destinationId"]." BDD ... ECHEC\n";
							}
							$bizrelation->endEnum();
						}
						unset($bizrelation);
					}
				}

				echo "\tSuppression des fichiers associes locaux ...\n";
				//on verifie si il s'agit d'un fichier francais ou anglais
				if(strpos($video->import_feed_id,'_TFR') !== false || strpos($video->import_feed_id,'_FR') !== false){
					//Recherche du xml francais
					if(file_exists(dirname(__FILE__)."/../../../backoffice/business/import/in/AFP-VIDEO/fr/".$video->import_feed_id.".xml")){
						echo "\tRecherche xml fr ... OK\n";
						//on fait la suppression
						system("rm -rf ".dirname(__FILE__)."/../../../backoffice/business/import/in/AFP-VIDEO/fr/".$video->import_feed_id.".xml",$supprXmlFr);
						$supprXmlFr=0;
						if($supprXmlFr==0){
							echo "\tSuppression xml fr ... OK\n";
						}else{
							echo "\tSuppression xml fr ... Echec\n";
						}

					}else{
						echo "\tRecherche xml fr ... ECHEC\n";
					}

				}else if(strpos($video->import_feed_id,'_TEN') !== false || strpos($video->import_feed_id,'_EN') !== false){
					//Recherche du xml anglais
					if(file_exists(dirname(__FILE__)."/../../../backoffice/business/import/in/AFP-VIDEO/en/".$video->import_feed_id.".xml")){
						echo "\tRecherche xml en ... OK\n";
						//on fait la suppression
						system("rm -rf ".dirname(__FILE__)."/../../../backoffice/business/import/in/AFP-VIDEO/en/".$video->import_feed_id.".xml",$supprXmlEn);
						if($supprXmlEn==0){
							echo "\tSuppression xml en ... OK\n";
						}else{
							echo "\tSuppression xml en ... Echec\n";
						}

					}else{
						echo "\tRecherche xml en ... ECHEC\n";
					}

				}
				//Recherche et suppression de la vignette importee dans AFP-VIDEO/in/photos
				if(file_exists(dirname(__FILE__)."/../../../backoffice/business/import/in/AFP-VIDEO/photos/".$video->import_feed_id.".jpg")){
					echo "\tRecherche jpg ... OK\n";
					//on fait la suppression
					system("rm -rf ".dirname(__FILE__)."/../../../backoffice/business/import/in/AFP-VIDEO/photos/".$video->import_feed_id.".jpg",$supprJpg);
					$supprJpg=0;
					if($supprJpg==0){
						echo "\tSuppression jpg ... OK\n";
					}else{
						echo "\tSuppression jpg ... Echec\n";
					}

				}else{
					echo "\tRecherche jpg ... ECHEC\n";
				}

				//RECHERCHE ET SUPPRESSION DES FICHIERS DISTANTS
				$conn_id = ftp_connect($ftp_server) or die("\tConnexion serveur ftp ... ECHEC");
				$conn_afp_id = ftp_connect($ftp_server) or die("\tConnexion serveur ftp (pour AFP) ... ECHEC");
				ftp_pasv($conn_id, false);
				ftp_pasv($conn_afp_id, false);

				echo "\tConnexion serveur FTP ... OK\n";

				//Tentative d'identification
				if (@ftp_login($conn_id, $ftp_user, $ftp_pass) && @ftp_login($conn_afp_id, $afpftp_user, $afpftp_pass)){
					echo "\tAuthentification serveur FTP en tant que rnews et rnews-afp ... OK\n";
					$path=explode("-",$argv[2]);
					//on verifie que l'explode a bien fonctionne
					if($path[0]!='' && $path[1]!='' && $path[2]!=''){

						//on recupere un tableau de fichier present dans le dossier (celui ou se trouvent les xmls)
						$files = ftp_nlist($conn_id, 'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2]);
						if(in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/'.$video->import_feed_id.'.xml',$files)){
							echo "\tRecherche xml ... OK\n";
							if (ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/'.$video->import_feed_id.'.xml')) {
								echo "\tSuppression xml ... OK\n";
							}else{
								echo "\tSuppression xml ... ECHEC\n";
							}
						}else{
							echo "\tRecherche xml -> incomings/afp/videos/".$path[0]."/".$path[1]."/".$path[2]."/".$video->import_feed_id.".xml ... ECHEC\n";
						}
						//Equivalent pour incoming(depot de l'afp)
						//Login en tant que afp-rnews pour avoir les droits de suppression dans incoming
						$files = ftp_nlist($conn_afp_id, '.');
						if(in_array($video->import_feed_id.'.xml',$files)){
							echo "\tRecherche xml (depot AFP) ... OK\n";
							if (ftp_delete($conn_afp_id,$video->import_feed_id.'.xml')) {
								echo "\tSuppression xml (depot AFP) ... OK\n";
							}else{
								echo "\tSuppression xml (depot AFP) ... ECHEC\n";
							}
						}else{
							echo "\tRecherche xml (depot AFP) ... ECHEC\n";
						}

						//on recherche les autres types de fichiers =>qt-hi (480x360.mp4 et 640x360.mp4)
						$files = ftp_nlist($conn_id, 'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/qt-hi');
						if(in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/qt-hi/'.$video->import_feed_id.'.480x360.mp4',$files) && in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/qt-hi/'.$video->import_feed_id.'.640x360.mp4',$files)){
							echo "\tRecherche qt-hi(480x360, 640x360) ... OK\n";
							if (ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/qt-hi/'.$video->import_feed_id.'.480x360.mp4') && ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/qt-hi/'.$video->import_feed_id.'.640x360.mp4')) {
								echo "\tSuppression qt-hi ... OK\n";
							}else{
								echo "\tSuppression qt-hi ... ECHEC\n";
							}
						}else{
							echo "\tRecherche qt-hi(480x360, 640x360)... ECHEC\n";
						}
						//Equivalent pour incoming(depot de l'afp)
						//Login en tant que afp-rnews pour avoir les droits de suppression dans incoming
						$files = ftp_nlist($conn_afp_id, './qt-hi');
						if(in_array('qt-hi/'.$video->import_feed_id.'.480x360.mp4',$files) && in_array('qt-hi/'.$video->import_feed_id.'.640x360.mp4',$files)){
							echo "\tRecherche qt-hi(480x360, 640x360) (depot AFP) ... OK\n";
							if (ftp_delete($conn_afp_id,'./qt-hi/'.$video->import_feed_id.'.480x360.mp4') && ftp_delete($conn_afp_id,'./qt-hi/'.$video->import_feed_id.'.640x360.mp4')) {
								echo "\tSuppression qt-hi (depot AFP) ... OK\n";
							}else{
								echo "\tSuppression qt-hi (depot AFP) ... ECHEC\n";
							}
						}else{
							echo "\tRecherche qt-hi(480x360, 640x360) (depot AFP) ... ECHEC\n";
						}

						//on recherche les autres types de fichiers => mpg (1280x720.mp4 et .mpg)
						$files = ftp_nlist($conn_id, 'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mpg');
						if(in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mpg/'.$video->import_feed_id.'.1280x720.mp4',$files) && in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mpg/'.$video->import_feed_id.'.mpg',$files)){
							echo "\tRecherche mpg(1280x720, mpg) ... OK\n";
							if (ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mpg/'.$video->import_feed_id.'.1280x720.mp4') && ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mpg/'.$video->import_feed_id.'.mpg')) {
								echo "\tSuppression mpg ... OK\n";
							}else{
								echo "\tSuppression mpg ... ECHEC\n";
							}
						}else{
							echo "\tRecherche mpg(1280x720, mpg)... ECHEC\n";
						}
						//Equivalent pour incoming(depot de l'afp)
						//Login en tant que afp-rnews pour avoir les droits de suppression dans incoming
						$files = ftp_nlist($conn_afp_id, './mpg');
						if(in_array('mpg/'.$video->import_feed_id.'.1280x720.mp4',$files) && in_array('mpg/'.$video->import_feed_id.'.mpg',$files)){
							echo "\tRecherche mpg(1280x720, mpg) (depot AFP) ... OK\n";
							if (ftp_delete($conn_afp_id,'./mpg/'.$video->import_feed_id.'.1280x720.mp4') && ftp_delete($conn_afp_id,'./mpg/'.$video->import_feed_id.'.mpg')) {
								echo "\tSuppression mpg (depot AFP) ... OK\n";
							}else{
								echo "\tSuppression mpg (depot AFP) ... ECHEC\n";
							}
						}else{
							echo "\tRecherche mpg(1280x720, mpg) (depot AFP) ... ECHEC\n";
						}

						//on recherche les autres types de fichiers => mp-hi (640x360.wmv et 480x360.wmv)
						$files = ftp_nlist($conn_id, 'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mp-hi');
						if(in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mp-hi/'.$video->import_feed_id.'.640x360.wmv',$files) && in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mp-hi/'.$video->import_feed_id.'.480x360.wmv',$files)){
							echo "\tRecherche mp-hi(640x360.wmv, 480x360.wmv) ... OK\n";
							if (ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mp-hi/'.$video->import_feed_id.'.640x360.wmv') && ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/mp-hi/'.$video->import_feed_id.'.480x360.wmv')) {
								echo "\tSuppression mp-hi ... OK\n";
							}else{
								echo "\tSuppression mp-hi ... ECHEC\n";
							}
						}else{
							echo "\tRecherche mp-hi(640x360.wmv, 480x360.wmv)... ECHEC\n";
						}
						//Equivalent pour incoming(depot de l'afp)
						//Login en tant que afp-rnews pour avoir les droits de suppression dans incoming
						$files = ftp_nlist($conn_afp_id, './mp-hi');
						if(in_array('mp-hi/'.$video->import_feed_id.'.640x360.wmv',$files) && in_array('mp-hi/'.$video->import_feed_id.'.480x360.wmv',$files)){
							echo "\tRecherche mp-hi(640x360.wmv, 480x360.wmv) (depot AFP) ... OK\n";
							if (ftp_delete($conn_afp_id,'./mp-hi/'.$video->import_feed_id.'.640x360.wmv') && ftp_delete($conn_afp_id,'./mp-hi/'.$video->import_feed_id.'.480x360.wmv')) {
								echo "\tSuppression mp-hi (depot AFP) ... OK\n";
							}else{
								echo "\tSuppression mp-hi (depot AFP) ... ECHEC\n";
							}
						}else{
							echo "\tRecherche mp-hi(640x360.wmv, 480x360.wmv) (depot AFP) ... ECHEC\n";
						}

						//on recherche les autres types de fichiers => jpg
						$files = ftp_nlist($conn_id, 'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/jpg');
						if(in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/jpg/'.$video->import_feed_id.'.jpg',$files)){
							echo "\tRecherche jpg ... OK\n";
							if (ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/jpg/'.$video->import_feed_id.'.jpg')) {
								echo "\tSuppression jpg ... OK\n";
							}else{
								echo "\tSuppression jpg ... OK\n";
							}
						}else{
							echo "\tRecherche jpg ... ECHEC\n";
						}
						//Equivalent pour incoming(depot de l'afp)
						//Login en tant que afp-rnews pour avoir les droits de suppression dans incoming
						$files = ftp_nlist($conn_afp_id, './jpg');
						if(in_array('jpg/'.$video->import_feed_id.'.jpg',$files)){
							echo "\tRecherche jpg (depot AFP) ... OK\n";
							if (ftp_delete($conn_afp_id,'./jpg/'.$video->import_feed_id.'.jpg')) {
								echo "\tSuppression jpg (depot AFP) ... OK\n";
							}else{
								echo "\tSuppression jpg (depot AFP) ... ECHEC\n";
							}
						}else{
							echo "\tRecherche jpg (depot AFP) ... ECHEC\n";
						}

						//on recherche les autres types de fichiers => iformat
						$files = ftp_nlist($conn_id, 'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/iformat');
						if(in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/iformat/'.$video->import_feed_id.'.mp4',$files)){
							echo "\tRecherche iformat ... OK\n";
							if (ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/iformat/'.$video->import_feed_id.'.mp4')) {
								echo "\tSuppression iformat ... OK\n";
							}else{
								echo "\tSuppression iformat ... ECHEC\n";
							}
						}else{
							echo "\tRecherche iformat ... ECHEC\n";
						}
						//Equivalent pour incoming(depot de l'afp)
						//Login en tant que afp-rnews pour avoir les droits de suppression dans incoming
						$files = ftp_nlist($conn_afp_id, './iformat');
						if(in_array('iformat/'.$video->import_feed_id.'.mp4',$files)){
							echo "\tRecherche iformat (depot AFP) ... OK\n";
							if (ftp_delete($conn_afp_id,'./iformat/'.$video->import_feed_id.'.mp4')) {
								echo "\tSuppression iformat (depot AFP) ... OK\n";
							}else{
								echo "\tSuppression iformat (depot AFP) ... ECHEC\n";
							}
						}else{
							echo "\tRecherche iformat (depot AFP) ... ECHEC\n";
						}

						//on recherche les autres types de fichiers => flv (640x360.f4v et 480x360.f4v)
						$files = ftp_nlist($conn_id, 'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/flv');
						if(in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/flv/'.$video->import_feed_id.'.640x360.f4v',$files) && in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/flv/'.$video->import_feed_id.'.480x360.f4v',$files)){
							echo "\tRecherche flv ... OK\n";
							if (ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/flv/'.$video->import_feed_id.'.640x360.f4v') && ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/flv/'.$video->import_feed_id.'.480x360.f4v')) {
								echo "\tSuppression flv ... OK\n";
							}else{
								echo "\tSuppression flv ... ECHEC\n";
							}
						}else{
							echo "\tRecherche flv ... ECHEC\n";
						}
						//Equivalent pour incoming(depot de l'afp)
						//Login en tant que afp-rnews pour avoir les droits de suppression dans incoming
						$files = ftp_nlist($conn_afp_id, './flv');
						if(in_array('flv/'.$video->import_feed_id.'.640x360.f4v',$files) && in_array('flv/'.$video->import_feed_id.'.480x360.f4v',$files)){
							echo "\tRecherche flv (depot AFP) ... OK\n";
							if (ftp_delete($conn_afp_id,'./flv/'.$video->import_feed_id.'.640x360.f4v') && ftp_delete($conn_afp_id,'./flv/'.$video->import_feed_id.'.480x360.f4v')) {
								echo "\tSuppression flv (depot AFP) ... OK\n";
							}else{
								echo "\tSuppression flv (depot AFP) ... ECHEC\n";
							}
						}else{
							echo "\tRecherche flv (depot AFP) ... ECHEC\n";
						}

						//on recherche les autres types de fichiers => 3gp
						$files = ftp_nlist($conn_id, 'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/3gp');
						if(in_array('incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/3gp/'.$video->import_feed_id.'.3g2',$files)){
							echo "\tRecherche 3gp ... OK\n";
							if (ftp_delete($conn_id,'incomings/afp/videos/'.$path[0].'/'.$path[1].'/'.$path[2].'/3gp/'.$video->import_feed_id.'.3g2')) {
								echo "\tSuppression 3gp ... OK\n";
							}else{
								echo "\tSuppression 3gp ... ECHEC\n";
							}
						}else{
							echo "\tRecherche 3gp ... ECHEC\n";
						}
						//Equivalent pour incoming(depot de l'afp)
						//Login en tant que afp-rnews pour avoir les droits de suppression dans incoming
						$files = ftp_nlist($conn_afp_id, './3gp');
						if(in_array('3gp/'.$video->import_feed_id.'.3g2',$files)){
							echo "\tRecherche 3gp (depot AFP) ... OK\n";
							if (ftp_delete($conn_afp_id,'./3gp/'.$video->import_feed_id.'.3g2')) {
								echo "\tSuppression 3gp (depot AFP) ... OK\n";
							}else{
								echo "\tSuppression 3gp (depot AFP) ... ECHEC\n";
							}
						}else{
							echo "\tRecherche 3gp (depot AFP) ... ECHEC\n";
						}

					}else{
						echo "\tGeneration du chemin pour la suppression distante ... ECHEC\n";
					}


				}else{
					echo "\tAuthentification serveur FTP ... ECHEC\n";
				}

				// Fermeture de la connexion
				ftp_close($conn_id);
				ftp_close($conn_afp_id);
			if($video->delete()){
				echo "\tSuppression video BDD ... OK\n";
			}else{
				echo "\tSuppression video BDD ... ECHEC\n";
			}
		}
	}

	//on se de-sessionne
	$session->logout();
}else{
	echo "Erreur d'arguments !\n";
}
?>
