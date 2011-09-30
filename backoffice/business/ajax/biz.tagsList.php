<?php

/**
 * Project:     WCM
 * File:        biz.tagsList.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();
$config  = wcmConfig::getInstance();

// Retrieve query parameters
$values     = getArrayParameter($_REQUEST, "values", null);
$className  = getArrayParameter($_REQUEST, "className", null);
$id         = getArrayParameter($_REQUEST, "id", 0);
$rank       = getArrayParameter($_REQUEST, "rank", null);

// make sure that null is not a string
if ($rank == 'null')
    $rank = null;

$action = getArrayParameter($_REQUEST, "action", null);
$locked = getArrayParameter($_REQUEST, "locked", null);

$values      = explode('|', $values);
$valuesCount = count($values);

$category = null;
$value    = null;

if ($valuesCount == 1)
{
    $category = $values[0];
}
elseif ($valuesCount == 2)
{
    $category = $values[0];
    $value    = $values[1];
}

// instanciate business object
$bizobject = new $className($project, $id);
$stateLocked = ($locked) ? ' disabled="disabled"' : '';

if (!isset($_SESSION['tags']) || !$_SESSION['tags'])
    $_SESSION['tags'] = array();

// Prepare ajax result
$html = '';
try
{
    if (!$className)
    {
        $html = '<li>Parameter "class name" cannot be null!</li>';
    }
    elseif (!$id)
    {
        $html = '<li>Parameter "object id" cannot be null or zero!</li>';
    }
    elseif ($action == 'remove')
    {
        // remove correct value from session array
        $count = count($_SESSION['tags']) - 1;
        for ($i = $rank; $i < $count; $i++)
            $_SESSION['tags'][$i] = $_SESSION['tags'][$i + 1];

        array_pop($_SESSION['tags']);
    }
    elseif ($action == 'down')
    {
        $temp0 = $_SESSION['tags'][$rank];
        $temp1 = $_SESSION['tags'][$rank+1];
        $_SESSION['tags'][$rank+1] = $temp0;
        $_SESSION['tags'][$rank]   = $temp1;
    }
    elseif ($action == 'up')
    {
        $temp0 = $_SESSION['tags'][$rank];
        $temp1 = $_SESSION['tags'][$rank-1];
        $_SESSION['tags'][$rank-1] = $temp0;
        $_SESSION['tags'][$rank]   = $temp1;
    }
    elseif (($value != null)&&(!$locked))
    {
        $tagArray = array();
        $tagArray[$category] = $value;

        if (!in_array($tagArray, $_SESSION['tags']))
        {
            $_SESSION['tags'][] = ($tagArray);
        }
    }
    if (isset($_SESSION['tags']))
    {
        foreach($_SESSION['tags'] as $rank => $tag)
        {
            $count    = $rank + 1;
            $category = key($tag);
            $pos = $rank + 1;
            $html .= '<table border="0" bgcolor="#c0c0c0" cellpadding="2" cellspacing="1" width="100%">';
            $html .= '<tr bgcolor="f4f4f4">';
            $html .= '<td width="30" rowspan="2" class="position" align="center">' . $pos . '</td>';
            $html .= '<td width="20" height="20">';

            if ((!$locked) && ($rank != 0))
            {
                $html .= '<img class="bttn" src="img/arrow_up.gif" width="16" height="16"';
                $html .= ' alt="'._BIZ_MOVEUP.'" title="'._BIZ_MOVEUP.'"';
                $html .= ' style="cursor: pointer;"';
                $html .= ' onclick="ajaxUpdateTagsList(\''.addslashes($value).'\', \''.$className.'\', ';
                $html .= $id.', \''.$rank.'\', \'up\', \''.$locked.'\')" />';
            }

            $html .= '</td>';
            $html .= '<td rowspan="2"><strong>' . $tag[$category] . '</strong>';

            if (preg_match('/^[A-Za-z]/', $category))
                $html .= '<br /><em>' . $category . '</em>';

            $html .= '</td><td rowspan="2" width="20">';
            if (!$locked)
            {
                $html .= '<img class="bttn" src="img/actions/delete.gif" width="16"';
                $html .= ' height="16" alt="'._BIZ_DELETE.'" title="'._BIZ_DELETE.'"';
                $html .= ' style="cursor: pointer;"';
                $html .= ' onclick="ajaxUpdateTagsList(\''.addslashes($value).'\', \''.$className.'\', ';
                $html .= $id.', \''.$rank.'\', \'remove\', \''.$locked.'\')" />';
            }
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr bgcolor="f4f4f4">';
            $html .= '<td width="20" height="20">';

            if ((!$locked) && ($rank != count($_SESSION['tags']) - 1))
            {
                $html .= '<img class="bttn" src="img/arrow_down.gif" width="16" height="16"';
                $html .= ' alt="' . _BIZ_MOVEDOWN . '" title="' . _BIZ_MOVEDOWN . '"';
                $html .= ' style="cursor: pointer;"';
                $html .= ' onclick="ajaxUpdateTagsList(\''.addslashes($value).'\', \''.$className.'\', ';
                $html .= $id.', \''.$rank.'\', \'down\', \''.$locked.'\')" />';
            }

            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';
        }
    }
}
catch(Exception $e)
{
    $html = '<li>* Error : ' . $e->getMessage() .'</li>';
}

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Xml output
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

// Selected tags/categories
echo "<ajax-response>\n";
echo "<response type='item' id='selectedTags'>\n";
echo $html;
echo "</response>\n";

// Available tags
echo "<response type='item' id='taxonomyOptions'>\n";

$treeId  = 'taxonomy_' . $className . '_' . $id . '_' . ($locked ? '1' : '0');
$treeXsl = WCM_DIR . '/business/xsl/tree/taxonomies.xsl';

$tree = new wcmTree($treeId, $config['wcm.backOffice.url'] . 'business',
                    _BIZ_TAGS_AVAILABLE, "refresh.gif", 'tree_tags',
                    null, ':', $treeXsl);

if ($action == 'reload_tags')
{
    $tree->refresh();
    $tree->saveIntoSession();
}
else
{
    $tree->initFromSession($treeId);
}

echo $tree->renderHTML($treeXsl);
echo "</response>\n";

// Suggested categories
echo "<response type='item' id='categoryOptions'>\n";

$treeId  = 'categories_' . $className . '_' . $id . '_' . ($locked ? '1' : '0');
$treeXsl = WCM_DIR . '/business/xsl/tree/categories.xsl';

$tree = new wcmTree($treeId, $config['wcm.backOffice.url'] . 'business',
                    _BIZ_CATEGORIES_SUGGESTED, "refresh.gif", 'tree_categories',
                    null, ':', $treeXsl);

if ($action == 'reload_categories')
{
    $semanticData = fetchSemanticData($bizobject, array('NCategorizer'));
    setSessionSemanticData($bizobject, 'NCategorizer', $semanticData['xmlSemanticData']);

    $tree->refresh();
    $tree->saveIntoSession();
}
else
{
    $tree->initFromSession($treeId);
}
                    
echo $tree->renderHTML($treeXsl);
echo "</response>\n";

echo "</ajax-response>\n";

?>
