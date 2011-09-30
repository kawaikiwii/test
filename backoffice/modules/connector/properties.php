<?php
/**
 * Project:     WCM
 * File:        modules/connector/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    $sysobject = wcmMVC_Action::getContext();

    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_CONNECTOR);
    wcmGUI::openFieldset(_PROPERTIES);
    wcmGUI::renderTextField('reference', $sysobject->reference, _REFERENCE . ' *', array('class' => 'type-req'));
    wcmGUI::renderTextField('name', $sysobject->name, _NAME . ' *', array('class' => 'type-req'));
    wcmGUI::renderTextField('connectionString', $sysobject->connectionString, _CONNECTION_STRING . ' *', array('class' => 'type-req'));
    wcmGUI::renderTextField('tablePrefix', $sysobject->tablePrefix, _TABLE_PREFIX . ' *', array('class' => 'type-req'));
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    echo '</div>';