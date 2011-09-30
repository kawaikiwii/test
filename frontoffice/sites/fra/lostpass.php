<?php 
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: text/html;charset=UTF-8');

$DISABLED_ACCESS = false;
$ANNOUNCE_MAINTENANCE = false;

require_once (dirname(__FILE__).'/conf/config.php');
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');

$minifyJsBase = new Minify_Build($_gc['base.js']);
$minifyCssIndex = new Minify_Build($_gc['index.css']);

$gif_wait = "<img src='/inc/images/default/relaxnews.gif' />";
$success = "";

if (isset($_POST['user_mail']) && isset($_POST['user_login'])) {

    $newpass = date("YmdUc");
    $newpass = md5($newpass);
    $newpass = substr($newpass, 0, 8);
    
    $db = new wcmDatabase($config['wcm.systemDB.connectionString']);
    $query = "UPDATE wcm_user SET password = '".md5($newpass)."' WHERE email = '".$_POST['user_mail']."' AND login = '".$_POST['user_login']."'";
    $db->executeQuery($query);
    
    $message = "Bonjour,\n\n";
    $message .= "vous avez demandé une récupération de mot de passe.\n\n";
    $message .= "ATTENTION : si vous n'êtes pas a l'origine de cette demande, veuillez nous consulter : support.marketing@relaxfil.com \n\n";
    $message .= "Voici votre nouveau mot de passe pour l'identifiant demandé : \n\n";
    $message .= " " . $newpass."\n\n";
    $message .= "Pour vous connecter : ".SITE_URL."\n\n";
    $message .= "Cordialement,\nl'Equipe Relaxnews";
    
    $subject = "RELAXNEWS: Votre nouveau mot de passe";
    
    require_once (dirname(__FILE__).'/../../wcm/includes/mail/mail.php');
    
    $mailer = new htmlMimeMail();
    $mailer->setSMTPParams(SMTPServer, 25, ServerName, SMTPAuth, SMTPUser, SMTPPassword);
    $mailer->setFrom('RELAXNEWS <noreply@relaxnews.net>');
    $mailer->charset = "UTF-8";
    $mailer->setSubject($subject);
    $mailer->setText(utf8_decode($message));
    $mailer->buildMessage();
    
    if ($mailer->send(array($_POST['user_mail']), 'smtp')) {
        $success = 'Un nouveau mot de passe vous a été envoyé. Veuillez consulter votre boite mail.';
    } else {
        $success = 'Error while sending mail';
    }
    
}

?>
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>RELAXNEWS : Demande de mot de passe</title>
        <link rel="shortcut icon" href="/inc/images/default/favicon.ico"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $minifyCssIndex->uri('/min/m.php/index.css')?>" />
    </head>
    <body onLoad="document.getElementById('user_mail').focus();">
        <div id="window">
            <br/>
            <br/>
            <div id="header">
                <a href="http://www.relaxfil.com"><img src="/rp/images/default/relaxnews.png" alt="RELAXNEWS" title="RELAXNEWS"/></a>
                <div class="ari-title">
                    <h2>RELAXNEWS : Merci de saisir votre identifiant ou votre adresse e-mail.</h2>
					<h3>Votre mot de passe vous sera envoyé par e-mail.</h3>
                </div>
            </div>
            <br/>
            <br/>
            <div id="informations">
                <form id="form1" method="post" action="lostpass.php">
                    <input type="hidden" name="todo" id="todo" value="unsuscribe" />
                    <br/>
                    Votre adresse email&nbsp;
                    <br/><br/>
                    <input type="text" name="user_mail" id="user_mail" style="width:250px"/>
                    <br/>
                    <br/>
                    et votre identifiant&nbsp;<br/>
                    <br/>
                    <input type="text" name="user_login" id="user_login" style="width:250px"/>
                    <br/>
                    <br/>
                    <input type="submit" value="Recevoir mon mot de passe."/>
                </form>
                <br/>
<?php echo $success?>&nbsp;
                <br/>
            </div>
            <div id="center">
                <br/>
                <br/>
            </div>
            <div id="footer">
                <fieldset id="contacts">
                    <legend>
                        Contactez-nous
                    </legend>
                    <ul>
                        <li>
                            Marketing/Commercial : <a href="mailto:marketing@relaxnews.com" rel="nofollow">marketing@relaxnews.com</a>
                        </li>
                        <li>
                            Technique : <a href="mailto:devteam@relaxnews.com" rel="nofollow">devteam@relaxnews.com</a>
                        </li>
                        <li>
                            Rédaction : <a href="mailto:redactionfil@relaxnews.com" rel="nofollow">redactionfil@relaxnews.com</a>
                        </li>
                    </ul>
                </fieldset>
            </div>
        </div>
        <script type="text/javascript" src="<?php echo $minifyJsBase->uri('/min/m.php/base.js')?>">
        </script>
    </body>
</html>
