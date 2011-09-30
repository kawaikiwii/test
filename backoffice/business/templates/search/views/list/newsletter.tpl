{* 	Set values to type specific attributes to be displayed in search results *}
	{assign var="documents" value=$object->getAssoc_related(false)}
	{assign var="documentCount" value=$documents|@count}

	{assign var="postfix" value='_BIZ_DOCUMENT'|constant}
	{* Add the 's' at the end of the postifx if necessary *}
	{if $documentCount > 1}
		{assign var="postfix" value="`$postfix`s"}
	{/if}

{assign var="titleColumnValue" value=$object->title}
{assign var="titleColumnHover" value="`$documentCount` `$postfix`"}

{* Display the row using the set attributes *}
{wcm name="include_template" file="search/views/list/row.tpl"}
