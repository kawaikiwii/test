<?php
/**
 * Project:     WCM
 * File:        import.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderAssetBar(_MENU_IMPORT, _MENU_IMPORT_FROM_FILES);

    echo '<div id="treeview">';
    $tabs = new wcmAjaxTabs('navigation', true);
    $tabs->addTab('history', _HISTORY, true, wcmGUI::renderObjectHistory());
    $tabs->render();
    echo '</div>';

    echo '<div id="content">';

    $tabs = new wcmAjaxTabs('import',true);
    $plugins = simplexml_load_file(WCM_DIR.'/xml/importPlugins.xml');
    foreach ($plugins->plugin as $plugin)
    {
        if (isset($plugin->module))
        {
            $description = $plugin->description;
            $tabs->addTab($plugin->className,$plugin->name,false,null,wcmModuleURL($plugin->module.'/main',
                          array('description' => (string) getConst($plugin->description),
                                'name'        => (string) $plugin->name,
                                'plugin'      => (string) $plugin->className)));
        }
    }
    $tabs->render();
    echo '</div>';
    
    include(WCM_DIR.'/pages/includes/footer.php');