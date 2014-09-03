var dt_caption;
var dt_columns = [];
var dt_data_url;
var selectedItem = 0;
var selectedItem2 = 0;

$(function() {

    $('#myEquipment').puipanel({
        toggleable: true,
        closable: false
    });

    $('#notMyEquipment').puipanel({
        toggleable: true
        , closable: false
    });

    initDataTable();

    $('#myItemAdd').puibutton({
        icon: 'ui-icon-circle-plus',
        click: function() {
            window.location.href = 'view/0';
        }
    });

    $('#myItemEdit').puibutton({
        icon: 'ui-icon-pencil',
        click: function() {
            window.location.href = 'view/' + selectedItem;
        }
    });

    $('#myItemDelete').puibutton({
        icon: 'ui-icon-trash',
        click: function() {
            $("#delete-dialog-confirm").puidialog('show');
        }
    });

    $('#notMyItemAdd').puibutton({
        icon: 'ui-icon-circle-plus',
        click: function() {
            window.location.href = 'view/0';
        }
    });

    $('#notMyItemEdit').puibutton({
        icon: 'ui-icon-pencil',
        click: function() {
            window.location.href = 'view/' + selectedItem2;
        }
    });

    $('#notMyItemDelete').puibutton({
        icon: 'ui-icon-trash',
        click: function() {
            $("#delete-dialog-confirm2").puidialog('show');
        }
    });

    $('#myItemNewTicket').puibutton({
        icon: 'ui-icon-tag',
          click: function() {
           newTicketAction();
        }
    });

    $('#notMyItemNewTicket').puibutton({
        icon: 'ui-icon-tag',
          click: function() {
           newTicketAction2();
        }
    });

    $("#delete-dialog-confirm2").puidialog({
        buttons: [{
                text: 'Yes',
                icon: 'ui-icon-check',
                click: function() {
                    deleteAction2();
                }
            },
            {
                text: 'No',
                icon: 'ui-icon-close',
                click: function() {
                    $('#delete-dialog-confirm2').puidialog('hide');
                }
            }
        ]
    }
    );

    disableEditButtons();
    disableEditButtons2();
});

function initDataTable()
{
    u_data = null;



    u_data = function(callback) {
        $.ajax({
            type: "GET",
            url: "listBelongingJson",
            dataType: "json",
            context: this,
            success: function(response) {
                callback.call(this, response);
            }
        });
    };



    $('#myDataTable').puidatatable({
        caption: dt_caption,
        paginator: {
            rows: 25
        },
        columns: dt_columns,
        datasource: u_data,
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

    u_data = function(callback) {
        $.ajax({
            type: "GET",
            url: "listNotBelongingJson",
            dataType: "json",
            context: this,
            success: function(response) {
                callback.call(this, response);
            }
        });
    };

    $('#notMyDataTable').puidatatable({
        caption: dt_caption,
        paginator: {
            rows: 25
        },
        columns: dt_columns,
        datasource: u_data,
        selectionMode: 'single',
        rowSelect: function(event, data) {
            selectedItem2 = data.id;
            enableEditButtons2();
        },
        rowUnselect: function(event, data) {
            selectedItem2 = 0;
            disableEditButtons2();
        }
    });
}

function enableEditButtons()
{
    $('#myItemEdit').puibutton('enable');
    $('#myItemDelete').puibutton('enable');
    $('#myItemNewTicket').puibutton('enable');
}

function disableEditButtons()
{
    $('#myItemEdit').puibutton('disable');
    $('#myItemDelete').puibutton('disable');
    $('#myItemNewTicket').puibutton('disable');
}

function enableEditButtons2()
{
    $('#notMyItemEdit').puibutton('enable');
    $('#notMyItemDelete').puibutton('enable');
    $('#notMyItemNewTicket').puibutton('enable');
}

function disableEditButtons2()
{
    $('#notMyItemEdit').puibutton('disable');
    $('#notMyItemDelete').puibutton('disable');
    $('#notMyItemNewTicket').puibutton('disable');
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
}

function deleteAction2()
{
    $('#delete-dialog-confirm').puidialog('hide');
    $('input[name="id"]').val(selectedItem2.toString());
    $('#deleteForm').submit();
}


function newTicketAction()
{
    $('input[name="id"]').val(selectedItem.toString());
    $('#newTicketForm').submit();
}

function newTicketAction2()
{
    $('input[name="id"]').val(selectedItem2.toString());
    $('#newTicketForm').submit();
}

