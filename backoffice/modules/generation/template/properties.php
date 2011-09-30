<?php
/**
 * Project:     WCM
 * File:        modules/generation/template/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $sysobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_TEMPLATE);
    wcmGUI::openFieldset( _GENERAL);
    wcmGUI::renderHiddenField('id', $sysobject->id);
    //wcmGUI::renderTextField('categoryId', $sysobject->categoryId, _CATEGORY);
    // @todo : selector
    //wcmGUI::renderHtmlDialogButton(_SELECT, 'categorySelector', 'field=categoryId');
    
    $url = $config['wcm.backOffice.url'] . 'ajax/autocomplete/wcm.templateCategories.php';
    $acOptions = array('url' => $url,
                       'paramName' => 'prefix',
                       'parameters' => '');
    wcmGUI::renderAutoCompletedField($url, 'categoryId', $sysobject->categoryId, _CATEGORY, null, $acOptions);
    wcmGUI::renderTextField('name', $sysobject->name, _NAME . ' *', array('class' => 'type-req'));
    wcmGUI::renderTextArea('content', $sysobject->content, _CONTENT, array('id' => 'diskcontent', 'rows' => 12));
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    echo '</div>';

    /**
     * Render HTML options representing the categories tree
     * @todo : correct this
     */
    function renderCategoriesOptions($parentId=null, $selectedValue=null, $prefix=null)
    {       
        $session = wcmSession::getInstance();

        foreach(wcmProject::getInstance()->generator->getCategories() as $category)
        {
            if ($category->parentId == $parentId)
            {
                echo "<option value='".$category->id."'";
                if ($category->id == $selectedValue) echo " selected='selected'";
                echo ">" . $prefix . $category->name . "</option>";
                self::renderCategoriesOptions($category->id, $selectedValue, $category->name . $prefix . " :: ");
            }
        }
    }
