<?php
/**
 * Project:     M
 * File:        modules/export/relaxTask/relaxTask.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

    $project = wcmProject::getInstance();
    $config = wcmConfig::getInstance();
    
    $bizobject = wcmMVC_Action::getContext();
	
    $info = '<ul>';
    $info .= '<li><a href="#" class="chapter" onclick="openmodal(\'Nouvelle Tache\',\'850\'); modalPopup(\'task\',\'insert\', 0);">Nouvelle Tache</a></li>';
    $info .= '</ul>';

    echo '<div class="zone">';
 	wcmGUI::openCollapsablePane('Taches',true,$info);
	echo '<div id="results" class="tabular-presentation"><div class="wait">Loading...</div></div>';
	wcmGUI::closeCollapsablePane();
    echo '</div>';

	echo "<script type='text/javascript' defer='defer'>";
	echo "	ajaxRelaxTask('refresh', 'results');";
	echo "</script>";
