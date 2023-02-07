function changeParentStage(stageID)
{
    $.get(createLink('programplan', 'ajaxGetAttribute', 'stageID=' + stageID), function(attribute)
    {
        $('#attributeType td').html(attribute);
        $("#attribute" + "_chosen").remove();
        $("#attribute").next('.picker').remove();
        $("#attribute").chosen();
    })
}

$(function()
{
    $("#parent").change(function()
    {
        var parent = $(this).children("option:selected").val();

        if(parent == 0)
        {
            $("#acl").attr('disabled', false);
        }
        else
        {
            $("#acl").attr('disabled', true);
        }

        changeParentStage(parent);
    });

    $('#submit').click(function(event)
    {
        if(plan.parent != $('#parent').val() && $('#parent').val() != 0)
        {
            var result = true;

            $.ajaxSettings.async = false;
            $.get(createLink('programplan', 'ajaxGetStageAttr', 'stageID=' + $('#parent').val()), function(attribute)
            {
                if(attribute != 'mix' && plan.attribute != attribute)
                {
                    result = confirm(changeAttrLang.replace('%s', stageTypeList[attribute]));
                }
            })
            $.ajaxSettings.async = true;

            if(!result) return false;
        }
    })
})
