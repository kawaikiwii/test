<?php
/**
 * Project:     WCM
 * File:        modules/shared/multiobjects.php
 *
 * @copyright   (c)2009 Relaxnews
 * @version     4.x
 *
 */
    /* IMPORTANT !! Utile car on perd les infos si on upload des photos */
	if(isset($_SESSION['wcmActionMain']) && $_SESSION['wcmAction'] != $_SESSION['wcmActionMain'])
    	$_SESSION['wcmAction'] = $_SESSION['wcmActionMain'];
    
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    
    $relclassname = "";
    $relclassid = ""; 
       
    echo '<div class="zone">';
    //wcmGUI::openCollapsablePane(_BIZ_FORECAST, true);
   	
    if (isset($_GET['params']) && !empty($_GET['params']))
    	$params = unserialize($_GET['params']);

   	if (isset($params['relclassname']) && isset($params['relclassid']) && !empty($params['relclassname']) && !empty($params['relclassid']))
   	{
   		$relclassname = $params['relclassname'];
    	$relclassid = $params['relclassid']; 
   	}
    
    wcmModule(  'business/relationship/multi',
                 array('kind' => wcmBizrelation::IS_COMPOSED_OF,
                      'destinationClass' => '',
                      'classFilter' =>  $params['classFilter'],
                      'resultSetStyle' => 'grid',
                      'prefix' => '_wcm_rel_'.$bizobject->getClass().'_',
                      'createTab' => false,
                      'searchEngine' => $config['wcm.search.engine'],
		      		  'createModule' => 'business/subForms/uploadPhoto',
                      'createModuleContact' => 'business/subForms/createContact',
                      'createModulePlace' => 'business/subForms/createPlace',
                      'createModulePersonality' => 'business/subForms/createPersonality',
                      'pageSize' => 9,
                 	  'relclassname' => $relclassname,
                 	  'relclassid' => $relclassid,
                 	  'uid' => $bizobject->getClass().'SearchId'
		));

    //wcmGUI::closeCollapsablePane();
    echo '</div>';

