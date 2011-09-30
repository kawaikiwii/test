<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty strip extra chars (and wrap lines) outputfilter plugin
 *
 */
function smarty_outputfilter_wrappedstrip($source, &$smarty)
{
	// Check if filter is enable
	if ($smarty->enableWrappedStripFilter)
	{
		$result = preg_replace('!\s+!', ' ', $source);
		// $result = wordwrap($result, 1024);
	    return $result;
	}
	return $source;
}
?>