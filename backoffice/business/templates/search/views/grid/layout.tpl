{wcm name="include_template" file="search/views/resultset-info.tpl"}
{if $searchContext->numItemsFound gt 0}
    <div id="resultset">
        <ul id="toolbar">
          	<li class="select-all">
                <a href="" onclick="toggleCheckboxes('item_'); return false">
                    {'_BIZ_TOGGLE'|constant}
                </a>
            </li>
            <li id="className">
                <a href="#" onclick="toggleSortOrder('className', 'ASC')">
                {'_BIZ_TYPE'|constant}
                </a>
            </li>
            <li id="title_sort">
                <a href="#" onclick="toggleSortOrder('title_sort', 'ASC')">
                {'_BIZ_TITLE'|constant}
                </a>
            </li>
            <li id="state">
                <a href="#" onclick="toggleSortOrder('workflowState', 'ASC')">
                {'_BIZ_WORKFLOW_STATE'|constant}
                </a>
            </li>
            <li id="publicationDate">
                <a href="#" onclick="toggleSortOrder('publicationDate', 'DESC')">
                {'_BIZ_PUBLISHED'|constant}
                </a>
            </li>
            <li id="modifiedAt">
                <a href="#" onclick="toggleSortOrder('modifiedAt', 'DESC')">
                {'_BIZ_MODIFIED'|constant}
                </a>
            </li>
        </ul>

        {foreach from=$searchContext->items item=object}

        <div class="box">
            
            <div class="toolbar">
                {* include lie toolbar *}
                {wcm name="include_template" file="search/views/grid/toolbar.tpl"}
            </div>

            {assign var="objectClass" value=$object->getClass()}
            {wcm name="render_object" object=$object
                template="search/views/grid/$objectClass.tpl"
                default_template="search/views/grid/default.tpl"}

            <div class="metadata">
                {* include generic metadata *}
                {wcm name="include_template" file="search/views/grid/metadata.tpl"}
            </div>

        </div>

        {/foreach}
    </div>

{wcm name="include_template" file="search/views/resultset-navigation.tpl"}

{/if}
