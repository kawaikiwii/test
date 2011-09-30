<?php

/**
 * Project:     WCM
 * File:        alerte.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

require_once dirname(__FILE__).'/../../../initWebApp.php';

$config = wcmConfig::getInstance();

$id     = getArrayParameter($_REQUEST, "id", 0);
$action = getArrayParameter($_REQUEST, "kind", null);

echo "<div id=\"errorMsg\"></div>";
wcmGUI::renderHiddenField('initAccountId', $id);
echo '<div id="alerte">';
echo "<div class='wait' style='display:inline;'>Loading...</div><br/><br/>";
echo '</div>';
?>