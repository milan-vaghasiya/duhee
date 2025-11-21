<?php
class ExternalHeatTreatmentModel extends MasterModel{
    private $external_heat_treatment = "external_heat_treatment";
    private $heat_treatment_approval = "heat_treatment_approval";

    public function getDTRows($data){       
        $data['tableName'] = $this->external_heat_treatment;
        $data['select'] = 'external_heat_treatment.*,item_master.item_code,item_master.item_name,item_master.drawing_no,item_master.rev_no,item_master.material_grade,item_master.part_no,item_category.category_name,heat_treatment_approval.approve_by,heat_treatment_approval.ext_ht_id';
        $data['leftJoin']['item_master'] = "item_master.id = external_heat_treatment.item_id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $data['leftJoin']['heat_treatment_approval'] = "heat_treatment_approval.ext_ht_id = external_heat_treatment.id";
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "item_master.part_no";
        $data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "item_master.drawing_no";
        $data['searchCol'][] = "item_master.rev_no";
        $data['searchCol'][] = "external_heat_treatment.carb_drg_no";
        $data['searchCol'][] = "external_heat_treatment.carb_rev_no";
        $data['searchCol'][] = "item_master.material_grade";
        $data['searchCol'][] = "";

		$columns =array('','','item_master.item_code','item_master.part_no','item_category.category_name','item_master.drawing_no','item_master.rev_no','external_heat_treatment.carb_drg_no','external_heat_treatment.carb_rev_no','item_master.material_grade','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data['item_id'],$data['id']) > 0):
                $errorMessage['item_id'] = "Item Name is duplicate.";
                $result = ['status'=>0,'message'=>$errorMessage];
            else:
                $result = $this->store($this->external_heat_treatment,$data,'External Heat Treatment');
            endif;
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function checkDuplicate($item_id,$id=""){
        $data['tableName'] = $this->external_heat_treatment;
        $data['where']['item_id'] = $item_id;        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function getHeatTreatment($id){
        $data['tableName'] = $this->external_heat_treatment;
        $data['select'] = 'external_heat_treatment.*,item_master.item_code,item_master.item_name,item_master.drawing_no,item_master.rev_no,item_master.material_grade,item_master.part_no,item_category.category_name,item_master.wt_pcs';
        $data['leftJoin']['item_master'] = "item_master.id = external_heat_treatment.item_id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $data['where']['external_heat_treatment.id'] = $id;
		return $this->row($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
            $result = $this->trash($this->external_heat_treatment,['id'=>$id],'External Heat Treatment');
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function saveBatchQtyDetails($data){ 
        try{
            $this->db->trans_begin();
            
            $result = $this->store($this->external_heat_treatment,$data,'External Heat Treatment');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    public function getExternalHtapproval($id){
        $data['tableName'] = $this->heat_treatment_approval;
        $data['select'] = 'heat_treatment_approval.*';
        $data['where']['heat_treatment_approval.id'] = $id;
		return $this->row($data);
    }

    public function saveApproveExternalHT($data)
	{
		try {
			$this->db->trans_begin();
			$result = $this->store($this->heat_treatment_approval, $data, 'External Heat Treatment');
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

    public function uploadBottomFile($data){ 
		$this->store($this->external_heat_treatment, ['id'=> $data['id'], 'bottom_layer' => $data['bottom_layer']]);
		return ['status' => 1, 'message' => 'File Upload successfully.'];
	}

    public function uploadBatchFile($data){ 
		$this->store($this->external_heat_treatment, ['id'=> $data['id'], 'batch_no' => $data['batch_no']]);
		return ['status' => 1, 'message' => 'File Upload successfully.'];
	}

}
?>