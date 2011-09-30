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
class videoAction extends wcmMVC_BizAction
{

	private function saveContents()
	{
		if ( isset ($_REQUEST['content_video_title']))
		{
			$contentsTitle = getArrayParameter($_REQUEST, 'content_video_title');
			$contentsDescription = getArrayParameter($_REQUEST, 'content_video_description');
			$contentsText = getArrayParameter($_REQUEST, 'content_video_text');
			$titleSigns = getArrayParameter($_REQUEST, 'content_video_titleSigns');
			$titleWords = getArrayParameter($_REQUEST, 'content_video_titleWords');
			$descriptionSigns = getArrayParameter($_REQUEST, 'content_video_descriptionSigns');
			$descriptionWords = getArrayParameter($_REQUEST, 'content_video_descriptionWords');
			$textSigns = getArrayParameter($_REQUEST, 'content_video_textSigns');
			$textWords = getArrayParameter($_REQUEST, 'content_video_textWords');

			$contents = array (	'title' =>  $contentsTitle,
							   	'description' => $contentsDescription,
								'text' => $contentsText,
								'titleSigns' => $titleSigns,
								'titleWords' => $titleWords,
								'descriptionSigns' => $descriptionSigns,
								'descriptionWords' => $descriptionWords,
								'textSigns' => $textSigns,
								'textWords' => $textWords);
			$this->context->updateContents($contents);
		}
	}

	protected function beforeSaving($session, $project)
	{
		parent::beforeSaving($session, $project);
		$this->saveContents();
	}

}

