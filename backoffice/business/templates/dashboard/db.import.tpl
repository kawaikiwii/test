<table cellspacing="0" cellpadding="0">
    <tr>
        {foreach from=$params.fields key=name item=field}
            <th>{$field|constant}</th>
        {/foreach}
    </tr>
    {loop class=$params.source where=`$params.where` orderby=$params.orderBy from=$params.from limit=$params.limit name="elem"}
    {assign var=url value="?_wcmAction=business/`$params.source`&amp;id=`$elem.id`"}
    {assign var=uniqueId value="`$params.source`_`$elem.id`"}
    <tr title="{$elem.title}" id="{$uniqueId}">
        {foreach from=$params.fields key=name item=field}
            {if $name eq "title"}
                <td width="70%">
                    <a href="{$url}">{$elem.title|SUBSTR:0:85}
	                	{if strlen($elem.title) > 85}
	                		(&hellip;)
	                	{/if}
	                </a>
                </td>
            {else}
                <td width="18%" style="padding-right:0;">
                	{$elem.$name}
                </td>
            {/if}
        {/foreach}
        <td width="12%">

            {assign var=titleForElement value=$elem.title}
            {php}
            	$title = str_replace("'", '', $this->get_template_vars('titleForElement'));
            	$title = str_replace('"', '', $title);
            	$title = str_replace('`', '', $title);
            	$this->assign('elemTitle', $title);
            {/php}
            
            {foreach from=$elem.transitions item=transition}
                <a class="saveImport" href="javascript:saveImport('{$elemTitle}', '{$params.source}', '{$elem.id}', '{$transition.id}', '{$uniqueId}');"></a>
            {/foreach}
            <a class="deleteImport" href="javascript:deleteImport('{$elemTitle}', '{$params.source}', '{$elem.id}', '{$uniqueId}');"></a>
            
        </td>
    </tr>
    {/loop}
    {if $loop.elem.total eq 0}
    <tr><td colspan="$params.fields|@count"> - (empty) - </td></tr>
    {/if}
</table>