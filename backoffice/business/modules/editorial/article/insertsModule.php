<?php
/**
 * Project:     WCM
 * File:        modules/editorial/article/insertsModule.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    
    $uniqid = uniqid('inserts_text_');
    $inserts = getArrayParameter($params, 'inserts', new inserts());

	$menus = array(
				getConst(_ADD_INSERTS_AFTER) => '\'' . wcmModuleURL('business/editorial/article/insertsModule') . '\', null, this',
				getConst(_DELETE_INSERTS)    => 'removeInserts'
			);

    $info = '<ul>';
    foreach ($menus as $title => $action)
    {
    	if ($title == getConst(_DELETE_INSERTS))
    	{
           $info .= '<li><a href="#" onclick="'.$action.'(this, \''.$uniqid.'\'); return false;">' . $title . '</a></li>';
    	}
    	else
    	{        
    	   $info .= '<li><a href="#" onclick="addInserts(' . $action . '); return false;">' . $title . '</a></li>';
    	}
    }
    $info .= '</ul>';
    
    // Cut title to 50 chars max
    
    $title = (isset($inserts->kind)) ? _BIZ_ARTICLE_INSERTS . ' ' . $inserts->rank : _BIZ_NEW_INSERTS;
    echo '<div class="zone">';
		echo '<div id="inserts" style="clear: both;">';
		wcmGUI::openCollapsablePane($title, true, $info);
		wcmGUI::openFieldset('', array('id' => 'insertFieldset'. $inserts->id));
		wcmGUI::renderDropdownField('inserts_kind[]', inserts::getKind(), $inserts->kind, _BIZ_INSERTS_KIND);
		wcmGUI::renderTextField('inserts_title[]', $inserts->title, _BIZ_INSERTS_TITLE);
		wcmGUI::renderTextArea('inserts_text[]', $inserts->text, _BIZ_INSERTS_CONTENT,array('rows'=>2));
		wcmGUI::renderTextField('inserts_source[]', $inserts->source, _BIZ_INSERTS_SOURCE);
		wcmGUI::closeFieldset();
		wcmGUI::closeCollapsablePane();
		echo "</div>";
	echo "</div>";