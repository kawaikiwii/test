<?php
/**
 * Project:     WCM
 * File:        business/modules/orange/export.php
 *
 * @copyright   (c)2010 Nstein Technologies
 * @version     4.x
 *
 */

// définition des paramètres de connexion FTP et des répertoire distants

$ftp_server 	= "ftp.orangeportails.net";
$ftp_user 		= "relaxnstar";
$ftp_pass 		= "cooXew7b";
$pathPortrait 	= "/portrait";
$pathEmission 	= "/emissions";
$pathFinale 	= "/finale";
$photoFinalPath = "/opt/nfs/production/repository/client/orange/OTV.NouvelleStar2010";
/*
$ftp_server 	= "ftp.relaxfil.com";
$ftp_user 		= "admin";
$ftp_pass 		= "ie1frn";
$pathPortrait 	= "/relaxfil/outgoing/_TEST_/otv/portrait";
$pathEmission 	= "/relaxfil/outgoing/_TEST_/otv/emissions";
$pathFinale 	= "/relaxfil/outgoing/_TEST_/otv/finale";
$photoFinalPath = "/opt/nfs/production/repository/client/orange/OTV.NouvelleStar2010";*/

$config = wcmConfig::getInstance();

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
    <form name='OTVExportFormSelect' id='OTVExportFormSelect'>
    <fieldset>
    <legend>Export OTV</legend>
    <ul>
    <?php
    $defaultDateDeb = getArrayParameter($_REQUEST, 'dateDeb','');
    $defaultDateFin = getArrayParameter($_REQUEST, 'dateFin','');
    
	wcmGUI::renderDateField('dateDeb', $defaultDateDeb, 'période du');
	wcmGUI::renderDateField('dateFin', $defaultDateFin, 'au');
	
	wcmGUI::renderHiddenField('todo', 'result');
    ?>
    <li><a href="javascript:$('OTVExportFormSelect').submit();" class="action">VALIDER</a></li>
    </ul>
    </form>
    <br><br>
    <form name='OTVExportForm' id='OTVExportForm'>
    <?php
	    if (getArrayParameter($_REQUEST, 'todo') == 'result')
	    {
		    // Construction du resultSet
		    $dateDebut = getArrayParameter($_REQUEST, 'dateDeb','*');
		    $dateFin = getArrayParameter($_REQUEST, 'dateFin','*');
		    $search = wcmBizsearch::getInstance($config['wcm.search.engine']);
		    $uid = 'OTV_search';
		    
		    if (!empty($dateDebut) && !empty($dateFin))
		    {
			    $query = 'classname:(otvPortrait OR otvEmission OR otvFinale) AND modifiedAt:['.$dateDebut.' TO '.$dateFin.']';
			    $total = $search->initSearch($uid, $query, 'modifiedAt DESC');
	
			    if ($total)
			    {
					$resultSet = $search->getDocumentRange(0, $total, $uid, false);		    	
					if (sizeof($resultSet)>0)
					{	
						echo "Tout cocher/décocher <input type=\"checkbox\" onclick=\"checkAll(document.getElementById('OTVExportForm'), 'box', this.checked);\" />";
						echo "<br /><br /><table class='spe'>";
						echo "<tr class='yellow'><td align='center'>selection</td><td>Id</td><td>Class</td><td>Title</td><td>ModifiedAt</td></tr>";
						foreach ($resultSet as $object)
						{
							echo "<tr><td align='center'><input type='checkbox' name='otv_ids[]' value='".$object->getClass()."_".$object->id."' class='box'/></td><td>".$object->id."</td><td>".$object->getClass()."</td><td>".$object->title."</td><td>".$object->modifiedAt."</td></tr>";
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
	    	$selection = getArrayParameter($_REQUEST, 'otv_ids','*');
	    	
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
	    			
	    			// changement de répertoire distant suivant le type d'objet envoyé
	    			$changeDir = false;
		    		switch($data[0])	
		    		{
		    			case "otvPortrait":
		    				if (ftp_chdir($conn_id, $pathPortrait)) $changeDir = true;
							$fileName_I = "portrait";
		    				break;
		    			case "otvEmission":
		    				if (ftp_chdir($conn_id, $pathEmission)) $changeDir = true;
							$fileName_I = "emission";
		    				break;
		    			case "otvFinale":
		    				if (ftp_chdir($conn_id, $pathFinale)) $changeDir = true;
							$fileName_I = "finale";
		    				break;
		    		}

		    		if ($changeDir == true)
		    		{
		    			// création d'un fichier temporaire pour l'upload sur le FTP
		    			$fSetup = tmpfile();
					    fwrite($fSetup, $object->exportXmlStructure());
					    fseek($fSetup,0);
						
						
						
					    if (!ftp_fput($conn_id, $fileName_I."-".$data[1].".xml", $fSetup, FTP_ASCII))
					        echo "<i>Xml de l'objet ".$data[0]." (".$data[1].") non transféré !</i><br />";
					    else
					    { 
					        echo "<b>transfert du fichier ".$fileName_I."-".$data[1].".xml</b><br />";
					        
					        // on teste si des photos sont associées et on les transfère
					        if (isset($object->photo) && !empty($object->photo))
					        {
					        	$photo = str_replace($config['wcm.backOffice.photosPathOTV']."/", "", $object->photo);
					        	$file = $photoFinalPath."/".$photo;
					        	if (file_exists($file))
					        	{
									$fp = fopen($file, 'r');
						        	if (!ftp_fput($conn_id, basename($photo), $fp, FTP_BINARY))
						        		echo "<i>image ".$photo." non transférée !</i><br />";
						        	else
						        		echo "image ".basename($photo)." transférée !<br />";
						        	fclose($fp);
					        	}
					        	else
					        		echo "<i>image ".$file." inexistante !</i><br />";  						        	
					        }
					        
					        // code doublé, pour éviter perte de connexion et rép courant, a optimiser sous forme de fonction quand temps dispo ;-)
					    	if (isset($object->photoLandscape) && !empty($object->photoLandscape))
					        {
					        	$photo = str_replace($config['wcm.backOffice.photosPathOTV']."/", "", $object->photoLandscape);
					        	$file = $photoFinalPath."/".$photo;
					        	if (file_exists($file))
					        	{
									$fp = fopen($file, 'r');
						        	if (!ftp_fput($conn_id, basename($photo), $fp, FTP_BINARY))
						        		echo "<i>image ".$photo." non transférée !</i><br />";
						        	else
						        		echo "image ".basename($photo)." transférée !<br />";
						        	fclose($fp);
					        	}
					        	else
					        		echo "<i>image ".$file." inexistante !</i><br />";  						        	
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
