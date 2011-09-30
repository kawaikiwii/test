<?php

/**
 * Project:     WCM
 * File:        edit_saved_search.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This page allows editing a saved search
 *
 */
require_once '../../../initWebApp.php';

$id   = getArrayParameter($_REQUEST, "id", null);
$saved_search_name   = getArrayParameter($_REQUEST, "saved_search_name", null);
$saved_search_description   = getArrayParameter($_REQUEST, "saved_search_description", null);
$action   = getArrayParameter($_REQUEST, "action", null);
$error = getArrayParameter($_REQUEST, "error", null);

$savedsearch = new wcmSavedSearch;
$savedsearch->refresh($id);

wcmGUI::openFieldset('');

if(isset($action) && $action == "save")
{
	$savedsearch->name = $saved_search_name;
	$savedsearch->description = $saved_search_description;
	
	if($savedsearch->save())
	{
		echo _BIZ_SAVED_BIZOBJECT;
		wcmFormGUI::renderHiddenField("saved_search_id", $id);
		wcmFormGUI::renderHiddenField("saved_search_name", $savedsearch->name);
		wcmFormGUI::renderHiddenField("saved_search_description", $savedsearch->description);
	}
	else
	{	
		echo "<script>wcmModal.showAjaxButtons('Edit', wcmBaseURL + 'business/modules/modalbox/edit_saved_search.php', {id: $id, saved_search_name:'$saved_search_name', saved_search_description:'$saved_search_description'}, editCallback, 	[wcmModal.getButtonByName('CANCEL'), 
  		 wcmModal.getButtonByName('SAVE')]);</script>"; 
		
	}
	
}
else
{
	wcmFormGUI::renderHiddenField("saved_search_id", $savedsearch->id);
	wcmFormGUI::renderTextField("saved_search_name", isset($saved_search_name)?$saved_search_name:$savedsearch->name, _BIZ_TITLE);
	wcmFormGUI::renderTextArea("saved_search_description", isset($saved_search_description)?$saved_search_description:$savedsearch->description, _BIZ_DESCRIPTION);
}

wcmGUI::closeFieldset(); 

?>