<?php 
/**
 * Project:     WCM
 * File:        ajax/autocomplete/wcm.previsionContact.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
*
 */
 
// Initialize the system
require_once dirname(__FILE__).'/../../../initWebApp.php';
$session = wcmSession::getInstance();
$prefix = getArrayParameter($_REQUEST, "prefix", '');
$max = getArrayParameter($_REQUEST, "max", 10);
$mySite = $session->getSite();
$lang = $mySite->language;

echo '<ul style="padding: 5px 5px;margin: 0;border-bottom: 1px solid #999;border-right: 1px solid #999; width: 100%">';

function generateListe($prefix, $max, $lang) {
    $enum = new organisation();
    
    if (!$enum->beginEnum("title LIKE '".$prefix."%' AND workflowState='published'", "title ASC LIMIT 0, $max"))
        return null;
    while ($enum->nextEnum()) {
        echo '<li id="'.$enum->id.'" style="display: block; -moz-border-radius: 3px;padding: 0 100px 0 5px;cursor: pointer;margin-top: 2px;" title="'.$enum->title.'">';
		echo $enum->title.":".$enum->id;
        echo '</li>';
    }
    $enum->endEnum();
	
	unset ($enum);
}

generateListe($prefix, $max, $lang);

echo '</ul>';
