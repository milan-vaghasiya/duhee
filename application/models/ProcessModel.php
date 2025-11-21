<?php
class ProcessModel extends MasterModel{
    private $processMaster = "process_master";
	
    public function getDTRows($data){
        $data['tableName'] = $this->processMaster;
        $data['select'] = "process_master.*,department_master.name as dept_name";
		$data['leftJoin']['department_master'] = "process_master.dept_id = department_master.id";
		
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "department_master.name";
        $data['serachCol'][] = "process_master.remark";
		
		$columns =array('','','process_master.process_name','department_master._name','process_master.remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getProcessList(){
        $data['tableName'] = $this->processMaster;
        return $this->rows($data);
    }

    public function getProcess($id){
        $data['tableName'] = $this->processMaster;
        $data['select'] = "process_master.*,department_master.name as dept_name";
		$data['leftJoin']['department_master'] = "process_master.dept_id = department_master.id";
        $data['where']['process_master.id'] = $id;
        return $this->row($data);
    }

    //Updated  By Karmi @27/04/2022
    public function save($data){
        if($this->checkDuplicate($data['process_name'],$data['id']) > 0):
            $errorMessage['process_name'] = "Process name is duplicate.";
            $result = ['status'=>0,'message'=>$errorMessage];
        else:
            $result = $this->store($this->processMaster,$data,'Process');

            /** Process added in store */
            /*$process_id = !empty($data['id']) ? $data['id'] : $result['insert_id'];
            $strQuery['where']['ref_id'] = $process_id;
            $strQuery['where']['store_type'] = 101;
            $strQuery['tableName'] = 'location_master';
            $strResult = $this->row($strQuery);
            if (empty($strResult)) {
                $storeData = [
                    'id' => '',
                    'store_name' => "Process",
                    'location' => $data['process_name'],
                    'store_type' => 101,
                    'ref_id' => $process_id
                ];
                $this->store->save($storeData);
            }*/
            
        endif;
        return $result;
    }

    public function checkDuplicate($name,$id=""){
        $data['tableName'] = $this->processMaster;
        $data['where']['process_name'] = $name;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        return $this->trash($this->processMaster,['id'=>$id],'Process');
    }
}
?>