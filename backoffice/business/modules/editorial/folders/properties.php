<?php
/**
 * File:        /business/modules/editorial/folders/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 * @author jmeyer@relaxnews.com
 *
 */

    require_once(WCM_DIR.'/business/api/toolbox/biz.relax.toolbox.php');

    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    //print_r($config);

    $_SESSION['wcm']['footprint']['context'] = $bizobject;


    echo '<div class="zone">';


	/*
     * Display Special folders
     */
    wcmGUI::openCollapsablePane(_RLX_SPECIALFOLDERS);
    
	$specialfoldersHtml = getSpecialfoldersList(true);

	
	wcmGUI::openFieldset('');
	
	echo '<table><tr>';

	$found = 0;	
	foreach($specialfoldersHtml['parents'] as $parent)
	{
		echo '<td valign="top">';
		echo '<b style="display:block; font-size:14px; padding-bottom:0.8em;">'.$parent['label'].'</b><br>';
		
		foreach($specialfoldersHtml['folders'] as $folder)
		{
			if (isset($folder['idParent']) && $folder['idParent'] == $parent['id'])
			{
				$obj = new channel();
				$obj->refresh($folder['id']);
				$transitions = $obj->getAvailableTransitions();
				
				echo "<div id=\"divFolder_".$folder['id']."\">";
				
				echo '<table>';
				echo '<tr><td valign="top"><i class="workflowStateColor '.$obj->workflowState.'" style=""display:block; float:left;>';
				echo $obj->workflowState;
				echo '</i></td>';
				echo '<td valign="top">';
				
				foreach ($transitions as $transition)
				{
					echo "<a href=\"javascript:void(0);\" onClick=\"executeTransition('channel', '".$folder['id']."', '".$transition->id."'); window.location.reload();\" style=\"display:block; float:left; padding:3px; border:#ccc 1px solid; margin-right:15px;\">&raquo;";
					echo getConst($transition->name);
					echo '</a>';
				}
				
				echo '</td></tr></table>';
				
				wcmGUI::renderTextField('folder_'.$folder['id'], $folder['label']);
				echo "<a href=\"javascript:void(0);\" onClick=\"saveFolder(document.getElementById('folder_".$folder['id']."').value, '".$folder['id']."');\">&raquo; Save</a>&nbsp;&nbsp;&nbsp;";
				echo "<a href=\"javascript:void(0);\" onClick=\"deleteFolder(document.getElementById('folder_".$folder['id']."').value, '".$folder['id']."');\">&raquo; Delete</a>&nbsp;&nbsp;&nbsp;";
				echo "</div>";
				
				echo '<br><br>';
			}
		}
		
	//echo "Temoin";

		echo '<div id="newFoldersZone" style="margin-top:1em;">';
		echo '<b style="display:block; font-size:14px; padding-bottom:0.8em;">Create New Folder :</b><br>';
		echo '<input id=folder_0_'.$found.' type=text size=30 /><br>';
		echo "<a href=\"javascript:void(0);\" onClick=\"saveNewFolder(document.getElementById('folder_0_".$found."').value, '".$parent['id']."');\">&raquo; Save</a>";
		echo '</div>';
		
		echo '</td>';
		
		$found++;
	}

	echo '</tr></table>';
	
	wcmGUI::closeCollapsablePane();

    echo '</div>';
