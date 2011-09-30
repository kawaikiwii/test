<?php
/**
 * Project:     WCM
 * File:        ajax/autocomplete/wcm.organisations.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize the system
require_once dirname(__FILE__).'/../../../initWebApp.php';
$config    = wcmConfig::getInstance();
$prefix    = getArrayParameter($_REQUEST, "prefix", '');

$organisation  = organisation::getOrganisations($prefix);

echo '<ul style="padding: 5px 5px;margin: 0;border-bottom: 1px solid #999;border-right: 1px solid #999; width: 100%">';

for ($i=0;$i<sizeof($organisation);$i++)
{	
    echo '<li id="'.$organisation[$i]['id'].'" style="display: block; -moz-border-radius: 3px;padding: 0 100px 0 5px;cursor: pointer;margin-top: 2px;">';
    echo $organisation[$i]['name'].":".$organisation[$i]['id'];
    echo '</li>';
}
echo '</ul>';
