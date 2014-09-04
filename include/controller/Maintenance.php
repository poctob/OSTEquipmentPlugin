<?php
namespace controller;

class Maintenance{

    private $progress = 0;
    
    public function defaultAction()
    {         
        $loader = new \Twig_Loader_Filesystem(EQUIPMENT_VIEWS_DIR);
        $twig = new \Twig_Environment($loader);
        $args = array();
        $args['title'] = 'Plugin Maintenance';
        global $ost;
        $staff = \StaffAuthenticationBackend::getUser();
        $tocken = $ost->getCSRF();

        $args['staff'] = $staff;
        $args['linktoken'] = $ost->getLinkToken();
        $args['tocken'] = $tocken->getToken();
        $args['tocken_name'] = $tocken->getTokenName();

        echo $twig->render('maintenanceTemplate.html.twig',
                $args);
    
    }
    
    public function startDatabaseIntegrityTest()
    {
        $_SESSION['eq_maint_progress'] = 0;
        while($_SESSION['eq_maint_progress'] < 100)
        {
            sleep(2);
            $_SESSION['eq_maint_progress']+=10;                    
        }
    }
    
    public function checkProgress()
    {
        return $_SESSION['eq_maint_progress'];
    }
}
