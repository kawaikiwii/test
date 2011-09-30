<div class="toolbar {$bizobject.className}">
	<span style="margin-left: 20px;font-weight:bold">{$bizobject.className|upper}</span>
    <div class="remove"><span>{'_REMOVE'|constant}</span>
    {*$bizobject.className*}
</div>

{php}
$all_tpl_vars = $this->get_template_vars();
$title = $all_tpl_vars['bizobject']['title'];
$pk = $all_tpl_vars['pk'];	
{/php}

 	{if $bizobject.specialUses != ""}
    	<div style="padding-left:20px;"><img src="/skins/default/images/gui/lock.png" alt="SPECIAL USES" title="SPECIAL USES" />{$bizobject.specialUses|truncate:90:"..."}.</div>
	{/if}	
	</div>
{php}
wcmGUI::openCollapsablePane($title , true);
{/php}
	<div class="relproperties">
	<input type="hidden" name="_list{$pk}[destinationClass][]" value="{$bizobject.className}" />
	<input type="hidden" name="_list{$pk}[destinationId][]" value="{$bizobject.id}" />
	<div>	
	<h4>{'_WEB_TITLE'|constant}</h4>
	<input type="text" name="_list{$pk}[title][]" value="{$bizobject.title|htmlspecialchars}" style="width:400px;" /><br/><br/>
{php}wcmGUI::openFieldset("");{/php}
	<img src="{$bizobject.urlByFormat.w400}" alt="{$bizobject.title|htmlspecialchars} - {$bizobject.specialUses|htmlspecialchars}" title="{$bizobject.title|htmlspecialchars} - {$bizobject.specialUses|htmlspecialchars}" style="margin:0; padding:0" />
{php}wcmGUI::closeFieldset();{/php}
{php}
$myId = $all_tpl_vars['myId'];
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
{/php}
	<b>{php} echo $legend_description; {/php}<br></b>
{php}
echo 'Fill with : ';
echo '<a href="javascript:void(0);" onClick="document.getElementById(\'media_description'.$myId.'\').value=\''.str_replace("'","\'",htmlspecialchars($descriptionReplace)).'\';">original Description</a>';
echo '&nbsp;/&nbsp;';
echo '<a href="javascript:void(0);" onClick="document.getElementById(\'media_description'.$myId.'\').value=\''.str_replace("'","\'",htmlspecialchars($description)).'\';">last saved Description</a>';
echo ' (Undo with Ctrl+Z in Field)';
{/php}
	<textarea name="_list{php} echo $pk; {/php}[media_description][]" id="media_description{php} echo $myId; {/php}" style="width:97%"></textarea>
	<br><br>
	<b>{php} echo $legend_text; {/php}</b><br>
{php}
echo 'Fill with : ';
echo '<a href="javascript:void(0);" onClick="document.getElementById(\'media_text'.$myId.'\').value=\''.str_replace("'","\'",htmlspecialchars($textReplace)).'\';">original Text</a>';
echo '&nbsp;/&nbsp;';
echo '<a href="javascript:void(0);" onClick="document.getElementById(\'media_text'.$myId.'\').value=\''.str_replace("'","\'",htmlspecialchars($text)).'\';">last saved Text</a>';
echo ' (Undo with Ctrl+Z in Field)';
{/php}
	<textarea name="_list{php} echo $pk; {/php}[media_text][]" id="media_text{php} echo $myId; {/php}" style="width:97%"></textarea>
	<br><br>
	<b>{php} echo $legend_link; {/php} (ex. http://www.google.com)</b><br>
	<input type="text" name="_list{php} echo $pk; {/php}[media_link][]" id="media_link{php} echo $myId; {/php}" style="width:97%">
	</div>
</div>
{php}
wcmGUI::closeCollapsablePane();
{/php}
