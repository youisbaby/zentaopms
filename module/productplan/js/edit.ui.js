function toggleDateVisibility()
{
    if($('#future_').prop('checked'))
    {
        $('#begin').attr('disabled', 'disabled');
        $('#end').attr('disabled', 'disabled').closest('.form-row').hide();
    }
    else
    {
        $('#begin').removeAttr('disabled');
        $('#end').removeAttr('disabled').closest('.form-row').show();
    }
}

/**
 * Convert a date string like 2011-11-11 to date object in js.
 *
 * @param  string $date
 * @access public
 * @return date
 */
function convertStringToDate(dateString)
{
    dateString = dateString.split('-');
    dateString = dateString[1] + '/' + dateString[2] + '/' + dateString[0];

    return Date.parse(dateString);
}

/**
 * Compute the end date for productplan.
 *
 * @param  int    $delta
 * @access public
 * @return void
 */
function computeEndDate(delta)
{
    var beginDate = $('#begin').val();
    if(!beginDate) return;

    var delta = parseInt(delta);
    beginDate = convertStringToDate(beginDate);
    if((delta == 7 || delta == 14) && (beginDate.getDay() == 1))
    {
        delta = (weekend == 2) ? (delta - 2) : (delta - 1);
    }

    var currentBeginDate = window.zui.formatDate(beginDate, 'yyyy-MM-dd');
    var endDate = window.zui.formatDate(beginDate.addDays(delta - 1), 'yyyy-MM-dd');

    $('#begin').val(currentBeginDate);
    $('#end').val(endDate).datetimepicker('update');
}
