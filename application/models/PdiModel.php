<?php
class PdiModel extends MasterModel{
    private $pdi_master = "pdi_master";
    private $pdi_transaction = "pdi_transaction";

    public function nextTransNo(){
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['tableName'] = $this->pdi_master;
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo;
    }

    public function getDTRows($data){
        $data['tableName'] = $this->pdi_master;
        $data['select'] = "pdi_master.*,party_master.party_name,item_master.item_code,item_master.item_name";
        $data['leftJoin']['party_master'] = "party_master.id = pdi_master.party_id";
        $data['leftJoin']['item_master'] = "item_master.id = pdi_master.item_id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "trans_number";
        $data['searchCol'][] = "DATE_FORMAT(pdi_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "lot_qty";

		$columns = array('','','trans_number','trans_date','party_master.party_name','item_master.item_name','lot_qty');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getPdiMasterData($id){
        $data['tableName'] = $this->pdi_master;
        $data['select'] = "pdi_master.*,party_master.party_name,item_master.part_no,item_master.item_code,item_master.item_name,mt.item_name as material_name,employee_master.emp_name as inspected_by,emp.emp_name as approved_by,tc1.emp_name as tech_name1,tc2.emp_name as tech_name2,ic1.emp_name as in_charge_name1,ic2.emp_name as in_charge_name2";
        $data['leftJoin']['party_master'] = "party_master.id = pdi_master.party_id";
        $data['leftJoin']['item_master'] = "item_master.id = pdi_master.item_id";
        $data['leftJoin']['item_master as mt'] = "mt.id = pdi_master.material";
        $data['leftJoin']['employee_master'] = 'employee_master.id = pdi_master.insp_by';
        $data['leftJoin']['employee_master as emp'] = 'emp.id = pdi_master.app_by';
        $data['leftJoin']['employee_master as tc1'] = 'tc1.id = pdi_master.tech1';
        $data['leftJoin']['employee_master as tc2'] = 'tc2.id = pdi_master.tech2';
        $data['leftJoin']['employee_master as ic1'] = 'ic1.id = pdi_master.in_charge1';
        $data['leftJoin']['employee_master as ic2'] = 'ic2.id = pdi_master.in_charge2';
        $data['where']['pdi_master.id'] = $id;
		$result = $this->row($data);		
		$result->itemData = $this->getPdiTransData($id);
		return $result;
    }

    public function getPdiTransData($id){
        $data['tableName'] = $this->pdi_transaction;
        $data['select'] = "pdi_transaction.*,pfc_trans.parameter,pfc_trans.requirement,pfc_trans.min_req,pfc_trans.max_req,pfc_trans.other_req,qc_fmea.instrument_code";
        $data['leftJoin']['pfc_trans'] = "pfc_trans.id = pdi_transaction.param_id";
        $data['leftJoin']['qc_fmea'] = 'qc_fmea.ref_id = pfc_trans.id';
        $data['where']['pdi_transaction.pdi_id'] = $id;
        $data['group_by'][] = "pdi_transaction.id";
		$result = $this->rows($data);
		return $result;
    }

    public function save($masterData,$itemData){
        try{
            $this->db->trans_begin();

            $mainId = $masterData['id'];
    		
    		if($this->checkDuplicate($masterData['party_id'],$masterData['trans_no'],$mainId) > 0):
    			$errorMessage['trans_no'] = "Report No. is duplicate.";
    			return ['status'=>0,'message'=>$errorMessage];
    		endif;

            if(empty($mainId)):
                $pdiMasterSave = $this->store($this->pdi_master,$masterData);
    			$mainId = $pdiMasterSave['insert_id'];	
    
    			$result = ['status'=>1,'message'=>'PDI saved successfully.','url'=>base_url("pdi")];			
    		else:
    			$this->store($this->pdi_master,$masterData);    			
    			$result = ['status'=>1,'message'=>'PDI updated successfully.','url'=>base_url("pdi")];
    		endif;

            foreach($itemData['param_id'] as $key=>$value):
                $transData = [
                    'id' => $itemData['id'][$key],
                    'pdi_id' => $mainId,
                    'param_id' => $value,
                    'sample_1' => $itemData['sample_1'][$key],
                    'sample_2' => $itemData['sample_2'][$key],
                    'sample_3' => $itemData['sample_3'][$key],
                    'sample_4' => $itemData['sample_4'][$key],
                    'sample_5' => $itemData['sample_5'][$key],
                    'remark' => $itemData['remark'][$key],
                    'created_by' => $itemData['created_by']
                ];
                $this->store($this->pdi_transaction,$transData);
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;        
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }		
	}

    public function checkDuplicate($party_id,$trans_no,$id = ""){
		$data['tableName'] = $this->pdi_master;
		$data['where']['party_id'] = $party_id;
		$data['where']['trans_no']  = $trans_no;
		$data['where']['trans_date >= '] = $this->startYearDate;
        $data['where']['trans_date <= '] = $this->endYearDate;
		if(!empty($id))
			$data['where']['id != '] = $id;		
		return $this->numRows($data);
    }

    public function delete($id){
		try{
            $this->db->trans_begin();

            $this->trash($this->pdi_transaction,['pdi_id'=>$id]);
            $result = $this->trash($this->pdi_master,['id'=>$id],'PDI');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }
	
}