<?php

namespace controller;

class Dashboard extends Controller {

    protected function getEntityClassName() {
        return 'model\EquipmentDashboard';
    }

    protected function getListTemplateName() {
        return 'dashboard_list.html.twig';
    }

    protected function getViewTemplateName() {
        return 'dashboard_view.html.twig';
    }

    public function displayAction() {
        
    }

    protected function getListColumns() {
        
    }

    protected function getTitle($plural = true) {
        return "Equipment Dashboard";
    }

    protected function getViewDirectory() {
        
    }

    public function treeJsonAction() {
        $items = \model\EquipmentCategory::getAll();
        $object = array();
        foreach ($items as $item) {
            if ($item->getParent_id() == 0) {
                $data = $this->getJsonTreeObject($item);
                $object[] = $data;
            }
        }

        return json_encode($object);
    }

    private function getJsonTreeObject($item) {
        $data = array();
        $data['label'] = $item->getName().' ('.$item->countEquipment().')';
        $data['data'] = $item->getId();
        $data['leaf'] = false;
        $status = \model\EquipmentStatus::getAll();
        $children = $item->getChildren();
        $kids = array();

        foreach ($children as $child) {
            $kids[] = $this->getJsonTreeObject($child);
        }

        foreach ($status as $s_item) {
            $kids[] = $this->getStatusTreeObject($s_item, $item->getId());
        }
        $data['children'] = $kids;
        return $data;
    }

    private function getStatusTreeObject($item, $category_id) {
        $data = array();
        $data['label'] = $item->getName().
                ' ('.$item->countEquipmentByCategory($category_id).')';
        $data['data'] = $item->getId();
        $data['leaf'] = false;
        $data['children'] = $this->getItemTreeObject($item->getId(),
                $category_id);
        return $data;
    }

    private function getItemTreeObject($status_id, $category_id) {
        $kids=array();
        $items = \model\Equipment::findByStatusAndCategory($status_id,
                        $category_id);
        foreach ($items as $item) {
            $data = array();
            $data['label'] = $item->getAsset_id();
            $data['data'] = $item->getId();
            $data['leaf'] = true;
            $data['children'] = null;
            $kids[]=$data;
        }
        return $kids;
    }

}
