<?php
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();
$session = wcmSession::getInstance();
$connector = $project->datalayer->getConnectorByReference("biz");
$db = $connector->getBusinessDatabase();
global $db, $project, $session;
// What we need:
// Object id
// Object collection
// Message Id

header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );
header('Content-Type: text/xml;charset=UTF-8');
//header("Content-Type: text/html");

$objectId    = $_REQUEST['id'];
$objectClass = $_REQUEST['className'];
$messageId   = $_REQUEST['messageId'];
$action      = $_REQUEST['action'];
$version     = $_REQUEST['version'];
$trace       = "";
$manager     = "";
$message     = "";

switch ($action)
{
        case "import":
                $damWS = new DAMBridge(DAM_WS.'BizObjectManagementService.asmx?WSDL', 'root', 'root');
                if ($damWS)
                    $damObjectXml = $damWS->getDamBizObjectVersion($objectClass, $objectId, $version); 
                $message = "Objet '".$objectClass."' DAM#".$objectId;
                echo $damObjectXml->GetVersionResult;
                $update=false;
                $array = explode('.',$objectClass);
                $className = strtolower($array[count($array)-1]);
                $currentObject = new $className($project);
                $currentObject->refreshFromSource("DAM",$objectId);
                if ($currentObject->id)
                    $update=true;
                else
                    $currentObject->siteId=$_SESSION["siteId"];
                     
                    $domDoc = new DOMDocument();
                    $domDoc->loadXML(html_entity_decode($damObjectXml->GetVersionResult));
                
                $currentObject->initFromXMLDocument_DAM($domDoc);
                $currentObject->sourceVersion = $version;
                $currentObject->checkin();
                if ($update)                
                    $message = "L'objet '".$className."' DAM#".$objectId."/WCM#".$currentObject->id." a été mis à jour.";
                else
                    $message = "L'objet '".$className."' DAM#".$objectId."/WCM#".$currentObject->id." a été importé.";
        break;

        case "manage":
            $update=false;
            $array = explode('.',$objectClass);
            $collection = $array[count($array)-1];
            $className = strtolower($collection);
            $currentObject = new $className($project);
            $currentObject->refreshFromSource("DAM",$objectId);
            if ($currentObject->id)
                $update=true;
            $damWS = new DAMBridge(DAM_WS.'BizObjectManagementService.asmx?WSDL', 'root', 'root');
            if ($damWS)
                $history = $damWS->getDamBizObjectHistory($objectClass, $objectId); 
            $message = "Objet '".$objectClass."' DAM#".$objectId;
            $domXml = new DOMDocument();
            for ($i = 0; $i < count($history); $i++ )
            {
                $domXml->loadXML($history[$i]);
                if (!$domXml)
                    $manager .= "<b>"._BIZ_INVALID_XML."</b>";
                else
                {
                    $root = $domXml->documentElement;   
                    // Stockage de la version DAM du document
                    // TODO : A modifier avec mise à jour du Werservice 
                    //$version = $root->getElementsByTagName("ModifiedAt")->item(0)->nodeValue;
                    $version = $i;
                    // Cas paer défaut => Création initiale
                    $bgcolor = '#FFFFFF';
                    $action = 'add';
                    $onClick = "onclick=\"manageDamObject('".$objectClass."',".$objectId.",'DAMObjectManagerInterface','import','".$version."')\"";
                    if ($update)
                    // Cas d'une mise à jour
                    { 
                        if ($version == $currentObject->sourceVersion)
                        // Version actuelle
                        {
                            $bgcolor = '#CCEECC';
                            $action = 'refresh';
                        }
                        elseif ($version > $currentObject->sourceVersion)
                        // Version antérieure
                        {
                            $bgcolor = '#CCCCCC';
                            $action = 'pixel';
                            $onClick = '';
                        }
                        else
                        // Version ultérieure
                        {
                            $bgcolor = '#FFFFFF';
                            $action = 'refresh';
                        }
                    }
                    $manager .= "  <div style='margin:5px'>";
                    $manager .= "    <table cellspacing='0' cellpadding='2' border='1' width='98%'>";
                    $manager .= "      <tr bgcolor='".$bgcolor."'>";
                    $manager .= "        <td width='20' align='center' valign='top'>";
                    $manager .= "           <img src='img/icons/nserver.gif' alt='' width='16' height='16' border='0' hspace='2'>";
                    $manager .= "        </td>";
                    $manager .= "        <td width='100%'>";
                    $manager .= "           <strong>";
                    $manager .= "               Version ".$version;
                    $manager .= "           </strong>";
                    $manager .= "           <br/>";
                    $manager .= "           <em>";
                    $manager .= "               Modifié le ".$root->getElementsByTagName("ModifiedAt")->item(0)->nodeValue." par ".$root->getElementsByTagName("ModifiedBy")->item(0)->nodeValue;
                    $manager .= "           </em>";  
                    $manager .= "        </td>";
                    $manager .= "        <td width='20' align='center' valign='top'>";
                    $manager .= "           <img src='img/".$action.".gif' alt='' width='16' height='16' border='0' hspace='2' ".$onClick." style='cursor:pointer'>";
                    $manager .= "        </td>";
                    $manager .= "      </tr>";
                    $manager .= "    </table>";
                    $manager .= "  </div>";
                }
            }
        break;
}
// Xml output
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
// Write ajax response
echo "<ajax-response>\n";
    echo "<response type=\"item\" id=\"".$messageId."Message\">";
        echo "<![CDATA[ ";
        echo "<table cellspacing='0' cellpadding='0' border='0' width='98%'>";
        echo "  <tr height='24'>";
        echo "    <td> <strong> ".$message." </strong>";
        echo "    </td>";
        echo "  </tr>";
        echo "</table>";
        echo "]]> ";
    echo "</response>\n";
    echo "<response type=\"item\" id=\"".$messageId."\">";
        echo "<![CDATA[ ";
        echo $manager;
        echo "]]> ";
    echo "</response>\n";
    echo "<response type=\"item\" id=\"trace\">";
    echo $trace;
    echo "</response>\n";
    echo "</ajax-response>";    
?>