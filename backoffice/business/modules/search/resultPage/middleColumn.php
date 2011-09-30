<?php
/**
 * Project:     WCM
 * File:        business/modules/search/middleColumn.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

$context = $params[0];          // from wcmModule() call

// Render the search form
wcmModule('business/search/form', array($context));

// Render the search result
wcmModule('business/search/result', array($context));
?>