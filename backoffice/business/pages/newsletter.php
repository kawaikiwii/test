<?php
/**
 * Project:     WCM
 * File:        newsletter.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     3.2
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/newsletter', array('class' => 'newsletter'));
    $bizobject = wcmMVC_Action::getContext();

    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderObjectMenu();



    echo '<div id="content">';

    wcmGUI::openObjectForm($bizobject);
    $tabs = new wcmAjaxTabs('newsletter', true);
    $tabs->addTab('t1', _BIZ_OVERVIEW, false, null, wcmModuleURL('business/shared/overview'));
    $tabs->addTab('t2', _PROPERTIES, false, null, wcmModuleURL('business/editorial/newsletter/properties'));
    $tabs->addTab('t3', _CONTENT, true, null, wcmModuleURL('business/editorial/newsletter/content'));
    $tabs->addTab('t4', _BIZ_NEWSLETTER_SUBSCRIBERS, false, null, wcmModuleURL('business/editorial/newsletter/subscribers'));
    $tabs->addTab('t5', _DESIGN, false, null, wcmModuleURL('business/editorial/newsletter/design'));
    $tabs->render($bizobject->id === 0);
    wcmGUI::closeForm();

    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');
