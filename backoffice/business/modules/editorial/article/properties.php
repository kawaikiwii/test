<?php
/**
 * Project:     WCM
 * File:        modules/editorial/article/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    
    echo '<div class="zone">';
    
    wcmGUI::openCollapsablePane(_PUBLICATION_GENERATION);
    wcmGUI::openFieldset( _LIFETIME);
    wcmGUI::renderDateField('publicationDate', $bizobject->publicationDate, _BIZ_PUBLICATIONDATE);
    wcmGUI::renderDropdownField('publication', $bizobject->getPublications(), $bizobject->publication, 'Publication');
    wcmGUI::renderDropdownField('publicationYear', $bizobject->getPublicationsYear(), $bizobject->publicationYear, 'Annee de Publication');
    wcmGUI::renderDropdownField('kind', article::getKind(), $bizobject->kind, 'Type');
    wcmGUI::renderDropdownField('location', article::getLocations(), $bizobject->location, 'Edition');
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();

    wcmGUI::openCollapsablePane(_SOURCE_INFORMATION);
    wcmGUI::openFieldset(_BIZ_OTHER_SOURCE);
    wcmGUI::renderTextField('source', $bizobject->source, _BIZ_OTHER_SOURCE_NAME);
    wcmGUI::renderTextField('sourceId', $bizobject->sourceId, _BIZ_ID);
    wcmGUI::renderTextField('sourceVersion', $bizobject->sourceVersion, _BIZ_VERSION);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();

    echo '</div>';
