<?php
/**
 * Project:     WCM
 * File:        biz.relaxGUI.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This class contains helper to render
 * the graphical user interface
 */
class relaxGUI extends wcmFormGUI
{
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
    static function renderRelaxListField($name, $values = null, $attributes = null, $acOptions = null, $suggest_label = null, $linkToAction = null)
    {
    	$config  = wcmConfig::getInstance();
        echo '<li>';
        $literalValue = (is_array($values)) ? implode('|', $values) : null;
        self::renderHiddenField($name, $literalValue, array('id' => $name));
        if (!is_array($attributes)) $attributes = array();
        $attributes['onkeydown'] = '_wcmAddElement(this, \'' . $name . '\', event, \''.$acOptions['className'].'\');';
        $attributes['type'] = 'text';

        $css_clear = '';
        if (is_array($acOptions) && isset($acOptions['css_clear']))
        {
            $css_clear = ' style="clear:'.$acOptions['css_clear'].'"';
            unset($acOptions['css_clear']);
        }

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
        
        echo '<a href="#" onclick="_wcmAddElement($(this).previous(\'input\'), \'' . $name . '\', null, \''.$acOptions['className'].'\'); return false;" class="list-builder"><span>' . _BIZ_ADD . '</span></a>';
        if($suggest_label)
            echo '<a id="'.$name.'_link" href="#" onclick="openmodal(\'' . $suggest_label . '\'); modalPopup(\'tme\',\''.$name.'\', \'\', null, \''.$name.'\'); return false;" class="list-builder"><span>'._BIZ_SUGGEST.'</span></a>';
            
        echo '<ul class="tags">';
        if (is_array($values))
        {
            foreach ($values as $value)
            {
                if (trim($value) != '')
                {
                	$bizobject = new $acOptions['className'];
                	$bizobject->refresh($value);
                	$id = $bizobject->id;
                	if ($acOptions['className'] == 'account')
                		$label = $bizobject->wcmUser_name;
                	else if ($acOptions['className'] == 'wcmUser')
                		$label = $bizobject->name.":".$bizobject->id;
                	else
                		$label = $bizobject->title;
                		
                	if ($linkToAction)	
                    	echo '<li'.$css_clear.' id="'.$bizobject->id.'"><a href="#" onclick="_wcmDeleteElement($(this).up(), \'' . $name . '\'); return false;"><span>' . _DELETE . '</span></a> <em>' . $label . '</em></li><a href="/index.php?_wcmAction=business/'.$linkToAction.'&id='.$bizobject->id.'" target="new"><img src="'.$config['wcm.backOffice.url'].'/img/icons/pencil.png" title="Edit" border="0"></a>';
                	else
                		echo '<li'.$css_clear.' id="'.$bizobject->id.'"><a href="#" onclick="_wcmDeleteElement($(this).up(), \'' . $name . '\'); return false;"><span>' . _DELETE . '</span></a> <em>' . $label . '</em></li>';
                }
            }
        } 
        echo '</ul>';
        echo '</li>';
    }
    
	static function createNewObjectWithRelation($object, $destinationObject = null)
    {
    	$config  = wcmConfig::getInstance();
    	$html  = "";
    	
    	if ($destinationObject)
    		$html  .= '<a class="list-builder" href="'.$config['wcm.backOffice.url'].'?_wcmAction=business/'.$destinationObject.'&relclassname='.$object->getClass().'&relclassid='.$object->id.'" onclick="return confirm(\'Warning : modifications will be lost, please confirm!\')"><span>New '.$destinationObject.'</span></a><br />';
        
    	$relations = wcmBizrelation::getRelationsToBizobject($object, wcmBizrelation::IS_COMPOSED_OF, null, false);
    	
    	if (!empty($relations))
    	{
    		$html .= "<br />Existing relations</br><hr>";
	    	foreach($relations as $rel)
	    	{
	    		$sourceclass = $rel['sourceClass'];
            	$sourceid = $rel['sourceId'];
	    		$objet = new $sourceclass(wcmProject::getInstance(), $sourceid);
	    		
	        	$html  .= "<div class=\"toolbar\"><nobr><a href='index.php?_wcmAction=business/".$sourceclass."&id=".$sourceid."'><span class=\"".$sourceclass."\" title=\"".$sourceclass."\"><span style=\"margin-left:20px;\">".bizobject::truncate($objet->title,23, true)."</span></span></a></nobr></div></br>";
	    	}
    	}
    	
        return $html;
    }
    
	static function getArrayCheckboxes($name, $options, $currentOption = null)
	{
	    $arrayResult = array();
	    if (is_array($options))
	    {
	    	foreach($options as $value)
		    {
		        $result = '';
		        $result .= "<nobr><input type='checkbox' name='".$name."[]' value='" . textH8($value) . "' style='cursor: pointer;'";
		        if ($currentOption && in_array($value,$currentOption)) $result .= " checked";
		        $result .= ">" . textH8($value) . " |</nobr> ";
		        echo $result;
		    }
	    }
	} 

	static function getArrayColumnsCheckboxes($name, $options, $currentOption = null)
	{
	    if (is_array($options))
	    {
	    	$i=0;
	    	$j=0;
	    	$result = "<table width='100%' border='0' cellspacing='1' cellpadding='1'>";
		    $end = sizeof($options);
		    
		    foreach($options as $value)
		    {
		    	if ($i == 0) $result .= "<tr>";
		        $result .= "<td><input type='checkbox' name='".$name."[]' value='" . textH8($value) . "' style='cursor: pointer;'";
		        if ($currentOption && in_array($value,$currentOption)) $result .= " checked";
		        $result .= ">".textH8($value)."</td>";
		        $j++;
		        
		        if ($i == 4)
		        {
		        	$result .= "</tr>";
		        	$i = 0;
		        }
		        else
		        {
		        	$i++;
		        	if ($j == $end)	$result .= "</tr>";    		        	
		        }	        
		    }
		    $result .= "</table>";
	    	echo $result;
	    }
	}  

	static function removeSpecialChar($txt)
	{
	    $txt = str_replace('œ', 'oe', $txt);
	    $txt = str_replace('Œ', 'Oe', $txt);
	    $txt = str_replace('æ', 'ae', $txt);
	    $txt = str_replace('Æ', 'Ae', $txt);
	    $txt = str_replace('’', '\'', $txt);
	    
	    return $txt;
	}
	
	/**
     * Renders HTML code to display a boolean field
     *
     * @param string $name       Field name
     * @param bool   $value      Optional field value
     * @param string $label      Optional field label
     * @param array  $attributes Optional assoc array of DHTML attributes
     */
	static function renderEchosBooleanUField($name, $value = false, $label = null, $fieldId1, $fieldId2, $attributes = null)
    {
    	$id = uniqid();
        echo '<li class="boolean">';
        self::renderHiddenField($name, ($value) ? '1' : '0', array('id' => $id));
        echo '<input type="checkbox"';
        if ($value) echo ' checked="checked"';
        echo ' onclick="$(\''.$id.'\').value=(this.checked)?\'1\':\'0\';if(this.checked) {$(\''.$fieldId1.'\').setAttribute(\'readonly\', \'readonly\');$(\''.$fieldId1.'\').style.backgroundColor=\'#eeeeee\';$(\''.$fieldId2.'\').setAttribute(\'readonly\', \'readonly\');$(\''.$fieldId2.'\').style.backgroundColor=\'#eeeeee\';} else {$(\''.$fieldId1.'\').removeAttribute(\'readonly\');$(\''.$fieldId1.'\').style.backgroundColor=\'#ffffff\';$(\''.$fieldId2.'\').removeAttribute(\'readonly\');$(\''.$fieldId2.'\').style.backgroundColor=\'#ffffff\';}  "';
        echo ' name="_wcmBox' . $name . '" id="_wcmBox' .uniqid().$name. '"';
        self::renderAttributes($attributes);
        echo '/>';
        if ($label) echo '&nbsp;'.textH8($label);
        echo '</li>';
    }
	
	static function renderEchoSingleTag($name, $attributes = null, $readOnly = null)
    {
        echo '<'.$name;
        wcmGUI::renderAttributes($attributes);
        if (isset($readOnly) && !empty($readOnly)) echo '  style=\'background:#eeeeee\' readonly';
        else echo ' style=\'background:#ffffff\'';
        echo '/>';
    }
    
	static function renderEchoTextFieldReadOnly($name, $value = null, $label = null, $attributes = null, $readonly = null)
    {
        echo '<li>';

        if ($label) echo '<label>'.textH8($label).'</label>';

        if (!is_array($attributes)) $attributes = array();
        $attributes['type'] = 'text';
        $attributes['name'] = $name;
        $attributes['value'] = $value;
        if (!isset($attributes['id'])) $attributes['id'] = $name;

        self::renderEchoSingleTag('input', $attributes, $readonly);

        echo '</li>';
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
     * Special note: This function is a variant of renderAutoCompletedField (wcm.formGUI.php)
     *               The auto-complete div has 'autoComplete' as default CSS class.
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
    static function renderAutoCompletedCountry($url, $name, $value = null, $label = null, $attributes = null, $acOptions = null, $noli = false)
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
     * Renders HTML code to display a boolean field
     *
     * @param string $name       Field name
     * @param bool   $value      Optional field value
     * @param string $label      Optional field label
     * @param array  $attributes Optional assoc array of DHTML attributes
     */
    static function renderRelaxBooleanField($name, $value = false, $label = null, $attributes = null)
    {
        echo '<li class="boolean">';
         if ($label) echo '<label class="checkbox" for="_wcmBox' . $name . '">'.textH8($label).'</label>';
        self::renderHiddenField($name, ($value) ? '1' : '0', array('id' => $name));
        echo '<input type="checkbox"';
        if ($value) echo ' checked="checked"';
        echo ' onclick="$(\''.$name.'\').value=(this.checked)?\'1\':\'0\';"';
        echo 'name="_wcmBox' . $name . '" id="_wcmBox' . $name . '"';
        self::renderAttributes($attributes);
        echo '/>';
        echo '</li>';
    }
    
    /**
     * Renders googleMap
     *
     * @param object 	$object     object name
     * @param name   	$name       google map name
     * @param string 	$height     map height
     * @param string  	$width 		map width
     */
    static function renderGoogleMap($bizobject, $name = "gmap", $height = "440", $width = "800")
    {
    	$config  = wcmConfig::getInstance();
    	
    	echo '<iframe src="'.$config['wcm.backOffice.url'].'business/modules/shared/googleMap.php?lat='.$bizobject->latitude.'&lon='.$bizobject->longitude.'" name="'.$name.'" height="'.$height.'" width="'.$width.'"></iframe>';  
    	wcmGUI::renderHiddenField('latitude', $bizobject->latitude);
		wcmGUI::renderHiddenField('longitude', $bizobject->longitude);
    }
    
    
	/**
     * fonction permettant de nettoyer une chaine
     *
     * @return string nouvelle chaine nettoyée
     * @param string $text
     * @param string $separator
     * @param string $charset
     */
    static function clear_str($text, $separator = '-', $charset = 'utf-8') 
    {  
	    // Pour l'encodage
	    $text = mb_convert_encoding($text,'HTML-ENTITIES',$charset);
	    
	    $text = strtolower(trim($text));
	    
	    // On vire les accents
	    $text = preg_replace(   array('/ß/','/&(..)lig;/', '/&([aouAOU])uml;/','/&(.)[^;]*;/'), 
	                    array('ss',"$1","$1".'e',"$1"),  
	                    $text);
	    
	    // on vire tout ce qui n'est pas alphanumérique
	    $text_clear = eregi_replace("[^a-z0-9_-]",' ',trim($text));// ^a-zA-Z0-9_-
	    
	    // Nettoyage pour un espace maxi entre les mots
	    $array = explode(' ', $text_clear);
	    $str = '';
	    $i = 0;
	    foreach($array as $cle=>$valeur)
	    {
	        
	        if(trim($valeur) != '' AND trim($valeur) != $separator AND $i > 0)
	            $str .= $separator.$valeur;
	        elseif(trim($valeur) != '' AND trim($valeur) != $separator AND $i == 0)
	            $str .= $valeur;
	        
	        $i++;
    	}
    
    	//on renvoie la chaîne transformée
    	return $str;
    }
    
	/**
     * fonction permettant de vérifier l'existence d'un fichier à partir du chemin complet
     *
     * @return string  nouveau nom avec incrémentation automatique si existant
     * @param string $file
     */
    static function availableNameBeforeUpload($file)
    {
    	if (file_exists($file))
    	{
    		for ($i=1;$i<50;$i++)
    		{
    			$extension=pathinfo($file,PATHINFO_EXTENSION);
    			$fileTemp = str_replace(".".$extension, "_".$i.".".$extension, $file);
    			if (!file_exists($fileTemp))
    				return $fileTemp;
    		}	
    	}
    	else return $file;
    }
    
	static function remove_accents($str, $charset='utf-8')
	{
	    $str = htmlentities($str, ENT_NOQUOTES, $charset);
	    
	    $str = preg_replace('#\&([A-za-z])(?:acute|cedil|circ|grave|ring|tilde|uml)\;#', '\1', $str);
	    $str = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $str); // pour les ligatures e.g. '&oelig;'
	    $str = preg_replace('#\&[^;]+\;#', '', $str); // supprime les autres caractères
	    
	    return $str;
	}
}
