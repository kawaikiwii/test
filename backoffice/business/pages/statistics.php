<?php

	// Include header
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderAssetBar(_MENU_SYSTEM_REPORTING, _MENU_SYSTEM_REPORTING_STATISTICS);
    echo '<br />';
    wcmModule('business/reports/statistics');
    echo '<br />';
    include(WCM_DIR . '/pages/includes/footer.php');