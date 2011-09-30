<div id="{$prefix}relations" class="relations">
<ul id="{$prefix}list">

{foreach from=$relations item=relation name=media}

{assign var='bizobject' value=$relation.destination}
{assign var='className' value=$bizobject.className}
{assign var='myId' value=$smarty.foreach.media.iteration}

<li class="bizrelation" id="rel-{$relation.destinationClass}-{$relation.destinationId}">
    <div class="toolbar">
        <div class="remove"><span>Remove</span></div>
        <div style="display:none">{$relation.destinationClass}: {$bizobject.title|truncate:40:"..."}</div>
    </div>

    {php}
    	
		$all_tpl_vars = $this->get_template_vars();
		$title = $all_tpl_vars['bizobject']['title'];
		$pk = $all_tpl_vars['pk'];
		wcmGUI::openCollapsablePane(substr($title, 0, 40), false);
		
	{/php}

	<input type="hidden" value="{$relation.destinationId}" name="_list{$pk}[destinationId][]" />
	<input type="hidden" value="{$relation.destinationClass}" name="_list{$pk}[destinationClass][]" />

	<input type="hidden" name="schedule_event_destinationId" id="schedule_event_destinationId" value="{$bizobject.id}" />
	
	<input type="hidden" name="_list{$pk}[media_description][]" value="" />
	<input type="hidden" name="_list{$pk}[media_text][]" value="" />
	<input type="hidden" name="_list{$pk}[title][]" value="" />
        
	<div style="clear:both;"></div>

    {php}
    	wcmGUI::closeCollapsablePane();
    {/php}

</li>
{/foreach}
</ul>
</div>