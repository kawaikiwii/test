<?php
/**
 * Project:     WCM
 * File:        modules/tme/footprint.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    /* IMPORTANT !! Utile car on perd les infos si on upload des photos */
	if(isset($_SESSION['wcmActionMain']) && $_SESSION['wcmAction'] != $_SESSION['wcmActionMain'])
    	$_SESSION['wcmAction'] = $_SESSION['wcmActionMain'];
    
    $bizobject = wcmMVC_Action::getContext();

    $_SESSION['wcm']['footprint']['context'] = $bizobject;
    
    echo '<div class="zone">';


    // sentiment analysis
    $tone = $bizobject->semanticData->tone;
    if ($tone > 10) $toneLabel = _BIZ_TME_SENTIMENT_TONE_POSITIVE;
    elseif ($tone < 10) $toneLabel = _BIZ_TME_SENTIMENT_TONE_NEGATIVE;
    else $toneLabel = _BIZ_TME_SENTIMENT_TONE_NEUTRAL;
    
    $subjectivity = $bizobject->semanticData->subjectivity;
    if ($subjectivity > 30) $subjectivityLabel = _BIZ_TME_SENTIMENT_SUBJECTIVITY_OPINION;
    else $subjectivityLabel = _BIZ_TME_SENTIMENT_SUBJECTIVITY_FACT;

    wcmGUI::openCollapsablePane(_BIZ_TME_SENTIMENT);
    wcmGUI::openFieldset();
    echo '<li><label>' . _BIZ_TME_SENTIMENT_TONE . '</label><span>' . $toneLabel . ' (' . $tone . '%)</span></li>';
    echo '<li><label>' . _BIZ_TME_SENTIMENT_SUBJECTIVITY . '</label><span>' . $subjectivityLabel . ' (' . $subjectivity . '%)</span></li>';
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();

    // concepts and entities
    wcmGUI::openCollapsablePane(_BIZ_TME);
    wcmGUI::openFieldset(_BIZ_TME_ENTITITES_ON);
    wcmGUI::renderListField('_semanticData[ON]', array_keys($bizobject->semanticData->ON), null, null,_BIZ_TME_ENTITITES_ON);
    wcmGUI::closeFieldset();
    wcmGUI::openFieldset(_BIZ_TME_ENTITITES_PN);
    wcmGUI::renderListField('_semanticData[PN]', array_keys($bizobject->semanticData->PN), null, null,_BIZ_TME_ENTITITES_PN);
    wcmGUI::closeFieldset();
    wcmGUI::openFieldset(_BIZ_TME_ENTITITES_GL);
    wcmGUI::renderListField('_semanticData[GL]', array_keys($bizobject->semanticData->GL), null, null,_BIZ_TME_ENTITITES_GL);
    wcmGUI::closeFieldset();
    wcmGUI::openFieldset(_BIZ_TME_CONCEPTS);
    wcmGUI::renderListField('_semanticData[concepts]', array_keys($bizobject->semanticData->concepts), null, null,_BIZ_TME_CONCEPTS);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
	
	//print_r($bizobject);

    echo '</div>';
    
?>
