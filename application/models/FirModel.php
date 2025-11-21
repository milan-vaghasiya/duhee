<?php
class FirModel extends MasterModel
{
    private $fir_master = "fir_master";
    private $fir_dimension = "fir_dimension";
    private $job_approval ="job_approval";
    private $jobCard = "job_card";
    private $stockTransaction ="stock_transaction";
    private $job_transaction ="job_transaction";
   
    public function getDTRows($data)
    {
        $data['tableName'] = "job_transaction";
        $data['select'] = "job_transaction.*,job_card.job_number,job_card.product_id,item_master.full_name,party_master.party_name,process_master.process_name";
        $data['leftJoin']['job_approval'] ='job_transaction.job_approval_id = job_approval.id';
        $data['leftJoin']['job_approval as crnt_approval'] ='crnt_approval.in_process_id = job_approval.out_process_id AND crnt_approval.job_card_id = job_approval.job_card_id';
        $data['leftJoin']['job_card'] ='job_card.id = job_approval.job_card_id';
        $data['leftJoin']['item_master'] ='item_master.id = job_card.product_id';
        $data['leftJoin']['party_master'] ='party_master.id = job_transaction.vendor_id';
        $data['leftJoin']['process_master'] ='process_master.id = job_approval.in_process_id';

        $data['where']['job_card.order_status'] = 2;
        // $data['where']['in_process_id'] = $this->FIR_PROCESS->id;
        $data['where']['crnt_approval.stage_type'] = 3;
        $data['where']['job_transaction.entry_type'] = 6;
        $data['having'][] = '(job_transaction.qty-job_transaction.accepted_qty) > 0';

        $data['searchCol'][] = "DATE_FORMAT(job_transaction.entry_date,'%d-%m-%Y')";
        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "job_transaction.qty";
        $data['searchCol'][] = "job_transaction.accepted_qty";

        $columns = array('', '', 'job_card.job_number', 'item_master.full_name','process_master.process_name','party_master.party_name', 'job_transaction.qty', 'job_transaction.accepted_qty', '');
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
            $setData['tableName'] = $this->job_transaction;
            $setData['where']['id'] = $data['job_trans_id'];
            $setData['set']['accepted_qty'] = 'accepted_qty, + '.$data['qty'];
            $result = $this->setValue($setData);
            
            $setData = array();
            $setData['tableName'] = $this->job_approval;
            $setData['where']['id'] = $data['job_approval_id'];
            $setData['set']['in_qty'] = 'in_qty, + '.$data['qty'];
            $result = $this->setValue($setData);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function checkFIStock($postData){
        $queryData = array();
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(stock_transaction.qty) as qty";
        $queryData['where']['ref_type'] = 22;
        $queryData['where']['ref_id'] = $postData['job_card_id'];
        $queryData['where']['location_id'] = $this->INSP_STORE->id;
        $queryData['group_by'][] = "stock_transaction.ref_id";
        return $this->row($queryData);
    }

    public function getPendingFirDTRows($data)
    {
        $data['tableName'] = "job_transaction";
        $data['select'] = "job_transaction.*,job_card.job_number,job_card.product_id,item_master.full_name,party_master.party_name,process_master.process_name";
        $data['leftJoin']['job_approval '] ='job_transaction.job_approval_id = job_approval.id';
        $data['leftJoin']['job_approval as crnt_approval'] ='crnt_approval.in_process_id = job_approval.out_process_id AND crnt_approval.job_card_id = job_approval.job_card_id';
        $data['leftJoin']['job_card '] ='job_card.id = job_approval.job_card_id';
        $data['leftJoin']['item_master '] ='item_master.id = job_card.product_id';
        $data['leftJoin']['party_master '] ='party_master.id = job_transaction.vendor_id';
        $data['leftJoin']['process_master '] ='process_master.id = job_approval.in_process_id';
        $data['where']['job_card.order_status'] = 2;
        // $data['where']['in_process_id'] = $this->FIR_PROCESS->id;
        $data['where']['crnt_approval.stage_type'] = 3;
        $data['where']['job_transaction.entry_type'] = 6;
        $data['having'][] = '(job_transaction.accepted_qty-job_transaction.fir_qty) > 0';

        $data['searchCol'][] = "DATE_FORMAT(job_transaction.entry_date,'%d-%m-%Y')";
        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "job_transaction.qty";
        $data['searchCol'][] = "job_transaction.fir_qty";

        $columns = array('', '','job_transaction.entry_date', 'job_card.job_number', 'item_master.full_name','process_master.process_name','party_master.party_name', 'job_transaction.qty', 'job_transaction.fir_qty', '');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        // print_r($this->db->last_query());exit;
        return $result;
    }

    public function getMaxLotNoJobcardWise($postData){
        $data['tableName'] = $this->fir_master;
        $data['select'] = "MAX(fir_no) as fir_no";
        $data['where']['job_card_id'] = $postData['job_card_id'];
        $maxNo = $this->specificRow($data)->fir_no;
		$nextFirNo = (!empty($maxNo))?($maxNo + 1):1;
		return $nextFirNo;
    }
   
    public function getMaxFgNo($postData){
        $data['tableName'] = $this->fir_master;
        $data['select'] = "MAX(fg_no) as fg_no";
        $data['where']['item_id'] = $postData['item_id'];
        $data['where']['YEAR(fir_date)'] = date("Y");
        $data['where']['MONTH(fir_date)'] = date("m");
        $maxNo = $this->specificRow($data)->fg_no;
		$nextFgNo = (!empty($maxNo))?($maxNo + 1):1;
		return $nextFgNo;
    }
   
    public function save($data){
        try {
            $this->db->trans_begin();
            $jobData = $this->processMovement->getApprovalData($data['job_approval_id']);
            $fir = $this->getFIRMasterDetail($data['fir_id']);
            /*** FIR Master Data  Remove previous Data*/
            $setData = array();
            $setData['tableName'] = $this->fir_master;
            $setData['where']['id'] = $data['fir_id'];
            $setData['set']['total_ok_qty'] = 'total_ok_qty, - '.$fir->total_ok_qty;
            $setData['set']['total_rej_qty'] = 'total_rej_qty, - '.$fir->total_rej_qty;
            $setData['set']['total_rw_qty'] = 'total_rw_qty, - '.$fir->total_rw_qty;
            $this->setValue($setData);

            $setData = array();
            $setData['tableName'] = $this->fir_master;
            $setData['where']['id'] = $data['fir_id'];
            $setData['set']['total_ok_qty'] = 'total_ok_qty, + '.(!empty($data['total_ok_qty'])?$data['total_ok_qty']:0);
            $setData['set']['total_rej_qty'] = 'total_rej_qty, + '.(!empty($data['total_rej_qty'])?$data['total_rej_qty']:0);
            $setData['set']['total_rw_qty'] = 'total_rw_qty, + '.(!empty($data['total_rw_qty'])?$data['total_rw_qty']:0);
            $this->setValue($setData);
            /*** Fir Dimention Data */
            if(!empty($data['dimension_id'])){
                foreach($data['dimension_id'] as $key=>$value){
                    $firDimension = [
                        'id'=>$data['trans_id'][$key],
                        'fir_id'=>$data['fir_id'],
                        'dimension_id'=>$value,
                        'job_card_id'=>$data['job_card_id'],
                        'trans_date'=>$data['trans_date'][$key],
                        'in_qty'=>$data['qty'],
                        'ok_qty'=>$data['ok_qty'][$key],
                        'ud_ok_qty'=>$data['ud_ok_qty'][$key],
                        'rej_qty'=>$data['rej_qty'][$key],
                        'rw_qty'=>$data['rw_qty'][$key],
                        'inspected_qty'=>((!empty($data['ok_qty'][$key])?$data['ok_qty'][$key]:0)+(!empty($data['ud_ok_qty'][$key])?$data['ud_ok_qty'][$key]:0)+(!empty($data['rej_qty'][$key])?$data['rej_qty'][$key]:0)+(!empty($data['rw_qty'][$key])?$data['rw_qty'][$key]:0)),
                        'inspector_id'=>$data['inspector_id'][$key],
                        'remark'=>$data['dim_remark'][$key],
                        'created_by'=>$data['created_by']
                    ];
                    $result = $trans = $this->store($this->fir_dimension,$firDimension);
                  
                }
            }else{
                return ['status' => 2, 'message' => "Control Plan  Dimension is required " ];
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

    public function getFIRMasterDetail($id){

        $data['tableName'] = $this->fir_master;
        $data['select'] = "fir_master.*,item_master.item_name,item_master.full_name,item_master.item_code,item_master.part_no,job_card.job_number,job_card.order_status,job_card.process,job_approval.in_process_id,job_approval.out_process_id,next_approval.id as next_approval_id";
        $data['leftJoin']['job_card'] = "job_card.id = fir_master.job_card_id";
        $data['leftJoin']['job_approval'] = "job_approval.id = fir_master.job_approval_id";
        $data['leftJoin']['job_approval as next_approval'] = "next_approval.in_process_id = job_approval.out_process_id AND next_approval.job_card_id=job_approval.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = fir_master.item_id";
        $data['where']['fir_master.id'] = $id ;
        return $this->row($data);
    }
 
    public function getFIRDimensionData($postData){
        $data['tableName'] = $this->fir_dimension;
        $data['select'] = "fir_dimension.*,pfc_trans.parameter,pfc_trans.char_class,pfc_trans.requirement,pfc_trans.min_req,pfc_trans.max_req,pfc_trans.other_req,qc_fmea.potential_effect,qc_fmea.instrument_code,qc_fmea.detec,qc_fmea.detec,item_category.category_name,qc_fmea.potential_cause,employee_master.emp_name";
        $data['leftJoin']['pfc_trans'] = "pfc_trans.id = fir_dimension.dimension_id";
        $data['leftJoin']['qc_fmea'] = 'qc_fmea.ref_id = pfc_trans.id AND qc_fmea.is_delete =0 AND process_detection = "INSP"' ;
        $data['leftJoin']['item_category'] = 'item_category.id = qc_fmea.detec';
        $data['leftJoin']['employee_master'] = 'employee_master.id = fir_dimension.inspector_id';
        $data['where']['fir_dimension.fir_id'] = $postData['fir_id'];
        $data['order_by']['fir_dimension.sequence'] ='ASC';
        return $this->rows($data);
    }

    public function getFirDTRows($data)
    {
        $data['tableName'] = $this->fir_master;
        $data['select'] = "fir_master.*,item_master.item_name,item_master.full_name,item_master.item_code,job_card.job_number,job_card.order_status,job_card.process,job_approval.ok_qty,job_approval.out_process_id";
        $data['leftJoin']['job_card'] = "job_card.id = fir_master.job_card_id";
        $data['leftJoin']['job_approval'] = "job_approval.id = fir_master.job_approval_id";
        $data['leftJoin']['item_master'] = "item_master.id = fir_master.item_id";
        $data['where']['fir_master.fir_type'] =1;
        if(empty($data['status'])){$data['having'][] ='fir_master.qty > (fir_master.total_rej_qty+fir_master.total_rw_qty+fir_master.movement_qty)';}
        if(!empty($data['status'])){$data['having'][] ='fir_master.qty <= (fir_master.total_rej_qty+fir_master.total_rw_qty+fir_master.movement_qty)';}
        $data['order_by']['fir_master.created_at'] = "DESC";
        $data['order_by']['fir_master.id'] = "DESC";

        $data['searchCol'][] = "DATE_FORMAT(fir_master.fir_date,'%d-%m-%Y')";
        $data['searchCol'][] = "fir_master.fir_number";
        $data['searchCol'][] = "job_card.batch_no";
        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "fir_master.qty";

        $columns = array('', '', 'fir_master.fir_date','fir_master.fir_number','job_card.batch_no' ,'job_card.job_number', 'item_master.full_name', 'fir_master.qty');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }
    
    public function delete($id){
        try {
           
            $this->db->trans_begin();
            $firData = $this->getFIRMasterDetail($id);
            $dimensionData = $this->getFIRDimensionData(['fir_id'=>$id]);

            $jobTrans = $this->getFirJobTrans($id);
            foreach($jobTrans as $row){
                $this->processMovement->delete($row->id);                   
            }
            $setData = array();
            $setData['tableName'] = $this->job_approval;
            $setData['where']['id'] = $firData->job_approval_id;
            $setData['set']['outward_qty'] = 'outward_qty, - '.$firData->qty;
            $this->setValue($setData);

            $this->trash($this->fir_dimension,['fir_id'=>$id]);
            $result = $this->trash($this->fir_master,['id'=>$id]);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getFirJobTrans($fir_id){
        $data['tableName'] ='job_transaction';
        $data['where']['job_transaction.ref_id'] = $fir_id;
        $data['where_in']['job_transaction.entry_type'] =8;
        return $this->rows($data);
    }

    public function saveLot($data){
        try {
         
            $this->db->trans_begin();
            $jobData = $this->processMovement->getApprovalData($data['job_approval_id']);
           
            $qty = array_sum($data['lot_qty']);
            $job_trans_id = implode(",",$data['job_trans_id']);
            /*** FIR Master Data */
            $firData=[
                'id'=>$data['id'],
                'fir_date'=>$data['fir_date'],
                'fir_type'=>1,
                'job_approval_id'=>$data['job_approval_id'],
                'job_trans_id'=>$data['job_trans_id'],
                'job_card_id'=>$data['job_card_id'],
                'item_id'=>$data['item_id'],
                'qty'=>$qty,
                'job_trans_id'=>$job_trans_id,
                'live_packing'=>$data['live_packing'],
                'created_by'=>$data['created_by']
            ];
            if(empty($data['id'])){
                $lot_no = $this->getMaxLotNoJobcardWise(['job_card_id'=>$data['job_card_id']]);
                $fir_number="FIR/".$jobData->job_number.'/'.$lot_no;
                $firData['fir_no'] = $lot_no;
                $firData['fir_prefix'] = "FIR/";
                $firData['fir_number'] = $fir_number;
                $fg_no = $this->fir->getMaxFGNo(['item_id'=>$data['item_id']]);
                $year = n2y(date('Y'));
                $month = n2m(date('m'));
                $firData['fg_no'] = $fg_no;
                $firData['fg_batch_no'] =$year.$month.sprintf('%02d',$fg_no);
            }
            $result = $this->store($this->fir_master,$firData);
            $fir_id = !empty($data['id'])?$data['id']:$result['insert_id'];

            

            foreach($data['job_trans_id'] as $key=>$value){
                $setData = array();
                $setData['tableName'] = $this->job_transaction;
                $setData['where']['id'] = $value;
                $setData['set']['fir_qty'] = 'fir_qty, + '.$data['lot_qty'][$key];
                $this->setValue($setData);
            }
            


            // $prsData = $this->item->getPrdProcessDataProductProcessWise(['item_id'=>$jobData->product_id,'process_id'=>$jobData->in_process_id]);
            $approvalData = $this->processMovement->getApprovalData($data['job_approval_id']);

            $firDimensionData = $this->controlPlan->getCPDimenstion(['pfc_id'=>$approvalData->pfc_ids,'item_id'=>$jobData->product_id,'control_method'=>'FIR','responsibility'=>'INSP']);

            /*** Fir Dimention Data */
            if(!empty($firDimensionData)){
                $freq='';
                $freqString = $firDimensionData[0]->potential_cause;
                $lot_type = 1;
                if(strpos($freqString,'%') > 0){
                    $freq = TO_FLOAT($freqString);
                    $qty = $freq * $qty/100;
                    $lot_type = 1;
                }elseif(strpos($freqString,'Lot') > 0){
                    $qty =$firDimensionData[0]->sev;
                    $lot_type = 2;
                }
                // $this->store($this->fir_master,['id'=>$fir_id,'lot_type'=>$lot_type,'sample_qty'=>$qty]);
                $i=1;
                foreach($firDimensionData as $row){
                    $firDimension = [
                        'id'=>'',
                        'fir_id'=>$fir_id,
                        'dimension_id'=>$row->id,
                        'job_card_id'=>$data['job_card_id'],
                        'in_qty'=>(($i == 1) ? $qty:0),
                        'ok_qty'=>(($i == 1) ? $qty:0),
                        'sequence'=>$i,
                        'created_by'=>$data['created_by']
                    ];
                    $trans = $this->store($this->fir_dimension,$firDimension);
                    $i++;
                }
            }else{
                return ['status' => 2, 'message' => "Control Plan  Dimension is required " ];
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

    public function completeFir($data){
        try {
            $this->db->trans_begin();
            $id = $data['id'];
            $firData = $this->getFIRMasterDetail($id);
            $firDimensionData = $this->getFIRDimensionData(['fir_id'=>$id]);
            $jobData = $this->jobcard->getJobcard($firData->job_card_id);
            /*** Fir Dimention Data */
            $totalRejQty =0; $totalRwQty =0; 
            if(!empty($firDimensionData)){
                foreach($firDimensionData as $row){
                    
                    if($row->rej_qty > 0 || $row->rw_qty >0){
                        $movementLogData = [
                            'id' => '',
                            'entry_date' => date("Y-m-d"),
                            'trans_type' => 1,
                            'entry_type' => 8,
                            'ref_id' => $row->fir_id,
                            'rej_rw_manag_id' => $row->id,
                            'vendor_id' => 0,
                            'job_card_id' => $firData->job_card_id,
                            'job_approval_id' =>$firData->job_approval_id,
                            'process_id' =>$firData->in_process_id,
                            'product_id' =>$firData->item_id,
                            'qty' =>0,
                            'remark' => $row->remark,
                            'cycle_time' => '',
                            'production_time' => '',
                            'send_to' => 0,
                            'machine_id' => '',
                            'shift_id' => '',
                            'operator_id' => $row->inspector_id,
                            'rej_qty' => ($row->rej_qty > 0)?$row->rej_qty:0,
                            'rw_qty' => ($row->rw_qty > 0)?$row->rw_qty:0,
                            'hold_qty' => 0,
                            'batch_no' => $data['batch_no'],
                            'created_by' => $this->session->userdata('loginId')
                        ];
                        $result = $this->processMovement->save($movementLogData);
                        $totalRejQty += (!empty($row->rej_qty)?$row->rej_qty:0);
                        $totalRwQty += (!empty($row->rw_qty)?$row->rw_qty:0);
                       
                    }
                }
            }else{
                return ['status' => 2, 'message' => "Control Plan  Dimension is required " ];
            }
            $okQty = $firData->qty - ($totalRejQty + $totalRwQty);
            $setData = array();
            $setData['tableName'] = $this->fir_master;
            $setData['where']['id'] = $firData->id;
            $setData['set']['total_rej_qty'] = 'total_rej_qty, + '.$totalRejQty;
            $setData['set']['total_rw_qty'] = 'total_rw_qty, + '.$totalRwQty;
            $result = $this->setValue($setData);
            $this->store($this->fir_master,['id'=>$firData->id,'total_ok_qty'=>$okQty]);
            $movementLogData = [
                'id' => '',
                'entry_date' => date("Y-m-d"),
                'trans_type' => 1,
                'entry_type' => 8,
                'ref_id' => $firData->id,
                'vendor_id' => 0,
                'job_card_id' => $firData->job_card_id,
                'job_approval_id' =>$firData->job_approval_id,
                'process_id' =>$firData->in_process_id,
                'product_id' =>$firData->item_id,
                'qty' =>$okQty,
                'remark' => '',
                'cycle_time' => '',
                'production_time' => '',
                'send_to' => 0,
                'machine_id' => '',
                'shift_id' => '',
                'operator_id' => 0,
                'rej_qty' => 0,
                'rw_qty' => 0,
                'hold_qty' => 0,
                'batch_no' => $data['batch_no'],
                'created_by' => $this->session->userdata('loginId')
            ];
            $result = $this->processMovement->save($movementLogData);                       
            
            $batch_no = $firData->fg_batch_no;
            if(!empty($jobData->batch_no)){
                $batch_no = $jobData->batch_no.','.$firData->fg_batch_no;
            }



            $this->store($this->jobCard,['id'=>$jobData->id,'batch_no'=>$batch_no]);
            $this->store($this->fir_master,['id'=>$id,'status'=>1]);
          
            /***** If FIR is live then Auto process movement */

            if($firData->live_packing == 1){
                $movementData =[
                    'id'=>'',
                    'ref_id'=>$firData->id,
                    'job_approval_id'=>$firData->job_approval_id,
                    'entry_date'=>date("Y-m-d"),
                    'send_to'=>0,
                    'qty'=>$okQty,
                    'remark'=>'',
                    'machine_id'=>0,
                    'vendor_id'=>0,
                    'location_id'=>0,
                    'entry_type'=>6,
                    'created_by'=>$this->loginId,
                    'accepted_qty'=>!empty($firData->fb_qty)?$firData->fb_qty:0,
                    'fir_qty'=>!empty($firData->fb_qty)?$firData->fb_qty:0,
                ];   
                $result = $this->processMovement->saveProcessMovement($movementData);
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

    public function getRejData($postData){
        $queryData['tableName'] = "rej_rw_management";
        $queryData['select']='ifnull(SUM(CASE WHEN rej_rw_management.rej_type = 1 THEN rej_rw_management.qty END),0) as mc_rej_qty,ifnull(SUM(CASE WHEN  rej_rw_management.rej_type = 2  THEN rej_rw_management.qty END),0) as rm_rej_qty,ifnull(SUM(CASE WHEN  rej_rw_management.operation_type = 3 THEN rej_rw_management.qty END),0) as hold_qty';
        $queryData['leftJoin']['job_transaction'] ="job_transaction.id = rej_rw_management.job_trans_id";
        $queryData['where']['job_transaction.rej_rw_manag_id'] = $postData['fir_trans_id'];
        $queryData['where_in']['rej_rw_management.entry_type'] ="3";
        $queryData['where_in']['rej_rw_management.operation_type'] =1;
        return $this->row($queryData);
    }

    public function saveDimension($data){ //print_r($data);exit;
        try {
            $this->db->trans_begin();
            $result  = $this->store($this->fir_dimension,$data);
            if(!empty($data['ok_qty']) || !empty($data['ud_ok_qty'])){
                $fir = $this->getFIRDimensionDetail(['id'=>$data['id']]);
                $totalQty = $fir->ok_qty+$fir->ud_ok_qty;
                $nxtDimension = $this->getFIRDimensionDetail(['fir_id'=>$fir->fir_id,'sequence'=>($fir->sequence+1)]);
                if(!empty($nxtDimension)){
                    $this->store($this->fir_dimension,['id'=>$nxtDimension->id,'in_qty'=>$totalQty,'ok_qty'=>$totalQty]);
                }else{
                    $this->store($this->fir_master,['id'=>$fir->fir_id,'total_ok_qty'=>$totalQty]);
                }
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

    public function getFIRDimensionDetail($postData){
        $data['tableName'] = $this->fir_dimension;
        $data['select'] = "fir_dimension.*,fir_master.sample_qty,fir_master.lot_type,fir_master.qty";
        $data['leftJoin']['fir_master'] ="fir_master.id = fir_dimension.fir_id";
        if(!empty($postData['id'])){ $data['where']['fir_dimension.id'] = $postData['id']; }
        if(!empty($postData['sequence'])){ $data['where']['fir_dimension.sequence'] = $postData['sequence']; }
        if(!empty($postData['fir_id'])){ $data['where']['fir_dimension.fir_id'] = $postData['fir_id']; }
        return $this->row($data);
    }

    public function updateDimensionSequance($data){
		try{
            $this->db->trans_begin();
    		$ids = explode(',', $data['id']);
            $queryData['tableName'] = $this->fir_dimension;
            $queryData['select'] = "fir_dimension.*";
            $queryData['where']['fir_id'] = $data['fir_id'];
            $queryData['where']['in_qty > '] = 0;
            $queryData['where']['inspected_qty'] = 0;
            $prevData = $this->row($queryData);

    		$i=($prevData->sequence+1);
    		foreach($ids as $pp_id):
    			$seqData=Array("sequence"=>$i++);
    			$this->edit($this->fir_dimension,['id'=>$pp_id],$seqData);
    		endforeach;
    		$result = ['status'=>1,'message'=>'Dimension Sequence updated successfully.'];
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
	}

    public function getDimensionOnSequence($postData){
        $data['tableName'] = $this->fir_dimension;
        $data['select'] = "fir_dimension.*";
        $data['where']['fir_dimension.fir_id'] = $postData['fir_id'];
        $data['where']['fir_dimension.sequence <='] = $postData['sequence']; 
        return $this->rows($data);
    }

    public function getFIRPendingJobTrans($postData){
        $data['tableName'] = "job_transaction";
        $data['select'] = "job_transaction.*,job_card.job_number,process_master.process_name,party_master.party_name";
        $data['leftJoin']['job_approval'] ='job_transaction.job_approval_id = job_approval.id';
        $data['leftJoin']['job_card '] ='job_card.id = job_transaction.job_card_id';
        $data['leftJoin']['job_approval as crnt_approval'] ='crnt_approval.in_process_id = job_approval.out_process_id AND crnt_approval.job_card_id = job_approval.job_card_id';
        $data['leftJoin']['process_master '] ='process_master.id = job_approval.in_process_id';
        $data['leftJoin']['party_master '] ='party_master.id = job_transaction.vendor_id';
        $data['where']['job_transaction.job_card_id'] = $postData['job_card_id'];
        $data['where']['job_transaction.vendor_id'] = $postData['vendor_id'];
        $data['where']['crnt_approval.stage_type'] = 3;
        $data['where']['job_transaction.entry_type'] = 6;
        $data['having'][] = '(job_transaction.accepted_qty-job_transaction.fir_qty) > 0';
        return $this->rows($data);
    }
}
