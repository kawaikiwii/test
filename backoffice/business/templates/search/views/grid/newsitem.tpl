{assign var="objectClass" value=$object->getClass()}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}


    <div class="preview textual">

        <h1 class="{$objectClass}">{$object->title} ({$type})</h1>
        <p class="info" title="{$object->text|strip_tags}">{$object->text|strip_tags|truncate:100:"..."}</p>

    </div>
    
