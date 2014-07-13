<?php

/**
 * Description of Controller
 *
 * @author Alex Pavlunenko <alexp at xpresstek.net>
 */
require_once (EQUIPMENT_APP_DIR.'app.php');

class Controller {
    public function render($template, $args = array())
    {
        $loader = new Twig_Loader_Filesystem(EQUIPMENT_VIEWS_DIR);
        $twig = new Twig_Environment($loader);  
       // print_r($args);
        echo $twig->render($template, $args);

    }
    
    public function redirectImagesAction($url)
    {
        $url_l=$url;
        $im = imagecreatefrompng(EQUIPMENT_ASSETS_DIR.$url_l);
        header('Content-Type: image/jpeg');
        imagepng($im);
       // imagedestroy($im);
      //  echo EQUIPMENT_ASSETS_DIR.$url;
    }
}
