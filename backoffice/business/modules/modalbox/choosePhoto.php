<?php
$session = wcmSession::getInstance();
$config = wcmConfig::getInstance();
$uniq_identifiant = getArrayParameter($_REQUEST, 'input', uniqid());

$url = $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.photoPath.php';
$acOptions = array('url' => $url,
				   'paramName' => 'prefix',
				   'parameters' => '');
echo '<div style="margin-left: 5px">';
if (isset($_SESSION['pathPhotoLogicImmo']))
	$pathPhoto = $_SESSION['pathPhotoLogicImmo'];
else
	$pathPhoto = $config['wcm.backOffice.photosPath'];
	
wcmGUI::renderAutoCompletedField($url, 'pathPhoto', $pathPhoto, _BIZ_FOLDER, array("style" => "width:85%; margin:5px"), $acOptions, true);
wcmGUI::renderHiddenField('uniq_identifiant', $uniq_identifiant);
echo '<img src="img/refresh.gif" style="cursor:pointer" onClick="updatePhotosList()" /><br />';
echo '<div id="photosList" style="overflow:auto; height:400px;">';
echo '</div><br />';
?>
 
<ul class="toolbar">
	<li><a href="#" onclick="closemodal(); return false;" class="cancel"><?php echo _BIZ_CANCEL;?></a></li>
</ul>
</div>
<script language="javascript" defer="defer">
	updatePhotosList();
</script>