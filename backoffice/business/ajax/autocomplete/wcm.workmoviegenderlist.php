<?php
/**
 * Project:     WCM
 * File:        ajax/autocomplete/wcm.workgenderlist.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize the system
require_once dirname(__FILE__).'/../../../initWebApp.php';
$config    = wcmConfig::getInstance();
$prefix    = getArrayParameter($_REQUEST, "prefix", '');

$LIST_CODE = "work_moviegender";
$ARRAY_LIST = "ArrayACListWMovieGender";

echo '<ul style="padding: 5px 5px;margin: 0;border-bottom: 1px solid #999;border-right: 1px solid #999; width: 100%">';

$ArrayAC = wcmCache::fetch($ARRAY_LIST);

if (empty($ArrayAC))
{
	$list = wcmList::getListLabelsFromParentCode($LIST_CODE);
	wcmCache::store($ARRAY_LIST, $list);
	$ArrayAC = wcmCache::fetch($ARRAY_LIST);
}
 
$prefix = strtolower($prefix);

function generateListe($debut,$liste) 
{
    $MAX_RETURN = 12;
    $i = 0;
    foreach ($liste as $key=>$val) 
    {
        if ($i<$MAX_RETURN && substr(strtolower($val), 0, strlen($debut))==$debut) 
        {
            echo '<li id="'.$key.'" style="display: block; -moz-border-radius: 3px;padding: 0 100px 0 5px;cursor: pointer;margin-top: 2px;">';
			echo getConst($val);
			echo '</li>';
            $i++;
        }
    }
}

generateListe($prefix,$ArrayAC);

echo '</ul>';