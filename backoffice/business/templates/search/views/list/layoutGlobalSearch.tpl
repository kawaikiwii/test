<table cols="6">
      	<tr>
      	    <th class="select-all">
                <a href="" onclick="toggleCheckboxes('item_'); return false">
                    {'_BIZ_TOGGLE'|constant}
                </a>
            </th>

      	    <th>
      		    {'_BIZ_TYPE'|constant}
      	    </th>
      	    
      	    <th id="title_sort">
                <a href="#" onclick="toggleSortOrder('title_sort', 'ASC')">
      		    {'_BIZ_TITLE'|constant}
                </a>
      	    </th>
      	    
      	    
			<th id="channelId">
			    <a href="#" onclick="toggleSortOrder('channelId', 'ASC')">
  		    	{'_BIZ_CHANNEL'|constant}
           		</a>
			</td>
			
      	    
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
      	    
      	    
      	    <th id="createdBy">
                <a href="#" onclick="toggleSortOrder('createdBy', 'ASC')">
      		    {'_BIZ_AUTHOR'|constant}
                </a>
      	    </th>
      	    
      	    
      	</tr>
      	{foreach from=$searchContext->items key=rank item=object}
    	    <tr align="center" grayed="{$rank is even ? 'grayed' : ''}" id="delete_tr_{$object->id}">
                    {assign var="objectClass" value=$object->getClass()}
                    {wcm name="render_object" object=$object
                        template="search/views/list/$objectClass.tpl"
                        default_template="search/views/list/default.tpl"}
      	    </tr>
      	{/foreach}
    </table>