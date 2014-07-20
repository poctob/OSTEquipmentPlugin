$(function() {
    $('#dynamic-form-body').puipanel();
    $.ajax({
        url: eq_root+'item/getDynamicForm/'+selected_item,
        cache: false
    })
            .done(function(html) {
                $("#dynamic-form-body").append(html);
                $(':radio').puiradiobutton();
                $(':text').puiinputtext();
                $('textarea').puiinputtextarea();
                $('select').puidropdown();
                $(':checkbox').puicheckbox(); 
            });
});