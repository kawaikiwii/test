<li class="bizobject" id="photo-{$bo->id}">
    <div class="toolbar">
        <div class="button">
            <a href="" class="add"><span>Add</span></a>
        </div>
        {$bo->getClass()}
    </div>
    <div>
        <div class="content" style="height:100px; overflow:hidden">
        <img height="80" src="{$bo->getPhotoUrlByFormat('w100')}" alt="" hspace="2" vspace="2" style="float:left;"/>
            <b>{$bo->title}</b><br/>
            {$bo->caption}
        </div>
    </div>
</li>