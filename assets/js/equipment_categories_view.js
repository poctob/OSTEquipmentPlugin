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
            window.location.href = eq_root+"dashboard/";
        }
    });

    applyDataTableUI();

    $("#saveForm").validate();
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
            {field: 'equipment', headerText: 'Equipment', sortable: true},
            {field: 'create_date', headerText: 'Created On', sortable: true},
            {field: 'subject', headerText: 'Subject', sortable: true},
            {field: 'name', headerText: 'Created By', sortable: true},
            {field: 'priority', headerText: 'Prioirty', sortable: true}
        ],
        datasource: function(callback) {
            $.ajax({
                type: "GET",
                url: eq_root+'/categories/openTicketsJson/' + selectedCategory,
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
                url: eq_root+'/categories/closedTicketsJson/' + selectedCategory,
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






