<?php 
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: text/html;charset=UTF-8');

require_once (dirname(__FILE__).'/conf/config.php');
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');

$CURRENT_SITECODE = (defined(CURRENT_SITECODE)) ? CURRENT_SITECODE : "fra";
$DISABLED_ACCESS = false;
$ANNOUNCE_MAINTENANCE = false;
require_once (dirname(__FILE__).'/../../inc/siteInit.php');

if (isset($session->userId) && $session->userId) {
    $session->ping();
    include (dirname(__FILE__).'/app.php');
    exit();
}

$minifyJsBase = new Minify_Build($_gc['base.js']);
$minifyCssIndex = new Minify_Build($_gc['index.fra.css']);

ob_start("ob_gzhandler");
?>
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo APP_TITLE?></title>
        <link rel="shortcut icon" href="<?php echo APP_FAVICON?>"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $minifyCssIndex->uri('/min/m.php/index.fra.css')?>" />
    </head>
    <body>
        <div id="loading-mask">
            <div id="loading">
                <div class="loading-message">
                    <h4>Chargement...</h4>
                    <div class="loading-indicator">
                        <br/>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            var newRelaxfil;
            
            function viewNew(){
                newRelaxfil = new Ext.Window({
                    id: "newRelaxfil",
                    closeAction: 'destroy',
                    width: 600,
                    height: 300,
                    plain: true,
                    frame: true,
                    autoScroll: true,
                    border: true,
                    bodyBorder: true,
                    hideBorders: true,
                    draggable: true,
                    resizable: true,
                    modal: true,
                    style: "color:#64247C;",
					bodyStyle:"padding:10px;",
                    closable: true
                });
                
                newRelaxfil.show();
                newRelaxfil.load("/new-relaxfil.html");
            }
        </script>
        <div style="position:absolute;width:580px;margin-left:-295px;left:50%;top:20px;">
            <h1 style="text-align:right;padding-bottom:1px;margin:0">Bienvenue sur le Relaxfil, le 1er fil d'info sur les loisirs !</h1>
            <h4 style="text-align:right;padding-top:0px;margin:0;"><a href="http://old.relaxfil.com/relaxfil.asp" style="color:#fff">Accéder au service événements du Relaxfil</a></h4>
        </div>
        <div id="window">
            <?php 
            //BEGIN OF DISABLED_ACCESS
            if (!$DISABLED_ACCESS) {
            ?>
            <fieldset id="loggin">
                <form id="loginForm" name="loginForm" method="post" action="/login.php">
                    <input id="code" name="code" value="fra" type="hidden" />
                    <dl>
                        <dt>
                            <label for="username">
                                <?php echo _LOG_USER?> :
                            </label>
                        </dt>
                        <dd>
                            <input name="username" value="" id="username" type="text" class="ari-input"/>
                        </dd>
                        <dt>
                            <label for="password">
                                <?php echo _LOG_PASS?> :
                            </label>
                        </dt>
                        <dd>
                            <input name="password" value="" id="password" type="password" class="ari-input" />
                        </dd>
                        <dt>
                            &nbsp;
                        </dt>
                        <dd>
                            <input class="submit" onclick="loadService();" name="login" value="<?php echo _LOG_SIGNIN_BUTTON?>" type="submit" class="ari-submit"/>
                        </dd>
                    </dl>
                </form>
            </fieldset>
            <?php 
            //END OF DISABLED_ACCESS
            }
            ?>
            <div id="informations" style="width:700px;margin-left:-180px">
                <?php 
                if (wcmMVC_Action::getMessage()) {
                    echo '<p class="loginerror"><b class="bgRed">NOTICE</b> &raquo; '.wcmMVC_Action::getMessage().'</p>';
                }
                ?>
		<!-- <div>
			<div>
				<p style="color:red;margin-left:195px"><strong>Op&eacute;ration de maintenance pr&eacute;vue</strong></p>
				<p><strong>ATTENTION: Une op&eacute;ration de maintenance est pr&eacute;vue sur notre site. Le service sera inaccessible entre 6h et midi (heure de Paris), dans la matin&eacute;e du samedi 27 ao&ucirc;t.</strong></p>
				<p><strong>Nous vous prions d'accepter nos excuses. Pour toute question, veuillez contacter <a href="mailto:techno@relaxnews.com">techno@relaxnews.com</a></strong></p>
			</div>		
		</div>
 -->		<br /><br /><br /><br /><br />
                <div>
                    <p>Service réservé aux professionnels et disponible sur abonnement.</p>
                </div>
                <div>
                    Pour demander votre accès test : <a href="mailto:marketing@relaxnews.com?Subject=Je%20souhaite%20recevoir%20un%20accès%20test%20au%20relaxfil%20et%20précise%20ma%20demande%20et%20mes%20coordonnées" rel="nofollow" style="color:white;">marketing@relaxnews.com</a> (en précisant votre demande et vos coordonnées)
                </div>
                <div>
                    Pour toute question technique : <a href="mailto:devteam@relaxnews.com" rel="nofollow" style="color:white;">devteam@relaxnews.com</a>
                </div>
                <div>
                    Pour contacter la rédaction : <a href="mailto:redaction@relaxnews.com" rel="nofollow" style="color:white;">redaction@relaxnews.com</a>
                </div>
                <div id="lostpass">
                    <p><a href="/lostpass.php" style="color:white;">Mot de passe oublié ?</a></p>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="http://www.google-analytics.com/ga.js">
        </script>
        <script type="text/javascript" src="<?php echo $minifyJsBase->uri('/min/m.php/base.js')?>">
        </script>
        <script type="text/javascript">
            Ext.get("username").focus();
            function loadService(){
                Ext.get("loading-mask").show();
            }
        </script>
    </body>
</html>
<?php ob_flush()?>
