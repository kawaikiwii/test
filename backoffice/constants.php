<?php
/**
 * Project:     WCM
 * File:        constants.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/*
 * The following constants are used by the WCM API and Web
 * application.
 *
 * To override the default value for a given constant, simply change
 * its value appropriately.
 */
if (!defined('WCM_DIR'))
{
    /**
     * The WCM version number
     *
     */
    define('WCM_VERSION', '4.0.0.9716');
    define('DEVELOPMENT_TEAM', '
        Agostino Deligia,
        Alex Dowgailenko,
        Bashar Al Fallouji,
        Chantal Ide,
        Cym Gomery,
        David Giamporcaro,
        Eric Williams,
        Francois Lefaivre,
        Jean-François Garneau,
        Jean-Michel Texier,
        Meng To,
        Nizar Samaha,
        Pierrick Charron,
        Radu Mirea,
        Thomas Nivot,
        Wendy MacKenzie,
        Zaher Zaraket
    ');

    /**
     * The absolute path of the root of the WCM Web application.
     *
     */
    define('WCM_DIR', dirname(__FILE__));

    /**
     * The absolute path of the "includes" directory.
     *
     */
    define('INCLUDES_DIR', WCM_DIR . '/includes');

    /**
     * The absolute path of the Creole library files.
     *
     */
    define('CREOLE_DIR', INCLUDES_DIR . '/Creole/classes');

    /**
     * The absolute path of the Apache Solr PHP Client library files.
     *
     */
    define('APACHE_SOLR_DIR', INCLUDES_DIR . '/SolrPhpClient');

    /**
     * The absolute path of the Smarty library files.
     *
     * Note the trailing slash (/).
     *
     */
    define('SMARTY_DIR', INCLUDES_DIR . '/Smarty/');
}

// Ensure Creole is loaded!
if (!strpos(CREOLE_DIR, ini_get('include_path')))
{
    // Update the include_path settings
    ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR
                            . APACHE_SOLR_DIR . PATH_SEPARATOR
                            . CREOLE_DIR . PATH_SEPARATOR
                            . INCLUDES_DIR);
}