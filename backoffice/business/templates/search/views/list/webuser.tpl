{* 	Set values to type specific attributes to be displayed in search results *}

{assign var="titleColumnValue" value="`$object->lastname`, `$object->firstname`"}

{assign var="prefix" value='_BIZ_USERNAME'|constant}
{assign var="username" value=$object->username}
{assign var=commentCount value=$object->getContributionCount()}
{if $commentCount > 1}
	{assign var="postfix" value='_BIZ_CONTRIBUTIONS'|constant|lower}
{else}
	{assign var="postfix" value='_BIZ_CONTRIBUTION'|constant|lower}
{/if}


{assign var="titleColumnHover" value="`$prefix`: `$username` - `$commentCount` `$postfix`"}


{* Display the row using the set attributes *}
{wcm name="include_template" file="search/views/list/row.tpl"}
