<?php
/**
 * Project:     WCM
 * File:        api/search/plugins/wcm.bizsearchPluginMSSQL.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The wcmBizsearchPluginMSSQL class implements an MSSQL-oriented
 * full-text search on bizobjects.
 */
class wcmBizsearchPluginMSSQL extends wcmBizsearchPluginDB
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
        return " CONTAINS (\"$name\", '\"" . str_replace("'","''", $value) . "\"' ) ";
    }
}
?>