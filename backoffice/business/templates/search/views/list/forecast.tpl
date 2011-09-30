{* 	Set values to type specific attributes to be displayed in search results *}
{assign var="titleColumnValue" value=$object->title}
{assign var="titleColumnHover" value=$object->title}

{assign var="sourceColumnValue" value=$object->type}
{assign var="sourceColumnValue" value=$object->type}


{* Display the row using the set attributes *}
{wcm name="include_template" file="search/views/list/row.tpl"}
