{* 	Set values to type specific attributes to be displayed in search results *}

{assign var="pollKinds" value=$object->getKindList() }
{assign var="kind" value=$object->kind }

{assign var="titleColumnValue" value=$object->title}
{assign var="titleColumnHover" value="`$pollKinds.$kind`"}

{* Display the row using the set attributes *}
{wcm name="include_template" file="search/views/list/row.tpl"}
