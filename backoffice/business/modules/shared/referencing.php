<?php
/**
 * Project:     WCM
 * File:        modules/editorial/shared/referencing.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();

    echo '<div class="zone">';

    wcmGUI::openCollapsablePane(_BIZ_PERMALINKS);
    wcmGUI::openFieldset(_BIZ_DEFAULT_PERMALINK);
    
    // get default permalink
    if (!$bizobject->id)
    {
        $defaultPermalink = '';
    }
    else if (isset($bizobject->permalinks))
    {
        $defaultPermalink = $bizobject->permalinks;
    }
    else
    {
        $defaultPermalink = smartyModifiers::getInstance()->permalink($bizobject->getAssocArray(false));
    }

    $modifiers = smartyModifiers::getInstance();
    echo '<ul><li>';
    echo '<input type="text" name="permalinks" id="permalinks" value="' . $defaultPermalink . '"/>';
    echo '<a href="javascript://" onclick="$(\'permalinks\').setValue(\'' 
         . $modifiers->permalink($bizobject->getAssocArray(false))
         . '\');" class="list-builder">' . _BIZ_RESET_SYSTEM_DEFAULT . '</a>';
    echo '<a href="' . $modifiers->url($bizobject->getAssocArray(false))
         . '?preview=1" target="web" class="list-builder">' . _BIZ_PREVIEW . '</a>';
    echo '</li></ul>';
    wcmGUI::closeFieldset();
/*
 * @todo: implement multiple permalinks in the API
 *
    
    wcmGUI::openFieldset(_BIZ_ADDITIONAL_PERMALINKS);
    wcmGUI::renderListField('_permalinks', array());
    wcmGUI::closeFieldset();
*/    
    wcmGUI::closeCollapsablePane();

    wcmGUI::openCollapsablePane(_BIZ_OUTBOUND_LINKS);
    
    wcmModule('business/relationship/main', 
        array('kind' => wcmBizrelation::IS_RELATED_TO,
              'destinationClass' => '',
              'classFilter' => '',
              'resultStyle' => 'grid',
              'prefix' => '_wcm_ref_',
              'searchEngine' => $config['wcm.search.engine'],
              'uid' => 'reference',
              'createTab' => false));

    /**
     * @todo
     * similar to permalinks - show list of outgoing links (eg related)
     * needs URL and title
     * add a ping button to check if link is !404
     */
    
    echo "<div style='clear:both;' >&nbsp;</div>";

    wcmGUI::closeCollapsablePane();

/**
 * @todo: implements inbound links

    wcmGUI::openCollapsablePane(_BIZ_INBOUND_LINKS);
    wcmGUI::openFieldset();
    wcmGUI::closeFieldset();
*/
    wcmGUI::closeCollapsablePane();

    echo '</div>';
