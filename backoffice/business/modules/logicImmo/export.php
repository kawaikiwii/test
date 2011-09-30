<?php
/**
 * Project:     WCM
 * File:        business/modules/logicImmo/export.php
 *
 * @copyright   (c)2010 Nstein Technologies
 * @version     4.x
 *
 */
 
?>
    <div class="genericForm">
    <form name='logicImmoExportForm' id='logicImmoExportForm'>
    <fieldset>
    <legend>Export LogicImmo</legend>
    <ul>
    <?php
	wcmGUI::renderDropdownField('publication', article::getPublications(), '', 'Numero de publication');
	wcmGUI::renderDropdownField('publicationYear', article::getPublicationsYear(), '', 'Annee publication');
	wcmGUI::renderHiddenField('todo', 'export');
    ?>
    <li><a href="javascript:$('logicImmoExportForm').submit();" class="action">EXPORT</a></li>
    </ul>
    <?php
	    if (getArrayParameter($_REQUEST, 'todo') == 'export')
	    {
		    // Construction du resultSet
		    $publication = getArrayParameter($_REQUEST, 'publication','*');
		    $publicationYear = getArrayParameter($_REQUEST, 'publicationYear','*');
		    $config = wcmConfig::getInstance();
		    $search = wcmBizsearch::getInstance($config['wcm.search.engine']);
		    $uid = 'logicImmo_search';
		    $query = 'className:article AND publication:\''.$publication.'\' AND publication_year:'.$publicationYear;
		    
		    //echo $query;
		    $total = $search->initSearch($uid, $query);

		    if ($total)
		    {
			$resultSet = $search->getDocumentRange(0, $total, $uid, false);
		        $exportRule = new exportRule();
		        $exportRule->refresh(5);
		        $parameter["remotePath"] = strtoupper($publication);
		        //print_r($exportRule->distributionChannels);
		        $error = $exportRule->execute($resultSet, $parameter);
			if ($error)
			{
			   	echo '<ul><br/><b style="color:red">'.date("Y-m-d H:i:s").' : L\'export a échoué</b></ul>';
			}
			else
			{
			   	echo '<ul><br/><b style="color:darkblue">'.date("Y-m-d H:i:s").' : Export effectué</b></ul>';
			}
		   }
		   else
		   {
		   	echo '<ul><br/><b style="color:red">'.date("Y-m-d H:i:s").' : Aucun article dans cette édition</b></ul>';
		   }
	    }
    ?>
    </fieldset>
    </form>
</div>
