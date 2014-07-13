
    <?php

    /*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */

    /**
     * Description of MediaController
     *
     * @author alex
     */
    require 'Controller.php';

    class MediaController extends Controller {

        public function defaultAction($request_path) {

            $file = EQUIPMENT_ASSETS_DIR . $request_path;
            

            if (file_exists($file)) {

                if($this->endsWith($file, '.js'))
                {
                    header('Content-type: text/javascript');
                }
                
                else if($this->endsWith($file, '.css'))
                {
                    header('Content-type: text/css');
                }
                
                else if($this->endsWith($file, '.png'))
                {
                    header('Content-type: image/png');
                }
                readfile($file);
            } else {
                echo 'File does not exist';
            }
        }

        public function endsWith($haystack, $needle) {
            $length = strlen($needle);
            if ($length == 0) {
                return true;
            }

            return (substr($haystack, -$length) === $needle);
        }

    }
    