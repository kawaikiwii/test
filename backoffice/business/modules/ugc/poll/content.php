<?php
/**
 * Project:     WCM
 * File:        modules/ugc/poll/choices.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $choices = $bizobject->getPollChoices();
    if (!$choices) $choices = array(new pollChoice());

    echo '<div class="zone">';

    wcmModule('business/shared/metacontent');

    wcmGUI::openCollapsablePane(_BIZ_POLL_QUESTION, true);
    wcmGUI::openFieldset();
    wcmGUI::renderTextArea('text', $bizobject->text, _BIZ_POLL_QUESTION_TEXT);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    
    echo '<div id="pages" style="clear: both;">';
    foreach ($choices as $choice)
    {
        wcmModule('business/ugc/poll/choice', array('choice' => $choice));
    }
    echo '</div>';

    echo '</div>';
