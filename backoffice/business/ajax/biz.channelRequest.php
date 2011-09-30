<?php

/**
 * Project:     WCM
 * File:        biz.channelRequest.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Initialize return values
$message    = null;

// Retrieve parameters
$messageId          = getArrayParameter($_REQUEST, "messageId", null);
$channelRequestId   = getArrayParameter($_REQUEST, "channelRequestId", null);
$command            = getArrayParameter($_REQUEST, "command", null);
$divName            = getArrayParameter($_REQUEST, "divName", null);
$locked             = getArrayParameter($_REQUEST, "locked", false);
$xmlWhere           = getArrayParameter($_REQUEST, "xmlWhere", false);
$xmlOrder           = getArrayParameter($_REQUEST, "xmlOrder", false);
$className          = getArrayParameter($_REQUEST, "className", false);
$name               = getArrayParameter($_REQUEST, "name", false);

// instanciate a request BizObject
$channelRequest = new channelRequest($project);

switch($command)
{
    case "refresh":
        if ($channelRequestId)
            $channelRequest->refresh($channelRequestId);
        break;
    case "update":
        if ($channelRequestId)
            $channelRequest->refresh($channelRequestId);
        $channelRequest->bind($_REQUEST);
        $channelRequest->checkin();
        break;
    case "insert":
        $channelRequest->checkin($_REQUEST);
        break;
    case "checkRequestValidity":
        if ($channelRequestId)
            $channelRequest->refresh($channelRequestId);
        $checkRequest = new channelRequest($project);
        $checkRequest->xmlWhere = $xmlWhere;
        $checkRequest->xmlOrder = $xmlOrder;
        $checkRequest->className = $className;
        break;
    default:
        break;
}

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Xml output
header("Content-Type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';

// Write ajax response
echo "<ajax-response>\n";

echo "<response type='item' id='".$divName."'>";
echo "<![CDATA[ ";
if ($command == "checkRequestValidity")
{
    if ($checkRequest->checkRequestValidity($channelRequestId))
        echo _BIZ_CHANNEL_REQUEST_VALID;
    else
        echo _BIZ_CHANNEL_REQUEST_NOT_VALID; 
}
else
{
?>
    <input type="hidden" name="xmlWhere" id="xmlWhere" value="<?php echoH8($channelRequest->xmlWhere); ?>"/>
    <input type="hidden" name="xmlOrder" id="xmlOrder" value="<?php echoH8($channelRequest->xmlOrder); ?>"/>
    <div style="border-bottom:1px solid #c0c0c0; margin:10px; width:750px; height:30px;">
        <select name="requestList" class="modField small"<?php if ($locked) echo " disabled='disabled'"?> onChange="ajaxChannelRequest('refresh', this.options[this.options.selectedIndex].value, 'channelRequestDiv', '<?php echo $locked; ?>');">
            <option value=""></option>
            <?php 
            $options = $channelRequest->getRequestList();    
            foreach($options as $value => $caption)
            {
                echo "<option value='" . textH8($value) . "' style='cursor: pointer;'";
                if ($channelRequest->id == $value)
                    echo " selected";
                echo ">" . textH8($caption) . "</option>";
            }
            ?>
        </select>
        <?php
            if (!$locked)
            {
                echo "<img src=\"img/edit.gif\" border=\"0\" onClick=\"showElements('true');\" style=\"cursor: pointer;\" alt=\""._BIZ_EDIT."\" title=\""._BIZ_EDIT."\" />";
                echo "<img src=\"img/actions/table_new.gif\" border=\"0\" onClick=\"ajaxChannelRequest('new', '".$channelRequestId."', 'channelRequestDiv', '".$locked."');showElements('true');\" style=\"cursor: pointer;\" alt=\""._BIZ_ADD."\" title=\""._BIZ_ADD."\" />";
            }
        ?>
    </div>
    <table width="730px" cellspacing="5">
    <tr>
        <td width="200px">
        <?php
            echo _BIZ_CHECK_REQUEST;
        ?>
        </td>
        <td width="20px">
            <?php
                echo "<img id=\"CheckValidity\" src=\"img/checked.gif\" border=\"0\" onClick=\"storeInXml();ajaxChannelRequest('checkRequestValidity', '".$channelRequestId."', 'checkValidityResponse', '".$locked."', $('xmlWhere').value, $('xmlOrder').value, $('className').value, $('name').value);\" style=\"cursor: pointer;\" />";
                echo "</td><td><div id=\"checkValidityResponse\"></div>";   
            ?>
        </td>
    </tr>
    </table>
    <br/>
    <table width="730px" cellspacing="5">
        <tr>
            <td width="20px">
                <?php
                echo _BIZ_CHANNEL_REQUEST_NAME;
                ?>
            </td>
            <td width="700px"><input type="text" name="name" id="name" value="<?php echoH8($channelRequest->name); ?>" style="width:100%" disabled/> </td>
        </tr>
        <tr>
            <td>
                <?php
                echo _BIZ_CHANNEL_REQUEST_CLASS;
                ?>
            </td>
            <td>
                <select name="className" id="className" onChange="changeFieldList(this.options[this.selectedIndex].value,true)" style="width:100%" disabled>
                    <?php
                        $array = getClassList();
                        renderHtmlBizclassOptions($array, $channelRequest->className);
                     ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <fieldset>
                        <legend><?php echo _BIZ_CHANNEL_REQUEST_WHERE; ?></legend>
                        <table cellspacing="5" cellpadding="0" border="0" width="730px">
                        <tr>
                            <td width="100px" align="center"><?php echo _BIZ_OPERATOR; ?></td>
                            <td width="50px" align="center">(</td>
                            <td width="180px" align="center"><?php echo _BIZ_FIELDS; ?></td>
                            <td width="100px" align="center"><?php echo _BIZ_COMPARE; ?></td>
                            <td width="180px" align="center"><?php echo _BIZ_VALUE; ?></td>
                            <td width="20px">&nbsp;</td>
                            <td width="50px" align="center">)</td>
                            <td width="20px">&nbsp;</td>
                            <td width="20px">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="100px" align="center">
                                <select name="where_operator" style="width:100%" disabled>
                                <?php renderHtmlOptions($channelRequest->getAssocList() , null); ?>
                                </select>
                            </td>
                            <td width="50px" align="center">
                                <input style="width:100%" type="checkbox" id="OpenBracket" name="OpenBracket" disabled />
                            </td>
                            <td width="180px" align="center">
                                <select name="where_fieldList" style="width:100%" disabled>
                                </select>
                            </td>
                            <td width="100px" align="center">
                                <select name="where_compare" style="width:100%" disabled>
                                    <?php renderHtmlOptions($channelRequest->getOperatorList() , null); ?>
                                </select>
                            </td>
                            <td width="180px" align="center">
                                <div id="value">
                                    <input name="where_value" type="text" style="width:100%" disabled />
                                </div>
                            </td>
                            <td width="20px" align="center">                                
                                <img onmouseover="$('requestValueHelper').style.display = ''" onmouseout="$('requestValueHelper').style.display = 'none'" alt="" style="cursor:pointer; margin-bottom:-3px" src="img/icons/about.gif"/>             
                                <div id="requestValueHelper" name="requestValueHelper" style="border: solid 1px #CCCCCC; padding:15px; background-color:#FFFFFF; display:none; position:absolute">
                                <u>SMARTTAGS</u> :<br/>
                                &nbsp;&nbsp;&nbsp;<i>#ChannelId</i><br/>
                                </div>
                            </td>
                            <td width="50px" align="center">
                                <input style="width:100%" type="checkbox" id="CloseBracket" name="CloseBracket" disabled />
                            </td>
                            <td width="20px">
                                &nbsp;
                            </td>
                            <td width="20px">
                                <?php
                                    if (!$locked)
                                        echo "<img id=\"WhereAdd\" src=\"img/add.gif\" border=\"0\" onClick=\"saveWhereClause();\" style=\"cursor: pointer;visibility: hidden;\" alt=\""._BIZ_ADD."\" title=\""._BIZ_ADD."\" />";
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10">&nbsp;</td>
                        </tr>
                        <tr>
                        <td colspan="10">
                            <div id="whereClause" style="border:1px solid #c0c0c0; width:100%; height:150px; overflow-y:scroll">
                            </div>
                        </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <fieldset>
                    <legend><?php echo _BIZ_CHANNEL_REQUEST_ORDERBY; ?></legend>
                    <table cellspacing="5" cellpadding="0" border="0" width="730px">
                        <tr>
                            <td width="180px"  align="center"><?php echo _BIZ_FIELDS; ?></td>
                            <td width="380px" align="center">&nbsp;</td>
                            <td width="150px" align="center"><?php echo _BIZ_ORDER; ?></td>
                            <td width="20px">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="180px" align="center"">
                                <select name="order_fieldList" style="width:100%" disabled >
                                </select>
                            </td>
                            <td width="380px" align="center">&nbsp;</td>
                            <td width="150px" align="center"">
                                <select name="order_list" style="width:100%" disabled>
                                    <?php renderHtmlOptions($channelRequest->getOrderList() , null); ?>
                                </select>
                            </td>
                            <td width="20px">
                                <?php
                                    if (!$locked) 
                                        echo "<img id=\"OrderAdd\" src=\"img/add.gif\" border=\"0\" onClick=\"saveOrderBy();\" style=\"cursor: pointer; visibility: hidden;\" alt=\""._BIZ_ADD."\" title=\""._BIZ_ADD."\" />";
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div id="orderClause" style="border:1px solid #c0c0c0; width:100%; height:100px; overflow-y:scroll">
                                </div>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="right">
                <?php
                if (!$locked)
                {
                    if ($channelRequest->id)
                    {
                        echo "<a style=\"cursor: pointer; visibility: hidden;\" id=\"Cancel\" href=\"#\" onClick=\"showElements('false');\"><img id=\"Cancel\" alt=\""._BIZ_CANCEL."\" title=\""._BIZ_CANCEL."\" src=\"img/actions/cancel.gif\" border=\"0\" />"._BIZ_CANCEL."</a>";
                        echo "<a style=\"cursor: pointer; visibility: hidden;\" id=\"Save\" href=\"#\" onClick=\"storeInXml();ajaxChannelRequest('update', ".$channelRequest->id.", '".$divName."', '".$locked."', $('xmlWhere').value, $('xmlOrder').value, $('className').value, $('name').value);\"><img id=\"Save\" alt=\""._CHECKIN."\" title=\""._CHECKIN."\" src=\"img/actions/table_refresh.gif\" border=\"0\"/>"._CHECKIN."</a>";
                        echo "<a style=\"cursor: pointer; visibility: hidden;\" id=\"AddNew\" href=\"#\" onClick=\"storeInXml();if ($('name').value == '".$channelRequest->name."'){alert('"._BIZ_NEW_REQUEST_NAME."');}else{ajaxChannelRequest('insert', 0, '".$divName."', '".$locked."', $('xmlWhere').value, $('xmlOrder').value, $('className').value, $('name').value);}\"><img id=\"Save\" alt=\""._CLONE."\" title=\""._CLONE."\" src=\"img/actions/table_add.gif\" border=\"0\"/>"._CLONE."</a>";
                    }
                    else
                    {
                        echo "<a style=\"cursor: pointer; visibility: hidden;\" id=\"Cancel\" href=\"#\" onClick=\"ajaxChannelRequest('refresh', '".$channelRequestId."', 'channelRequestDiv', '');showElements('false');\"><img id=\"Cancel\" alt=\""._BIZ_CANCEL."\" title=\""._BIZ_CANCEL."\" src=\"img/actions/cancel.gif\" border=\"0\" />"._BIZ_CANCEL."</a>";
                        echo "<a style=\"cursor: pointer; visibility: hidden;\" id=\"Save\" href=\"#\" onClick=\"storeInXml(); ajaxChannelRequest('insert', '".$channelRequest->id."', '".$divName."', '".$locked."', $('xmlWhere').value, $('xmlOrder').value, $('className').value, $('name').value);\"><img id=\"Save\" alt=\""._CHECKIN."\" title=\""._CHECKIN."\" src=\"img/actions/table_add.gif\" border=\"0\" />"._CHECKIN."</a>";
                    }
                }
                ?>
            </td>
        </tr>
    </table>
<?php
}
echo " ]]>";
echo "</response>\n";

// Return message ?
if ($messageId != null)
{
    echo "<response type='item' id='".$messageId."'>";
    echo ($message) ? "<![CDATA[ ".$message." ]]>" : "";
    echo "</response>\n";
}
echo "</ajax-response>";

?>
