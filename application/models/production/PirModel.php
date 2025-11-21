<?php
class PirModel extends MasterModel
{
    private $ic_inspection = "ic_inspection";
    private $job_transaction = "job_transaction";
  
   
    public function getDTRows($data)
    {
        $data['tableName'] = $this->ic_inspection;
        $data['select'] = "ic_inspection.*,item_master.item_name,item_master.full_name,item_master.item_code,employee_master.emp_name,job_card.job_number,job_card.order_status,job_card.process,process_master.process_name,mc.item_name as machine_name,mc.item_code as machine_code";

        $data['leftJoin']['employee_master'] = "ic_inspection.created_by = employee_master.id";
        $data['leftJoin']['job_card'] = "job_card.id = ic_inspection.mir_id";
        $data['leftJoin']['item_master'] = "item_master.id = ic_inspection.item_id";
        $data['leftJoin']['item_master as mc'] = "mc.id = ic_inspection.party_id";
        $data['leftJoin']['process_master'] = "process_master.id = ic_inspection.mir_trans_id";
        $data['where']['ic_inspection.trans_type'] = 2;
        $data['order_by']['ic_inspection.created_at'] = "DESC";
        $data['order_by']['ic_inspection.id'] = "DESC";

        $data['searchCol'][] = "DATE_FORMAT(ic_inspection.created_at,'%d-%m-%Y')";
        $data['searchCol'][] = "ic_inspection.trans_no";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "mc.item_name";
        $data['searchCol'][] = "ic_inspection.remark";

        $columns = array('', '', 'prod_setup_request.created_at', 'prod_setup_request.trans_no', 'employee_master.emp_name', 'job_card.job_number', 'pfc_trans.process_no', 'item_master.full_name', 'mc.item_name', 'ic_inspection.remark');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function getPendingPirDTRows($data)
    {
        $data['tableName'] = $this->job_transaction;
        $data['select']="job_transaction.*,item_master.full_name,mc.item_name as machine_name,mc.item_code as machine_code,job_card.job_number,process_master.process_name";
        $data['leftJoin']['job_card'] = "job_card.id = job_transaction.job_card_id";
        $data['leftJoin']['process_master'] = "process_master.id = job_transaction.process_id";
        $data['leftJoin']['item_master'] = "item_master.id = job_transaction.product_id";
        $data['leftJoin']['item_master mc'] = "mc.id = job_transaction.machine_id";
        $data['where']['job_transaction.entry_type'] = 0;
        $data['where']['job_transaction.stage_type'] =2;
        $data['where']['job_card.order_status'] = 2;
        $data['group_by'][]="job_transaction.job_card_id,job_transaction.process_id,job_transaction.machine_id";
        $data['order_by']['job_transaction.job_card_id'] ='DESC';

        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "CONCAT(mc.item_code,mc.item_name)";
        $data['searchCol'][] = "";

        $columns = array('', '', 'job_card.job_number', 'item_master.full_name', 'process_master.process_name', 'mc.item_code','');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function save($data){
        try{
			$this->db->trans_begin();
            if(empty($data['id'])){ $data['trans_no'] = $this->gateReceipt->getNextIIRNo(2); }
			$result = $this->store($this->ic_inspection,$data);
			$result['url']='production/pir';
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
    }
   
    /* Updated By :- Sweta @26-03-2024 */
    public function getPirData($id){
		$data['tableName'] = $this->ic_inspection;
        $data['select'] = "ic_inspection.*,item_master.item_name,item_master.full_name,item_master.item_code,employee_master.emp_name,job_card.job_number,job_card.process,process_master.process_name,mc.item_name as machine_name,mc.item_code as machine_code,st.batch_no as heat_no,department_master.name";
        $data['leftJoin']['employee_master'] = "ic_inspection.created_by = employee_master.id";
        $data['leftJoin']['job_card'] = "job_card.id = ic_inspection.mir_id";
        $data['leftJoin']['item_master'] = "item_master.id = ic_inspection.item_id";
        $data['leftJoin']['item_master as mc'] = "mc.id = ic_inspection.party_id";
        $data['leftJoin']['process_master'] = "process_master.id = ic_inspection.mir_trans_id";
        $data['leftJoin']['department_master'] = "department_master.id = process_master.dept_id";
        $data['leftJoin']['(SELECT batch_no,ref_id FROM stock_transaction WHERE is_delete = 0 AND ref_type = 3 GROUP BY batch_no,ref_id) as st'] = "st.ref_id = job_card.id";        
		$data['where']['ic_inspection.id'] = $id;    
		return $this->row($data);
	}

    public function delete($id){
        try{
			$this->db->trans_begin();
			$result = $this->trash($this->ic_inspection,['id'=>$id]);
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
    }

    /* Updated By :- Sweta @26-03-2024 */
    public function getPIRReports($postData){
        $data['tableName'] = $this->ic_inspection;
        $data['select'] = "ic_inspection.*,item_master.item_name,item_master.full_name,item_master.item_code,item_master.part_no,employee_master.emp_name,job_card.job_number,job_card.process,process_master.process_name,mc.item_name as machine_name,mc.item_code as machine_code,item_master.material_grade,job_card.party_id,party_master.party_name,st.batch_no as heat_no,department_master.name,mir_transaction.heat_no as mill_heat_no"; // 26-03-2024
        $data['leftJoin']['employee_master'] = "ic_inspection.created_by = employee_master.id";
        $data['leftJoin']['job_card'] = "job_card.id = ic_inspection.mir_id";
        $data['leftJoin']['item_master'] = "item_master.id = ic_inspection.item_id";
        $data['leftJoin']['item_master as mc'] = "mc.id = ic_inspection.party_id";
        $data['leftJoin']['process_master'] = "process_master.id = ic_inspection.mir_trans_id";
        $data['leftJoin']['party_master'] = 'party_master.id = job_card.party_id';
        $data['leftJoin']['department_master'] = "department_master.id = process_master.dept_id";
        $data['leftJoin']['(SELECT batch_no,item_id,ref_id FROM stock_transaction WHERE is_delete = 0 AND ref_type = 3 GROUP BY batch_no,ref_id) as st'] = "st.ref_id = job_card.id";
        $data['leftJoin']['mir_transaction'] = "mir_transaction.batch_no = st.batch_no AND mir_transaction.item_id = st.item_id";
		$data['where']['ic_inspection.mir_id'] = $postData['job_card_id'];    
		$data['where']['ic_inspection.mir_trans_id'] = $postData['process_id'];    
		if(!empty($postData['machine_id'])){
            $data['where']['ic_inspection.party_id'] = $postData['machine_id'];
        }   
		$data['where']['ic_inspection.item_id'] = $postData['item_id'];    
        if(!empty($postData['trans_date'])){$data['where']['ic_inspection.trans_date'] = $postData['trans_date'];}
        if(isset($postData['singleRow']) && $postData['singleRow']==1){
            return $this->row($data);
        }else{
		    return $this->rows($data);
        }
    }
}
