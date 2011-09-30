<?php
/**
 * Project:     WCM
 * File:        modules/editorial/slideshow/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    /* IMPORTANT !! Utile car on perd les infos si on upload des photos */
	if(isset($_SESSION['wcmActionMain']) && $_SESSION['wcmAction'] != $_SESSION['wcmActionMain'])
    	$_SESSION['wcmAction'] = $_SESSION['wcmActionMain'];
    
    $bizobject = wcmMVC_Action::getContext();
	$session = wcmSession::getInstance();
	$currentSite = $session->getSite();

    echo '<div class="zone">';

    wcmGUI::openCollapsablePane(_PUBLICATION_GENERATION);
    wcmGUI::openFieldset(_LIFETIME);
	
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
    wcmGUI::renderDateField('expirationDate', $bizobject->expirationDate, _BIZ_EXPIRATIONDATE, 'datetime',  array('class' => 'type-datetime'));
    wcmGUI::renderDateField('embargoDate', $bizobject->embargoDate, _BIZ_EMBARGODATE, 'datetime',  array('class' => 'type-datetime'));
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
	
	if ($currentSite->code != "fr" && $currentSite->code != "en" && $currentSite->code != "bgfr" && $currentSite->code != "bgen")
	{
		$liste = new wcmList();
		$liste->refreshByCode("bang");
		$extract[] = $liste->id;
	}
	
	if ($currentSite->code == "bgfr" || $currentSite->code == "bgen")
	{
		$liste = new wcmList();
		$liste->refreshByCode("afp");
		$extract[] = $liste->id;
		$liste->refreshByCode("relaxnews");
		$extract[] = $liste->id;
		$liste->refreshByCode("afp_relaxnews");
		$extract[] = $liste->id;
		$liste->refreshByCode("biph");
		$extract[] = $liste->id;

	}
	if ($currentSite->code == "bmdfr" || $currentSite->code == "bmden")
	{
		$liste = new wcmList();
		$liste->refreshByCode("afp");
		$extract[] = $liste->id;
		$liste->refreshByCode("relaxnews");
		$extract[] = $liste->id;
		$liste->refreshByCode("afp_relaxnews");
		$extract[] = $liste->id;
		$liste->refreshByCode("biph");
		$extract[] = $liste->id;
		$liste->refreshByCode("bang");
		$extract[] = $liste->id;

	}

	bizobject::getListSourceForGui($bizobject->source,$extract);
	
   // wcmGUI::renderTextField('sourceLocation', $bizobject->sourceLocation, _BIZ_LOCATION);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();

   /* wcmGUI::openCollapsablePane(_SOURCE_INFORMATION);
    wcmGUI::openFieldset(_BIZ_SOURCE);
    wcmGUI::renderTextField('source', $bizobject->source, _BIZ_OTHER_SOURCE_NAME);
    wcmGUI::renderTextField('sourceId', $bizobject->sourceId, _BIZ_ID);
    wcmGUI::renderTextField('sourceVersion', $bizobject->sourceVersion, _BIZ_VERSION);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();*/

    /*wcmGUI::openCollapsablePane(_COMMUNITY_PARTICIPATION);
    wcmGUI::openFieldset(_COMMENTING);
    wcmGUI::renderDropdownField('contributionState', getContributionStateList(), $bizobject->contributionState, _BIZ_CONTRIBUTION);
    wcmGUI::renderDropdownField('moderationKind', getModerationKindList(), $bizobject->moderationKind, _BIZ_MODERATION);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();*/

    echo '</div>';
