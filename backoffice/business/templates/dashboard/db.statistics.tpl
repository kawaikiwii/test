<table cellspacing="0">
    <tr>
    {foreach from=$params.fields key=name item=field}
        <th>{$field|constant}</th>
    {/foreach}
    </tr>
    {loop class=$params.source where=$params.where orderby=$params.orderBy from=$params.from limit=$params.limit name="elem"}
    {assign var=url value="?_wcmAction=business/`$params.source`&amp;id=`$elem.id`"}
    <tr{cycle values=", class='alternate'"}>
        {foreach from=$params.fields key=name item=field}
            {if $name eq "title"}
                <td width="100%" class="type {$elem.className}"><a href="{$url}">{$elem.$name}</a> </td>
            {else}
                <td class="nowrap"  class="type {$elem.className}" style="text-align: right;">{$elem.$name}</td>
            {/if}
        {/foreach}
    </tr>
    {/loop}
    {if $loop.elem.total eq 0}
    <tr><td colspan="$params.fields|@count"> - (empty) - </td></tr>
    {/if}
</table>
