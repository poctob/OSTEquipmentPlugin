var selectedItem = 0;
var currentItem = 0;

$(function() {


    $(':radio').puiradiobutton();
    $('#category_name').puiinputtext();
    $('#category_description').puiinputtextarea();
    $('#category_notes').puiinputtextarea();


    $('#openTicketsPanel').puipanel({
        toggleable: true
    });

    $('#closedTicketsPanel').puipanel({
        toggleable: true
    });

    $('#saveButton').puibutton({
        icon: 'ui-icon-disk'
    });

    $('#resetButton').puibutton({
        icon: 'ui-icon-arrowrefresh-1-w',
        click: function(event) {
            resetForm($('#saveForm'));
        }
    });

    $('#cancelButton').puibutton({
        icon: 'ui-icon-circle-close',
        click: function(event) {
            window.location.href = eq_root + "dashboard/";
        }
    });

    open_url = eq_root + '/categories/openTicketsJson/' + selectedCategory;
    closed_url = eq_root + '/categories/closedTicketsJson/' + selectedCategory;
    applyTicketsTableUI(open_url, closed_url);

    $("#saveForm").validate();
});


function enableCategoryEditButtons()
{
    $('#categoryEdit').puibutton('enable');
    $('#categoryDelete').puibutton('enable');

}

function disableCategoryEditButtons()
{
    $('#categoryEdit').puibutton('disable');
    $('#categoryDelete').puibutton('disable');
}






