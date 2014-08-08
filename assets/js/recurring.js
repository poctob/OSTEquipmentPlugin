var selectedItem = 0;
$(function() {


    $('#next_date').datepicker();

    $('#interval').puispinner({
        min: 1,
        max: 3650
    });

    $('#ticketsDD').puidropdown();
    $('#equipmentDD').puidropdown();
  //  $('#intervalDD').puidropdown();
    

    $.getJSON(eq_root + 'recurring/listTicketsJson', populateTicketsDropDown);
    $.getJSON(eq_root + 'recurring/listEquipmentJson', populateEquipmentDropDown);

    function populateTicketsDropDown(data)
    {

        for (var key in data)
        {
            var ticket = data[key];
            $('#ticketsDD').puidropdown
                    ('addOption', ticket['number'], ticket['ticket_id']);

            if (typeof ticket_id !== 'undefined')
            {
                $('#ticketsDD').puidropdown('selectValue', ticket_id);
            }
        }
    }

    function populateEquipmentDropDown(data)
    {

        for (var key in data)
        {
            var equipment = data[key];
            $('#equipmentDD').puidropdown
                    ('addOption', equipment['asset_id'], equipment['equipment_id']);

            if (typeof equipment_id !== 'undefined')
            {
                $('#equipmentDD').puidropdown('selectValue', equipment_id);
            }
        }
    }

});



