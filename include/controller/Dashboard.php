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

    private function getJsonTreeObject($item, &$color) {
        $data = array();
        $data['label'] = $item->getName() . ' (' . $item->countEquipment() . ')';
        $data['data'] = $item->getId();
        $data['leaf'] = false;
        $status = \model\EquipmentStatus::getAll();
        $children = $item->getChildren();
        $kids = array();
        $status_color = $color;

        foreach ($children as $child) {
            $kids[] = $this->getJsonTreeObject($child,
                    $status_color);
        }

        foreach ($status as $s_item) {
            $kids[] = $this->getStatusTreeObject($s_item,
                    $item->getId(),
                    $status_color);
        }
        $data['children'] = $kids;
        $data['color'] = $status_color;
        $color = $status_color;
        return $data;
    }

    private function getStatusTreeObject($item, $category_id, &$color) {
        $data = array();
        $baseline = \model\EquipmentStatus::getBaselineStatus();
        $data['label'] = $item->getName() .
                ' (' . $item->countEquipmentByCategory($category_id) . ')';
        $data['data'] = $item->getId();
        $data['leaf'] = false;
        $data['color'] = $item->getColor();
        $data['image'] = '../assets/images/' . $item->getImage();
        $data['children'] = $this->getItemTreeObject($item->getId(),
                $category_id,
                $item->getColor());
        if ($baseline && $baseline->getColor() != $item->getColor()) {
            if (isset($data['children']) && count($data['children']) > 0) {
                $color = $item->getColor();
            } else if(!isset($color)){
                $color = $baseline->getColor();
            }
        }

        return $data;
    }

    private function getItemTreeObject($status_id, $category_id, $color) {
        $kids = array();
        $items = \model\Equipment::findByStatusAndCategory($status_id,
                        $category_id);
        foreach ($items as $item) {
            $data = array();
            $data['label'] = $item->getAsset_id();
            $data['data'] = $item->getId();
            $data['leaf'] = true;
            $data['children'] = null;
            $data['color'] = $color;
            $kids[] = $data;
        }
        return $kids;
    }

}
