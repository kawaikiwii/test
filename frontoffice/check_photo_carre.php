<?php
require_once (dirname(__FILE__).'/inc/wcmInit.php');
set_time_limit(180);
$dir = "../repository/";
$nb_photos = 0;
$list_id = "";

clearstatcache();

$connect = new PDO("mysql:host=10.23.65.201; dbname=RELAX_BIZ", "relaxweb", "kzq!2007");
foreach($connect->query("SELECT id,permalinks FROM biz_photo WHERE permalinks LIKE '%.jpg'") as $row) {
	$id_photo   = $row["id"];
	$permalinks = str_replace("%format%","s200",$row["permalinks"]);
	if(!is_file($dir.$permalinks)) {
		$nb_photos++;
		/*$photo = new photo(null,$id_photo);
		$date = (isset($photo->createdAt)) ? $photo->createdAt : date('Y-m-d h:i:s');
		$creationDate = dateOptionsProvider::fieldDateToArray($date);
		$publicationPath = 'illustration/photo/'.$creationDate['year'].'/'.$creationDate['month'].'/'.$creationDate['day'].'/';
		$dir2 = $config['wcm.webSite.repository'].$publicationPath;
		$infos = $photo->processImage($dir2, $publicationPath, $photo->original);
		echo $id_photo." ".$permalinks."<br />";
		unset($photo);*/
	}
	/*if($nb_photos > 1)
		break;*/
}
echo $nb_photos." photos modifies<br />";
?>