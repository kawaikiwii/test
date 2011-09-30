<?php
/**
 * Project:     WCM
 * File:        business/templates/search/3-columns.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

$config = wcmConfig::getInstance();
$searchConfig = wcmBizsearchConfig::getInstance();

$view = $searchConfig->getView($context->configId, $context->pageType, $context->viewName);
$css = $config['wcm.backOffice.url'].$view->css;
?>
<div>
    <link rel="stylesheet" type="text/css" href="<?php echo $css; ?>" />
    <table width="100%">
        <tr>
            <td valign="top">
                <?php wcmModule('business/search/resultPage/leftColumn', array($context)); ?>
            </td>
            <td valign="top" width="100%">
                <?php wcmModule('business/search/resultPage/middleColumn', array($context)); ?>
            </td>
            <td valign="top">
                <?php wcmModule('business/search/resultPage/rightColumn', array($context)); ?>
            </td>
        </tr>
    </table>
</div>
