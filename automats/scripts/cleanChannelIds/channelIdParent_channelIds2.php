<?php 
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');
$link = mysql_connect("10.23.65.201","relaxweb","kzq!2007") or die("Connexion impossible");
$db_selected = mysql_select_db('RELAX_BIZ', $link);
//$id = 599215;
//$query = "SELECT channelids FROM biz_news WHERE id=".$id;
$cpt = 0;
$query = "SELECT id,channelids FROM biz_news WHERE (siteid=4 OR siteid=5 OR siteid=6) AND channelids IS NOT NULL AND channelids<>''";
$result = mysql_query($query,$link);
while ($row = mysql_fetch_array($result)) {
	$afficher = false;
	$id = $row["id"];
	$channelids = $row["channelids"];
	if(!empty($channelids))
	{
		$obj = new news();
    	$obj->refresh($id);
		$channelids_array = unserialize($channelids);
		$new_channelids = $channelids_array;
		foreach($channelids_array as $channelid) {
			$query2 = "SELECT id,parentid FROM biz_channel WHERE id=".$channelid;
			$result2 = mysql_query($query2,$link);
			if ($row2 = mysql_fetch_array($result2)) {
				$parentid = $row2["parentid"];
				if($parentid != NULL && array_search($parentid,$new_channelids) === false)
				{
					$afficher = true;
					array_push($new_channelids,$parentid);
			    	$pilier = $obj->getPilier($parentid);
			    	if($pilier != NULL && array_search($pilier,$new_channelids) === false)
						array_push($new_channelids,$pilier);
				}
			}
		}
		$new_channelids = serialize($new_channelids);
		if($afficher)
		{
			$obj->channelIds = $new_channelids;
	    	$obj->save();
	    	unset($obj);
			$cpt++;
			//echo $id." : ".$channelids." : ".$new_channelids."<br />";
			if($cpt > 100)
				break;
		}
	}
}
//echo $cpt;
?>
