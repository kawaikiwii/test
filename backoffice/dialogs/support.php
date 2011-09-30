<?php
/**
 * Project:     WCM
 * File:        support.php
 * 
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../initWebApp.php';

include(WCM_DIR . '/pages/includes/header_popup.php');

echo '<div class="description">';
echo '<h2>' . _DIALOG_SUPPORT . '</h2>';
echo '</div>';

echo '<div class="scroll">';
echo '<div id="license">';
echo "<p>"._SUPPORT_DESCRIPTION."</p>";

echo '<ul>';
echo "<li>"._SUPPORT_URL_USER_GUIDE."</li>";
//echo "<li>"._SUPPORT_URL_ADMINISTRATION_GUIDE."</li>";
echo '</ul>';

echo '</div>';
echo '</div>';

echo '<div id="footer"><ul>';
echo '<li></li>';
echo '</ul></div>';

include(WCM_DIR . '/pages/includes/footer.php');
?>