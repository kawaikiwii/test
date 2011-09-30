<?php
	$htmllist = getArrayParameter($params, 'list', '');
	if ($htmllist)
    	echo $htmllist;
    else
        echo _BIZ_SITE_NO_RESULT;	
?>
<script>

</script>

<ul class="toolbar">
	<li><a href="#" onclick="closemodal(); return false;" class="cancel"><?php echo _BIZ_CANCEL;?></a></li>
</ul>
