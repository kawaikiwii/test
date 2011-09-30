<div id="{$prefix}relations" class="relations">
<ul id="{$prefix}list">
{foreach from=$relations item=relation name=media}
{assign var='bizobject' value=$relation.destination}
{assign var='className' value=$bizobject.className}
{assign var='myId' value=$smarty.foreach.media.iteration}
<li class="bizrelation" id="rel-{$relation.destinationClass}-{$relation.destinationId}">
{php}
$all_tpl_vars = $this->get_template_vars();
$myId = $all_tpl_vars['myId'];
$credz = $all_tpl_vars['bizobject']['credits'];
$specialUses = $all_tpl_vars['bizobject']['specialUses'];
$title = $all_tpl_vars['relation']['title'];
$pk = $all_tpl_vars['pk'];
{/php}
	<div class="toolbar {$bizobject.className}">
    <div class="remove"><span>Remove</span></div>
    {if $relation.destinationClass == 'photo'}
    	{if $bizobject.specialUses != ""}
    	<div style="padding-left:20px;"><img src="/skins/default/images/gui/lock.png" alt="SPECIAL USES" title="SPECIAL USES" />{$bizobject.specialUses|truncate:90:"..."}.</div>
		{/if}
    {else}
    	<div style="margin-left:5px;">&nbsp;&nbsp;{$relation.destinationClass}: {$bizobject.title|truncate:40:"..."}</div>
    {/if }
    </div>
{php}
wcmGUI::openCollapsablePane('<input type="text" id="title'.$myId.'" name="_list'.$pk.'[title][]" value="'.htmlspecialchars($title).'" style="width:92%;" />', false);
{/php}
		<input type="hidden" value="{$relation.destinationId}" name="_list{$pk}[destinationId][]" />
		<input type="hidden" value="{$relation.destinationClass}" name="_list{$pk}[destinationClass][]" />	
		{wcm name='include_template' file="relations/$className.tpl" fallback='relations/default.tpl'}
		{if $bizobject.className eq 'photo'}
		<table style="clear:both; border:#444 1px solid; margin-bottom:0.5em;" cellpadding=0 cellspacing=5>
			<tr><td><b>Credits</b> {php} echo $credz; {/php}<br>
			<b>Special Uses</b> {php} echo $specialUses; {/php}</td></tr>
		</table>
		{/if}	
{php}	
$text = $all_tpl_vars['relation']['media_text'];
$description = $all_tpl_vars['relation']['media_description'];
$link = $all_tpl_vars['relation']['media_link'];

if ($description == NULL || $description == '')
{
	$photoId = $all_tpl_vars['bizobject']['id'];
	$oPhoto = new photo(null, $photoId);
	$contents = $oPhoto->getContents();
	foreach($contents as $content)
	{
		$textReplace = $content->text;
	    /*$textReplace = str_replace("'", '', $textReplace);
        $textReplace = str_replace('"', '', $textReplace);*/
        $textReplace = str_replace('`', '', $textReplace);
        $textReplace = str_replace("\n", "", $textReplace);
        $textReplace = str_replace("\r", "", $textReplace);
        $textReplace = str_replace("\r\n", "", $textReplace);

        $descriptionReplace = $content->description;
        /*$descriptionReplace = str_replace("'", '', $descriptionReplace);
        $descriptionReplace = str_replace('"', '', $descriptionReplace);*/
        $descriptionReplace = str_replace('`', '', $descriptionReplace);
        $descriptionReplace = str_replace("\n", "", $descriptionReplace);
        $descriptionReplace = str_replace("\r", "", $descriptionReplace);
        $descriptionReplace = str_replace("\r\n", "", $descriptionReplace);
	}
}
else
{
	$textReplace = '';
	$descriptionReplace = '';
}

$legend_text = getConst('_BIZ_TEXT');
$legend_description = getConst('_BIZ_DESCRIPTION');	
$legend_link = getConst('_BIZ_VIDEO_URL');
	
$className = $this->get_template_vars('className');
	
wcmGUI::openFieldset("");

echo '<b>'.$legend_description.'</b><br>'; 
echo 'Fill with : ';
echo '<a href="javascript:void(0);" onClick="document.getElementById(\'media_description'.$myId.'\').value=\''.str_replace("'","\'",htmlspecialchars($descriptionReplace)).'\';">original Description</a>';
echo '&nbsp;/&nbsp;';
echo '<a href="javascript:void(0);" onClick="document.getElementById(\'media_description'.$myId.'\').value=\''.str_replace("'","\'",htmlspecialchars($description)).'\';">last saved Description</a>';
echo ' (Undo with Ctrl+Z in Field)';
if ($className == "work")
	echo '<br/><a href="javascript:addToTextarea(\'content_forecast_description\',\'media_description'.$myId.'\');"><img src="/skins/default/images/gui/notebook_add.png" border="0" title="ADD DESCRIPTION TO OBJECT HEADER"></a>';

wcmGUI::renderTextArea("_list".$pk."[media_description][]", $description, '', array('id' => 'media_description'.$myId, 'style' => 'width:97%'));
wcmGUI::closeFieldset();

wcmGUI::openFieldset("");
	
echo '<b>'.$legend_text.'</b><br>';
echo 'Fill with : ';
echo '<a href="javascript:void(0);" onClick="document.getElementById(\'media_text'.$myId.'\').value=\''.str_replace("'","\'",htmlspecialchars($textReplace)).'\';">original Text</a>';
echo '&nbsp;/&nbsp;';
echo '<a href="javascript:void(0);" onClick="document.getElementById(\'media_text'.$myId.'\').value=\''.str_replace("'","\'",htmlspecialchars($text)).'\';">last saved Text</a>';
echo ' (Undo with Ctrl+Z in Field)';
if ($className == "work")
	echo '<br/><a href="javascript:addToTextarea(\'content_forecast_description\',\'media_text'.$myId.'\');"><img src="/skins/default/images/gui/notebook_add.png" border="0" title="ADD TEXT TO OBJECT HEADER"></a>';

wcmGUI::renderTextArea("_list".$pk."[media_text][]", $text, '', array('id' => 'media_text'.$myId, 'style' => 'width:97%'));
wcmGUI::closeFieldset();

wcmGUI::openFieldset("");
	
echo '<b>'.$legend_link.' (ex. http://www.google.com)</b><br>';
wcmGUI::renderTextField("_list".$pk."[media_link][]", $link, '', array('id' => 'media_link'.$myId, 'style' => 'width:97%'));
wcmGUI::closeFieldset();


{/php}

	<div style="clear:both;"></div>
{php}
wcmGUI::closeCollapsablePane();
{/php}
</li>
{/foreach}
</ul>
</div>
