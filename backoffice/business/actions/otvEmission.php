<?php
/**
 * Project:     WCM
 * File:        otvEmission.php
 *
 * @copyright   (c)2010 Nstein Technologies
 * @version     4.x
 *
 */

 /**
 * This class implements the action controller for the channel
 */
class otvEmissionAction extends wcmMVC_BizAction
{

	/**
	 * Save contents from content tab
	 */
	private function saveContent()
	{
		$arrayText = array();
		
		if (isset ($_REQUEST['subtitle1']) && isset ($_REQUEST['paragraph1']))
		{
			$arrayText[1]["subtitle"] = getArrayParameter($_REQUEST, 	'subtitle1');
			$arrayText[1]["paragraph"] = getArrayParameter($_REQUEST, 	'paragraph1');
		}
		if (isset ($_REQUEST['subtitle2']) && isset ($_REQUEST['paragraph2']))
		{
			$arrayText[2]["subtitle"] = getArrayParameter($_REQUEST, 	'subtitle2');
			$arrayText[2]["paragraph"] = getArrayParameter($_REQUEST, 	'paragraph2');
		}
		if (isset ($_REQUEST['subtitle3']) && isset ($_REQUEST['paragraph3']))
		{
			$arrayText[3]["subtitle"] = getArrayParameter($_REQUEST, 	'subtitle3');
			$arrayText[3]["paragraph"] = getArrayParameter($_REQUEST, 	'paragraph3');
		}
		if (isset ($_REQUEST['subtitle4']) && isset ($_REQUEST['paragraph4']))
		{
			$arrayText[4]["subtitle"] = getArrayParameter($_REQUEST, 	'subtitle4');
			$arrayText[4]["paragraph"] = getArrayParameter($_REQUEST, 	'paragraph4');
		}
		if (isset ($_REQUEST['subtitle5']) && isset ($_REQUEST['paragraph5']))
		{
			$arrayText[5]["subtitle"] = getArrayParameter($_REQUEST, 	'subtitle5');
			$arrayText[5]["paragraph"] = getArrayParameter($_REQUEST, 	'paragraph5');
		}

		if (isset ($_REQUEST['subtitle6']) && isset ($_REQUEST['paragraph6']))
		{
			$arrayText[6]["subtitle"] = getArrayParameter($_REQUEST, 	'subtitle6');
			$arrayText[6]["paragraph"] = getArrayParameter($_REQUEST, 	'paragraph6');
		}
		if (isset ($_REQUEST['subtitle7']) && isset ($_REQUEST['paragraph7']))
		{
			$arrayText[7]["subtitle"] = getArrayParameter($_REQUEST, 	'subtitle7');
			$arrayText[7]["paragraph"] = getArrayParameter($_REQUEST, 	'paragraph7');
		}
		if (isset ($_REQUEST['subtitle8']) && isset ($_REQUEST['paragraph8']))
		{
			$arrayText[8]["subtitle"] = getArrayParameter($_REQUEST, 	'subtitle8');
			$arrayText[8]["paragraph"] = getArrayParameter($_REQUEST, 	'paragraph8');
		}
		if (isset ($_REQUEST['subtitle9']) && isset ($_REQUEST['paragraph9']))
		{
			$arrayText[9]["subtitle"] = getArrayParameter($_REQUEST, 	'subtitle9');
			$arrayText[9]["paragraph"] = getArrayParameter($_REQUEST, 	'paragraph9');
		}
		if (isset ($_REQUEST['subtitle10']) && isset ($_REQUEST['paragraph10']))
		{
			$arrayText[10]["subtitle"] = getArrayParameter($_REQUEST, 	'subtitle10');
			$arrayText[10]["paragraph"] = getArrayParameter($_REQUEST, 	'paragraph10');
		}
		$this->context->updateText($arrayText);		
		
		$arrayVideo = array();
		
		if (isset ($_REQUEST['video1']))
			$arrayVideo["video"][1] = getArrayParameter($_REQUEST, 	'video1');
		
		if (isset ($_REQUEST['video2']))
			$arrayVideo["video"][2] = getArrayParameter($_REQUEST, 	'video2');
		
		if (isset ($_REQUEST['video3']))
			$arrayVideo["video"][3] = getArrayParameter($_REQUEST, 	'video3');
			
		$this->context->updateVideos($arrayVideo);	
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
		$this->saveContent();
	}

}

