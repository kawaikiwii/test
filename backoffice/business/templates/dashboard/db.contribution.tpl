<table cellspacing="0">
    <tr>
        {foreach from=$params.fields key=name item=field}
            <th>{$field|constant}</th>
        {/foreach}
        <th>&nbsp;</th>
    </tr>
    {loop class=$params.source where=$params.where orderby=$params.orderBy from=$params.from limit=$params.limit name="elem"}
    {assign var=url value="?_wcmAction=business/`$params.source`&amp;id=`$elem.id`"}
    {assign var=uniqueId value="`$params.source`_`$elem.id`"}
    <tr{cycle values=", class='alternate'"} id="{$uniqueId}">
        {foreach from=$params.fields key=name item=field}
            {if $name eq "modifiedAt" or $name eq "createdAt" or $name eq "publicationDate"}
                <td class="nowrap">{$elem.$name|date_format:"%Y-%m-%d @ %H:%M"}</td>
            {elseif $name eq "title"}
                <td class="type {$elem.className}" width="100%">
                    <a href="{$url}">{if $elem.title}{$elem.title}: {/if}{$elem.text}</a>
                </td>
            {else}
                <td width="50%">{$elem.$name}</td>
            {/if}
        {/foreach}
        <td>
        <ul style="float: right;">
            {foreach from=$elem.transitions item=transition}
                <li><a href="javascript:executeTransition('{$params.source}', '{$elem.id}','{$transition.id}', '{$uniqueId}');">{$transition.name|constant}</a></li>
            {/foreach}
        </ul>
        </td>
    </tr>
    {/loop}
    {if $loop.elem.total eq 0}
    <tr><td colspan="$params.fields|@count"> - (empty) - </td></tr>
    {/if}
</table>