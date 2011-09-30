<script language="javascript" type="text/javascript">
var searchFilters = null;

function onSelectItem(action, id)
{
    window.location = wcmBaseURL + 'index.php?_wcmAction=' + action + '&id=' + id;
}

function toggleCheckboxes(idPrefix)
{

    var newIds = new Array;
    var oldIds = new Array;
    eval('var rExp = /' + idPrefix + '/');

    $A(document.getElementsByTagName('input')).select(
                
        function (element) {
            return element.type == 'checkbox' && element.id.startsWith(idPrefix);
        }).each(
            function (checkbox) {
                
                if (checkbox.checked == true)
                {
                    
                    oldIds.push(checkbox.id.replace(rExp,''));
                    checkbox.checked = false;
                    
                    $$('#compteur h4 span').each(function(someSpan)
                    {
                        
                        total = parseInt(someSpan.innerHTML);
                        total--;
                        someSpan.update(total);
                    });
                                        
                } else {
                
                    checkbox.checked = true;
                    newIds.push(checkbox.id.replace(rExp,''));
                    
                    $$('#compteur h4 span').each(function(someSpan)
                    {
                        
                        total = parseInt(someSpan.innerHTML);
                        total++;
                        someSpan.update(total);
                    });
                        
                    // manageBin('addToSessionBin', '', '', checkbox.id, '', 'compteur', '')
                }
            });
     
     if (newIds.size() > 0) wcmBizAjaxController.callWithoutUpdate('biz.manageBin',{object: newIds.toJSON(), command: 'massAddToSessionBin'},null);
     if (oldIds.size() > 0) wcmBizAjaxController.callWithoutUpdate('biz.manageBin',{object: oldIds.toJSON(), command: 'massRemoveFromSessionBin'},null);
            
}

function toggleSortOrder(sortedBy, defaultSortOrder)
{
    var sortOrder = $('sortOrder').value;
    var sortedById = $('paramPrefix').value + 'sortedBy';
    var toggling = (sortedBy + ' ' + sortOrder == $(sortedById).value);

    if (toggling) {
        sortOrder = (sortOrder == 'ASC' ? 'DESC' : sortOrder == 'DESC' ? 'ASC' : defaultSortOrder);
    }
    else {
        sortOrder = defaultSortOrder;
    }

    $('sortOrder').value = sortOrder;
    $(sortedById).value = sortedBy + ' ' + sortOrder;

    launchSearch();
}

function launchSearch()
{
    clearSearchFilters();
    search('initSearch');
}

function fetchSearchResultItems(pageNum, view)
{
    var oldPageNum = $('pageNum').value;
    if (!pageNum)
        pageNum = oldPageNum;

    var oldView = $('view').value;
    if (!view)
        view = oldView;

    if (pageNum != oldPageNum || view != oldView) {
        $('pageNum').value = pageNum;
        $('view').value = view;
        search('fetchItems');
    }
}

function refineSearch(filterName, filterItemId, checked, facetName, facetValue)
{
    var query = getQuery();
    var oldQueryValue = query.value;

    var queryValue = refineQueryValue(oldQueryValue, filterName,
                                      filterItemId, checked, facetName, facetValue);

    if (queryValue != oldQueryValue) {
        query.value = queryValue;
        search('initSearch');
    }
}

function searchUsingFilters()
{
    var queryValue = '';

    loadSearchFilters();
    for (var filterName in searchFilters) {
        var filter = searchFilters[filterName];
        var checkedItems = filter.checkedItems;

        for (var itemId in checkedItems) {
            var item = checkedItems[itemId];
            queryValue = refineQueryValue(queryValue, filterName, itemId,
                                          true, item.facetName, item.facetValue);
        }
    }

    var query = getQuery();
    var oldQueryValue = query.value;
    if (queryValue != oldQueryValue) {
        query.value = queryValue;
        launchSearch();
    }
}

function resetFiltersAndSearch()
{
    var query = getQuery();
    var queryValue = query.value;
    var oldQueryValue = queryValue;

    loadSearchFilters();
    for (var filterName in searchFilters) {
        var filter = searchFilters[filterName];
        if (filter) {
            var checkedItems = filter.checkedItems;

            for (var itemId in checkedItems) {
                var item = checkedItems[itemId];
                queryValue = refineQueryValue(queryValue, filterName, itemId,
                                              false, item.facetName, item.facetValue);
            }
        }
    }

    if (queryValue != oldQueryValue) {
        query.value = queryValue;
        launchSearch();
    }
}

// HELPERS

function clearSearchFilters()
{
    loadSearchFilters();
    for (var filterName in searchFilters) {
        searchFilters[filterName].checkedItems = {};
    }
}

function loadSearchFilters()
{
    if (!searchFilters)
        searchFilters = $('searchFilters').value.evalJSON();
}

function saveSearchFilters()
{
    if (searchFilters)
        $('searchFilters').value = Object.toJSON(searchFilters);
}

function getSearchForm()
{
    var name = $('name').value;
    return $(name + 'ResultPageForm');
}

function getQuery()
{
    var paramPrefix = $('paramPrefix').value;
    return $(paramPrefix + 'query');
}

function refineQueryValue(queryValue, filterName, filterItemId, checked, facetName, facetValue)
{
    if (facetValue.indexOf(' ') != -1) {
        var quotedRE = new RegExp('^"([^"]|\\")*"$');
        if (!facetValue.match(quotedRE))
            facetValue = '"' + facetValue + '"';
    }
    var facetTerm = facetName + ':' + facetValue;

    loadSearchFilters();

    var filter = searchFilters[filterName];
    var filterItem = $(filterItemId);
    filterItem.checked = checked;

    if (checked) {
        var filterItem = {
            facetName: facetName,
            facetValue: facetValue
        };
        filter.checkedItems[filterItemId] = filterItem;

        if (queryValue.indexOf(' ') != -1) {
            if (queryValue[0] != '(' || queryValue[queryValue.length - 1] != ')') {
                var subQueryRE = new RegExp('^\\s*\\S+(\\s+AND\\s+\\S+)+\\s*$');
                if (!queryValue.match(subQueryRE))
                    queryValue = '(' + queryValue + ')';
            }
        }

        if (queryValue != '')
            queryValue += ' AND ';

        queryValue += facetTerm;
    }
    else {
        delete filter.checkedItems[filterItemId];

        var str = ' AND ' + facetTerm;
        var pos = queryValue.indexOf(str);

        if (pos == -1) {
            str = facetTerm + ' AND ';
            pos = queryValue.indexOf(str);
        }
        if (pos == -1) {
            str = '(' + facetTerm + ')';
            pos = queryValue.indexOf(str);
        }
        if (pos == -1) {
            str = facetTerm;
            pos = queryValue.indexOf(str);
        }
        if (pos != -1)
            queryValue = queryValue.substr(0, pos) + queryValue.substr(pos + str.length);
    }

    return queryValue;
}

function search(todo)
{
    $('_wcmTodo').value = todo;

    saveSearchFilters();

    var form = getSearchForm();
    if ($('ajaxRequest').value == 'false')
        window.location = wcmBaseURL + 'index.php?' + form.serialize();
    else {
        var parameters = form.serialize(true);
        var options = {
            asynchronous: false,
            onComplete: function () {
                updateSearchFilters();
            }
        };
        wcmBizAjaxController.call("biz.bizsearch", parameters, null, options);
    }
}

function toggleSearchFilter(filterName)
{
    loadSearchFilters();

    var filter = searchFilters[filterName];
    if (filter) {
        var buttonId = filterName + '_button';
        var button = $(buttonId);

        var panelId = filterName + '_panel';
        var panel = $(panelId);

        if (panel.style.display != 'none') {
            panel.style.display = 'none';
            button.style.background = "url(skins/default/images/gui/bullet-arrow-open.png) no-repeat 0 50% #DFDFDF";
            filter.open = false;
        }
        else {
            panel.style.display = '';
            filter.open = true;
            updateSearchFilter(filterName);
        }
    }
}

function updateSearchFilter(filterName)
{
    loadSearchFilters();

    var filter = searchFilters[filterName];
    if (filter) {
        var parameters = {
            filterName: filterName,
            filter: Object.toJSON(filter),
            searchId: $('searchId').value,
            searchEngine: $('searchEngine').value
        };
        var options = {
            asynchronous: true
        };
        wcmBizAjaxController.call("biz.filter", parameters, null, options);
    }
}

function updateSearchFilters()
{
    loadSearchFilters();
    for (var filterName in searchFilters) {
        var filter = searchFilters[filterName];
        if (filter && filter.open) {
            updateSearchFilter(filterName);
        }
    }
}
</script>
