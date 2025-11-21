<?php
class PlanningTypesModel extends MasterModel{
    private $purchaseMaster = "purchase_planning_type";
	
    public function getDTRows($data){
        $data['tableName'] = $this->purchaseMaster;
        $data['searchCol'][] = "planning_type";
		$columns =array('','','planning_type','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getPlanning($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->purchaseMaster;
        return $this->row($data);
    }

    public function save($data){
        return $this->store($this->purchaseMaster,$data,'Planning Types');
    }

    public function delete($id){
        return $this->trash($this->purchaseMaster,['id'=>$id],'Planning Types');
    }
}
?>