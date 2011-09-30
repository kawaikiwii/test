<li class="bizobject" id="{$className}-{$bo->id}">    
    <div class="toolbar">
        <div class="button">
            <a href="" class="add"><span>Add</span></a>
        </div>
        {$bo->getClass()}
    </div>
    <div>
        <div class="content" style="height:100px; overflow:hidden">
            <b>{$bo->title}</b><br/>
            {$bo->caption}
        </div>
    </div>
</li>