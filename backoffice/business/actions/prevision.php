<?php
/**
 * Project:     WCM
 * File:        prevision.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */

 /**
 * This class implements the action controller for the channel
 */
class previsionAction extends wcmMVC_BizAction
{

	/**
	 * Save contents from content tab
	 */
	private function saveContents()
	{
				
		if (isset($_REQUEST['content_prevision_title']))
		{
			$contentsTitle = getArrayParameter($_REQUEST, 'content_prevision_title');
			$contentsDescription = getArrayParameter($_REQUEST, 'content_prevision_description');
			$contentsText = getArrayParameter($_REQUEST, 'content_prevision_text');
			$titleSigns = getArrayParameter($_REQUEST, 'content_prevision_titleSigns');
			$titleWords = getArrayParameter($_REQUEST, 'content_prevision_titleWords');
			$descriptionSigns = getArrayParameter($_REQUEST, 'content_prevision_descriptionSigns');
			$descriptionWords = getArrayParameter($_REQUEST, 'content_prevision_descriptionWords');
			$textSigns = getArrayParameter($_REQUEST, 'content_prevision_textSigns');
			$textWords = getArrayParameter($_REQUEST, 'content_prevision_textWords');
			//$channelId = getArrayParameter($_REQUEST, 'channelId');
			//$sourceLocation = getArrayParameter($_REQUEST, 'sourceLocation');

			$contents = array (	'title' =>  $contentsTitle,
							   	'description' => $contentsDescription,
								'text' => $contentsText,
								'titleSigns' => $titleSigns,
								'titleWords' => $titleWords,
								'descriptionSigns' => $descriptionSigns,
								'descriptionWords' => $descriptionWords,
								'textSigns' => $textSigns,
								'textWords' => $textWords);/*
								'channelId' => $channelId,
								'channelIds' => $channelIds,
								'listIds' => $listIds,
								'sourceLocation' => $sourceLocation);*/

			$this->context->updateContents($contents);
		}
		
		$channelIds = getArrayParameter($_REQUEST, 'channelIds');
		$listIds = getArrayParameter($_REQUEST, 'listIds');
		$folderIds = getArrayParameter($_REQUEST, 'folderIds');
		
		if(empty($listIds)) 
			$this->context->listIds = Array();
		else 
			$this->context->updateSerialStorage('listIds', $listIds);
		
		if(empty($channelIds)) 
			$this->context->channelIds = Array();
		else 
			$this->context->updateSerialStorage('channelIds', $channelIds);
		
		if(empty($folderIds)) 
			$this->context->folderIds = Array();
		else 
			$this->context->updateSerialStorage('folderIds', $folderIds);
		
				
	}
    
	/**
	 * beforeSaving is called on checkin and on save before the store
	 *
	 * @param wcmSession $session Current session
	 * @param wcmProject $project Current project
	 */
	protected function beforeSaving($session, $project)
	{
		parent::beforeSaving($session, $project);
		$this->saveContents();
	}
}

