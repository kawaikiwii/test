<?php
/**
 * Project:     WCM
 * File:        modules/viaMichelin/properties.php
 *
 * @copyright   (c)2011 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    echo '<div class="zone">';
    
   wcmGUI::openCollapsablePane("Informations Générales");
    wcmGUI::openFieldset();
    // champ permettant d'identifier l'objet lors d'une recherche
    wcmGUI::renderTextField('title', $bizobject->title, "Libellé général");
     // initialisation de la date
    if ($bizobject->publication_date != NULL)
		$dateInit = $bizobject->publication_date;
	else
		$dateInit = date('Y-m-d H:i:s');
		
    wcmGUI::renderDateField('publication_date', $dateInit, "Date de publication", 'datetime');
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    
    echo '</div>';
