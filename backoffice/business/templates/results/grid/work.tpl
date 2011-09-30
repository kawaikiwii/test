<li class="bizobject grid" id="{$className}-{$bo->id}">
    <div class="toolbar">
        <div class="button">
            <a href="" class="add"><span>Add</span></a>
        </div>
        <span class="{$className}" title="{$bo->getClass()}"><span style="visibility: hidden;">{$bo->getClass()}</span></span>
    </div>
    <div>
        <div class="content" style="height:100px; overflow:hidden">
            <u class="info" title="{$bo->title}" ><b>{$bo->title|truncate:15:"...":true}</b></u><br/>
            <u>type</u> : {$bo->type}<br/>
            {foreach from=$bo->getSpecificInfos() key=label item=info}
	        	{if $info !=""}
	        		<u>{$label}</u>: {$info}<br/>
	        	{/if}
	        {/foreach} 
        </div>
    </div>
</li>