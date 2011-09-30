<table cellspacing="0">
    <tr>
        {foreach from=$params.fields key=name item=field}
            <th>{$field|constant}</th>
        {/foreach}
        <th>&nbsp;</th>
    </tr>
    {search where="`$params.query`" order="`$params.orderBy`" limit="`$params.limit`" name="elem"}
    {assign var=url value="?_wcmAction=business/`$elem.className`&amp;id=`$elem.id`"}
    {assign var=uniqueId value="`$elem.className`_`$elem.id`"}
    <tr{cycle values=", class='alternate'"} id="{$uniqueId}">
        {foreach from=$params.fields key=name item=field}
            {if $name eq "modifiedAt" or $name eq "createdAt" or $name eq "publicationDate"}
                <td>{$elem.$name|date_format:"%Y-%m-%d @ %H:%M"}</td>
            {elseif $name eq "title"}
                <td class="type {$elem.className}" width="50%"><a href="{$url}">{$elem.title}</a></td>
            {else}
                <td>{$elem.$name}</td>
            {/if}
        {/foreach}
        <td>
        <ul style="float: right;">
            {foreach from=$elem.transitions item=transition}
                <li><a href="javascript:executeTransition('{$elem.className}', '{$elem.id}','{$transition.id}', '{$uniqueId}');">{$transition.name|constant}</a></li>
            {/foreach}
        </ul>
        </td>
    </tr>
    {/search}
</table>