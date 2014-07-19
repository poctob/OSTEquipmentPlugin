var selectedItem = 0;
var currentItem = 0;

$(function() {
    $(':checkbox').puicheckbox();
    $('#status_name').puiinputtext();
    $('#status_image').puiinputtext();
    $('#status_color').puiinputtext();
    $('#status_description').puiinputtextarea();


    $('#itemEditPanel').puipanel({
        toggleable: true
    });

    $('#itemsPanel').puipanel({
        toggleable: true
    });

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
            window.location.href = eq_root+"dashboard/";
        }
    });

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






