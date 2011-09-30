<ul id="{$prefix}list">
{foreach from=$mixedresults item=result}
{if get_class($result) == "wcmObjectAssocArray"}
    {assign var='bizobject' value=$result.destination}
    {assign var='className' value=$bizobject.className}
    <li class="bizrelation" id="rel-{$result.destinationClass}-{$result.destinationId}">
        <div class="toolbar">
        <div class="remove"><span>Remove</span></div>
            {$result.destinationClass}: {$bizobject.title|truncate:40:"..."}
        </div>
        <div class="relproperties" style="background-color: #739EDE;">
            <input type="hidden" value="{$result.destinationId}" name="_list{$pk}[destinationId][]" />
            <input type="hidden" value="{$result.destinationClass}" name="_list{$pk}[destinationClass][]" />
            {php}echo constant('_TITLE'){/php}: <input type="text" name="_list{$pk}[title][]" value="{$bizobject.title}" /><br />
            {wcm name='include_template' file="relations/$className.tpl" fallback='relations/default.tpl'}
        </div>
    </li>
{elseif is_object($result)}
    {assign var="objectClass" value=$result->getClass()}
    {assign var='bizobject' value=$result->getAssocArray()}
    <li class="bizrelation" id="rel-{$result->id}">
        <div class="toolbar">
            {$objectClass}: {$result->title|truncate:40:"..."}
        </div>
        <div class="relproperties">
            <input type="hidden" value="0" name="_list{$pk}[destinationId][]" />
            <input type="hidden" value="displayonly" name="_list{$pk}[destinationClass][]" />
            {php}echo constant('_TITLE'){/php}: <input type="text" name="_list{$pk}[title][]" value="{$result->title}" /><br />
            {wcm name='include_template' file="relations/$objectClass.tpl" fallback='relations/default.tpl'}
        </div>
    </li>
{else}
    <li style="border: 1px dashed red;" id="rel-phantome">&nbsp;
        <div class="relproperties">
            <input type="hidden" value="0" name="_list{$pk}[destinationId][]" />
            <input type="hidden" value="skiprank" name="_list{$pk}[destinationClass][]" />
            <input type="hidden" value="" name="_list{$pk}[title][]" />
        </div>
    </li>
{/if}
{/foreach}

<li style="border: 1px dashed red;" id="rel-phantome">&nbsp;
    <div class="relproperties">
        <input type="hidden" value="0" name="_list{$pk}[destinationId][]" />
        <input type="hidden" value="skiprank" name="_list{$pk}[destinationClass][]" />
        <input type="hidden" value="" name="_list{$pk}[title][]" />
    </div>
</li>
<li style="border: 1px dashed red;" id="rel-phantome">&nbsp;
    <div class="relproperties">
        <input type="hidden" value="0" name="_list{$pk}[destinationId][]" />
        <input type="hidden" value="skiprank" name="_list{$pk}[destinationClass][]" />
        <input type="hidden" value="" name="_list{$pk}[title][]" />
    </div>
</li>
<li style="border: 1px dashed red;" id="rel-phantome">&nbsp;
    <div class="relproperties">
        <input type="hidden" value="0" name="_list{$pk}[destinationId][]" />
        <input type="hidden" value="skiprank" name="_list{$pk}[destinationClass][]" />
        <input type="hidden" value="" name="_list{$pk}[title][]" />
    </div>
</li>
<li style="border: 1px dashed red;" id="rel-phantome">&nbsp;
    <div class="relproperties">
        <input type="hidden" value="0" name="_list{$pk}[destinationId][]" />
        <input type="hidden" value="skiprank" name="_list{$pk}[destinationClass][]" />
        <input type="hidden" value="" name="_list{$pk}[title][]" />
    </div>
</li>

</ul>
