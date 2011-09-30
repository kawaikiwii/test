<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Translate a constant
 *
 * Type:     modifier<br>
 * Name:     constant<br>
 *
 * @param  string $string
 *
 * @return string 
 */
function smarty_modifier_constant($string)
{
    return getConst($string);
}