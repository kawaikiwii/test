<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty lower modifier plugin
 *
 * Type:     modifier<br>
 * Name:     base64_decode<br>
 * Purpose:  decode base 64 string
 * @link http://www.php.net/manual/fr/function.base64-decode.php
 *
 * @author   relaxNews
 * @param string
 * @return string
 */
function smarty_modifier_base64_decode($string)
{
    return base64_decode($string);
}

?>
