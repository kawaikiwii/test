{wcm name="include_template" file="search/views/resultset-info.tpl"}
    <div id="resultset">
        <table cols="6">
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
          	{php}
			$items = $this->get_template_vars('searchContext')->items;
			$xmlDoc = simplexml_load_string($items);
			$xml = "";
			if (isset($_SESSION['tempBin']))
			{
				foreach($_SESSION['tempBin'] as $key => $value)
					$xml .= ",".$key;
			}
			
			$xmlDoc->result[0]->addChild('tempBin',substr($xml,1));
    			$newDoc = dom_import_simplexml($xmlDoc);
    			
			$xslDoc = new DOMDocument();
			$xslDoc->load("business/templates/search/views/highlight/highlight.xsl");
			$xsltProc = new XSLTProcessor;
			$xsltProc->importStyleSheet($xslDoc);
            		$xsltProc->registerPHPFunctions();
            		
            		echo $xsltProc->transformToXML($newDoc);
		{/php}
        </table>
    </div>
{wcm name="include_template" file="search/views/resultset-navigation.tpl"}
