<?php

/**
 * Description of Controller
 *
 * @author Alex Pavlunenko <alexp at xpresstek.net>
 */
require_once (EQUIPMENT_APP_DIR . 'app.php');
require_once(INCLUDE_DIR . 'class.staff.php');

class Controller {

    public function render($template, $args = array()) {
        $loader = new Twig_Loader_Filesystem(EQUIPMENT_VIEWS_DIR);
        $twig = new Twig_Environment($loader);

        global $ost;
        $staff = StaffAuthenticationBackend::getUser();
        $tocken = $ost->getCSRF();

        $args['staff'] = $staff;
        $args['linktoken'] = $ost->getLinkToken();
        $args['tocken'] = $tocken->getToken();
        $args['tocken_name'] = $tocken->getTokenName();

        echo $twig->render($template, $args);
    }

    public function redirectImagesAction($url) {
        $url_l = $url;
        $im = imagecreatefrompng(EQUIPMENT_ASSETS_DIR . $url_l);
        header('Content-Type: image/jpeg');
        imagepng($im);
        // imagedestroy($im);
        //  echo EQUIPMENT_ASSETS_DIR.$url;
    }

}
