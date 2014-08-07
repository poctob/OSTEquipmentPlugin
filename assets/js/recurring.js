var selectedItem = 0;
$(function() {

    applyDataTableUI();

    $('#recurringAdd').puibutton({
        icon: 'ui-icon-circle-plus',
        click: function() {
            window.location.href = 'view/0';
        }
    });

    $('#recurringEdit').puibutton({
        icon: 'ui-icon-pencil',
        click: function() {
            window.location.href = 'view/' + selectedItem;
        }
    });

    $('#recurringDelete').puibutton({
        icon: 'ui-icon-trash',
        click: function() {
            $("#delete-dialog-confirm").puidialog('show');
        }
    });
    
    

    disableRecurringEditButtons();
});

function applyDataTableUI()
{
    $('#recurringDataTable').puidatatable({
        caption: "Recurring Tickets",
        paginator: {
            rows: 25
        },
        columns: [
            {field: 'equipment_id', headerText: 'Equipment', sortable: true},
            {field: 'ticket_id', headerText: 'Ticket', sortable: true},
            {field: 'last_opened', headerText: 'Last Opened', sortable: true},
            {field: 'next_date', headerText: 'Next Date', sortable: true},
            {field: 'interval', headerText: 'Interval', sortable: true},
            {field: 'active', headerText: 'Is Active', sortable: true}
        ],
        datasource: function(callback) {
            $.ajax({
                type: "GET",
                url: 'listJson',
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
            enableRecurringEditButtons();
        },
        rowUnselect: function(event, data) {
            selectedItem = 0;
            disableRecurringEditButtons();
        }
    });
}

function enableRecurringEditButtons()
{
    $('#recurringEdit').puibutton('enable');
    $('#recurringDelete').puibutton('enable');

}

function disableRecurringEditButtons()
{
    $('#recurringEdit').puibutton('disable');
    $('#recurringDelete').puibutton('disable');

}

function deleteAction()
{
    $('#delete-dialog-confirm').puidialog('hide');
    $('input[name="id"]').val(selectedItem.toString());
    $.post('delete', $('#deleteForm').serialize())
            .done(function()
            {
               location.reload();              
            });
          
}

