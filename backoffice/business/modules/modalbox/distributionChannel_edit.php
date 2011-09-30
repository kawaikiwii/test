<?php

/**
 * Project:     WCM
 * File:        distributionChannel_edit.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

require_once dirname(__FILE__).'/../../../initWebApp.php';

$id           = getArrayParameter($_REQUEST, "id", 0);
$action       = getArrayParameter($_REQUEST, "kind", null);
$exportRuleId = getArrayParameter($_REQUEST, "input",0);

$bizobject = new distributionChannel();
if ($id)
	$bizobject->refresh($id);
else
	$bizobject->exportRuleId = $exportRuleId;
$typeList = distributionChannel::getTypeList();
echo "<div id=\"errorMsg\"></div>";
echo '<div id="distributionChannel">';
echo "<form id='distributionChannel_edit' name='distributionChannel_edit'>";
echo "<table border='0' width='98%'>";
echo "<tr>";
echo "<td valign='top'>";
    wcmGUI::openFieldset('');
	    wcmGUI::renderHiddenField('exportRuleId', $bizobject->exportRuleId);
	    wcmGUI::renderTextField('code', $bizobject->code, _CODE);
	    wcmGUI::renderBooleanField('active', $bizobject->active, _ACTIVE);
		
	    $onChange = "var types = ['".implode("','",array_keys($typeList))."'];for (var i=0;i<types.length;i++){\$(types[i]).hide();}\$(this.options[this.selectedIndex].value).show();";
	    wcmGUI::renderDropdownField('type', $typeList, $bizobject->type, _TYPE,array('onChange'=>$onChange));
    wcmGUI::closeFieldset();
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td valign='top'>";
echo "<hr/>";
echo "<br/>";
$params = unserialize($bizobject->connexionString);
    wcmGUI::openFieldset('');    
	    echo "<div id='ftp' name='ftp'>";
	    wcmGUI::renderTextField('host', getArrayParameter($params,'host',''), _DISTRIBUTIONCHANNEL_HOST);
	    wcmGUI::renderTextField('user', getArrayParameter($params,'user',''), _DISTRIBUTIONCHANNEL_USER);
	    wcmGUI::renderTextField('pass', getArrayParameter($params,'pass',''), _DISTRIBUTIONCHANNEL_PASS);
	    wcmGUI::renderTextField('remotePath_ftp', getArrayParameter($params,'remotePath_ftp',''), _DISTRIBUTIONCHANNEL_REMOTE_PATH);
	    echo "</div>";
	    echo "<div id='fs' name='fs'>";
	    wcmGUI::renderTextField('remotePath_fs', getArrayParameter($params,'remotePath_fs',''), _DISTRIBUTIONCHANNEL_REMOTE_PATH);
	    echo "</div>";
	    echo "<div id='email' name='email'>";
	    wcmGUI::renderTextField('fromName', getArrayParameter($params,'fromName',''), _DISTRIBUTIONCHANNEL_FROM_NAME);
	    wcmGUI::renderTextField('fromMail', getArrayParameter($params,'fromMail',''), _DISTRIBUTIONCHANNEL_FROM_MAIL);
	    wcmGUI::renderTextField('to', getArrayParameter($params,'to',''), _DISTRIBUTIONCHANNEL_TO);
	    wcmGUI::renderTextField('title', getArrayParameter($params,'title',''), _DISTRIBUTIONCHANNEL_TITLE);
	    echo "</div>";
    wcmGUI::closeFieldset();
echo "<hr/>";
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td align='right'>";
        echo "<br />";
	echo "<ul class='toolbar'>";
		echo "<li><a href='#' onclick=\"closemodal(); return false;\" class='cancel'>"._BIZ_CANCEL."</a></li>";
		echo "<li><a href='#' onclick=\"document.getElementById('errorMsg').innerHTML = '';parent.ajaxDistributionChannel('".$action."', ".$exportRuleId.", ".$id.", 'results', $('distributionChannel_edit').serialize()); if (document.getElementById('errorMsg').innerHTML == '') closemodal(); return false;\" class='save'>"._BIZ_SAVE."</a></li>";
	echo "</ul>";
echo "</td>";
echo "</tr>";
echo "</table>";
echo "</form>";
echo "</div>";


?>

<script type='text/javascript' defer='defer'>
var types = ['<?php echo implode("','",array_keys($typeList)); ?>'];
for (var i=0;i<types.length;i++)
{
	$(types[i]).hide();
}
<?php
if ($bizobject->type)
	echo "\$('".$bizobject->type."').show();";
else
	echo '$(types[0]).show();';
?>
</script>
	    
	    
