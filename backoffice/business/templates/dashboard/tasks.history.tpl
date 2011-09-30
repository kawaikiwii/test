<table cellspacing="0" cellpadding="0">
	<tr>
        {foreach from=$params.fields key=name item=field}
            <th>{$field|constant}</th>
        {/foreach}
    </tr>
    {foreach from=$session.history key=url item=history}
	    <tr title="{$history.info}">
	        <td>
	        	<a href="{$url}">
	        		{$history.info}
	        	</a>
	        </td>
	        <td>
	        	{$history.objectClass}
	        </td>
	    </tr>
    {/foreach}
</table>