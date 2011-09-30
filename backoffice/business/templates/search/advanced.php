<?php
/**
 * Project:     WCM
 * File:        business/templates/search/advanced.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

$options = $context->options;
?>
<div style="margin:10px">
    <div class="header"><?php echo getArrayParameter($options, "title", _BIZ_OBJECTS_SEARCH); ?></div>
    <?php wcmModule('business/search/form', array($context)); ?>
</div>
