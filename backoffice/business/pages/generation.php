<?php

/**
 * Project:     WCM
 * File:        generation.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    renderAssetBar(_MENU_WEB_DESIGN, _MENU_PUBLISH_LAUNCH_GENERATION);

    // Build generation tree
    $tree = new wcmTree("generation_exe", null, _GENERATIONS, "refresh.gif", "tree_generation_exe", null, ':', null, 'executeGenerationRule');
    $tree->initFromSession("generation_exe");

    $url = $config['wcm.backOffice.url'] . '/dialogs/generate.php?rule=';
    echo '<script type="text/javascript">' . 
         '    function executeGenerationRule(treeView, nodePath) {'.
         '        openDialog("'. $url .'"+nodePath, null, 1000, 530, null, null, nodePath);'.
         '    }'.
         '</script>';

    echo '<div id="treeview">';
    echo $tree->renderHTML();
    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');