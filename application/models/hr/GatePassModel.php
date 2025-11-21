<?php
class GatePassModel extends MasterModel{
    private $gatePass = "gate_pass";
    
    public function getDTRows($data){
        $data['tableName'] = $this->gatePass;
        $data['select'] = "gate_pass.*,employee_master.emp_name";
        $data['join']['employee_master'] = "employee_master.id = gate_pass.emp_id";

        $data['searchCol'][]="employee_master.emp_name";
        $data['searchCol'][]="out_time";
        $data['searchCol'][]="reason";
        $columns = array('','','emp_name','out_time','reason');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getPassDetails($id){
        $data['tableName'] = $this->gatePass;
        $data['select'] = "gate_pass.*,employee_master.emp_name";
        $data['leftJoin']['employee_master'] = "employee_master.id = gate_pass.emp_id";
        $data['where']['gate_pass.id'] = $id;
        return  $this->row($data);
    }
    
    public function save($data){
     return $this->store($this->gatePass,$data,'gatePass');
    }

    public function delete($id){
        return $this->trash($this->gatePass,['id'=>$id],'gatePass');
    }
}
?>