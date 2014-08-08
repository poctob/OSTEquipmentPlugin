var currentTicket = 0;

$(function() {
    $('#openTicketsPanel').puipanel();
    $('#closedTicketsPanel').puipanel();
    open_url = eq_root + 'item/openTicketsJson/' + selected_item;
    closed_url = eq_root + 'item/closedTicketsJson/' + selected_item;
    applyTicketsTableUI(open_url, closed_url);
});

function applyTicketsTableUI(open_url, closed_url)
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
                url: open_url,
                dataType: "json",
                context: this,
                success: function(response) {
                    callback.call(this, response);
                }
            });
        },
        selectionMode: 'single',
        rowSelect: function(event, data) {
            currentTicket = data.id;
            enableOpenTicketsEditButtons();
        }
    });

    $('#openTicketView').puibutton({
        icon: 'ui-icon-search',
        click: function(event)
        {
            window.location.href = ostroot + "scp/tickets.php?id=" + currentTicket;
        }
    });

    $('#openTicketInteval').puibutton({
        icon: 'ui-icon-clock',
        click: function(event)
        {
             window.location.href = eq_root + 'recurring/viewByTicket/'  + currentTicket;
        }
    });

    disableOpenTicketsEditButtons();

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
                url: closed_url,
                dataType: "json",
                context: this,
                success: function(response) {
                    callback.call(this, response);
                }
            });
        },
        selectionMode: 'single',
        rowSelect: function(event, data) {
            currentTicket = data.id;
            enableClosedTicketsEditButtons();
        }
    });

    $('#closedTicketView').puibutton({
        icon: 'ui-icon-search',
        click: function(event)
        {
            window.location.href = ostroot + "scp/tickets.php?id=" + currentTicket;           
        }
    });

    $('#closedTicketInteval').puibutton({
        icon: 'ui-icon-clock',
        click: function(event)
        {
             window.location.href = eq_root + 'recurring/viewByTicket/'  + currentTicket;
        }
    });
    disableClosedTicketsEditButtons();
    $('#closedTicketInteval').hide();
    $('#openTicketInteval').hide();
}

function enableClosedTicketsEditButtons()
{
    $('#closedTicketView').puibutton('enable');
   // $('#closedTicketInteval').puibutton('enable');

}

function disableClosedTicketsEditButtons()
{
    $('#closedTicketView').puibutton('disable');
   // $('#closedTicketInteval').puibutton('disable');
}

function enableOpenTicketsEditButtons()
{
    $('#openTicketView').puibutton('enable');
  //  $('#openTicketInteval').puibutton('enable');

}

function disableOpenTicketsEditButtons()
{
    $('#openTicketView').puibutton('disable');
   // $('#openTicketInteval').puibutton('disable');
}

