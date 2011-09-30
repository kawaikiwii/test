<?php
/**
 * Project:     WCM
 * File:        modules/ugc/poll/choice.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    $bizobject = wcmMVC_Action::getContext();
    $pollChoice = getArrayParameter($params, 'choice', new pollChoice());

    $menus = array(
                    getConst(_ADD_POLL_CHOICE) => '\'' . wcmModuleURL('business/ugc/poll/choice') . '\', null, this',
                    getConst(_DELETE_POLL_CHOICE)    => 'removeChoice'
                    );
    
    $uniqid = uniqid('pollChoice_text_');

    $info = '<ul>';
    foreach ($menus as $title => $action)
    {
        if ($title == getConst(_DELETE_POLL_CHOICE))
        {
            $info .= '<li><a href="#" onclick="'.$action.'(this, \''.$uniqid.'\'); return false;">' . $title . '</a></li>';
        }
        else
        {        
           $info .= '<li><a href="#" onclick="addChoice(' . $action . '); return false;">' . $title . '</a></li>';
        }

    }
    $info .= '</ul>';

    // Cut title to 50 chars max
    $title = _POLL_CHOICE . ' :: ' . strip_tags($pollChoice->text);
    if (strlen($title) > 50)
        $title = substr($title, 0, 50) . '...';
    // Render form
    wcmGUI::openCollapsablePane($title, true, $info);
    wcmGUI::openFieldset(_POLL_CHOICE);
    wcmGUI::renderHiddenField('pollId', $bizobject->id);
    wcmGUI::renderTextField('pollChoice_text[]', $pollChoice->text, _BIZ_TEXT, array('id' => $uniqid));
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
