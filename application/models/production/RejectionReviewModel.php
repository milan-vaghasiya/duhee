<?php
class RejectionReviewModel extends MasterModel
{
    private $rejRWManage = "rej_rw_management";
    private $qc_fmea = "qc_fmea";
    private $jobTrans = "job_transaction";
    private $jobApproval = "job_approval";
    private $jobCard = "job_card";
    private $fg_stock_trans = "fg_stock_trans";

    public function getDTRows($data)
    {
        $data['tableName'] = $this->rejRWManage;
        $data['select'] = "rej_rw_management.*,(rej_rw_management.qty-rej_rw_management.cft_qty) as pending_qty,process_master.process_name,itm.item_code as product_code,itm.item_name as product_name,item_master.item_name,item_master.item_code,employee_master.emp_name,job_card.job_no,job_card.job_prefix,job_card.job_number,rejection_comment.remark as rejection_reason";
        $data['leftJoin']['job_card'] = "job_card.id = rej_rw_management.job_card_id";
        $data['leftJoin']['job_transaction'] = "job_transaction.id = rej_rw_management.job_trans_id";
        $data['leftJoin']['item_master'] = "item_master.id = job_transaction.machine_id";
        $data['leftJoin']['item_master itm'] = "itm.id = job_card.product_id";
        $data['leftJoin']['process_master'] = "process_master.id = job_transaction.process_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = job_transaction.operator_id";
        $data['leftJoin']['rejection_comment'] = "rejection_comment.id = rej_rw_management.rr_reason";
      
        $data['where']['job_card.job_date >= '] = $this->startYearDate;
        $data['where']['job_card.job_date <= '] = $this->endYearDate;
        if($data['entry_type'] == 1){ $data['customWhere'][] = '(rej_rw_management.entry_type = 1 OR (rej_rw_management.ref_type = 2 AND rej_rw_management.entry_type != 3)) AND (rej_rw_management.qty - rej_rw_management.cft_qty) > 0'; }
        else{$data['where_in']['rej_rw_management.entry_type'] = $data['entry_type']; }

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "itm.item_code";
        $data['searchCol'][] = "DATE_FORMAT(rej_rw_management.entry_date,'%d-%m-%Y')";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "employee_master.emp_name";
		$data['searchCol'][] = "rej_rw_management.qty";
		$data['searchCol'][] = "";
		$data['searchCol'][] = "";
		$data['searchCol'][] = "";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}

        return $this->pagingRows($data);
    }

    public function getRejMovementData($param = array()){
        $queryData['tableName'] = $this->rejRWManage;
        $queryData['select'] = "rej_rw_management.*,job_transaction.process_id,job_card.process,job_card.product_id,job_transaction.job_approval_id,job_card.job_no,job_card.job_prefix,job_card.job_number,job_card.process,item_master.item_code,item_master.item_name,job_transaction.entry_type as job_entry_type";
        $queryData['leftJoin']['job_card'] = "job_card.id = rej_rw_management.job_card_id";
        $queryData['leftJoin']['job_transaction'] = "job_transaction.id = rej_rw_management.job_trans_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = job_card.product_id";
        if(!empty($param['id'])){ $queryData['where']['rej_rw_management.id'] = $param['id']; }
        return $this->row($queryData);
    }

    public function getNextTagNo($entry_type, $operation_type){
        $data['tableName'] = $this->rejRWManage;
        $data['select'] = "MAX(tag_no) as tag_no";
        $data['where']['entry_type'] = $entry_type;
        $data['where']['operation_type'] = $operation_type;
        $maxNo = $this->specificRow($data)->tag_no;
        $nextTagNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextTagNo;
    }

    public function saveCFTQty($data){
        try {
            $this->db->trans_begin();
            if($data['entry_type']==2){
                $data['tag_no'] = $this->getNextTagNo($data['entry_type'], $data['operation_type']);
                $data['tag_prefix'] = (($data['operation_type'] == 1) ? 'RJ/' : (($data['operation_type'] == 2) ? 'RW/' : 'OK/') ). n2y(date('Y'));
            }
            /** Review Entry */
            $result = $this->store($this->rejRWManage,$data);

            /* Update Inspected Qty */
            $setData = array();
            $setData['tableName'] = $this->rejRWManage;
            $setData['where']['id'] = $data['ref_id'];
            $setData['set']['cft_qty'] = 'cft_qty, + ' . $data['qty'];
            $this->setValue($setData);


            $cftData = $this->rejReview->getRejMovementData(['id'=>$data['ref_id']]); // Operator Entry Data
            $jobProcesses = explode(",", $cftData->process);
            $aprvData = $this->processMovement->getApprovalData(['id'=>$cftData->job_approval_id]);

            /** Reverse Qty Operator entered */
            $setData = array();
            $setData['tableName'] = $this->jobApproval;
            $setData['where']['id'] = $cftData->job_approval_id;
            if ($cftData->operation_type == 1) { $setData['set']['total_rejection_qty'] = 'total_rejection_qty, - ' . $data['qty']; }  /** If First Dicision is rejection */
            elseif ($cftData->operation_type == 2) {$setData['set']['total_rework_qty'] = 'total_rework_qty, - ' . $data['qty']; } /** If First Dicision is Rework */
            elseif ($cftData->operation_type == 3) { $setData['set']['total_hold_qty'] = 'total_hold_qty, - ' . $data['qty']; }/** If First Dicision is Hold */
            $this->setValue($setData);

            /** Job Transaction Entry */
            $jobTransData = [
                'id' => '',
                'entry_date' => $data['entry_date'],
                'entry_type' => ($data['operation_type'] == 4 || $data['operation_type'] == 5) ? 0 : $data['operation_type'],
                'ref_id' => $data['job_trans_id'],
                'rej_rw_manag_id' => $result['insert_id'],
                'job_card_id' => $data['job_card_id'],
                'job_approval_id' => $aprvData->id,
                'process_id' => $aprvData->in_process_id,
                'product_id' => $aprvData->product_id,
                'rr_stage' => (!empty($data['rr_stage']) ? $data['rr_stage'] : ''),
                'rr_reason' => (!empty($data['rr_reason']) ? $data['rr_reason'] : ''),
                'rr_by' => (!empty($data['rr_by']) ? $data['rr_by'] : ''),
                'qty' => $data['qty'],
                'created_by' => $this->session->userdata('loginId')
            ];
            $this->store($this->jobTrans, $jobTransData);
            /** IF OK Qty  */
            if($data['operation_type'] == 4 || $data['operation_type'] == 5){
                $setData = array();
                $setData['tableName'] = $this->jobApproval;
                $setData['where']['id'] = $cftData->job_approval_id;
                $setData['set']['ok_qty'] = 'ok_qty, + ' . $data['qty'];
                $this->setValue($setData);

                if (empty($aprvData->out_process_id)) :
                    $setData = array();
                    $setData['tableName'] = $this->jobCard;
                    $setData['where']['id'] = $cftData->job_card_id;
                    $setData['set']['unstored_qty'] = 'unstored_qty, + ' . $data['qty'];
                    $this->setValue($setData);
                endif;
            }
            elseif($data['operation_type'] == 1){ 
                /** IF Rejected Qty */
                $setData = array();
                $setData['tableName'] = $this->jobApproval;
                $setData['where']['id'] = $cftData->job_approval_id;
                $setData['set']['total_rejection_qty'] = 'total_rejection_qty, + ' . $data['qty'];
                $this->setValue($setData);   
                
                $setData = array();
                $setData['tableName'] = $this->jobCard;
                $setData['where']['id'] = $cftData->job_card_id;
                $setData['set']['total_rej_qty'] = 'total_rej_qty, + ' . $data['qty'];
                $this->setValue($setData);  
            }
            elseif ($data['operation_type'] == 2) {
                /** If Rework Qty */
                $processIds = explode(",", $data['rw_process_id']);
                $counter = count($processIds);
                for ($i = 0; $i < $counter; $i++) :
                    $approvalData = [
                        'id' => "",
                        'entry_date' => date("Y-m-d"),
                        'trans_type' => 2,
                        'ref_id' => $result['insert_id'],
                        'process_ref_id' => $cftData->process_id,
                        'job_card_id' => $aprvData->job_card_id,
                        'product_id' => $aprvData->product_id,
                        'in_process_id' => $processIds[$i],
                        'inward_qty' => ($i == 0) ? $data['qty'] : 0,
                        'out_process_id' => (isset($processIds[$i + 1])) ? $processIds[$i + 1] : $cftData->process_id,
                        'created_by' => $data['created_by']
                    ];
                    $this->store($this->jobApproval, $approvalData);
                endfor;
                $setData = array();
                $setData['tableName'] = $this->jobApproval;
                $setData['where']['id'] = $cftData->job_approval_id;
                $setData['set']['in_qty'] = 'in_qty, - ' . $data['qty'];
                $setData['set']['total_prod_qty'] = 'total_prod_qty, - ' . $data['qty'];
                if($cftData->job_entry_type == 4){
                    $setData['set']['outward_qty'] = 'outward_qty, - ' . $data['qty'];
                    $setData['set']['v_prod_qty'] = 'v_prod_qty, - ' . $data['qty'];
                }else{
                    $setData['set']['inward_qty'] = 'inward_qty, - ' . $data['qty'];
                    $setData['set']['ih_prod_qty'] = 'ih_prod_qty, - ' . $data['qty'];
                }
                $this->setValue($setData);
            }

            $jobCardData = $this->jobcard->getJobcardData(['id'=>$cftData->job_card_id]);
            $totalQty = $jobCardData->total_rej_qty + $jobCardData->total_ok_qty + $jobCardData->semi_finish_qty + $jobCardData->convert_qty;
            if ($totalQty >= $jobCardData->qty):
                $this->store($this->jobCard, ['id' => $jobCardData->id, 'order_status' => 3]);
            endif;

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function saveConvertedItem($data){
        try {
            $this->db->trans_begin();

            $jobData = $this->jobcard->getJobcardData(['id'=>$data['job_id']]);          
            $bomData = $this->jobcard->getJobBomRawMaterialData($data['job_id']);
            $next_sr_no = $this->processMovement->getNextBatchNo();
            $batchNo = n2y(date("Y")).n2m(date("m")).sprintf("%02d",$next_sr_no);

            $stockTrans = [
                'id' => '',
                'entry_type' => 5,
                'p_or_m' => 1,
                'ref_date' => date("Y-m-d"),
                'item_id' => $data['item_id'],
                'qty' => $data['qty'],
                'main_ref_id' => $data['job_id'],
                'batch_no' => $batchNo,
                'sr_no' => $next_sr_no,
                'ref_no' => $jobData->job_number,
                'location_id' => $this->RTD_STORE->id,
                'party_id' => $bomData->supplied_id,
                'created_by' => $data['created_by'],
                'created_at' => date("Y-m-d H:i:s"),
            ];
            $result = $this->store($this->fg_stock_trans, $stockTrans);

            $setData = array();
            $setData['tableName'] = $this->rejRWManage;
            $setData['where']['id'] = $data['ref_id'];
            $setData['set']['cft_qty'] = 'cft_qty, + ' . $data['qty'];
            $this->setValue($setData);

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
