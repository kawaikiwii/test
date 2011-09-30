<?php
/**
 * Project:     WCM
 * File:        mod_design.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    $bizobject = wcmMVC_Action::getContext();
    $project = wcmProject::getInstance();
    $widgetMode = wcmWidget::VIEW_FRAME;
    $locked = !$bizobject->isEditable();
    if (!$locked)
    {
        $widgetMode |= wcmWidget::VIEW_SETTINGS;
    }

    // Retrieve all templates
    $commonTemplatesPage = array_flip($project->generator->getTemplatesByPath('design/common/'));
    $objectTemplatesPage = array_flip($project->generator->getTemplatesByPath('design/'.$bizobject->getClass()));
    $templateList = array_merge($commonTemplatesPage, $objectTemplatesPage); 
	$objectTemplatesPage[] = "";
    
    echo '<script language="text/javascript"> modules = [';
    foreach($project->layout->getWidgets($bizobject) as $code => $title)
    {
        echo '{ code: "'.$code.'", title: "'.getConst($title).'", html: "" },';
    }
    echo ']; </script>';

    wcmGUI::openFieldset(_BIZ_DESIGN_TOOLBOX, array('class' => 'designToolbar'));
    wcmGUI::renderHiddenField('_widgetMode',$widgetMode, array('id' => '_widgetMode'));
    wcmGUI::renderHiddenField('_locked',$locked, array('id', '_locked'));

    wcmGUI::renderDropdownField('templateId', $templateList, $bizobject->templateId, _BIZ_WIDGET_TEMPLATE,
                    array('onchange' => "wcmDesign_UpdatePageContent(this.value);", 'id' => 'templateId'));

    wcmGUI::renderDropdownField('_wcmDesign_widgets', $objectTemplatesPage, null, _BIZ_WIDGET_ADD,
                    array('onchange' => "portal.addModule(this.value, $('_wcmDesign_zones').value); this.selectedIndex=0;", 'id' => '_wcmDesign_widgets'));

    wcmGUI::renderDropdownField('_wcmDesign_zones', array(), '', _BIZ_WIDGET_ZONE, array('id' => '_wcmDesign_zones'));

    wcmGUI::closeFieldset(); 
?>
<div id="_wcmDesign_Page">
    <div id="portal">
    </div>
</div>
