<?php 
require_once (dirname(__FILE__).'/inc/wcmInit.php');



if ( isset ($_GET['id']) && isset ($_GET['format']))
{
    $id = $_GET['id'];
    $format = $_GET['format'];

	$photo = new photo();
	$photo->refresh($id);
	
	$filename = $config["wcm.webSite.repository"].$photo->getPhotoRelativePathByFormat($format);
	
    header("Content-Type : application/octet-stream");
    header("Content-disposition: attachment; filename=".basename($filename));
    readfile($filename);
	
	unset($photo);
    
}
?>
