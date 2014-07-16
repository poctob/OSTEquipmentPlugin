var selectedItem = 0;
var currentItem = 0;


$(function() {


    $(':radio').puiradiobutton();
    $('#category_name').puiinputtext();
    $('#category_description').puiinputtextarea();
    $('#category_notes').puiinputtextarea();


    $('#itemEditPanel').puipanel({
        toggleable: true
    });

    $('#itemsPanel').puipanel({
        toggleable: true
    });

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
            window.location.href = "../list";
        }
    });

    applyDataTableUI();
    applyItemButtonUI();
    disableItemButtons();

    $("#saveForm").validate();
});

function applyItemButtonUI()
{
    $('#itemAdd').puibutton({
        icon: 'ui-icon-circle-plus',
        click: function(event) {
            window.location.href = "../item/view/0";
        }
    });

    $('#itemEdit').puibutton({
        icon: 'ui-icon-pencil',
        click: function(event) {
            window.location.href = "../item/view/" + currentItem;
        }
    });

    $('#itemPublish').puibutton({
        icon: 'ui-icon-document',
        click: function(event) {
            window.location.href = "../item/view/" + currentItem;
        }
    });

    $('#itemUnpublish').puibutton({
        icon: 'ui-icon-document',
        click: function(event) {
            window.location.href = "../item/view/" + currentItem;
        }
    });

    $('#itemActivate').puibutton({
        icon: 'ui-icon-lightbulb',
        click: function(event) {
            window.location.href = "../item/view/" + currentItem;
        }
    });


    $('#itemDeactivate').puibutton({
        icon: 'ui-icon-lightbulb',
        click: function(event) {
            window.location.href = "../item/view/" + currentItem;
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




