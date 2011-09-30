<?php
/**
 * Project:     WCM
 * File:        modules/subForms/subForm.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Generic module use to include a sub-module in an IFRAME
include(WCM_DIR.'/initWebApp.php');
include(WCM_DIR.'/pages/includes/head.php');
$config = wcmConfig::getInstance();
$session = wcmSession::getInstance();

$subform = wcmModuleURL($params['module'], $params['options']);
?>
<div id="subForm_iFrameContainer" class="subFormIframeContainer">
<iframe id="subForm" class="subFormIframe" src="<?php echo $subform;?>" style="width: 99%; height: 660px" scrolling="auto"></iframe>
</div>
<script type="text/javascript">
iFrameCover = function(argText)
{
    iFrameLoader = new Element('div');
    iFrameLoader.id = 'iframeLoader';
    iFrameLoader.addClassName('iframeLoader');
    iFrameLoader.setStyle({
        position: 'absolute', 
        backgroundColor: '#fff',
        top: $('subForm').offsetTop + 'px',
        left: $('subForm').offsetLeft + 'px',
        height: $('subForm').offsetHeight + 'px',
        width: $('subForm').offsetWidth + 'px'
    });
    iFrameLoader.update('<div class="wait">' + argText + '</div>');
    $('subForm_iFrameContainer').appendChild(iFrameLoader);
}

iFrameCover($I18N.LOADING);

$('subForm').observe('load', function() {
    $('iframeLoader').remove();
});
</script>
