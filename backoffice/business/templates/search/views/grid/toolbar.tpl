{assign var="objectClass" value=$object->getClass()}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}
{php}
$itemSelector = $this->get_template_vars('itemSelector');

if (isset($_SESSION['tempBin'][$itemSelector]))
{
    $this->assign('checked','checked');
} else {
    $this->assign('checked','');
}
{/php}

<ul>
	<li>
		<input type="checkbox" id="item_{$itemSelector}"
		onclick="var command = ($('item_{$itemSelector}').checked
		? 'addToSessionBin'
		: 'removeFromSessionBin');
		manageBin(command, '', '', '{$itemSelector}', '', 'compteur', '')"
		{ $checked != '' ? 'checked="checked"' : ''} />
	</li>
	<li>
		<a href="#" class="add" title="{'_BIZ_SEARCH_ADD_TO_SELECTED_BIN'|constant}"
		onclick="manageBin('addToSelectedBin', '', '', '{$itemSelector}', $('selectBin').options[$('selectBin').selectedIndex].value, 'binData')">
		<span>{'_BIZ_SEARCH_ADD_TO_SELECTED_BIN'|constant}</span></a>
	</li>
	<li>
                <a href="?_wcmAction=business/{$objectClass}&id={$object->id}" class="edit" title="{'_BIZ_EDIT'|constant}">
		<span>{'_BIZ_EDIT'|constant}</span>
		</a>
	</li>
	<li>
		{if $lockedBy eq '_ADMINISTRATOR'}
		{assign var="lockedBy" value=$lockedBy|constant}
		{/if}

		{if $locked eq 'TRUE'}
		<a href="#" class="lock" title="{'_LOCKED_BY'|constant}{$lockedBy}">
		<span>{'_LOCK'|constant}</span>
		</a>
		{elseif $locked eq 'ME'}
		<a href="#" class="locked_by_me" title="{'_LOCKED_BY_ME'|constant}">
		<span>{'_LOCK_LOCKED_BY_ME'|constant}</span>
		</a>
		{/if}
	</li>
</ul>
