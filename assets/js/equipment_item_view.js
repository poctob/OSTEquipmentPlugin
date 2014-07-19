var selectedItem = 0;
var currentItem = 0;


$(function() {

    $.getJSON(eq_root+'status/listJson', populateStatusDropDown);

    $(':radio').puiradiobutton();
    $('#asset_id').puiinputtext();
    $('#item_name').puiinputtext();    
    $('#item_description').puiinputtextarea();
    $('#item_notes').puiinputtextarea();

    $("#saveForm").validate();
    
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
            window.location.href = eq_root+"categories/view/"+category_id;
        }
    });
    
    applyDataTableUI();
});




function applyDataTableUI()
{   
    $('#openTicketsDataTable').puidatatable({
        caption: "Open Tickets",
        paginator: {
            rows: 20
        },
        columns: [
            {field: 'number', headerText: 'Number', sortable: true},
            {field: 'create_date', headerText: 'Created On', sortable: true},
            {field: 'subject', headerText: 'Subject', sortable: true},
            {field: 'name', headerText: 'Created By', sortable: true},
            {field: 'priority', headerText: 'Prioirty', sortable: true}
        ],
        datasource: function(callback) {
            $.ajax({
                type: "GET",
                url: '../openTicketsJson/' + selectedItem,
                dataType: "json",
                context: this,
                success: function(response) {
                    callback.call(this, response);
                }
            });
        },
        selectionMode: 'single',
        rowSelect: function(event, data) {
            window.location.href = ostroot + "scp/tickets.php?id=" + data.id;
        }
    });

    $('#closedTicketsDataTable').puidatatable({
        caption: "Closed Tickets",
        paginator: {
            rows: 20
        },
        columns: [
            {field: 'number', headerText: 'Number', sortable: true},
            {field: 'create_date', headerText: 'Created On', sortable: true},
            {field: 'subject', headerText: 'Subject', sortable: true},
            {field: 'name', headerText: 'Created By', sortable: true},
            {field: 'priority', headerText: 'Prioirty', sortable: true},
            {field: 'close_date', headerText: 'Closed On', sortable: true},
            {field: 'closed_by', headerText: 'Closed By', sortable: true},
            {field: 'elapsed', headerText: 'Time Open', sortable: true}
        ],
        datasource: function(callback) {
            $.ajax({
                type: "GET",
                url: '../closedTicketsJson/' + selectedItem,
                dataType: "json",
                context: this,
                success: function(response) {
                    callback.call(this, response);
                }
            });
        },
        selectionMode: 'single',
        rowSelect: function(event, data) {
            window.location.href = ostroot + "scp/tickets.php?id=" + data.id;
        }
    });
}


function populateStatusDropDown(data)
{
    $('#statusDropDown').puidropdown();
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


