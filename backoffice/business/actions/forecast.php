<?php
/**
 * Project:     WCM
 * File:        forecast.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

 /**
 * This class implements the action controller for the channel
 */
class forecastAction extends wcmMVC_BizAction
{

	/**
	 * Save contents from content tab
	 */
	private function saveContents()
	{
		if ( isset ($_REQUEST['content_forecast_title']))
		{
			$contentsTitle = getArrayParameter($_REQUEST, 'content_forecast_title');
			$contentsDescription = getArrayParameter($_REQUEST, 'content_forecast_description');
			$contentsText = getArrayParameter($_REQUEST, 'content_forecast_text');
			$titleSigns = getArrayParameter($_REQUEST, 'content_forecast_titleSigns');
			$titleWords = getArrayParameter($_REQUEST, 'content_forecast_titleWords');
			$descriptionSigns = getArrayParameter($_REQUEST, 'content_forecast_descriptionSigns');
			$descriptionWords = getArrayParameter($_REQUEST, 'content_forecast_descriptionWords');
			$textSigns = getArrayParameter($_REQUEST, 'content_forecast_textSigns');
			$textWords = getArrayParameter($_REQUEST, 'content_forecast_textWords');
			//$channelId = getArrayParameter($_REQUEST, 'channelId');
			$channelIds = getArrayParameter($_REQUEST, 'channelIds');
			$listIds = getArrayParameter($_REQUEST, 'listIds');
			$folderIds = getArrayParameter($_REQUEST, 'folderIds');
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

			$this->context->updateSerialStorage('channelIds', $channelIds);
			$this->context->updateSerialStorage('listIds', $listIds);
			$this->context->updateSerialStorage('folderIds', $folderIds);
			$this->context->updateContents($contents);
		}
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

