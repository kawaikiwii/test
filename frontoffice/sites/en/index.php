<?php 
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: text/html;charset=UTF-8');

require_once (dirname(__FILE__).'/conf/config.php');
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');

$CURRENT_SITECODE = (defined(CURRENT_SITECODE)) ? CURRENT_SITECODE : "en";
$DISABLED_ACCESS = false;
$ANNOUNCE_MAINTENANCE = false;
require_once (dirname(__FILE__).'/../../inc/siteInit.php');

if (isset($session->userId) && $session->userId) {
    $session->ping();
    include (dirname(__FILE__).'/app.php');
    exit();
}

$minifyJsBase = new Minify_Build($_gc['base.js']);
$minifyCssIndex = new Minify_Build($_gc['index.en.css']);

ob_start("ob_gzhandler");
?>
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo APP_TITLE?></title>
        <link rel="shortcut icon" href="<?php echo APP_FAVICON?>"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $minifyCssIndex->uri('/min/m.php/index.en.css')?>" />
        <script type="text/javascript" src="/inc/js/protoculous.js"></script>
		<script type="text/javascript" src="/inc/js/ticker.js"></script>
		<script type="text/javascript" src="/inc/js/functions.js"></script>
    </head>
    <body>
        <div id="wrapper">
	<div id="global">
		<div id="header" >
			<h1 id="logo"><a href="index.php" title="Relaxnews - The world's first leisure newswire">Relaxnews - The world's first leisure newswire</a></h1>
			<ul id="switchLanguage">
				<li><a href="/en" title="English version"><img src="/rp/images/login/en.gif" width="16" height="11" alt="English version" /></a></li>
				<li><a href="/fr" title="Version française"><img src="/rp/images/login/fr.gif" width="16" height="11" alt="Version française" /></a></li>
			</ul>
		</div>
		<div id="main">
			<?php require_once (dirname(__FILE__).'/../../inc/js/tickeren.inc.php'); ?>
			<!-- <div class="bloc" id="bloc3">
				<div class="top"><h2>INFORMATION</h2></div>
				<div class="content center">
					<p><strong class="rouge">Scheduled website maintenance</strong></p>
					<p><strong>PLEASE NOTE: The website will be undergoing maintenance operations. The site will be unavailable between 4 am and 10 am (GMT) on Saturday, August 27.</strong></p>
					<p><strong>We apologize for any inconvenience. If you have any questions, please contact <a href="mailto:techno@relaxnews.com">techno@relaxnews.com</a></strong></p>
				</div>
				<div class="bottom">&nbsp;</div>
			</div>
 -->			<div class="bloc" id="bloc1">
				<div class="top"><h2>NEW</h2></div>
				<div class="content center">
					<p><strong>Over 55 AFP-Relaxnews subscribers worldwide</strong></p>
					<p><a href="http://www.afprelaxnews.com/rp/images/login/AFPRelaxnews-clients.jpg" target="_blank"><img src="/rp/images/login/AFPRelaxnews-clients.jpg" width="290" height="79" alt="E-G8"/></a></p>
					<p><strong>use our lifestyle and leisure contents</strong></p>
				</div>
				<div class="bottom">&nbsp;</div>
			</div>
			<div class="bloc" id="bloc2">
				<div class="top"><h2>SIGN IN</h2></div>
				<div class="content">
					<?php 
                    if (wcmMVC_Action::getMessage()) {
                        echo '<p class="loginerror"><b class="bgRed">NOTICE</b> &raquo; '.wcmMVC_Action::getMessage().'</p>';
                    }
                    ?>
					<form id="loginForm" name="loginForm" method="post" action="/login.php">	
						<input id="code" name="code" value="en" type="hidden" />
						<p><label for="login">Login :</label><input name="username" value="" id="username" type="text" class="ari-input"/></p>
						<p><label for="password">Password :</label><input name="password" value="" id="password" type="password" class="ari-input" />
							<input onclick="loadService();" name="login" type="submit" value="GO" id="submitLogin"/>
						</p>
					</form>
					<p class="center"><strong>Don't have an account ?</strong></p>
					<p class="center"><strong><a href="mailto:contact@afprelaxnews.com?subject=AFP-Relaxnews: I would like to get 24-hour free trial access&body=My details">Get 24-hour free trial access</a></strong></p>
					<!--<p class="center"><strong class="rouge">SPECIAL E-G8: Free access until the 31st of May !</strong></p>
					<p class="center"><strong>Login & password: <span class="rouge">relaxeg8</span></strong></p>-->
				</div>
				<div class="bottom">&nbsp;</div>
			</div>
			<div class="bloc" id="bloc3">
				<div class="top"><h2>ABOUT THE SERVICE</h2></div>
				<div class="content">
					<img src="/rp/images/login/visuel-en.jpg" width="485" height="65" alt="" style="float:right;"/>
					<h3>A "one stop shop" to earn impact, time & money</h3>
					<p><strong>AFP-Relaxnews is the first newswire dedicated to leisure and lifestyle.</strong> Available by subscription for consultation and for web/print/mobile/social media/public screens reproduction, it provides in English and in French: </p>
					<ul>
						<li><strong>1 600 stories / month</strong>, always accompanied by a picture and often an embedded video, on well-being, house & home, entertainment and tourism</li>
						<li><strong>100 must-see events</strong> around the world</li>
						<li><strong>40 dynamic photo slideshows</strong> / month</li>
						<li><strong>25 pre-tracked video reports</strong> / month </li>
					</ul>
					<h3>The newswire coverage</h3>
					<ul>
						<li><strong>Well-Being</strong>: Nutrition, Health/Fitness, Sport, Beauty & Cosmetics</li>
						<li><strong>House & Home</strong>: DIY/Gardening, Interiors & Design, Environment, Technology, Fashion, Household Consumption</li>
						<li><strong>Entertainment</strong>: Art, Shows & Exhibitions, Cinema, Video games, Books, Music, Television & Media, Internet</li>
						<li><strong>Tourism</strong>: Cars & Motorbikes, Gastronomy, Hotels, Destinations, Transport</li>
						<li><strong>Cross thematics</strong>: Women, Luxury, People, Trends, Products</li>
						<li><strong>Hot topics</strong>: Fashion Week, Christmas, World's Cup...</li>
					</ul>
					<h3>References</h3>
					<p>The AFP-Relaxnews service counts more than 55 iconic clients in 25 countries.</p>
					<p>Among these subscribers: <strong>Yahoo</strong> on three continents, <strong>MSN</strong> in Quebec and South East Asia, Redcats, <strong>France 24</strong>, <strong>Luxuo</strong> blog, The Week in India, <strong>The Independent</strong>, South African Broadcasting Corporation, <strong>RTLinfo.be</strong>, <strong>Newscorp</strong> in Australia.</p>
					<p><a href="/rp/images/login/Clients-AFPRelaxnews-En.jpg" target="_blank"><strong>Download the full AFP-Relaxnews clients map</strong></a></p>
				</div>
				<div class="bottom">&nbsp;</div>
			</div>
		</div>
	</div>
	<div id="footer">
		<ul>
			<li>Contacts: <a href="mailto:contact@afprelaxnews.com">Sales & Marketing</a> - <a href="/managing_editor.php">Editorial</a> - <a href="mailto:support.tech@afprelaxnews.com">Technical</a></li>
			<li><a href="http://www.afp.com" target="_blank">Afp.com</a></li>
			<li><a href="http://www.relaxnews.com" target="_blank">Relaxnews.com</a></li>
			<li><a href="http://www.relaxfil.com" target="_blank">Relaxfil.com</a></li>
			<li><a href="http://twitter.com/afprelaxnews" target="_blank">Twitter</a></li>
			<li><a href="http://www.facebook.com/relaxnewsinternational" target="_blank">Facebook</a></li>
			<li class="last"><a href="http://relaxnote.wordpress.com" target="_blank">Blog</a></li>
		</ul>
	</div>
	<script type="text/javascript">tick();</script>
</div>
    </body>
</html>
<?php ob_flush()?>
