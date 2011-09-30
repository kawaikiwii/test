<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty creates friendly link modifier plugin
 *
 * Type:     modifier<br>
 * Name:     urlfriendly<br>
 *
 * @param biz_object $bizObject
 *
 * @return string 
 */
function smarty_modifier_link($bizObject)
{
    return safeLink($bizObject);
}
?>
