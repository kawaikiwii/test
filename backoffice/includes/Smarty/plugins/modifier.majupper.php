<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty upper modifier plugin
 *
 * Type:     modifier<br>
 * Name:     majupper<br>
 * Purpose:  convert string to uppercase with accents
 * @link http://smarty.php.net/manual/en/language.modifier.upper.php
 *          upper (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @return string
 */
function smarty_modifier_majupper($string)
{

	$string = strtoupper($string);
	$string = strtr($string, "äâàáåãéèëêòóôõöøìíîïùúûüýñçþÿæœðø",	"ÄÂÀÁÅÃÉÈËÊÒÓÔÕÖØÌÍÎÏÙÚÛÜÝÑÇÞÝÆŒÐØ");

    return $string;
}

?>
