<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * WCM friendly modifier plugin
 *
 * Type:     modifier<br>
 * Name:     wcm<br>
 *
 * @param string $source Source to be modified
 * @param string $modifier Name of modifier to execute
 * @param mixed  $extra Extra parameters
 *
 * @return mixed Modifier result
 */
function smarty_modifier_wcm()
{
    $args = func_get_args();

    // Retrieve modifier name (shift twice, and unshift source)
    $source = array_shift($args);
    $modifier = array_shift($args);
    array_unshift($args, $source);

    // Execute modifier
    $modifiers = smartyModifiers::getInstance();
    if (!method_exists($modifiers, $modifier))
    {
        trigger_error('Invalid wcm modifier: ' . $modifier);
        return null;
    }
    return call_user_func_array(array($modifiers, $modifier), $args);
}
?>