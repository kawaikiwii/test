<?php
/**
 * Project:     WCM
 * File:        import.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderAssetBar(_MENU_IMPORT, _MENU_IMPORT_FROM_FILES);
    
    $xml = simplexml_load_file(WCM_DIR.'/business/import/in/PJ/index.xml');   
     
    foreach ($xml->channel->item as $item)
    {
    	//print_r($item);
    	
    	$dossierPJ = new dossierPJ();
    	$guid = (int) $item->guid;
    	$obj = $dossierPJ->refreshByGUID($guid);
        
    	if (empty($obj->id))
    	{
	    	$dossierPJ->siteId = 18;
	    	$dossierPJ->guid = $guid;
	    	$dossierPJ->title = (string) $item->title;
	    	$dossierPJ->source = (string) $item->source;
	    	$dossierPJ->startDate = (string) $item->startDate;
	    	$dossierPJ->header = (string) $item->header;
	    	$dossierPJ->description = (string) $item->description;
	    	$dossierPJ->search = (string) $item->search;
	    	$dossierPJ->searchType = (string) $item->search->attributes()->type;
	    	
	    	$dossierPJ->photoUrl = (string) $item->enclosure[0]->attributes()->url;
	    	$dossierPJ->photoCredit = (string) $item->enclosure[0]->credits;		
	    	
	    	//print_r($dossierPJ);   	
	    	$dossierPJ->save();
	    	
	    	echo "Dossier ".$dossierPJ->title." importé<br>";
    	}
    	else 
    		echo "Dossier ".$item->title." existant (id:".$obj->id."/ guid:".$obj->guid.")<br>";
    	
    }
    
    /*
   	$transmissionId = (string) $xml->NewsItem->Identification->NewsIdentifier->NewsItemId;
    	
    $dossierPJ->siteId = 18;
	$dossierPJ->title = (string) $xml->NewsItem->NewsComponent->NewsLines->HeadLine;
    $dossierPJ->startDate = "";
    $dossierPJ->header = "";
    $dossierPJ->description = "";
    $dossierPJ->photoUrl = "";
    $dossierPJ->photoCredit = ""; 
    $dossierPJ->search = "";
	$dossierPJ->searchType = ""; 
	     
	$dossierPJ->save();
	     */   
    include(WCM_DIR.'/pages/includes/footer.php');