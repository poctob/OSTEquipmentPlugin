<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of plugin
 *
 * @author Alex Pavlunenko <alexp at xpresstek.net>
 */

set_include_path(get_include_path().PATH_SEPARATOR.dirname(__file__).'/include');
return array(
    'id' =>             'xpresstek:equipment', # notrans
    'version' =>        '0.3',
    'name' =>           'Equipment Manager',
    'author' =>         'Alex Pavlunenko',
    'description' =>    'Provides equipment asset management capability.',
    'url' =>            'http://www.xpresstek.net/osticket/plugins/equipment',
    'plugin' =>         'equipment.php:EquipmentPlugin'
);

?>