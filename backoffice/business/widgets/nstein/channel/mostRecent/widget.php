<?php

/**
 *  Widget
 *
 */

class wcmWidgetNsteinChannelMostRecent extends wcmWidget {

    public function getLabel()
    {
        return 'Most recent articles';
    }

    public function displaySettings()
    {
        return captureOutput(array($this, 'renderSettings'));
    }
    
    public function renderSettings()
    {
        echo '<ul>';
        wcmFormGUI::renderDropdownField('nb', range(0,10), intval(getArrayParameter($this->settings, 'nb', 6)), _BIZ_LIMIT_TO);
        echo '</ul>';
    }
}

