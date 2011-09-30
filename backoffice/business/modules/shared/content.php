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
$uniqid = 'content_'.$initialClass;
$content = getArrayParameter($params, 'content', new content());

wcmGUI::openFieldset('', array('id' => 'pageFieldset'. $content->id));

if ($initialClass == 'photo')
{
	echo '<div style="border-left:#ddd 3px solid; padding-left:10px;">';
		
		$credits = (isset($bizobject->credits)) ? $bizobject->credits : '';
		echo '<table class="formWithSignsCount"><tr><td class="myTitle">';	
		    wcmGUI::renderTextField($uniqid.'_credits', $credits, _BIZ_CREDITS);
		echo '</td><td>';
	    echo '</tr></td></table>';
		
		$specialUses = (isset($bizobject->specialUses)) ? $bizobject->specialUses : '';
		echo '<table class="formWithSignsCount"><tr><td class="myTitle">';	
		    wcmGUI::renderTextField($uniqid.'_specialUses', $specialUses, _BIZ_SPECIAL_USES);
		echo '</td><td>';
	    echo '</tr></td></table>';
		
	echo '</div>';
}

// Extras infos : Signs count
wcmGUI::renderHiddenField($uniqid.'_titleSigns', ($content->titleSigns != NULL) ? $content->titleSigns : '0', array('id' => 'content_news_title_signCounter'));
wcmGUI::renderHiddenField($uniqid.'_descriptionSigns', ($content->descriptionSigns != NULL) ? $content->descriptionSigns : '0', array('id' => 'content_news_description_signCounter'));
wcmGUI::renderHiddenField($uniqid.'_textSigns', ($content->textSigns != NULL) ? $content->textSigns : '0', array('id' => 'content_news_text_signCounter'));

if ($initialClass == 'photo')
{
	echo '<table class="formWithSignsCount"><tr><td class="myTitle">';
	wcmGUI::renderTextField($uniqid.'_title', $content->title, _BIZ_CONTENT_TITLE);
	echo '</td><td>';
	//echo '<span class="signsCountGreen" id="counter-content_news_title">'.$content->titleSigns.'</span> '._BIZ_SIGNS;
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
	wcmGUI::renderTextField($uniqid.'_title', $content->title, _BIZ_CONTENT_TITLE, array("style" => "width:700px"),true);	
    //echo 'test';
    $currentTitleSignsCount = ($content->title != NULL) ? $content->title : '0';
    //echo '<span class="signsCountGreen" id="counter-content_news_Title">'.$currentTitleSignsCount.'</span> '._BIZ_SIGNS;
	wcmGUI::renderEditableField($uniqid.'_description', $content->description, _BIZ_CONTENT_DESCRIPTION, null, array('width' => 704, 'height' => 200,'language' => 'en',  'spellchecker_languages' => $spellLanguage));
    wcmGUI::renderEditableField($uniqid.'_text', $content->text, _BIZ_CONTENT_TEXT, null, array('width' => 704, 'height' => 600, 'language' => 'en', 'spellchecker_languages' => $spellLanguage));
}
wcmGUI::closeFieldset();

/* Characters limiter Javascript 

<script type='text/javascript'>

	/* Added by RelaxNews
	   @contact jmeyer@relaxnews.com
	 

	initCounter();

	function initCounter(){
		makeItCount('content_news_title', 90, false);
		//makeItCount('content_news_description', 500, false);
		//makeItCount('content_news_text', 1500, false);
	}

	function charCounter(id, maxlimit, limited){
		/*if (!$('counter-'+id)){
			$(id).insert({after: '<div id="counter-'+id+'"></div>'});
		}

		if($F(id).length >= maxlimit){
			//if(limited){	$(id).value = $F(id).substring(0, maxlimit); }
			//$('counter-'+id).addClassName('signsCountRed');
			//$('counter-'+id).removeClassName('signsCountGreen');
		} else {
			//$('counter-'+id).removeClassName('signsCountRed');
			//$('counter-'+id).addClassName('signsCountGreen');
		}
		//$('counter-'+id).update( $F(id).length + '/' + maxlimit );
		$('counter-'+id).update( $F(id).length );
		//if (id == 'content_news_title')
		//{
			document.getElementById('content_news_title_signCounter').value = $F(id).length;
		//}
	}

	function makeItCount(id, maxsize, limited){
		if(limited == null) limited = true;
		if ($(id)){
			Event.observe($(id), 'keyup', function(){charCounter(id, maxsize, limited);}, false);
			Event.observe($(id), 'keydown', function(){charCounter(id, maxsize, limited);}, false);
			Event.observe($(id), 'onchange', function(){charCounter(id, maxsize, limited);}, false);
			charCounter(id,maxsize,limited);
		}

	}
</script>*/
?>
