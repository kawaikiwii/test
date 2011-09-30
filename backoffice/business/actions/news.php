<?php
/**
 * Project:     WCM
 * File:        article.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

 /**
 * This class implements the action controller for the channel
 */
class newsAction extends wcmMVC_BizAction
{

	/**
	 * Save contents from content tab
	 */
	private function saveContents()
	{			
		if ( isset ($_REQUEST['content_news_title']))
		{
			$contentsTitle = getArrayParameter($_REQUEST, 'content_news_title');
			$contentsDescription = getArrayParameter($_REQUEST, 'content_news_description');
			$contentsText = getArrayParameter($_REQUEST, 'content_news_text');
			$titleSigns = getArrayParameter($_REQUEST, 'content_news_titleSigns');
			$titleWords = getArrayParameter($_REQUEST, 'content_news_titleWords');
			$descriptionSigns = getArrayParameter($_REQUEST, 'content_news_descriptionSigns');
			$descriptionWords = getArrayParameter($_REQUEST, 'content_news_descriptionWords');
			$textSigns = getArrayParameter($_REQUEST, 'content_news_textSigns');
			$textWords = getArrayParameter($_REQUEST, 'content_news_textWords');
			
			$folderIds = getArrayParameter($_REQUEST, 'folderIds');
			//$channelId = getArrayParameter($_REQUEST, 'channelId');
			/*$obj_tmp = new news();
			$new_channelids = $channelIds;
			foreach($new_channelids as $channelid) {
				$channel = new channel($obj_tmp->getProject(), $channelid);
				@$parentid = $channel->getParentChannel()->id;
				if($parentid != NULL && array_search($parentid,$new_channelids) === false) {
					array_push($channelIds,$parentid);
					$pilier = $obj_tmp->getPilier($parentid);
					if($pilier != NULL && array_search($pilier,$new_channelids) === false)
						array_push($channelIds,$pilier);
				}
			}*/
			//$sourceLocation = getArrayParameter($_REQUEST, 'sourceLocation');
			
			// fatal bug with "::" in title
			if (!empty($contentsTitle))
				$contentsTitle = str_replace("::", ":", $contentsTitle);
			if (!empty($contentsDescription))
				$contentsDescription = str_replace("::", ":", $contentsDescription);
			if (!empty($contentsText))
				$contentsText = str_replace("::", ":", $contentsText);
				
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
			
			/*
			$this->context->updateSerialStorage('channelIds', $channelIds);
			$this->context->updateSerialStorage('listIds', $listIds);
			$this->context->updateSerialStorage('folderIds', $folderIds);
			*/
			$this->context->updateContents($contents);
		}
		
		// traitement de l'onglet categorisation
		if (isset($_REQUEST['tab_categorization']))
		{
			$channelIds = getArrayParameter($_REQUEST, 'channelIds');
			$listIds = getArrayParameter($_REQUEST, 'listIds');
				
			if(empty($listIds)) 
				$this->context->listIds = Array();
			else 
				$this->context->updateSerialStorage('listIds', $listIds);
			
			if(empty($channelIds)) 
				$this->context->channelIds = Array();
			else 
				$this->context->updateSerialStorage('channelIds', $channelIds);
		}
		
		// traitement de l'onglet dossiers speciaux
		if (isset($_REQUEST['tab_specialfolders']))
		{
			$folderIds = getArrayParameter($_REQUEST, 'folderIds');
				
			if(empty($folderIds)) 
				$this->context->folderIds = Array();
			else 
				$this->context->updateSerialStorage('folderIds', $folderIds);
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


	/*public function onDuplicate($session, $project)
	{
		$bizobject = $this->getContext();
		$language = (isset($_GET['duplicateLanguage'])) ? array($_GET['duplicateLanguage']) : NULL;
		return $bizobject->duplicateCurrentObjetInOtherLanguages($language);
	}*/
	
	public function onCreateslideshow()
	{
		$bizobject = $this->getContext();
		$bizobject->createSlideshow();
	}
}

