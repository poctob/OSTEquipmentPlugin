var currentTicket = 0;
$(function() {
    $('#recurringTicket').puipanel();
    $('#intervalAdd').puibutton({
        icon: 'ui-icon-circle-plus',
        click: function() {
            window.location.href = eq_root + 'recurring/addByTicket/'  + currentTicket;
        }
    });
});

