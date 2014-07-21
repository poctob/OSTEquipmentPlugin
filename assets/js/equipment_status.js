var selectedItem = 0;
$(function() {

    applyDataTableUI();

    $('#statusAdd').puibutton({
        icon: 'ui-icon-circle-plus',
        click: function() {
            window.location.href = '../status/view/0';
        }
    });

    $('#statusEdit').puibutton({
        icon: 'ui-icon-pencil',
        click: function() {
            window.location.href = '../status/view/' + selectedItem;
        }
    });

    $('#statusDelete').puibutton({
        icon: 'ui-icon-trash',
        click: function() {
            $("#delete-dialog-confirm").puidialog('show');
        }
    });

    disableStatusEditButtons();
});

function applyDataTableUI()
{
    $('#statusDataTable').puidatatable({
        caption: "Equipment Status",
        paginator: {
            rows: 25
        },
        columns: [
            {field: 'name', headerText: 'Name', sortable: true},
            {field: 'color', headerText: 'Color', sortable: true},
            {field: 'image', headerText: 'Image', sortable: true, content: getImage},
            {field: 'equipments', headerText: 'Equipment', sortable: true},
            {field: 'baseline', headerText: 'Is Default?', sortable: true, content: boolToString}
        ],
        datasource: function(callback) {
            $.ajax({
                type: "GET",
                url: '../status/listJson',
                dataType: "json",
                context: this,
                success: function(response) {
                    callback.call(this, response);
                }
            });
        },
        selectionMode: 'single',
        rowSelect: function(event, data) {
            selectedItem = data.status_id;
            enableStatusEditButtons();
        },
        rowUnselect: function(event, data) {
            selectedItem = 0;
            disableStatusEditButtons();
        }
    });
}

function enableStatusEditButtons()
{
    $('#statusEdit').puibutton('enable');
    $('#statusDelete').puibutton('enable');

}

function disableStatusEditButtons()
{
    $('#statusEdit').puibutton('disable');
    $('#statusDelete').puibutton('disable');

}

function deleteAction()
{
    $('#delete-dialog-confirm').puidialog('hide');
    $('input[name="id"]').val(selectedItem.toString());
    $.post('status/delete', $('#deleteForm').serialize())
            .done(function()
            {
                location.reload();
                $('#messages').puigrowl('show',
                        [{severity: 'info', summary: 'Success', detail: 'Item deleted!'}]);
            })
            .error(function()
            {
                $('#messages').puigrowl('show',
                        [{severity: 'error', summary: 'Error', detail: 'Failed to delete item!'}]);
            });
}

function boolToString(input)
{
    return input.baseline === '1' ? 'Yes' : 'No';
}

function getImage(input)
{
    return '<img src="assets/images/'+input.image+'" class="status_image_table" />';
}

