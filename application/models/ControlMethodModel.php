<?php
class ControlMethodModel extends MasterModel{
    private $controlMethod = "control_method";

    public function getDTRows($data){
        $data['tableName'] = $this->controlMethod;
        $data['searchCol'][] = "";
        $data['searchCol'][] = "control_method";
        $data['searchCol'][] = "resp_short_name";
        $data['searchCol'][] = "resp_full_name";
		$columns =array('','','control_method','resp_short_name','resp_full_name');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getControlMethod($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->controlMethod;
        return $this->row($data);
    }

    public function save($data){
        return $this->store($this->controlMethod,$data,'Control Method');
    }

    public function delete($id){
        return $this->trash($this->controlMethod,['id'=>$id],'Control Method');
    }

    public function getControlMethodList(){
        $data['tableName'] = $this->controlMethod;
        return $this->rows($data);
    }
}
?>