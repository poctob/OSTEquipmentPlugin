var dt_caption;
var dt_columns = [];
var dt_data_url;
var selectedItem = 0;

$(function() {

    initDataTable();

    $('#itemAdd').puibutton({
        icon: 'ui-icon-circle-plus',
        click: function() {
            window.location.href = 'view/0';
        }
    });

    $('#itemEdit').puibutton({
        icon: 'ui-icon-pencil',
        click: function() {
            window.location.href = 'view/' + selectedItem;
        }
    });

    $('#itemDelete').puibutton({
        icon: 'ui-icon-trash',
        click: function() {
            $("#delete-dialog-confirm").puidialog('show');
        }
    });

    disableEditButtons();
});

function initDataTable()
{
    $('#dataTable').puitreetable({
        caption: dt_caption,      
        columns: dt_columns,
        nodes: function(ui, response) {
            $.ajax({
                type: "GET",
                url: "listJsonTree",
                dataType: "json",
                context: this,
                success: function(data) {
                    response.call(this, data);
                }
            });
        },
        selectionMode: 'single',
        nodeSelect: function(event, ui) {
            selectedItem = ui.data.id;
            enableEditButtons();
        },
        nodeUnselect: function(event, ui) {
            selectedItem = 0;
            disableEditButtons();
        }
    });
}

function enableEditButtons()
{
    $('#itemEdit').puibutton('enable');
    $('#itemDelete').puibutton('enable');

}

function disableEditButtons()
{
    $('#itemEdit').puibutton('disable');
    $('#itemDelete').puibutton('disable');

}

function deleteAction()
{
    $('#delete-dialog-confirm').puidialog('hide');
    $('input[name="id"]').val(selectedItem.toString());
    $('#deleteForm').submit();
}


