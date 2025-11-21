<?php
class FinalCFTModel extends MasterModel
{
    private $rejRWManage = "rej_rw_management";
    private $qc_fmea = "qc_fmea";
    private $jobTrans = "job_transaction";
    private $jobApproval = "job_approval";
    private $jobCard = "job_card";
    private $fir_master = "fir_master";
    private $fir_dimension = "fir_dimension";
    private $job_heat_trans = "job_heat_trans";

    public function getDTRows($data)
    {
        $data['tableName'] = $this->rejRWManage;
        $data['select'] = "rej_rw_management.*,(rej_rw_management.qty-rej_rw_management.cft_qty) as pending_qty,process_master.process_name,item_master.item_name,itm.item_code as product_name,itm.full_name,item_master.item_code,employee_master.emp_name,job_card.job_no,job_card.job_prefix,job_card.job_number,rejection_comment.remark as rejection_reason,rejection_comment.code as reason_code,pfc_trans.parameter,party_master.party_name,pcft.tag_no as pcft_tag_no,pcft.tag_prefix as pcft_tag_prefix,job_approval.stage_type,job_transaction.mfg_by";
        $data['leftJoin']['job_card'] = "job_card.id = rej_rw_management.job_card_id";
        $data['leftJoin']['job_transaction'] = "job_transaction.id = rej_rw_management.job_trans_id";
        $data['leftJoin']['rejection_comment'] = "rejection_comment.id = rej_rw_management.rr_reason";
        $data['leftJoin']['pfc_trans'] = "pfc_trans.id = rej_rw_management.rr_stage";
        $data['leftJoin']['party_master'] = "party_master.id = rej_rw_management.rr_by";
        $data['leftJoin']['item_master'] = "item_master.id = job_transaction.machine_id";
        $data['leftJoin']['item_master itm'] = "itm.id = job_card.product_id";
        $data['leftJoin']['process_master'] = "process_master.id = job_transaction.process_id";
        $data['leftJoin']['job_approval'] = "job_approval.in_process_id = job_transaction.process_id AND job_approval.job_card_id = job_transaction.job_card_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = job_transaction.operator_id";
        $data['leftJoin']['rej_rw_management as pcft'] = "pcft.id = rej_rw_management.ref_id AND rej_rw_management.ref_type = 2";
        if($data['entry_type'] ==2){ $data['customWhere'][] = '(rej_rw_management.entry_type = 2  OR rej_rw_management.ref_type=3)     AND rej_rw_management.operation_type != 5   AND( rej_rw_management.qty - rej_rw_management.cft_qty) > 0'; }
        else{$data['where_in']['rej_rw_management.entry_type'] = $data['entry_type']; } 
        $data['where']['job_card.job_date >= '] = $this->startYearDate;
        $data['where']['job_card.job_date <= '] = $this->endYearDate;
        $data['customWhere'][] = '( rej_rw_management.qty - rej_rw_management.cft_qty) > 0';

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "itm.item_code";
        $data['searchCol'][] = "DATE_FORMAT(rej_rw_management.entry_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT(pcft.tag_prefix,pcft.tag_no)";
        $data['searchCol'][] = "CONCAT(rej_rw_management.tag_prefix,rej_rw_management.tag_no)";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "rej_rw_management.qty";
        $data['searchCol'][] = "rej_rw_management.qty-rej_rw_management.cft_qty";
        $data['searchCol'][] = "rejection_comment.remark";
        $data['searchCol'][] = "pfc_trans.parameter";
        $data['searchCol'][] = "party_master.party_name";

        $columns = array('', '', 'job_card.job_no', 'itm.item_code', 'rej_rw_management.entry_date','pcft.tag_no','rej_rw_management.tag_no', 'process_master.process_name', 'item_master.item_code', 'employee_master.emp_name', 'rej_rw_management.qty', 'rej_rw_management.qty', 'rejection_comment.remark', 'pfc_trans.parameter', 'party_master.party_name');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        } else {
            $data['order_by']['rej_rw_management.entry_date'] = 'DESC';
            $data['order_by']['rej_rw_management.id'] = 'DESC';
        }
        return  $this->pagingRows($data); //print_r($this->db->last_query());exit;
    }

    public function getRejMovementData($id)
    {
        $queryData['tableName'] = $this->rejRWManage;
        $queryData['select'] = "rej_rw_management.*,job_transaction.process_id,job_card.product_id,job_transaction.job_approval_id,job_transaction.vendor_id,job_card.job_no,job_card.job_prefix,job_card.job_number,job_card.job_category,job_transaction.mfg_by";
        $queryData['leftJoin']['job_card'] = "job_card.id = rej_rw_management.job_card_id";
        $queryData['leftJoin']['job_transaction'] = "job_transaction.id = rej_rw_management.job_trans_id";
        $queryData['where_in']['rej_rw_management.id'] = $id;
        return $this->row($queryData);
    }

    public function getNextTagNo($entry_type, $operation_type)
    {
        $data['tableName'] = $this->rejRWManage;
        $data['select'] = "MAX(tag_no) as tag_no";
        $data['where']['entry_type'] = $entry_type;
        $data['where']['operation_type'] = $operation_type;
        $maxNo = $this->specificRow($data)->tag_no;
        $nextTagNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextTagNo;
    }
    
    public function saveCFTQty($data)
    {
        try {
            $this->db->trans_begin();

            $data['tag_no'] = $this->getNextTagNo($data['entry_type'], $data['operation_type']);
            $data['tag_prefix'] ='F'.(($data['operation_type'] == 1) ? 'RJ/' : (($data['operation_type'] == 2) ? 'RW/' : 'OK/') ). n2y(date('Y'));
            $result = $this->store($this->rejRWManage, $data);

            $setData = array();
            $setData['tableName'] = $this->rejRWManage;
            $setData['where']['id'] = $data['ref_id'];
            $setData['set']['cft_qty'] = 'cft_qty, + ' . $data['qty'];
            $this->setValue($setData);

            $primaryCftData=new stdClass();
            if($data['ref_type'] == 4){
                $udCftData = $this->primaryCFT->getRejMovementData($data['ref_id']);// UD Data
                $finalCftData = $this->primaryCFT->getRejMovementData($udCftData->ref_id); //Final CFT Data
                $primaryCftData = $this->primaryCFT->getRejMovementData($finalCftData->ref_id); //Primary CFT Data
            }else{
                $cftData = $this->primaryCFT->getRejMovementData($data['ref_id']); // primary
                if($cftData->ref_type == 4){ 
                    $udCftData = $this->primaryCFT->getRejMovementData($cftData->ref_id);  // primary UD 
                    $primaryCftData = $this->primaryCFT->getRejMovementData($udCftData->ref_id); } // primary
                else{ $primaryCftData=$cftData; }
            }

            $jobTransData = [
                'id' => '',
                'entry_date' => $data['entry_date'],
                'entry_type' => ($data['operation_type'] == 4) ? 0 : $data['operation_type'],
                'ref_id' => $data['job_trans_id'],
                'mfg_by' => $primaryCftData->mfg_by,
                'rej_rw_manag_id' => $result['insert_id'],
                'job_card_id' => $data['job_card_id'],
                'job_approval_id' => $primaryCftData->job_approval_id,
                'process_id' => $primaryCftData->process_id,
                'product_id' => $primaryCftData->product_id,
                'rr_stage' => (!empty($data['rr_stage']) ? $data['rr_stage'] : ''),
                'rr_reason' => (!empty($data['rr_reason']) ? $data['rr_reason'] : ''),
                'rr_by' => (!empty($data['rr_by']) ? $data['rr_by'] : ''),
                'operator_id' => $primaryCftData->operator_id,
                'qty' => $data['qty'],
                'created_by' => $this->session->userdata('loginId')
            ];
            $this->store($this->jobTrans, $jobTransData);

            $jobData = $this->jobcard->getJobcard($primaryCftData->job_card_id);
            $jobProcesses = explode(",", $jobData->process);
            $aprvData = $this->processMovement->getApprovalData($primaryCftData->job_approval_id);
            $opCftData = $this->primaryCFT->getRejMovementData($primaryCftData->ref_id); // Operator CFT
            /*** Update out qty & rejection qty in current process ***/
            // print_r($data);exit;
           if( ($data['entry_type'] == 3 && $data['operation_type'] != 5) || $data['operation_type'] == 4){
                $setData = array();
                $setData['tableName'] = $this->jobApproval;
                $setData['where']['id'] = $primaryCftData->job_approval_id;
                $valid=0;
                if ($opCftData->operation_type == 1) {
                    $valid=1;
                    $setData['set']['total_rejection_qty'] = 'total_rejection_qty, - ' . $data['qty'];
                } elseif ($opCftData->operation_type == 2 && $data['operation_type'] != 2) {
                    $valid=1;
                    $setData['set']['total_rework_qty'] = 'total_rework_qty, - ' . $data['qty'];
                } elseif ($opCftData->operation_type == 3) {
                    $valid=1;
                    $setData['set']['total_hold_qty'] = 'total_hold_qty, - ' . $data['qty'];
                }
                if($valid==1){
                    $this->setValue($setData);
                    // print_r($this->db->last_query());
                }
            }
            
            if ($data['operation_type'] == 4) {
                $setData = array();
                $setData['tableName'] = $this->jobApproval;
                $setData['where']['id'] = $primaryCftData->job_approval_id;
               
                $setData['set']['ok_qty'] = 'ok_qty, + ' . $data['qty'];

                $this->setValue($setData);
                
                /*** Update Job Heat Table */
                $setData = array();
                $setData['tableName'] = $this->job_heat_trans;
                $setData['where']['job_approval_id'] =$cftData->job_approval_id;
                $setData['where']['batch_no'] = $cftData->batch_no;
                $setData['set']['rej_rw_qty'] = 'rej_rw_qty, - ' . $data['qty'];
                $this->setValue($setData);
                
                /** If Rejection Ok from final ispection */
                if(!empty($aprvData->stage_type) && $aprvData->stage_type == 3){
                    $jbtQuery['tableName'] =$this->jobTrans;
                    $jbtQuery['select']="job_transaction.rej_rw_manag_id,job_transaction.ref_id"; // rej_rw_manag_id = fir_dimension id , ref_id = fir_master id
                    $jbtQuery['where']['job_transaction.entry_type'] = 8;
                    $jbtQuery['where']['job_transaction.id'] = $cftData->job_trans_id;
                    $jbtData = $this->row($jbtQuery);

                    /** Minus Qty From FIR Master */
                    $setData = array();
                    $setData['tableName'] = $this->fir_master;
                    $setData['where']['id'] = $jbtData->ref_id;
                    // $setData['set']['qty'] = 'qty, - ' . $data['qty'];
                    if ($cftData->operation_type == 1) {
                        $setData['set']['total_rej_qty'] = 'total_rej_qty, - ' . $data['qty'];
                    } elseif ($cftData->operation_type == 2 ) {
                        $setData['set']['total_rw_qty'] = 'total_rw_qty, - ' . $data['qty'];
                    }
                    $this->setValue($setData);

                    $fdData = $this->fir->getFIRDimensionDetail(['id'=>$jbtData->rej_rw_manag_id]);
                    $fdDimension = $this->fir->getDimensionOnSequence(['fir_id'=>$fdData->fir_id,'sequence'=> $fdData->sequence]);
                   
                    foreach($fdDimension as $row){
                        $valid=0;
                        $setData = array();
                        $setData['tableName'] = $this->fir_dimension;
                        $setData['where']['id'] = $row->id;
                        if ($cftData->operation_type == 1 && $row->id == $jbtData->rej_rw_manag_id) {
                            $setData['set']['rej_qty'] = 'rej_qty, - ' . $data['qty'];
                            $valid=1;
                        } elseif ($cftData->operation_type == 2 && $row->id == $jbtData->rej_rw_manag_id ) {
                            $setData['set']['rw_qty'] = 'rw_qty, - ' . $data['qty'];
                            $valid=1;
                        }
                        if($valid){
                            $this->setValue($setData);
                        }
                        
                    }

                }

                /*** If Lst Process then Maintain Unstored Qty ***/

                if ($jobProcesses[count($jobProcesses) - 1] == $aprvData->in_process_id) :
                    $setData = array();
                    $setData['tableName'] = $this->jobCard;
                    $setData['where']['id'] = $primaryCftData->job_card_id;
                    $setData['set']['unstored_qty'] = 'unstored_qty, + ' . $data['qty'];
                    $this->setValue($setData);
                endif;
            }

            if ($data['operation_type'] == 1 && $data['entry_type']==3) {
                $setData = array();
                $setData['tableName'] = $this->jobApproval;
                $setData['where']['id'] = $primaryCftData->job_approval_id;
                $setData['set']['total_rejection_qty'] = 'total_rejection_qty, + ' . $data['qty'];
                $this->setValue($setData);         
                
                // If Rejection from rework in FIR
                if($primaryCftData->job_entry_type == 8 && $opCftData->operation_type == 2){ 
                    $setData = array();
                    $setData['tableName'] = $this->fir_master;
                    $setData['where']['id'] = $primaryCftData->job_ref_id;
                    $setData['set']['total_rej_qty'] = 'total_rej_qty, + ' . $data['qty'];
                    $setData['set']['total_rw_qty'] = 'total_rw_qty, - ' . $data['qty'];
                    $this->setValue($setData);

                    $setData = array();
                    $setData['tableName'] = $this->fir_dimension;
                    $setData['where']['id'] = $primaryCftData->rej_rw_manag_id;
                    $setData['set']['rej_qty'] = 'rej_qty, + ' . $data['qty'];
                    $setData['set']['rw_qty'] = 'rw_qty, - ' . $data['qty'];
                    $this->setValue($setData);
                }
            }

            if ($data['operation_type'] == 2 && $data['entry_type']==3) {
                $processIds = explode(",", $data['rw_process_id']);
                $counter = count($processIds);
                for ($i = 0; $i < $counter; $i++) :
                    $approvalData = [
                        'id' => "",
                        'entry_date' => date("Y-m-d"),
                        'trans_type' => 2,
                        'ref_id' => $result['insert_id'],
                        'process_ref_id' => $primaryCftData->process_id,
                        'job_card_id' => $jobData->id,
                        'product_id' => $jobData->product_id,
                        'in_process_id' => $processIds[$i],
                        //'in_qty' => ($i == 0) ? $data['qty'] : 0,
                        'inward_qty' => ($i == 0) ? $data['qty'] : 0,
                        'out_process_id' => (isset($processIds[$i + 1])) ? $processIds[$i + 1] : $primaryCftData->process_id,
                        'created_by' => $data['created_by']
                    ];
                    $this->store($this->jobApproval, $approvalData);
                endfor;
                $setData = array();
                $setData['tableName'] = $this->jobApproval;
                $setData['where']['id'] = $primaryCftData->job_approval_id;
                $setData['set']['in_qty'] = 'in_qty, - ' . $data['qty'];
                $setData['set']['total_prod_qty'] = 'total_prod_qty, - ' . $data['qty'];
                if($primaryCftData->job_entry_type == 0){
                    $setData['set']['inward_qty'] = 'inward_qty, - ' . $data['qty'];
                    $setData['set']['ih_prod_qty'] = 'ih_prod_qty, - ' . $data['qty'];
                }
                if($primaryCftData->job_entry_type == 4){
                    $setData['set']['outward_qty'] = 'outward_qty, - ' . $data['qty'];
                    $setData['set']['v_prod_qty'] = 'v_prod_qty, - ' . $data['qty'];
                    $setData['set']['ch_qty'] = 'ch_qty, - ' . $data['qty'];

                }
                if($primaryCftData->job_entry_type == 8){ 
                    $setData['set']['outward_qty'] = 'outward_qty, - ' . $data['qty'];
                }
                // $setData['set']['total_rework_qty'] = 'total_rework_qty, - ' . $data['qty'];
                if ($opCftData->operation_type == 2) { $setData['set']['total_rework_qty'] = 'total_rework_qty, - ' . $data['qty'];}

                $this->setValue($setData);

                // If Rework from Rejection in FIR
                if($primaryCftData->job_entry_type == 8 && $opCftData->operation_type == 1){ 
                    $setData = array();
                    $setData['tableName'] = $this->fir_master;
                    $setData['where']['id'] = $primaryCftData->job_ref_id;
                    $setData['set']['total_rej_qty'] = 'total_rej_qty, - ' . $data['qty'];
                    $setData['set']['total_rw_qty'] = 'total_rw_qty, + ' . $data['qty'];
                    $this->setValue($setData);

                    $setData = array();
                    $setData['tableName'] = $this->fir_dimension;
                    $setData['where']['id'] = $primaryCftData->rej_rw_manag_id;
                    $setData['set']['rej_qty'] = 'rej_qty, - ' . $data['qty'];
                    $setData['set']['rw_qty'] = 'rw_qty, + ' . $data['qty'];
                    $this->setValue($setData);
                }

                /*** Update Job Heat Table */
                $setData = array();
                $setData['tableName'] = $this->job_heat_trans;
                $setData['where']['job_approval_id'] =$cftData->job_approval_id;
                $setData['where']['batch_no'] = $cftData->batch_no;
                $setData['set']['rej_rw_qty'] = 'rej_rw_qty, - ' . $data['qty'];
                $setData['set']['in_qty'] = 'in_qty, - ' . $data['qty'];
                $this->setValue($setData);

            }
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getFmeData($data)
    {
        $data['tableName'] = $this->qc_fmea;
        $data['select'] = "qc_fmea.*";
        $data['where_in']['qc_fmea.pfc_id'] = $data['pfc_id'];
        $data['where_in']['qc_fmea.item_id'] = $data['item_id'];
        return $this->rows($data);
    }

    public function convertToScrap($data){
        try {
            $this->db->trans_begin();
            $cftData = $this->getRejMovementData($data['id']);
            $setData = array();
            $setData['tableName'] = $this->rejRWManage;
            $setData['where']['id'] = $data['id'];
            $setData['set']['cft_qty'] = 'cft_qty, + ' . $cftData->qty;
            $this->setValue($setData);

            $stockPlusQuery = [
                'id' => "",
                'location_id' => ($cftData->job_category == 1)?$this->SUPLY_REJ_STORE->id:$this->SCRAP_STORE->id,
                'batch_no' => $cftData->job_number,
                'trans_type' => 1,
                'item_id' => $cftData->product_id,
                'qty' =>$cftData->qty,
                'ref_type' => 34,
                'ref_id' => $cftData->job_card_id,
                'trans_ref_id' => $data['id'],
                'ref_no' => $cftData->tag_prefix.'/'. $cftData->tag_no,
                'ref_date' => date("Y-m-d"),
                'created_by' => $this->session->userdata('loginId'),
                'stock_effect' => 1
            ];
            $this->store('stock_transaction', $stockPlusQuery);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status'=>1,'message' => "Scrap Saved Successfully"];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getFIRData($jobTransId){
        $data['tableName'] =$this->jobTrans;
        $data['select']="job_transaction.rej_rw_manag_id,job_transaction.ref_id,fir_master.fg_batch_no"; // rej_rw_manag_id = fir_dimension id , ref_id = fir_master id
        $data['leftJoin']['fir_master']="fir_master.id = job_transaction.ref_id";
        $data['where']['job_transaction.entry_type'] = 8;
        $data['where']['job_transaction.id'] = $jobTransId;
        return $this->row($data);
    }
}
