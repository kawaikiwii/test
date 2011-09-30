{assign var="objectClass" value=$object->getClass()}
{assign var=objectSubClass value=$object->type}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}


	{assign var="type" value=$object->type|capitalize}
    <div class="preview textual">

        <h1 class="{$objectClass}{$objectSubClass}">{$object->title} ({$type})</h1>
        <p>{$object->location}</p>

    </div>
    
