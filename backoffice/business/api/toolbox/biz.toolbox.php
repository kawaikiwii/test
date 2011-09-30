<?php
/**
 * Project:     WCM
 * File:        biz.toolbox.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

function xmlEntities($str) {
    $xml = array('&#34;', '&#34;','&#34;','&#38;', '&#38;', '&#60;', '&#62;', '&#160;', '&#161;', '&#162;', '&#163;', '&#164;', '&#165;', '&#166;', '&#167;', '&#168;', '&#169;', '&#170;', '&#171;', '&#172;', '&#173;', '&#174;', '&#175;', '&#176;', '&#177;', '&#178;', '&#179;', '&#180;', '&#181;', '&#182;', '&#183;', '&#184;', '&#185;', '&#186;', '&#187;', '&#188;', '&#189;', '&#190;', '&#191;', '&#192;', '&#193;', '&#194;', '&#195;', '&#196;', '&#197;', '&#198;', '&#199;', '&#200;', '&#201;', '&#202;', '&#203;', '&#204;', '&#205;', '&#206;', '&#207;', '&#208;', '&#209;', '&#210;', '&#211;', '&#212;', '&#213;', '&#214;', '&#215;', '&#216;', '&#217;', '&#218;', '&#219;', '&#220;', '&#221;', '&#222;', '&#223;', '&#224;', '&#225;', '&#226;', '&#227;', '&#228;', '&#229;', '&#230;', '&#231;', '&#232;', '&#233;', '&#234;', '&#235;', '&#236;', '&#237;', '&#238;', '&#239;', '&#240;', '&#241;', '&#242;', '&#243;', '&#244;', '&#245;', '&#246;', '&#247;', '&#248;', '&#249;', '&#250;', '&#251;', '&#252;', '&#253;', '&#254;', '&#255;');
    $html = array('&lsquo;','&rsquo;','&quot;', '&amp;', '&amp;', '&lt;', '&gt;', '&nbsp;', '&iexcl;', '&cent;', '&pound;', '&curren;', '&yen;', '&brvbar;', '&sect;', '&uml;', '&copy;', '&ordf;', '&laquo;', '&not;', '&shy;', '&reg;', '&macr;', '&deg;', '&plusmn;', '&sup2;', '&sup3;', '&acute;', '&micro;', '&para;', '&middot;', '&cedil;', '&sup1;', '&ordm;', '&raquo;', '&frac14;', '&frac12;', '&frac34;', '&iquest;', '&Agrave;', '&Aacute;', '&Acirc;', '&Atilde;', '&Auml;', '&Aring;', '&AElig;', '&Ccedil;', '&Egrave;', '&Eacute;', '&Ecirc;', '&Euml;', '&Igrave;', '&Iacute;', '&Icirc;', '&Iuml;', '&ETH;', '&Ntilde;', '&Ograve;', '&Oacute;', '&Ocirc;', '&Otilde;', '&Ouml;', '&times;', '&Oslash;', '&Ugrave;', '&Uacute;', '&Ucirc;', '&Uuml;', '&Yacute;', '&THORN;', '&szlig;', '&agrave;', '&aacute;', '&acirc;', '&atilde;', '&auml;', '&aring;', '&aelig;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirc;', '&iuml;', '&eth;', '&ntilde;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;', '&divide;', '&oslash;', '&ugrave;', '&uacute;', '&ucirc;', '&uuml;', '&yacute;', '&thorn;', '&yuml;');
    $str = str_replace($html, $xml, $str);
    $str = str_ireplace($html, $xml, $str);
    return $str;
}

/**
 * Returns a human-readable label for an object
 * (usually the name of the title property)
 *
 * @param wcmObject $wcmObject
 */
function getObjectLabel($wcmObject)
{
    if (!$wcmObject)
        return null;

    if ($wcmObject instanceof webuser)
        return $wcmObject->firstname . ' ' . $wcmObject->lastname;

    if (isset($wcmObject->title))
        return getConst($wcmObject->title);

    if (isset($wcmObject->name))
        return getConst($wcmObject->name);

    // Default label
    return $wcmObject->getClass() . ' ' . $wcmObject->id;
}

/**
 * Get class list
 *
 * returns assoc array, all bizclasses
 */
function getClassList()
{
    $project = wcmProject::getInstance();
    $bizlogic = new wcmBizlogic($project);

    $classList = array();
    foreach ($bizlogic->getBizclasses() as $bizclass)
    {
        $classList[$bizclass->className] = getConst($bizclass->name);
    }
    asort($classList);

    return $classList;
}

/**
* List of all available dates
*/
function getDateList()
{
    return array(
        "7"     => _BIZ_NEXT_7_DAYS,
        "3"     => _BIZ_NEXT_3_DAYS,
        "1"     => _BIZ_TOMORROW,
        "0"     => _BIZ_TODAY,
        "-1"    => _BIZ_YESTERDAY,
        "-3"    => _BIZ_LAST_3_DAYS,
        "-7"    => _BIZ_LAST_7_DAYS,
        "-30"   => _BIZ_LAST_MONTH,
        "-365"  => _BIZ_LAST_YEAR
        );
}

/**
* List all kind of sorting
*/
function getSortList()
{
    $project = wcmProject::getInstance();
    $config  = wcmConfig::getInstance();

    $title = ($config['wcm.search.engine'] == 'textml') ? 'title_sort' : 'title';
    return array(
        "id"            => _ID,
        "createdAt"     => _CREATION_DATE,
        "modifiedAt"    => _MODIFICATION_DATE,
        $title          => _BIZ_TITLE
    );
}

/**
* List (assoc array) of moderation kinds
*/
function getModerationKindList()
{
    return array(
        "a_priori"     => _BIZ_A_PRIORI,
        "a_posteriori" => _BIZ_A_POSTERIORI,
    );
}

/**
* List contribution states
*/
function getContributionStateList()
{
    return array(
        "none"   => _BIZ_CONTRIBUTION_NONE,
        "closed" => _BIZ_CONTRIBUTION_CLOSED,
        "open"   => _BIZ_CONTRIBUTION_OPEN,
    );
}

/**
 * Delete a folder and all its contents
 * @param       string   $dirname    Directory to delete
 * @return      bool     Returns TRUE on success, FALSE on failure
 */
function rmdir_r($dirname)
{
	// Check
	if (!file_exists($dirname))
		return false;

	$currentDirectory = opendir($dirname);
	// Loop through the folder
	while (($file = readdir($currentDirectory)) !== false)
	{
		// File delete
		if (is_file($dirname."/".$file))
			unlink($dirname."/".$file);

		// Recurse
		if ((is_dir($dirname."/".$file))&&($file[0] != '.'))
			rmdir_r($dirname."/".$file);
	}
	closedir($currentDirectory);
	return rmdir($dirname);
}