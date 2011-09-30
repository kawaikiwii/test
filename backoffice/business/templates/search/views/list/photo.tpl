{* 	Set values to type specific attributes to be displayed in search results *}
{assign var="titleColumnValue" value=$object->title}
{assign var="prefix" value='_BIZ_IMAGE_FILE'|constant}
{assign var="original" value=$object->original}
{assign var="titleColumnHover" value="$prefix: $original"}


{* Display the row using the set attributes *}
{wcm name="include_template" file="search/views/list/row.tpl"}
