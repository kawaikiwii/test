<?php
/**
 * Project:     WCM
 * File:        otvFinale.php
 *
 * @copyright   (c)2010 Nstein Technologies
 * @version     4.x
 *
 */

 /**
 * This class implements the action controller for the channel
 */
class otvFinaleAction extends wcmMVC_BizAction
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

		$this->context->updateText($arrayText);	

		$arrayText2 = array();
		
		if (isset ($_REQUEST['subtitle21']) && isset ($_REQUEST['paragraph21']))
		{
			$arrayText2[1]["subtitle"] = getArrayParameter($_REQUEST, 	'subtitle21');
			$arrayText2[1]["paragraph"] = getArrayParameter($_REQUEST, 	'paragraph21');
		}
		if (isset ($_REQUEST['subtitle22']) && isset ($_REQUEST['paragraph22']))
		{
			$arrayText2[2]["subtitle"] = getArrayParameter($_REQUEST, 	'subtitle22');
			$arrayText2[2]["paragraph"] = getArrayParameter($_REQUEST, 	'paragraph22');
		}
		if (isset ($_REQUEST['subtitle23']) && isset ($_REQUEST['paragraph3']))
		{
			$arrayText2[3]["subtitle"] = getArrayParameter($_REQUEST, 	'subtitle23');
			$arrayText2[3]["paragraph"] = getArrayParameter($_REQUEST, 	'paragraph23');
		}
		if (isset ($_REQUEST['subtitle24']) && isset ($_REQUEST['paragraph24']))
		{
			$arrayText2[4]["subtitle"] = getArrayParameter($_REQUEST, 	'subtitle24');
			$arrayText2[4]["paragraph"] = getArrayParameter($_REQUEST, 	'paragraph24');
		}
		if (isset ($_REQUEST['subtitle25']) && isset ($_REQUEST['paragraph25']))
		{
			$arrayText2[5]["subtitle"] = getArrayParameter($_REQUEST, 	'subtitle25');
			$arrayText2[5]["paragraph"] = getArrayParameter($_REQUEST, 	'paragraph25');
		}

		$this->context->updateText2($arrayText2);
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
