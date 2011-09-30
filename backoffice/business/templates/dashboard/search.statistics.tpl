
<table cellspacing="0">
    <tr>
        {foreach from=$params.fields key=name item=field}
            <th>{$field|constant}</th>
        {/foreach}
    </tr>
    {search where="`$params.query`" order="`$params.orderBy`" limit="`$params.limit`" name="result"}
    {assign var=url value="?_wcmAction=business/`$result.className`&amp;id=`$result.id`"}
    <tr{cycle values=", class='alternate'"}>
        {foreach from=$params.fields key=name item=field}
            {if $name eq "title"}
                <td width="100%" class="type {$result.className}"><a href="{$url}" title="{$result.title}">{$result.title|truncate:20}</a> </td>
            {else}
                <td style="text-align: right;">{$result.$name}</td>
            {/if}
        {/foreach}

    </tr>
    {/search}
</table>

