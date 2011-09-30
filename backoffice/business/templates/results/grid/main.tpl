<li class="bizobject grid" id="{$className}-{$bo->id}" style="  clear: none; margin-left: 1px;">    
    <div class="toolbar">
        <div class="button">
            <a href="" class="add"><span>Add</span></a>
        </div>
        <span class="{$className}" title="{$bo->getClass()}"><span style="visibility: hidden;">{$bo->getClass()}</span></span>
    </div>
    <div>
        <div class="content" style="height:100px; overflow:hidden">
            <u class="info" title="{$bo->title}"><b>{$bo->title|truncate:45:"..."}</b></u><br/> 
        </div>
    </div>
</li>