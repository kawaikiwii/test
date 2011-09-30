<?php 
/*
 * Project:     WCM
 * File:        login.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

$config  = wcmConfig::getInstance();

// Retrieve url of back-office site
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

?>
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php echoH8($project->title.' :: '._SIGN_IN); ?></title>
        <meta http-equiv="Content-Type" content="text/html;" />
        <meta name="Generator" content="Nstein (2008).  All rights reserved." />
        <link rel="stylesheet" type="text/css" href="<?php echo $config['wcm.backOffice.url']?>skins/default/css/common.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $config['wcm.backOffice.url']?>skins/default/css/header.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $config['wcm.backOffice.url']?>skins/default/css/login.css" />
        <script type="text/javascript" src="<?php echo $config['wcm.backOffice.url']?>includes/js/prototype.js">
        </script>
        <script type="text/javascript">
            function checkProtocol(protocol){
                switch (protocol) {
                    case 'ldap':
                        document.getElementById('usernamePassword').style.display = 'block';
                        document.getElementById('ldap').style.display = 'block';
                        document.getElementById('openid').style.display = 'none';
                        break;
                    case 'openid':
                        document.getElementById('openid').style.display = 'block';
                        document.getElementById('ldap').style.display = 'none';
                        document.getElementById('usernamePassword').style.display = 'none';
                        break;
                    case 'wcm':
                    default:
                        document.getElementById('usernamePassword').style.display = 'block';
                        document.getElementById('ldap').style.display = 'none';
                        document.getElementById('openid').style.display = 'none';
                        break;
                }
            }
        </script>
    </head>
    <body onload="checkProtocol('<?php echo $config['wcm.default.authentication']; ?>'); document.getElementById('username').focus();">
        <div id="wrapper">
            <div id="header" class="wcm">
                <div id="banner" class="wcm">
                    <h1><span>Nstein WCM</span></h1>
                    <h2>Powering Online Publishing</h2>
                </div>
                <div id="systemBar">
                </div>
            </div>
            <div id="login-box">
                <?php 
                echo '<div id="signon">';
                if (wcmMVC_Action::getMessage()) {
                    $className = wcmMVC_Action::getMessageKind();
                    $className = ($className == WCMLOG_ERROR) ? 'error' : ($className == WCMLOG_WARNING) ? 'warning' : 'info';
                    echo '<ul class="message"><li class="'.$className.'">'.wcmMVC_Action::getMessage().'</li></ul>';
                }
                
                wcmGUI::openForm('loginForm', str_replace('?_wcmAction=logout', '', $_SERVER['REQUEST_URI']), array('action'=>'login'));
                
                wcmGUI::openFieldset(_SIGN_IN);
                
                /**
                 * Login fields.
                 * Depending on the login protocol, those fields will be visible or hidden.
                 * By default the protocol is wcm, else it need to be set into the
                 * xml configuration file (wcm.default.authentication). (ldap, openid, wcm)
                 */
                echo '<div id="usernamePassword">';
                wcmGUI::renderTextField('username', getArrayParameter($_REQUEST, 'username', null), _USERNAME);
                wcmGUI::renderPasswordField('password', null, _PASSWORD);
                echo '</div>';
                echo '<div id="ldap" style="display:none;">';
                wcmGUI::renderTextField('host', getArrayParameter($_REQUEST, 'host', 'nstein.com'), 'Host');
                wcmGUI::renderTextField('port', getArrayParameter($_REQUEST, 'port', '369'), 'Port');
                wcmGUI::renderTextField('basedn', getArrayParameter($_REQUEST, 'basedn', 'dc=nstein,dc=com'), 'Base db');
                echo '</div>';
                echo '<div id="openid" style="display:none;">';
                wcmGUI::renderTextField('openIdUrl', getArrayParameter($_REQUEST, 'openIdUrl', 'myopenid.com'), 'OpenID url');
                echo '</div>';
                
                wcmGUI::renderDropdownField('_wcmLanguage', array('en'=>'English', 'fr'=>'Français'), $language, _LANGUAGE, array('onchange'=>'window.location.href="?_wcmLanguage="+this.value+"&username="+document.getElementById(\'username\').value'));
                
                echo '<div id="submit">';
                
                echo '<input type="hidden" name="uri_referrer" value="'.str_replace('?_wcmAction=logout', '', $_SERVER['REQUEST_URI']).'" />';
                wcmGUI::renderSubmitButton('login', _SIGN_IN, array('class'=>'submit'));
                ?>
                <!--<div style="text-align:left; padding:10px; margin-top:10px; border:1px solid #CCC; width:335px;">
                    <?php 
                    /*if ($language == "fr") {
                        echo "<h2>Aide</h2>";
                        echo "<a href=\"".$config['wcm.webSite.urlRepository']."guide_manager/account_creation_manager_$language.pdf\" target=\"_blank\">Guide d'utilisation <b>Manager</b> (.pdf)</a>";
                        echo "<br><a href=\"".$config['wcm.webSite.urlRepository']."guide_manager/account_creation_supervisor_$language.pdf\" target=\"_blank\">Guide d'utilisation <b>Superviseur</b> (.pdf)</a>";
                    } else if ($language == "en") {
                        echo "<h2>Help</h2>";
                        echo "<a href=\"".$config['wcm.webSite.urlRepository']."guide_manager/account_creation_manager_$language.pdf\" target=\"_blank\"><b>Manager</b> User Guide (.pdf)</a>";
                        echo "<br><a href=\"".$config['wcm.webSite.urlRepository']."guide_manager/account_creation_supervisor_$language.pdf\" target=\"_blank\"><b>Supervisor</b> User Guide (.pdf)<a>";
                    }*/
                    
                    ?>
                </div>-->
                <?php 
                echo '</div>';
                
                wcmGUI::closeFieldset();
                wcmGUI::closeForm();
                
                echo '</div>';
                echo '<div id="credits">';
                wcmGUI::renderUList(array('<h1>RELAX BACK-OFFICE</h1>', '<h3>Le back-office qui loisire</h3>', '[powered by Nstein WCM '.WCM_VERSION.']'
                    //_CLIENT_SUPPORT,
                    //_NSTEIN_WARNING,
                    //_NSTEIN_SUPPORT,
                    //_RELAXNEWS_SUPPORT,
                    //_NSTEIN_SUPPORT_NETWORK,
                    //_COPYRIGHT
                    ));
                echo '</div>';
                ?>
            </div>
        </div>
    </body>
</html>
