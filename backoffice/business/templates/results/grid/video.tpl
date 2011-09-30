<li class="bizobject grid" id="video-{$bo->id}">
    <div class="toolbar">
        <div class="button">
            <a href="" class="add"><span>Add</span></a>
        </div>
        <span class="{$className}" title="{$bo->getClass()}"><span style="visibility: hidden;">{$bo->getClass()}</span></span>
    </div>
    <div>
        <div class="content" style="height:50px; overflow:hidden;font-size:x-small;">   
           {if $bo->sourceId != ""}<img src="/img/icons/content/page_script.gif" border="0">{/if}
           <u class="info" title="{$bo->title}">><b>{$bo->title}</b></u><br/>   
        </div>
    </div>
</li>
