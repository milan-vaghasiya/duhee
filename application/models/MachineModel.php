<?php
class MachineModel extends MasterModel{
    private $machineMaster = "item_master";
    private $machineActivities = "machine_activities";
    private $machinePrevnective = "machine_preventive";
    private $machineMaintenance = "machine_maintenance";
    private $itemMaster = "item_master";
    private $requisitionLog = "requisition_log";
    private $machineConfig = "machine_config";
	
    public function getDTRows($data){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "id,item_code,item_name,item_type,description,make_brand,mfg_year,install_year,location,prev_maint_req,process_id,size";
        $data['where']['item_type'] = 5;
        
        $data['searchCol'][] = "item_name";
        $data['searchCol'][] = "item_code";
        $data['searchCol'][] = "make_brand";
        $data['searchCol'][] = "size";
        $data['searchCol'][] = "install_year";
        $data['searchCol'][] = "location";
        $data['searchCol'][] = "prev_maint_req";

		$columns =array('','','item_name','item_code','make_brand','size','install_year','location','prev_maint_req','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getProcess($id){
        $data['where_in']['id'] = $id;
        $data['tableName'] = 'process_master';
        return $this->rows($data);
    }

    public function getMachine($id){
        $data['select'] = "item_master.*";
        $data['where']['id'] = $id;
        $data['where']['item_type'] = 5;
        $data['tableName'] = $this->itemMaster;
        return $this->row($data);
    }

    public function getMachineList(){
        $data['select'] = "item_master.*";
        $data['where']['item_type'] = 5;
        $data['tableName'] = $this->itemMaster;
        return $this->rows($data);
    }

	public function getmaintanenceData($machine_id){
		$data['select'] = "machine_preventive.*,machine_activities.activities";
        $data['join']['machine_activities'] = "machine_activities.id = machine_preventive.activity_id";
		$data['where']['machine_id'] = $machine_id;
        $data['tableName'] = $this->machinePrevnective;
        return $this->rows($data);
	}
	
    public function getActivity()
    {
        $data['tableName'] = $this->machineActivities;
        return $this->rows($data);
    }

    public function getProcessWiseMachine($processId){
        $data['customWhere'][] = 'find_in_set("'.$processId.'", process_id)';
        $data['tableName'] = $this->itemMaster;
        return $this->rows($data);
    }

    public function save($data){    
        if($this->checkDuplicate($data['item_code'],$data['id']) > 0):
            $errorMessage['item_code'] = "Machine No. is duplicate.";
            return ['status'=>0,'message'=>$errorMessage];
        else:
            $result = $this->store($this->itemMaster,$data,'Machine');
            return $result;	
        endif;
    }

    public function checkDuplicate($name,$id=""){
        $data['tableName'] = $this->itemMaster;
        $data['where']['item_code'] = $name;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        return $this->trash($this->itemMaster,['id'=>$id],'Machines');
    }

    public function saveActivity($machine_id,$itemData){

		$queryData['select'] = "id";
		$queryData['where']['machine_id'] = $machine_id;
        $queryData['tableName'] = $this->machinePrevnective;
        $old_data = $this->rows($queryData);
		
        foreach($itemData['activity_id'] as $key=>$value):
            $activityData = [
                'id' => $itemData['id'][$key],
				'machine_id' => $machine_id,
				'activity_id' => $value,
				'checking_frequancy' => $itemData['checking_frequancy'][$key],
                'created_by' => $itemData['created_by'][$key]
            ];
            $this->store($this->machinePrevnective,$activityData,'Machines');
        endforeach;
		
		foreach($old_data as $value):
			if(!in_array($value->id,$itemData['id'])):						
				$this->trash($this->machinePrevnective,['id'=>$value->id]);
			endif;
		endforeach;
        $result = ['status'=>1,'message'=>'Machine Activity saved successfully.','url'=>base_url("machines")];
        return $result;
    }

    public function getMachineForReport(){
        $data['tableName'] = $this->itemMaster;
        $data['where']['item_type'] = 5;
        return $this->rows($data);
    }

    public function getPartReplacementData($data){
        $queryData = array();
		$queryData['tableName'] = $this->requisitionLog;
		$queryData['select'] = 'requisition_log.*,machine_maintenance.machine_id,machine_maintenance.trans_no,machine_maintenance.trans_prefix,item_master.item_name,mc.item_name as itemName,mc.item_code as itemCode';
		$queryData['leftJoin']['machine_maintenance'] = 'machine_maintenance.id = requisition_log.req_from';
		$queryData['leftJoin']['item_master'] = 'item_master.id = requisition_log.req_item_id';
		$queryData['leftJoin']['item_master as mc'] = 'mc.id = machine_maintenance.machine_id';
        $queryData['where']['requisition_log.log_type'] = 2;
        $queryData['where']['requisition_log.reqn_type'] = 2;
		if(!empty($data['machine_id'])){$queryData['where']['machine_maintenance.machine_id'] = $data['machine_id'];}
        $queryData['customWhere'][] = "DATE(requisition_log.issue_date) BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['requisition_log.issue_date'] = 'ASC';
		return $this->rows($queryData);
    }
    
    public function getMachinePrevMaintenance($machine_id){
        $data['tableName'] = $this->machinePrevnective;
		$data['select'] = 'machine_preventive.*, machine_activities.activities';
		$data['leftJoin']['machine_activities'] = 'machine_preventive.activity_id = machine_activities.id';
        $data['where']['machine_preventive.machine_id'] = $machine_id;
        return $this->rows($data);
    }

    // Created By Meghavi @08/11/2022
    public function getMachineAssignOperator($postData){
        $data['tableName'] = $this->machineConfig;
        $data['where']['shift_id'] = $postData['shift_id'];
        $data['where']['machine_id'] = $postData['machine_id'];
        if(!empty($postData['emp_type']) && $postData['emp_type'] == 'INQ'){ $data['where']['inq_id >'] = 0;}
        if(!empty($postData['emp_type']) && $postData['emp_type'] == 'OPR'){ $data['where']['opr_id >'] = 0;}
        $data['customWhere'][] = 'to_date IS NULL';
        return $this->row($data);
    }

    public function saveOprInqData($data){
        try{
            $this->db->trans_begin();
            $prevData = $this->getMachineAssignOperator(['machine_id'=>$data['machine_id'],'shift_id'=>$data['shift_id'],'emp_type'=>$data['emp_type']]);
            unset($data['emp_type']);

            $data['from_date'] = date("Y-m-d H:i:s");
            if(!empty($data['opr_id']) || !empty($data['inq_id'])){
                $data['id'] = '';
                $result = $this->store($this->machineConfig,$data);
            }
            if(!empty($prevData)){
                $to_date = date('Y-m-d H:i:s', strtotime($data['from_date'].' -1 minutes'));
                $this->store($this->machineConfig,['id'=>$prevData->id,'to_date'=>$data['from_date']]);
            }
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
            
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getMachineAsignData($postData){
        $data['tableName'] = $this->machineConfig;
        $data['select'] = 'machine_config.from_date,machine_config.to_date,employee_master.emp_name';
        $data['where']['machine_config.shift_id'] = $postData['shift_id'];
        $data['where']['machine_config.machine_id'] = $postData['machine_id'];
        if(!empty($postData['emp_type']) && $postData['emp_type'] == 'INQ'){ $data['leftJoin']['employee_master'] = 'machine_config.inq_id = employee_master.id'; $data['where']['inq_id >'] = 0;}
        if(!empty($postData['emp_type']) && $postData['emp_type'] == 'OPR'){ $data['leftJoin']['employee_master'] = 'machine_config.opr_id = employee_master.id'; $data['where']['opr_id >'] = 0;}
        $data['customWhere'][] = 'machine_config.from_date >= "'.$postData['from_date'].'" AND machine_config.from_date < "'.$postData['to_date'].'"  AND (machine_config.to_date <= "'.$postData['to_date'].'" OR machine_config.to_date IS NULL)';
        return $this->rows($data);
    }
}
?>