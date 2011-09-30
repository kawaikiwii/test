<?php
/**
 * Project:     WCM
 * File:        event.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

 /**
 * This class implements the action controller for the channel
 */
class eventAction extends wcmMVC_BizAction
{

	/**
	 * Save contents from content tab
	 */
	private function saveContents()
	{
		if ( isset ($_REQUEST['content_event_title']))
		{
			
			$contentsTitle = getArrayParameter($_REQUEST, 'content_event_title');
			$contentsDescription = getArrayParameter($_REQUEST, 'content_event_description');
			$contentsText = getArrayParameter($_REQUEST, 'content_event_text');
			$titleSigns = getArrayParameter($_REQUEST, 'content_event_titleSigns');
			$titleWords = getArrayParameter($_REQUEST, 'content_event_titleWords');
			$descriptionSigns = getArrayParameter($_REQUEST, 'content_event_descriptionSigns');
			$descriptionWords = getArrayParameter($_REQUEST, 'content_event_descriptionWords');
			$textSigns = getArrayParameter($_REQUEST, 'content_event_textSigns');
			$textWords = getArrayParameter($_REQUEST, 'content_event_textWords');
			//$channelId = getArrayParameter($_REQUEST, 'channelId');
			//$channelIds = getArrayParameter($_REQUEST, 'channelIds');
			//$listIds = getArrayParameter($_REQUEST, 'listIds');
			//$sourceLocation = getArrayParameter($_REQUEST, 'sourceLocation');
			//$phone = getArrayParameter($_REQUEST, 'phone');
			//$email = getArrayParameter($_REQUEST, 'email');
			//$website = getArrayParameter($_REQUEST, 'website');
			
			// Specific for Event
			//$startsAt = getArrayParameter($_REQUEST, 'startsAt');
			//$endsAt = getArrayParameter($_REQUEST, 'endsAt');

			$contents = array (	'title' =>  $contentsTitle,
							   	'description' => $contentsDescription,
								'text' => $contentsText,
								'titleSigns' => $titleSigns,
								'titleWords' => $titleWords,
								'descriptionSigns' => $descriptionSigns,
								'descriptionWords' => $descriptionWords,
								'textSigns' => $textSigns,
								'textWords' => $textWords);/*,
								'channelId' => $channelId,
								'channelIds' => $channelIds,
								'listIds' => $listIds);,
								'sourceLocation' => $sourceLocation);,
								'phone' => $phone,
								'email' => $email,
								'website' => $website);*/

			$this->context->updateContents($contents);
		}
	}
	
	
	private function saveSchedules()
	{
		$temoin = false;
		$schedules = array();
		if (isset($_REQUEST['schedule_event_startsAt']))
		{	
		
			$startsAt = getArrayParameter($_REQUEST, 'schedule_event_startsAt');
			$endsAt = getArrayParameter($_REQUEST, 'schedule_event_endsAt');
	
			$schedules['startsAt'] = $startsAt;
			$schedules['endsAt'] = $endsAt;

			$temoin = true;
		}
		if (isset($_REQUEST['schedule_event_destinationId']))
		{
			$destinationId = getArrayParameter($_REQUEST, 'schedule_event_destinationId');
			$schedules['destinationId'] = $destinationId;

			$temoin = true;
		}
		if ($temoin) { $this->context->updateSchedules($schedules); }
	}
	private function saveSchedules1()
	{
		//$relations = $this->context->getRelations();
		//foreach ($relations as $relation)
		//{
			//if ($relation->destinationId);
		//}
		print_r($this->context->getRelations());
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
		$this->saveSchedules();
	}

}

