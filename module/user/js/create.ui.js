$(function()
{
    password1Encrypted = false;
    password2Encrypted = false;
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

    $('#companyBox').toggleClass('hidden', type == 'inside');
    $('[name="dept"]').closest('.form-row').toggleClass('hidden', type != 'inside');
    $('[name="join"]').closest('.form-row').toggleClass('hidden', type != 'inside');
    $('#commiter').closest('.form-row').toggleClass('hidden', type != 'inside');
}

function changeAddCompany(event)
{
    const checked = $(event.target).prop('checked');
    if(checked)
    {
        const $inputGroup = $('[name="company"]').closest('.picker-box');
        if($inputGroup.length == 0) return;
        $('[name="company"]').zui('picker').destroy();
        $inputGroup.replaceWith("<input name='company' id='company' class='form-control'/>");
    }
    else
    {
        const link = $.createLink('company', 'ajaxGetOutsideCompany');
        $.post(link, function(data)
        {
            var $companyPicker = $('#company').replaceWith('<div id="companyPicker" class="form-group-wrapper picker-box"></div>');
            if(data)
            {
                data = JSON.parse(data);
                new zui.Picker('#companyPicker', {name: 'company', items: data});
            }
        })
    }
}

function clickSubmit()
{
    if(!password1Encrypted || !password2Encrypted)
    {
        const password1 = $('#password1').val();
        const password2 = $('#password2').val();
        const verifyPassword = $('#verifyPassword').val();
        if(!password1Encrypted)
        {
            passwordStrength = computePasswordStrength(password1);
            $("#passwordLength").val(password1.length);
        }

        if($("form input[name=passwordStrength]").length == 0) $('#passwordLength').after("<input type='hidden' name='passwordStrength' value='0' />");
        $("form input[name=passwordStrength]").val(passwordStrength);

        const rand = $('input#verifyRand').val();
        if(password1 && !password1Encrypted) $('#password1').val(md5(password1) + rand);
        if(password2 && !password2Encrypted) $('#password2').val(md5(password2) + rand);

        password1Encrypted = true;
        password2Encrypted = true;
    }
}

/**
 * Change group when change role.
 *
 * @param  role $role
 * @access public
 * @return void
 */
function changeRole(event)
{
    var role = $(event.target).val();
    if(role && roleGroup[role])
    {
        $('[name^="group"]').zui('picker').$.setValue(roleGroup[role]);
    }
    else
    {
        $('[name^="group"]').zui('picker').$.setValue('');
    }
}
