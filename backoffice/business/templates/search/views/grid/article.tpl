{assign var="objectClass" value=$object->getClass()}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}


	<div class="preview textual">
	
		<h1 class="{$objectClass} info" title="{$object->title}">{$object->title|truncate:50:"..."}</h1>
		<p class="info" title="{$object->abstract|strip_tags}">{$object->abstract|strip_tags|truncate:180:"..."}</p>

	</div>

