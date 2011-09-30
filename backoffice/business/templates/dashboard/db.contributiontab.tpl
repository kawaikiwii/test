<ul class="filters">
	<li><a href="javascript:modifyview('awaiting');" {if $params.state eq 'awaiting'}class="selected"{/if}>Awaiting approval</a></li>
	<li><a href="javascript:modifyview('all');" {if $params.state eq 'all'}class="selected"{/if}>All</a></li>
	<li><a href="javascript:modifyview('approved');" {if $params.state eq 'approved'}class="selected"{/if}>Approved</a></li>
	<li><a href="javascript:modifyview('rejected');" {if $params.state eq 'rejected'}class="selected"{/if}>Rejected</a></li>
</ul>    
<table id="comments">
    <tr>
        <th></th>
        {foreach from=$params.fieldtitles key=name item=field}
            <th>{$field|constant}</th>
        {/foreach}
        <th>&nbsp;</th>
    </tr>
    {loop class=$params.source where=$params.where orderby=$params.orderBy from=$params.from limit=$params.limit name="elem"}
    {assign var=url value="?_wcmAction=business/`$params.source`&amp;id=`$elem.id`"}
    {assign var=uniqueId value="`$params.source`_`$elem.id`"}
    <tr{cycle values=", class='alternate'"} id="{$uniqueId}">
        <td class="actions">
		<ul class="three-buttons">
			<li><a class="quick" title="{'_QUICK_EDIT'|constant}" id="_link" href="javascript: editcomment({$elem.id}); "><span>Quick edit</span></a></li>
			<li><a class="edit" title="{'_EDIT'|constant}" href="{$url}"><span>Edit</span></a></li>
			<li><a class="delete" title="{'_DELETE'|constant}" href="javascript: deletecomment({$elem.id});" id=""><span>Delete</span></a></li>
		</ul>
	</td>
        {foreach from=$params.fields item=name}
            {if $name eq "modifiedAt" or $name eq "createdAt" or $name eq "publicationDate"}
                <td class="nowrap">{$elem.$name|date_format:"%Y-%m-%d @ %H:%M"}</td>
            {elseif $name eq "title"}
                <td class="type {$elem.className}" width="100%" id="title_text_{$elem.id}">
                  	{if $elem.title}{$elem.title}: {/if}{$elem.text}
                </td>
            {elseif $name eq "workflowState"}
                <td class="state">
                    <strong class="{$elem.workflowState}">{$elem.workflowState}</strong>
                </td>
            {else}
                <td width="50%">{$elem.$name}</td>
            {/if}
        {/foreach}
        
        <td class="transition">
        <ul>
            {foreach from=$elem.transitions item=transition}
            	{if $transition.name eq '_WT_APPROVE' and $elem.workflowState ne 'approved'} 
                	<li><a href="javascript:executeTransition('{$params.source}', '{$elem.id}','{$transition.id}', '{$uniqueId}');">{$transition.name|constant}</a></li>
                {/if}	
               	{if $transition.name eq '_WT_REJECT' and $elem.workflowState ne 'rejected'} 
                	<li><a href="javascript:executeTransition('{$params.source}', '{$elem.id}','{$transition.id}', '{$uniqueId}');">{$transition.name|constant}</a></li>
                {/if}
            {/foreach}
        </ul>
        </td>
    </tr>
    {/loop}
    {if $loop.elem.total eq 0}
    <tr><td colspan="$params.fields|@count"> - (empty) - </td></tr>
    {/if}

</table>

<p class="show-more"><a href="javascript:addtoview('{$params.state}')">Show more &raquo;</a></p>
