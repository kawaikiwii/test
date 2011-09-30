function executeSearch(name)
{
    // Display waiting message
    var header = $(name + "SearchResultHeader");
    if (header)
        header.innerHTM
            = "<span class='error'>"
            + "<img src='../img/wait.gif' width='15' height='15' alt='" + $I18N.LOADING + "...' />"
            + "</span>";

    // Initialize search parameters
    var parameters = {
    name: name
    };

    // Initialize Ajax options
    var options = {
    onComplete: function () {
            updateMetasAndStats($('searchId').value);
        }
    };

    // Invoke ajax method
    wcmBizAjaxController.submit("biz.bizsearch", name + 'SearchForm', parameters, null, options);

    $(name + 'SearchList').scrollTop = 0;
}