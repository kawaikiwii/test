<?php

/**
 * Project:     WCM
 * File:        biz.listManager_choice.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 *
 * function for Rendering HTML
 *
 */

function renderList($toPrint, $className, $locked, $type = null, $index = null, $parent = null, $parentIndex = null)
{
    foreach ($toPrint as $key => $value)
    {
        $count = $key + 1;

        $html .= '<table width="480" border="0" height="50" bgcolor="#c0c0c0" cellspacing="1" cellpadding="0">';
        $html .= '<tr bgcolor="#f4f4f4" height="25">';

        $html .= '<td width="20" rowspan="2" align="center" valign="middle">'. $count .'</td>';
        $html .= '<td width="20" align="center" valign="middle">';

        if ((!$locked) && ($key != 0) && ($type != 'update'))
            $html .= '<img class="bttn" src="img/arrow_up.gif" border="0" alt="' . _BIZ_MOVEUP .'" title="' . _BIZ_MOVEUP .'" style="cursor:pointer" onclick="manageMultipleList(\'up\', \'' . $key . '\', \'' . $className . '\', \'' . $locked .'\', \'choices\', \'' . $parentIndex . '\');" />';

        $html .= '</td>';

        $html .= "<td rowspan='2'>";

        $html .= "<table cellspacing='0' cellpadding='0' border='0' width='100%'>";
        $html .= "<tr><td width='20'></td>";
        $html .= '<td valign="middle">';
        if (($type == 'update') && ($key == $index))
        {
            $html .= '<form name="choiceForm' . $parentIndex . '" id="choiceForm' . $parentIndex . '" action="?" method="post" style="margin: 0; padding: 0 0 0 10px;">';
            $html .= '<input type="text" name="answerTitle" id="answerTitle" value="' . textH8($_SESSION['list_' . $parent][$parentIndex]['list_' . $className][$index]['answerTitle']) . '" style="width: 250px;" />';
            $html .= '<select name="answerWeight" id="answerWeight" style="width: 50px;">';
            $html .= renderHtmlOptions(questionnaire::getAnswerWeight(), (int)$_SESSION['list_' . $parent][$parentIndex]['list_' . $className][$index]['answerWeight'], false);
            $html .= '</select>';
            $html.= "<div id='choiceState" . $parentIndex . "' style='display: inline; padding-left: 10px;'></div>";
            $html .= '</form>';
        }
        else
        {
            foreach ($value as $key2 => $item)
            {
                $html .= $item . " ";
            }
        }
        $html .= "</td>";
        $html .= "</tr></table>";

        $html .= '<td width="20" align="center" valign="middle">';

        if ( (!$locked) && ($type != 'update'))
            $html .= '<img src="img/edit.gif" border="0" alt="' . _BIZ_EDIT .'" title="' ._BIZ_EDIT . '" style="cursor:pointer" onClick="manageMultipleList(\'update\', \'' . $key .'\', \'' . $className . '\', \'' . $locked .'\', \'choices\', \'' . $parentIndex . '\');" />';

        $html .= "</td></tr>";
        $html .= "<tr bgcolor='#f4f4f4' height='25'>";
        $html .= '<td width="20" align="center" valign="middle">';

        if ((!$locked) && ($key != count($toPrint) - 1) && ($type != 'update'))
            $html .= "<img class='bttn' src='img/arrow_down.gif' width='16' height='16' alt='" . _BIZ_MOVEDOWN . "' title='" . _BIZ_MOVEDOWN . "' onclick='manageMultipleList(\"down\", \"" . $key ."\", \"" . $className . "\", \"" . $locked ."\", \"choices\", \"" . $parentIndex . "\");' />";

        $html .= "</td>";
        $html .= '<td width="20" align="center" valign="middle">';

        if (!$locked)
        {
            if (($type == 'update') && ($key == $index))
                $html .= "<img class='bttn' src='../img/actions/create.gif' width='15' height='15' alt='" . _BIZ_ADD . "' title='" . _BIZ_ADD . "' onclick='manageMultipleList(\"updateSave\", \"" . $index . "\", \"choice\", \"" . $locked ."\", \"choices\", \"" . $parentIndex . "\");' />";
            elseif ($type != 'update')
                $html .= "<img class='bttn' src='img/actions/delete.gif' width='16' height='16' alt='" . _BIZ_DELETE . "' alt='" . _BIZ_DELETE . "' onclick='manageMultipleList(\"delete\", \"" . $key ."\", \"" . $className . "\", \"" . $locked ."\", \"choices\", \"" . $parentIndex . "\");' />";
        }

        $html .= "</td>";
        $html .= "</tr></table>";
    }


    if ($type != 'update')
    {
        $html .= '<table width="480" border="0" height="50" bgcolor="#c0c0c0" cellspacing="1" cellpadding="0">';
        $html .= '<tr bgcolor="#f4f4f4" height="25">';

        $html .= '<td width="41" align="center" valign="middle"></td>';
        $html .= '<td>';
        $html .= '<table cellspacing="0" cellpadding="0" border="0" width="100%">';
        $html .= '<tr>';
        $html .= '<td valign="middle">';
        $html .= '<form name="choiceForm' . $parentIndex . '" id="choiceForm' . $parentIndex . '" action="?" method="post" style="margin: 0; padding: 0 0 0 10px;">';
        $html .= '<input type="text" name="answerTitle" id="answerTitle" value="" style="width: 250px;" />';
        $html .= '<select name="answerWeight" id="answerWeight" style="width: 50px;">';
        $html .= renderHtmlOptions(questionnaire::getAnswerWeight() , '', false);
        $html .= '</select>';
        $html.= "<div id='choiceState" . $parentIndex . "' style='display: inline; padding-left: 10px;'></div>";
        $html .= '</form>';
        $html .= "</td>";
        $html .= "</tr></table>";

        $html .= '<td width="20" align="center" valign="middle">';

        if (!$locked)
            $html .= "<img class='bttn' src='../img/actions/create.gif' width='15' height='15' alt='" . _BIZ_ADD . "' title='" . _BIZ_ADD . "' onclick='manageMultipleList(\"add\", \"" . $index . "\", \"choice\", \"" . $locked ."\", \"choices\", \"" . $parentIndex . "\");' />";

        $html .= "</td></tr>";
        $html .= "</table>";

    }



    return $html;
}

function renderUpdateList($toPrint, $className, $locked, $type = null, $index = null)
{
}
?>