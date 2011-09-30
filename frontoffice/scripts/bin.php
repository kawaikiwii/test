<?php
set_time_limit(120);
// Initialize WCM API
require_once (dirname( __FILE__ ).'/../inc/wcmInit.php');
if (!($session->userId))
{
    header("location: /");
    exit ();
}
$site = $session->getSite();
$session->setLanguage($site->language);
// Retrieve FO Language Pack
require_once (dirname(__FILE__)."/../sites/".$site->code."/conf/lang.php");

$action = ( isset ($_REQUEST["action"]))?strtolower($_REQUEST["action"]):
    "";
    $binControl = new binControl();
   
    switch($action)
    {
        case "create":
            $binControl->create($_REQUEST["binName"]);
            echo ("bin has been created");
            break;
        case "remove":
            $binControl->remove($_REQUEST["binId"]);
            echo ("bin has been removed");
            break;
        case "getdata":
            $aResults = $binControl->getBinData($_REQUEST["binId"]);
            echo ($aResults);
            break;
        case "removefrom":
            $binControl->removeFromBin($_REQUEST["binId"], $_REQUEST["items"]);
            echo ($_REQUEST["items"]." has been removed from bin ".$_REQUEST["binId"]);
            break;
        case "clear":
            $binControl->clear($_REQUEST["binId"]);
            echo ($_REQUEST["binId"]." has been cleared");
            break;
        case "addto":
            $binControl->addToBin($_REQUEST["binId"], $_REQUEST["items"]);
            echo ($_REQUEST["items"]." has been added to bin ".$_REQUEST["binId"]);
            break;
        case "getbinsmenu":
            $aResults = $binControl->getUserBinsMenu($_REQUEST["cmpId"]);
            echo (json_encode($aResults)."\n");
			break;
		default:
?>
<ul class="ari-sidebar-list">
		<?php 
			$binControl = new binControl();
			foreach ($binControl->getUserBins() as $bin) { 
		?>
	    <li class="ari-bin" id="bin-<?php echo $bin['id']?>"><a href="#" class="ari-bin-delete" onclick="ARe.bin.remove(<?php echo $bin['id']?>)">X</a><a href="#" class="ari-bin-name" onclick="ARe.bin.open(this, <?php echo $bin['id']?>)"><?php echo $bin['name'] ?></a></li>
		<?php 
			} 
		?>
	</ul>
<?php
    }
?>
