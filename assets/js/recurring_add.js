var selectedItem = 0;
$(function() {

    $('#ticketsDD').puidropdown();
    $('#equipmentDD').puidropdown();
    $('#intervalDD').puidropdown();
    $('#next_date').datepicker();
    $('#active').puicheckbox();
 
    
    $('#interval').puispinner({  
                min:1,  
                max:3650  
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
            window.location.href = eq_root + "dashboard/";
        }
    });

});



