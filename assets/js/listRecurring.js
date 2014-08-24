$(function() {
    $('#noticePanel').puipanel(); 
    $('#enableButton').puibutton(); 
    if(events_on)
    {
        $('#enableButton').puibutton('enable'); 
    }
    else
    {
        $('#enableButton').puibutton('disable'); 
    }
});

