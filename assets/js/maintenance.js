$(function() {
    $('#waitImage').hide();
    $('#tabview').puitabview(
            {
                orientation: 'left'
            });

    $('#startStructureTest').puibutton(
            {
                click: function(event)
                {
                    $('#waitImage').show();
                    $.ajax({
                        url: 'startStructureTest',
                        dataType: "html"
                    })
                            .done(function( data ) {
                                $('#waitImage').hide();
                                $('#structureTestResults').html(data);
                            });
                }
            }
    );
});
