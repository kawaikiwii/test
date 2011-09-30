{assign var="objectClass" value=$object->getClass()}
{assign var="photo" value=$object->getAssocArray(false)}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}

{assign var="originalInfos" value=$object->getInfosByFormat('original')}

<div class="preview media">
    <h1 class="{$photo.className} info" title="{$photo.title}">{$photo.title|truncate:20:"..."}</h1>
    <p class="photographer">
        {$photo.credits} <br/>({$originalInfos.width} X {$originalInfos.height})
    </p>
    <span class="info" title="{'_BIZ_IMAGE_FILE'|constant}: {$photo.original}"><img src="{$object->getPhotoUrlByFormat('w100')}"/></span>
</div>