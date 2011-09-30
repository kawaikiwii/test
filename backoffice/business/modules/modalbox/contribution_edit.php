<?php

/**
 * Project:     WCM
 * File:        contribution_edit.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This page is called by the contribution tab, It allows to edit a comment
 *
 */
require_once '../../../initWebApp.php';

$id   = getArrayParameter($_REQUEST, "id", null);
$action   = getArrayParameter($_REQUEST, "action", null);
$contribution_text   = getArrayParameter($_REQUEST, "contribution_text", null);

$contribution = new contribution();
$contribution->refresh($id);

wcmGUI::openFieldset('');

if(isset($action) && $action == "save")
{
	$contribution->text = $contribution_text;
	if($contribution->save())
	{
		echo _BIZ_SAVED_BIZOBJECT;
	}
	else
	{
		echo _BIZ_ERROR_SAVE;
	}
	wcmFormGUI::renderHiddenField("comment_id", $id);
	wcmFormGUI::renderHiddenField("contribution_title", $contribution->title);
	wcmFormGUI::renderHiddenField("contribution_text", $contribution->text);
	
}
else if(isset($action) && $action == "delete")
{
	if($contribution->delete())
	{
		echo _OBJECT_DELETED;
		wcmFormGUI::renderHiddenField("comment_id_remove", $id);
	}
	else
	{
		echo _BIZ_ERROR;
	}
	
}
else
{
	wcmFormGUI::renderHiddenField("comment_id", $id);
	wcmFormGUI::renderTextArea("contribution_text", $contribution->text, _BIZ_TEXT);
}
wcmGUI::closeFieldset(); 
?>