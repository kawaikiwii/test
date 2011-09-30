<?php
/**
 * Project:     WCM
 * File:        business/modules/import/photos/main.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $config = wcmConfig::getInstance();
    // retrieve plugin className
    $plugin = getArrayParameter($params, 'plugin');

    // prepare start button
    $start = '<ul><li><a href="#" onclick="window.open(\'\', \'importLog\', ' .
             '\'resizable=disallow,scrollbars=1,location=0,status=1,toolbar=0,width=800,height=400\');' .
             '$(\''.$plugin.'_importForm\').submit(); return false;">' .
             _START_IMPORT . '</a></li></ul>';
    
    echo '<div class="zone">';

    wcmGUI::openForm($plugin.'_importForm', 'dialogs/import.php', '', array('target'=>'importLog', 'class' => 'mainForm'));
    wcmGUI::renderHiddenField('plugin', $plugin);
    wcmGUI::openCollapsablePane($params['description'], true, $start);

    wcmGUI::openFieldset(_BIZ_IMPORT_FILE_LOCATION);
    wcmGUI::renderTextField($plugin.'_sourceFolder', WCM_DIR . '/' . wcmImportPhotos::DEFAULT_ROOTFOLDER, _BIZ_IMPORT_SOURCE_FOLDER);
    wcmGUI::closeFieldset();

    wcmGUI::closeForm();
    echo '</div>';
