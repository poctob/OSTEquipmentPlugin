var selectedItem = 0;
$(function() {

    applyDataTableUI();
    
      $('#categoryAdd').puibutton({
        icon: 'ui-icon-circle-plus',
        click: function() {
            window.location.href = 'view/0';        
        }
    });

    $('#categoryEdit').puibutton({
        icon: 'ui-icon-pencil',
       click: function() {
            window.location.href = 'view/' + selectedItem;
        }
    });

    $('#categoryDelete').puibutton({
        icon: 'ui-icon-trash',
        click: function() {
             $( "#delete-dialog-confirm" ).puidialog('show');           
        }
    });
    
    disableCategoryEditButtons();
});

function applyDataTableUI()
{
    $('#categoriesDataTable').puidatatable({
        caption: "Equipment Categories",
        paginator: {
            rows: 5
        },
        columns: [
            {field: 'name', headerText: 'Name', sortable: true},
            {field: 'ispublic', headerText: 'Type', sortable: true},
            {field: 'equipment_count', headerText: 'Equipment', sortable: true},
            {field: 'open_ticket_count', headerText: 'Open Tickets', sortable: true},
             {field: 'closed_ticket_count', headerText: 'Closed Tickets', sortable: true},
            {field: 'updated', headerText: 'Last Updated', sortable: true}
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
            enableCategoryEditButtons();
            $('#messages').puigrowl('show', [{severity: 'info', summary: 'Row Selected', detail: (data.id)}]);
        },
        rowUnselect: function(event, data) {
            selectedItem = 0;
            disableCategoryEditButtons();
            $('#messages').puigrowl('show', [{severity: 'info', summary: 'Row Unselected', detail: (data.id)}]);
        }
    });
}

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


