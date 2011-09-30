<?php
/**
 * Project:     WCM
 * File:        api/search/plugins/wcm.bizsearchPluginMySQL.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The wcmBizsearchPluginMySQL class implements a MySQL-oriented
 * full-text search on bizobjects.
 */
class wcmBizsearchPluginMySQL extends wcmBizsearchPluginDB
{
    /**
     * Computes the SQL full-text 'where' clause fragment for a given
     * search parameter.
     *
     * @param string $name  The parameter name
     * @param string $value The parameter value
     *
     * @return string The SQL full-text 'where' clause fragment
     */
    public function getMatchCondition($name, $value)
    {
        return " MATCH (`$name`) AGAINST ('".str_replace("'","''", $value)."' IN BOOLEAN MODE) ";
    }
}
?>