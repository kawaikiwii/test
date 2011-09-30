<?php
/**
 * Project:     WCM
 * File:        modules/viaMichelin/hotels.php
 *
 * @copyright   (c)2011 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    // pour le correcteur d'orthographe du Wysiwyg
    $spellLanguage = ($_SESSION['wcmSession']->getSite()->language == 'en') ? '+English=en' : '+French=fr';
   	echo '<div class="zone">';
    
    wcmGUI::openCollapsablePane("Bloc 1");   
    wcmGUI::openFieldset("Source Française pour site Belge",array('style' => 'background-color:lavender'));
    wcmGUI::renderTextField('hotels_be_fra_title', $bizobject->hotels_be_fra_title, "Titre");
    wcmGUI::renderTextArea('hotels_be_fra_header', $bizobject->hotels_be_fra_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('hotels_be_fra_text', $bizobject->hotels_be_fra_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('hotels_be_fra_text_signCounter', '', array('id' => 'hotels_be_fra_text_signCounter'));    
    wcmGUI::renderTextArea('hotels_be_fra_photoLegend', $bizobject->hotels_be_fra_photoLegend, "Légende photo");
    wcmGUI::renderTextField('hotels_be_fra_articleLink', $bizobject->hotels_be_fra_articleLink, "Lien article");
    wcmGUI::renderTextField('hotels_be_fra_photoLink', $bizobject->hotels_be_fra_photoLink, "Lien Photo");
    wcmGUI::renderTextField('hotels_be_fra_photoCredits', $bizobject->hotels_be_fra_photoCredits, "Crédits Photo");
    wcmGUI::closeFieldset();
    
    wcmGUI::openFieldset("Traduction en Flamand pour site Belge",array('style' => 'background-color:Beige'));
    wcmGUI::renderTextField('hotels_be_nld_title', $bizobject->hotels_be_nld_title, "Titre");
    wcmGUI::renderTextArea('hotels_be_nld_header', $bizobject->hotels_be_nld_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('hotels_be_nld_text', $bizobject->hotels_be_nld_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('hotels_be_nld_text_signCounter', '', array('id' => 'hotels_be_nld_text_signCounter'));    
    wcmGUI::renderTextArea('hotels_be_nld_photoLegend', $bizobject->hotels_be_nld_photoLegend, "Légende photo");
    wcmGUI::closeFieldset();
    
    wcmGUI::openFieldset("Traduction en Allemand pour site Suisse",array('style' => 'background-color:Beige'));
    wcmGUI::renderTextField('hotels_ch_deu_title', $bizobject->hotels_ch_deu_title, "Titre");
    wcmGUI::renderTextArea('hotels_ch_deu_header', $bizobject->hotels_ch_deu_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('hotels_ch_deu_text', $bizobject->hotels_ch_deu_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('hotels_ch_deu_text_signCounter', '', array('id' => 'hotels_ch_deu_text_signCounter'));    
    wcmGUI::renderTextArea('hotels_ch_deu_photoLegend', $bizobject->hotels_ch_deu_photoLegend, "Légende photo");
    wcmGUI::closeFieldset();  
    
    wcmGUI::openFieldset("Traduction en Italien pour site Suisse",array('style' => 'background-color:Beige'));
    wcmGUI::renderTextField('hotels_ch_ita_title', $bizobject->hotels_ch_ita_title, "Titre");
    wcmGUI::renderTextArea('hotels_ch_ita_header', $bizobject->hotels_ch_ita_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('hotels_ch_ita_text', $bizobject->hotels_ch_ita_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('hotels_ch_ita_text_signCounter', '', array('id' => 'hotels_ch_ita_text_signCounter'));    
    wcmGUI::renderTextArea('hotels_ch_ita_photoLegend', $bizobject->hotels_ch_ita_photoLegend, "Légende photo");
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    
    wcmGUI::openCollapsablePane("Bloc 2"); 
	wcmGUI::openFieldset("Source Française pour site Suisse",array('style' => 'background-color:lavender'));
    wcmGUI::renderTextField('hotels_ch_fra_title', $bizobject->hotels_ch_fra_title, "Titre");
    wcmGUI::renderTextArea('hotels_ch_fra_header', $bizobject->hotels_ch_fra_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('hotels_ch_fra_text', $bizobject->hotels_ch_fra_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('hotels_ch_fra_text_signCounter', '', array('id' => 'hotels_ch_fra_text_signCounter'));    
    wcmGUI::renderTextArea('hotels_ch_fra_photoLegend', $bizobject->hotels_ch_fra_photoLegend, "Légende photo");
    wcmGUI::renderTextField('hotels_ch_fra_articleLink', $bizobject->hotels_ch_fra_articleLink, "Lien article");
    wcmGUI::renderTextField('hotels_ch_fra_photoLink', $bizobject->hotels_ch_fra_photoLink, "Lien Photo");
    wcmGUI::renderTextField('hotels_ch_fra_photoCredits', $bizobject->hotels_ch_fra_photoCredits, "Crédits Photo");
    wcmGUI::closeFieldset();
       
	wcmGUI::openFieldset("Traduction en Néerlandais pour site Pays-bas",array('style' => 'background-color:Beige'));
    wcmGUI::renderTextField('hotels_nl_nld_title', $bizobject->hotels_nl_nld_title, "Titre");
    wcmGUI::renderTextArea('hotels_nl_nld_header', $bizobject->hotels_nl_nld_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('hotels_nl_nld_text', $bizobject->hotels_nl_nld_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('hotels_nl_nld_text_signCounter', '', array('id' => 'hotels_nl_nld_text_signCounter'));    
    wcmGUI::renderTextArea('hotels_nl_nld_photoLegend', $bizobject->hotels_nl_nld_photoLegend, "Légende photo");
    wcmGUI::closeFieldset();
    
    wcmGUI::openFieldset("Traduction en Allemand pour site Autrichien",array('style' => 'background-color:Beige'));
    wcmGUI::renderTextField('hotels_at_deu_title', $bizobject->hotels_at_deu_title, "Titre");
    wcmGUI::renderTextArea('hotels_at_deu_header', $bizobject->hotels_at_deu_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('hotels_at_deu_text', $bizobject->hotels_at_deu_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));    
    wcmGUI::renderHiddenField('hotels_at_deu_text_signCounter', '', array('id' => 'hotels_at_deu_text_signCounter'));    
    wcmGUI::renderTextArea('hotels_at_deu_photoLegend', $bizobject->hotels_at_deu_photoLegend, "Légende photo");
    wcmGUI::closeFieldset(); 
    wcmGUI::closeCollapsablePane();
    
    wcmGUI::openCollapsablePane("Bloc 3"); 
    wcmGUI::openFieldset("Source Anglaise pour site Anglais",array('style' => 'background-color:lavender'));
    wcmGUI::renderTextField('hotels_uk_int_title', $bizobject->hotels_uk_int_title, "Titre");
    wcmGUI::renderTextArea('hotels_uk_int_header', $bizobject->hotels_uk_int_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('hotels_uk_int_text', $bizobject->hotels_uk_int_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('hotels_uk_int_text_signCounter', '', array('id' => 'hotels_uk_int_text_signCounter'));    
    wcmGUI::renderTextArea('hotels_uk_int_photoLegend', $bizobject->hotels_uk_int_photoLegend, "Légende photo");
    wcmGUI::renderTextField('hotels_uk_int_articleLink', $bizobject->hotels_uk_int_articleLink, "Lien article");
    wcmGUI::renderTextField('hotels_uk_int_photoLink', $bizobject->hotels_uk_int_photoLink, "Lien Photo");
    wcmGUI::renderTextField('hotels_uk_int_photoCredits', $bizobject->hotels_uk_int_photoCredits, "Crédits Photo");	
	wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    
    echo '</div>';
