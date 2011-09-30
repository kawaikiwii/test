<?php
$session = wcmSession::getInstance();
$config = wcmConfig::getInstance();
?>
 
<iframe id="subForm" class="subFormIframe" src="<?php echo $config['wcm.backOffice.url']; ?>/ajax/wcm.module.php?module=business/editorial/photo/upload" scrolling="auto"></iframe>

<ul class="toolbar">
    <li><a href="#" onclick="closemodal(); return false;" class="cancel"><?php echo _BIZ_CANCEL;?></a></li>
</ul>