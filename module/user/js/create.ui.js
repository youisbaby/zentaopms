$(function()
{
    password1Encrypted = false;
    password2Encrypted = false;
    verifyEncrypted    = false;
    passwordStrength   = 0;
})

function changePassword(event)
{
    if($(event.target).attr('id') == 'password1')
    {
        password1Encrypted = false;
    }
    if($(event.target).attr('id') == 'password2')
    {
        password2Encrypted = false;
    }
    if($(event.target).attr('id') == 'verifyPassword')
    {
        verifyEncrypted = false;
    }
}

function changeType(event)
{
    const type = $(event.target).val();
    if(type == 'inside')
    {
        $('#companyBox').addClass('hidden');
        $('#dept, #join, #commiter').closest('.form-row').removeClass('hidden');
    }
    else
    {
        $('#companyBox').removeClass('hidden');
        $('#dept, #join, #commiter').closest('.form-row').addClass('hidden');
    }
}

function changeAddCompany(event)
{
    const checked = $(event.target).prop('checked');
    if(checked)
    {
        $('#company').replaceWith("<input name='company' id='company' class='form-control'/>");
    }
    else
    {
        const link = $.createLink('company', 'ajaxGetOutsideCompany');
        $.post(link, function(data)
        {
            $('#company').replaceWith(data);
        })
    }
}

function changeVision(event)
{
    var visions = [];
    $('input[name="visions[]"]:checked').each(function()
    {
        visions.push($(this).val());
    });

    const link  = $.createLink('user', 'ajaxGetGroup', 'visions=' + visions);
    $.get(link, function(data)
    {
        let group        = $('[name^="group"]').val();
        let $groupPicker = $('[name^="group"]').zui('picker');
        if(data)
        {
            data = JSON.parse(data);
            $groupPicker.render({items: data});
            $groupPicker.$.clear();
            $groupPicker.$.setValue(group);
        }
    });
}

function clickSubmit()
{
    if(!password1Encrypted || !password2Encrypted)
    {
        const password1 = $('#password1').val();
        const password2 = $('#password2').val();
        if(!password1Encrypted)
        {
            passwordStrength = computePasswordStrength(password1);
            $("#passwordLength").val(password1.length);
        }

        $('#passwordLength').after("<input type='hidden' name='passwordStrength' value='" + passwordStrength + "' />");

        const rand = $('input#verifyRand').val();
        if(password1 && !password1Encrypted) $('#password1').val(md5(password1) + rand);
        if(password2 && !password2Encrypted) $('#password2').val(md5(password2) + rand);
        password1Encrypted = true;
        password2Encrypted = true;
    }
}
