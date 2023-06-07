window.renderCell = function(result, {col, row})
{
    if(col.name == 'name')
    {
        if(row.data.marker == 1)
        {
            result[result.length] = {html: "<icon class='icon icon-flag red' title='" + markerTitle + "'></icon>"};
            return result;
        }
    }

    if(col.name == 'build')
    {
        result[0] = '';
        for(key in row.data.builds)
        {
            let moduleName  = row.data.builds[key].execution > 0 ? 'build' : 'projectbuild';
            let branchLabel = showBranch ? "<span class='label label-outline label-badge'>" + row.data.builds[key].branchName + '</span> ' : '';
            result[result.length] = {html: branchLabel + "<a href='" + $.createLink(moduleName, 'view', 'buildID=' + row.data.builds[key].id) + "' title='" + row.data.builds[key].name + "'>" + row.data.builds[key].name + '</a>'}
        }
        return result;
    }

    if(col.name == 'project')
    {
        result[0] = '';
        if(row.data.builds.length == 0) return result;
        for(key in row.data.builds)
        {
            result[result.length] = {html: row.data.builds[key].projectName};
        }
        return result;
    }

    return result;
}

window.confirmDelete = function(releaseID)
{
    if(window.confirm(confirmDelete))
    {
        $.ajaxSubmit({url: $.createLink('release', 'delete', 'releaseID=' + releaseID)});
    }
}

window.createSortLink = function(col)
{
    var sort = col.name + '_asc';
    if(sort == orderBy) sort = col.name + '_desc';
    return sortLink.replace('{orderBy}', sort);
}

/**
 * 计算表格发布信息的统计。
 * Set release summary for table footer.
 *
 * @access public
 * @return object
 */
window.setStatistics = function()
{
    const rows    = this.layout.allRows;
    let total     = 0;
    let normal    = 0;
    let terminate = 0;
    let summary = '';
    if(type != 'all')
    {
        summary =  pageSummary.replace('%s', total);
    }
    else
    {
        summary = pageAllSummary.replace('%total%', total).replace('%normal%', normal).replace('%terminate%', terminate);
    }

    return {html: summary};
}
