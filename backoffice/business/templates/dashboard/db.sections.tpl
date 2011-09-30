<table cellspacing="0">
    <tr>
    	<th> Section </th>
    	<th> Web URL </th>
    </tr>
    {loop class="article" orderby="modifiedAt DESC" limit="10"}
    {assign var=weburl value=$article|@wcm:url}
    <tr{cycle values=", class='alternate'"}>
		<td> <a target="web" href="{$article.channel|@wcm:url}">{$article.channel.title}</a> </td>
		<td> <a target="web" href="{$weburl}">{$weburl}</a> </td>
    </tr>
    {/loop}
</table>