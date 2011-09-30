<li class="bizobject grid" id="{$className}-{$bo->id}">
    <div class="toolbar">
        <div class="button">
            <a href="" class="add"><span>Add</span></a>
        </div>
        <span class="{$className}" title="{$bo->getClass()}"><span style="visibility: hidden;">{$bo->getClass()}</span></span>
    </div>
    <div>
        <div class="content" style="height:100px; overflow:hidden">
        <u class="info" title="{$bo->title}" ><b>{$bo->name}</b></u><br/><br/>
         {$bo->address_1}<br/>
         {$bo->zipcode}<br/>
         {$bo->city}<br/>
         {$bo->country}<br/></div>
    </div>
</li>