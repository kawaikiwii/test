{assign var=channelname value=$params.channel}
{php}
	$channelname = $this->get_template_vars('channelname');
	if ($channelname)
	{
		$rub = channel::getSubChannelsIds();
		$whereChannel = ' AND (';
		foreach ($rub as $channel)
		{
			if ($channel['rubric'] == $channelname)
			{
				for ($i = 0; $i<sizeof($channel['ids']); $i++)
				{
					$whereChannel .= 'channelId:'.$channel['ids'][$i].' OR ';
				}
				$whereChannel = substr($whereChannel, 0, strlen($whereChannel)-4);
				$whereChannel .= ')';
			}
		}
	}
	else
	{
		$whereChannel = '';
	}
	$this->assign('whereChannel', $whereChannel);
{/php}
<table cellspacing="0" cellpadding="0">
    <tr>
    {foreach from=$params.fields key=name item=field}
        <th>{$field|constant}</th>
    {/foreach}
    </tr>
    {search where="`$params.query``$whereChannel`" order="`$params.orderBy`" limit="`$params.limit`" name="result"}
    {assign var=url value="?_wcmAction=business/`$result.className`&amp;id=`$result.id`"}
    <tr title="{$result.title}">

                <td width="70%">
                	<a href="{$url}">
	                	{$result.title|SUBSTR:0:85}
	                	{if strlen($result.title) > 85}
	                		(&hellip;)
	                	{/if}
                	</a>
                </td>

            	<td width="12%">
            		{$result.categorization.mainChannel_label_translated|SUBSTR:0:10}
            	</td>

                <td width="18%" style="padding-right:0;">
                	{$result.publicationDate}
                </td>


    </tr>
    {/search}
</table>
