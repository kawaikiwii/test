<?php
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: text/html;charset=UTF-8');

$DISABLED_ACCESS = false;
$ANNOUNCE_MAINTENANCE = false;

require_once (dirname( __FILE__ ).'/inc/wcmInit.php');

$minifyJsBase = new Minify_Build($_gc['base.js']);
$minifyCssIndex = new Minify_Build($_gc['index.css']);

$user_mail = "";
if(isset($_GET["user_mail"]))
	$user_mail = $_GET["user_mail"];

$gif_wait = "<img src='/inc/images/default/afp-relaxnews.gif' />";
if($session->userId) {
	$site = $session->getSite();
	require_once (dirname(__FILE__)."/sites/".$site->code."/conf/lang.php");
}
else
	require_once (dirname(__FILE__)."/sites/en/conf/lang.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>AFP-RELAXNEWS : Newletter unsubscription</title>
		<link rel="shortcut icon" href="/inc/images/default/favicon.ico"/>
		<link rel="stylesheet" type="text/css" href="<?php echo $minifyCssIndex->uri('/min/m.php/index.css')?>" />
		<script type="text/javascript" language="JavaScript">
			var _xmlHttp			= null;

			// retourne un objet xmlHttpRequest.
			function getXMLHTTP(){
				var xhr				= null;
				if(window.XMLHttpRequest)			// Firefox, Opera et autres
					xhr				= new XMLHttpRequest();
				else if(window.ActiveXObject) {	// Internet Explorer
					try {
						xhr			= new ActiveXObject("Msxml2.XMLHTTP");
					} catch (e) {
						try {
							xhr		= new ActiveXObject("Microsoft.XMLHTTP");
						} catch (e1) {
								xhr	= null;
						}
						}
					}
				else {
					alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
				}
				return xhr;
			}
			
			function doAction() {
				document.getElementById('message').style.display = "block";
				user_mail = document.getElementById('user_mail').value;
				if (user_mail != "") {
					document.getElementById('message').innerHTML = "<img src='/inc/images/default/ajax-loader.gif' />";
					if (_xmlHttp && _xmlHttp.readyState != 0) {
						_xmlHttp.abort()
					}
					_xmlHttp = getXMLHTTP();
					if (_xmlHttp) {
						URL = "/scripts/unsubscribe.php?user_mail=" + user_mail;
						_xmlHttp.open("GET", URL, true);
						_xmlHttp.onreadystatechange = function(){
							if (_xmlHttp.readyState == 4 && _xmlHttp.responseText) {
								/*var xmlDoc = _xmlHttp.responseXML.documentElement;
								document.getElementById("message").innerHTML = xmlDoc.getElementsByTagName("response")[0].childNodes[0].nodeValue;*/
								var xmlDoc = _xmlHttp.responseText;
								document.getElementById("message").innerHTML = xmlDoc;
							}
						}
						_xmlHttp.send(null); // envoi de la requête
					}
				} else {
					document.getElementById('message').innerHTML = "<font color='#FA6811'><b>Attention</b> : merci d’indiquer une adresse email valide svp.<br/><b>Warning</b> : Please enter a valid email address.</b></font>";
				}
			}
		</script>
	</head>
	<body onLoad="document.getElementById('user_mail').focus();">
		<div id="window">
			<br/><br/>
			<div id="header">
				<a href="http://www.afprelaxnews.com"><img src="/inc/images/default/afp-relaxnews.gif" alt="AFP-RELAXNEWS" title="AFP-RELAXNEWS"/></a>
				<div class="ari-title"><h1>NEWSLETTER : D&#233;sabonnement / Unsubscription</h1></div>
			</div>
			<br/><br/>
			<div id="informations">
				<br/><br/>
				Pour vous d&#233;sabonner de notre newsletter, merci d’indiquer votre adresse email et de cliquer sur le lien ci-dessous.
				<br/>
				To unsubscribe from our newsletter, please enter your email address below and then click on the following link.
				<br/><br/>
				<form id="form1" method="post" action="subscription.php">
					<input type="hidden" name="todo" id="todo" value="unsuscribe" />
					Votre adresse email / Your email address&nbsp;<input type="text" name="user_mail" id="user_mail" value="<?php echo $user_mail; ?>" style="width:250px" />
					<br/><br/>
					<a href="JavaScript:void(0);" onClick="doAction();">Se d&#233;sabonner / Unsubscribe</a>
				</form>
				<br/><br/>
				<div id="message">&nbsp;</div>
				<br/><br/>
			</div>
			<div id="center">
				<br/><br/>
			</div>
			<div id="footer">
				<fieldset id="contacts">
					<legend><?php echo _CONTACT_US ?></legend>
					<ul>
						<li><?php echo _CONTACT_COMMON ?> : <a href="mailto:contact@afprelaxnews.com" rel="nofollow">contact@afprelaxnews.com</a></li>
						<li><?php echo _CONTACT_EDITORIAL ?> : <a href="mailto:managing.editor@afprelaxnews.com" rel="nofollow">managing.editor@afprelaxnews.com</a></li>
						<li><?php echo _CONTACT_SUPPORT ?> : <a href="mailto:support.tech@afprelaxnews.com" rel="nofollow">support.tech@afprelaxnews.com</a></li>
					</ul>
				</fieldset>
			</div>
		</div>
		<script type="text/javascript" src="<?php echo $minifyJsBase->uri('/min/m.php/base.js')?>"></script>		
	</body>
</html>
