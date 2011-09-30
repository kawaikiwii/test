<?php
/**
 * Better include template function
 *
 * @param array $params List of named parameters passed to the function
 * @param Smarty $smarty Reference to the instance of smarty
 */
function smarty_function_include_template($params, &$smarty)
{
    // Is this a relative or absolute path?
    if ($params['file']{0} == '/')
    {
        $tpl = $params['file'];
    } else {
        $tpl = $smarty->template_dir.'/'.$params['file'];
    }
    
    if (file_exists($tpl))
    {
        return $smarty->fetch($tpl);
    } else {
        
        if ($params['fallback']{0} == '/')
        {
            $tpl = $params['fallback'];
        } else {
            $tpl = $smarty->template_dir.'/'.$params['fallback'];
        }
        return $smarty->fetch($tpl);
    }
}
?>