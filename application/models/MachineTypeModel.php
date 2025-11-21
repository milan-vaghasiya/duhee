<?php
class MachineTypeModel extends MasterModel{
    private $machineType = "machine_type";

    public function getDTRows($data){
        $data['tableName'] = $this->machineType;
   
        $data['searchCol'][] = "typeof_machine";

		$columns =array('','','typeof_machine');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getMachineType($id){
        $data['tableName'] = $this->machineType;
	    $data['where']['id'] = $id;
        return $this->row($data);
	}

    public function getMachineTypeList(){
        $data['tableName'] = $this->machineType;
        return $this->rows($data);
    }

    public function save($data){
        return $this->store($this->machineType,$data,'Machine Type');
    }

    public function delete($id){
        return $this->trash($this->machineType,['id'=>$id],'Machines Type');
    }

    public function getMachineTypeByName($typeof_machine){
        $data['tableName'] = $this->machineType;
	    $data['where']['	typeof_machine'] = $typeof_machine;
        return $this->row($data);
	}
}
?>