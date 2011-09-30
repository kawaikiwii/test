<?php
/**
 * Project:     WCM
 * File:        importDAM.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    // Execute action
    $session->setCurrentAction("importDAM");

    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderAssetBar(_MENU_IMPORT, _MENU_IMPORT_DAM);

    echo '<div id="content" style="margin-left:10px">';
    $tabs = new wcmAjaxTabs('importDAM',true);
    $tabs->addTab(  'dam', _MENU_IMPORT_DAM, false, null,
                    wcmModuleURL('business/import/DAM/main',
                        array('description' => _MENU_IMPORT_DAM,
                            'name'        => 'XML',
                            'plugin'      => 'wcmImportXML'))
                );
    $tabs->render();
    echo '</div>';
 
    include(WCM_DIR.'/pages/includes/footer.php');