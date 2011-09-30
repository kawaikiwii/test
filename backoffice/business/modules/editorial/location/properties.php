<?php
/**
 * Project:     WCM
 * File:        modules/editorial/event/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();

    echo '<div class="zone">';

    wcmGUI::openCollapsablePane(_PUBLICATION_GENERATION);
    wcmGUI::openFieldset( _LIFETIME);

    /*
     * Old version
     * wcmGUI::renderDateField('publicationDate', $bizobject->publicationDate, _BIZ_PUBLICATIONDATE, array('class' => 'type-date'));
    */

    if ($bizobject->publicationDate != NULL)
	{
		$dateInit = $bizobject->publicationDate;
	}
	else
	{
		$mktime = mktime(date("H")+1, date("i"), date("s"), date("n"), date("j"), date("Y"));
		$dateInit = date('Y-m-d H:i:s', $mktime);
	}
	
    wcmGUI::renderDateField('publicationDate', $dateInit, _BIZ_PUBLICATIONDATE, 'datetime');
    wcmGUI::renderDateField('expirationDate', $bizobject->expirationDate, _BIZ_EXPIRATIONDATE,  'datetime');

    // New field "Embargo date"
    wcmGUI::renderDateField('embargoDate', $bizobject->embargoDate, _BIZ_EMBARGODATE,  'datetime');

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
