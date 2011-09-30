<?php
/**
 * Project:     WCM
 * File:        business/modules/pagesjaunes/exportHTML.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */

// définition des paramètres de connexion FTP et des répertoire distants

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
    <li>Cliquez sur le bouton pour exporter le fichier HTML</li> 
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
	    		foreach($selection as $item)
	    		{
	    			$data = explode("_", $item);
	    			$object = new $data[0];
	    			$object->refresh($data[1]);
	    			
	    			$filename = "/opt/nfs/production/feeds/dspj_7a9098b0c01a5aeb95cc92a63807b6f9/index.html";
	    			
	    			if (!$fp = fopen($filename,"w+"))
						echo "Echec de l'ouverture du fichier";
				    else 
				    {
				    	if (fwrite($fp, $object->exportHtmlStructure()) === FALSE) 
        					echo "Impossible d'écrire dans le fichier".$filename;	
        				else
        					echo "Ecriture du fichier ".$filename;				
				    }					 
		    		echo "</div>";
	    		}
	    	}
	    }
    ?>
     </form>
    </fieldset>
</div>
