{* 	Set values to type specific attributes to be displayed in search results *}
	{assign var="photos" value=$object->getPhotos()}
	{assign var="photoCount" value=$photos|@count}

	{* Use singular or plural prefix *}
	{if $photoCount > 1}
		{assign var="postfix" value='_BIZ_PHOTOS'|constant}
	{else}
		{assign var="postfix" value='_BIZ_PHOTO'|constant}
	{/if}


{assign var="titleColumnValue" value=$object->title}
{assign var="titleColumnHover" value="`$photoCount` `$postfix`"}

{* Display the row using the set attributes *}
{wcm name="include_template" file="search/views/list/row.tpl"}
