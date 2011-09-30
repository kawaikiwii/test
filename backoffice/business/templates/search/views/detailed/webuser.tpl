{assign var="objectClass" value=$object->getClass()}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}
{assign var="dateTimeFormat" value='_DATE_TIME_FORMAT'|constant}

<div class="row">
    
    <div class="toolbar">
        {* include the toolbar *}
        {wcm name="include_template" file="search/views/detailed/toolbar.tpl"}
    </div>

    <div class="details {$objectClass}">
        <ul>
            <li class="detail title">{$object->lastname}, {$object->firstname}</li>

            {assign var="prefix" value='_BIZ_USERNAME'|constant}
            {assign var="username" value=$object->username}
            {assign var=commentCount value=$object->getContributionCount()}
            {if $commentCount > 1}
                {assign var="postfix" value='_BIZ_CONTRIBUTIONS'|constant|lower}
            {else}
                {assign var="postfix" value='_BIZ_CONTRIBUTION'|constant|lower}
            {/if}
            <li class="detail">{$prefix} {$username}</li>
            <li class="detail"><span class="label">{'_BIZ_LAST_LOGIN'|constant}</span> {$object->lastLogin|date_format:$dateTimeFormat}</li>
            <li class="detail">{$commentCount} {$postfix}</li>

            <li class="detail"><span class="label">{'_BIZ_ADDRESS'|constant}</span> {$object->address}</li>
            <li class="detail"><span class="label">{'_BIZ_CITY'|constant}</span> {$object->city}</li>
            <li class="detail"><span class="label">{'_BIZ_STATE_PROVINCE'|constant}</span> {$object->state}</li>
            <li class="detail"><span class="label">{'_BIZ_COUNTRY'|constant}</span> {$object->country}</li>
            <li class="detail"><span class="label">{'_BIZ_POSTALCODE'|constant}</span> {$object->postalCode}</li>
            
        </ul>

    </div>
    
    <div class="metadata">
        {* include generic metadata *}
        {wcm name="include_template" file="search/views/detailed/metadata.tpl"}
    </div>
</div>
