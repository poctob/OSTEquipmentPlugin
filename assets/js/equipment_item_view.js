$(function() {
    $('#categoriesDropDown').puidropdown();
    $('#statusDropDown').puidropdown();
    $.getJSON(eq_root + 'status/listJson', populateStatusDropDown);
    $.getJSON(eq_root + 'categories/listJson', populateCategoriesDropDown);
});




/*function applyDataTableUI()
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
 url: eq_root+'item/openTicketsJson/' + selectedItem,
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
 url: eq_root+'item/closedTicketsJson/' + selectedItem,
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
 */

function populateStatusDropDown(data)
{

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

function populateCategoriesDropDown(data)
{

    for (var key in data)
    {
        var category = data[key];
        $('#categoriesDropDown').puidropdown
                ('addOption', category['name'], category['category_id']);

        if (typeof category_id !== 'undefined')
        {
            $('#categoriesDropDown').puidropdown('selectValue', category_id);
        }
    }
}

function populateDynamicForm(data)
{
    $("#dynamic-form-body").replaceWith("data");
}


