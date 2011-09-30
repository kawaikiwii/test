<li class="bizobject grid" id="photo-{$bo->id}">
    <div class="toolbar">
        <div class="button">
            <a href="" class="add"><span>Add</span></a>
        </div>
        <span class="{$className}" title="{$bo->getClass()}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
		{if $bo->specialUses != ""}
		<img src="/skins/default/images/gui/lock.png" alt="SPECIAL USES" title="SPECIAL USES" />
		{/if}
		 &copy;{$bo->credits|truncate:5:"":true}
    </div>
    <div>
        <div class="content" style="height:130px; overflow:hidden">
            <h3>{$bo->title|truncate:25:"...":true}</h3>
            	
			<div style="text-align:center">
            	<img height="100" src="{$bo->getPhotoUrlByFormat('h100')}" alt="{$bo->title} - {$bo->specialUses}" title="{$bo->title} - {$bo->specialUses}" />
            </div>
			
        </div>
    </div>
</li>
