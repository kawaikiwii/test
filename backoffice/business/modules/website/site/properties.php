<?php
/**
 * Project:     WCM
 * File:        modules/website/site/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    $bizobject = wcmMVC_Action::getContext();

    // Load languages
    $languages = $bizobject->getLanguageList();

    // Render form
    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_META_CONTENT);
    wcmGUI::openFieldset(_GENERAL);
    wcmGUI::renderTextField('title', $bizobject->title, _TITLE . '*', array('class' => 'type-req'));
    wcmGUI::renderTextArea('description', $bizobject->description, _BIZ_DESCRIPTION);
    wcmGUI::renderDropdownField('language', $languages, $bizobject->language, _BIZ_LANGUAGE);
    wcmGUI::renderTextField('code', $bizobject->code, _BIZ_CODE);
    wcmGUI::renderTextField('url', $bizobject->url, _BIZ_URL);
    $sitePartnerFeed = new site();
    $sitePartnerFeed->refresh($bizobject->id);
    wcmGUI::renderBooleanField('partnerFeeds', $sitePartnerFeed->partnerFeeds, _BIZ_PARTNER_FEEDS);
    wcmGUI::renderHiddenField('services', $bizobject->services);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    echo '</div>';
