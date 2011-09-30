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

echo '<IFRAME src="/business/modules/ugc/account/permissions.php?id='.$id.'" width=680 height=550 scrolling=auto frameborder=0 > </IFRAME>';

