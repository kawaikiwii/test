<?php
/**
 * Project:     WCM
 * File:        about.php
 * 
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../initWebApp.php';

include(WCM_DIR . '/pages/includes/header_popup.php');

echo '<div class="description">';
echo '<h2>' . _DIALOG_ABOUT . '</h2>';
echo '</div>';

echo '<div class="scroll">';
echo '<div id="license">';
echo "<h1>Nstein WCM ".WCM_VERSION."</h1>";
echo "<p>"._CLIENT_SUPPORT."</p>";
echo "<p>"._NSTEIN_WARNING."</p>";
echo "<p>"._DIALOG_ABOUT_DEVELOPMENT_TEAM.DEVELOPMENT_TEAM."</p>";
echo "<p>"._NSTEIN_SUPPORT."</p>";
echo "<p>"._NSTEIN_SUPPORT_NETWORK."</p>";
echo "<p>"._COPYRIGHT."</p>";
echo '</div>';
echo '</div>';

echo '<div id="footer"><ul>';
echo '<li> <a href="javascript:alert(\'Printer Sharp 75 Queen on dcmtl02 says: Blue toner is empty.\nContact your administrator.\');"> Print </a> </li>';
echo '</ul></div>';

include(WCM_DIR . '/pages/includes/footer.php');
?>