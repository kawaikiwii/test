<?php
/**
 * Project:     WCM
 * File:        modules/ugc/poll/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    $bizobject = wcmMVC_Action::getContext();
    echo '<div class="zone">';

    $channels = channel::getChannelHierarchy();
    wcmGUI::openCollapsablePane(_GENERAL, true);
    wcmGUI::openFieldset();
    wcmGUI::renderDropdownField('kind', poll::getKindList(), $bizobject->kind, _BIZ_KIND_OF_POLL);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();

    wcmGUI::openCollapsablePane(_PUBLICATION_GENERATION);
    wcmGUI::openFieldset( _LIFETIME);
    wcmGUI::renderDateField('publicationDate', $bizobject->publicationDate, _BIZ_PUBLICATIONDATE, array('class' => 'type-date'));
    wcmGUI::renderDateField('expirationDate', $bizobject->expirationDate, _BIZ_EXPIRATIONDATE, array('class' => 'type-date'));
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();

    wcmGUI::openCollapsablePane(_SOURCE_INFORMATION);
    wcmGUI::openFieldset(_BIZ_SOURCE);
    wcmGUI::renderTextField('source', $bizobject->source, _BIZ_OTHER_SOURCE_NAME);
    wcmGUI::renderTextField('sourceId', $bizobject->sourceId, _BIZ_ID);
    wcmGUI::renderTextField('sourceVersion', $bizobject->sourceVersion, _BIZ_VERSION);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();

   
    echo '</div>';