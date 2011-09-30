<?php
/**
 * Project:     M
 * File:        modules/ugc/account/management.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

    $project = wcmProject::getInstance();
    $config = wcmConfig::getInstance();
    
    $userId = wcmSession::getInstance()->userId;
    
    $bizobject = wcmMVC_Action::getContext();
    $bizobject->refreshByWcmUser($userId);

    $sysobject = new wcmUser($project);
    if ($bizobject->wcmUserId)
    	$sysobject->refresh($bizobject->wcmUserId);

    $select = '<select name="bulkAction" id="bulkAction">';
    $select .= '<option value="">'.getConst(_BIZ_FOR_SELECTION).'</option>';
    $select .= '<option value="bulkDelete">'.getConst(_DELETE).'</option>';
    $select .= '<option value="bulkPermissions">'.getConst(_PERMISSIONS).'</option>';
    $select .= '</select>';
    $info = '<ul>';
    $info .= '<li><a href="#" class="chapter" onclick="openmodal(\'' . _NEW_ACCOUNT . '\',\'800\'); modalPopup(\'account\',\'insert\', 0, \'\', \'\');">'._NEW_ACCOUNT.'</a></li>';
    $info .= '<li style="float:left; margin: 2px">'.$select.'</li>';
    $info .= '<li style="float:right;"><a href="#" class="chapter" onClick="javascript:bulkupdate();">'._OK.'</a></li>';
    $info .= '</ul>';

    echo '<div class="zone">';
 	wcmGUI::openCollapsablePane(_USERS,true,$info);
	echo '<div id="results" class="tabular-presentation"><div class="wait">Loading...</div></div>';
	wcmGUI::closeCollapsablePane();
    echo '</div>';
	echo "<script type='text/javascript' defer='defer'>";
	echo "	ajaxAccount('refresh', 'childs', ".$userId.", 0, 0, 0, 'results');";
	echo "</script>";
