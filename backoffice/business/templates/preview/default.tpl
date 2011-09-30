{include file="preview/includes/header.tpl"}
    <h1> Previewing bizobject {$bizobject.className} #{$bizobject.id} </h1>
    <ul>
    {foreach from=$bizobject key=property item=value}
        {if $property eq "semanticData"}
           <li>{$property} : concepts
           <ul>
                {foreach from=$value.concepts  key=entity item=data}
                <li> {$entity}
                {/foreach}
           </ul>
           <li>{$property} : categories
           <ul>
                {foreach from=$value.categories  key=entity item=data}
                <li> {$entity}
                {/foreach}
           </ul>
           <li>{$property} : ON
           <ul>
                {foreach from=$value.ON  key=entity item=data}
                <li> {$entity}
                {/foreach}
           </ul>
           <li>{$property} : PN
           <ul>
                {foreach from=$value.PN key=entity item=data}
                <li> {$entity}
                {/foreach}
           </ul>
           <li>{$property} : GL
           <ul>
                {foreach from=$value.GL key=entity item=data}
                <li> {$entity}
                {/foreach}
           </ul>
           <li>{$property} : similars
           <ul>
                {foreach from=$value.similars  key=entity item=data}
                <li> similar to {$data.className} #{$data.id}
                {/foreach}
           </ul>
           <li>{$property} : tone = {$value.tone}
           <li>{$property} : subjectivity = {$value.subjectivity}
        {elseif $property eq "channel"}
        {elseif $property eq "site"}
        {elseif $property eq "state"}
        {else}
           <li>{$property}: {$value}</li>
        {/if}
    {/foreach}
    </ul>
{include file="preview/includes/footer.tpl"}