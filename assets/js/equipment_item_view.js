$(function() {
    $('#categoriesDropDown').puidropdown();
    $('#statusDropDown').puidropdown();
    $('#staffDropDown').puidropdown();
    $.getJSON(eq_root + 'status/listJson', populateStatusDropDown);
    $.getJSON(eq_root + 'categories/listJson', populateCategoriesDropDown);
    $.getJSON(eq_root + 'item/listStaffJson', populateStaffDropDown);
});

function populateStaffDropDown(data)
{

    for (var key in data)
    {
        var staff = data[key];
        $('#staffDropDown').puidropdown
                ('addOption', staff['name'], staff['staff_id']);

        if (typeof staff_id !== 'undefined')
        {
            $('#staffDropDown').puidropdown('selectValue', staff_id);
        }
    }
}

function populateStatusDropDown(data)
{

    for (var key in data)
    {
        var status = data[key];
        $('#statusDropDown').puidropdown
                ('addOption', status['name'], status['status_id']);

        if (typeof status_id !== 'undefined')
        {
            $('#statusDropDown').puidropdown('selectValue', status_id);
        }
    }
}

function populateCategoriesDropDown(data)
{

    for (var key in data)
    {
        var category = data[key];
        $('#categoriesDropDown').puidropdown
                ('addOption', category['name'], category['category_id']);

        if (typeof category_id !== 'undefined')
        {
            $('#categoriesDropDown').puidropdown('selectValue', category_id);
        }
    }
}

function populateDynamicForm(data)
{
    $("#dynamic-form-body").replaceWith("data");
}


