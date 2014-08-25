/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(function() {
    
     $('#equipmentTree').puipanel({
        toggleable: true
        , closable: true
    });
    
    
    $('#totalEquipment').puipanel({
        toggleable: true
        , closable: true
    });

    $('#totalCategories').puipanel({
        toggleable: true
        , closable: true
    });

    $('#totalStatus').puipanel({
        toggleable: true
        , closable: true
    });

    $('#totalTickets').puipanel({
        toggleable: true
        , closable: true
    });
    
    $('#dashboardTree').etree({  
        nodes: function(ui, response) {                          
            $.ajax({  
                type: "GET",  
                url: 'treeJson',  
                dataType: "json",  
                context: this,  
                success: function(data) {  
                    response.call(this, data);  
                }  
            });  
        }  
    });  

});

