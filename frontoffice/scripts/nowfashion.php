<?php
require_once (dirname(__FILE__).'/../inc/wcmInit.php');

$externalFolderId = $_REQUEST["externalFolderId"];
$minifyCss = new Minify_Build($_gc["fr.css"]);
$slideshow = new slideshow();
$permissions = $slideshow->getAccountPermissions();
if((array_key_exists(16, $permissions) && $externalFolderId == 467) || (array_key_exists(17, $permissions) && $externalFolderId == 468)) {
?>
<html>
<head><title></title>
</head>
<body>

<script type="text/javascript" src="http://www.nowfashion.com/js/jquery.js"></script>

<script type="text/javascript">
var NF_WIDGET_OTHER_LIB = false; // if you allready use a javascript framework different than jQuery, put true, else put false;
var NF_WIDGET_THUMBNAILS = false; // thumbnails visible (true) or not (false)
var NF_WIDGET_KEYBOARD_NAV = true; // keyboard's arrows navigation enable (true) or not (false)
var NF_WIDGET_AUTOPLAY = true; // slideshow autoplay (true) or not (false)

var NF_WIDGET_TIMER_SPEED = 5; // default slideshow speed in seconds (between 1 - 30)
var NF_WIDGET_DEFAULT_SPEED = 400; // default sliding speed in milliseconds  (between 1 - 1000)
var NF_WIDGET_THUMBNAILS_OPACITY = 0.4; // thumbnails opacity

var NF_WIDGET_PANNEL_OPACITY = 0.8; // left pannel opacity
var NF_WIDGET_PANNEL_INFOS_BG_COLOR = '#000000'; // left pannel background color
var NF_WIDGET_PANNEL_INFOS_TEXT_COLOR = '#EEEEEE'; // left pannel text color
var NF_WIDGET_PANNEL_INFOS_TEXT_COLOR2 = '#AAAAAA'; // left pannel secondary text color

var NF_WIDGET_BARRE_COLOR = '#E5E5E5'; // controle barre background color (hexadecimal)
var NF_WIDGET_TIMER_COLOR = '#BBBBBB'; // slideshow timer barre background color (hexadecimal)

var NF_WIDGET_THUMBNAILS_OPACITY_COLOR = '#FFFFFF'; // color of thumbnails opacity
var NF_WIDGET_BG_COLOR = '#FFFFFF'; // player background color (hexadecimal)
var NF_WIDGET_BORDER_COLOR_DARK = '#C9C9C9'; // border dark color (hexadecimal)
var NF_WIDGET_COLOR_CACHE = '#FFFFFF'; // image cache color (hexadecimal)
var NF_WIDGET_BUTTON_BORDER_COLOR = '#E5E5E5'; // player button border color (hexadecimal)
var NF_WIDGET_BUTTON_BORDER_COLOR_HOVER = '#000000'; // player button hover border color (hexadecimal)
var NF_WIDGET_BUTTON_COLOR = '#444444'; // player button background color (hexadecimal)
var NF_WIDGET_BUTTON_COLOR_HOVER = '#000000'; // player button hover background color (hexadecimal)

var NF_WIDGET_FOCUS_FUNCTION = null // javascript function which will be called when a picture is focus, for exemple if you have a function called myFunction -> var NF_WIDGET_FOCUS_FUNCTION = function(){ myFunction(); }

</script>
<script src="http://www.nowfashion.com/widget.php?id=5" type="text/javascript"></script>
<?php
}
else
{
?>
<html>
<head><title></title>
<link rel="stylesheet" type="text/css" href="<?php echo $minifyCss->uri('/min/m.php/fr.css')?>" />
</head>
<body>
<div class="ext-el-mask"></div>
<div class="ext-el-mask-msg ari-access-denied" id="ext-gen414" style="left: 428px; top: 160px;">
	<div>
<?php
if($externalFolderId == 467) {
?>
		<h1 class="ari-restricted-title">Sorry, your “relax” access doesn’t include this service yet.</h1>
	    <p>To subscribe, please contact us : <a href="mailto:contact@afprelaxnews.com?subject=I%20would%20like%20to%20subscribe%20to%20new%20AFP%20Relaxnews%20services">contact@afprelaxnews.com</a></p>
<?php
}
elseif($externalFolderId == 468) {
?>
		<h1 class="ari-restricted-title">Désolé, votre relax accès n’inclut pas - encore - ce service.</h1>
	    <p>Pour vous y abonner, merci de contacter : <a href="mailto:contact@afprelaxnews.com?subject=Je%20souhaite%20m%E2%80%99abonner%20%C3%A0%20des%20services%20suppl%C3%A9mentaires%20du%20fil%20AFP-Relaxnews">contact@afprelaxnews.com</a></p>
<?php
}
?>
    </div>
</div>
<?php
}
?>
</body>
</html>