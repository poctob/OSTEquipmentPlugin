$(function() {
    $('#categoriesDropDown').puidropdown();
    $('#statusDropDown').puidropdown();
    $.getJSON(eq_root + 'status/listJson', populateStatusDropDown);
    $.getJSON(eq_root + 'categories/listJson', populateCategoriesDropDown);
});


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


