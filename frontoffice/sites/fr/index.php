<?php 
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: text/html;charset=UTF-8');

require_once (dirname(__FILE__).'/conf/config.php');
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');

$CURRENT_SITECODE = (defined(CURRENT_SITECODE)) ? CURRENT_SITECODE : "fr";
$DISABLED_ACCESS = false;
$ANNOUNCE_MAINTENANCE = false;
require_once (dirname(__FILE__).'/../../inc/siteInit.php');

if (isset($session->userId) && $session->userId) {
    $session->ping();
    include (dirname(__FILE__).'/app.php');
    exit();
}

$minifyJsBase = new Minify_Build($_gc['base.js']);
$minifyCssIndex = new Minify_Build($_gc['index.fr.css']);

ob_start("ob_gzhandler");
?>
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo APP_TITLE?> - v3</title>
        <link rel="shortcut icon" href="<?php echo APP_FAVICON?>"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $minifyCssIndex->uri('/min/m.php/index.fr.css')?>" />
        <script type="text/javascript" src="/inc/js/protoculous.js"></script>
		<script type="text/javascript" src="/inc/js/ticker.js"></script>
		<script type="text/javascript" src="/inc/js/functions.js"></script>
    </head>
    <body>
    <div id="wrapper">
	<div id="global">
		<div id="header" >
			<h1 id="logofr"><a href="index.php" title="Relaxnews - Première source mondiale d'infos loisirs">Relaxnews - Première source mondiale d'infos loisirs</a></h1>
			<ul id="switchLanguage">
				<li><a href="/en" title="English version"><img src="/rp/images/login/en.gif" width="16" height="11" alt="English version" /></a></li>
				<li><a href="/fr" title="Version française"><img src="/rp/images/login/fr.gif" width="16" height="11" alt="Version française" /></a></li>
			</ul>
		</div>
		<div id="main">
			<?php require_once (dirname(__FILE__).'/../../inc/js/tickerfr.inc.php'); ?>
			<!-- <div class="bloc" id="bloc3">
				<div class="top"><h2>INFORMATION</h2></div>
				<div class="content center">
					<p><strong class="rouge">Op&eacute;ration de maintenance pr&eacute;vue</strong></p>
					<p><strong>ATTENTION: Une op&eacute;ration de maintenance est pr&eacute;vue sur notre site. Le service sera inaccessible entre 6h et midi (heure de Paris), dans la matin&eacute;e du samedi 27 ao&ucirc;t.</strong></p>
					<p><strong>Nous vous prions d'accepter nos excuses. Pour toute question, veuillez contacter <a href="mailto:techno@relaxnews.com">techno@relaxnews.com</a></strong></p>
				</div>
				<div class="bottom">&nbsp;</div>
			</div>
 -->			<div class="bloc" id="bloc1">
				<div class="top"><h2>NOUVEAU</h2></div>
				<div class="content center">
					<p><strong>Plus de 55 abonnés AFP-Relaxnews dans le monde</strong></p>
					<p><a href="http://www.afprelaxnews.com/rp/images/login/AFPRelaxnews-clients.jpg" target="_blank"><img src="/rp/images/login/AFPRelaxnews-clients.jpg" width="290" height="79" alt="E-G8"/></a></p>
					<p><strong>utilisent nos contenus loisirs et lifestyle</strong></p>
				</div>
				<div class="bottom">&nbsp;</div>
			</div>
			<div class="bloc" id="bloc2">
				<div class="top"><h2>CONNEXION</h2></div>
				<div class="content">
					<?php 
                    if (wcmMVC_Action::getMessage()) {
                        echo '<p class="loginerror"><b class="bgRed">NOTICE</b> &raquo; '.wcmMVC_Action::getMessage().'</p>';
                    }
                    ?>
					<form id="loginForm" name="loginForm" method="post" action="/login.php">	
						<input id="code" name="code" value="fra" type="hidden" />
						<p><label for="login">Identifiant :</label><input name="username" value="" id="username" type="text" class="ari-input"/></p>
						<p><label for="password">Mot de passe :</label><input name="password" value="" id="password" type="password" class="ari-input" />
							<input onclick="loadService();" name="login" type="submit" value="GO" id="submitLogin"/>
						</p>
					</form>
					<p class="center"><strong>Vous n'avez pas de compte ?</strong></p>
					<p class="center"><strong><a href="mailto:contact@afprelaxnews.com?subject=AFP-Relaxnews Je souhaiterais recevoir un accès test de 24h&body=Mes coordonnées">Recevez un accès test de 24h</a></strong></p>
					<!--<p class="center"><strong>Identifiant & mot de passe: <span class="rouge">relaxeg8</span></strong></p>-->
				</div>
				<div class="bottom">&nbsp;</div>
			</div>
			<div class="bloc" id="bloc3">
				<div class="top"><h2>A PROPOS DU SERVICE</h2></div>
				<div class="content">
					<img src="/rp/images/login/visuel-fr.jpg" width="508" height="65" alt="" style="float:right;"/>
					<h3>Une offre éditoriale unique et rich media</h3>
					<p><strong>AFP-Relaxnews est le premier fil d’info dédié <br/>
						à l'actualité des loisirs et du lifestyle.</strong><br/>
						Disponible par abonnement à titre de consultation et de reproduction web/print/mobile/médias sociaux/écrans, il propose e<strong>n anglais et en français</strong> : </p>
					<ul>
						<li><strong>1 600 dépêches</strong> / mois, illustrées par une photo et/ou une vidéo embedded (Youtube...)</li>
						<li><strong>100 événements</strong> à ne manquer dans le monde en permanence</li>
						<li><strong>40 diaporamas</strong> de 5 à 12 photos / mois</li>
						<li><strong>25 vidéos</strong> reportages / mois</li>
					</ul>
					<h3>Les thématiques couvertes</h3>
					<ul>
						<li><strong>Bien-Etre</strong> : Nutrition, Santé & forme, Sport, Beauté & cosmétiques</li>
						<li><strong>Maison</strong> : Brico-jardin, Décoration & design, Environnement, High-tech, Mode, Conso & vie pratique</li>
						<li><strong>Divertissements</strong> : Arts, expos & spectacles, Cinéma, Jeux vidéos, Livres, BD & mangas, Musique, TV & médias, Internet</li>
						<li><strong>Tourisme</strong> : Auto & Deux roues, Gastronomie, Hôtels, Destination, Transports</li>
						<li><strong>Chaînes transversales</strong> : Femmes, Luxe, People, Produits...</li>
						<li><strong>Dossiers spéciaux</strong> : Défilés, Coupe du Monde, Noël...</li>
					</ul>
					<h3>Les clients</h3>
					<p>Le service AFP-Relaxnews compte plus de 55 clients emblématiques dans 25 pays.</p>
					<p>Parmi eux: <strong>Yahoo</strong> sur trois continents, <strong>MSN</strong> au Québec et en Asie du Sud Est, Redcats (La Redoute), <strong>France 24</strong>, le blog <strong>Luxuo</strong>, The Week in India, <strong>The Independent</strong>, South African Broadcasting Corporation, <strong>RTLinfo.be</strong>, <strong>Newscorp</strong> en Australie. </p>
					<p><a href="/rp/images/login/Clients-AFPRelaxnews-Fr.jpg" target="_blank"><strong>Voir la carte des clients AFP-Relaxnews dans le monde</strong></a></p>
				</div>
				<div class="bottom">&nbsp;</div>
			</div>
		</div>
	</div>
	<div id="footer">
		<ul>
			<li>Contacts: <a href="mailto:contact@afprelaxnews.com">Commercial & Marketing</a> - <a href="/managing_editor.php">Rédaction</a> - <a href="mailto:support.tech@afprelaxnews.com">Technique</a></li>
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
