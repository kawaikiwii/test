<?php
require_once (dirname(__FILE__).'/inc/wcmInit.php');
set_time_limit(0);
if (!(isset($session->userId) && $session->userId)) {
    exit();
}
try {
	$rep_temp = "../../_MIGRATION_/ARCHIVES/news/all/";
	
	$connect = new PDO("mysql:host=10.23.65.201; dbname=RELAX_BIZ", "relaxweb", "kzq!2007");
	$tab_channel = array();
	foreach($connect->query("SELECT min(id) as id,sourceId FROM biz_channel WHERE siteId=6 GROUP BY sourceId ORDER BY sourceId") as $row) {
		$tab_channel[$row["sourceId"]] = $row["id"];
	}
	$nb_nofound = 0;
	$nb_modif = 0;
	$nb_supp = 0;
	foreach($connect->query("SELECT id,import_feed_id FROM `biz_news` WHERE import_feed='icm' AND (channelId='' OR channelId IS NULL) LIMIT 0,200") as $row) {
		$id             = $row["id"];
		$import_feed_id = "news_".$row["import_feed_id"];
		$news = new news(null,$id);
		if(is_file($rep_temp.$import_feed_id)) {
			$rss_file = file_get_contents($rep_temp.$import_feed_id);
			$xml = new SimpleXMLElement($rss_file);
			$old_channel = "";
			foreach($xml->head->categories->category as $category) {
				if((string) $category["main"] == true) {
					$old_channel = (string) $category["id"];
					break;
				}
			}
			if(!empty($old_channel) && array_key_exists($old_channel,$tab_channel)) {
				//echo "UPDATE biz_news SET channelId=".$tab_channel[$old_channel].",channelIds='a:1:{i:0;".serialize($tab_channel[$old_channel])."}' FROM fruit WHERE id=?";
				$update = $connect->prepare("UPDATE biz_news SET channelId=?,channelIds=? WHERE id=?");
				$update->bindParam(1,$tab_channel[$old_channel],PDO::PARAM_INT);
				$channelids = "a:1:{i:0;".serialize($tab_channel[$old_channel])."}";
				$update->bindParam(2,$channelids,PDO::PARAM_STR,50);
				$update->bindParam(3,$id,PDO::PARAM_INT);
				$update->execute();
				$news->generate(false, null, true);
				echo $id." modifie ".$import_feed_id." ".$tab_channel[$old_channel]."<br />";
				$nb_modif++;
			}
			else {
				$news->delete();
                echo $id." supprime ".$import_feed_id."<br />";
				$nb_supp++;
			}
			$xml = null;
		}
		else {
			$news->delete();
			echo $id." non trouve ".$import_feed_id."<br />";
			$nb_nofound++;
		}
		unset($news);
	}
	echo "Modifs : ".$nb_modif." suppression : ".$nb_supp." non trouve : ".$nb_nofound;
	$connect = null;
}
catch(PDOException $e) {
	echo("Connexion à la base de données RELAX_BIZ impossible : ".$e->getMessage()."\r\n");
	$connect = null;
}
?>