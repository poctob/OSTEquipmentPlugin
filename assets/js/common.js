var flash_severity = "";
var flash_summary = "";
var flash_details = "";

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
    showFlash();
});

function resetForm($form) {
    $form.find('input:text, input:password, input:file, select, textarea').val('');
    $form.find('input:radio, input:checkbox')
            .removeAttr('checked').removeAttr('selected');
}

function showFlash()
{
    if (flash_severity.length > 0 && flash_summary.length > 0
            && flash_details.length > 0)
    {
        msg = [{severity: flash_severity,
                summary: flash_summary,
                detail: flash_details}];
        $('#messages').puigrowl('show', msg);
    }
    flash_severity = "";
    flash_summary = "";
    flash_details = "";
}




