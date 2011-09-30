<li class="bizobject grid" id="{$className}-{$bo->id}">
    <div class="toolbar">
        <div class="button">
            <a href="" class="add"><span>Add</span></a>
        </div>
        <span class="{$className}" title="{$bo->getClass()}"><span style="visibility: hidden;">{$bo->getClass()}</span></span>
    </div>
    <div>
        <div class="content" style="height:100px; overflow:hidden">
         <u class="info" title="{$bo->title}" ><b>{$bo->title}</b></u><br/><br/>
         {if $bo->jobtitle !=""} <u>jobtitle:</u> {$bo->jobtitle}<br/>{/if}
         {if $bo->company !=""} <u>company:</u> {$bo->company}<br/>{/if}
         {if $bo->city !=""} <u>city:</u> {$bo->city}<br/>{/if}
         {if $bo->country !=""} <u>country:</u> {$bo->country}<br/>{/if}
         </div>
    </div>
</li>