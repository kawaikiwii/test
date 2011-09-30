<li class="bizobject grid" id="{$className}-{$bo->id}">
    <div class="toolbar">
        <div class="button">
            <a href="" class="add"><span>Add</span></a>
        </div>
        <span class="{$className}" title="{$bo->getClass()}"><span style="visibility: hidden;">{$bo->getClass()}</span></span>
    </div>
    <div>
        <div class="content" style="height:100px; overflow:hidden">
            <a href="?_wcmAction=business/personality&id={$bo->id}" target="edit"><img src="img/actions/edit.gif" title="Edit" border="0"></a>&nbsp;<u class="info" title="{$bo->title}" ><b>{$bo->title|truncate:15:"...":true}</b></u><br/>
            {$bo->job|truncate:45:"...":true|nl2br}
        </div>
    </div>
</li>