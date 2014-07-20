<?php

/**
 * Description of Controller
 *
 * @author Alex Pavlunenko <alexp at xpresstek.net>
 */
require_once (EQUIPMENT_APP_DIR . 'app.php');
require_once(INCLUDE_DIR . 'class.staff.php');

spl_autoload_register('Controller::loadClass');

abstract class Controller {

    public static function loadClass($class) {
        $path = EQUIPMENT_INCLUDE_DIR . 'class.' . strtolower($class) . '.php';
        include_once($path);
        if (class_exists($class, false)) {
            return new $class();
        } else {
            Controller::setFlash('error', 'Class load error!', 'Failed to load ' . $class);
            return false;
        }
    }

    protected abstract function getEntityClassName();

    protected abstract function getListTemplateName();

    protected abstract function getViewTemplateName();
    
    protected function defaultAction()
    {
        $this->viewAction($_POST['id']);
    }

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
        if (!empty($_SESSION['flash'])) {
            $args['flash'] = $_SESSION['flash'];
            unset($_SESSION['flash']);
        }

        echo $twig->render($template, $args);
    }

    public static function setFlash($severity, $summary, $details) {
        if (!empty($_SESSION['flash'])) {
            unset($_SESSION['flash']);
        }

        $flash = array(
            'severity' => $severity,
            'summary' => $summary,
            'details' => $details
        );

        $_SESSION['flash'] = $flash;
    }

    public function listJsonAction() {
        $entityClass = $this->getEntityClassName();
        $items = $entityClass::getAll();
        echo json_encode($items);
    }

    public function listAction() {
        $template_name = $this->getListTemplateName();
        $this->render($template_name);
    }

    public function viewAction($id=0, $args=array()) {
        $entityClass = $this->getEntityClassName();
        if (isset($id) && $id > 0) {            
            $item = $entityClass::lookup($id);
            $title = 'Edit ' . $entityClass;
        } else {
            $item = new $entityClass();
            $title = 'New ' . $entityClass;
        }

        $template_name = $this->getViewTemplateName();
        $args['item'] = $item;
        $args['title'] = $title;
        $this->render($template_name, $args);
    }

    public function saveAction() {
        $errors = array();
        $form_id = EquipmentPlugin::getCustomForm();
        echo  $form_id;
        $_POST['form_id'] = $form_id;
        $entityClass = $this->getEntityClassName();
        $entityClass::save($_POST['id'], $_POST, $errors);
        if(isset($errors) && count($errors)>0)
        {
            $this::setFlash('error', 'Failed to save item!', print_r($errors));
        }
        else
        {
            $this::setFlash('info', 'Success!', 'Item Saved');
        }
        $this->defaultAction();
    }

    public function deleteAction() {
        $entityClass = $this->getEntityClassName();
        $item = new $entityClass($_POST['id']);
        if (isset($item) && $item->delete()) {
            $this::setFlash('info', 'Success!', 'Item Deleted');
        }
        else {
            $this::setFlash('error', '!', 'Failed to delete Item!');
        }
        $this->listAction();
    }

}
