{assign var="dateFormat" value='_DATE_FORMAT'|constant}
{assign var="dateTimeFormat" value='_DATE_TIME_FORMAT'|constant}


<table>
{if $objectClass == 'photo'}

{/if}
	<tr>
		<td id="state">{'_BIZ_WORKFLOW_STATE'|constant}</td>
		<td>{$object->workflowState|capitalize}</td>
	</tr>
	<tr>
		<td>{'_BIZ_PUBLISHED'|constant}</td>
		{assign var=section value=$object->getChannel()}
		<td class="date info" title="{if $object->publicationDate} {'_BIZ_PUBLISHED_IN'|constant} {$section->title}{/if}">
        {if $object->publicationDate}
		<u class="info">{$object->publicationDate|date_format:$dateFormat}</u>
		{else}
		{'_BIZ_NEVER_PUBLISHED'|constant}		
		{/if}
		</td>
	</tr>
	<tr>
		<td>{'_BIZ_MODIFIED_AT'|constant}</td>
		{* Get the right name to display *}
		{if $modifiedByUserName eq '_ADMINISTRATOR'}
		{assign var="modifiedBy" value=$modifiedByUserName|constant}
		{else}
		{assign var="modifiedBy" value=$modifiedByUserName}
		{/if}

		<td class="date" title="{'_BIZ_MODIFIEDAT'|constant}{$object->modifiedAt|date_format:$dateTimeFormat} {'_BIZ_BY'|constant} {$modifiedBy}">
		<u class="info">{$object->modifiedAt|date_format:$dateFormat}</u>
		</td>
	</tr>
</table>
