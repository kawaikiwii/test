<?php
/**
 * Project:     WCM
 * File:        modules/editorial/work/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    
	require_once(WCM_DIR.'/business/api/toolbox/biz.relax.toolbox.php');
	$config = wcmConfig::getInstance();
	$bizobject = wcmMVC_Action::getContext();
	$session = wcmSession::getInstance();
	$currentSite = $session->getSite();

	echo '<div class="zone">';

    wcmGUI::openCollapsablePane(_PUBLICATION_GENERATION);
    wcmGUI::openFieldset( _LIFETIME);
	
	if ($bizobject->publicationDate != NULL)
	{
		$dateInit = $bizobject->publicationDate;
	}
	else
	{
		$mktime = mktime(date("H")+1, date("i"), date("s"), date("n"), date("j"), date("Y"));
		$dateInit = date('Y-m-d H:i:s', $mktime);
	}
 
	wcmGUI::renderDateField('publicationDate', $dateInit, _BIZ_PUBLICATIONDATE, 'datetime');
    wcmGUI::renderDateField('embargoDate', $bizobject->embargoDate, _BIZ_EMBARGODATE, 'datetime',  array('class' => 'type-datetime'));
    wcmGUI::renderDateField('expirationDate', $bizobject->expirationDate, _BIZ_EXPIRATIONDATE, 'datetime',  array('class' => 'type-datetime'));
     
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();

	wcmGUI::openCollapsablePane(_SOURCE_INFORMATION);

	wcmGUI::openFieldset('');
	
	$extract = array();
	if ($currentSite->code != "bfr" && $currentSite->code != "ben" && $currentSite->code != "fr")
	{
		$liste = new wcmList();
		$liste->refreshByCode("biph");
		$extract[] = $liste->id;
	}
	
	if ($currentSite->code != "fr" && $currentSite->code != "en")
	{
		$liste = new wcmList();
		$liste->refreshByCode("bang");
		$extract[] = $liste->id;
	}
	
	bizobject::getListSourceForGui($bizobject->source,$extract);
	
    wcmGUI::renderTextField('sourceLocation', $bizobject->sourceLocation, _BIZ_LOCATION);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
	
    echo '</div>';
