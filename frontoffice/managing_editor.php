<?php
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: text/html;charset=UTF-8');

require_once (dirname(__FILE__).'/inc/wcmInit.php');

$CURRENT_SITECODE 		= getDefaultSiteCode();;
$DISABLED_ACCESS 		= false;
$ANNOUNCE_MAINTENANCE 	= false;

require_once (dirname(__FILE__).'/inc/siteInit.php');

$minifyJsBase = new Minify_Build($_gc['base.js']);
$minifyCssIndex = new Minify_Build($_gc['index.css']);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>RELAXNEWS : Contact Editorial</title>
        <link rel="shortcut icon" href="/inc/images/default/favicon.ico"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $minifyCssIndex->uri('/min/m.php/index.css')?>" />
    </head>
    <body>
        <div id="window">
            <br/>
            <br/>
            <div id="header">
                <a href="http://www.relaxfil.com"><img src="/rp/images/default/relaxnews.png" alt="RELAXNEWS" title="RELAXNEWS"/></a>
                <div class="ari-title">
                    <h2>Editorial contacts / Les contacts de la rédaction</h2>
                </div>
            </div>
            <br/>
            <br/>
            <div id="footer">
            	<span style="font-weight:bold;">Editor in Chief / Rédacteur en chef</span><br />
				Jean-Yves Katelan, <a href="mailto:jykatelan@relaxnews.com">jykatelan@relaxnews.com</a><br /><br />
				 
				<span style="font-weight:bold;">Photo Editor / Responsable photo</span><br />
				Mildred Puissant, <a href="mailto:photo3@relaxnews.com">photo3@relaxnews.com</a><br /><br />
				 
				<span style="font-weight:bold;">Partnerships / Partenariats</span><br />
				Charlotte Wiedemann, <a href="mailto:cwiedeman@relaxnews.com">cwiedeman@relaxnews.com</a><br /><br />
				 
				<span style="font-weight:bold;">Editors / Responsables rubriques</span><br />
				Tourism, transport & motors / Tourisme, transports, auto<br />
				Nick Holmes, <a href="mailto:nholmes@relaxnews.com">nholmes@relaxnews.com</a><br />
				Christina Musacchio, <a href="mailto:cmusacchio@relaxnews.com">cmusacchio@relaxnews.com</a><br /><br />
				
				High-tech, internet, arts<br />
				Lani Marcus, <a href="mailto:lmarcus@relaxnews.com">lmarcus@relaxnews.com</a><br />
				Christina Musacchio, <a href="mailto:cmusacchio@relaxnews.com">cmusacchio@relaxnews.com</a><br /><br />
				 
				Fashion, beauty, design / Mode, beauté, design<br />
				Charlotte Wiedemann, <a href="mailto:cwiedemann@relaxnews.com">cwiedemann@relaxnews.com</a><br /><br />
				 
				Music, books / Musique, livres<br />
				Jennifer Weaver, <a href="mailto:jweaver@relaxnews.com">jweaver@relaxnews.com</a><br />
				Kristen Congedo, <a href="mailto:kcongedo@relaxnews.com">kcongedo@relaxnews.com</a><br /><br />
				 
				Games & movies / Jeux & films<br />
				Chris Pepper, <a href="mailto:cpepper@relaxnews.com">cpepper@relaxnews.com</a><br /><br />
				 
				Food, beverage, nutrition / Gastronomie, nutrition<br />
				Vivian Song, <a href="mailto:vsong@relaxnews.com">vsong@relaxnews.com</a><br /><br />
				 
				Home, household consumption, environment / Vie pratique, maison, environnement<br />
				Tom Bowen, <a href="mailto:tbowen@relaxnews.com">tbowen@relaxnews.com</a><br /><br />
				 
				Health, fitness, sports / Santé, forme, sport<br />
				Jennifer Weaver, <a href="mailto:jweaver@relaxnews.com">jweaver@relaxnews.com</a><br /><br />
				 
				<span style="font-weight:bold;">French translation / Traduction française</span><br />
				Jean-René Etienne, <a href="mailto:jretienne@relaxnews.com">jretienne@relaxnews.com</a><br />
				Jennifer Ignace, <a href="mailto:jignace@relaxnews.com">jignace@relaxnews.com</a><br />
				Thomas Isackson, <a href="mailto:tisackson@relaxnews.com">tisackson@relaxnews.com</a><br /><br />
				 
				<span style="font-weight:bold;">Events agenda / Calendrier événements</span><br />
				Corinne Gavard <a href="mailto:cgavard@relaxnews.com">cgavard@relaxnews.com</a>
            </div>
        </div>
        <script type="text/javascript" src="<?php echo $minifyJsBase->uri('/min/m.php/base.js')?>">
        </script>
    </body>
</html>