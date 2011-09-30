<?php
/**
 * Project:     WCM
 * File:        business/modules/pagesjaunes/export.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */

// définition des paramètres de connexion FTP et des répertoire distants

// test
$ftp_server 	= "ftp.relaxfil.com";
$ftp_user 		= "admin";
$ftp_pass 		= "ie1frn";
$path			= "/relaxfil/outgoing/_TEST_/pagesjaunes/ds/";
/*
// prod
$ftp_server 	= "10.23.65.11";
$ftp_user 		= "relaxweb2";
$ftp_pass 		= "kzq!2007";
$path			= "/production/feeds/viamichelin_c7adb7f5c11343824e018d4fc7041fe0/";
*/
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
    <form name='PAGESJAUNESExportFormSelect' id='PAGESJAUNESExportFormSelect'>
    <fieldset>
    <legend>Export Pagesjaunes</legend>
    <ul>
    <?php
    wcmGUI::renderHiddenField('todo', 'export');
    ?>
    <li>Cliquez sur le bouton pour exporter le fichier XML</li> 
	<li><a href="javascript:$('PAGESJAUNESExportFormSelect').submit();" class="action" style="float:left;">Exporter</a></li>
    </ul>
    <!-- ajout spécifique pour passer outre l'étape de sélection par date -->
    <input type='hidden' name='dossierPJ_ids[]' value='dossierPJ_1' class='box'/>
    </form>
    <br><br>
    <form name='PAGESJAUNESExportForm' id='PAGESJAUNESExportForm'>
    <?php
      
	    if (getArrayParameter($_REQUEST, 'todo') == 'export')
	    {
	    	echo "<div style='margin:10px 10px 10px 10px;'>";
	    	$selection = getArrayParameter($_REQUEST, 'dossierPJ_ids','*');

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

	    			$fileName_I = "PAGESJAUNES";
					
	    			if (ftp_chdir($conn_id, $path))
		    		{
		    			// création d'un fichier temporaire pour l'upload sur le FTP
		    			$fSetup = tmpfile();
					    fwrite($fSetup, $object->exportXmlStructure());
					    fseek($fSetup,0);

					    if (!ftp_fput($conn_id, "relaxevents_ds_".date('YmdHi').".xml", $fSetup, FTP_ASCII))
					        echo "<i>Xml de l'objet ".$data[0]." (".$data[1].") non transféré !</i><br />";
					    else
					        echo "<b>transfert du fichier relaxevents_ds_".date('YmdHi').".xml</b><br />";

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
