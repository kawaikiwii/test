<?php
/**
 * Project:     WCM
 * File:        modules/editorial/shared/contributions.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    $bizobject = wcmMVC_Action::getContext();
	echo '<div class="zone">';
 
    $session = wcmSession::getInstance(); 
 	$config = wcmConfig::getInstance();
 	
 	$params = array();
 	
 	//default is all the posts
 	$params["where"] = "referentClass='".get_class($bizobject)."' AND referentId='".$bizobject->id."'";
 	$params["from"] = 0;
 	$params["limit"] = 25;
 	$params["state"] = 'all';
 	$params["fields"] = array("title", "nickname", "createdAt", "workflowState");
 	$params["fieldtitles"] = array(_DASHBOARD_MODULE_HEADER_TITLE, _DASHBOARD_MODULE_HEADER_BYLINE, _DASHBOARD_MODULE_HEADER_MODIFICATION_DATE, _WORKFLOW_STATE);
 	
 	$params["source"] = "contribution";
 	$params["orderby"] = "createdAt ASC";
 	
 	wcmGUI::openCollapsablePane(_MENU_MODERATE_USER_COMMENTS);
 	
 	
 	$parameters['params'] = $params;
 	$generator = new wcmTemplateGenerator();
    echo '<div id="results" class="tabular-presentation">';
 	echo $generator->executeTemplate('dashboard/db.contributiontab.tpl', $parameters);
 	echo '</div>';
    
    wcmGUI::closeCollapsablePane();
	
    echo '</div>';
?>
    
    
  
 	
 	