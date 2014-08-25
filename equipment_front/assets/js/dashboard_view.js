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
    $('#dashboardTree').etree({
        nodes: tree_data
    });
});

