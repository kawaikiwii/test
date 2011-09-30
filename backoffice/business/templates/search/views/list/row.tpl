{assign var="objectClass" value=$object->getClass()}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}
{assign var="dateFormat" value='_DATE_FORMAT'|constant}
{assign var="dateTimeFormat" value='_DATE_TIME_FORMAT'|constant}
{assign var="photos" value=$object->getPhotos()}
{php}
$itemSelector = $this->get_template_vars('itemSelector');

if (isset($_SESSION['tempBin'][$itemSelector]))
{
    $this->assign('checked','checked');
} else {
    $this->assign('checked','');
}
{/php}

<td class="actions">
    <div class="toolbar" style="width:100px;">
        <ul>
            <li>
                <input type="checkbox" id="item_{$itemSelector}"
                    onclick="var command = ($('item_{$itemSelector}').checked
                        ? 'addToSessionBin'
                        : 'removeFromSessionBin');
                        manageBin(command, '', '', '{$itemSelector}', '', 'compteur', '')"
                {if $checked eq 'checked'} checked="checked" {/if} />
            </li>
            
            
            
            
            <li>
                <a href="#" class="add" title="{'_BIZ_SEARCH_ADD_TO_SELECTED_BIN'|constant}" onclick="manageBin('addToSelectedBin', '', '', '{$itemSelector}', $('selectBin').options[$('selectBin').selectedIndex].value, 'binData', '')">
                    <span>{'_BIZ_SEARCH_ADD_TO_SELECTED_BIN'|constant}</span>
                </a>
            </li>
            
            
            {assign var="titleForElement" value=$object->title}
            {php}
            	$title = str_replace("'", '', $this->get_template_vars('titleForElement'));
            	$title = str_replace('"', '', $title);
            	$title = str_replace('`', '', $title);
            	$this->assign('elemTitle', $title);
            {/php}
            <li>
           	 <a class="delete" title="{'_BIZ_DELETE'|constant}" href="javascript:deleteImport('{$elemTitle}', '{$objectClass}', '{$object->id}', 'delete_tr_{$object->id}');">
                    <span>{'_BIZ_DELETE'|constant}</span>
                </a>
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
    </div>
</td>
<!--<td class="type">
    <span class="{$objectClass}{$objectSubClass}" title="{if $objectClass == 'channel'}{'_BIZ_SECTION'|constant}{else}{$objectClass}{/if}">{$objectClass|capitalize}</span>
</td>-->






{php}
	if (!isset($_GET['search_baseQuery']) && !isset($_GET['search_basequery'])) { $this->assign('globalSearch', true); }
	else { $this->assign('globalSearch', false); }
{/php}

{if $globalSearch eq true}
			
	{include file="`$config.wcm.templates.path`search/views/list/rowGlobalSearch.tpl"}

{else}

    {include file="`$config.wcm.templates.path`search/views/list/rowDefault.tpl"}
   
{/if}


