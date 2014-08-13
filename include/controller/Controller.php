<?php

namespace controller;

/**
 * Description of Controller
 *
 * @author Alex Pavlunenko <alexp at xpresstek.net>
 */
require_once(INCLUDE_DIR . 'class.staff.php');

abstract class Controller {

    public static function loadClass($className) {

        $className = ltrim($className,
                '\\');
        $fileName = '';
        $namespace = '';
        if ($lastNsPos = strrpos($className,
                '\\')) {
            $namespace = substr($className,
                    0,
                    $lastNsPos);
            $className = substr($className,
                    $lastNsPos + 1);
            $fileName = str_replace('\\',
                            DIRECTORY_SEPARATOR,
                            $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_',
                        DIRECTORY_SEPARATOR,
                        $className) . '.php';

        require $fileName;
    }

    protected abstract function getEntityClassName();

    protected function getListTemplateName() {
        return 'listTemplate.html.twig';
    }

    protected function getViewTemplateName() {
        return 'viewTemplate.html.twig';
    }

    protected abstract function getListColumns();

    protected abstract function getTitle($plural = true);

    protected abstract function getViewDirectory();

    protected function defaultAction() {
        $this->listAction();
    }

    public function render($template, $args = array()) {
        $loader = new \Twig_Loader_Filesystem(EQUIPMENT_VIEWS_DIR);
        $twig = new \Twig_Environment($loader);

        global $ost;
        $staff = \StaffAuthenticationBackend::getUser();
        $tocken = $ost->getCSRF();

        $args['staff'] = $staff;
        $args['linktoken'] = $ost->getLinkToken();
        $args['tocken'] = $tocken->getToken();
        $args['tocken_name'] = $tocken->getTokenName();
        if (!empty($_SESSION['flash'])) {
            $args['flash'] = $_SESSION['flash'];
            unset($_SESSION['flash']);
        }

        echo $twig->render($template,
                $args);
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
        $properties = array();
        $entityClass = $this->getEntityClassName();
        $items = $entityClass::getAll();

        foreach ($items as $item) {
            $properties[] = $item->getJsonProperties();
        }
        echo json_encode($properties);
    }

    public function listAction() {
        $args = array();
        $args['title'] = $this->getTitle();
        $args['dt_columns'] = $this->getListColumns();

        $template_name = $this->getListTemplateName();
        $this->render($template_name,
                $args);
    }

    public function viewAction($id = 0, $args = array()) {

        if ($id >= 0) {
            $entityClass = $this->getEntityClassName();
            $item = new $entityClass($id);
            $args['item'] = $item;
        }

        $template_name = $this->getViewTemplateName();

        $args['title'] = $this->getTitle();
        $args['stitle'] = $this->getTitle(false);
        $args['form_path'] = $this->getViewDirectory();
        $this->render($template_name,
                $args);
    }

    public function saveAction() {

        $entityClass = $this->getEntityClassName();
        $object = new $entityClass($_POST['id']);

        if (isset($object)) {
            if (!$object->saveFromData($_POST)) {
                $this::setFlash('error',
                        'Failed to save item!',
                        print_r($object->getErrors()));
            } else {
                $object->postSave($_POST);
                $this::setFlash('info',
                        'Success!',
                        'Item Saved');
            }
        }
        $this->defaultAction();
    }

    public function deleteAction() {
        $entityClass = $this->getEntityClassName();
        $item = new $entityClass($_POST['id']);
        if (isset($item) && $item->delete()) {
            $this::setFlash('info',
                    'Success!',
                    'Item Deleted');
        } else {
            $this::setFlash('error',
                    '!',
                    'Failed to delete Item!');
        }
        $this->listAction();
    }

    public function openTicketsJsonAction($item_id) {
        $entityClass = $this->getEntityClassName();
        $ticket_id = $entityClass::getTicketList('open',
                        $item_id);
        $tickets = $this->ticketsAction('open',
                $ticket_id);
        echo json_encode($tickets);
    }

    public function closedTicketsJsonAction($item_id) {
        $entityClass = $this->getEntityClassName();
        $ticket_id = $entityClass::getTicketList('closed',
                        $item_id);
        $tickets = $this->ticketsAction('closed',
                $ticket_id);
        echo json_encode($tickets);
    }

    protected function ticketsAction($type, $ticket_id) {
        $tickets = array();
        foreach ($ticket_id as $id) {
            $ticket = \Ticket::lookup($id['ticket_id']);
            $equipment = new \model\Equipment($id['equipment_id']);
            if (isset($ticket) && isset($equipment)) {
                $ticket_data = array(
                    'id' => $ticket->getId(),
                    'number' => $ticket->getNumber(),
                    'equipment' => $equipment->getAsset_id(),
                    'create_date' => \Format::db_datetime($ticket->getCreateDate()),
                    'subject' => $ticket->getSubject(),
                    'name' => $ticket->getName()->getFull(),
                    'priority' => $ticket->getPriority(),
                );

                if ($type == 'closed') {
                    $ts_open = strtotime($ticket->getCreateDate());
                    $ts_closed = strtotime($ticket->getCloseDate());
                    $ticket_data['close_date'] = \Format::db_datetime($ticket->getCloseDate());
                    $ticket_data['closed_by'] = $ticket->getStaff()->getUserName();
                    $ticket_data['elapsed'] = $this->elapsedTime($ts_closed - $ts_open);
                }

                $tickets[] = $ticket_data;
            }
        }
        return $tickets;
    }

    private function elapsedTime($sec) {

        if (!$sec || !is_numeric($sec)) {
            return "";
        }

        $days = floor($sec / 86400);
        $rem = $sec % 86400;
        $hrs = floor($rem / 3600);
        $rem = $rem % 3600;
        $mins = round($rem / 60);

        if ($days > 0)
            $tstring = $days . 'd, ';
        if ($hrs > 0)
            $tstring = $tstring . $hrs . 'h, ';
        $tstring = $tstring . $mins . 'm';

        return $tstring;
    }

}
