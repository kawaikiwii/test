{wcm name="include_template" file="search/views/resultset-info.tpl"}
{if $searchContext->numItemsFound gt 0}
    <div id="resultset">
        <table id="toolbar">
            <tr>
                <th class="select-all">
                    <a href="" onclick="toggleCheckboxes('item_'); return false">
                        {'_BIZ_TOGGLE'|constant}
                    </a>
                </th>
                <th id="className">
                    <a href="#" onclick="toggleSortOrder('className', 'ASC')">
                    {'_BIZ_TYPE'|constant}
                </a>
                </th>
                <th id="title_sort">
                    <a href="#" onclick="toggleSortOrder('title_sort', 'ASC')">
                    {'_BIZ_TITLE'|constant}
                </a>
                </th>
                <th id="state">
                    <a href="#" onclick="toggleSortOrder('workflowState', 'ASC')">
                    {'_BIZ_WORKFLOW_STATE'|constant}
                </a>
                </th>
                <th id="publicationDate">
                    <a href="#" onclick="toggleSortOrder('publicationDate', 'DESC')">
                    {'_BIZ_PUBLISHED'|constant}
                </a>
                </th>
                <th id="modifiedAt">
                    <a href="#" onclick="toggleSortOrder('modifiedAt', 'DESC')">
                    {'_BIZ_MODIFIED'|constant}
                </a>
                </th>
            </tr>
        </table>

        {foreach from=$searchContext->items item=object}
            {assign var="objectClass" value=$object->getClass()}
            {wcm name="render_object" object=$object
                template="search/views/detailed/$objectClass.tpl"
                default_template="search/views/detailed/default.tpl"}
        {/foreach}
    </div>

    {wcm name="include_template" file="search/views/resultset-navigation.tpl"}

{/if}
