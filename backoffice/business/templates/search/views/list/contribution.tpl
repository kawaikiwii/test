{*  Set default values to type specific attributes to be displayed in search results *}

{assign var="prefix" value='_WEB_COMMENT_ON'|constant }
{assign var="referent" value=$object->getReferent() }
{if $referent ne null}
    {assign var="referentTitle" value=$referent->title|truncate:45:"..." }
    {assign var="referentClass" value=$referent->getClass()|capitalize }
{else}
    {assign var="referentTitle" value='_BIZ_CONTRIBUTION_ORPHAN'|constant }
    {assign var="referentClass" value='_BIZ_CONTRIBUTION_NO_REFERENT'|constant }
{/if}

{assign var="titleColumnValue" value="`$object->title` `$object->text`"}
{assign var="titleColumnHover" value="`$prefix``$referentTitle` (`$referentClass`)"}


{* Display the row using the set attributes *}
{wcm name="include_template" file="search/views/list/row.tpl"}
