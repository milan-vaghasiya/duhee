<?php
class SarModel extends MasterModel{
    private $sar = "sar";
    private $processMaster = "process_master";

    public function getDTRows($data){
        $data['tableName'] = $this->sar;
        $data['select'] = "sar.*,job_card.job_number,process_master.process_name,item_master.item_code,item_master.item_name,employee_master.emp_name";
        $data['leftJoin']['job_card'] = "job_card.id = sar.job_card_id";
        $data['leftJoin']['process_master'] = "process_master.id = sar.process_id";
        $data['leftJoin']['item_master'] = "item_master.id = sar.machine_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = sar.setter_id";

        if(!empty($data['status'])) { $data['where']['sar.status'] = $data['status']; }
        else { $data['where']['sar.status'] = 0; }

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(sar.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "setting_time";
        $data['searchCol'][] = "sar.remark";

		$columns =array('','','sar.trans_date','job_card.job_number','process_master.process_name','CONCAT("[",item_master.item_code,"] ",item_master.item_name)','employee_master.emp_name','setting_time','sar.remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getSarDetails($id){
        $data['tableName'] = $this->sar;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function getJobcardProcessList($data){
        $data['tableName'] = $this->processMaster;
        $data['select'] = "process_master.*,job_card.process";
        $data['leftJoin']['job_card'] = "FIND_IN_SET(process_master.id,job_card.process) > 0";
        $data['where']['job_card.id'] = $data['job_card_id'];
        $data['where']['process_type'] = 0;
        return $this->rows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();            

            $result = $this->store($this->sar,$data);
			$result['url'] = base_url("sar");

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
	}

    public function delete($id){
        try{
            $this->db->trans_begin(); 
            
            $result = $this->trash($this->sar,['id'=>$id]);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getSarDetailsForPrint($id){
        $data['tableName'] = $this->sar;
        $data['select'] = "sar.*,item_master.item_code,item_master.full_name,item_master.part_no,item_master.drawing_no,item_master.rev_no,item_master.material_grade,party_master.party_name,job_card.job_number,job_card.product_id,process_master.process_name,department_master.name,mc.item_code as machine_code,st.batch_no as heat_no,employee_master.emp_name as insp_by,ap.emp_name as approve_by,product_process.cycle_time";        
        $data['leftJoin']['job_card'] = "job_card.id = sar.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = job_card.product_id";
        $data['leftJoin']['party_master'] = "party_master.id = job_card.party_id";
        $data['leftJoin']['item_master as mc'] = "mc.id = sar.machine_id";
        $data['leftJoin']['process_master'] = "process_master.id = sar.process_id";
        $data['leftJoin']['department_master'] = "department_master.id = process_master.dept_id";
        $data['leftJoin']['(SELECT batch_no,ref_id FROM stock_transaction WHERE is_delete = 0 AND ref_type = 3 GROUP BY batch_no,ref_id) as st'] = "st.ref_id = job_card.id";  
        $data['leftJoin']['employee_master'] = "sar.created_by = employee_master.id";
        $data['leftJoin']['employee_master ap'] = "sar.approve_by = ap.id";
        $data['leftJoin']['product_process'] = "product_process.item_id = job_card.product_id AND product_process.process_id = sar.process_id AND product_process.is_delete = 0";
        $data['where']['sar.id'] = $id;
        return $this->row($data);
    }
}
?>