<li class="bizobject grid" id="{$className}-{$bo->id}">
    <div class="toolbar">
        <div class="button">
            <a href="" class="add"><span>Add</span></a>
        </div>
        <span class="{$className}" title="{$bo->getClass()}"><span style="visibility: hidden;">{$bo->getClass()}</span></span>
    </div>
    <div>
        <div class="content" style="height:100px; overflow:hidden">
            <img height="80" src="{$bo->getPhotoUrlByFormat('w100')}" alt="{$bo->title}" hspace="2" vspace="2" style="float:left;"/><br clear="all" />
            <u class="info" title="{$bo->title}" ><b>{$bo->title|truncate:15:"...":true}</b></u><br/>
        </div>
    </div>
</li>