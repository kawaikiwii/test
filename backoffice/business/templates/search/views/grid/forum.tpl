{assign var="objectClass" value=$object->getClass()}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}


    <div class="preview textual">

        <h1 class="{$objectClass} info" title="{$object->title}">{$object->title|truncate:45:"..."}</h1>
        <p class="info" title="{$object->description|strip_tags}">{$object->description|strip_tags|truncate:100:"..."}</p>

    </div>
    
