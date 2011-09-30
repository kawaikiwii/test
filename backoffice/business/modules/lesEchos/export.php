<?php
/**
 * Project:     WCM
 * File:        business/modules/lesEchos/export.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */

// définition des paramètres de connexion FTP et des répertoire distants
$config = wcmConfig::getInstance();

$ftp_server 	= "ftp.relaxfil.com";
$ftp_user 		= "admin";
$ftp_pass 		= "ie1frn";
$path			= "/relaxfil/outgoing/_TEST_/lesechos/";
//$path			= "/relaxfil/outgoing/_TEST_/lesechos-test/";
$photoFinalPath = $config['afprelax.bo']."img/photos/lesechos/";

ini_set('max_execution_time', 300);
?>
	<style>
	table.spe {
		font: 11px/24px Verdana, Arial, Helvetica, sans-serif;
		border-collapse: collapse;
		width: 100%;
		}

	tr.yellow td {
		border-top: 1px solid #FB7A31;
		border-bottom: 1px solid #FB7A31;
		background: #FFC;
		}

	td.spe {
		border-bottom: 1px solid #CCC;
		padding: 0 0.5em;
		text-align: center;
		}

	td+td {
		border-left: 1px solid #CCC;
		text-align: center;
		}

	</style>
	<script language="JavaScript">
	function checkAll(theForm, cName, status) {
		for (i=0,n=theForm.elements.length;i<n;i++)
		if (theForm.elements[i].className.indexOf(cName) !=-1) {
		theForm.elements[i].checked = status;
		}}
	</script>

    <div class="genericForm">
    <form name='LESECHOSExportFormSelect' id='LESECHOSExportFormSelect'>
    <fieldset>
    <legend>Export LES ECHOS</legend>
    <ul>
    <?php
    $defaultDateDeb = getArrayParameter($_REQUEST, 'dateDeb','');
    $defaultDateFin = getArrayParameter($_REQUEST, 'dateFin','');
	wcmGUI::renderDateField('dateDeb', $defaultDateDeb, 'période du');
	wcmGUI::renderDateField('dateFin', $defaultDateFin, 'au');
	wcmGUI::renderHiddenField('todo', 'result');
    ?>
    <li><a href="javascript:$('LESECHOSExportFormSelect').submit();" class="action">VALIDER</a></li>
    </ul>
    </form>
    <br><br>
    <form name='LESECHOSExportForm' id='LESECHOSExportForm'>
    <?php
	    if (getArrayParameter($_REQUEST, 'todo') == 'result')
	    {
		    // Construction du resultSet
		    $dateDebut = getArrayParameter($_REQUEST, 'dateDeb','*');
		    $dateFin = getArrayParameter($_REQUEST, 'dateFin','*');
		    $search = wcmBizsearch::getInstance($config['wcm.search.engine']);
		    $uid = 'LESECHOS_search';

		    if (!empty($dateDebut) && !empty($dateFin))
		    {
			    $query = 'classname:(echoCity) AND modifiedAt:['.$dateDebut.' TO '.$dateFin.']';
			    $total = $search->initSearch($uid, $query, 'id');

			    if ($total)
			    {
					$resultSet = $search->getDocumentRange(0, $total, $uid, false);
					if (sizeof($resultSet)>0)
					{
						echo "Tout cocher/décocher <input type=\"checkbox\" onclick=\"checkAll(document.getElementById('LESECHOSExportForm'), 'box', this.checked);\" />";
						echo "<br /><br /><table class='spe'>";
						echo "<tr class='yellow'><td align='center'>selection</td><td>Id</td><td>Class</td><td>Title</td><td>ModifiedAt</td></tr>";
						foreach ($resultSet as $object)
						{
							echo "<tr><td align='center'><input type='checkbox' name='lesechos_ids[]' value='".$object->getClass()."_".$object->id."' class='box'/></td><td>".$object->id."</td><td>".$object->getClass()."</td><td>".$object->title."</td><td>".$object->modifiedAt."</td></tr>";
						}
						echo "</table><br />";
						echo "<div align='center'><input type='submit' value='EXPORTER' /></div>";
						wcmGUI::renderHiddenField('todo', 'export');
					}
			   }
			   else
			   		echo '<ul><br/><b style="color:red">'.date("Y-m-d H:i:s").' : Aucun résultat</b></ul>';
		    }
	    }
	    else if (getArrayParameter($_REQUEST, 'todo') == 'export')
	    {
	    	$selection = getArrayParameter($_REQUEST, 'lesechos_ids','*');

	    	if (!empty($selection))
	    	{
	    		// Mise en place de la connexion ftp
				$conn_id = ftp_connect($ftp_server) or die("erreur connexion FTP $ftp_server");
				// Tentative d'identification
				if (ftp_login($conn_id, $ftp_user, $ftp_pass))
				    echo "Connexion FTP réussie : $ftp_server<br>";
				else
				    echo "Connexion impossible en tant que $ftp_user<br>";

	    		foreach($selection as $item)
	    		{
	    			$data = explode("_", $item);
	    			$object = new $data[0];
	    			$object->refresh($data[1]);

	    			$changeDir = false;

	    			$fileName_I = "lesechos";

		    		if (ftp_chdir($conn_id, $path))
		    		{
		    			// création d'un fichier temporaire pour l'upload sur le FTP
		    			$fSetup = tmpfile();
					    fwrite($fSetup, $object->exportXmlStructure());
					    fseek($fSetup,0);

					    // on créé le rep de l'objet et on transfère tout dedans
					    echo "création du répertoire ".$path.$fileName_I.$data[1]."/<br />";

					    if (!@ftp_chdir($conn_id, $path.$fileName_I.$data[1]))
					    {
					    	ftp_mkdir($conn_id, $path.$fileName_I.$data[1]);
					    	ftp_chdir($conn_id, $path.$fileName_I.$data[1]);
					    }

					    if (!ftp_fput($conn_id, $fileName_I."-".$data[1].".xml", $fSetup, FTP_ASCII))
					        echo "<i>Xml de l'objet ".$data[0]." (".$data[1].") non transféré !</i><br />";
					    else
					    {
					        echo "<b>transfert du fichier ".$fileName_I."-".$data[1].".xml</b><br />";

					        $tabphotos = array();
					    	// on teste si des photos sont associées et on les transfère

					        if (isset($object->mapCityFile) && !empty($object->mapCityFile))
					        {
					        	$tabphotos['file'][] = $object->mapCityFile;
					        	$tabphotos['path'][] = "";
					        }
					        if (isset($object->mapTransportFile) && !empty($object->mapTransportFile))
					        {
					        	$tabphotos['file'][] = $object->mapTransportFile;
					        	$tabphotos['path'][] = "";
					        }

							// on récupère les images des aéroports
					        $arrayAirports = $object->getEchoAirport();
						    if (!empty($arrayAirports))
						    {
						    	foreach($arrayAirports as $data)
						        {
						        	if (!empty($data->mapFile))
						        	{
						        		$tabphotos['file'][] = $data->mapFile;
						        		$tabphotos['path'][] = "airports";
						        	}
						        }
						    }

						    // on récupère les images des diaporamas
						    $arraySlideshows = $object->getEchoSlideshow();
						    if (!empty($arraySlideshows))
						    {
								foreach($arraySlideshows as $data)
						        {
						        	if (!empty($data->file))
						        	{
						        		$tabphotos['file'][] = $data->file;
						        		$tabphotos['path'][] = "slideshow";
						        	}
						        }
						    }

					    	// on récupère les images des évènements
						    $arrayEvents = $object->getEchoEvent();
						    if (!empty($arrayEvents))
						    {
						    	foreach($arrayEvents as $data)
						        {
						        	if (!empty($data->linkFile))
						        	{
						        		$tabphotos['file'][] = $data->linkFile;
						        		$tabphotos['path'][] = "events";
						        	}
						        }
						    }

					    	// on récupère les images des gares
					        $arrayStations = $object->getEchoStation();
						    if (!empty($arrayStations))
						    {
						    	foreach($arrayStations as $data)
						        {
						        	if (!empty($data->mapFile))
						        	{
						        		$tabphotos['file'][] = $data->mapFile;
						        		$tabphotos['path'][] = "stations";
						        	}
						        }
						    }
						    
					    	// on récupère les images des salons
						    $arrayShows = $object->getEchoShow();
						    if (!empty($arrayShows))
						    {
						    	foreach($arrayShows as $data)
						        {
						        	if (!empty($data->linkFile))
						        	{
						        		$tabphotos['file'][] = $data->linkFile;
						        		$tabphotos['path'][] = "shows";
						        	}
						        }
						    }
						    
					        if (!empty($tabphotos['file']))
					        {
					        	$i=0;
					        	// création des répertoires
					        	/*
					        	foreach($tabphotos['path'] as $path)
					        	{
					        		if (!empty($path)) @ftp_mkdir($conn_id, $path);
					        	}
					        	*/
					        	//transfert des images
					        	foreach($tabphotos['file'] as $photo)
					        	{
						        	$file = $photoFinalPath."/".$photo;
						        	if (file_exists($file))
						        	{
										$fp = fopen($file, 'r');
										//if (isset($tabphotos['path'][$i]) && !empty($tabphotos['path'][$i])) $addPath = $tabphotos['path'][$i]."/";
										//else  $addPath = "";
										$addPath = "";
							        	if (!ftp_fput($conn_id, $addPath.basename($photo), $fp, FTP_BINARY))
							        		echo "<i>image ".$photo." non transférée !</i><br />";
							        	else
							        		echo "image ".basename($photo)." transférée !<br />";
							        	fclose($fp);
						        	}
						        	else
						        	{
						        		echo "<i>image ".$file." inexistante !</i><br />";
						        	}

						        	$i++;
					        	}
					        }
					    }
					    // supression du fichier temporaire
						fclose($fSetup);
		    		}
		    		else
		    			echo "pb lors du changement de répertoire !<br>";
	    		}
	    		// Fermeture de la connexion FTP
				ftp_close($conn_id);
	    	}
	    }
    ?>
     </form>
    </fieldset>
</div>
