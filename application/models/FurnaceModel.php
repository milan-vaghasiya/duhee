<?php
class FurnaceModel extends MasterModel{
    private $furnace_master = "furnace_master";
	
    public function getDTRows($data){
        $data['tableName'] = $this->furnace_master;
        $data['searchCol'][] = "furnace_type";
        $data['serachCol'][] = "furnace_no";
        $data['serachCol'][] = "remark";
		$columns =array('','','furnace_type','furnace_no','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getFurnaceMasterData($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->furnace_master;
        return $this->row($data);
    }

    public function save($data){
        if($this->checkDuplicate($data['furnace_no'],$data['id']) > 0):
            $errorMessage['furnace_no'] = "Furnace No. is duplicate.";
            return ['status'=>0,'message'=>$errorMessage];
        else:
            return $this->store($this->furnace_master,$data,'Furnace Master');
        endif;
    }

    public function checkDuplicate($no,$id=""){
        $data['tableName'] = $this->furnace_master;
        $data['where']['furnace_no'] = $no;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        return $this->trash($this->furnace_master,['id'=>$id],'Furnace Master');
    }

    public function getFurnaceList(){
        $data['tableName'] = $this->furnace_master;
        return $this->rows($data);
    }
   
}
?>