<?php
/**
 * Project:     WCM
 * File:        biz.manageBin.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

$command  = getArrayParameter($_REQUEST, "command", null);
$kind     = getArrayParameter($_REQUEST, "kind", null);
$id     = getArrayParameter($_REQUEST, "id", null);
$input = getArrayParameter($_REQUEST, "input", null);
$targetid = getArrayParameter($_REQUEST, "targetid", null);
$score = getArrayParameter($_REQUEST, "score", null);

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Xml output
header('Content-Type: text/xml;charset=UTF-8');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

// Write ajax response
echo "<ajax-response>\n";
echo '<response type="item" id="modalDialog"><![CDATA[';

switch($command)
{
    case "savedSearch":
        switch ($kind)
        {
            case "createSearch":
                wcmModule('business/modalbox/create_saved_search');             
                break;
        }
        break;
    case "bin":
        switch ($kind)
        {
            case "createEmpty":
                wcmModule('business/modalbox/create_empty_bin');    
                break;
            case "updateBin":
                wcmModule('business/modalbox/update_bin', array($id));  
                break;
            case "exportBin":
                wcmModule('business/modalbox/export_bin', array($id));  
                break;
           case "printBin":
                wcmModule('business/modalbox/print_bin', array($id));  
                break;
           case "createBinFromSession":
                 wcmModule('business/modalbox/create_bin_session'); 
                break;
        }
        break;
    case "changesite":
        switch ($kind)
        {
            case "list":
                
                $session = wcmSession::getInstance();
                $config = wcmConfig::getInstance();
                $site = $session->getSite();
                $htmlList = null;
                $enumSite = new site();
                if ($enumSite->beginEnum())
                { 
                    while ($enumSite->nextEnum())
                    {
                        if (($session->isAllowed($enumSite, wcmPermission::P_READ)) && ($enumSite->id != $site->id))
                        {
                            $htmlList .= '<li><a href="' . $config['wcm.backoffice.url'] .
                                 '?_wcmAction=home&_wcmSiteId=' . $enumSite->id . '">' .
                                 $enumSite->title . '</a></li>';
                        }
                    }
                    $enumSite->endEnum();
                }
                unset($enumSite);
                wcmModule('business/modalbox/change_site', array('list' => $htmlList));             
                break;
        }
        break;
    case "changephoto":
        switch ($kind)
        {
            case "new":
                $session = wcmSession::getInstance();
                $config = wcmConfig::getInstance();
                wcmModule('business/modalbox/change_photo');              
                break;
        }
        break;
    case "templateCategory":
        switch ($kind)
        {
            default:
                $session = wcmSession::getInstance();
                $config = wcmConfig::getInstance();
                wcmModule('generation/templateCategory/select');              
                break;
        }
        break;
    case "tme":
        switch ($kind)
        {
            case "abstract":
                $session = wcmSession::getInstance();
                $config = wcmConfig::getInstance();
                
                wcmModule('business/modalbox/tme_abstract', array("input"=>$input, "targetid"=>$targetid));              
                break;
            
            case "_semanticData[ON]":
                wcmModule('business/modalbox/tme_suggest', array("method"=>"NFinder", "type"=>"ON", "targetid"=>$targetid, "score"=>$score));              
                break;
            case "_semanticData[PN]":
                wcmModule('business/modalbox/tme_suggest', array("method"=>"NFinder", "type"=>"PN", "targetid"=>$targetid, "score"=>$score));              
                break;
            case "_semanticData[GL]":
                wcmModule('business/modalbox/tme_suggest', array("method"=>"NFinder", "type"=>"GL", "targetid"=>$targetid, "score"=>$score));              
                break;    
            case "_semanticData[concepts]":
                wcmModule('business/modalbox/tme_suggest', array("method"=>"NConceptExtractor", "type"=>"concepts", "targetid"=>$targetid, "score"=>$score));              
                break;
            case "_semanticData[categories]":
                wcmModule('business/modalbox/tme_suggest', array("method"=>"NCategorizer", "type"=>"categories", "targetid"=>$targetid, "score"=>$score));              
                break;
            case "_xmlTags[tags]":
                wcmModule('business/modalbox/tme_suggest', array("method"=>"NCategorizer", "type"=>"categories", "targetid"=>$targetid, "score"=>$score));              
                break;    
                
             
        }
        break;
    case "account":
	wcmModule('business/modalbox/account_edit',array("action" => $kind, "id" => $id));             
        break;
}
echo "</ul>";
echo ']]></response>';
echo "</ajax-response>";
?>
