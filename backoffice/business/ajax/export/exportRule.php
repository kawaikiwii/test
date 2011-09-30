<?php
/**
 * Project:     M
 * File:        business/ajax/export/exportRule.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 */

require_once dirname(__FILE__) . '/../../../initWebApp.php';

$response = getArrayParameter($_REQUEST, 'response', null);


switch ($response)
{
    case 'OK':
        $id = getArrayParameter($_REQUEST, 'id', null);
        $exportRule = new exportRule();
        $exportRule->refresh($id);

        // array containing select objects (className_id => true)
        $bizobjects = getArrayParameter($_SESSION, 'tempBin', null);
        if (($bizobjects)&&($exportRule->id))
        {
		//compute documents
		$documents = array();
		foreach($bizobjects as $key => $val)
		{
		    $obj = explode('_', $key);
		    $currentObj = new $obj[0]();
		    $currentObj->refresh($obj[1]);
		    $documents[] = $currentObj;
		    
		}
		
		//execute export rule        	
		$exportRule->execute($documents);

		// display end message
		wcmGUI::openFieldset();
		echo '<li>' . _BIZ_EXPORTRULE_COMPLETE . '</li><br/>';
		wcmGUI::closeFieldset();
		break;;
        }
	break;        

    // display relations form
    default:
        $bizobjects = getArrayParameter($_SESSION, 'tempBin', null);
        if ($bizobjects)
        {
		$exportRules = exportRule::getExportRules();
		$collArray[0] = '('._SELECT.')';
		foreach ($exportRules as $exportRule)
		{
		    $collArray[$exportRule->id] = $exportRule->title;
		}
		wcmGUI::openForm('exportExportRule');
		wcmGUI::openFieldset();
		wcmGUI::renderDropdownField('_wcmExportRuleId', $collArray, null, _BIZ_CHOOSE_EXPORTRULE);
		wcmGUI::closeFieldset();
		wcmGUI::closeForm();
	}
        break;
}