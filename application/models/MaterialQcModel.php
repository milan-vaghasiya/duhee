<?php
class MaterialQcModel extends MasterModel{
    private $mqsParam = "mqs_param";
    private $familyGroup = "family_group";
    private $jobCard = "job_card";
    private $materialQc ="material_qc";

    public function getDTRows($data){        
        $data['tableName'] = $this->jobCard;
        $data['select'] = "SUM(job_approval.ok_qty) AS total_ok_qty, `item_master`.`full_name`, `job_card`.`product_id`, `rm`.`item_code` AS `rm_code`, GROUP_CONCAT( DISTINCT material_qc.trans_number ) AS report_no";
        $data['leftJoin']['job_approval']= "job_approval.job_card_id = job_card.id AND job_approval.out_process_id = 0";
        $data['leftJoin']['material_qc']= "material_qc.item_id = job_card.product_id";
        $data['join']['item_master'] = "item_master.id = job_card.product_id";
        $data['join']['item_kit'] = "item_master.id = item_kit.item_id";
        $data['join']['item_master rm'] = "rm.id = item_kit.ref_item_id AND rm.item_type= 3";
        $data['where']['job_card.job_date >= '] = $this->startYearDate;
        $data['where']['job_card.job_date <= '] = $this->endYearDate;
        if(empty($data['status'])){
            $data['customWhere'][] ='job_card.product_id NOT IN(SELECT mqc.item_id FROM material_qc as mqc WHERE mqc.trans_date BETWEEN "'.$this->startYearDate.'" AND "'.$this->endYearDate.'")'; 
        }else{
            $data['having'][] = "GROUP_CONCAT( DISTINCT material_qc.trans_number ) != ''";
        }
        $data['group_by'][] = "job_card.product_id";
        $data['having'][] = "SUM(job_approval.ok_qty) > 0";
     
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "rm.rm_code";
        $data['searchCol'][] = "job_approval.total_ok_qty";
        $data['searchCol'][] = "material_qc.report_no";

		$columns =array('','','item_master.full_name','rm.rm_code','job_approval.total_ok_qty');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
    }

    public function getProductRMDetail($item_id){
        $data['tableName'] = 'item_kit';
        $data['select']="item_master.item_code,item_master.material_grade";
        $data['leftJoin']['item_master'] = "item_master.id = item_kit.ref_item_id";
        $data['where']['item_kit.item_id'] = $item_id;
        $data['where']['item_master.item_type'] = 3;
        return $this->row($data);
    }

    public function getNextTransNo(){
        $data['tableName'] = $this->materialQc;
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['trans_date >= '] = $this->startYearDate;
        $data['where']['trans_date <= '] = $this->endYearDate;
        $maxNo = $this->specificRow($data)->trans_no;
        $nextTransNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextTransNo;
    }

    public function save($data){
        try {
            $this->db->trans_begin();
            $result = $this->store($this->materialQc,$data);
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}	
    }

    public function getReportData($reportNo,$item_id){
        $data['tableName'] = $this->materialQc;
        $data['select'] = "material_qc.*,item_master.item_code,item_master.full_name,item_master.part_no,employee_master.emp_name,family_group.family_name as process_name";
        $data['leftJoin']['item_master']="item_master.id = material_qc.item_id";
        $data['leftJoin']['employee_master']="employee_master.id = material_qc.created_by";
        $data['leftJoin']['family_group'] = "family_group.id = material_qc.process_id";
        $data['where']['material_qc.trans_number'] = $reportNo;
        $result = $this->row($data);
        return $result;
    
    }
}
?>