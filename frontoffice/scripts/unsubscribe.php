<?php 
ini_set('display_errors', 0);
$response = "";
$num_rows = 0;
$trouve = false;
if (isset($_REQUEST['user_mail'])) {
    // Initialize system
    require_once (dirname(__FILE__).'/../inc/wcmInit.php');
    
    $mail = $_REQUEST['user_mail'];
    $db = new wcmDatabase(str_replace(":3306", "", $config['wcm.systemDB.connectionString']));
    $query = "SELECT id FROM wcm_user WHERE email = '".$mail."'";
    $result = mysql_query($query);
    $num_rows = mysql_num_rows($result);
    if ($num_rows > 0) {
        $db = new wcmDatabase(str_replace(":3306", "", $config['wcm.businessDB.connectionString']));
        while ($row = mysql_fetch_row($result)) {
	    	$id = $row[0];
	        $query2 = "SELECT id FROM biz_subscription WHERE sysUserId = ".$id;
	        $result2 = mysql_query($query2);
	        $num_rows2 = mysql_num_rows($result2);
	        if ($num_rows2 > 0) {
	            $inResult = mysql_query("DELETE FROM biz_subscription WHERE sysUserId = ".$id);
	            $rcResult = mysql_query("SELECT ROW_COUNT()");
	            $count = mysql_result($rcResult, 0, 0);
	            if ($count > 0)
	            	$trouve = true;
	            	
	            mysql_free_result($rcResult);
        	}
        }
        if ($trouve)
        {
        	$response .= "Votre demande de d&#233;sabonnement a bien &#233;t&#233; prise en compte, vous allez recevoir un email de confirmation.<br/>Your request to cancel your subscription has been noted, you will shortly receive a confirmation email.";
			require_once (WCM_DIR."/includes/mail/mail.php");
			$myMail = new htmlMimeMail();
			//$myMail = new Mail();
			//$myMail->setHeader('X-Mailer', 'HTML Mime mail class');
			$myMail->setHeader('X-Mailer', 'text');
			$myMail->setHeader('Date', date('D, d M y H:i:s O'));
			$myMail->setFrom('"support@afprelaxnews.com" <support@afprelaxnews.com>');
			$myMail->setSubject('Desabonnement newsletter / Newsletter unsubscription');
			//$mailContent = 'Bonjour / Hello<br/>Votre demande de d&#233;sabonnement a bien &#233;t&#233; prise en compte.<br/><br/>Your request to cancel your subscription has been noted.';
			$mailContent = "\n\nHello,\n\nYour request to cancel your subscription has been noted.";
			$mailContent .= "\n\nBest regards\nThe relaxnews team";
			$mailContent .= "\n\n-------------------------------------------------------------------";
			$mailContent .= "Bonjour,\n\nVotre demande de désabonnement a bien été prise en compte.";
			$mailContent .= "\n\nBien cordialement\nL'équipe relaxnews";
			$myMail->setSMTPParams(SMTPServer, '25', ServerName, SMTPAuth, SMTPUser, SMTPPassword);
			//$myMail->setHtmlCharset('UTF-8');
			$myMail->setTextCharset('UTF-8');
			//@$myMail->setHtml($mailContent);
			@$myMail->setText($mailContent);
			//@$myMail->html      = $mailContent;
			$myMail->buildMessage();
			if (is_array($mail))
				$send_to = explode(',', $mail);
			else
				$send_to = array($mail);
			$success = @$myMail->send($send_to, 'smtp');
			
			if (isset($myMail->errors)) {
				foreach ($myMail->errors as $error)
					$response .= '<br/>Erreur envoi email / Error sending email';
			}
			if (!$success)
				$response .= '<br/>Erreur envoi email / Error sending email';
			unset($myMail);
        }
        else
            $response .= "<font color='#FA6811'><b>Attention</b> : Aucun abonnement &#224; la newsletter n'a &#233;t&#233; trouv&#233; pour cette adresse email. Merci de v&#233;rifier svp.<br/><b>Warning</b> : No subscription to the newsletter has been found for this email address. Please check again.</font>";
    } else {
        $response .= "<font color='#FA6811'><b>Attention</b> : Cette adresse email n'est pas dans notre base de donn&#233;es. Merci de v&#233;rifier svp.<br/><b>Warning</b> : The email address was not in our database. Please check again.</font>";
    }
}
// No browser cache
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Xml output
header("Content-Type: text/html");
echo $response;
?>
