<?php
/**
 * Project:     WCM
 * File:        modules/editorial/newsletter/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    $bizobject = wcmMVC_Action::getContext();

    echo '<div class="zone">';
 

    wcmGUI::openCollapsablePane(_BIZ_NEWLETTERS_TEMPLATES);
    wcmGUI::openFieldset();

    wcmGUI::renderTextField('htmlTemplate', $bizobject->htmlTemplate, _BIZ_HTML_TEMPLATE);
    wcmGUI::renderTextField('textTemplate', $bizobject->textTemplate, _BIZ_TEXT_TEMPLATE);
    
    
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();



    echo '</div>';
