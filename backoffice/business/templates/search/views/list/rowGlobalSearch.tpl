<td class="type">
    {$photos|@count}
</td>

<td style="padding-left:10px;">
   {php} $this->assign('objectClassName', ucfirst($this->get_template_vars('object')->getClass())); {/php}
  <b> {$objectClassName}</b>
</td>

<td class="title" nowrap>
    <a href="?_wcmAction=business/{$objectClass}&id={$object->id}" class="edit" title="{'_BIZ_EDIT'|constant}">
    {php}
    	/*<u title="[{if $objectClass == 'channel'}{'_BIZ_SECTION'|constant}{else}{$objectClass|capitalize}{/if}] {$titleColumnHover}" class="info">{if $objectClass == 'contribution'}&laquo; {/if}{$titleColumnValue|truncate:45:"..."}{if $objectClass == 'contribution'} &raquo;{/if}</u>*/
    {/php}
    <u class="info">{if $objectClass == 'contribution'}&laquo; {/if}{$titleColumnValue|truncate:40:"..."}{if $objectClass == 'contribution'} &raquo;{/if}</u>
    </a>
    
</td>



<td class="channelId">
   {assign var="channelIdName" value=$object->getChannelName($object->channelId)}
   {$channelIdName}
</td>


<td class="workflowState">
    <i class="workflowStateColor {$object->workflowState}">{$object->workflowState|capitalize}</i>
</td>

{assign var=section value=$object->getChannel()}
<td class="date">
    <u class="info" title="{if $object->publicationDate}{'_BIZ_PUBLISHED_IN'|constant} {$section->title}{/if}">{$object->publicationDate|date_format:$dateTimeFormat}</u>
</td>

{* Get the right name to display *}
{if $modifiedByUserName eq '_ADMINISTRATOR'}
	{assign var="modifiedBy" value=$modifiedByUserName|constant}
{else}
 	{assign var="modifiedBy" value=$modifiedByUserName}
{/if}

<td class="date">
	<u class="info" title="{'_BIZ_MODIFIEDAT'|constant}{$object->modifiedAt|date_format:$dateTimeFormat} {'_BIZ_BY'|constant} {$modifiedBy}">{$object->modifiedAt|date_format:$dateFormat}
	{php}
		$modifiedBy = $this->get_template_vars('modifiedBy');
		if (strpos($modifiedBy,' '))
			$this->assign('modifiedByShort', substr($modifiedBy,0,strpos($modifiedBy,' ')));
		else
			$this->assign('modifiedByShort', $modifiedBy);
	{/php}
	({$modifiedByShort})</u>
</td>

<td class="createdBy">
	{if $createdByUserName eq '_ADMINISTRATOR'}
		{assign var="createdBy" value=$createdByUserName|constant}
	{else}
	 	{assign var="createdBy" value=$createdByUserName}
	{/if}
	<u class="info" title="{$createdBy}">
		{php}
			$createdBy = $this->get_template_vars('createdBy');
			if (strpos($createdBy,' '))
				$this->assign('createdByShort', substr($createdBy,0,strpos($createdBy,' ')));
			else
				$this->assign('createdByShort', $createdBy);
		{/php}
		{$createdByShort}
	</u>
</td>