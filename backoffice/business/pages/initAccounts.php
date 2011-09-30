<?php
/*
 * Project:     WCM
 * File:        initAccounts.php
 *
 * @copyright   (c)2010 Nstein Technologies
 * @version     4.x
 */

    // Execute action
    //wcmMVC_Action::execute('business/account', array('class' => 'account'));
    //$bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();

    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    //wcmGUI::renderObjectMenu();
	ini_set("max_execution_time","300");
	//ini_set("memory_limit","256M");
	
	//id ADMIN RELAXNEWS
    //$id = 25395;
    //id ADMIN MARKETING
    //$id = 25099;
    //id ADMIN AFP
    //$id = 25381;
    
	if (isset($id))
	{
	    $temps_debut = microtime(true);
	    
	    $account = new account();
	    $account->refreshByWcmUser($id);
	    $permArray = $account->getArrayPermissions();
	    
		account::setAccountFamilyPerm($id, $permArray, true);
		$temps_fin = microtime(true);
		echo '<br /><br />Temps d\'execution : '.round($temps_fin - $temps_debut, 4);
	}
	//else
	//	echo "<center><h1>Pas de traitement.</h1></center>";
	
	// check empty perm -- root wcmuserId = 1 !
	account::checkAccountsEmptyPerm(1);	
		
    include(WCM_DIR . '/pages/includes/footer.php');
