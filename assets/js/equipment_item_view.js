var selectedItem = 0;
var currentItem = 0;


$(function() {

    $.getJSON('../../status/listJson', populateStatusDropDown);

    $(':radio').puiradiobutton();
    $('#item_name').puiinputtext();
    $('#serial_number').puiinputtext();
    $('#item_description').puiinputtextarea();
    $('#item_notes').puiinputtextarea();

    $("#saveForm").validate();
});

function applyItemButtonUI()
{
    $('#itemAdd').puibutton({
        icon: 'ui-icon-circle-plus',
        click: function(event) {
            window.location.href = "../../item/new/" + selectedItem;
        }
    });

    $('#itemEdit').puibutton({
        icon: 'ui-icon-pencil',
        click: function(event) {
            window.location.href = "../../item/view/" + currentItem;
        }
    });

    $('#itemPublish').puibutton({
        icon: 'ui-icon-document',
        click: function(event) {
            publishItem(true);
        }
    });

    $('#itemUnpublish').puibutton({
        icon: 'ui-icon-document',
        click: function(event) {
            publishItem(false);
        }
    });

    $('#itemActivate').puibutton({
        icon: 'ui-icon-lightbulb',
        click: function(event) {
            activateItem(true);
        }
    });


    $('#itemDeactivate').puibutton({
        icon: 'ui-icon-lightbulb',
        click: function(event) {
            activateItem(false);
        }
    });

    $('#itemDelete').puibutton({
        icon: 'ui-icon-circle-close',
        click: function(event) {
            window.location.href = "../item/delete/" + currentItem;
        }
    });


}

function disableItemButtons()
{
    $('#itemEdit').puibutton('disable');
    $('#itemDelete').puibutton('disable');
    $('#itemPublish').hide();
    $('#itemActivate').hide();
    $('#itemUnpublish').hide();
    $('#itemDeactivate').hide();
}

function enableItemButtons(published, active)
{
    $('#itemEdit').puibutton('enable');
    $('#itemDelete').puibutton('enable');

    if (published)
    {
        $('#itemUnpublish').show()
    }
    else
    {
        $('#itemPublish').show();
    }

    if (active)
    {
        $('#itemDeactivate').show()
    }
    else
    {
        $('#itemActivate').show();
    }


}
function applyDataTableUI()
{
    $('#itemsDataTable').puidatatable({
        caption: "Category Items",
        paginator: {
            rows: 20
        },
        columns: [
            {field: 'name', headerText: 'Name', sortable: true},
            {field: 'status', headerText: 'Status', sortable: true},
            {field: 'published', headerText: 'Is Published?', sortable: true},
            {field: 'active', headerText: 'Is Active?', sortable: true}
        ],
        datasource: function(callback) {
            $.ajax({
                type: "GET",
                url: '../categoryItemsJson/' + selectedItem,
                dataType: "json",
                context: this,
                success: function(response) {
                    callback.call(this, response);
                }
            });
        },
        selectionMode: 'single',
        rowSelect: function(event, data) {
            currentItem = data.id;
            enableItemButtons(data.published === 'Yes', data.active === 'Yes');
        },
        rowUnselect: function(event, data) {
            selectedItem = 0;
            disableItemButtons();
        }
    });

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

function activateItem(activate)
{
    $('input[name="item_id"]').val(currentItem.toString());
    $('input[name="item_activate"]').val(activate ? '1' : '0');
    $.post('../../item/activate', $('#activateForm').serialize())
            .done(function()
            {
                $('#itemsDataTable').puidatatable('unselectAllRows');
                location.reload();
                $('#messages').puigrowl('show',
                        [{severity: 'info', summary: 'Success', detail: 'Item updated!'}]);
            })
            .error(function()
            {
                $('#messages').puigrowl('show',
                        [{severity: 'error', summary: 'Error', detail: 'Failed to update item!'}]);
            });
}

function publishItem(publish)
{
    $('input[name="item_id"]').val(currentItem.toString());
    $('input[name="item_publish"]').val(publish ? '1' : '0');
    $.post('../../item/publish', $('#publishForm').serialize())
            .done(function()
            {
                $('#itemsDataTable').puidatatable('unselectAllRows');
                location.reload();
                $('#messages').puigrowl('show',
                        [{severity: 'info', summary: 'Success', detail: 'Item updated!'}]);
            })
            .error(function()
            {
                $('#messages').puigrowl('show',
                        [{severity: 'error', summary: 'Error', detail: 'Failed to update item!'}]);
            });
}


function populateStatusDropDown(data)
{
    $('#statusDropDown').puidropdown();
    for (var key in data)
    {
        var status = data[key];
        $('#statusDropDown').puidropdown
                ('addOption', status, key);

        if (typeof status_id !== 'undefined')
        {
            $('#statusDropDown').puidropdown('selectValue', status_id);
        }
    }

    $('#statusDropDown').puidropdown({
        change: function(event) {
           // var url = $('#calendarsDropDown').puidropdown('getSelectedValue');
           // window.location.href = url;
        }
    });

}


