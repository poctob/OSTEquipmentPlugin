$(function() {
    $('#tabview').puitabview(
            {
                orientation: 'left'
            });

    $('#startStructureTest').puibutton(
            {
                click: function(event)
                {
                    $.ajax({
                        url: 'startStructureTest'
                    });
                    updateProgressBar('#structureTestPB');
                }
            }
    );

    $('#structureTestPB').puiprogressbar();
});

function updateProgressBar(id)
{
    setInterval(function() {
        $.ajax({
            url: 'checkProgress',
            dataType: 'text'
        })
                .done(function(val)
                {
                    $(id).puiprogressbar('option', 'value', val);
                });
    }, 1000);

}