$(function() {
    $('#categoriesDropDown').puidropdown();
    $.getJSON(eq_root + 'categories/listJson', populateCategoriesDropDown);
});


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


