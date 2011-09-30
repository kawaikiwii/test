<?php
/**
 * Project:     WCM
 * File:        popup.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../initWebApp.php';

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Display module
$module = getArrayParameter($_REQUEST, "module", null);

    include(WCM_DIR . '/pages/includes/header_popup.php');
    echo "<tr><td colspan='2' height='100%' valign='top' bgcolor='#f0f0f0'>";
    wcmModule('business/'.$module);
    echo "</td></tr>";
    include(WCM_DIR . '/pages/includes/footer.php');
?>
