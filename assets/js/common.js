$(function() {
    $('#messages').puigrowl();
    $('#menuBarList').puimenubar();
    $("#delete-dialog-confirm").puidialog({
        buttons: [{
                text: 'Yes',
                icon: 'ui-icon-check',
                click: function() {
                    deleteAction();
                }
            },
            {
                text: 'No',
                icon: 'ui-icon-close',
                click: function() {
                    $('#delete-dialog-confirm').puidialog('hide');
                }
            }
        ]
    }
    );
});

function resetForm($form) {
    $form.find('input:text, input:password, input:file, select, textarea').val('');
    $form.find('input:radio, input:checkbox')
            .removeAttr('checked').removeAttr('selected');
}




