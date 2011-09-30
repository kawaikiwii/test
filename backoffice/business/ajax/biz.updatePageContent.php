<?php
/**
 * Project:     WCM
 * File:        biz.updatePageContent.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Retrieve parameters
$bizobject = wcmMVC_Action::getContext();
$elementId = getArrayParameter($_REQUEST, 'elementId', null);
$templateId = getArrayParameter($_REQUEST, 'templateId', $bizobject->templateId);

$widgetMode = wcmWidget::VIEW_FRAME;
if ($bizobject->isEditable())
{
    $widgetMode |= wcmWidget::VIEW_SETTINGS;
}

// XML output (for AJAX response)
header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?><ajax-response><response type="item" id="'.$elementId.'">';

// Execute template
$generator = new wcmTemplateGenerator(null, false, $widgetMode);
echo $generator->executeTemplate($templateId, array('widgetMode' => $widgetMode,
                                                    'obizobject' => $bizobject,
                                                    'bizobject' => $bizobject->getAssocArray(false)));

echo '</response></ajax-response>';
