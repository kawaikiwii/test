<?php
/**
 * Project:     WCM
 * File:        business/modules/viaMichelin/export.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */

// définition des paramètres de connexion FTP et des répertoire distants
/*
// test
$ftp_server 	= "ftp.relaxfil.com";
$ftp_user 		= "admin";
$ftp_pass 		= "ie1frn";
$path			= "/relaxfil/outgoing/_TEST_/viamichelin/";
*/
// prod
$ftp_server 	= "10.23.65.11";
$ftp_user 		= "relaxweb2";
$ftp_pass 		= "kzq!2007";
$path			= "/production/feeds/viamichelin_c7adb7f5c11343824e018d4fc7041fe0/";

$config = wcmConfig::getInstance();
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
    <form name='VIAMICHELINExportFormSelect' id='VIAMICHELINExportFormSelect'>
    <fieldset>
    <legend>Export Via Michelin</legend>
    <ul>
    <?php
    /*
    $defaultDateDeb = getArrayParameter($_REQUEST, 'dateDeb','');
    $defaultDateFin = getArrayParameter($_REQUEST, 'dateFin','');

	wcmGUI::renderDateField('dateDeb', $defaultDateDeb, 'période du');
	wcmGUI::renderDateField('dateFin', $defaultDateFin, 'au');
	*/
	//wcmGUI::renderHiddenField('todo', 'result');
    wcmGUI::renderHiddenField('todo', 'export');
    ?>
    <li>Cliquez sur le bouton pour exporter le fichier XML</li> 
	<li><a href="javascript:$('VIAMICHELINExportFormSelect').submit();" class="action" style="float:left;">Exporter</a></li>
    </ul>
    <!-- ajout spécifique pour passer outre l'étape de sélection par date -->
    <input type='hidden' name='VIAMICHELIN_ids[]' value='viaMichelin_1' class='box'/>
    </form>
    <br><br>
    <form name='VIAMICHELINExportForm' id='VIAMICHELINExportForm'>
    <?php
    /*
	    if (getArrayParameter($_REQUEST, 'todo') == 'result')
	    {
		    // Construction du resultSet
		    //$dateDebut = getArrayParameter($_REQUEST, 'dateDeb','*');
		    //$dateFin = getArrayParameter($_REQUEST, 'dateFin','*');
		    $search = wcmBizsearch::getInstance($config['wcm.search.engine']);
		    $uid = 'VIAMICHELIN_search';
			
		    if (!empty($dateDebut) && !empty($dateFin))
		    {
			    $query = 'classname:(viaMichelin) AND modifiedAt:['.$dateDebut.' TO '.$dateFin.']';
			    $total = $search->initSearch($uid, $query, 'id');

			    if ($total)
			    {
					$resultSet = $search->getDocumentRange(0, $total, $uid, false);
					if (sizeof($resultSet)>0)
					{
						echo "Tout cocher/décocher <input type=\"checkbox\" onclick=\"checkAll(document.getElementById('VIAMICHELINExportForm'), 'box', this.checked);\" />";
						echo "<br /><br /><table class='spe'>";
						echo "<tr class='yellow'><td align='center'>selection</td><td>Id</td><td>libellé</td><td>Date de publication</td><td>Date de modification</td></tr>";
						foreach ($resultSet as $object)
						{
							echo "<tr><td align='center'><input type='checkbox' name='VIAMICHELIN_ids[]' value='".$object->getClass()."_".$object->id."' class='box'/></td><td>".$object->id."</td><td>".$object->title."</td><td>".$object->publication_date."</td><td>".$object->modifiedAt."</td></tr>";
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
	    else */
	    
	    if (getArrayParameter($_REQUEST, 'todo') == 'export')
	    {
	    	echo "<div style='margin:10px 10px 10px 10px;'>";
	    	$selection = getArrayParameter($_REQUEST, 'VIAMICHELIN_ids','*');

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

	    			$fileName_I = "VIAMICHELIN";
					
	    			//$object->exportXmlStructure();
	    			
	    			
		    		if (ftp_chdir($conn_id, $path))
		    		{
		    			// création d'un fichier temporaire pour l'upload sur le FTP
		    			$fSetup = tmpfile();
					    fwrite($fSetup, $object->exportXmlStructure());
					    fseek($fSetup,0);

					    // on créé le rep de l'objet et on transfère tout dedans
					    /*
					    echo "création du répertoire ".$path.$fileName_I.$data[1]."/<br />";

					    if (!@ftp_chdir($conn_id, $path.$fileName_I.$data[1]))
					    {
					    	ftp_mkdir($conn_id, $path.$fileName_I.$data[1]);
					    	ftp_chdir($conn_id, $path.$fileName_I.$data[1]);
					    }
						*/

					    //if (!ftp_fput($conn_id, $fileName_I."-".$data[1].".xml", $fSetup, FTP_ASCII))
					    if (!ftp_fput($conn_id, "index.xml", $fSetup, FTP_ASCII))
					        echo "<i>Xml de l'objet ".$data[0]." (".$data[1].") non transféré !</i><br />";
					    else
					    {
					        echo "<b>transfert du fichier index.xml</b><br />";
					        //echo "<b>transfert du fichier ".$fileName_I."-".$data[1].".xml</b><br />";
					    }
					    // supression du fichier temporaire
						fclose($fSetup);
		    		}
		    		else
		    			echo "pb lors du changement de répertoire !<br>";

		    	echo "</div>";
	    		}
	    		// Fermeture de la connexion FTP
				ftp_close($conn_id);
	    	}
	    }
    ?>
     </form>
    </fieldset>
</div>
