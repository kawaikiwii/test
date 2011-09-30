<table cellspacing="0">
    <tr>
    {foreach from=$params.fields key=name item=field}
        <th>{$field|constant}</th>
    {/foreach}
    </tr>
    {loop class=$params.source where="siteId = '`$params.siteId`' AND `$params.where`" orderby=$params.orderBy from=$params.from limit=$params.limit name="elem"}
    {assign var=url value="?_wcmAction=business/`$params.source`&amp;id=`$elem.id`"}
    <tr{cycle values=", class='alternate'"}>
        {foreach from=$params.fields key=name item=field}
            {if $name eq "modifiedAt" or $name eq "createdAt" or $name eq "publicationDate"}
                <td class="nowrap"> <a href="{$url}">{$elem.$name|date_format:"%Y-%m-%d @ %H:%M"}</a> </td>
            {else}
                <td> <a href="{$url}">{$elem.$name}</a> </td>
            {/if}
        {/foreach}
    </tr>
    {/loop}
    {if $loop.elem.total eq 0}
    <tr><td colspan="$params.fields|@count"> - (empty) - </td></tr>
    {/if}
</table>
