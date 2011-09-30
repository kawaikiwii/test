<?php
/**
 * Project:     WCM
 * File:        otvPortrait.php
 *
 * @copyright   (c)2010 Nstein Technologies
 * @version     4.x
 *
 */

 /**
 * This class implements the action controller for the channel
 */
class otvPortraitAction extends wcmMVC_BizAction
{

	/**
	 * Save contents from content tab
	 */
	private function saveParagraphs()
	{
		$arrayDescription = array();
		
		if (isset ($_REQUEST['paragraph1']))
			$arrayDescription["paragraph"][1] = getArrayParameter($_REQUEST, 	'paragraph1');
		if (isset ($_REQUEST['paragraph2']))
			$arrayDescription["paragraph"][2] = getArrayParameter($_REQUEST, 	'paragraph2');
		if (isset ($_REQUEST['paragraph3']))
			$arrayDescription["paragraph"][3] = getArrayParameter($_REQUEST, 	'paragraph3');
		if (isset ($_REQUEST['paragraph4']))
			$arrayDescription["paragraph"][4] = getArrayParameter($_REQUEST, 	'paragraph4');
		if (isset ($_REQUEST['paragraph5']))
			$arrayDescription["paragraph"][5] = getArrayParameter($_REQUEST, 	'paragraph5');
			
		$this->context->updateDescription($arrayDescription);		
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
		$this->saveParagraphs();
	}

}

