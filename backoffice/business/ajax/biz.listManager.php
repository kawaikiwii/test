<?php
/**
 * Project:     WCM
 * File:        biz.listManager.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Manage child list with AJAX call
 *
 */

// Initialize system
require_once dirname(__FILE__) . '/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Get parameters
$ajaxMessage    = getArrayParameter($_REQUEST, 'ajaxMessageId', 'ajaxMessage');
$listDisplayId  = getArrayParameter($_REQUEST, 'listDisplayId', 'listDisplay');
$action         = getArrayParameter($_REQUEST, 'action', 'show');
$bizobjectId    = getArrayParameter($_REQUEST, 'bizobjectId', null);
$bizobjectClass = getArrayParameter($_REQUEST, 'bizobjectClass', null);
$parentId       = getArrayParameter($_REQUEST, 'parentId', null);
$parentObject   = getArrayParameter($_REQUEST, 'parentObject', null);
$rank           = getArrayParameter($_REQUEST, 'rank', null);
$locked         = getArrayParameter($_REQUEST, 'locked', true);
$text           = getArrayParameter($_REQUEST, 'text', null);
$title          = getArrayParameter($_REQUEST, 'title', null);

$html = '';
if (!$action || !$bizobjectClass)
{
    $html = '<response type="item" id="' . $listDisplayId . '">' . _BIZ_ERROR . '</response>';
}
else if ($action == 'show')
{
    $html = showListElement($project, $bizobjectId, $bizobjectClass);
}
else if ($action == 'display')
{
    $html = displayList($_REQUEST, $project);
}
else if ($action == 'checkin')
{
    $bizobject = new $bizobjectClass($project);
    // if there is a choice, then it is an update
    if(intval($bizobjectId) != 0)
    {
        $bizobject->refresh($bizobjectId);
        $bizobject->rank = $rank;
    }
    else // Otherwise, it is a new record
    {
        // Recover the max rank to calculate the rank of this new poll's choices
        $connector = $project->datalayer->getConnectorByReference("biz");
        $businessDB = $connector->getBusinessDatabase();
        $sql = 'select max(rank) from #__' . $bizobjectClass . ' where ' . $parentObject . 'Id =' . $parentId;
        $maxPosition = $businessDB->executeScalar($sql);
        $bizobject->rank = $maxPosition + 1;
        $parentObjectId = $parentObject . 'Id';
        $bizobject->$parentObjectId = $parentId;
    }
    if ($title)
        $bizobject->title = $title;
    $bizobject->text = $text;
    $bizobject->checkin();

    // Update the poll's choices listing to show the record that was just inserted or updated
    $html = displayList($_REQUEST, $project);
}
else if ($action == 'delete')
{
    $bizobject = new $bizobjectClass($project, $bizobjectId);
    // Update the rank of others records
    $connector = $project->datalayer->getConnectorByReference("biz");
    $businessDB = $connector->getBusinessDatabase();
    $sql = 'update #__' . $bizobjectClass . ' set rank = rank-1 where ' . $parentObject . 'Id = ' . $parentId . ' AND rank > ' . $rank;
    $businessDB->executeStatement($sql);
    $parentObjectId = $parentObject . 'Id';
    $bizobject->delete($bizobject->$parentObjectId);

    $html = displayList($_REQUEST, $project);
}
else if ($action == 'movedown')
{
    // recover the business database to execute the query
    $connector = $project->datalayer->getConnectorByReference("biz");
    $businessDB = $connector->getBusinessDatabase();

    $sql = 'select rank from #__' . $bizobjectClass . ' where id =' . $bizobjectId;
    $currentRank = $businessDB->executeScalar($sql);

    $sql2 = 'select id, rank from #__' . $bizobjectClass . ' where ' . $parentObject . 'Id =' . $parentId . ' and rank > ' . $currentRank . ' order by rank ASC';
    $rs = $businessDB->executeQuery($sql2);
    $rs->next();
    $idSwapped = $rs->get('id');
    $rankSwapped = $rs->get('rank');

    //Update the rank of the current poll's choice
    $bizobject = new $bizobjectClass($project);
    $bizobject->refresh($bizobjectId);
    $tempRank = $bizobject->rank;
    $bizobject->rank = $rankSwapped;
    $bizobject->checkin();

    // Update the rank of the swapped poll's choice
    $bizobject->refresh($idSwapped);
    $bizobject->rank = $tempRank;
    $bizobject->checkin();

    $html = displayList($_REQUEST, $project);
}
else if ($action == 'moveup')
{
    // recover the business database to execute the query
    $connector = $project->datalayer->getConnectorByReference("biz");
    $businessDB = $connector->getBusinessDatabase();

    $sql = 'select rank from #__' . $bizobjectClass . ' where id =' . $bizobjectId;
    $currentRank = $businessDB->executeScalar($sql);
    $sql = 'select id, rank from #__' . $bizobjectClass . ' where ' . $parentObject . 'Id =' . $parentId . ' and rank < ' . $currentRank . ' order by rank DESC';
    $rs = $businessDB->executeQuery($sql);

    $rs->next();
    $idSwapped = $rs->get('id');
    $rankSwapped = $rs->get('rank');

    //Update the rank of the current poll's choice
    $bizobject = new $bizobjectClass($project);
    $bizobject->refresh($bizobjectId);
    $tempRank = $bizobject->rank;
    $bizobject->rank = $rankSwapped;
    $bizobject->checkin();

    // Update the rank of the swapped poll's choice
    $bizobject->refresh($idSwapped);
    $bizobject->rank = $tempRank;
    $bizobject->checkin();

    $html = displayList($_REQUEST, $project);
}

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// XML output
header("Content-Type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';

// Write ajax response
echo '<ajax-response>';
echo '<response type="item" id="' . $ajaxMessage . '">(' . _BIZ_AJAX_PROCESSING_DONE_MSG . ')</response>';
echo $html;
echo '</ajax-response>';

// Display all the poll's choicees of an article
function displayList($array, $project)
{
    $config = wcmConfig::getInstance();

    $url = $config['wcm.backOffice.url'];
    $html = '<response type="item" id="' . $array['listDisplayId'] . '"><![CDATA[';

    // We update the body div, where we display all the choices linked to a poll
    if (isset($array['parentObject']))
    {
        $where = $array['parentObject'].'Id = ' . $array['parentId'];
    }
    $bizobjects = bizobject::getBizobjects($array['bizobjectClass'], $where, "rank ASC");
    if($bizobjects != null)
    {
        // recover the business database to execute the query
        $connector = $project->datalayer->getConnectorByReference("biz");
        $businessDB = $connector->getBusinessDatabase();

        $sql = 'select max(rank) from #__' . $array['bizobjectClass'] . ' where '. $array['parentObject'].'Id =' . $array['parentId'];
        $maxPosition = $businessDB->executeScalar($sql);

        $sql = 'select min(rank) from #__' . $array['bizobjectClass'] . ' where '. $array['parentObject'].'Id =' .$array['parentId'];
        $minPosition = $businessDB->executeScalar($sql);

        $cpt = 1;
        $html .= '<table cellspacing="4" cellpadding="0" border="0" width="620">';
        foreach($bizobjects as $bizobject)
        {
            $movedown = true;
            $parentObjectId = $array['parentObject'] . 'Id';
            if($bizobject->rank == $maxPosition)
            {
                $movedown = false;
            }

            $moveup = true;
            if($bizobject->rank == $minPosition)
            {
                $moveup = false;
            }
            $html .= '<tr><td>';
            $html .= '<table width="510" height="50" bgcolor="#c0c0c0" cellspacing="1" cellpadding="0">';
            $html .= '<tr bgcolor="#f4f4f4" height="25">';
            $html .= '<td width="30" rowspan="2" align="center" class="position">' . $cpt . '</td>';
            $html .= '<td width="20" align="center">';
            if($moveup  && (!array_key_exists('locked', $array) || !$array['locked'] || ($array['locked'] == 'false')))
            {
                $html .= '<img src="' . $url . '/img/arrow_up.gif" border="0" alt="' . _BIZ_MOVEUP . '" style="cursor:pointer" onclick="getList(\'moveup\', \'' .  $bizobject->id . '\', \'' .  $array['bizobjectClass'] . '\', \'' . $bizobject->$parentObjectId . '\', \'' .  $array['parentObject'] . '\', \'' . $array['listDisplayId'] . '\')">';
            }
            $renderParameters = $array;
            $renderParameters["callback"] = 'getList';
           
            $html .= '</td>';
            $html .= '<td width="340" rowspan="2">';
            $html .= renderBizobject($bizobject, '', $renderParameters);
            $html .= '</td>';
            $html .= '<td width="20" align="center">';
            if (!array_key_exists('locked', $array) || !$array['locked'] || ($array['locked'] == 'false'))
            {
                $html .= '<img src="' . $url . '/img/edit.gif" border="0" alt="' . _BIZ_EDIT . '" style="cursor:pointer" onclick="resetFields(\'chapter\'); getList(\'show\', \'' .  $bizobject->id . '\', \'' .  $array['bizobjectClass'] . '\', \'' . $bizobject->$parentObjectId . '\', \'' .  $array['parentObject'] . '\', \'' . $array['listDisplayId'] . '\')">';
            }
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr bgcolor="#f4f4f4" height="25">';
            $html .= '<td width="20" align="center">';
            if ($movedown && (!array_key_exists('locked', $array) || !$array['locked'] || ($array['locked'] == 'false')))
            {
                $html .= '<img src="' . $url . '/img/arrow_down.gif" border="0" alt="' . _BIZ_MOVEDOWN . '" style="cursor:pointer" onclick="getList(\'movedown\', \'' .  $bizobject->id . '\', \'' .  $array['bizobjectClass'] . '\', \'' . $bizobject->$parentObjectId . '\', \'' .  $array['parentObject'] . '\', \'' . $array['listDisplayId'] . '\')">';
            }
            $html .= '</td>';
            $html .= '<td align="center">';
            if (!array_key_exists('locked', $array) || !$array['locked'] || ($array['locked'] == 'false'))
            {
                $html .= '<img src="' . $url . '/img/delete.gif" border="0" alt="' . _BIZ_DELETE .'" style="cursor:pointer" onClick="getList(\'delete\', \'' .  $bizobject->id . '\', \'' .  $array['bizobjectClass'] . '\', \'' . $bizobject->$parentObjectId . '\', \'' .  $array['parentObject'] . '\', \'' . $array['listDisplayId'] . '\')">';
            }
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';
            $html .= '</td></tr>';
            $cpt++;
        }
        $html .= '</table>';
    }
    $html .= ']]></response>';
    return $html;
}

/**
 * Show the choice's text
 */
function showListElement($project, $bizobjectId, $bizobjectClass)
{
    $bizobject = new $bizobjectClass($project);
    $bizobject->refresh($bizobjectId);

    $html = '';
    $html .= '<response type="item" id="bizobjectId">';
    $html .= $bizobject->id;
    $html .= '</response>';
    if (isset($bizobject->text))
    {
        $html .= '<response type="item" id="bizObject_manage"><![CDATA[';
        $html .= $bizobject->text;
        $html .= ']]></response>';
    }
    if (isset($bizobject->rank))
    {
        $html .= '<response type="item" id="rank">';
        $html .= $bizobject->rank;
        $html .= '</response>';
    }
    if (isset($bizobject->title))
    {
        $html .= '<response type="item" id="title' . $bizobjectClass . '"><![CDATA[';
        $html .= $bizobject->title;
        $html .= ']]></response>';
    }
    // only display if it's a chapter.
    // TODO - optimise so it can be used by different objects
    if ($bizobjectClass == 'chapter')
    {
        $html .= '<response type="item" id="phlnk"><![CDATA[';
        // only display photo link if chapter already exists (need Id to create biz_relation)
        $html .= '<a href="javascript:openDialog(\'popup.php\', \'module=select_bizobject&amp;typeSource=chapter&amp;idSource=' . $bizobject->id . '&amp;position=0&amp;mode=insert&amp;classSearch=photo&amp;typeRelation=3&amp;div=relatedPhotos_\',1030,570,\'select_bizobject\')">';
        $html .= _BIZ_ADD_PHOTOS_MSG . '</a>';
        $html .= ']]></response>';
    }
    return $html;
}
    ?>