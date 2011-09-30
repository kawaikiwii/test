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
 * @param string    $title  creates a web safe filename
 *
 * @return string 
 */
function smarty_modifier_safe_filename($title)
{
    return safeFileName($title);
}
?>
