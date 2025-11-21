<?php
class MachineActivitiesModel extends MasterModel{
    private $machineActivities = "machine_activities";

    public function getDTRows($data){
        $data['tableName'] = $this->machineActivities;
        $data['searchCol'][] = "activities";
		$columns =array('','','activities');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getActivities($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->machineActivities;
        return $this->row($data);
    }

    public function save($data){
        $data['activities'] = trim($data['activities']);
        if($this->checkDuplicate($data['activities'],$data['id']) > 0):
            $errorMessage['activities'] = "Activities is duplicate.";
            return ['status'=>0,'message'=>$errorMessage];
        else:
            return $this->store($this->machineActivities,$data,'Machine Activities');
        endif;
    }

    public function checkDuplicate($activities,$id=""){
        $data['tableName'] = $this->machineActivities;
        $data['where']['activities'] = $activities;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        return $this->trash($this->machineActivities,['id'=>$id],'Machine Activities');
    }
}
?>