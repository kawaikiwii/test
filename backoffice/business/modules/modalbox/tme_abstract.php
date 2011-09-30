<?php

$input = strip_tags($params['input']);
$targetid = $params['targetid'];

$tme = wcmSemanticServer::getInstance();
$methods = array('NSummarizer');

$sdata = $tme->mineText($input, "owi92", wcmSession::getInstance()->getLanguage(), $methods);
?>
<textarea id="summary" cols="44" rows="8">
	<?php echo $sdata->summary;?>
</textarea>

<ul class="toolbar">
	<li><a href="#" onclick="closemodal(); return false;" class="cancel"><?php echo _BIZ_CANCEL;?></a></li>
	<li><a href= "#" onclick="tinyMCE.get('<?php echo $targetid;?>').setContent(document.getElementById('summary').value); closemodal(); return false;" class="save"><?php echo _BIZ_REPLACE;?></a></li>
</ul>