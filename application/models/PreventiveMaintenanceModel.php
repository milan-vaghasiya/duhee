<?php
class PreventiveMaintenanceModel extends MasterModel{
    private $preventive_maintenance_plan = " preventive_maintenance_plan";
    private $machinePrevnective = " machine_preventive";

    public function getDTRows($data){
        $data['tableName'] = $this->preventive_maintenance_plan;
        $data['select'] = "preventive_maintenance_plan.*,item_master.item_name,item_master.item_code,machine_activities.activities";
        $data['leftJoin']['item_master'] = "item_master.id=preventive_maintenance_plan.machine_id";
        $data['leftJoin']['machine_activities'] = "machine_activities.id=preventive_maintenance_plan.activity_id";
        $data['searchCol'][] = "CONCAT(item_master.item_code,item_master.item_name)";
        $data['searchCol'][] = "machine_activities.activities";
        $data['searchCol'][] = "DATE_FORMAT(preventive_maintenance_plan.last_maintence_date,'%d-%m-%Y')";
        $data['searchCol'][] = "DATE_FORMAT(preventive_maintenance_plan.due_date,'%d-%m-%Y')";
        $data['searchCol'][] = "DATE_FORMAT(preventive_maintenance_plan.schedule_date,'%d-%m-%Y')";
      

		$columns =array('','','item_master.item_name','machine_activities.activities','last_maintence_date','due_date','schedule_date');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getMachineActivities($maintence_frequancy){
        $data['select'] = "machine_preventive.*,machine_activities.activities,item_master.item_name,item_master.item_code";
        $data['join']['machine_activities'] = "machine_activities.id = machine_preventive.activity_id";
        $data['join']['item_master'] = "item_master.id = machine_preventive.machine_id";
		$data['where']['checking_frequancy'] = $maintence_frequancy;
        $data['tableName'] = $this->machinePrevnective;
        return $this->rows($data);
    }

    public function getLastMaintainanceDate($machine_id,$activity_id){
        $data['tableName'] = $this->preventive_maintenance_plan;
        $data['select'] = "MAX(last_maintence_date) as last_maintence_date";
        $data['where']['machine_id'] = $machine_id;
        $data['where']['activity_id'] = $activity_id;
        return $this->row($data);
    }

    public function save($data){

        foreach($data['schedule_date'] as $key=>$value){
            if(!empty($value)){
                $planData=[
                    'id'=>$data['id'][$key],
                    'machine_id'=>$data['machine_id'][$key],
                    'activity_id'=>$data['activity_id'][$key],
                    'maintence_frequancy'=>$data['maintence_frequancy'],
                    'last_maintence_date'=>$data['last_maintence_date'][$key],
                    'due_date'=>$data['due_date'][$key],
                    'schedule_date'=>$value,
                    'created_by'=>$data['created_by']
                ];
                $result=$this->store($this->preventive_maintenance_plan,$planData);
            }
        }
        return $result;
    }

    public function getMaintanancePlan($id){
        $data['tableName'] = $this->preventive_maintenance_plan;
        $data['select'] = "preventive_maintenance_plan.*,item_master.item_name,item_master.item_code,machine_activities.activities";
        $data['leftJoin']['item_master'] = "item_master.id=preventive_maintenance_plan.machine_id";
        $data['leftJoin']['machine_activities'] = "machine_activities.id=preventive_maintenance_plan.activity_id";
        $data['where']['preventive_maintenance_plan.id'] = $id;
        return $this->row($data);
    }

    public function delete($id){
        return $this->trash($this->preventive_maintenance_plan,['id'=>$id],'Plan');
    }

    public function saveUpdatedPlan($data){
        $result=$this->store($this->preventive_maintenance_plan,$data);
        return $result;
    }
}
