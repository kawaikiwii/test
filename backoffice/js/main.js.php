<?php
/*
 * Project:     WCM
 * File:        main.js.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
if ($session->getCurrentAction() == 'login' || $session->getCurrentAction() == 'logout')
    return;

/**
* Generates a SCRIPT element to include a given JavaScript file
*
* @param string     $baseUrl The base URL for generated links
*
* @param string $filePath The path of the file for which to generate
*                         the SCRIPT element, relative to the Web
*                         application URL
*
* @return string The generated 'document.write' function call
*
*/
function jsIncludeFile($baseUrl, $filePath)
{
	return "<script language='JavaScript' type='text/javascript' src='" . $baseUrl . $filePath . "'></script>\n";
}

/**
* Recursively invokes the jsIncludeFile function for each JavaScript
* file under a given directory.
*
* @param string     $baseUrl The base URL for generated links
* @param string     $dirPath The path of the directory
* @param array      $ignore  List of entries to ignore (default: null)
*
* @return string The generated 'document.write' function call
*
*/
function jsIncludeDir($baseUrl, $dirPath, $ignore = null)
{
	$js = '';

	if (is_dir($dirPath))
	{
		$jsExts = array('js', 'js.php');

		$entries = scandir($dirPath);
		foreach ($entries as $entry)
		{
			if ($entry != '.' && $entry != '..' && (!$ignore || !in_array($entry, $ignore)))
			{
				$entryPath = $dirPath.$entry;
				if (is_dir($entryPath))
				{
					//$js .= jsIncludeDir($baseUrl, $entryPath, $ignore);
					$js .= jsIncludeDir($baseUrl, $entryPath . '/', $ignore);
				}
				else
				{
					foreach ($jsExts as $jsExt)
					{
						if (mb_substr($entry, mb_strlen($entry) - mb_strlen($jsExt)) == $jsExt)
						{
							$js .= jsIncludeFile($baseUrl, $entryPath);
							break;
						}
					}
				}
			}
		}
	}

	return $js;
}

if (!isset($_SESSION['wcm_MainJS']))
{
    // Change to the Web application's root directory
    $oldWd = getcwd();
    chdir(WCM_DIR);

    // Base URL for generated links corresponds to current working directory
    $baseUrl = $config['wcm.backOffice.url'];

    // Include JS files
    $js = '';

    $js .= jsIncludeFile($baseUrl, 'includes/js/prototype.js');
    $js .= jsIncludeFile($baseUrl, 'includes/js/rico.js');
    $js .= jsIncludeFile($baseUrl, 'includes/js/wcmAjaxController.js');
    $js .= jsIncludeFile($baseUrl, 'includes/js/wcmFormValidator.js');
    $js .= jsIncludeFile($baseUrl, 'includes/js/wcmActionController.js');
    $js .= jsIncludeFile($baseUrl, 'includes/js/wcmGetFulltext.js');
    $js .= jsIncludeFile($baseUrl, 'includes/js/wcm.js');
    $js .= jsIncludeFile($baseUrl, 'includes/js/widget.js');
    $js .= jsIncludeFile($baseUrl, 'includes/js/relations.js');
    $js .= jsIncludeFile($baseUrl, 'includes/js/menus.js');
    $js .= jsIncludeFile($baseUrl, 'includes/js/tabpane.js');
    $js .= jsIncludeFile($baseUrl, 'includes/js/tree_tags.js');
    $js .= jsIncludeFile($baseUrl, 'includes/js/tree_categories.js');
    $js .= jsIncludeFile($baseUrl, 'includes/js/calendar/calendar.js');
    //$js .= jsIncludeFile($baseUrl, 'includes/js/calendar/lang/calendar-'.$session->getLanguage().'.js');
    $js .= jsIncludeFile($baseUrl, 'includes/js/calendar/calendar-setup.js');
    $js .= jsIncludeFile($baseUrl, 'includes/js/scriptaculous.js?load=effects,dragdrop,builder,sortable,controls');
    $js .= jsIncludeFile($baseUrl, 'includes/tinymce/tiny_mce.js');
    $js .= jsIncludeFile($baseUrl, 'includes/js/modalbox.js');
    $js .= jsIncludeFile($baseUrl, 'includes/js/behaviour.js');
    $js .= jsIncludeFile($baseUrl, 'includes/js/ModalPopup.js');
    //$js .= jsIncludeFile($baseUrl, 'includes/FusionChartsFree/FusionCharts.js');
    $js .= jsIncludeFile($baseUrl, 'includes/js/wcmListe.js');
	$js .= jsIncludeFile($baseUrl, 'includes/js/extra.js');
    //$js .= jsIncludeFile($baseUrl, 'business/ajax/export/biz.relaxTask.js'); //automatiquement recupere dans includes ajax plus bas
    // Include Ajax autocomplete JS files
    //$js .= jsIncludeFile($baseUrl, 'includes/js/autocomplete/jquery.tokeninput.js');
	
    // Load JAVASCRIPT resources
    //$js .= jsIncludeFile($baseUrl, 'languages/'.$session->getLanguage().'.js');
    //$js .= jsIncludeFile($baseUrl, 'business/languages/'.$session->getLanguage().'.js');

    // Include Ajax JS files
    $js .= jsIncludeDir($baseUrl, 'ajax/');
    $js .= jsIncludeDir($baseUrl, 'business/ajax/');

    // Change back to the previous working directory
    chdir($oldWd);

    // Create Sys and Biz Ajax controllers
    $js .=  "<script language='JavaScript' type='text/javascript'>\n" .
            "var wcmBaseURL = '" . $baseUrl . "';\n" .
            "var wcmSysAjaxController = new NcmAjaxController(" .
                                        "'wcmSysAjaxController', " .
                                        "'".$config['wcm.backOffice.url']."ajax', " .
                                        "'controller'" . ");\n" .
            "var wcmBizAjaxController = new NcmAjaxController(" .
                                        "'wcmBizAjaxController', " .
                                        "'".$config['wcm.backOffice.url']."business/ajax', " .
                                        "'controller'" . ");\n" .
            "</script>";

    $_SESSION['wcm_MainJS'] = $js;
}
echo $_SESSION['wcm_MainJS'];

chdir(WCM_DIR);

// Base URL for generated links corresponds to current working directory
$baseUrl = $config['wcm.backOffice.url'];
echo jsIncludeFile($baseUrl, 'includes/js/calendar/lang/calendar-'.$session->getLanguage().'.js');
echo jsIncludeFile($baseUrl, 'languages/'.$session->getLanguage().'.js');
echo jsIncludeFile($baseUrl, 'business/languages/'.$session->getLanguage().'.js');