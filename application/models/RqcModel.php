<?php
class RqcModel extends MasterModel
{
    private $icInspection = "ic_inspection";
    private $job_approval ="job_approval";
    private $job_transaction ="job_transaction";

   
    public function getDTRows($data)
    {
        $data['tableName'] = $this->job_transaction;
        $data['select'] = "job_transaction.*,job_card.job_number,job_card.product_id,item_master.full_name,process_master.process_name,crnt_approval.id as jobApprovalId,crnt_approval.in_process_id";
        $data['leftJoin']['job_card '] ='job_card.id = job_transaction.job_card_id';
        $data['leftJoin']['job_approval'] ='job_approval.id = job_transaction.job_approval_id';
        $data['leftJoin']['job_approval as crnt_approval'] ='crnt_approval.in_process_id = job_approval.out_process_id AND crnt_approval.job_card_id = job_approval.job_card_id';
        $data['leftJoin']['process_master'] ='crnt_approval.in_process_id = process_master.id';
        $data['leftJoin']['item_master '] ='item_master.id = job_card.product_id';
        // $data['where']['job_card.order_status'] = 2;
        $data['where']['crnt_approval.stage_type'] = 7;
        $data['where']['job_transaction.entry_type'] = 6;
        $data['having'][] = '(job_transaction.qty-job_transaction.total_weight) > 0';

 
        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "job_approval.inward_qty";
        $data['searchCol'][] = "job_approval.in_qty";

        $columns = array('', '', 'job_card.job_number', 'item_master.full_name', 'job_approval.inward_qty', 'job_approval.in_qty', '');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        // print_r($this->db->last_query());exit;
        return $result;
    }

    public function saveInward($data){
        try {
            $this->db->trans_begin();
            $setData = array();
            $setData['tableName'] = $this->job_approval;
            $setData['where']['id'] = $data['job_approval_id'];
            $setData['set']['in_qty'] = 'in_qty, + '.$data['lot_qty'];
            $result = $this->setValue($setData);
            
            $setData = array();
            $setData['tableName'] = $this->job_transaction;
            $setData['where']['id'] = $data['mir_trans_id'];
            $setData['set']['total_weight'] = 'total_weight, + '.$data['lot_qty'];
            $result = $this->setValue($setData);

            $pfcProcess = $this->item->getPrdProcessDataProductProcessWise(['item_id' =>$data['item_id'], 'process_id' => $data['process_id']]);
            $insParamData =  $this->controlPlan->getCPDimenstion(['item_id'=>$data['item_id'],'stage_type'=>7,'pfc_id'=>$pfcProcess->pfc_process,'control_method'=>'RQC']);
            unset($data['process_id']);
            if(!empty($insParamData)){
                $sample = $this->reactionPlan->getSampleSize($data['lot_qty'],'RQC');
                $data['sampling_qty'] = $sampleSize = $sample->sample_size;
                $insp = Array();$param_ids = Array();$data['observation_sample'] = '';
                if(!empty($insParamData)):
                    foreach($insParamData as $row):
                        for($j = 1; $j <= $sampleSize; $j++):
                            $param[] = '';
                        endfor;
                        $param[] = '';
                        $insp[$row->id] = $param;
                        $param_ids[] = $row->id;
                    endforeach;
                endif;
                $data['parameter_ids'] = implode(',',$param_ids);
                $data['observation_sample'] = json_encode($insp);
                $data['param_count'] = count($insParamData);
                $result = $this->store($this->icInspection,$data);
            }else{
                return ['status' => 2, 'message' => "Control Plan is required.." ];
            }
        //    print_r($data);exit;
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }


    public function getRQCDTRows($data)
    {
        $data['tableName'] = $this->icInspection;
        $data['select'] = "ic_inspection.*,item_master.item_name,item_master.full_name,item_master.item_code,employee_master.emp_name,job_card.job_number,job_card.order_status,job_card.process,process_master.process_name,job_approval.ok_qty,job_approval.out_process_id,job_approval.job_card_id";

        $data['leftJoin']['employee_master'] = "ic_inspection.created_by = employee_master.id";
        $data['leftJoin']['job_card'] = "job_card.id = ic_inspection.mir_id";
        $data['leftJoin']['item_master'] = "item_master.id = ic_inspection.item_id";
        $data['leftJoin']['process_master'] = "process_master.id = ic_inspection.party_id";
        $data['leftJoin']['job_approval'] = "job_approval.id = ic_inspection.job_approval_id";
        $data['where']['ic_inspection.trans_type'] = 4;
        if($data['status'] == 0){ $data['where_in']['ic_inspection.status'] = '0,1';}
        if($data['status'] == 1){ $data['where']['ic_inspection.status'] = 2;}
        $data['order_by']['ic_inspection.created_at'] = "DESC";
        $data['order_by']['ic_inspection.id'] = "DESC";

        $data['searchCol'][] = "DATE_FORMAT(ic_inspection.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "ic_inspection.trans_no";
        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "ic_inspection.lot_qty";

        $columns = array('', '', 'ic_inspection.trans_date', 'job_card.job_number', 'job_card.job_number', 'item_master.full_name', 'ic_inspection.lot_qty');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function getRQCReport($postData){
        $data['tableName'] = $this->icInspection;
        $data['select'] = "ic_inspection.*,item_master.item_name,item_master.full_name,item_master.item_code,job_card.job_number,job_card.process,item_master.part_no";
        $data['leftJoin']['job_card'] = "job_card.id = ic_inspection.mir_id";
        $data['leftJoin']['item_master'] = "item_master.id = ic_inspection.item_id";
		$data['where']['ic_inspection.id'] = $postData['id'];     
        return $this->row($data);

    }

    public function save($data){
        try {
            $this->db->trans_begin();
            $result = $this->store($this->icInspection,$data);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
}
