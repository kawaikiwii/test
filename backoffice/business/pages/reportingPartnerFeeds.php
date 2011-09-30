<?php

	// Include header
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderAssetBar(_MENU_SYSTEM_REPORTING, _MENU_SYSTEM_REPORTING_PARTNERFEEDS);
    
    wcmModule('business/reports/reportingPartnerFeeds');
    
    include(WCM_DIR . '/pages/includes/footer.php');