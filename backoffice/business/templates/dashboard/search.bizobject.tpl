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
            {if $name eq "modifiedAt" or $name eq "createdAt" or $name eq "publicationDate"}
                <td class="nowrap"> <a href="{$url}">{$result.$name|date_format:"%Y-%m-%d @ %H:%M"}</a> </td>
            {elseif $name eq "title"}
                <td class="type {$result.className}"><a href="{$url}">{$result.$name}</a> </td>
            {else}
                <td>{$result.$name}</td>
            {/if}
        {/foreach}
    </tr>
    {/loop}
    {if $result.total eq 0}
    <tr><td colspan="$params.fields|@count"> - (empty) - </td></tr>
    {/if}
</table>
