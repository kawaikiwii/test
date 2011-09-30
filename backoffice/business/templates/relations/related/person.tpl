<div class="toolbar {$bizobject.className}">
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
		wcmGUI::openCollapsablePane("", true);
	{/php}


	<div class="relproperties">
	<input type="hidden" name="_list{$pk}[destinationClass][]" value="{$bizobject.className}" />
	<input type="hidden" name="_list{$pk}[destinationId][]" value="{$bizobject.id}" />
	


<div>
	
	<h4>{'_WEB_TITLE'|constant}</h4>
	<input type="text" name="_list{$pk}[title][]" value="{$bizobject.title}" style="width:400px;" /><br/><br/>

	<b>{php} echo $legend_description; {/php}<br></b>


	{php}
		echo 'Fill with : ';
                echo '<a href="javascript:void(0);" onClick="document.getElementById(\'media_description'.$myId.'\').value=\''.$descriptionReplace.'\';">original Description</a>';
                echo '&nbsp;/&nbsp;';
                echo '<a href="javascript:void(0);" onClick="document.getElementById(\'media_description'.$myId.'\').value=\''.$description.'\';">last saved Description</a>';
                echo ' (Undo with Ctrl+Z in Field)';
    {/php}

	<textarea cols=40 rows=6 name="_list{php} echo $pk; {/php}[media_description][]" id="media_description{php} echo $myId; {/php}"></textarea>
<br><br>
	<b>{php} echo $legend_text; {/php}</b><br>
        

	{php}
		echo 'Fill with : ';
                        echo '<a href="javascript:void(0);" onClick="document.getElementById(\'media_text'.$myId.'\').value=\''.$textReplace.'\';">original Text</a>';
                        echo '&nbsp;/&nbsp;';
                        echo '<a href="javascript:void(0);" onClick="document.getElementById(\'media_text'.$myId.'\').value=\''.$text.'\';">last saved Text</a>';
                        echo ' (Undo with Ctrl+Z in Field)';

	{/php}

	<textarea cols=40 rows=6 name="_list{php} echo $pk; {/php}[media_text][]" id="media_text{php} echo $myId; {/php}"></textarea>

</div>
</div>

{php}
	wcmGUI::closeCollapsablePane();
{/php}
