<?php
require_once (dirname(__FILE__).'/inc/wcmInit.php');
set_time_limit(180);
if (!(isset($session->userId) && $session->userId)) {
    exit();
}

$dir = "../repository/";
$nb_news = 0;
$nb_photos = 0;
$list_id = "";

clearstatcache();

$connect = new PDO("mysql:host=10.23.65.201; dbname=RELAX_BIZ", "relaxweb", "kzq!2007");
foreach($connect->query("SELECT id,permalinks FROM biz_photo WHERE permalinks LIKE '%.jpg'") as $row) {
	$id_photo   = $row["id"];
	$permalinks = str_replace("%format%","original",$row["permalinks"]);
	if(!is_file($dir.$permalinks)) {
		$nb_photos++;
		if($nb_photos <= 200) {
			foreach($connect->query("SELECT sourceClass,sourceId FROM biz__relation WHERE destinationClass='photo' AND destinationId=".$id_photo) as $row2) {
				$sourceClass = $row2["sourceClass"];
				$sourceId    = $row2["sourceId"];
				$photo = new photo(null,$id_photo);
				$item = new $sourceClass(null,$sourceId);
				$photo->delete();
				$item->generate(false, null, true);
				echo $id_photo." : ".$permalinks." : ".$sourceClass." : ".$sourceId."<br />";
				$nb_news++;
				unset($item);
				unset($photo);
			}
		}
	}
}
echo $nb_news." items modifies, encore ".$nb_photos." photos<br />";
?>