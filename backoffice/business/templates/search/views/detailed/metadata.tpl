{assign var="dateFormat" value='_DATE_FORMAT'|constant}
{assign var="dateTimeFormat" value='_DATE_TIME_FORMAT'|constant}


        <table cols="2">
            <tr>
                <td><span class="icon {$objectClass} {$objectSubClass}" title="{$objectClass|capitalize}" /></td>
				<td />
            </tr>
            <tr>
                <th id="state">{'_BIZ_WORKFLOW_STATE'|constant}</th>
                <td>{$object->workflowState|capitalize}</td>
            </tr>
            <tr>
                <th>{'_BIZ_PUBLISHED'|constant}</th>
				{assign var=section value=$object->getChannel()}
				<td class="date" title="{if $object->publicationDate}{'_BIZ_PUBLISHED_IN'|constant} {$section->title}{/if}">
					{if $object->publicationDate}
					    <span class="has_details">{$object->publicationDate|date_format:$dateFormat}</span>
					{else}
						{'_BIZ_NEVER_PUBLISHED'|constant}		
					{/if}
				</td>
            </tr>
            <tr>
                <th>{'_BIZ_MODIFIED_AT'|constant}</th>
				{* Get the right name to display *}
				{if $modifiedByUserName eq '_ADMINISTRATOR'}
					{assign var="modifiedBy" value=$modifiedByUserName|constant}
				{else}
				 	{assign var="modifiedBy" value=$modifiedByUserName}
				{/if}
				
				<td class="date" title="{'_BIZ_MODIFIEDAT'|constant}{$object->modifiedAt|date_format:$dateTimeFormat} {'_BIZ_BY'|constant} {$modifiedBy}">
					<span class="has_details">{$object->modifiedAt|date_format:$dateFormat}</span>
				</td>
            </tr>
        </table>
