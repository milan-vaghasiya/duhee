<?php
class HsnMasterModel extends MasterModel{
    private $hsnMaster = "hsn_master";
    
    public function getDTRows($data){
        $data['tableName'] = $this->hsnMaster;
		$data['searchCol'][] = "hsn";
        $data['searchCol'][] = "cgst";
        $data['searchCol'][] = "sgst";
        $data['searchCol'][] = "igst";
        $data['searchCol'][] = "description";
		$columns =array('','','hsn','cgst','sgst','igst','description');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getHSNDetail($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->hsnMaster;
        return $this->row($data);
    }

    public function getHSNDetailByCode($hsn){
        $data['where']['hsn'] = $hsn;
        $data['tableName'] = $this->hsnMaster;
        return $this->row($data);
    }

    public function save($data){
        if($this->checkDuplicate($data['hsn'],$data['id']) > 0):
            $errorMessage['hsn'] = "HSN is duplicate.";
            return ['status'=>0,'message'=>$errorMessage];
        else:
            return $this->store($this->hsnMaster,$data,'HSN Master');
        endif;
    }
    public function checkDuplicate($hsn,$id=""){
        $data['tableName'] = $this->hsnMaster;
        $data['where']['hsn'] = trim($hsn);
        
        if(!empty($id))
            $data['where']['id !='] = $id;
            
        return $this->numRows($data);
    }

    public function delete($id){
        return $this->trash($this->hsnMaster,['id'=>$id],'HSN Master');
    }
    
    public function getHSNList(){
        $data['tableName'] = $this->hsnMaster;
        return $this->rows($data);
    }
}
?>