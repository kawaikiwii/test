<?php
/**
 * Project:     WCM
 * File:        license.php
 * 
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../initWebApp.php';

include(WCM_DIR . '/pages/includes/header_popup.php');

echo '<div class="description">';
echo '<h2>' . _DIALOG_COPYRIGHT . '</h2>';
echo '</div>';

echo '<div class="scroll">';
echo '<div id="license">';
include (WCM_DIR . '/license/license.php');
echo '</div>';
echo '</div>';

echo '<div id="footer"><ul>';
echo '<li> <a href="javascript:alert(\'Printer Sharp 75 Queen on dcmtl02 says: Blue toner is empty.\nContact your administrator.\');"> Print </a> </li>';
echo '</ul></div>';

include(WCM_DIR . '/pages/includes/footer.php');
?>