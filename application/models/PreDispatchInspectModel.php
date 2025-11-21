<?php
class PreDispatchInspectModel extends MasterModel{
    private $preDispatch = "predispatch_inspection";
	
    public function getDTRows($data){
        $data['tableName'] = $this->preDispatch;
        $data['select'] = "predispatch_inspection.*,item_master.item_code";
		$data['join']['item_master'] = "item_master.id = predispatch_inspection.item_id";
		
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "predispatch_inspection.param_count";
		
		$columns =array('','','item_master.item_code','predispatch_inspection.param_count');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getPreInspection($id){
        $data['tableName'] = $this->preDispatch;
		$data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        return $this->store($this->preDispatch,$data,'Predispatch Inspection');
    }

    public function delete($id){
        return $this->trash($this->preDispatch,['id'=>$id],'Predispatch Inspection');
    }
}
?>