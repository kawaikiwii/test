<?php
/**
 * Project:     WCM
 * File:        location.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

 /**
 * This class implements the action controller for the channel
 */
class locationAction extends wcmMVC_BizAction
{

	/**
	 * Save contents from content tab
	 */
	private function saveContents()
	{
		if ( isset ($_REQUEST['content_location_title']))
		{
			$contentsTitle = getArrayParameter($_REQUEST, 'content_location_title');
			$contentsDescription = getArrayParameter($_REQUEST, 'content_location_description');
			$contentsText = getArrayParameter($_REQUEST, 'content_location_text');
			$titleSigns = getArrayParameter($_REQUEST, 'content_location_titleSigns');
			$titleWords = getArrayParameter($_REQUEST, 'content_location_titleWords');
			$descriptionSigns = getArrayParameter($_REQUEST, 'content_location_descriptionSigns');
			$descriptionWords = getArrayParameter($_REQUEST, 'content_location_descriptionWords');
			$textSigns = getArrayParameter($_REQUEST, 'content_location_textSigns');
			$textWords = getArrayParameter($_REQUEST, 'content_location_textWords');
			//$channelId = getArrayParameter($_REQUEST, 'channelId');
			//$channelIds = getArrayParameter($_REQUEST, 'channelIds');
			
			//$address = getArrayParameter($_REQUEST, 'address');
			//$zipcode = getArrayParameter($_REQUEST, 'zipcode');
			//$country = getArrayParameter($_REQUEST, 'country');

			$contents = array (	'title' =>  $contentsTitle,
							   	'description' => $contentsDescription,
								'text' => $contentsText,
								'titleSigns' => $titleSigns,
								'titleWords' => $titleWords,
								'descriptionSigns' => $descriptionSigns,
								'descriptionWords' => $descriptionWords,
								'textSigns' => $textSigns,
								'textWords' => $textWords);
								//'channelId' => $channelId,
								//'channelIds' => $channelIds);
								//'address' => $address,
								//'zipcode' => $zipcode,
								//'country' => $country);

			$this->context->updateSerialStorage('channelIds', $channelIds);
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

