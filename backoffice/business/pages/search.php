<?php
/**
 * Project:     WCM
 * File:        business/pages/search.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Execute the action
wcmMVC_Action::execute('business/search', $_REQUEST);
$context = wcmMVC_Action::getContext();

// Include the page header
include(WCM_DIR . '/pages/includes/header.php');

// Design and render the search page
$searchConfig = wcmBizsearchConfig::getInstance();

$className = $context->query;//ucfirst(substr($context->query, strpos($context->query, ':')+1));

echo $searchConfig->designSearchPage($context->configId, $context->pageType, $context);

// Include the page footer
include(WCM_DIR . '/pages/includes/footer.php');
?>