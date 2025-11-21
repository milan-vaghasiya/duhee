<?php
class JobWorkModel extends MasterModel
{
    private $jobWork = "jobwork";
    private $jobTransaction = "jobwork_transaction";
    private $jobworkOrder = "jobwork_order";
    private $jobwork_order_trans="jobwork_order_trans";
    private $jobCard = "job_card";
    private $stockTrans  = "stock_transaction";
    private $jobworkOrderTrans = "jobwork_order_trans";
    private $processMasterJobwork = "process_master_jobwork";

    public function getNextJobworkNo(){
        $data['tableName'] = $this->jobWork;
        $data['select'] = "MAX(trans_no) as jobworkNo";
        $jobworkNo = $this->specificRow($data)->jobworkNo;
        $oldNo = 1129;
		$nextJobworkNo = (!empty($jobworkNo))?($jobworkNo + 1):$oldNo;
		return $nextJobworkNo;
    }
    
    public function getNextJobworkReturnNo(){
        $data['tableName'] = $this->jobTransaction;
        $data['select'] = "MAX(job_trans_no) as job_trans_no";
        $job_trans_no = $this->specificRow($data)->job_trans_no;
		$nextJobworkReturnNo = (!empty($job_trans_no))?($job_trans_no + 1):1;
		return $nextJobworkReturnNo;
    }
    
    public function getProcessListForJobWork(){
        $data['tableName'] = $this->processMasterJobwork;
        return $this->rows($data);
    }

    public function getProcessJobWork($id){
        $data['tableName'] = $this->processMasterJobwork;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    //Changed By Karmi @10/05/2022
    public function getDTRows($data){
        $data['tableName'] = $this->jobTransaction;
        $data['select'] = "jobwork_transaction.*,process_master_jobwork.process_name,item_master.item_name,item_master.full_name,jobwork.trans_number,jobwork.ewb_no,jobwork.vendor_id,party_master.party_name,jobwork_order.trans_no as order_no,jobwork_order.trans_prefix as order_prefix";
        $data['leftJoin']['process_master_jobwork'] =  "process_master_jobwork.id = jobwork_transaction.process_id";
        $data['leftJoin']['item_master'] =  "item_master.id = jobwork_transaction.item_id";
        $data['leftJoin']['jobwork'] =  "jobwork.id = jobwork_transaction.jobwork_id";
        $data['leftJoin']['party_master'] = "party_master.id = jobwork.vendor_id";
        $data['leftJoin']['jobwork_order_trans'] = "jobwork_transaction.job_order_trans_id = jobwork_order_trans.id";
        $data['leftJoin']['jobwork_order'] = "jobwork_order.id = jobwork_order_trans.order_id";
        $data['where']['jobwork.vendor_id != '] = 0;
        //$data['where']['jobwork_transaction.entry_type'] = 1;
        $data['order_by']['jobwork_transaction.id'] = "DESC";

        //Changed By Karmi @16/05/2022
		if($data['status'] == 1){$data['customWhere'][] = 'jobwork_transaction.qty = jobwork_transaction.received_qty AND jobwork_transaction.entry_type = 1 AND jobwork_transaction.is_approve != 0 ';}
        elseif($data['status'] == 2) { $data['customWhere'][] = 'jobwork_transaction.entry_type = 2 AND jobwork_transaction.is_approve != 0'; }
        else {$data['customWhere'][] = 'jobwork_transaction.entry_type = 1 AND jobwork_transaction.is_approve = 0';}
        

        if($data['status'] == 2){
            $data['searchCol'][] = "";
            $data['searchCol'][] = "jobwork_transaction.id";
            $data['searchCol'][] = "jobwork.trans_number";
            $data['searchCol'][] = "DATE_FORMAT(jobwork_transaction.entry_date,'%d-%m-%Y')";
            $data['searchCol'][] = "jobwork_transaction.challan_no";
            $data['searchCol'][] = "party_master.party_name";
            $data['searchCol'][] = "item_master.full_name";
            $data['searchCol'][] = "process_master_jobwork.process_name";
            $data['searchCol'][] = "jobwork_transaction.received_qty";
            $data['searchCol'][] = "jobwork_transaction.received_com_qty";
            $data['searchCol'][] = "jobwork_transaction.rej_qty";
            $data['searchCol'][] = "jobwork_transaction.wp_qty";
            $data['searchCol'][] = "jobwork_transaction.bill_qty";
            

            $columns = array('','','jobwork.trans_number','jobwork_transaction.entry_date', 'jobwork_transaction.challan_no', 'party_master.party_name', 'item_master.full_name', 'process_master_jobwork.process_name','jobwork_transaction.received_qty','jobwork_transaction.received_com_qty','jobwork_transaction.rej_qty','jobwork_transaction.wp_qty','jobwork_transaction.bill_qty');
        
        }else{
            $data['searchCol'][] = "";
            $data['searchCol'][] = "jobwork.trans_number";
            $data['searchCol'][] = "DATE_FORMAT(jobwork_transaction.entry_date,'%d-%m-%Y')";
            $data['searchCol'][] = "party_master.party_name";
            $data['searchCol'][] = "item_master.full_name";
            $data['searchCol'][] = "process_master_jobwork.process_name";
            $data['searchCol'][] = "jobwork_transaction.qty";
            $data['searchCol'][] = "jobwork_transaction.com_qty";
            $data['searchCol'][] = "jobwork_transaction.received_qty";
            $data['searchCol'][] = "jobwork_transaction.received_com_qty";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
    
            $columns = array('','jobwork.trans_number','jobwork_transaction.entry_date', 'party_master.party_name', 'item_master.full_name', 'process_master_jobwork.process_name', 'jobwork_transaction.qty', 'jobwork_transaction.com_qty','jobwork_transaction.received_qty','jobwork_transaction.received_com_qty','','');
        }
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }


    public function getJobworkOutData($id){
        $data['select'] = "job_inward.*,process_master_jobwork.process_name,item_master.item_name,item_master.item_code,job_card.job_no,job_card.job_prefix, party_master.party_name,party_master.party_address,party_master.gstin,job_work_order.jwo_prefix,job_work_order.jwo_no,job_work_order.production_days";
        $data['join']['process_master_jobwork'] =  "process_master_jobwork.id = job_inward.process_id";
        $data['join']['item_master'] =  "item_master.id = job_inward.product_id";
        $data['join']['job_card'] =  "job_card.id = job_inward.job_card_id";
        $data['join']['party_master'] = "party_master.id = job_inward.vendor_id";
        $data['leftJoin']['job_work_order'] = "job_work_order.id = job_inward.job_order_id";
        $data['where']['job_inward.vendor_id !='] = 0;
        $data['where']['job_inward.id'] = $id;
        $data['tableName'] = $this->jobWork;
        return $this->row($data);
    }

    public function getWorkOrderListVendorWise($vendor_id){
        $data['select'] = "jobwork_order.*";
        $data['tableName'] = $this->jobworkOrder;
        $data['where']['vendor_id'] = $vendor_id;
        return $this->rows($data);
    }

    public function getWorkOrderTransList($order_id){
        $data['select'] = "jobwork_order_trans.*,item_master.full_name,item_master.item_name,process_master_jobwork.process_name,unit_master.unit_name,com.unit_name as com_unit_name";
        $data['tableName'] = $this->jobwork_order_trans;
        $data['join']['process_master_jobwork'] =  "process_master_jobwork.id = jobwork_order_trans.process_id";
        $data['join']['item_master'] =  "item_master.id = jobwork_order_trans.item_id";
        $data['join']['unit_master'] =  "unit_master.id = item_master.unit_id";
        $data['join']['unit_master as com'] =  "com.id = jobwork_order_trans.com_unit";
        $data['where']['order_id'] = $order_id;
        return $this->rows($data);
    }
    
    public function getJobWorkChallan($id){
        $data['tableName'] = $this->jobWork;
        $data['select'] = "jobwork.*,party_master.party_name,party_master.party_address,party_master.gstin";
        $data['join']['party_master'] = "party_master.id = jobwork.vendor_id";
        $data['where']['jobwork.id'] = $id;
        $result = $this->row($data);
        return $result;
    }

    //Changed By Karmi @10/05/2022
    public function getJobWork($id){
        $data['tableName'] = $this->jobWork;
        $data['select'] = "jobwork.*,party_master.party_name,party_master.party_address,party_master.gstin";
        $data['join']['party_master'] = "party_master.id = jobwork.vendor_id";
        $data['where']['jobwork.id'] = $id;
        $result = $this->row($data);
        $result->itemData = $this->getJobworkTrans($id);
        return $result;
    }

    //Changed By Karmi @10/05/2022
    public function getJobworkTrans($id){
        $data['tableName'] = $this->jobTransaction;
        $data['select'] = "jobwork_transaction.*,process_master_jobwork.process_name,item_master.item_name,item_master.full_name";
        $data['join']['process_master_jobwork'] =  "process_master_jobwork.id = jobwork_transaction.process_id";
        $data['join']['item_master'] =  "item_master.id = jobwork_transaction.item_id";
        $data['where']['jobwork_transaction.jobwork_id'] = $id;
        $data['where']['jobwork_transaction.entry_type'] = 1;
        return $this->rows($data);
    }

    //Changed By Karmi @10/05/2022
    public function getJobworkTransBasedOnTransId($id){
        $data['tableName'] = $this->jobTransaction;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    //Changed By Karmi @10/05/2022
    public function saveJobworkOutward($masterData,$itemData){
        try{ 
            $vendor_name = $masterData['vendor_name']; unset($masterData['vendor_name']);
            $this->db->trans_begin();
            if(empty($masterData['id'])):
                $masterData['trans_prefix'] = "JW/".$this->shortYear."/";
                $masterData['trans_no'] = $this->getNextJobworkNo();
                $masterData['trans_number'] = $masterData['trans_prefix'].$masterData['trans_no'];
                $jobwork = $this->store($this->jobWork,$masterData);
                $jobworkId =  $jobwork['insert_id'];
                $result = ['status'=>1,'message'=>'Jobwork save Successfully.','url'=>base_url("jobWork")];
            else:
                $this->store($this->jobWork,$masterData);
                $jobworkId = $masterData['id'];
                $jobworkItems = $this->getJobworkTrans($jobworkId);

                /** Remove Stock Transaction NYN**/
                foreach ($jobworkItems as $row) :
                    $this->remove($this->stockTrans,['ref_id'=>$jobworkId,'trans_ref_id'=>$row->id,'trans_type'=>1,'ref_type'=>18]);
                endforeach;

                $result = ['status'=>1,'message'=>'Jobwork updated Successfully.','url'=>base_url("jobWork")];
            endif;
            //print_r($itemData);exit;
            foreach($itemData['item_id'] as $key=>$value):
                $transData = [
                    'id'=>$itemData['id'][$key],
                    'ref_id'=>"",
                    'jobwork_id'=>$jobworkId,
                    'item_id'=>$value,
                    'entry_date'=>$masterData['trans_date'],
                    'process_id'=>$itemData['process_id'][$key],
                    'job_order_id'=>$itemData['job_order_id'][$key],
                    'qty'=>$itemData['qty'][$key],
                    'wpp'=>$itemData['wpp'][$key],
                    'variance'=>$itemData['variance'][$key],
                    'scarp_per_pcs'=>$itemData['scarp_per_pcs'][$key],
                    'scarp_rate_pcs'=>$itemData['scarp_rate_pcs'][$key],
                    'value_rate'=>$itemData['value_rate'][$key],
                    'unit_id'=>$itemData['unit_id'][$key],
                    'com_qty'=>$itemData['com_qty'][$key],
                    'price'=>$itemData['price'][$key],
                    'total_value'=>$itemData['total_value'][$key],
                    'cgst_amount'=>$itemData['cgst_amount'][$key],
                    'sgst_amount'=>$itemData['sgst_amount'][$key],
                    'igst_amount'=>$itemData['igst_amount'][$key],
                    'net_amount'=>$itemData['net_amount'][$key],
                    'location_id'=>$this->JOBW_STORE->id,
                    'batch_no'=>$vendor_name,
                    'remark'=>$itemData['remark'][$key],
                    'created_by' => $masterData['created_by']
                ];
                /** Insert Record in Delivery Transaction **/
               
                $saveTrans = $this->store($this->jobTransaction,$transData);
                $trans_id = (empty($itemData['id'][$key]))?$saveTrans['insert_id']:$itemData['id'][$key];

                /*** UPDATE STOCK TRANSACTION DATA ***/
                $stockQueryData['id']="";
                $stockQueryData['location_id']=$this->JOBW_STORE->id;
                $stockQueryData['batch_no']=$vendor_name;
                $stockQueryData['trans_type']=1;
                $stockQueryData['item_id']=$value;
                $stockQueryData['qty'] = $itemData['qty'][$key];
                $stockQueryData['ref_type']=18;
                $stockQueryData['ref_id']=$jobworkId;
                $stockQueryData['trans_ref_id']=$trans_id;
                $stockQueryData['ref_no']=$masterData['trans_prefix'].$masterData['trans_no'];
                $stockQueryData['ref_date']=$masterData['trans_date'];
                $stockQueryData['stock_effect']=0;
                $stockQueryData['created_by']=$masterData['created_by'];
                $this->store($this->stockTrans,$stockQueryData);

                /*** UPDATE STOCK TRANSACTION DATA ***/
                /*$stockQueryData['id']="";
                $stockQueryData['location_id']=$itemData['location_id'][$key];
                if(!empty($itemData['batch_no'][$key])){$stockQueryData['batch_no']=$itemData['batch_no'][$key];}
                $stockQueryData['trans_type']=2;
                $stockQueryData['item_id']=$value;
                $stockQueryData['qty'] = "-".$itemData['qty'][$key];
                $stockQueryData['ref_type']=18;
                $stockQueryData['ref_id']=$jobworkId;
                $stockQueryData['trans_ref_id']=$trans_id;
                $stockQueryData['ref_no']=$masterData['trans_prefix'].$masterData['trans_no'];
                $stockQueryData['ref_date']=$masterData['trans_date'];
                $stockQueryData['stock_effect']=1;
                $stockQueryData['created_by']=$masterData['created_by'];
                $this->store($this->stockTrans,$stockQueryData);*/
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

    //Changed By Karmi @10/05/2022
    public function deleteJobwork($id){
        //print_r($transData);exit
        
        $transData  = $this->getJobworkTransBasedOnTransId($id);  
        $where['id'] = $id;
        $this->trash($this->jobTransaction,$where);

        if($transData->ref_id == 0 && $transData->entry_type == 1):            
            $transCount = $this->getJobworkTrans($transData->jobwork_id);
            /** Remove Stock Transaction NYN**/
            $this->remove($this->stockTrans,['ref_id'=>$transData->jobwork_id,'trans_ref_id'=>$id,'ref_type'=>18]);       
            if(count($transCount) <= 0):
                //order master delete
                $result = $this->trash($this->jobWork,['id'=>$transData->jobwork_id],'Jobwork');
            endif; 
        else:
            $this->remove($this->stockTrans,['ref_id'=>$transData->jobwork_id,'trans_ref_id'=>$id,'ref_type'=>19]);

            $setData = array();
            $setData['tableName'] = $this->jobTransaction;
            $setData['where']['id'] = $transData->ref_id;
            $setData['set']['received_qty'] = 'received_qty, - ' . $transData->qty .'-'.$transData->rej_qty.'-'.$transData->wp_qty;
            $setData['set']['received_com_qty'] = 'received_com_qty, - ' . $transData->com_qty;
            $this->setValue($setData);
        endif;


        return ['status'=>1,'message'=>'Jobwork Deleted.','result'=>$result];
    }

    //Changed By Karmi @10/05/2022
    public function jobWorkReturnSave($data){

        //JobWork Trans Data
        $queryData = array(); 
        $queryData['tableName'] = $this->jobTransaction;
        $queryData['where']['id'] = $data['ref_id'][0];
        $jobInwardData = $this->row($queryData);

        //JobOrder Trans Data
        $transData = array(); 
        $transData['tableName'] = $this->jobwork_order_trans;
        $transData['where']['id'] = $data['job_order_trans_id'][0];
        $jobOrderData = $this->row($transData);

        $next_return_no = $this->getNextJobworkReturnNo();
        if ($jobInwardData->pending_qty <= $data['in_qty']) :
            $scrap_weight = round($jobOrderData->scarp_per_pcs * $data['in_qty'][0],3); //Changed by Karmi@17/05/2022
            $transData = [
                'id'=>"",
                'ref_id'=>$data['ref_id'][0],
                'entry_date'=>$data['trans_date'],
                'job_order_id'=>$jobInwardData->job_order_id,
                'challan_no'=>$data['challan_no'],
                'jobwork_id'=>$data['jobwork_id'][0],
                'item_id'=>$jobOrderData->converted_product,
                'process_id'=>$jobOrderData->process_id,
                'entry_type'=>2,
                'job_trans_no'=>$next_return_no,
                'job_order_trans_id'=>$data['job_order_trans_id'][0],
                'qty'=>$data['in_qty'][0],
                //'location_id'=>$data['location_id'][0],
                //'batch_no'=>$data['batch_no'][0],
                'com_qty'=>$data['in_com_qty'][0],
                'rej_qty'=>$data['rej_qty'][0],
                'rej_remark'=>$data['rej_remark'][0],
                'wp_qty'=>$data['wp_qty'][0],
                'price'=>$jobOrderData->process_charge,
                'value_rate'=>$jobOrderData->value_rate,
                'scrap_weight'=>$scrap_weight,
                'remark'=>$data['trans_remark'][0],
                'created_by' => $this->session->userdata('loginId')
            ];
            //print_r($transData);exit;
            $saveTrans = $this->store($this->jobTransaction,$transData);
            $trans_id = (empty($data['id'][0]))?$saveTrans['insert_id']:$data['id'][0];
            $masterData = $this->getJobWork($transData['jobwork_id']); //print_r($masterData);exit;

            // /*** UPDATE STOCK TRANSACTION DATA ***/
            // $stockQueryData['id']="";
            // $stockQueryData['location_id']=$jobInwardData->location_id;
            // $stockQueryData['batch_no']=$jobInwardData->batch_no;
            // $stockQueryData['trans_type']=2;
            // $stockQueryData['item_id']=$data['item_id'][0];
            // $stockQueryData['qty'] = "-".$data['in_qty'][0];
            // $stockQueryData['ref_type']=19;
            // $stockQueryData['ref_id']=$data['jobwork_id'][0];
            // $stockQueryData['trans_ref_id']=$trans_id;
            // $stockQueryData['ref_no']=$masterData->trans_prefix.$masterData->trans_no;
            // $stockQueryData['ref_date']=$data['trans_date'];
            // $stockQueryData['stock_effect']=0;
            // $stockQueryData['created_by']=$transData['created_by'];
            // $this->store($this->stockTrans,$stockQueryData);

            /*** UPDATE STOCK TRANSACTION DATA ***/
            /*$stockQueryData['id']="";
            $stockQueryData['location_id']=$data['location_id'][0];
            if(!empty($data['batch_no'][0])){$stockQueryData['batch_no']=$data['batch_no'][0];}
            $stockQueryData['trans_type']=1;
            $stockQueryData['item_id']=$data['item_id'][0];
            $stockQueryData['qty'] = $data['in_qty'][0];
            $stockQueryData['ref_type']=19;
            $stockQueryData['ref_id']=$data['jobwork_id'][0];
            $stockQueryData['trans_ref_id']=$trans_id;
            $stockQueryData['ref_no']=$masterData->trans_prefix.$masterData->trans_no;
            $stockQueryData['ref_date']=$data['trans_date'];
            $stockQueryData['stock_effect']=1;
            $stockQueryData['created_by']=$transData['created_by'];
            $this->store($this->stockTrans,$stockQueryData);*/


            $setData = array();
            $setData['tableName'] = $this->jobTransaction;
            $setData['where']['id'] = $data['ref_id'][0];
            $setData['set']['received_qty'] = 'received_qty, + ' . $data['in_qty'][0].'+'. $data['rej_qty'][0].'+'.$data['wp_qty'][0];
            $setData['set']['received_com_qty'] = 'received_com_qty, + ' . $data['in_com_qty'][0];
            $setData['set']['scrap_weight'] = 'scrap_weight, + ' . $scrap_weight;
            $this->setValue($setData);
            
            $this->approveReturn($trans_id);

            return ['status'=>1,'message'=>'JobWork Return Successfully. CH.NO: '.$next_return_no];
        else:
            return ['status'=>0,'message'=>'Invalid Data.'];
        endif;

        
    }

    public function jobRecordInserts($data, $is_last){
        $jobCardData = $this->jobcard->getJobcard($data['job_card_id']);

        $queryData['tableName'] = $this->productionTrans;
        $queryData['where']['id'] = $data['ref_id'];
        $inwardData = $this->row($queryData);

        $queryData = array();
        $queryData['tableName'] = $this->productionApproval;
        $queryData['where']['id'] = $inwardData->job_approval_id;
        $approvalData = $this->row($queryData);

        $jobCardUpdateData['total_out_qty'] = $jobCardData->total_out_qty + $data['qty'];
        $completeJobQty = $jobCardUpdateData['total_out_qty'] + $jobCardData->total_reject_qty + $jobCardData->total_rework_qty;
        if ($jobCardData->qty <= $completeJobQty) :
            $jobCardUpdateData['order_status'] = 4;
        endif;
        $this->edit($this->jobCard, ['id' => $data['job_card_id']], $jobCardUpdateData);

        if (empty($jobCardData->pre_disp_inspection)) :
            $setData = array();
            $setData['tableName'] = $this->jobCard;
            $setData['where']['id'] = $data['job_card_id'];
            $setData['set']['unstored_qty'] = 'unstored_qty, + ' . $data['qty'];
            $this->setValue($setData);
        else :
            $itemData = $this->item->getItem($data['product_id']);
            $stockQty['pending_inspection_qty'] = $itemData->pending_inspection_qty + $data['qty'];
            $this->edit($this->itemMaster, ['id' => $data['product_id']], $stockQty);
        endif;


        $juq['select'] = 'wp_qty';
        $juq['tableName'] = $this->jobUsedMaterial;
        $juq['where']['id'] = $inwardData->material_used_id;
        $wpQty = $this->row($juq)->wp_qty;
        $imq = round((($data['qty']) * $wpQty), 3);

        $outwardPostData = [
            'id' => '',
            'entry_type' => '2',
            'ref_id' => $data['ref_id'],
            'entry_date' => $data['entry_date'],
            'job_card_id' => $data['job_card_id'],
            'job_approval_id' => $inwardData->job_approval_id,
            'job_order_id' => $inwardData->job_order_id,
            'vendor_id' => $inwardData->vendor_id,
            'process_id' => $inwardData->process_id,
            'product_id' => $inwardData->product_id,
            'in_qty' => $data['qty'],
            'in_w_pcs' => "",
            'in_total_weight' => "",
            'rework_qty' => 0,
            'rejection_qty' => 0,
            'out_qty' => $data['qty'],
            'ud_qty' => 0,
            'w_pcs' => $data['total_weight'] / $data['qty'],
            'total_weight' => $data['total_weight'],
            'rejection_reason' => "",
            'rejection_stage' => "",
            'remark' => $data['remark'],
            'challan_prefix' => "",
            'challan_no' => "",
            'in_challan_no' => "",
            'charge_no' => "",
            'material_used_id' => $inwardData->material_used_id,
            'issue_batch_no' => $inwardData->issue_batch_no,
            'issue_material_qty' => $imq,
            'challan_status' => $inwardData->challan_status,
            'operator_id' => "",
            'machine_id' => "",
            'shift_id' => "",
            "production_time" => "",
            "cycle_time" => "",
            "job_process_ids" => $inwardData->job_process_ids,
            "rework_process_id" => "",
            "created_by" => $data['created_by']
        ];
        $otData = $this->store($this->productionTrans, $outwardPostData);

        $this->edit($this->productionTrans, ['id' => $data['ref_id']], ['out_qty' => ($inwardData->out_qty + $data['qty'])]);

        $setData = array();
        $setData['tableName'] = $this->productionApproval;
        $setData['where']['in_process_id'] = $data['process_id'];
        $setData['where']['job_card_id'] = $inwardData->job_card_id;
        if (!empty($inwardData->rework_process_id)) :
            $lastReworkProcess = explode(",", $inwardData->rework_process_id);
            if ($lastReworkProcess[count($lastReworkProcess) - 1] != $data['process_id']) :
                $setData['where']['trans_type'] = 1;
                $setData['where']['ref_id'] = $approvalData->ref_id;
            else :
                $setData['where']['trans_type'] = 0;
                $setData['where']['ref_id'] = 0;
            endif;
        else :
            $setData['where']['trans_type'] = 0;
            $setData['where']['ref_id'] = $approvalData->ref_id;
        endif;
        $setData['set']['in_qty'] = 'in_qty, + ' . $data['qty'];
        if ($is_last != true) :
            $setData['set']['out_qty'] = 'out_qty, + ' . $data['qty'];
        endif;
        $this->setValue($setData);

        $saveInward['insert_id'] = 0;
        if ($is_last != true) :
            $queryData = array();
            $queryData['tableName'] = $this->productionApproval;
            $queryData['select'] = "id,out_process_id";
            $queryData['where']['in_process_id'] = $data['process_id'];
            $queryData['where']['job_card_id'] = $inwardData->job_card_id;
            if (!empty($inwardData->rework_process_id)) :
                $lastReworkProcess = explode(",", $inwardData->rework_process_id);
                if ($lastReworkProcess[count($lastReworkProcess) - 1] != $data['process_id']) :
                    $queryData['where']['trans_type'] = 1;
                    $queryData['where']['ref_id'] = $approvalData->ref_id;
                else :
                    $queryData['where']['trans_type'] = 0;
                    $queryData['where']['ref_id'] = 0;
                endif;
            else :
                $queryData['where']['trans_type'] = 0;
                $queryData['where']['ref_id'] = $approvalData->ref_id;
            endif;
            $nextApproval = $this->row($queryData);

            if (!empty($nextApproval->out_process_id)) :
                $data['challan_prefix'] = '';
                $data['ch_no'] = 0;
                $jobWorkProcess = "";
                if (!empty($inwardData->vendor_id)) :
                    $data['challan_prefix'] = 'JO/' . $this->shortYear . '/';
                    $data['ch_no'] = $this->processApprove->nextJobWorkChNo();
                endif;
                $outwardData = $this->processApprove->getOutward($nextApproval->id);
                $reworkProcess = "";
                if ($outwardData->trans_type == 1) :
                    $queryData = array();
                    $queryData['tableName'] = $this->productionTrans;
                    $queryData['where']['id'] = $outwardData->ref_id;
                    $refInwardData = $this->row($queryData);
                    $reworkProcess = $refInwardData->rework_process_id;
                endif;
                $materialUsedId = $inwardData->material_used_id;
                $inwardPostData = [
                    'id' => "",
                    'entry_type' => 1,
                    'entry_date' => $data['entry_date'],
                    'job_card_id' => $data['job_card_id'],
                    'job_approval_id' => $nextApproval->id,
                    'job_order_id' => $inwardData->job_order_id,
                    'vendor_id' => $inwardData->vendor_id,
                    'process_id' => $nextApproval->out_process_id,
                    'product_id' => $inwardData->product_id,
                    'in_qty' => $data['qty'],
                    'in_w_pcs' => $data['total_weight'] / $data['qty'],
                    'in_total_weight' => $data['total_weight'],
                    'remark' => $data['remark'],
                    'challan_prefix' => $data['challan_prefix'],
                    'challan_no' => $data['ch_no'],
                    'material_used_id' => $inwardData->material_used_id,
                    'issue_batch_no' => $inwardData->issue_batch_no,
                    'issue_material_qty' => $inwardData->issue_material_qty,
                    'job_process_ids' => $inwardData->job_process_ids,
                    'rework_process_id' => $reworkProcess,
                    'created_by' => $data['created_by'],
                    'accepted_by' => $data['created_by'],
                    'accepted_at' => $data['entry_date'] . " " . date("H:i:s")
                ];
                $saveInward = $this->store($this->productionTrans, $inwardPostData);

                // If First Process then Maintain Batchwise Rowmaterial 
                $jqry['select'] = 'process';
                $jqry['where']['id'] = $data['job_card_id'];
                $jqry['tableName'] = $this->jobCard;
                $jobData = $this->row($jqry);
                $jobProcesses = explode(",", $jobData->process);

                if ($nextApproval->out_process_id == $jobProcesses[0]) :
                    // Update Used Stock in Job Material Used 
                    $setData = array();
                    $setData['tableName'] = $this->jobUsedMaterial;
                    $setData['where']['id'] = $materialUsedId;
                    $setData['set']['used_qty'] = 'used_qty, + ' . $data['total_weight'];
                    $qryresult = $this->setValue($setData);
                endif;
            endif;
        endif;

        return $saveInward['insert_id'];
    }

    public function getJobworkOrderList(){
        $data['select'] = "jobwork_order.*";
        $data['tableName'] = $this->jobworkOrder;
        return $this->rows($data);
    }

    //Created By Nayan @18/05/2022
    public function getJobWorkOrderTransData($job_order_trans_id){
        $data['tableName'] = $this->jobwork_order_trans;
        $data['select'] = "jobwork_order_trans.*";
        $data['leftJoin']['jobwork_order'] = "jobwork_order.id = jobwork_order_trans.order_id";
        $data['where']['jobwork_order_trans.id'] = $job_order_trans_id;
        $data['where']['jobwork_order_trans.is_active'] = 1;       
        return $this->row($data);
    }
    
    //Created By Karmi @14/05/2022
    public function getSameOrderTrans($item_id,$converted_product,$process_id,$vendor_id,$job_order_trans_id){
        $data['tableName'] = $this->jobwork_order_trans;
        $data['select'] = "jobwork_order_trans.*";
        $data['leftJoin']['jobwork_order'] = "jobwork_order.id = jobwork_order_trans.order_id";
        $data['where']['jobwork_order_trans.item_id'] = $item_id;
        $data['where']['jobwork_order_trans.converted_product'] = $converted_product;
        $data['where']['jobwork_order_trans.process_id'] = $process_id;
        $data['where']['jobwork_order.vendor_id'] = $vendor_id;
        $data['where']['jobwork_order_trans.order_id != '] = $job_order_trans_id;
        $data['where']['jobwork_order_trans.is_active'] = 1;
        $result = $this->row($data);
        //print_r($this->db->last_query());exit;
        return $result;
    }

    //Created By Karmi @10/05/2022
    public function getJobWorkForReturn($id){
        $data['tableName'] = $this->jobTransaction;
        $data['select'] = "jobwork_transaction.*,process_master_jobwork.process_name,item_master.item_name,item_master.full_name";
        $data['join']['process_master_jobwork'] =  "process_master_jobwork.id = jobwork_transaction.process_id";
        $data['join']['item_master'] = "item_master.id = jobwork_transaction.item_id";
        $data['where']['jobwork_transaction.id'] = $id;
        return $this->rows($data);
       
    }
    //Created By Karmi @11/05/2022
    public function getLocationListBasedOnItem($item_id){
        $data['tableName'] = $this->stockTrans;
        $data['select'] = "stock_transaction.location_id,sum(stock_transaction.qty) as stock,location_master.store_name,location_master.location,location_master.id";
        $data['join']['location_master'] = "location_master.id = stock_transaction.location_id";
        $data['where']['stock_transaction.stock_effect'] = 1;
        $data['where']['stock_transaction.item_id'] = $item_id;
        $data['group_by'][] = "stock_transaction.location_id";
        //print_f($this->db->last_query());
        return $this->rows($data);
    }

    //Created By Karmi @11/05/2022
    public function locationWiseBatch($item_id,$location_id){
		$data['tableName'] = "stock_transaction";
		$data['select'] = "SUM(qty) as qty,batch_no";
		$data['where']['item_id'] = $item_id;
		$data['where']['location_id'] = $location_id;
        $data['where']['stock_effect'] = 1;
		$data['order_by']['id'] = "asc";
		$data['group_by'][] = "batch_no";
		return $this->rows($data);
	}

    //Created By Karmi @10/05/2022
    public function getJobWorkReturnData($id){
        $data['tableName'] = $this->jobTransaction;
        $data['select'] = "jobwork_transaction.*,process_master_jobwork.process_name,item_master.item_name,item_master.full_name";
        $data['join']['process_master_jobwork'] =  "process_master_jobwork.id = jobwork_transaction.process_id";
        $data['join']['item_master'] =  "item_master.id = jobwork_transaction.item_id";
        $data['where']['jobwork_transaction.ref_id'] = $id;
        $data['where']['jobwork_transaction.entry_type'] = 2;
        return $this->rows($data);
    }

    //Created By NYN @15/05/2022  
    public function getJobOrderByVendor($vendor_id){
        $data['tableName'] = $this->jobwork_order_trans;
        $data['select'] = "jobwork_order_trans.*,process_master_jobwork.process_name,item_master.item_name,item_master.full_name,unit_master.unit_name";
        $data['leftJoin']['process_master_jobwork'] =  "process_master_jobwork.id = jobwork_order_trans.process_id";
        $data['leftJoin']['item_master'] =  "item_master.id = jobwork_order_trans.item_id";
        $data['leftJoin']['jobwork_order'] = "jobwork_order.id = jobwork_order_trans.order_id";
        $data['leftJoin']['unit_master'] =  "unit_master.id = jobwork_order_trans.com_unit";
        $data['where']['jobwork_order.vendor_id'] = $vendor_id;
        $data['where']['jobwork_order_trans.is_active'] = 1;
        $data['group_by'][] = 'jobwork_order_trans.item_id';
        $data['group_by'][] = 'jobwork_order_trans.process_id';
        return $this->rows($data);
    }
    
    //Created By Karmi @17/05/2022
    public function getJobworkTransForPrint($id){
        $data['tableName'] = $this->jobTransaction;
        $data['select'] = "jobwork_transaction.*,process_master_jobwork.process_name,item_master.item_name,item_master.full_name,item_master.item_code,jobwork.trans_prefix,jobwork.trans_no,jobwork.trans_date,jobwork.vendor_id,jobwork.vehicle_no,jobwork.ewb_no,unit_master.unit_name";
        $data['leftJoin']['jobwork'] =  "jobwork.id = jobwork_transaction.jobwork_id";
        $data['leftJoin']['process_master_jobwork'] =  "process_master_jobwork.id = jobwork_transaction.process_id";
        $data['leftJoin']['item_master'] =  "item_master.id = jobwork_transaction.item_id";
        $data['leftJoin']['unit_master'] =  "unit_master.id = jobwork_transaction.unit_id";
        $data['where']['jobwork_transaction.id'] = $id;
        return $this->row($data);
    }
    
    public function updateChallanPrintStatus($id){
        return $this->edit($this->jobTransaction,['id'=>$id],['print_status'=>date('Y-m-d H:i:s')]);
    }
    
    //Created By Karmi @17/05/2022
    public function getorderTrans($id){
        $data['tableName'] = $this->jobworkOrderTrans;
        $data['where']['id'] = $id;
        $result = $this->row($data);
        return $result;
    }

    //Created By Karmi @23/05/2022
    public function getorderTransBasedOnOrderId($id){
        $data['tableName'] = $this->jobworkOrderTrans;
        $data['where']['order_id'] = $id;
        $result = $this->row($data);
        return $result;
    }

    public function getJobWorkOrderTransItemWise($orderId,$itemId,$processId){
        $queryData = array();
        $queryData['tableName'] = $this->jobworkOrderTrans;
        $queryData['where']['order_id'] = $orderId;
        $queryData['where']['item_id'] = $itemId;
        $queryData['where']['process_id'] = $processId;
        $queryData['order_by']['id'] = "ASC";
        $queryData['limit'] = 1;
        $result = $this->row($queryData);
        return $result;
    }
    
    //Created By JP @06/06/2022
    public function vehicleSearch(){
        $queryData = array();
        $queryData['tableName'] = $this->jobWork;
        $queryData['select'] = "vehicle_no";
        $queryData['group_by'][] = "vehicle_no";
        $result = $this->rows($queryData);
        
		$searchResult = array();
        if(!empty($result)): $searchResult = array_column($result,'vehicle_no'); endif;
		return  $searchResult;
    }
    
    //Created By Karmi @04/05/2022
    public function getNextJobTransNo(){
        $data['tableName'] = $this->jobTransaction;
        $data['select'] = "MAX(job_trans_no) as jobTransNo";
        $jobTransNo = $this->specificRow($data)->jobTransNo;
		$nextJobTransNo = (!empty($jobTransNo))?($jobTransNo + 1):1;
		return $nextJobTransNo;
    }

    
    //Created By Karmi @27/05/2022
    public function approveReturn($id)
    {
        $returnData = $this->getJobworkTransBasedOnTransId($id);//print_r($returnData);exit;
        $masterData = $this->getJobWorkChallan($returnData->jobwork_id);

        $queryData = array(); 
        $queryData['tableName'] = $this->jobTransaction;
        $queryData['where']['id'] = $returnData->ref_id;
        $jobInwardData = $this->row($queryData);
        

        /*** UPDATE STOCK TRANSACTION DATA ***/
        $stockQueryData['id']="";
        $stockQueryData['location_id']=$jobInwardData->location_id;
        $stockQueryData['batch_no']=$jobInwardData->batch_no;
        $stockQueryData['trans_type']=2;
        $stockQueryData['item_id']=$returnData->item_id;
        $stockQueryData['qty'] = "-".$returnData->qty;
        $stockQueryData['ref_type']=19;
        $stockQueryData['ref_id']=$returnData->jobwork_id;
        $stockQueryData['trans_ref_id']=$id;
        $stockQueryData['ref_no']=$masterData->trans_prefix.$masterData->trans_no;
        $stockQueryData['ref_date']=$returnData->entry_date;
        $stockQueryData['stock_effect']=0;
        $stockQueryData['created_by']=$this->loginID;
        $this->store($this->stockTrans,$stockQueryData);

        $this->store($this->jobTransaction,['id'=>$id,'is_approve'=>$this->session->userdata('loginId')]);
        if($jobInwardData->qty == $jobInwardData->received_qty):
            $this->store($this->jobTransaction,['id'=>$jobInwardData->id,'is_approve'=>$this->session->userdata('loginId')]);
        endif;

        return ['status'=>1,'message'=>'Jobwork Approved Successfully..'];
        
    }

    //Created By Karmi @27/05/2022
    public function rejectReturn($id)
    {
        $returnData = $this->getJobworkTransBasedOnTransId($id);//print_r($returnData);exit;

        $this->remove($this->stockTrans,['ref_id'=>$returnData->jobwork_id,'trans_ref_id'=>$id,'ref_type'=>19]);
        $this->store($this->jobTransaction,['id'=>$id,'is_approve'=>0]);

        return ['status'=>1,'message'=>'Jobwork Rejected Successfully..'];

    }
     //Created By Karmi @27/06/2022
     public function getUnapprovedCount($id){
        $data['tableName'] = $this->jobTransaction;
        $data['where']['jobwork_transaction.ref_id'] = $id;
        $data['where']['jobwork_transaction.entry_type'] = 2;
        $data['where']['jobwork_transaction.is_approve'] = 0;
        return $this->rows($data);
    }
}
