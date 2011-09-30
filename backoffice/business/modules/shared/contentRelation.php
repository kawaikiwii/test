<?php
/**
 * Project:     WCM
 * File:        modules/shared/contentRelation.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();

	$initialClass = getArrayParameter($params, 'bizObjectClass', '');
    $uniqid = 'content_' . $initialClass;
    $content = getArrayParameter($params, 'content', new content());


    if ($initialClass == 'photo')
	{
			
			$credits = (isset($bizobject->credits))?$bizobject->credits:'';
			//echo '<table cellpadding=0 cellspacing=0><tr><td>';
			echo '<b>'._BIZ_CREDITS.'</b><br>';
			    wcmGUI::renderTextField($uniqid.'_credits', $credits, NULL, array('size' => '60'));
			//echo '</td><td>';
		    //echo '</tr></td></table>';
			
			$specialUses = (isset($bizobject->specialUses))?$bizobject->specialUses:'';
			//echo '<table><tr><td>';	
			echo '<b>'._BIZ_SPECIAL_USES.'</b><br>';
			    wcmGUI::renderTextField($uniqid.'_specialUses', $specialUses, NULL, array('size' => '60'));
			//echo '</td><td>';
		    //echo '</tr></td></table>';
	}
	
	// Extras infos : Signs count
    wcmGUI::renderHiddenField($uniqid.'_titleSigns', ($content->titleSigns != NULL) ? $content->titleSigns : '0', array('id' => 'content_news_title_signCounter'));
    wcmGUI::renderHiddenField($uniqid.'_descriptionSigns', ($content->descriptionSigns != NULL) ? $content->descriptionSigns : '0', array('id' => 'content_news_description_signCounter'));
    wcmGUI::renderHiddenField($uniqid.'_textSigns', ($content->textSigns != NULL) ? $content->textSigns : '0', array('id' => 'content_news_text_signCounter'));

	
	if ($initialClass == 'photo')
	{
			//echo '<table><tr><td>';	
			echo '<b>'._BIZ_CONTENT_TITLE.'</b><br>';
		wcmGUI::renderTextField($uniqid.'_title', $content->title, NULL, array('size' => '60'));
		//echo '</td><td>';
		//echo '<span class="signsCountGreen" id="counter-content_news_title">0</span> '._BIZ_SIGNS;
	    //echo '</tr></td></table>';

			//echo '<table><tr><td>';	
			echo '<b>'._BIZ_CONTENT_DESCRIPTION.'</b><br>';
	    wcmGUI::renderTextArea($uniqid.'_description', $content->description, NULL, array('cols' => '45', 'rows' => '3'));
	    //echo '</td><td>';
		$currentDescriptionSignsCount = ($content->descriptionSigns != NULL) ? $content->descriptionSigns : '0';
		//echo '<span class="signsCountGreen" id="counter-content_news_description">'.$currentDescriptionSignsCount.'</span> '._BIZ_SIGNS;
	    //echo '</tr></td></table>';
	
			//echo '<table><tr><td>';	
			echo '<b>'._BIZ_CONTENT_TEXT.'</b><br>';
	    wcmGUI::renderTextArea($uniqid.'_text', $content->text, NULL, array('cols' => '45', 'rows' => '3'));
		//echo '</td><td>';
		$currentTextSignsCount = ($content->textSigns != NULL) ? $content->textSigns : '0';
		//echo '<span class="signsCountGreen" id="counter-content_news_text">'.$currentTextSignsCount.'</span> '._BIZ_SIGNS;
	    //echo '</tr></td></table>';
		
		wcmGUI::renderHiddenField('formats', $bizobject->formats);
	}
	else
	{

		wcmGUI::renderTextField($uniqid.'_title', $content->title, _BIZ_CONTENT_TITLE);
		
	    wcmGUI::renderEditableField($uniqid.'_description', $content->description, _BIZ_CONTENT_DESCRIPTION, null, array('height' => '10px', 'width' => '200px', 'theme' => 'simple'));
		//$currentDescriptionSignsCount = ($content->descriptionSigns != NULL) ? $content->descriptionSigns : '0';
	
	    wcmGUI::renderEditableField($uniqid.'_text', $content->text, _BIZ_CONTENT_TEXT);
	    //wcmGUI::renderEditableField($uniqid.'_text', $content->text, _BIZ_CONTENT_TEXT, null, array('language' => 'fr'));
		//$currentTextSignsCount = ($content->textSigns != NULL) ? $content->textSigns : '0';
	}
	

    

	
	



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
