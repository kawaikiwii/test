<?php
/**
 * Project:     WCM
 * File:        modules/shared/content.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();

	$initialClass = getArrayParameter($params, 'bizObjectClass', '');
    $uniqid = 'content_' . $initialClass;
    $content = getArrayParameter($params, 'content', new content());

    wcmGUI::openFieldset('', array('id' => 'pageFieldset'. $content->id));

    if ($initialClass == 'photo')
	{
		echo '<div style="border-left:#ddd 3px solid; padding-left:10px;">';
			
			$credits = (isset($bizobject->credits))?$bizobject->credits:'';
			echo '<table class="formWithSignsCount"><tr><td class="myTitle">';	
			    wcmGUI::renderTextField($uniqid.'_credits', $credits, _BIZ_CREDITS);
			echo '</td><td>';
		    echo '</tr></td></table>';
			
			$specialUses = (isset($bizobject->specialUses))?$bizobject->specialUses:'';
			echo '<table class="formWithSignsCount"><tr><td class="myTitle">';	
			    wcmGUI::renderTextField($uniqid.'_specialUses', $specialUses, _BIZ_SPECIAL_USES);
			echo '</td><td>';
		    echo '</tr></td></table>';
			
		echo '</div>';
	}
	
	// Extras infos : Signs count
    wcmGUI::renderHiddenField($uniqid.'_titleSigns', ($content->titleSigns != NULL) ? $content->titleSigns : '0', array('id' => 'content_news_title_signCounter'));
    //wcmGUI::renderHiddenField($uniqid.'_descriptionSigns', ($content->descriptionSigns != NULL) ? $content->descriptionSigns : '0', array('id' => 'content_news_description_signCounter'));
    //wcmGUI::renderHiddenField($uniqid.'_textSigns', ($content->textSigns != NULL) ? $content->textSigns : '0', array('id' => 'content_news_text_signCounter'));

	
	if ($initialClass == 'photo')
	{
		echo '<table class="formWithSignsCount"><tr><td class="myTitle">';
		wcmGUI::renderTextField($uniqid.'_title', $content->title, _BIZ_CONTENT_TITLE);
		echo '</td><td>';
		//echo '<span class="signsCountGreen" id="counter-content_news_title">0</span> '._BIZ_SIGNS;
	    echo '</tr></td></table>';

		echo '<table class="formWithSignsCount"><tr><td class="myTitle">';
	    wcmGUI::renderTextArea($uniqid.'_description', $content->description, _BIZ_CONTENT_DESCRIPTION);
	    echo '</td><td>';
		$currentDescriptionSignsCount = ($content->descriptionSigns != NULL) ? $content->descriptionSigns : '0';
		//echo '<span class="signsCountGreen" id="counter-content_news_description">'.$currentDescriptionSignsCount.'</span> '._BIZ_SIGNS;
	    echo '</tr></td></table>';
	
		echo '<table class="formWithSignsCount"><tr><td class="myTitle">';
	    wcmGUI::renderTextArea($uniqid.'_text', $content->text, _BIZ_CONTENT_TEXT);
		echo '</td><td>';
		$currentTextSignsCount = ($content->textSigns != NULL) ? $content->textSigns : '0';
		//echo '<span class="signsCountGreen" id="counter-content_news_text">'.$currentTextSignsCount.'</span> '._BIZ_SIGNS;
	    echo '</tr></td></table>';
		
		wcmGUI::renderHiddenField('formats', $bizobject->formats);
	}
	else
	{
		$spellLanguage = ($_SESSION['wcmSession']->getSite()->language == 'en') ? '+English=en' : '+French=fr';
		wcmGUI::renderTextField($uniqid.'_title', $content->title, _BIZ_CONTENT_TITLE);	
	    //wcmGUI::renderEditableField($uniqid.'_description', $content->description, _BIZ_CONTENT_DESCRIPTION, null, array('language' => 'en', 'height' => '10px', 'spellchecker_languages' => $spellLanguage));
	    //wcmGUI::renderEditableField($uniqid.'_text', $content->text, _BIZ_CONTENT_TEXT, null, array('language' => 'en', 'spellchecker_languages' => $spellLanguage));
	}

    wcmGUI::closeFieldset();
	

?>
