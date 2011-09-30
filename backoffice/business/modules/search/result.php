<?php
/**
 * Project:     WCM
 * File:        business/modules/search/result.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

$searchConfig = wcmBizsearchConfig::getInstance();

$context = $params[0];          // from wcmModule() call
$resultName = $context->name . ucfirst($context->pageType) . 'Result';
?>
<div id="<?php echo $resultName; ?>">
    <?php
    $view = $searchConfig->getView($context->configId, $context->pageType, $context->viewName);
    $templateId = 'search/views/' . $view->template;

    $templateGenerator = new wcmTemplateGenerator(wcmProject::getInstance());
    echo $templateGenerator->executeTemplate($templateId, array(
                                                 'searchConfig' => $searchConfig,
                                                 'searchContext' => $context,
                                                 ));
    ?>

</div>
