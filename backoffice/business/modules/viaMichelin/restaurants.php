<?php
/**
 * Project:     WCM
 * File:        modules/viaMichelin/restaurants.php
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
    wcmGUI::renderTextField('restaurants_be_fra_title', $bizobject->restaurants_be_fra_title, "Titre");
    wcmGUI::renderTextArea('restaurants_be_fra_header', $bizobject->restaurants_be_fra_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('restaurants_be_fra_text', $bizobject->restaurants_be_fra_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('restaurants_be_fra_text_signCounter', '', array('id' => 'restaurants_be_fra_text_signCounter'));    
    wcmGUI::renderTextArea('restaurants_be_fra_photoLegend', $bizobject->restaurants_be_fra_photoLegend, "Légende photo");
    wcmGUI::renderTextField('restaurants_be_fra_articleLink', $bizobject->restaurants_be_fra_articleLink, "Lien article");
    wcmGUI::renderTextField('restaurants_be_fra_photoLink', $bizobject->restaurants_be_fra_photoLink, "Lien Photo");
    wcmGUI::renderTextField('restaurants_be_fra_photoCredits', $bizobject->restaurants_be_fra_photoCredits, "Crédits Photo");
    wcmGUI::closeFieldset();
    
    wcmGUI::openFieldset("Traduction en Flamand pour site Belge",array('style' => 'background-color:Beige'));
    wcmGUI::renderTextField('restaurants_be_nld_title', $bizobject->restaurants_be_nld_title, "Titre");
    wcmGUI::renderTextArea('restaurants_be_nld_header', $bizobject->restaurants_be_nld_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('restaurants_be_nld_text', $bizobject->restaurants_be_nld_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('restaurants_be_nld_text_signCounter', '', array('id' => 'restaurants_be_nld_text_signCounter'));    
    wcmGUI::renderTextArea('restaurants_be_nld_photoLegend', $bizobject->restaurants_be_nld_photoLegend, "Légende photo");
    wcmGUI::closeFieldset();
    
    wcmGUI::openFieldset("Traduction en Allemand pour site Suisse",array('style' => 'background-color:Beige'));
    wcmGUI::renderTextField('restaurants_ch_deu_title', $bizobject->restaurants_ch_deu_title, "Titre");
    wcmGUI::renderTextArea('restaurants_ch_deu_header', $bizobject->restaurants_ch_deu_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('restaurants_ch_deu_text', $bizobject->restaurants_ch_deu_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('restaurants_ch_deu_text_signCounter', '', array('id' => 'restaurants_ch_deu_text_signCounter'));    
    wcmGUI::renderTextArea('restaurants_ch_deu_photoLegend', $bizobject->restaurants_ch_deu_photoLegend, "Légende photo");
    wcmGUI::closeFieldset();  
    
    wcmGUI::openFieldset("Traduction en Italien pour site Suisse",array('style' => 'background-color:Beige'));
    wcmGUI::renderTextField('restaurants_ch_ita_title', $bizobject->restaurants_ch_ita_title, "Titre");
    wcmGUI::renderTextArea('restaurants_ch_ita_header', $bizobject->restaurants_ch_ita_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('restaurants_ch_ita_text', $bizobject->restaurants_ch_ita_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('restaurants_ch_ita_text_signCounter', '', array('id' => 'restaurants_ch_ita_text_signCounter'));    
    wcmGUI::renderTextArea('restaurants_ch_ita_photoLegend', $bizobject->restaurants_ch_ita_photoLegend, "Légende photo");
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    
    wcmGUI::openCollapsablePane("Bloc 2"); 
	wcmGUI::openFieldset("Source Française pour site Suisse",array('style' => 'background-color:lavender'));
    wcmGUI::renderTextField('restaurants_ch_fra_title', $bizobject->restaurants_ch_fra_title, "Titre");
    wcmGUI::renderTextArea('restaurants_ch_fra_header', $bizobject->restaurants_ch_fra_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('restaurants_ch_fra_text', $bizobject->restaurants_ch_fra_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('restaurants_ch_fra_text_signCounter', '', array('id' => 'restaurants_ch_fra_text_signCounter'));    
    wcmGUI::renderTextArea('restaurants_ch_fra_photoLegend', $bizobject->restaurants_ch_fra_photoLegend, "Légende photo");
    wcmGUI::renderTextField('restaurants_ch_fra_articleLink', $bizobject->restaurants_ch_fra_articleLink, "Lien article");
    wcmGUI::renderTextField('restaurants_ch_fra_photoLink', $bizobject->restaurants_ch_fra_photoLink, "Lien Photo");
    wcmGUI::renderTextField('restaurants_ch_fra_photoCredits', $bizobject->restaurants_ch_fra_photoCredits, "Crédits Photo");
    wcmGUI::closeFieldset();
       
	wcmGUI::openFieldset("Traduction en Néerlandais pour site Pays-bas",array('style' => 'background-color:Beige'));
    wcmGUI::renderTextField('restaurants_nl_nld_title', $bizobject->restaurants_nl_nld_title, "Titre");
    wcmGUI::renderTextArea('restaurants_nl_nld_header', $bizobject->restaurants_nl_nld_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('restaurants_nl_nld_text', $bizobject->restaurants_nl_nld_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('restaurants_nl_nld_text_signCounter', '', array('id' => 'restaurants_nl_nld_text_signCounter'));    
    wcmGUI::renderTextArea('restaurants_nl_nld_photoLegend', $bizobject->restaurants_nl_nld_photoLegend, "Légende photo");
    wcmGUI::closeFieldset();
    
    wcmGUI::openFieldset("Traduction en Allemand pour site Autrichien",array('style' => 'background-color:Beige'));
    wcmGUI::renderTextField('restaurants_at_deu_title', $bizobject->restaurants_at_deu_title, "Titre");
    wcmGUI::renderTextArea('restaurants_at_deu_header', $bizobject->restaurants_at_deu_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('restaurants_at_deu_text', $bizobject->restaurants_at_deu_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));    
    wcmGUI::renderHiddenField('restaurants_at_deu_text_signCounter', '', array('id' => 'restaurants_at_deu_text_signCounter'));    
    wcmGUI::renderTextArea('restaurants_at_deu_photoLegend', $bizobject->restaurants_at_deu_photoLegend, "Légende photo");
    wcmGUI::closeFieldset(); 
    wcmGUI::closeCollapsablePane();
    
    wcmGUI::openCollapsablePane("Bloc 3"); 
    wcmGUI::openFieldset("Source Anglaise pour site Anglais",array('style' => 'background-color:lavender'));
    wcmGUI::renderTextField('restaurants_uk_int_title', $bizobject->restaurants_uk_int_title, "Titre");
    wcmGUI::renderTextArea('restaurants_uk_int_header', $bizobject->restaurants_uk_int_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('restaurants_uk_int_text', $bizobject->restaurants_uk_int_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('restaurants_uk_int_text_signCounter', '', array('id' => 'restaurants_uk_int_text_signCounter'));    
    wcmGUI::renderTextArea('restaurants_uk_int_photoLegend', $bizobject->restaurants_uk_int_photoLegend, "Légende photo");
    wcmGUI::renderTextField('restaurants_uk_int_articleLink', $bizobject->restaurants_uk_int_articleLink, "Lien article");
    wcmGUI::renderTextField('restaurants_uk_int_photoLink', $bizobject->restaurants_uk_int_photoLink, "Lien Photo");
    wcmGUI::renderTextField('restaurants_uk_int_photoCredits', $bizobject->restaurants_uk_int_photoCredits, "Crédits Photo");	
	wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    
    echo '</div>';
