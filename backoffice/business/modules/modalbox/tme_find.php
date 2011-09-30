<?php

/**
 * Project:     WCM
 * File:        tme_suggest.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
//require_once dirname(__FILE__).'/../../initWebApp.php';
require_once '../../../initWebApp.php';

$kind   = getArrayParameter($_REQUEST, "kind", null);
$targetid = getArrayParameter($_REQUEST, "targetid", null);
$data = getArrayParameter($_REQUEST, "data", null);

$context = wcmMVC_Action::getContext();
 
$session = wcmSession::getInstance();
$config = wcmConfig::getInstance();


$content = '';
//transform to assoc array
$data = get_object_vars(json_decode($data));

foreach($data as $key => $value)
{
	if(is_array($value))
		foreach($value as $subkey => $subvalue)
			$content .= trim($subvalue, " \t\n\r\0\x0B.") . ".\n";
	else
		$content .= trim($value, " \t\n\r\0\x0B.") . ".\n";
}

$method = "";
$type = "";

switch ($kind)
{
    case "abstract":
        wcmModule('business/modalbox/tme_abstract', 
                    array(  "input"=>$input,
                            "targetid"=>$targetid,
                            "language" => $context->getLanguage())
                 );
        break;
    case "_sentiment":
        $method = "NSentiment";
        break;
    case "_semanticData[ON]":
        $method = "NFinder";
        $type = "ON";
        break;
    case "_semanticData[PN]":
        $method = "NFinder";
        $type = "PN";              
        break;
    case "_semanticData[GL]":
        $method = "NFinder";
        $type = "GL";              
        break;    
    case "_semanticData[concepts]":
        $method = "NConceptExtractor";
        $type = "concepts";                              
        break;
    case "_semanticData[categories]":
        $method = "NCategorizer";
        $type = "categories";              
        break;
    case "_xmlTags[tags]":
        $method = "NCategorizer";
        $type = "categories";              
        break;            
    case "_similars":
        $method = "NLikeThis_Compare";
        $type = "similars";              
        break;         
}

    $tme = wcmSemanticServer::getInstance();
    $methods = array($method);
    $sdata = $tme->mineText($content, $context->getClass().'_'.$context->id, $context->getLanguage(), $methods);
    
    if($sdata == null)
    {
        echo "<script>wcmModal.showOk(wcmModal.title,'".addslashes(_BIZ_TME_PROCESSING_FAILED)."');</script>";
    }
    else
    {
        echo '<ul id="_tmeSuggestions">';
        
        $count = 0;
        
        foreach($sdata->$type as $key =>$value)
        {
            if($type == "similars")
                print '<li><label><input type="checkbox" value="'.$key.'"> '.$value['title'] .' ( '.$value['className'].' - '.$value['score'].'%)' .'</label></li>';
            else
                print '<li><label><input type="checkbox" value="'.$key.'"> '.$key .'</label></li>';
            
            $count++;
            
            // limit to the 10 first
            if ($count == 10) break;
        }
        echo '</ul>';
    
        if($count == 0)
        {
            echo "<script>wcmModal.showOk(wcmModal.title,'".addslashes(_BIZ_NO_SUGGESTION)."');</script>";
        }
    }

?>