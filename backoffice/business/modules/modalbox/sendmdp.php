<?php
/**
 * Project:     WCM
 * File:        sendmdp.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

require_once dirname(__FILE__).'/../../../initWebApp.php';

$config = wcmConfig::getInstance();

$id     = getArrayParameter($_REQUEST, "id", 0);
$action = getArrayParameter($_REQUEST, "kind", null);

$currentUserAccount = new account();
$currentUserAccount->refresh($id);

$universe = $currentUserAccount->getPermissionsUniverse();

$wcmUserAccount = new wcmUser();
$wcmUserAccount->refresh($currentUserAccount->wcmUserId);

require_once (WCM_DIR."/includes/mail/mail.php");
$myMail = new htmlMimeMail();
$myMail->setHeader('X-Mailer', 'HTML Mime mail class');
$myMail->setHeader('Date', date('D, d M y H:i:s O'));
$myMail->setFrom('"Relaxnews" <noreply@relaxnews.com>');
$myMail->setSubject('Votre mot de passe / Your password');
$mailContent = "<html><head></head><body>Hello,<br /><br />Please find below your login and password to access our lifestyle and leisure newswire.";
$mailContent .= "<br /><br />Best regards<br />The relaxnews team";
$mailContent .= "<br /><br />-------------------------------------------------------------------";
$mailContent .= "<br /><br />Bonjour,<br /><br />Vous trouverez ci-dessous votre identifiant et votre mot de passe pour accéder à notre fil d’info loisirs et lifestyle.";
$mailContent .= "<br /><br />Bien cordialement<br />L'équipe relaxnews<br /><br /><br />";
if(in_array(4, $universe) || in_array(5, $universe))
	$mailContent .= "<br /><a href='www.afprelaxnews.com'>www.afprelaxnews.com</a>";
if(in_array(6, $universe))
	$mailContent .= "<br /><a href='www.relaxfil.com'>www.relaxfil.com</a>";
$mailContent .= "<br />Login : ".$wcmUserAccount->login."<br />Password / Mot de passe : ".base64_decode($wcmUserAccount->token)."</body></html>";
$myMail->setSMTPParams(SMTPServer, '25', ServerName, SMTPAuth, SMTPUser, SMTPPassword);
$myMail->setHtmlCharset('UTF-8');
$myMail->setHTML($mailContent);
@$myMail->buildMessage();
$success = $myMail->send(array($wcmUserAccount->email), 'smtp');

echo '<div id="sendmdp">';
if(!$success)
	echo _PASSWORD_NOT_SENT;
else
	echo _PASSWORD_SENT;
echo '</div>';
?>