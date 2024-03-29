﻿<?php
/**
 * Project:     WCM
 * File:        wcm.formGUI.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This class contains helper to render
 * the graphical user interface
 *
 * Important notes:
 *      This class has been designed to create DHTML form containing fieldsets
 *      The class assume that each fields will be rendered as list item (using ul/li tags)
 *      Therefore:
 *      - wcmGUI::openFieldset() will create both 'fieldset' and 'ul' tags
 *      - wcmGUI::render{xxx}Field() will use 'li' and optionally 'label' tags
 *      - wcmGUI::renderHiddenField() is the only exception
 */
class wcmFormGUI
{
    /**
     * Renders HTML code corresponding to a tag that may contains HTML content
     *
     * @param string $name       Tag name
     * @param array  $attributes Optional assoc array of tag's attributes
     * @param string $content    Optional HTML content
     */
    static function renderTag($name, $attributes = null, $content = null)
    {
        if ($content)
        {
            self::renderOpenTag($name, $attributes);
            echo $content."</$name>";
        }
        else
        {
            self::renderSingleTag($name, $attributes);
        }
    }

    /**
     * Renders HTML code corresponding to a single (auto-closed) tag
     *
     * @param string $name       Tag name
     * @param array  $attributes Optional assoc array of tag's attributes
     */
    static function renderSingleTag($name, $attributes = null)
    {
        echo '<'.$name;
        self::renderAttributes($attributes);
        echo '/>';
    }

    /**
     * Renders HTML code corresponding to a tag opening
     *
     * @param string $name       Tag name
     * @param array  $attributes Optional assoc array of tag's attributes
     */
    static function renderOpenTag($name, $attributes = null)
    {
        echo '<'.$name;
        self::renderAttributes($attributes);
        echo '>'.PHP_EOL;
    }

    /**
     * Renders HTML code corresponding to an array of DHTML attributes
     * Note: array values must be string, numeric or boolean
     *
     * @param array  $attributes Optional assoc array of DHTML attributes
     */
    static function renderAttributes($attributes = null)
    {
        if (is_array($attributes))
        {
            foreach($attributes as $name => $value)
            {
                echo ' '.$name.'="'.textH8($value).'"';
            }
        }
    }

    /**
     * Returns Javascript array (e.g. '[ val1, val2, ....]')
     * Note: array values must be string, numeric or boolean
     *
     * @param array $options Array containing options
     *
     * @return string Javascript corresponding to array
     */
    static function getJavascriptArray($options)
    {
        $js = null;
        if (is_array($options))
        {
            foreach($options as $val)
            {
                if ($js) $js .= ',';
                $js .= (is_bool($val)) ? (($val) ? 'true' : 'false') : $val;
            }
        }

        return ($js) ? '{'.$js.'}' : 'null';
    }

    /**
     * Returns Javascript options (e.g. '{ opt1: true, opt2:123, opt3:'string', ....}')
     *
     * @param array $options Assoc array containing options
     *
     * @return string Javascript corresponding to assoc array
     */
    static function getJavascriptOptions($options)
    {
        $js = null;
        if (is_array($options))
        {
            foreach($options as $option => $val)
            {
                if ($js) $js .= ',';
                $js .= "$option:";
                if (is_bool($val))
                {
                    $js .= ($val) ? 'true' : 'false';
                }
                elseif (is_numeric($val))
                {
                    $js .= $val;
                }
                else
                {
                    $js .= '\'' . $val . '\'';
                }
            }
        }

        return ($js) ? '{'.$js.'}' : '{}';
    }

    /**
     * Renders HTML code to corresponding to a simple ul/li list
     *
     * @param array  $items       Array of string (items to render)
     * @param array  $attributes    Optional assoc array for form attributes
     */
    static function renderUList(array $items, $attributes = null)
    {
        self::renderOpenTag('ul', $attributes);
        foreach($items as $item)
        {
            self::renderTag('li', null, $item);
        }
        echo '</ul>';
    }

    /**
     * Renders HTML code to corresponding to a simple ol/li list
     *
     * @param array  $items       Array of string (items to render)
     * @param array  $attributes    Optional assoc array for form attributes
     */
    static function renderOList(array $items, $attributes = null)
    {
        self::renderOpenTag('ol', $attributes);
        foreach($items as $item)
        {
            self::renderTag('li', null, $item);
        }
        echo '</ol>';
    }

    /**
     * Renders HTML code to open a form
     *
     * @param string $name          Form name ('wcmForm' by default)
     * @param string $action        Form action ('?' by default)
     * @param array  $hiddenFields  Optional assoc array of hidden fields (none by default)
     * @param array  $attributes    Optional assoc array for form attributes
     */
    static function openForm($name = 'wcmForm', $action = '?', $hiddenFields = null, $attributes = null)
    {
        if (!is_array($attributes)) $attributes = array();
        $attributes['name'] = $attributes['id'] = $name;
        if (!isset($attributes['method']))
            $attributes['method'] = 'post';
        if (!isset($attributes['action']))
            $attributes['action'] = $action;

        self::renderOpenTag('form', $attributes);
        if (is_array($hiddenFields))
        {
            foreach($hiddenFields as $id => $value)
            {
                self::renderSingleTag('input', array('type' => 'hidden', 'id' => $id, 'name' => $id, 'value' => $value));
            }
        }
    }

    /**
     * Renders HTML code to open a fieldset
     *
     * @param string $legend Optional legend
     * @param array $attributes Optional assoc array for fieldset's attributes
     */
    static function openFieldset($legend = null, $attributes = null)
    {
        self::renderOpenTag('fieldset', $attributes);
        if ($legend)
        {
            echo '<legend>' . $legend.'</legend>'.PHP_EOL;
        }
        echo '<ul>';
    }

    /**
     * Renders HTML code to open a collapsable fieldset
     *
     * @param string $id    mandatory id for enabling collapse
     * @param string $legend Optional legend
     * @param array $attributes Optional assoc array for fieldset's attributes
     */
    static function openCollapsableFieldset($legend = null, $attributes = null)
    {
        self::renderOpenTag('fieldset', $attributes);
        if ($legend)
        {
            echo '<legend class="collapsable" onclick="this.siblings().shift().toggle();">';
            echo $legend.'</legend>'.PHP_EOL;
        }
        echo '<ul>';
    }

    /**
     * Renders HTML code to close a tag
     *
     * @param string $name Name of tag to close
     */
    static function renderCloseTag($name)
    {
        echo '</' . $name . '>'.PHP_EOL;
    }

    /**
     * Renders HTML code to close a fieldset
     */
    static function closeFieldset()
    {
        echo '</ul>';
        echo '</fieldset>'.PHP_EOL;
    }

    /**
     * Renders HTML code to close a form
     */
    static function closeForm()
    {
        echo '</form>'.PHP_EOL;
    }

    /**
     * Renders HTML code to display a text field
     *
     * @param string $name       Field name
     * @param string $value      Optional field value
     * @param string $label      Optional field label
     * @param array  $attributes Optional assoc array of DHTML attributes
     */
    static function renderTextField($name, $value = null, $label = null, $attributes = null)
    {
        echo '<li>';

        if ($label) echo '<label>'.textH8($label).'</label>';

        if (!is_array($attributes)) $attributes = array();
        $attributes['type'] = 'text';
        $attributes['name'] = $name;
        $attributes['value'] = $value;
        if (!isset($attributes['id'])) $attributes['id'] = $name;

        self::renderSingleTag('input', $attributes);

        echo '</li>';
    }
    
    /**
     * Renders HTML code to display a file field
     *
     * @param string $name       Field name
     * @param string $label      Optional field label
     * @param array  $attributes Optional assoc array of DHTML attributes
     */    
    static function renderFileField($name, $label = null, $attributes = null)
    {
        echo '<li>';
        
        if ($label) echo '<label>'.textH8($label).'</label>';
        
        if (!is_array($attributes)) $attributes = array();
        $attributes['type'] = 'file';
        $attributes['name'] = $name;
        
        self::renderSingleTag('input', $attributes);
        
        echo '</li>';
    }

    /**
     * Renders HTML code to display a text area
     *
     * @param string $name       Field name
     * @param string $value      Optional field value
     * @param string $label      Optional field label
     * @param array  $attributes Optional assoc array of DHTML attributes
     */
    static function renderTextArea($name, $value = null, $label = null, $attributes = null)
    {
        echo '<li>';

        if ($label) echo '<label>'.textH8($label).'</label>';

        if (!is_array($attributes)) $attributes = array();
        $attributes['name'] = $name;
        if (!isset($attributes['id'])) $attributes['id'] = $name;

        self::renderOpenTag('textarea', $attributes);
        echo textH8($value);
        echo self::renderCloseTag('textarea');

        echo '</li>';
    }

    /**
     * Renders HTML code to display a password field
     *
     * @param string $name       Field name
     * @param string $value      Optional field value
     * @param string $label      Optional field label
     * @param array  $attributes Optional assoc array of DHTML attributes
     */
    static function renderPasswordField($name, $value = null, $label = null, $attributes = null)
    {
        echo '<li>';

        if ($label) echo '<label>'.textH8($label).'</label>';

        if (!is_array($attributes)) $attributes = array();
        $attributes['type'] = 'password';
        $attributes['name'] = $name;
        $attributes['value'] = $value;

        self::renderSingleTag('input', $attributes);

        echo '</li>';
    }

    /**
     * Renders HTML code to display a hidden field
     *
     * @param string $name       Field name
     * @param string $value      Optional field value
     */
    static function renderHiddenField($name, $value = null, $attributes = null)
    {
        if (!is_array($attributes)) $attributes = array();
        if (!isset($attributes['id'])) $attributes['id'] = $name;
        $attributes['type'] = 'hidden';
        $attributes['name'] = $name;
        $attributes['value'] = $value;
        self::renderSingleTag('input', $attributes);
    }

    /**
     * Renders HTML code to display a submit button
     *
     * @param string $name       Field name
     * @param string $value      Optional field value
     */
    static function renderSubmitButton($name, $value = null, $attributes = null)
    {
        if (!is_array($attributes)) $attributes = array();
        $attributes['type'] = 'submit';
        $attributes['name'] = $name;
        $attributes['value'] = $value;

        self::renderSingleTag('input', $attributes);
    }

    /**
     * Renders HTML code to display a reset button
     *
     * @param string $name       Field name
     * @param string $value      Optional field value
     */
    static function renderResetButton($name, $value = null, $attributes = null)
    {
        if (!is_array($attributes)) $attributes = array();
        $attributes['type'] = 'reset';
        $attributes['name'] = $name;
        $attributes['value'] = $value;

        self::renderSingleTag('input', $attributes);
    }

    /**
     * Renders HTML code to display a generic XHTML button
     *
     * @param string $name       Field name
     * @param string $value      Value cannot be null for an XHTML button
     * @param array  $attributes
     */
    static function renderButton($name, $value, $attributes = null)
    {
        if (!is_array($attributes)) $attributes = array();
        self::renderOpenTag('li');
        self::renderOpenTag('button', $attributes);
        echo $value;
        self::renderCloseTag('button');
        self::renderCloseTag('li');
    }
    
    /**
     * Renders HTML code to display a boolean field
     *
     * @param string $name       Field name
     * @param bool   $value      Optional field value
     * @param string $label      Optional field label
     * @param array  $attributes Optional assoc array of DHTML attributes
     */
    static function renderBooleanField($name, $value = false, $label = null, $attributes = null)
    {
        echo '<li class="boolean">';
        self::renderHiddenField($name, ($value) ? '1' : '0', array('id' => $name));
        echo '<input type="checkbox"';
        if ($value) echo ' checked="checked"';
        echo ' onclick="$(\''.$name.'\').value=(this.checked)?\'1\':\'0\';"';
        echo 'name="_wcmBox' . $name . '" id="_wcmBox' . $name . '"';
        self::renderAttributes($attributes);
        echo '/>';
        if ($label) echo '<label class="checkbox" for="_wcmBox' . $name . '">'.textH8($label).'</label>';
        echo '</li>';
    }

    /**
     * Renders HTML code to display a date field
     *
     * @param $name     Field name
     * @param $value    Field value: a parsable date (or null for today)
     * @param $mode     Date mode: either 'date' or 'datetime' ('date' by default)
     * @param $callback Javascript callback (or null for default behaviour)
     */
    static function renderDateField($name, $value = null, $label = null, $mode = 'date', $callback = null)
    {
        echo '<li>';
        if ($label) echo '<label>'.textH8($label).'</label>';
        $calendar = new wcmHtmlCalendar();
        echo $calendar->render($name, $value, $mode, $callback);
        echo '</li>';
    }

    /**
     * Renders HTML code to display a select field
     *
     * @param string $name       Field name
     * @param array  $options    Array of options (value => label)
     * @param string $value      Optional selected value
     * @param string $label      Optional field label
     * @param array  $attributes Optional assoc array of DHTML attributes
     */
    static function renderDropdownField($name, array $options, $value = null, $label = null, $attributes = null)
    {
        echo '<li>';

        if ($label) echo '<label>'.textH8($label).'</label>';

        if (!is_array($attributes)) $attributes = array();
        
        if (!isset($attributes['id'])) $attributes['id'] = $name;
        $attributes['name'] = $name;

        echo '<select';
        self::renderAttributes($attributes);
        echo '>';
        foreach($options as $val => $label)
        {
            echo '<option value="'.textH8($val).'"';
            if ($val == $value) echo ' selected="selected"';
            echo '>'.textH8($label).'</option>'.PHP_EOL;
        }
        echo '</select>';
        echo '</li>';
    }

    /**
     * Renders a DHTML editable field (tinyMCE)
     *
     * @param string $name        Field name
     * @param string $value       Optional selected value
     * @param string $label       Optional field label
     * @param array  $attributes  Optional assoc array of DHTML attributes
     * @param array  $editOptions Optional assoc array of tinyMCE options (e.g. 'mode', 'width', 'height', 'theme'...)
     */
    static function renderEditableField($name, $value = null, $label = null, $attributes = null, $editOptions = null)
    {
        echo '<li>';
        if ($label) echo '<label>'.textH8($label).'</label>';
        if (!is_array($attributes)) $attributes = array();
        $attributes['name'] = $name;
        if (!isset($attributes['id'])) $attributes['id'] = $name;

        echo self::renderOpenTag('textarea', $attributes);
        echo textH8($value);
        echo '</textarea>';
        echo '</li>';

// MBUL : for google translation plugin
        echo '<input type="hidden" id="sLang" value="en"><input type="hidden" id="dLang" value="fr">';


        // Set default options for tinyMCE editor
        $options = array();
        $options['plugins'] = 'spellchecker,style,table,searchreplace,print,paste,google_translations';
//		$options['setup'] = 'function(ed){ed.onPreProcess.add(function(ed, o){alert("toto");});}';
        $options['auto_reset_designmode'] = true;
        $options['elements'] = $attributes['id'];
        $options['entities'] = '160,nbsp';
        $options['entity_encoding'] = 'named';
        $options['language'] = $_SESSION['wcmSession']->getSite()->language;
        $options['valid_elements'] = 'hr,p,br,a[href|target:_blank],strong/b,em/i,sub,sup,table,tr,td[colspan|rowspan|align|style],th[colspan|rowspan|align|style],h1,h2,h3,ul,ol,li';
        $options['cleanup_on_startup'] = true;
		$options['paste_auto_cleanup_on_paste'] = true;
        $options['invalid_elements'] = 'xml,w:WordDocument,!-,!--';
        $options['mode'] = 'exact';
        $options['theme'] = 'advanced';
        $options['apply_source_formatting'] = true;
//        $options['debug '] = true;
        $options['theme_advanced_buttons1'] = 'cut,copy,pasteword,selectall,undo,redo,|,search,replace,|,cleanup,code';
        $options['theme_advanced_buttons2'] = 'bold,italic,underline,|,hr,bullist,numlist,|,link,anchor,|,spellchecker,google_translations';
        $options['theme_advanced_buttons3'] = 'tablecontrols';
        $options['theme_advanced_layout_manager'] = 'SimpleLayout';
        $options['theme_advanced_path'] = false;
        $options['theme_advanced_resize_horizontal'] = false;
        $options['theme_advanced_resizing'] = true;
        $options['theme_advanced_statusbar_location'] = 'bottom';
        $options['theme_advanced_toolbar_location'] = 'top';
        $options['theme_advanced_toolbar_align'] = 'left';
        $options['theme_advanced_font_sizes'] = '7';
        $options['width'] = 564;
        $options['height'] = 400;

/*$myLG = $options['language'];
        echo '<script type="text/javascript">';
        echo "alert(' OK - $myLG');";
        echo '</script>';
        exit();*/

        // Override editor options
        if (is_array($editOptions))
        {
            foreach($editOptions as $option => $val)
            {
                $options[$option] = $val;
            }
        }

	

		$options = self::getJavascriptOptions($options);

//print_r($options);
//exit();
		
		echo '<script type="text/javascript">';	
		//echo 'function trimSaveContent(element_id, html, body) { html = html.replace(/<!--.*?-->/g,""); return html; }';
        echo 'tinyMCE.init(';
        //echo 'save_callback : \'trimSaveContent\',';
        echo substr($options, 0, strlen($options)-1).',';
        echo 'paste_preprocess : function(pl, o) { o.wordContent = true; },';
        echo 'theme_advanced_path : false,
     			setup : function(ed) {
          			ed.onKeyUp.add(function(ed, e) {   
               		var strip = (tinyMCE.activeEditor.getContent()).replace(/<([^>]+)>/ig,"");
					strip = strip.replace(/&nbsp;/ig,"");
					strip = strip.replace(/\n/ig,"");
					strip = strip.replace(/\r/ig,"");
					var charsCount = strip.length;
					var text = charsCount + " Characters"
        			tinymce.DOM.setHTML(tinymce.DOM.get(tinyMCE.activeEditor.id + \'_path_row\'), text);
					document.getElementById(tinyMCE.activeEditor.id + \'_signCounter\').value = charsCount;
    			});}
     		});';
        echo '</script>';
    }

    /**
     * Renders a DHTML auto-complete field (using Scriptaculous Ajax.Autocompleter)
     *
     * @param string $name        Field name
     * @param string $value       Optional selected value
     * @param string $label       Optional field label
     * @param array  $attributes  Optional assoc array of DHTML attributes
     * @param array  $acOptions   Assoc array of Ajax.Autocompleter options
     * @param bool   $noli        TRUE to remove the surrounding LI tag (default is false)
     *
     *
     * Special note: The auto-complete div has 'autoComplete' as default CSS class.
     *               To override this class please set a special 'acClass' key into the 'attributes' parameter
     *
     * About acOptions: The acOptions can be empty, but if null the auto-completion will be deactivated.
     *                  Also, this array reflect scriptaculous options, so it can contains many parameters such as:
     *                  - paramName: a string to pass to the AJAX URL callback (the element name by default)
     *                  - callback: the javascript callback (null by default)
     *                  - tokens: the token (or an array of tokens) (null by default)
     *                  - min_chars: the minimum number of chars (1 by default)
     *                  - indicator: a DHTML element id to show/hide during AJAX call
     *                  etc.. (see Scriptaculous doc for more info)
     */
    static function renderAutoCompletedField($url, $name, $value = null, $label = null, $attributes = null, $acOptions = null, $noli = false)
    {
        if (!$noli) echo '<li>';
        if ($label) echo '<label>'.textH8($label).'</label>';
        if (!is_array($attributes)) $attributes = array();
        $attributes['type'] = 'text';
        $attributes['name'] = $name;
        $attributes['value'] = $value;
        if (!isset($attributes['id'])) $attributes['id'] = $name;

        self::renderSingleTag('input', $attributes);
        echo '<div id="_acdiv_' . $name . '" style="display: none;" class="autoComplete"></div>';

        // Add auto-completer behaviour?
        if (is_array($acOptions))
        {
            // Set default auto-completer options
            $options = array('min_chars' => 1);

            foreach($acOptions as $option => $val)
            {
                $options[$option] = $val;
            }

            // Render javascript
            echo '<script type="text/javascript">';
            echo ' _ac_' . $name  . '= new Ajax.Autocompleter(\'' . $name . '\', \'_acdiv_' . $name . '\', \'' . $url . '?autoCompleteName=' . $name .'\'';
            echo ', '.self::getJavascriptOptions($options).');';
            echo '</script>';
        }
        if (!$noli) echo '</li>';
    }

    /**
     * Renders HTML code to display a field and the list of properties it contains (as a UL/LI)
     * When entering a string into this field, it will be added to the list
     *
     * @param string $name       Field name
     * @param array  $values     Optional field values
     * @param array  $attributes Optional assoc array of DHTML attributes
     * @param array  $acOptions  Optional array for auto-complete options (must contains an 'url' key)
     * @param constant $suggest_label    Label used for the suggest modal - send null if we don't want to have the suggest button
     * 
     * About $acOptions: This array is build exactly as for the 'renderAutoCompletedField' method
     *                   AND MUST contains a special 'url' key for the auto-completer ajax wizard.
     *                   Please see documentation of renderAutoCompletedField method for details.
     */
    static function renderListField($name, $values = null, $attributes = null, $acOptions = null, $suggest_label = null)
    {
        echo '<li>';
        $literalValue = (is_array($values)) ? implode('|', $values) : null;
        self::renderHiddenField($name, $literalValue, array('id' => $name));
        if (!is_array($attributes)) $attributes = array();
        $attributes['onkeydown'] = '_wcmAddElement(this, \'' . $name . '\', event);';
        $attributes['type'] = 'text';

        if (is_array($acOptions) && isset($acOptions['url']))
        {
            // generate auto-completed field
            $url = $acOptions['url'];
            unset($acOptions['url']);
            self::renderAutoCompletedField($url, '_acf_'.uniqid(), '', null, $attributes, $acOptions, true);
        }
        else
        {
            // standard input text
            self::renderSingleTag('input', $attributes);        
        }
        
        echo '<a href="#" onclick="_wcmAddElement($(this).previous(\'input\'), \'' . $name . '\', null); return false;" class="list-builder"><span>' . _BIZ_ADD . '</span></a>';
        if($suggest_label)
            echo '<a id="'.$name.'_link" href="#" onclick="openmodal(\'' . $suggest_label . '\'); modalPopup(\'tme\',\''.$name.'\', \'\', null, \''.$name.'\'); return false;" class="list-builder"><span>'._BIZ_SUGGEST.'</span></a>';
            
        echo '<ul class="tags">';
        if (is_array($values))
        {
            foreach ($values as $value)
            {
                if (trim($value) != '')
                {
                    echo '<li><a href="#" onclick="_wcmDeleteElement($(this).up(), \'' . $name . '\'); return false;"><span>' . _DELETE . '</span></a> <em>' . $value . '</em></li>';
                }
            }
        } 
        echo '</ul>';
        echo '</li>';
    }
 
    /**
     * Renders a DHTML auto-complete field (using Scriptaculous Ajax.Autocompleter)
     *
     * @param string $name        Field name
     * @param string $value       Optional selected value
     * @param string $label       Optional field label
     * @param array  $attributes  Optional assoc array of DHTML attributes
     * @param array  $acOptions   Optional assoc array of Ajax.Autocompleter options
     *
     *
     * Special note: The auto-complete div has 'autoComplete' as default CSS class.
     *               To override this class please set a special 'acClass' key into the 'attributes' parameter
     *
     * About acOptions: The scriptaculous options may contains many parameters such as:
     *                  - paramName: a string to pass to the AJAX URL callback (the element name by default)
     *                  - callback: the javascript callback (null by default)
     *                  - tokens: the token (or an array of tokens) (null by default)
     *                  - min_chars: the minimum number of chars (1 by default)
     *                  - indicator: a DHTML element id to show/hide during AJAX call
     *                  etc.. (see Scriptaculous doc for more info)
     */
    static function renderSectionsField($url, $name, $value = null, $label = null, $attributes = null, $acOptions = null)
    {
        if ($label) echo '<label>'.textH8($label).'</label>';
        if (!is_array($attributes)) $attributes = array();
        $attributes['type'] = 'text';
        $attributes['name'] = $name;
        $attributes['value'] = $value;
        if (!isset($attributes['id'])) $attributes['id'] = $name;

        self::renderSingleTag('input', $attributes);
        echo '<div id="_acdiv_' . $name . '" style="display: none;" class="autoComplete"></div>';

        // Set default auto-completer options
        $options = array();

        // Override auto-completer options
        if (is_array($acOptions))
        {
            foreach($acOptions as $option => $val)
            {
                $options[$option] = $val;
            }
        }

        // Render javascript
        echo '<script type="text/javascript">';
        echo ' _ac_' . $name  . '= new Ajax.Autocompleter(\'' . $name . '\', \'_acdiv_' . $name . '\', \'' . $url . '?autoCompleteName=' . $name .'\'';
        echo ', '.self::getJavascriptOptions($options).');';
        echo '</script>';
    }
}
