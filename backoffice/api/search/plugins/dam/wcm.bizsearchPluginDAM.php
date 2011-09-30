<?php
/**
 * Project:     WCM
 * File:        api/search/plugins/wcm.bizsearchPluginDAM.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * The wcmBizsearchPluginDAM class implements a DAM-oriented full-text
 * search on bizobjects.
 */
class wcmBizsearchPluginDAM extends wcmBizsearchPluginTextML
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $config = wcmConfig::getInstance();
        parent::__construct($config['dam.textml']);
    }
}
?>