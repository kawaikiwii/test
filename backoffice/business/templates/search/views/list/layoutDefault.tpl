<table cols="6">
          	<tr>
          	    <th class="select-all">
                    <a href="" onclick="toggleCheckboxes('item_'); return false">
                        {'_BIZ_TOGGLE'|constant}
                    </a>
                </th>
<!-- 
         	    <th id="className">
                    <a href="#" onclick="toggleSortOrder('className', 'ASC')">
          		    {'_BIZ_TYPE'|constant}
                    </a>
          	    </th>
-->
{php}
$this->assign('bool_forecast', strpos($this->get_template_vars('searchContextQuery'), 'classname:forecast'));
$this->assign('bool_prevision', strpos($this->get_template_vars('searchContextQuery'), 'classname:prevision'));
{/php}
{if $bool_forecast|$bool_prevision}
				<th id="startDate" nowrap>
                    <a href="#" onclick="toggleSortOrder('forecast_startDate', 'ASC')">
          		    	start date
                    </a>&nbsp;
          	    </th>
          	    <th id="endDate" nowrap>
                    <a href="#" onclick="toggleSortOrder('forecast_endDate', 'ASC')">
          		    	end date
                    </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          	    </th>
{else}
				<th id="photos" nowrap>
                    <a href="#" onclick="toggleSortOrder('photocount', 'ASC')">
          		    	Photos
                    </a>&nbsp;
          	    </th>
          	    <th id="source" nowrap>
                    <a href="#" onclick="toggleSortOrder('photocount', 'ASC')">
          		    	Source
                    </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          	    </th>         	    
{/if}
          	    <th id="title_sort">
                    <a href="#" onclick="toggleSortOrder('title_sort', 'ASC')">
          		    {'_BIZ_TITLE'|constant}
                    </a>
          	    </th>
          	    
          	    
          	    
      	    	{php}
					$this->assign('bool_event', strpos($this->get_template_vars('searchContextQuery'), 'classname:event'));
				{/php}


				{if $bool_event}
					<th id="location">
{*					    <a href="#" onclick="#">*}
          		    	{'_LOCATION'|constant}
{*                   		</a>*}
					</th>
					<th id="startDate">
					    <a href="#" onclick="toggleSortOrder('event_startdate', 'DESC')">
          		    	{'_BIZ_SCHEDULES_FIRST_SHOWDATE'|constant}
                   		</a>
					</th>
				{/if}

					<th id="channelId">
					    <a href="#" onclick="toggleSortOrder('channelId', 'ASC')">
          		    	{'_BIZ_CHANNEL'|constant}
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