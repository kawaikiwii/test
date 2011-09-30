<?php
/**
 * Project:     M
 * File:        modules/export/exportRule/distributionChannels.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

    $bizobject = wcmMVC_Action::getContext();

    $info = '<ul>';
    $info .= '<li><a href="#" class="chapter" onclick="openmodal(\'' . _DISTRIBUTION_CHANNEL . '\',\'500\'); modalPopup(\'distributionChannel\',\'insert\', 0, '.$bizobject->id.', \'\');">'._NEW_DISTRIBUTION_CHANNEL.'</a></li>';
    $info .= '</ul>';

    echo '<div class="zone">';
 	wcmGUI::openCollapsablePane(_DISTRIBUTION_CHANNELS,true,$info);
	echo '<div id="results" class="tabular-presentation">&nbsp;</div>';
	wcmGUI::closeCollapsablePane();
    echo '</div>';

	echo "<script type='text/javascript' defer='defer'>";
	echo "ajaxDistributionChannel('refresh', '".$bizobject->id."', 0, 'results','');";
	echo "</script>";
