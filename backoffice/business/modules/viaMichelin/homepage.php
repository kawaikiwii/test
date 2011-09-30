<?php
/**
 * Project:     WCM
 * File:        modules/viaMichelin/homepage.php
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
    wcmGUI::renderTextField('homepage_be_fra_title', $bizobject->homepage_be_fra_title, "Titre");
    wcmGUI::renderTextArea('homepage_be_fra_header', $bizobject->homepage_be_fra_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('homepage_be_fra_text', $bizobject->homepage_be_fra_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('homepage_be_fra_text_signCounter', '', array('id' => 'homepage_be_fra_text_signCounter'));    
    wcmGUI::renderTextArea('homepage_be_fra_photoLegend', $bizobject->homepage_be_fra_photoLegend, "Légende photo");
    wcmGUI::renderTextField('homepage_be_fra_articleLink', $bizobject->homepage_be_fra_articleLink, "Lien article");
    wcmGUI::renderTextField('homepage_be_fra_photoLink', $bizobject->homepage_be_fra_photoLink, "Lien Photo");
    wcmGUI::renderTextField('homepage_be_fra_photoCredits', $bizobject->homepage_be_fra_photoCredits, "Crédits Photo");
    wcmGUI::closeFieldset();
    
    wcmGUI::openFieldset("Traduction en Flamand pour site Belge",array('style' => 'background-color:Beige'));
    wcmGUI::renderTextField('homepage_be_nld_title', $bizobject->homepage_be_nld_title, "Titre");
    wcmGUI::renderTextArea('homepage_be_nld_header', $bizobject->homepage_be_nld_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('homepage_be_nld_text', $bizobject->homepage_be_nld_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('homepage_be_nld_text_signCounter', '', array('id' => 'homepage_be_nld_text_signCounter'));    
    wcmGUI::renderTextArea('homepage_be_nld_photoLegend', $bizobject->homepage_be_nld_photoLegend, "Légende photo");
    wcmGUI::closeFieldset();
    
    wcmGUI::openFieldset("Traduction en Allemand pour site Suisse",array('style' => 'background-color:Beige'));
    wcmGUI::renderTextField('homepage_ch_deu_title', $bizobject->homepage_ch_deu_title, "Titre");
    wcmGUI::renderTextArea('homepage_ch_deu_header', $bizobject->homepage_ch_deu_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('homepage_ch_deu_text', $bizobject->homepage_ch_deu_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('homepage_ch_deu_text_signCounter', '', array('id' => 'homepage_ch_deu_text_signCounter'));    
    wcmGUI::renderTextArea('homepage_ch_deu_photoLegend', $bizobject->homepage_ch_deu_photoLegend, "Légende photo");
    wcmGUI::closeFieldset();  
    
    wcmGUI::openFieldset("Traduction en Italien pour site Suisse",array('style' => 'background-color:Beige'));
    wcmGUI::renderTextField('homepage_ch_ita_title', $bizobject->homepage_ch_ita_title, "Titre");
    wcmGUI::renderTextArea('homepage_ch_ita_header', $bizobject->homepage_ch_ita_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('homepage_ch_ita_text', $bizobject->homepage_ch_ita_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('homepage_ch_ita_text_signCounter', '', array('id' => 'homepage_ch_ita_text_signCounter'));    
    wcmGUI::renderTextArea('homepage_ch_ita_photoLegend', $bizobject->homepage_ch_ita_photoLegend, "Légende photo");
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    
    wcmGUI::openCollapsablePane("Bloc 2"); 
	wcmGUI::openFieldset("Source Française pour site Suisse",array('style' => 'background-color:lavender'));
    wcmGUI::renderTextField('homepage_ch_fra_title', $bizobject->homepage_ch_fra_title, "Titre");
    wcmGUI::renderTextArea('homepage_ch_fra_header', $bizobject->homepage_ch_fra_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('homepage_ch_fra_text', $bizobject->homepage_ch_fra_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('homepage_ch_fra_text_signCounter', '', array('id' => 'homepage_ch_fra_text_signCounter'));    
    wcmGUI::renderTextArea('homepage_ch_fra_photoLegend', $bizobject->homepage_ch_fra_photoLegend, "Légende photo");
    wcmGUI::renderTextField('homepage_ch_fra_articleLink', $bizobject->homepage_ch_fra_articleLink, "Lien article");
    wcmGUI::renderTextField('homepage_ch_fra_photoLink', $bizobject->homepage_ch_fra_photoLink, "Lien Photo");
    wcmGUI::renderTextField('homepage_ch_fra_photoCredits', $bizobject->homepage_ch_fra_photoCredits, "Crédits Photo");
    wcmGUI::closeFieldset();
       
	wcmGUI::openFieldset("Traduction en Néerlandais pour site Pays-bas",array('style' => 'background-color:Beige'));
    wcmGUI::renderTextField('homepage_nl_nld_title', $bizobject->homepage_nl_nld_title, "Titre");
    wcmGUI::renderTextArea('homepage_nl_nld_header', $bizobject->homepage_nl_nld_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('homepage_nl_nld_text', $bizobject->homepage_nl_nld_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('homepage_nl_nld_text_signCounter', '', array('id' => 'homepage_nl_nld_text_signCounter'));    
    wcmGUI::renderTextArea('homepage_nl_nld_photoLegend', $bizobject->homepage_nl_nld_photoLegend, "Légende photo");
    wcmGUI::closeFieldset();
    
    wcmGUI::openFieldset("Traduction en Allemand pour site Autrichien",array('style' => 'background-color:Beige'));
    wcmGUI::renderTextField('homepage_at_deu_title', $bizobject->homepage_at_deu_title, "Titre");
    wcmGUI::renderTextArea('homepage_at_deu_header', $bizobject->homepage_at_deu_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('homepage_at_deu_text', $bizobject->homepage_at_deu_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));    
    wcmGUI::renderHiddenField('homepage_at_deu_text_signCounter', '', array('id' => 'homepage_at_deu_text_signCounter'));    
    wcmGUI::renderTextArea('homepage_at_deu_photoLegend', $bizobject->homepage_at_deu_photoLegend, "Légende photo");
    wcmGUI::closeFieldset(); 
    wcmGUI::closeCollapsablePane();
    
    wcmGUI::openCollapsablePane("Bloc 3"); 
    wcmGUI::openFieldset("Source Anglaise pour site Anglais",array('style' => 'background-color:lavender'));
    wcmGUI::renderTextField('homepage_uk_int_title', $bizobject->homepage_uk_int_title, "Titre");
    wcmGUI::renderTextArea('homepage_uk_int_header', $bizobject->homepage_uk_int_header, "Entête", array('rows'=>3));
    wcmGUI::renderEditableField('homepage_uk_int_text', $bizobject->homepage_uk_int_text, "Description", null, array('width' => 700, 'height' => 400, 'language' => 'fr', 'spellchecker_languages' => $spellLanguage));
	wcmGUI::renderHiddenField('homepage_uk_int_text_signCounter', '', array('id' => 'homepage_uk_int_text_signCounter'));    
    wcmGUI::renderTextArea('homepage_uk_int_photoLegend', $bizobject->homepage_uk_int_photoLegend, "Légende photo");
    wcmGUI::renderTextField('homepage_uk_int_articleLink', $bizobject->homepage_uk_int_articleLink, "Lien article");
    wcmGUI::renderTextField('homepage_uk_int_photoLink', $bizobject->homepage_uk_int_photoLink, "Lien Photo");
    wcmGUI::renderTextField('homepage_uk_int_photoCredits', $bizobject->homepage_uk_int_photoCredits, "Crédits Photo");	
	wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    
    echo '</div>';
