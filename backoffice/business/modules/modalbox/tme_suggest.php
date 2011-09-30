<?php

$method = $params['method'];
$type = $params['type'];
$targetid = $params['targetid'];

$config = wcmConfig::getInstance();
?>


<div id="suggested_results">

	<div class="wait"><?php echo _LOADING; ?></div>

</div>


<script>
	new Ajax.Updater('suggested_results', '<?php echo $config['wcm.backOffice.url']?>business/ajax/biz.tme_suggest.php', {method: 'get',parameters: {method: '<?php echo $method;?>', type: '<?php echo $type;?>', targetid: '<?php echo $targetid;?>'}});
</script>