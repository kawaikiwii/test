{* 	Set values to type specific attributes to be displayed in search results *}

{assign var="title" value=$object->title}
{assign var="type" value=$object->type|capitalize}
{assign var=objectSubClass value=$object->type}
{assign var="titleColumnValue" value="`$title` (`$type`)"}
{assign var="titleColumnHover" value=$object->location}


{* Display the row using the set attributes *}
{wcm name="include_template" file="search/views/list/row.tpl"}
