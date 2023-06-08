function showTask()
{
    var show = $('#showTask').is(':checked') ? 1 : 0;
    $.cookie.set('showTask', show, {expires:config.cookieLife, path:config.webRoot});

    const link = $.createLink('project', 'execution', 'status=' + status + '&projectID=' + projectID + '&orderBy=' + orderBy + '&productID=' + productID);
    loadPage(link);
}

$(document).on('click', '.batch-btn', function()
{
    const dtable = zui.DTable.query($(this).target);
    const checkedList = dtable.$.getChecks();
    if(!checkedList.length) return;

    const url  = $(this).data('url');
    const form = new FormData();
    checkedList.forEach((id) => form.append('executionIDList[]', id.replace("pid", '')));

    if($(this).hasClass('ajax-btn'))
    {
        $.ajaxSubmit({url, data:form});
    }
    else
    {
        postAndLoadPage(url, form);
    }
});
