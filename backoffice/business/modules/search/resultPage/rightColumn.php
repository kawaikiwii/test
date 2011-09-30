<?php
/**
 * Project:     WCM
 * File:        business/modules/search/rightColumn.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     3.2
 *
 */

$context = $params[0];          // from wcmModule() call

$savedSearchAction = getArrayParameter($_SESSION, 'savedSearchAction', 'showSavedSearches');

?>
<div id="tools">
    <?php
    echo '<div id="manageBin">';
    echo '<div class="tasks">';
    echo '<span id="compteur"><h4>'._BIZ_SELECTED_ITEMS.' (<span>';
            if (isset($_SESSION['tempBin'])) 
                echo count($_SESSION['tempBin']);
            else
                echo "0";
        echo "</span>)</h4></span>";
        echo '<div class="scroll">';
        echo '<ul>';
        echo '<li>';
        echo "<a href=\"#\" onclick=\"manageBin('addSessionToSelectedBin', '', '', '', document.getElementById('selectBin').options[document.getElementById('selectBin').selectedIndex].value, 'binData', '')\" class=\"view\">"._BIZ_ADD_TO_SELECTED_BIN."</a>";
        echo '</li>';
        echo '<li>';
        echo "<a href=\"#\" title=\""._BIZ_SAVE."\" onclick=\"openmodal('" . _BIZ_CREATE_BIN . "'); modalPopup('bin','createBinFromSession', ''); return false;\" class=\"view\">"._BIZ_CREATE_BIN."</a>";
        echo '</li>';
        echo '<li>';
        echo "<a href=\"#\" class=\"view\" onclick=\"_wcmExportCollection(); return false;\" class=\"view\">"._BIZ_EXPORT_COLLECTION."</a>";
        echo '</li>';
/* 2009.03.25 : ExportRules - BEGIN */
        echo '<li>';
        echo "<a href=\"#\" class=\"view\" onclick=\"_wcmExportExportRule(); return false;\" class=\"view\">"._BIZ_EXPORT_EXPORTRULE."</a>";
        echo '</li>';
/* 2009.03.25 : ExportRules - END */
        echo '</ul>';   
        echo '</div>';
    echo '</div>';
    
    echo '<div class="bins" id="bins">';
    $binSearchControl = new binSearchControl();
    $binSearchControl->initialLoad();
    echo '</div>';
    echo '</div>';

    echo '<div id="searches" class="searches">';
    $savedSearchControl = new savedSearchControl();
    $savedSearchControl->initialLoad($savedSearchAction);
    echo '</div>';
    ?>
</div>
<div id="modalWindow">
    <h2 id="modalTitle"></h2>
    <div id="modalDialog"></div>
</div>
<div id="modalBackground"></div>
