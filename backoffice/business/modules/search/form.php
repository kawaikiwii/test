<?php
/**
 * Project:     WCM
 * File:        business/modules/search/form.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

$config = wcmConfig::getInstance();
$searchConfig = wcmBizsearchConfig::getInstance();

$context = $params[0];          // from wcmModule() call
$formName = $context->name . ucfirst($context->pageType) . 'Form';
$sortedBy = $context->sortedBy;

// Always generate this form using the following values
$options = $context->options;
$options['_wcmAction'] = 'business/search';
$options['_wcmTodo'] = 'initSearch';
$options['paramPrefix'] = $searchConfig->getDefaultParameterPrefix();

wcmFormGUI::openForm($formName, $config['wcm.backoffice.url']);
    wcmFormGUI::renderHiddenField('search_sortedBy', $sortedBy);
    foreach ($options as $name => $value)
    {
        wcmFormGUI::renderHiddenField($name, $value);
    }
    ?>
    <div id="<?php echo $formName; ?>Header">
        <?php
        echo $searchConfig->designSearchForm($context->configId, $context->pageType,
                                             $context->params, $options);
        ?>
    </div>
<?php
wcmFormGUI::closeForm();