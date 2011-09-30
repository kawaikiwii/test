<table cellspacing="0">
    <tr>
        {foreach from=$params.fields key=name item=field}
        <th>{$field|capitalize|constant}</th>
        {/foreach}
        <th>{'_ACTION'|constant}</th>
    </tr>
    {loop class="wcmLock" orderby=$params.groupBy name="elem"}
        <tr class="{cycle values=",alternate"}">
            {foreach from=$params.fields key=name item=field}
                {if $name eq "object"}
                    {if $elem.object.name}
                        <td> {$elem.object.name|capitalize|constant} </td>
                    {elseif $elem.object.title}
                        <td> {$elem.object.title|capitalize|constant} </td>
                    {else}
                        <td> {$elem.objectClass}capitalize|constant} #{$elem.objectId} </td>
                    {/if}
                {elseif $name eq "user"}
                    <td class="nowrap"> {$elem.user.name|constant} </td>
                {elseif $name eq "lockDate" or $name eq "expirationDate"}
                    <td class="nowrap"> {$elem.$name|date_format:"%Y-%m-%d @ %H:%M"} </td>
                {else}
                    <td> {$elem.$name} </td>
                {/if}
            {/foreach}
            <td>
                <span id="{$module.id}_{$elem.objectClass}{$elem.objectId}">
                {assign var=command value="_UNLOCK"}
                <a href="javascript:wcmSysAjaxController.call('wcm.lock',
                        {ldelim}
                            itemId: '{$module.id}_{$elem.objectClass}{$elem.objectId}',
                            command: 'unlock',
                            objectClass: '{$elem.objectClass}',
                            objectId: '{$elem.objectId}'
                        {rdelim});">{$command|constant}</a>
                </span>
            </td>
        </tr>
    {/loop}
</table>