<?php


/**
 * Project:     WCM
 * File:        testWS.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 * Test WCM web-service
 */

// Initialize system
require_once dirname(__FILE__).'/initWebApp.php';

// Execute special action
$action = $session->getCurrentAction();


switch($action)
{
    case 'login':
        // clear application cache
        // @todo: remove that line for final implementation
        // @info: cache is cleared for development/debugging purpose
        wcmCache::clear();
        
        // execute only if the user submit the login form
        if (isset($_REQUEST['login']))
        {
            // Perform login
            $protocol_extras = array();
            $username = $password = '';
            foreach ($_REQUEST as $key => $value)
            {
                if ($key == 'username' || $key == 'password')
                    eval("\$".$key." = getArrayParameter(\$_REQUEST, '".$key."', null);");
                else
                    eval("\$protocol_extras['".$key."'] = getArrayParameter(\$_REQUEST, '".$key."', null);");
            }

            $protocol = isset($config['wcm.default.authentication']) ? $config['wcm.default.authentication'] : 'wcm';
            $auth = new wcmAuthenticate($session);
            
	        if (isset($_GET["_wcmLanguage"])) {
				$language = $_GET["_wcmLanguage"];
				@require WCM_DIR."/languages/".$language.".php";
			}
			else
			{
				$language = $session->getLanguage();
				
				if (empty($language))
				{
					$language = $config['wcm.default.language'];
					@require WCM_DIR."/languages/".$language.".php";
				}
			}
            
            $auth->login($protocol, $username, $password, $protocol_extras);
		

            if ($session->userId)
            {
                // Check permission on default site
                if (!$session->isAllowed($session->getSite(), wcmPermission::P_READ))
                {
                    $sites = bizobject::getBizobjects('site');
                    foreach ($sites as $site)
                    {
                        // Find first allowed website
                        if ($session->isAllowed($site, wcmPermission::P_READ))
                        {
                            $session->setSite($site);
                        }
                    }

                    if ($session->getSiteId() == 0)
                    {

                        $session->logout();
                        wcmMVC_Action::setError(_NO_SITE_ALLOWED);
                    }
                }           

				if (isset($_POST['uri_referrer']) && $_SERVER['REQUEST_URI'] != '')
				{
					header("Location: ".$_POST['uri_referrer']);
				}
            }
        }
        break;

    case 'logout':
        // Close session
        if ($session != null)
        {
            $session->logout();
            wcmMVC_Action::setMessage(_LOGGED_OUT);
        }
        break;

    default:
        break;
}

//$session->setLanguage($session->getSite()->language);
$mySite = $session->getSite();
if ($mySite)
	$session->setLanguage($mySite->getLanguage());
else
	$session->setLanguage($session->getLanguage());

// Load page for current action
$action = $session->getCurrentAction();
//$action = $_GET['_wcmAction'];


//echo "\n\n\n".$action."\n\n\n";



$session->ping(); // Ping session to keep it active

	

if (substr($action, 0, 9) == 'business/')
{
    $page = WCM_DIR . '/business/pages/' . substr($action, 9) . '.php';
}
else
{
    //$page = WCM_DIR . '/pages/' . $action . '.php?_wcmAction=' . $_REQUEST['_wcmAction'];
    $page = WCM_DIR . '/pages/' . $action . '.php';
}

if (!file_exists($page))
{
    // Invalid page => redirect to error page
    wcmMVC_Action::setWarning(_INVALID_ACTION . ' : ' . $action);
    $page = WCM_DIR . '/pages/home.php';
}


//echo "_REQUEST=" . $_REQUEST['_wcmAction'];	//_REQUEST=business/account
//echo "action=" . $action;	//action=login
//echo "page=" . $page;	//page=/var/www/dev.bo.afprelax.net/pages/login.php

require_once($page);
