<div class="toolbar {$bizobject.className}">
    <div class="remove"><span>{'_REMOVE'|constant}</span></div>
    {*$bizobject.className*}
</div>


{php}
	$all_tpl_vars = $this->get_template_vars();
	$title = $all_tpl_vars['bizobject']['title'];
	$pk = $all_tpl_vars['pk'];
	wcmGUI::openCollapsablePane($title);
{/php}


	<div class="relproperties">
	    
	<input type="hidden" name="_list{$pk}[destinationClass][]" value="{$bizobject.className}" />
	<input type="hidden" name="_list{$pk}[destinationId][]" value="{$bizobject.id}" />
	    

	<input type="hidden" name="_list{$pk}[media_description][]" value="" />
	<input type="hidden" name="_list{$pk}[media_text][]" value="" />
	<input type="hidden" name="_list{$pk}[title][]" value="" />





<div style="display:none">

	<input type="hidden" name="schedule_event_destinationId" id="schedule_event_destinationId" value="{$bizobject.id}" />

</div>

	    <img src="{$config.wcm.webSite.urlRepository}illustration/photo/{$bizobject.createdAt|date_format:"%Y/%m/%d"}/{$bizobject.thumbnail}" alt="" style="margin-left: 46px; margin-top: 4px"/>
	</div>

{php}
	wcmGUI::closeCollapsablePane();
{/php}
