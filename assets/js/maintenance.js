$(function() {
    $('#waitImage').hide();
    $('#waitImage2').hide();
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
    
    $('#purgeData').puibutton(
            {
                click: function(event)
                {
                    $('#waitImage').show();
                    $.ajax({
                        url: 'purgeData',
                        dataType: "html"
                    })
                            .done(function( data ) {
                                $('#waitImage').hide();
                                $('#purgeResults').html(data);
                            });
                }
            }
    );
    
    $('#recreateDatabase').puibutton(
            {
                click: function(event)
                {
                    $('#waitImage2').show();
                    $.ajax({
                        url: 'recreateDatabase',
                        dataType: "html"
                    })
                            .done(function( data ) {
                                $('#waitImage2').hide();
                                $('#recreateResults').html(data);
                            });
                }
            }
    );
});
