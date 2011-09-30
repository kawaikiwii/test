<?php
/**
 * WCM generic function
 *
 * @param array $params Assoc array of parameters
 * @param Smarty $smarty Smarty instance
 *
 * @return mixed Result of function
 */
function smarty_function_wcm($params, &$smarty)
{
    if (!isset($params['name']))
    {
        $smarty->trigger_error("wcm: missing 'name' parameter");
        return;
    }

    // Retrieve function name
    $funcName = $params['name'];
    unset($params['name']);

    // Check function existence
    $functions = smartyFunctions::getInstance();
    if (!method_exists($functions, $funcName))
    {
        $smarty->trigger_error("wcm: invalid function name '" . $funcName . "'");
        return;
    }
    
    // Execute function
    return call_user_func_array(array($functions, $funcName), array($params, $smarty));
}
?>
