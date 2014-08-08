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
    $('#dataTable').puidatatable({
        caption: dt_caption,
        paginator: {
            rows: 25
        },
        columns: dt_columns,
        datasource: function(callback) {
            $.ajax({
                type: "GET",
                url: "listJson",
                dataType: "json",
                context: this,
                success: function(response) {
                    callback.call(this, response);
                }
            });
        },
        selectionMode: 'single',
        rowSelect: function(event, data) {
            selectedItem = data.id;
            enableEditButtons();
        },
        rowUnselect: function(event, data) {
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

function boolToString(input)
{
    return input.baseline === '1' ? 'Yes' : 'No';
}

function getImage(input)
{
    return '<img src="assets/images/' + input.image + '" class="status_image_table" />';
}

function deleteAction()
{
    $('#delete-dialog-confirm').puidialog('hide');
    $('input[name="id"]').val(selectedItem.toString());
    $('#deleteForm').submit();
  /*  $.post('delete', $('#deleteForm').serialize())
            .done(function()
            {
                location.reload();
            });*/
}


