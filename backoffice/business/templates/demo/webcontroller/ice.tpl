{literal}<?php
/*
 * Project:     WCM
 * File:        ice.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This script simply handle the ICE (in-context editing) mode
 * by providing a basic authentication form.
 *
 * It toggles ICE mode thanks to a session variable ($_SESSION['ice'])
 */

// Initialize api
require_once dirname(__FILE__).'/../init.php';

// Get parameters
$mode = getArrayParameter($_REQUEST, 'mode', 'login');
$mods = smartyModifiers::getInstance();
$siteURL = $mods->url($session->getSite()->getAssocArray(false));

if ($mode == 'logout')
{
    // Go back to cache mode
    eraseDirectory(dirname(__FILE__) . '/cache/article');
    eraseDirectory(dirname(__FILE__) . '/cache/channel');
    
    unset($_SESSION['ice']);
    header('location: ' . $siteURL);
    exit();
}

if ($mode != 'login')
{
    // Enter ice mode
    $_SESSION['ice'] = intval($mode); 
    header('location: ' . $_SERVER['HTTP__REFERER']);
    exit();
}

// check for login attempt
$errorMessage = null;
if (isset($_REQUEST['action']))
{
    if (isset($_REQUEST['username']) && isset($_REQUEST['password']))
    {
        try
        {
            $session = wcmSession::getInstance();
            if ($session->login($_REQUEST['username'], $_REQUEST['password']))
            {
                $_SESSION['ice'] = 1;
                $_SESSION['ice_webServices_token'] = $session->getToken();
            }
            else
            {
                throw new Exception('could not login into ice mode');
            }
            header('location: ' . $siteURL);
        }
        catch (Exception $e)
        {
            $errorMessage = _LOGIN_FAILED;
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title> .:: Nstein ICE : in-context editing ::. </title>
    <link href="<?php echo $config['wcm.webSite.url']?>css/main.css" rel="stylesheet" type="text/css" />
  </head>
<body>
<div id="wrapper">

    <div id="header">
        <img class="banner" src="<?php echo $config['wcm.webSite.url']?>img/nstein_daily.gif" width="217" height="41" alt="" /><br/>
        <p id="last_updated"> <?php echo date('Y-m-d @ H:i');?> </p>
        <ul id="channel_navigation">
            <?php $channelId=0; include(dirname(__FILE__).'/cache/navigation.php'); ?>
            <li> <a name="login" class="active"> Nstein ICE </a> </li>
        </ul>
    </div>
    <div id="login">
        <div id="signon">
            <ul class="message">
                <li<?php if ($errorMessage) echo ' class="error"'?>> <?php echo $errorMessage; ?>&nbsp;</li>
            </ul>
            <form name="login" method="post">
                <input type="hidden" name="action" value="login"/>
                <fieldset>
                    <ul>
                        <li><label><?php echo _USERNAME; ?></label><input type="text" name="username" value="" /></li>
                        <li><label><?php echo _PASSWORD; ?></label><input type="password" name="password" value="" /></li>
                    </ul>
                    <input type="submit" name="login" value="<?php echo _SIGN_IN; ?>" class="submit" />
                </fieldset>
            </form>
        </div>
    </div>
</div>
</body>
</html>
{/literal}
