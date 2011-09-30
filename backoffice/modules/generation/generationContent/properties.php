<?php
/**
 * Project:     WCM
 * File:        modules/generation/generationContent/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    $project = wcmProject::getInstance();
    $sysobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    
    $info = null;
    // Special actions
    if ($sysobject->id)
    {
        $path = $project->generator->getGenerationById($sysobject->generationId)->generationSetId;
        $path .= ':' . $sysobject->generationId.':'.$sysobject->id; 
        // prepare sub menues
        $info = '';
        $info .= '<ul class="actions">';
        $info .= '<li><a href="'. wcmDialogUrl('generate', 'rule='.$path);
        $info .= '">' . _EXECUTE . '</a></li>';
        $info .= '</ul>';
    }

    // Retrieve available generation
    $generations = array();
    foreach ($project->generator->getGenerations() as $generation)
    {
        // Retrieve generationSet
        $genSet = $project->generator->getGenerationSetById($generation->generationSetId);        
        $generations[$generation->id] = ($genSet) ? getConst($genSet->name) . ' :: ' . getConst($generation->name) : getConst($generation->name);
    }
    
    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_GENERAL, true, $info);
    wcmGUI::openFieldset(_PROPERTIES);
    wcmGUI::renderDropdownField('generationId', $generations, $sysobject->generationId, _GENERATION);
    wcmGUI::renderTextField('name', $sysobject->name, _NAME . ' *', array('class' => 'type-req'));
    wcmGUI::renderTextField('code', $sysobject->code, _CODE . ' *', array('class' => 'type-code-req'));
        
    $url = $config['wcm.backOffice.url'] . 'ajax/autocomplete/wcm.templatelist.php';
    $acOptions = array('url' => $url,
                       'paramName' => 'prefix',
                       'parameters' => '');
    wcmGUI::renderAutoCompletedField($url, 'templateId', $sysobject->templateId, _TEMPLATE. ' *', array('class' => 'type-req'), $acOptions);
    
    wcmGUI::renderTextField('loop', $sysobject->loop, _LOOP);
    wcmGUI::renderTextField('context', $sysobject->context, _CONTEXT);
    wcmGUI::renderTextField('namingRule', $sysobject->namingRule, _NAMING_RULE . ' *', array('class' => 'type-req'));
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    echo '</div>';
