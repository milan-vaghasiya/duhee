<?php
class ProductionModel extends MasterModel{
    private $jobCard = "job_card";
    private $productionTrans = "job_transaction";
    private $productionApproval = "job_approval";

    private $jobOutward = "job_outward";
    private $jobInward = "job_inward";
    private $jobRejection = "job_rejection";
    private $jobUsedMaterial = "job_used_material";
    private $jobMaterialReturn = "job_return_material";
    private $jobMaterialDispatch = "job_material_dispatch";
    private $itemMaster = "item_master";
    private $mahineMaster = "machine_master";
    private $productKit = "item_kit";
    private $employee = "employee_master";
    private $machineIdle = "machine_idle_logs";
    private $machineLog = "machine_log";

    public function getProductionList($id,$processId = ""){
        $data['tableName'] = $this->productionTrans;
        $data['select'] = "job_transaction.*,job_card.job_no,job_card.job_prefix,job_card.delivery_date,item_master.item_name as product_name,item_master.item_code as product_code,job_approval.trans_type";
        $data['join']['job_card'] = "job_transaction.job_card_id = job_card.id";
        $data['join']['job_approval'] = "job_transaction.job_approval_id = job_approval.id";
        $data['join']['item_master'] = "job_transaction.product_id = item_master.id";
        $data['where']['job_transaction.job_card_id'] = $id;
		$data['where']['job_transaction.vendor_id'] = 0;
        $data['where']['job_transaction.entry_type'] = 1;
        $data['where_in']['job_card.order_status'] = [1,2,4];

        if(!empty($processId))
            $data['where']['job_transaction.process_id'] = $processId;

        return $this->rows($data);
    }

    /* public function getProcessWiseProduction($job_id,$process_id){
        $data['tableName'] = $this->jobInward;
        $data['select'] = "SUM(in_qty) as total_in_qty,SUM(rework_qty) as total_rework_qty,SUM(rejection_qty) as total_rejection_qty,SUM(out_qty) as total_out_qty";
        $data['where']['job_inward.job_card_id'] = $job_id;
        $data['where']['job_inward.process_id'] = $process_id;
        $data['where']['is_delete'] = 0;
        $data['where']['trans_type'] = 0;
        $regularData = $this->row($data);

        $queryData = array();
        $queryData['tableName'] = $this->jobInward;
        $queryData['select'] = "SUM(in_qty) as total_in_qty,SUM(rework_qty) as total_rework_qty,SUM(rejection_qty) as total_rejection_qty,SUM(out_qty) as total_out_qty";
        $queryData['where']['job_inward.job_card_id'] = $job_id;
        $queryData['where']['job_inward.process_id'] = $process_id;
        $queryData['where']['is_delete'] = 0;
        $queryData['where']['trans_type'] = 1;
        $reworkData = $this->row($queryData);

        $result = new stdClass();
        $result->total_in_qty = $regularData->total_in_qty + $reworkData->total_in_qty;
        $result->rework_in_qty = $reworkData->total_in_qty;
        $result->regular_in_qty = $regularData->total_in_qty;
        $result->total_rework_qty = $reworkData->total_rework_qty + $regularData->total_rework_qty;
        $result->total_rejection_qty = $reworkData->total_rejection_qty + $regularData->total_rejection_qty;
        $result->total_out_qty = $reworkData->total_out_qty + $regularData->total_out_qty;

        return $result;
    } */

    public function getProcessWiseProduction($job_id,$process_id,$type = 0){
        $data['tableName'] = $this->productionApproval;
        $data['select'] = "job_approval.*";
        $data['where']['job_card_id'] = $job_id;
        $data['where']['in_process_id'] = $process_id;
        $data['where']['trans_type'] = $type;
        $data['where']['is_delete'] = 0;
        $result = $this->row($data);
        return $result;
    }

    public function getReworkData($job_id){
        $data['tableName'] = $this->productionApproval;
        $data['select'] = "job_approval.*";
        $data['where']['job_card_id'] = $job_id;
        $data['where']['trans_type'] = 1;
        $data['where']['is_delete'] = 0;
        $result = $this->rows($data);
        return $result;
    }

    public function acceptJob($id,$emp_id = 0){
        $data['tableName'] = $this->productionTrans;
        $data['where']['id'] = $id;
        $result = $this->row($data);

        if(!empty($result)):
            $this->edit($this->productionTrans,['id'=>$id],['accepted_by'=>$emp_id,'accepted_at'=>date("Y-m-d H:i:s")]);
            $this->edit($this->jobCard,['id'=>$result->job_card_id],['order_status'=>2]);
            return ['status'=>1,'message'=>"Job Accepted Successfully."];
        else:
            return ['status'=>0,'message'=>'Something went wrong...Please try again.'];
        endif;
    }  
	
    public function getShift(){
		$queryData['tableName'] = "shift_master";
		return $this->rows($queryData);
    }
    
    public function getOutwardTrans($data){//print_r($data);exit;
        $queryData['tableName'] = $this->productionTrans;
        $queryData['where']['ref_id'] = $data['ref_id'];
        $queryData['where']['entry_type'] = 2;
        $queryData['where']['process_id'] = $data['process_id'];
        $result = $this->rows($queryData);
		
        $dataRow = array();$html = "";
        if(!empty($result)): 
            $i=1;   
            foreach($result as $row):
                $transDate = date("d-m-Y",strtotime($row->entry_date));
                $operatorName = "";$machineNo = "";$shiftName = "";
                if(!empty($row->operator_id)):
                    $queryData=array();
                    $queryData['where']['id'] = $row->operator_id;
                    $queryData['tableName'] = $this->employee;
                    $operatorName = $this->row($queryData)->emp_name;
                endif;
				if(!empty($row->machine_id)):
                    $mqData=array();
                    $mqData['where']['id'] = $row->machine_id;
                    $mqData['tableName'] = $this->itemMaster;
                    $machineNo = $this->row($mqData)->item_code;
                endif;
				if(!empty($row->shift_id)):
                    $mqData=array();
                    $mqData['where']['id'] = $row->shift_id;
                    $mqData['tableName'] = 'shift_master';
                    $shiftName = $this->row($mqData)->shift_name;
                endif;
                $deleteBtn = '';
				$deleteBtn = '<button type="button" onclick="trashOutward('.$row->id.','.$row->in_qty.');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
                $html .= '<tr class="text-center">
                            <td>'.$i++.'</td>
                            <td>'.$transDate.'</td>
                            <td class="challanNoCol">'.$row->in_challan_no.'</td>
                            <td class="challanNoCol">'.$row->charge_no.'</td>
                            <td>'.$row->out_qty.'</td>                            
                            <td>'.$row->ud_qty.'</td>
                            <td>'.$row->rejection_qty.'</td>
                            <td>'.$row->rework_qty.'</td>
                            <td>'.$row->production_time.'</td>
                            <td>'.$shiftName.'</td>
                            <td>'.$operatorName.'</td>
                            <td>'.$machineNo.'</td>
                            <td class="text-center" style="width:10%;">'.$deleteBtn.'</td>
                        </tr>';
                $dataRow[] = $row;
            endforeach;
        endif;
        return ['sendData'=>$html,'outwardTrans'=>$dataRow];
    } 
    
    public function getJobInwardDataById($id){
		$queryData['tableName'] = $this->productionTrans;
		$queryData['where']['id'] = $id;
		return $this->row($queryData);
    }
    
    public function getMachineTrans($data){
        $queryData['tableName'] = $this->productionTrans;
        $queryData['select'] = 'job_transaction.production_time,job_transaction.operator_id,job_card.job_no,job_card.job_prefix,job_card.job_no, process_master.process_name';
        $queryData['join']['job_card'] = 'job_card.id = job_transaction.job_card_id';
        $queryData['join']['process_master'] = 'process_master.id = job_transaction.process_id';
        $queryData['where']['job_transaction.entry_date'] = $data['entry_date'];
        $queryData['where']['job_transaction.machine_id'] = $data['machine_id'];
        $queryData['where']['job_transaction.shift_id'] = $data['shift_id'];
        $queryData['where']['job_transaction.entry_type'] = $data['entry_type'];
        return $this->rows($queryData);	
	}
    
    public function saveProductionTrans($data){
        $jobCardData = $this->jobcard->getJobcard($data['job_card_id']);

        $queryData['tableName'] = $this->productionTrans;
        $queryData['where']['id'] = $data['ref_id'];
        $inwardData = $this->row($queryData);
        //print_r($data);exit;
        if(!empty($inwardData->job_process_ids)):
            $pendingQty = $inwardData->in_qty - ($inwardData->out_qty + $inwardData->rework_qty + $inwardData->rejection_qty);
            $totalOutQty = $data['out_qty'] + $data['rejection_qty'] + $data['rework_qty'];
            if($pendingQty < $totalOutQty): 
                return ['status'=>0,'message'=>['outQty'=>'Qty not avalible for outward.']];
            endif;

            $ref_id = $data['ref_id'];
            $process_id = $data['process_id'];
            $jobProcess = explode(",",$inwardData->job_process_ids);
            $i=0;$recordId=0;$countProcess=count($jobProcess);
            
            foreach($jobProcess as $key=>$value):
                if($i != 0):
                    $data['ref_id'] = $recordId;
                    $data['process_id'] = $value;
                endif;
                $recordId = $this->jobRecordInserts($data,((($countProcess-1) == $key)?true:false));
                $i++;
            endforeach;

            return ['status'=>1,'message'=>'Outward saved successfully.','sendData'=>$this->getOutwardTrans(['ref_id'=>$ref_id,'process_id'=>$process_id])['sendData']];
        else:

            $pendingQty = $inwardData->in_qty - ($inwardData->out_qty + $inwardData->rework_qty + $inwardData->rejection_qty);
            $totalOutQty = $data['out_qty'] + $data['rejection_qty'] + $data['rework_qty'];
            if($pendingQty >= $totalOutQty):    
                $queryData=array();
                $queryData['tableName'] = $this->productionApproval;
                $queryData['where']['id'] = $inwardData->job_approval_id;
                $approvalData = $this->row($queryData);    
                
                $processes = explode(",",$jobCardData->process);
                
                //get next Process
                //$nextProcess = 0;
                if($processes[count($processes) - 1] != $data['process_id']):
                    /* foreach($processes as $key=>$value):
                        if($data['process_id']==$value){$nextProcess = $processes[$key + 1];break;}
                    endforeach; */
                    $jobCardUpdateData['total_ud_qty'] = $jobCardData->total_ud_qty + $data['ud_qty'];
                    $this->edit($this->jobCard,['id'=>$data['job_card_id']],$jobCardUpdateData);
                else:
                    $jobCardUpdateData['total_out_qty'] = $jobCardData->total_out_qty + $data['out_qty'];
                    $jobCardUpdateData['total_ud_qty'] = $jobCardData->total_ud_qty + $data['ud_qty'];
                    $completeJobQty = $jobCardUpdateData['total_out_qty'] + $jobCardData->total_reject_qty + $jobCardData->total_rework_qty;
                    if($jobCardData->qty <= $completeJobQty):
                        $jobCardUpdateData['order_status'] = 4;
                    endif;
                    $this->edit($this->jobCard,['id'=>$data['job_card_id']],$jobCardUpdateData);
                       
                    if(empty($jobCardData->pre_disp_inspection)):
                        $setData = array();
                        $setData['tableName'] = $this->jobCard;
                        $setData['where']['id'] = $data['job_card_id'];
                        $setData['set']['unstored_qty'] = 'unstored_qty, + '.$data['out_qty'];
                        $this->setValue($setData);
                    else:
                        $setData = array();
                        $setData['tableName'] = $this->itemMaster;
                        $setData['where']['id'] = $data['product_id'];
                        $setData['set']['pending_inspection_qty'] = 'pending_inspection_qty, + '.$data['out_qty'];
                        $this->setValue($setData);
                    endif;  
                endif;
                
    			$juq['select'] = 'wp_qty';
    			$juq['tableName'] = $this->jobUsedMaterial;
    			$juq['where']['id'] = $data['material_used_id'];
    			$wpQty = $this->row($juq)->wp_qty;
    			$imq = round(($data['out_qty'] * $wpQty),3);
    			$data['issue_material_qty'] = $imq;
                $data['job_approval_id'] = $inwardData->job_approval_id;
                $data['in_qty'] = $data['out_qty'] + $data['rejection_qty'] + $data['rework_qty'];
                $this->store($this->productionTrans,$data);
    
                $this->edit($this->productionTrans,['id'=>$data['ref_id']],['out_qty'=>($inwardData->out_qty + $data['out_qty']),'ud_qty'=>($inwardData->ud_qty + $data['ud_qty']),'rework_qty'=>($inwardData->rework_qty + $data['rework_qty']),'rejection_qty'=>($inwardData->rejection_qty + $data['rejection_qty'])]);

                $setData = array();
                $setData['tableName'] = $this->productionApproval;
                $setData['where']['in_process_id'] = $data['process_id'];
                $setData['where']['job_card_id'] = $inwardData->job_card_id;
                if(!empty($inwardData->rework_process_id)):
                    $lastReworkProcess = explode(",",$inwardData->rework_process_id);
                    if($lastReworkProcess[count($lastReworkProcess) - 1] != $data['process_id']):
                        $setData['where']['trans_type'] = 1;
                        $setData['where']['ref_id'] = $approvalData->ref_id;
                    else:
                        $setData['where']['trans_type'] = 0;
                        $setData['where']['ref_id'] = 0;
                    endif;
                else:
                    $setData['where']['trans_type'] = 0;
                    $setData['where']['ref_id'] = $approvalData->ref_id;
                endif;
                $setData['set']['in_qty'] = 'in_qty, + '.$data['out_qty'];
                $this->setValue($setData);
    
                return ['status'=>1,'message'=>'Outward saved successfully.','sendData'=>$this->getOutwardTrans(['ref_id'=>$data['ref_id'],'process_id'=>$data['process_id']])['sendData']];
            else:
                return ['status'=>0,'message'=>['outQty'=>'Qty not avalible for outward.']];
            endif;
        endif;
    }

    public function jobRecordInserts($data,$is_last){
        $jobCardData = $this->jobcard->getJobcard($data['job_card_id']);

        $queryData['tableName'] = $this->productionTrans;
        $queryData['where']['id'] = $data['ref_id'];
        $inwardData = $this->row($queryData);

        $queryData=array();
        $queryData['tableName'] = $this->productionApproval;
        $queryData['where']['id'] = $inwardData->job_approval_id;
        $approvalData = $this->row($queryData);

        $processes = explode(",",$jobCardData->process);

        //get next Process
        /* $nextProcess = 0; */
        if($processes[count($processes) - 1] != $data['process_id']):
            /* foreach($processes as $key=>$value):
                if($data['process_id']==$value){$nextProcess = $processes[$key + 1];break;}
            endforeach; */
            $jobCardUpdateData['total_ud_qty'] = $jobCardData->total_ud_qty + $data['ud_qty'];
            $this->edit($this->jobCard,['id'=>$data['job_card_id']],$jobCardUpdateData);
        else:
            $jobCardUpdateData['total_out_qty'] = $jobCardData->total_out_qty + $data['out_qty'];
            $jobCardUpdateData['total_ud_qty'] = $jobCardData->total_ud_qty + $data['ud_qty'];
            $completeJobQty = $jobCardUpdateData['total_out_qty'] + $jobCardData->total_reject_qty + $jobCardData->total_rework_qty;
            if($jobCardData->qty <= $completeJobQty):
                $jobCardUpdateData['order_status'] = 4;
            endif;
            $this->edit($this->jobCard,['id'=>$data['job_card_id']],$jobCardUpdateData);
                   
            if(empty($jobCardData->pre_disp_inspection)):
                $setData = Array();
                $setData['tableName'] = $this->jobCard;
                $setData['where']['id'] = $data['job_card_id'];
                $setData['set']['unstored_qty'] = 'unstored_qty, + '.$data['out_qty'];
                $this->setValue($setData);
            else:
                $itemData = $this->item->getItem($data['product_id']);
                $stockQty['pending_inspection_qty'] = $itemData->pending_inspection_qty + $data['out_qty'];
                $this->edit($this->itemMaster,['id'=>$data['product_id']],$stockQty);
            endif;  
        endif;
        
        $juq['select'] = 'wp_qty';
        $juq['tableName'] = $this->jobUsedMaterial;
        $juq['where']['id'] = $data['material_used_id'];
        $wpQty = $this->row($juq)->wp_qty;
        $imq = round((($data['out_qty']) * $wpQty),3);
        
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
            'in_qty' => $data['out_qty'] + $data['rejection_qty'] + $data['rework_qty'],
            'in_w_pcs' => "",
            'in_total_weight' => "",
            'rework_qty' => $data['rework_qty'],
            'rejection_qty' => $data['rejection_qty'],
            'out_qty' => $data['out_qty'],
            'ud_qty' => $data['ud_qty'],
            'w_pcs' => $data['w_pcs'],
            'total_weight' => $data['total_weight'],
            'rejection_reason' => $data['rejection_reason'],
            'rejection_stage' => $data['rejection_stage'],
            'remark' => $data['remark'],
            'challan_prefix' => "",
            'challan_no' => "",
            'in_challan_no' => $data['challan_no'],
            'charge_no' => $data['charge_no'],
            'material_used_id' => $inwardData->material_used_id,
            'issue_batch_no' => $inwardData->issue_batch_no,
            'issue_material_qty' => $imq,
            'challan_status' => $inwardData->challan_status,
            'operator_id' => $data['operator_id'],
            'machine_id' => $data['machine_id'],
            'shift_id' => $data['shift_id'],
            "production_time" => $data['production_time'],
            "cycle_time" => $data['cycle_time'],
            "job_process_ids" => $inwardData->job_process_ids,
            "rework_process_id" => $data['rework_process_id'],
            "created_by" => $data['created_by']
        ];
        $otData = $this->store($this->productionTrans,$outwardPostData);

        $this->edit($this->productionTrans,['id'=>$data['ref_id']],['out_qty'=>($inwardData->out_qty + $data['out_qty']),'ud_qty'=>($inwardData->ud_qty + $data['ud_qty']),'rework_qty'=>($inwardData->rework_qty + $data['rework_qty']),'rejection_qty'=>($inwardData->rejection_qty + $data['rejection_qty'])]);

        $setData = array();
        $setData['tableName'] = $this->productionApproval;
        $setData['where']['in_process_id'] = $data['process_id'];
        $setData['where']['job_card_id'] = $inwardData->job_card_id;
        if(!empty($inwardData->rework_process_id)):
            $lastReworkProcess = explode(",",$inwardData->rework_process_id);
            if($lastReworkProcess[count($lastReworkProcess) - 1] != $data['process_id']):
                $setData['where']['trans_type'] = 1;
                $setData['where']['ref_id'] = $approvalData->ref_id;
            else:
                $setData['where']['trans_type'] = 0;
                $setData['where']['ref_id'] = 0;
            endif;
        else:
            $setData['where']['trans_type'] = 0;
            $setData['where']['ref_id'] = $approvalData->ref_id;
        endif;
        $setData['set']['in_qty'] = 'in_qty, + '.$data['out_qty'];
        if($is_last != true):
            $setData['set']['out_qty'] = 'out_qty, + '.$data['out_qty'];
        endif;
        $this->setValue($setData);

        $saveInward['insert_id'] = 0;
        if($is_last != true):   
            $queryData = array();
            $queryData['tableName'] = $this->productionApproval;
            $queryData['select'] = "id,out_process_id";
            $queryData['where']['in_process_id'] = $data['process_id'];
            $queryData['where']['job_card_id'] = $inwardData->job_card_id;
            if(!empty($inwardData->rework_process_id)):
                $lastReworkProcess = explode(",",$inwardData->rework_process_id);
                if($lastReworkProcess[count($lastReworkProcess) - 1] != $data['process_id']):
                    $queryData['where']['trans_type'] = 1;
                    $queryData['where']['ref_id'] = $approvalData->ref_id;
                else:
                    $queryData['where']['trans_type'] = 0;
                    $queryData['where']['ref_id'] = 0;
                endif;
            else:
                $queryData['where']['trans_type'] = 0;
                $queryData['where']['ref_id'] = $approvalData->ref_id;
            endif;
            $nextApproval = $this->row($queryData);   

            if(!empty($nextApproval->out_process_id)):
                $data['challan_prefix'] = '';$data['ch_no'] = 0;$jobWorkProcess="";
                if(!empty($inwardData->vendor_id)):
                    $data['challan_prefix'] = 'JO/'.$this->shortYear.'/';
                    $data['ch_no'] = $this->processApprove->nextJobWorkChNo();
                endif;
                $outwardData = $this->processApprove->getOutward($nextApproval->id);
                $reworkProcess = "";
                if($outwardData->trans_type == 1):
                    $queryData = array();
                    $queryData['tableName'] = $this->productionTrans;
                    $queryData['where']['id'] = $outwardData->ref_id;
                    $refInwardData = $this->row($queryData);
                    $reworkProcess = $refInwardData->rework_process_id;
                endif;
                $inwardData = [
                    'id' => "",
                    'entry_type' => 1,
                    'entry_date' => $data['entry_date'],
                    'job_card_id' => $data['job_card_id'],
                    'job_approval_id' => $nextApproval->id,
                    'job_order_id' => $inwardData->job_order_id,
                    'vendor_id' => $inwardData->vendor_id,
                    'process_id' => $nextApproval->out_process_id,            
                    'product_id' => $data['product_id'],            
                    'in_qty' => $data['out_qty'],
                    'in_w_pcs' => $data['w_pcs'],
                    'in_total_weight' => $data['total_weight'],
                    'remark' => $data['remark'],
                    'challan_prefix' => $data['challan_prefix'],
                    'challan_no' => $data['ch_no'],
                    'material_used_id' => $data['material_used_id'],
                    'issue_batch_no' => $data['issue_batch_no'],
                    'issue_material_qty' => $data['issue_material_qty'],
                    'job_process_ids' => $inwardData->job_process_ids,  
                    'rework_process_id' => $reworkProcess,
                    'created_by' => $data['created_by'],
                    'accepted_by' => $data['created_by'],
                    'accepted_at' => $data['entry_date']." ".date("H:i:s")
                ];
                $saveInward = $this->store($this->productionTrans,$inwardData);	
                
                $this->edit($this->productionApproval,['id'=>$data['ref_id']],['out_qty'=>($data['out_qty'] + $outwardData->out_qty)]);
                
                /*** If First Process then Maintain Batchwise Rowmaterial ***/
                $jqry['select'] = 'process';
                $jqry['where']['id'] = $data['job_card_id'];
                $jqry['tableName'] = $this->jobCard;
                $jobData = $this->row($jqry); 
                $jobProcesses = explode(",",$jobData->process);
                
                if($nextApproval->out_process_id == $jobProcesses[0]):
                    /* Update Used Stock in Job Material Used */
                    $setData = Array();
                    $setData['tableName'] = $this->jobUsedMaterial;
                    $setData['where']['id'] = $data['material_used_id'];
                    $setData['set']['used_qty'] = 'used_qty, + '.$data['req_qty'];
                    $qryresult = $this->setValue($setData);
                endif;
            endif;
        endif;

        return $saveInward['insert_id'];
    }

    public function deleteProductionTrans($id){
        $data['tableName'] = $this->productionTrans;
        $data['where']['id'] = $id;
        $transData = $this->row($data);

        $transQty = $transData->out_qty + $transData->rejection_qty + $transData->rework_qty;

        $queryData = array();
        $queryData['tableName'] = $this->productionApproval;
        $queryData['where']['id'] = $transData->job_approval_id;
        $approvalData = $this->row($queryData);

        $queryData = array();
        $queryData['tableName'] = $this->productionApproval;
        $queryData['where']['in_process_id'] = $transData->process_id;
        $queryData['where']['job_card_id'] = $transData->job_card_id;
        $queryData['where']['trans_type'] = $approvalData->trans_type;
        $queryData['where']['ref_id'] = $approvalData->ref_id;
        $nextApprovalData = $this->row($queryData);

        $pendingQty = $nextApprovalData->in_qty - ($nextApprovalData->total_rework_qty + $nextApprovalData->total_rejection_qty + $nextApprovalData->out_qty);
        
        if($transQty > $pendingQty):
            return ['status'=>0,'message'=>"You can't delete this outward because This outward forwared to next process."];
        else:
            $jobCardData = $this->jobcard->getJobcard($transData->job_card_id);

            $processes = explode(",",$jobCardData->process);
            //check last process
            if($processes[count($processes) - 1] == $transData->process_id):
                $jobCardUpdateData['total_out_qty'] = $jobCardData->total_out_qty - $transData->out_qty;
                $jobCardUpdateData['total_ud_qty'] = $jobCardData->total_ud_qty - $transData->ud_qty;
                $completeJobQty = $jobCardUpdateData['total_out_qty'] + $jobCardData->total_reject_qty + $jobCardData->total_rework_qty;
                if($jobCardData->qty != $completeJobQty):
                    $jobCardUpdateData['order_status'] = 2;
                endif;
                $this->edit($this->jobCard,['id'=>$jobCardData->id],$jobCardUpdateData);

                                    
                if(empty($jobCardData->pre_disp_inspection)):
                    $setData = Array();
                    $setData['tableName'] = $this->jobCard;
                    $setData['where']['id'] = $jobCardData->id;
                    $setData['set']['unstored_qty'] = 'unstored_qty, - '.$transData->out_qty;
                    $this->setValue($setData);
                else:
                    $setData = Array();
                    $setData['tableName'] = $this->itemMaster;
                    $setData['where']['id'] = $transData->product_id;
                    $setData['set']['pending_inspection_qty'] = 'pending_inspection_qty, - '.$transData->out_qty;
                    $this->setValue($setData);
                endif;                    
                
            else:
                $jobCardUpdateData['total_ud_qty'] = $jobCardData->total_ud_qty - $transData->ud_qty;
                $this->edit($this->jobCard,['id'=>$jobCardData->id],$jobCardUpdateData);
            endif;          

            $queryData = array();
            $queryData['tableName'] = $this->productionTrans;
            $queryData['where']['id'] = $transData->ref_id;
            $inwData = $this->row($queryData);
            $this->edit($this->productionTrans,['id'=>$transData->ref_id],['out_qty'=>($inwData->out_qty - $transData->out_qty),'ud_qty'=>($inwData->ud_qty - $transData->ud_qty)]);

            $setData = array();
            $setData['tableName'] = $this->productionApproval;
            $setData['where']['in_process_id'] = $transData->process_id;
            $setData['where']['job_card_id'] = $transData->job_card_id;
            $setData['where']['trans_type'] = $approvalData->trans_type;
            if(!empty($transData->rework_process_id)):
                $lastReworkProcess = explode(",",$transData->rework_process_id);
                if($lastReworkProcess[count($lastReworkProcess) - 1] != $transData->process_id):
                    $setData['where']['trans_type'] = 1;
                    $setData['where']['ref_id'] = $approvalData->ref_id;
                else:
                    $setData['where']['trans_type'] = 0;
                    $setData['where']['ref_id'] = 0;
                endif;
            else:
                $setData['where']['trans_type'] = 0;
                $setData['where']['ref_id'] = $approvalData->ref_id;
            endif;
            $setData['set']['in_qty'] = 'in_qty, - '.$transData->out_qty;
            //$setData['set']['total_rejection_qty'] = 'total_rejection_qty, + '.$transData->rejection_qty;
            //$setData['set']['total_rework_qty'] = 'total_rework_qty, + '.$transData->rework_qty;
            $this->setValue($setData);

            $result = $this->trash($this->productionTrans,['id'=>$id],'Outward');
            $result['sendData'] = $this->getOutwardTrans(['ref_id'=>$inwData->id,'process_id'=>$transData->process_id])['sendData'];
            return $result;
        endif;

    }

    public function getReturnOrScrapeTrans($data){
        $queryData['tableName'] = $this->jobMaterialReturn;
        $queryData['select'] = "job_return_material.*,item_master.item_name,unit_master.unit_name";
        $queryData['join']['item_master'] = "item_master.id = job_return_material.item_id";
        $queryData['join']['unit_master'] = "unit_master.id = item_master.unit_id";
        $queryData['where']['job_return_material.type'] = $data['type'];
        $queryData['where']['job_return_material.job_card_id'] = $data['job_card_id'];
        $queryData['where']['job_return_material.job_inward_id'] = $data['ref_id'];
        $queryData['where']['job_return_material.process_id'] = $data['process_id'];
        $result = $this->rows($queryData);

        $i=1;$html="";$functionName = "";
        foreach($result as $row):
            $functionName = ($data['type'] == 1)?"deleteReturn":"deleteScrape";
            $button = '<button type="button" onclick="'.$functionName.'('.$row->id.','.$row->qty.');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
            $operatorName = "";
            if(!empty($row->operator_id)):
                $queryData=array();
                $queryData['where']['id'] = $row->operator_id;
                $queryData['tableName'] = $this->employee;
                $operatorName = $this->row($queryData)->emp_name;
            endif;	
			$html .= '<tr>
                        <td style="width:5%;">'.$i++.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$row->qty.' ('.$row->unit_name.')</td>
                        <td>'.$row->remark.'</td>
                        <td class="operatorCol">'.$operatorName.'</td>
						<td class="text-center" style="width:10%;">
							'.$button.'
						</td>
					</tr>';
		endforeach;

        $sendData['result'] = $result;
        $sendData['resultHtml'] = $html;
        $sendData['itemOption'] = $this->getReturnOrScrapeItemList($data['job_card_id'],$data['process_id']);        
		return $sendData;
    }

    public function getReturnOrScrapeItemList($job_id,$process_id){
        $jobCardData = $this->jobcard->getJobcard($job_id);
        $process = explode(',',$jobCardData->process);

        $data['select'] = "item_kit.*,item_master.item_name";
        $data['join']['item_master'] = "item_master.id = item_kit.ref_item_id";
        $data['where']['item_kit.item_id'] = $jobCardData->product_id;
        $data['where']['item_kit.kit_type'] = 0;
        if($process_id == $process[0]):
            $data['where_in']['item_kit.process_id'] = [$process_id,0];
        else:
            $data['where']['item_kit.process_id'] = $process_id;
        endif;
        $data['tableName'] = $this->productKit;
        $productKitData = $this->rows($data);

        $html = '<option value="">Select Item</option>';
        foreach($productKitData as $row):                
            if(!empty($row->process_id)):
                $html .= '<option value="'.$row->ref_item_id.'" data-ptrasn_id="0">'.$row->item_name.'</option>';
            else:
                $html .= '<option value="'.$row->ref_item_id.'" data-ptrasn_id="'.$jobCardData->ptrans_id.'">'.$row->item_name.'</option>';
            endif;                
        endforeach;

        return $html;
    }

    public function returnOrScrapeSave($data){
        $returnData = [
            'id' => '',
            'entry_date' => (isset($data['entry_date']))?$data['entry_date']:"",
            'type' => $data['trans_type'],
            'job_card_id' => $data['job_card_id'],
            'job_inward_id' => $data['ref_id'],
            'process_id' => $data['process_id'],
            'item_id' => $data['item_id'],
            'qty' => $data['qty'],
            'remark' => $data['remark'],
            'operator_id' => $data['operator_id'],
            'machine_id' => $data['machine_id'],
            'created_by' => $data['created_by'],
        ];
        $save = $this->store($this->jobMaterialReturn,$returnData);
		
        if($data['trans_type'] == 1):
            $stockTrans = [
                'id' => "",
                'location_id' => $data['location_id'],
                'batch_no' => $data['batch_no'],
                'trans_type' => 1,
                'item_id' => $data['item_id'],
                'qty' => $data['qty'],
                'ref_type' => 10,
                'ref_id' => $save['insert_id'],
                'ref_no' => 'JMR/'.$save['insert_id'],
                'ref_date' => (isset($data['entry_date']))?$data['entry_date']:date("Y-m-d"),
                'created_by' => $data['created_by']
            ];
            $this->store("stock_transaction",$stockTrans);

            $setData = Array();
			$setData['tableName'] = $this->itemMaster;
			$setData['where']['id'] = $data['item_id'];
			$setData['set']['qty'] = 'qty, + '.$data['qty'];
			$qryresult = $this->setValue($setData);
        endif;
        $msg = ($data['trans_type'] == 1)?"Return Stock":"Scrape";
        return ['status'=>1,'message'=>$msg." saved successfully.",'result'=>$this->getReturnOrScrapeTrans(['type'=>$data['trans_type'],'process_id'=>$data['process_id'],'ref_id'=>$data['ref_id'],'job_card_id'=>$data['job_card_id']])];
    }

    public function deleteRetuenOrScrapeItem($id){
        $data['tableName'] = $this->jobMaterialReturn;
        $data['where']['id'] = $id;
        $returnData = $this->row($data);

        if($returnData->type == 1):
            $this->remove("stock_transaction",['ref_type'=>10,'ref_id'=>$id]);

            $setData = Array();
			$setData['tableName'] = $this->itemMaster;
			$setData['where']['id'] = $returnData->item_id;
			$setData['set']['qty'] = 'qty, + '.$returnData->qty;
			$qryresult = $this->setValue($setData);
        endif;

        $this->trash($this->jobMaterialReturn,['id'=>$id]);

        $msg = ($returnData->type == 1)?"Return Stock":"Scrape";
        return ['status'=>1,'message'=>$msg." deleted successfully.",'result'=>$this->getReturnOrScrapeTrans(['type'=>$returnData->type,'process_id'=>$returnData->process_id,'ref_id'=>$returnData->job_inward_id,'job_card_id'=>$returnData->job_card_id])];
    }

    
	
	public function getProductProcessRow($data){
		$data['tableName'] = 'product_process';
		$data['where']['item_id'] = $data['product_id'];
		$data['where']['process_id'] = $data['process_id'];
		return $this->row($data);
	}
	
	
	
	/*** Job Card Print ***/
    public function getJobTransactions1($job_id,$type=false){
        $queryData['tableName'] = $this->jobOutward;
        $queryData['select'] = 'job_outward.*,process_master.process_name';
        $queryData['join']['process_master'] = 'process_master.id = job_outward.in_process_id';
        $queryData['where']['job_outward.job_card_id'] = $job_id;
		if($type){$queryData['where']['vendor_id != '] = 0;}
        else{$queryData['where']['vendor_id'] = 0;}
		$queryData['order_by']['job_outward.entry_date'] = 'ASC';
		$queryData['order_by']['job_outward.id'] = 'ASC';
        $result = $this->rows($queryData);
		
        $dataRow = array();$html = "";
        if(!empty($result)): 
            $i=1;   
            foreach($result as $row):
                $transDate = date("d-m-Y",strtotime($row->entry_date));
                $transType = ($row->trans_type == 0)?"Regular":"Rework";
                $operatorName = "";$machineNo = "";$shiftName = "";$row->operatorName = "";$row->machineNo = "";$row->shiftName = "";
                if(!empty($row->operator_id)):
                    $queryData=array();
                    $queryData['where']['id'] = $row->operator_id;
                    $queryData['tableName'] = $this->employee;
                    $operatorName = $this->row($queryData)->emp_name;
					$row->operatorName = $operatorName;
                endif;
				if(!empty($row->machine_id)):
                    $mqData=array();
                    $mqData['where']['id'] = $row->machine_id;
                    $mqData['tableName'] = $this->itemMaster;
                    $machineNo = $this->row($mqData)->item_code;
					$row->machineNo = $machineNo;
                endif;
				if(!empty($row->machine_id)):
                    $mqData=array();
                    $mqData['where']['id'] = $row->shift_id;
                    $mqData['tableName'] = 'shift_master';
                    $shiftName = $this->row($mqData)->shift_name;
					$row->shiftName = $shiftName;
                endif;
                $deleteBtn = '';
                if($row->out_qty = "0.000" || empty($row->out_qty)):
					$deleteBtn = '<button type="button" onclick="trashOutward('.$row->id.','.$row->in_qty.');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
				endif;
                $html .= '<tr class="text-center">
                            <td>'.$i++.'</td>
                            <td>'.$row->process_name.'</td>
                            <td>'.$transDate.'</td>
                            <td>'.$operatorName.'</td>
                            <td>'.$shiftName.'</td>
                            <td>'.$machineNo.'</td>
                            <td>'.floatVal($row->in_qty + $row->ud_qty).'</td>
                            <td></td>
                            <td></td>
                            <td>'.$row->production_time.'</td>
                            <td>'.$row->remark.'</td>
                        </tr>';
                $dataRow[] = $row;
            endforeach;
        endif;
        return ['printTable'=>$html,'outwardTrans'=>$dataRow];
    }    

	/*** Job Card Print ***/
    public function getJobTransactions($job_id,$type=false){
		
        $dataRow = array();$html = "";$i=1; 
		$queryData = array();
		$queryData['tableName'] = $this->jobOutward;
		$queryData['select'] = 'job_outward.*,process_master.process_name';
		$queryData['join']['process_master'] = 'process_master.id = job_outward.in_process_id';
		$queryData['where']['job_outward.job_card_id'] = $job_id;
		if($type){$queryData['where']['job_outward.vendor_id != '] = 0;}
		else{$queryData['where']['job_outward.vendor_id'] = 0;}
		$queryData['order_by']['job_outward.entry_date'] = 'ASC';
		$queryData['order_by']['job_outward.id'] = 'ASC';
		$outData = $this->rows($queryData);
		
		$j=0;$prev_date="";$pid=0;
		foreach($outData as $row):
			$rjqty = "";$rwqty = "";
			if($prev_date != $row->entry_date or $pid != $row->in_process_id)
			{
				$queryData = array();
				$queryData['select'] = "SUM(qty) as qty";
				$queryData['tableName'] = $this->jobRejection;
				$queryData['where']['job_card_id'] = $job_id;
				$queryData['where']['process_id'] = $row->in_process_id;
				$queryData['where']['entry_date'] = $row->entry_date;
				$queryData['where']['type'] = 0;
				$rejectQty = $this->row($queryData);
				$rjqty = (!empty($rejectQty)) ? $rejectQty->qty : 0;
				
				$queryData = array();
				$queryData['select'] = "SUM(qty) as qty";
				$queryData['tableName'] = $this->jobRejection;
				$queryData['where']['job_card_id'] = $job_id;
				$queryData['where']['process_id'] = $row->in_process_id;
				$queryData['where']['entry_date'] = $row->entry_date;
				$queryData['where']['type'] = 1;
				$reworkQty = $this->row($queryData);
				$rwqty = (!empty($reworkQty)) ? $reworkQty->qty : 0;
			}
			
			$transDate = date("d-m-Y",strtotime($row->entry_date));
			$transType = ($row->trans_type == 0)?"Regular":"Rework";
			$operatorName = "";$machineNo = "";$shiftName = "";$row->operatorName = "";$row->machineNo = "";$row->shiftName = "";
			
			if(!empty($row->operator_id)):
				$queryData=array();
				$queryData['where']['id'] = $row->operator_id;
				$queryData['tableName'] = $this->employee;
				$operatorName = $this->row($queryData)->emp_name;
				$row->operatorName = $operatorName;
			endif;
			if(!empty($row->machine_id)):
				$mqData=array();
				$mqData['where']['id'] = $row->machine_id;
				$mqData['tableName'] = $this->itemMaster;
				$machineNo = $this->row($mqData)->item_code;
				$row->machineNo = $machineNo;
			endif;
			if(!empty($row->shift_id)):
				$mqData=array();
				$mqData['where']['id'] = $row->shift_id;
				$mqData['tableName'] = 'shift_master';
				$shiftName = $this->row($mqData)->shift_name;
				$row->shiftName = $shiftName;
			endif;
			
			$extraTd = "";
			if($type):
				$extraTd ='<td>'.$row->challan_no.'</td><td>'.$row->charge_no.'</td>';
			else:
				$extraTd ='<td>'.$operatorName.'</td><td>'.$shiftName.'</td><td>'.$machineNo.'</td>';
			endif;
			
			$html .= '<tr class="text-center">
						<td>'.$i++.'</td>
						<td>'.$row->process_name.'</td>
						<td>'.$transDate.'</td>
						'.$extraTd.'
						<td>'.floatVal($row->in_qty + $row->ud_qty).'</td>
						<td>'.$rjqty.'</td>
						<td>'.$rwqty.'</td>
						<td>'.$row->production_time.'</td>
						<td>'.$row->remark.'</td>
					</tr>';
			$dataRow[] = $row;
			$prev_date = $row->entry_date;$pid = $row->in_process_id;
		endforeach;	
        return ['printTable'=>$html,'outwardTrans'=>$dataRow];
    }    
	
	public function getIdleTimeByMachineDate($data){
        $queryData['tableName'] = $this->machineIdle;
		$queryData['where']['entry_date'] = $data['entry_date'];
        $queryData['where']['machine_id'] = $data['machine_id'];
        $queryData['where']['shift_id'] = $data['shift_id'];
        $result = $this->rows($queryData);
	}
	
	public function getIdleTimeList($data){
        $queryData['tableName'] = $this->machineIdle;
        $queryData['select'] = "machine_idle_logs.*,rejection_comment.code,rejection_comment.remark";
        $queryData['join']['rejection_comment'] = "rejection_comment.id = machine_idle_logs.idle_reason";
        $queryData['where']['job_card_id'] = $data['job_card_id'];
        $queryData['where']['process_id'] =$data['process_id'];
        $result = $this->rows($queryData);

        $dataRow = array();$html = "";
        if(!empty($result)): 
            $i=1;   
            foreach($result as $row):
                $entryDate = date("d-m-Y",strtotime($row->entry_date));
                $operatorName = "";$machineNo = "";$shiftName = "";
                if(!empty($row->operator_id)):
                    $queryData=array();
                    $queryData['where']['id'] = $row->operator_id;
                    $queryData['tableName'] = $this->employee;
                    $operatorName = $this->row($queryData)->emp_name;
                endif;
                if(!empty($row->machine_id)):
                    $mqData=array();
                    $mqData['where']['id'] = $row->machine_id;
                    $mqData['tableName'] = $this->itemMaster;
                    $machineNo = $this->row($mqData)->item_code;
                endif;
				if(!empty($row->shift_id)):
                    $mqData=array();
                    $mqData['where']['id'] = $row->shift_id;
                    $mqData['tableName'] = 'shift_master';
                    $shiftName = $this->row($mqData)->shift_name;
                endif;
                $deleteBtn = '';
                $deleteBtn = '<button type="button" onclick="trashIdleTime('.$row->id.');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
                $html .= '<tr>
                            <td>'.$i++.'</td>
                            <td>'.$entryDate.'</td>
                            <td>['.$row->code.'] - '.$row->remark.'</td>
                            <td>'.$row->idle_time.'</td>
                            <td>'.$shiftName.'</td>
                            <td>'.$operatorName.'</td>
                            <td>'.$machineNo.'</td>
                            <td class="text-center" style="width:10%;">
							    '.$deleteBtn.'
						    </td>
                        </tr>';
                $dataRow[] = $row;
            endforeach;
        endif;
        return ['sendData'=>$html,'idleTime'=>$dataRow];
    }

    public function saveIdleTime($data){
        unset($data['page_process_id']);
        $this->store($this->machineIdle,$data);            
        return ['status'=>1,'message'=>'Machine Idle Time saved successfully.','sendData'=>$this->getIdleTimeList(['job_card_id'=>$data['job_card_id'],'process_id'=>$data['process_id']])['sendData']];
    }

    public function deleteIdleTime($data){
        $result = $this->trash($this->machineIdle,['id'=>$data['id']],'Machine Idle Time.');

        return ['status'=>1,'message'=>'Machine Idle Time deleted successfully.','sendData'=>$this->getIdleTimeList(['job_card_id'=>$data['job_card_id'],'process_id'=>$data['process_id']])['sendData']];
    }

//-------------------------------------------------------------------------------------------

	public function getOutwardDates($data){
        $queryData['tableName'] = $this->jobOutward;
        $queryData['select'] = 'entry_date';
        $queryData['where']['ref_id'] = $data['ref_id'];
        $queryData['where']['in_process_id'] = $data['in_process_id'];
        $result = $this->rows($queryData);
		return $result;
	}
	
    public function saveOutTrans($data){
        $jobCardData = $this->jobcard->getJobcard($data['job_card_id']);

        $queryData['tableName'] = $this->jobInward;
        $queryData['where']['id'] = $data['ref_id'];
        $inwardData = $this->row($queryData);
        
        if(!empty($inwardData->job_process_ids)):
            $ref_id = $data['ref_id'];
            $in_process_id = $data['in_process_id'];
            $jobProcess = explode(",",$inwardData->job_process_ids);
            $i=0;$recordId=0;$countProcess=count($jobProcess);
            
            foreach($jobProcess as $key=>$value):
                if($i != 0):
                    $data['ref_id'] = $recordId;
                    $data['in_process_id'] = $value;
                endif;
                $recordId = $this->jobRecordInserts($data,((($countProcess-1) == $key)?true:false));
                $i++;
            endforeach;

            return ['status'=>1,'message'=>'Outward saved successfully.','sendData'=>$this->getOutwardTrans(['ref_id'=>$ref_id,'in_process_id'=>$in_process_id])['sendData']];
        else:

            $pendingQty = $inwardData->in_qty - ($inwardData->out_qty + $inwardData->rework_qty + $inwardData->rejection_qty); 
            if($pendingQty >= $data['out_qty']):
    
                $queryData=array();
                $queryData['tableName'] = $this->jobOutward;
                $queryData['where']['id'] = $inwardData->ref_id;
                $outwardData = $this->row($queryData);
    
                if(empty($inwardData->trans_type)):
                    $processes = explode(",",$jobCardData->process);
                else:
                    $queryData = array();
                    $queryData['tableName'] = $this->jobRejection;
                    $queryData['where']['id'] = $inwardData->trans_type;
                    $reworkData = $this->row($queryData);
                    $processes = explode(",",$reworkData->rework_process_id);
                endif;
                //get next Process
                $nextProcess = 0;
                if($processes[count($processes) - 1] != $data['in_process_id']):
                    foreach($processes as $key=>$value):
                        if($data['in_process_id']==$value){$nextProcess = $processes[$key + 1];break;}
                    endforeach;
                    $jobCardUpdateData['total_ud_qty'] = $jobCardData->total_ud_qty + $data['ud_qty'];
                    $this->edit($this->jobCard,['id'=>$data['job_card_id']],$jobCardUpdateData);
                else:
                    $jobCardUpdateData['total_out_qty'] = $jobCardData->total_out_qty + $data['out_qty'];
                    $jobCardUpdateData['total_ud_qty'] = $jobCardData->total_ud_qty + $data['ud_qty'];
                    $completeJobQty = $jobCardUpdateData['total_out_qty'] + $jobCardData->total_reject_qty + $jobCardData->total_rework_qty;
                    if($jobCardData->qty <= $completeJobQty):
                        $jobCardUpdateData['order_status'] = 4;
                    endif;
                    $this->edit($this->jobCard,['id'=>$data['job_card_id']],$jobCardUpdateData);
    
                    if(empty($jobCardData->ref_id)):                    
                        if(empty($jobCardData->pre_disp_inspection)):
                            $setData = Array();
                            $setData['tableName'] = $this->jobCard;
                            $setData['where']['id'] = $data['job_card_id'];
                            $setData['set']['unstored_qty'] = 'unstored_qty, + '.$data['out_qty'];
                            $this->setValue($setData);
                        else:
                            $itemData = $this->item->getItem($data['product_id']);
                            $stockQty['pending_inspection_qty'] = $itemData->pending_inspection_qty + $data['out_qty'];
                            $this->edit($this->itemMaster,['id'=>$data['product_id']],$stockQty);
                        endif;                    
                    endif;
                endif;
                
    			$juq['select'] = 'wp_qty';
    			$juq['tableName'] = $this->jobUsedMaterial;
    			$juq['where']['id'] = $data['material_used_id'];
    			$wpQty = $this->row($juq)->wp_qty;
    			$imq = round((($data['out_qty'] + $data['ud_qty']) * $wpQty),3);
    			
                $outwardPostData = [
                    'id' => '',
                    'entry_date' => $data['entry_date'],
                    'production_time' => $data['production_time'],
                    'job_card_id' => $data['job_card_id'],
                    'vendor_id' => $inwardData->vendor_id,
                    'trans_type' => $inwardData->trans_type,
                    'ref_id' => $data['ref_id'],
                    'product_id' => $data['product_id'],
                    'from_process_id' => ($data['in_process_id'] != $outwardData->from_process_id)?$outwardData->from_process_id:0,
                    'in_process_id' => $data['in_process_id'],
                    'in_qty' => $data['out_qty'],
                    'ud_qty' => $data['ud_qty'],
                    'w_pcs' => $data['w_pcs'],
                    'total_weight' => $data['total_weight'],
                    'out_process_id' => $nextProcess,
                    'remark' => $data['remark'],
                    'challan_no' => $data['challan_no'],
    				'batch_no'=>$data['batch_no'],
    				'issue_material_qty'=>$imq,
    				'material_used_id'=>$data['material_used_id'],
    				'shift_id'=>$data['shift_id'],
                    'charge_no' => $data['charge_no'],
                    'operator_id' => $data['operator_id'],
                    'machine_id' => $data['machine_id'],
                    'created_by' => $data['created_by']
                ];
                $this->store($this->jobOutward,$outwardPostData);
    
                $this->edit($this->jobInward,['id'=>$data['ref_id']],['out_qty'=>($inwardData->out_qty + $data['out_qty']),'ud_qty'=>($inwardData->ud_qty + $data['ud_qty'])]);
    
                return ['status'=>1,'message'=>'Outward saved successfully.','sendData'=>$this->getOutwardTrans(['ref_id'=>$data['ref_id'],'in_process_id'=>$data['in_process_id']])['sendData']];
            else:
                return ['status'=>0,'message'=>['outQty'=>'Qty not avalible for outward.']];
            endif;
        endif;
    }
    
    

    public function deleteOutward($id){
        $data['tableName'] = $this->jobOutward;
        $data['where']['id'] = $id;
        $outwardData = $this->row($data);
        if($outwardData->out_qty != "0.000"):
            return ['status'=>0,'message'=>"You can't delete this outward because This outward forwared to next process."];
        else:
            $jobCardData = $this->jobcard->getJobcard($outwardData->job_card_id);

            $processes = explode(",",$jobCardData->process);
            //check last process
            if($processes[count($processes) - 1] == $outwardData->in_process_id):
                $jobCardUpdateData['total_out_qty'] = $jobCardData->total_out_qty - $outwardData->in_qty;
                $jobCardUpdateData['total_ud_qty'] = $jobCardData->total_ud_qty - $outwardData->ud_qty;
                $completeJobQty = $jobCardUpdateData['total_out_qty'] + $jobCardData->total_reject_qty + $jobCardData->total_rework_qty;
                if($jobCardData->qty != $completeJobQty):
                    $jobCardUpdateData['order_status'] = 2;
                endif;
                $this->edit($this->jobCard,['id'=>$jobCardData->id],$jobCardUpdateData);

                if(empty($jobCardData->ref_id)):
                    
                    if(empty($jobCardData->pre_disp_inspection)):
                        $setData = Array();
                        $setData['tableName'] = $this->jobCard;
                        $setData['where']['id'] = $jobCardData->id;
                        $setData['set']['unstored_qty'] = 'unstored_qty, - '.$outwardData->in_qty;
                        $this->setValue($setData);
                    else:
                        $itemData = $this->item->getItem($outwardData->product_id);
                        $stockQty['pending_inspection_qty'] = $itemData->pending_inspection_qty - $outwardData->in_qty;
                        $this->edit($this->itemMaster,['id'=>$outwardData->product_id],$stockQty);
                    endif;                    
                endif;
            else:
                $jobCardUpdateData['total_ud_qty'] = $jobCardData->total_ud_qty - $outwardData->ud_qty;
                $this->edit($this->jobCard,['id'=>$jobCardData->id],$jobCardUpdateData);
            endif;          

            $queryData = array();
            $queryData['tableName'] = $this->jobInward;
            $queryData['where']['id'] = $outwardData->ref_id;
            $inwData = $this->row($queryData);
            $this->edit($this->jobInward,['id'=>$outwardData->ref_id],['out_qty'=>($inwData->out_qty - $outwardData->in_qty),'ud_qty'=>($inwData->ud_qty - $outwardData->ud_qty)]);

            $result = $this->trash($this->jobOutward,['id'=>$id],'Outward');
            $result['sendData'] = $this->getOutwardTrans(['ref_id'=>$inwData->id,'in_process_id'=>$outwardData->in_process_id])['sendData'];
            return $result;
        endif;

    }

    public function getReworkTrans($data){
        $queryData['tableName'] = $this->jobRejection;
        $queryData['job_card_id'] = $data['job_card_id'];
        $queryData['where']['job_inward_id'] = $data['ref_id'];
        $queryData['where']['process_id'] = $data['in_process_id'];
        $queryData['where']['type !='] = 0;
        $result = $this->rows($queryData);

        $dataRow = array();$html = "";
        if(!empty($result)): 
            $i=1;   
            foreach($result as $row):
                 $transDate = date("d-m-Y",strtotime($row->entry_date));
                $operatorName = "";
                if(!empty($row->operator_id)):
                    $queryData=array();
                    $queryData['where']['id'] = $row->operator_id;
                    $queryData['tableName'] = $this->employee;
                    $operatorName = $this->row($queryData)->emp_name;
                endif;
                $deleteBtn = '';
                $deleteBtn = '<button type="button" onclick="trashRework('.$row->id.','.$row->qty.');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
                $html .= '<tr>
                            <td>'.$i++.'</td>
                            <td>'.$transDate.'</td>
                            <td>'.$row->qty.'</td>
                            <td>'.$row->w_pcs.'</td>
                            <td>'.$row->total_weight.'</td>
                            <td>'.$row->remark.'</td>
                            <td>'.$operatorName.'</td>
                            <td class="text-center" style="width:10%;">
							    '.$deleteBtn.'
						    </td>
                        </tr>';
                $dataRow[] = $row;
            endforeach;
        endif;
        return ['sendData'=>$html,'reworkTrans'=>$dataRow];
    }

    public function saveRework($data){
        $jobCardData = $this->jobcard->getJobcard($data['job_card_id']);

        $queryData = array(); 
        $queryData['tableName'] = $this->jobInward;
        $queryData['where']['id'] = $data['ref_id'];
        $jobInwardData = $this->row($queryData);

        $pendingQty = $jobInwardData->in_qty - ($jobInwardData->out_qty + $jobInwardData->rework_qty + $jobInwardData->rejection_qty); 
        if($pendingQty >= $data['qty']):                 

            $jobInwardPostData['rework_qty'] = $jobInwardData->rework_qty + $data['qty']; 
            $jobCardUpdateData['total_rework_qty'] = $jobCardData->total_rework_qty + $data['qty'];
            
            $reworkProcessStr = $data['rework_process_id'].",".$data['in_process_id'];    
            			
            $jobRejectionPostData = [
                'id' => '',
                'entry_date' => $data['entry_date'],
                'job_card_id' => $data['job_card_id'],
                'job_inward_id' => $data['ref_id'],
                'type' => 1,
                'vendor_id' => $jobInwardData->vendor_id,
                'rework_process_id' => $reworkProcessStr,
                'process_id' => $data['in_process_id'],
                'qty' => $data['qty'],
                'w_pcs' => $data['w_pcs'],
                'total_weight' => $data['total_weight'],
                'remark' => $data['remark'],
                'operator_id' => $data['operator_id'],
                'machine_id' => $data['machine_id'],
				'batch_no'=>$data['batch_no'],
				'issue_material_qty'=>$data['issue_material_qty'],
				'shift_id'=>$data['shift_id'],
                'created_by' => $data['created_by']
            ];
            $saveRejection = $this->store($this->jobRejection,$jobRejectionPostData); 

            $jobOutWardData = [
                'id' => "",
                'entry_date' => $data['entry_date'],
                'trans_type' => $saveRejection['insert_id'],
                'job_card_id' => $data['job_card_id'],
                'product_id' =>  $jobCardData->product_id,
                'from_process_id' => $data['in_process_id'],
                'in_process_id' => $data['in_process_id'],
                'in_qty' => $data['qty'],
                'w_pcs' => $data['w_pcs'],
                'total_weight' => $data['total_weight'],
                'out_process_id' => explode(",",$data['rework_process_id'])[0],
                'remark' => $data['remark'],
				'batch_no'=>$data['batch_no'],
				'issue_material_qty'=>$data['issue_material_qty'],
                'created_by' => $data['created_by']
            ];
            $this->store($this->jobOutward,$jobOutWardData);            

            $this->edit($this->jobInward,['id'=>$data['ref_id']],$jobInwardPostData);
            $this->edit($this->jobCard,['id'=>$data['job_card_id']],$jobCardUpdateData);

            return ['status'=>1,'message'=>'Rework saved successfully.','sendData'=>$this->getReworkTrans(['ref_id'=>$data['ref_id'],'job_card_id'=>$data['job_card_id'],'in_process_id'=>$data['in_process_id']])['sendData']];
        else:
            return ['status'=>0,'message'=>['rewQty'=>'Qty not avalible for rework.']];
        endif;
    }

    public function deleteRework($id){
        $data['tableName'] = $this->jobRejection;
        $data['where']['id'] = $id;
        $rejectionData = $this->row($data);

        $data = array();
        $data['tableName'] = $this->jobInward;
        $data['where']['id'] = $rejectionData->job_inward_id;
        $jobInwardData = $this->row($data);

        if(!empty($rejectionData->type)):
            //$reworkJobCardData = $this->jobcard->getJobcard($rejectionData->type);
            $data = array();
            $data['tableName'] = $this->jobOutward;
            $data['where']['trans_type'] = $id;
            $data['where']['is_delete'] = 0;
            $data['order_by']['id'] = "asc";
            $data['limit'] = 1;
            $reworkData = $this->row($data);
            if($reworkData->out_qty != "0.000"):
                return ['status'=>0,'message'=>"You can't delete this rework because this rework in process."];
            else:
                //delete rework job card
                $this->trash($this->jobOutward,['id'=>$reworkData->id]);
            endif;
        endif;

        $jobCardData = $this->jobcard->getJobcard($rejectionData->job_card_id);

        $jobInwardPostData['rework_qty'] = $jobInwardData->rework_qty - $rejectionData->qty;
        $jobCardUpdateData['total_rework_qty'] = $jobCardData->total_rework_qty - $rejectionData->qty;

        $this->edit($this->jobInward,['id'=>$rejectionData->job_inward_id],$jobInwardPostData);
        $this->edit($this->jobCard,['id'=>$rejectionData->job_card_id],$jobCardUpdateData);

        $this->trash($this->jobRejection,['id'=>$id]);

        return ['status'=>1,'message'=>'Rework deleted successfully.','sendData'=>$this->getReworkTrans(['ref_id'=>$rejectionData->job_inward_id,'job_card_id'=>$rejectionData->job_card_id,'in_process_id'=>$rejectionData->process_id])['sendData']];        
    }

    public function getRejectionTrans($data){
        $queryData['tableName'] = $this->jobRejection;
        $queryData['job_card_id'] = $data['job_card_id'];
        $queryData['where']['job_inward_id'] = $data['ref_id'];
        $queryData['where']['process_id'] = $data['in_process_id'];
        $queryData['where']['type'] = 0;
        $result = $this->rows($queryData);

        $dataRow = array();$html = "";
        if(!empty($result)): 
            $i=1;   
            foreach($result as $row):
                $transDate = date("d-m-Y",strtotime($row->entry_date));
                $operatorName = "";$rejectionReason="";$shiftName = "";$rejStage="";
                if(!empty($row->operator_id)):
                    $queryData=array();
                    $queryData['where']['id'] = $row->operator_id;
                    $queryData['select'] = 'emp_name';
                    $queryData['tableName'] = $this->employee;
                    $operatorName = $this->row($queryData)->emp_name;
                endif;
                if(!empty($row->rejection_reason)):
                    if($row->rejection_reason == -1):
                        $rejectionReason = "Material Fault";
                    else:
                        $rejectionReason = $this->comment->getComment($row->rejection_reason)->remark;
                    endif;
                endif;
				if(!empty($row->shift_id)):
                    $mqData=array();
                    $mqData['where']['id'] = $row->shift_id;
                    $mqData['tableName'] = 'shift_master';
                    $shiftName = $this->row($mqData)->shift_name;
                endif;
				if(!empty($row->rejection_type_id)):
					if($row->rejection_type_id == -1):
						$rejStage="Material Fault";
					else:
						$prData=array();
						$prData['where']['id'] = $row->rejection_type_id;
						$prData['tableName'] = 'process_master';
						$rejStage = $this->row($prData)->process_name;
					endif;
				endif;
                $deleteBtn = '';
                $deleteBtn = '<button type="button" onclick="trashRejection('.$row->id.','.$row->qty.');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
                $html .= '<tr class="text-center">
                            <td>'.$i++.'</td>
                            <td>'.$transDate.'</td>
                            <td>'.$row->qty.'</td>
                            <td>'.$rejStage.'</td>
                            <td>'.$rejectionReason.'</td>
                            <td>'.$shiftName.'</td>
                            <td>'.$operatorName.'</td>
                            <td class="text-center" style="width:10%;">'.$deleteBtn.'</td>
                        </tr>';
                $dataRow[] = $row;
            endforeach;
        endif;
        return ['sendData'=>$html,'rejectionTrans'=>$dataRow];
    }

    public function saveRejection($data){
        $jobCardData = $this->jobcard->getJobcard($data['job_card_id']);

        $queryData = array(); 
        $queryData['tableName'] = $this->jobInward;
        $queryData['where']['id'] = $data['ref_id'];
        $jobInwardData = $this->row($queryData);

        $pendingQty = $jobInwardData->in_qty - ($jobInwardData->out_qty + $jobInwardData->rework_qty + $jobInwardData->rejection_qty); 
        if($pendingQty >= $data['qty']):

            $jobRejectionPostData = [
                'id' => '',
                'entry_date' => $data['entry_date'],
                'job_card_id' => $data['job_card_id'],
                'job_inward_id' => $data['ref_id'],
                'type' => 0,
                'vendor_id' => $jobInwardData->vendor_id,
                'process_id' => $data['in_process_id'],
                'qty' => $data['qty'],
                'pending_qty' => $data['qty'],
                'w_pcs' => $data['w_pcs'],
                'total_weight' => $data['total_weight'],
                'rejection_type_id' => $data['rejection_type_id'],
                'rejection_reason' => $data['rejection_reason'],
                'remark' => $data['remark'],
				'shift_id' => $data['shift_id'],
                'operator_id' => $data['operator_id'],
                'machine_id' => $data['machine_id'],
                'created_by' => $data['created_by']
            ];
            $saveRejection = $this->store($this->jobRejection,$jobRejectionPostData);            

            $jobInwardPostData['rejection_qty'] = $jobInwardData->rejection_qty + $data['qty'];
            $jobCardUpdateData['total_reject_qty'] = $jobCardData->total_reject_qty + $data['qty'];
            $completeJobQty = $jobCardUpdateData['total_reject_qty'] + $jobCardData->total_rework_qty + $jobCardData->total_out_qty;
            if($jobCardData->qty == $completeJobQty):
                $jobCardUpdateData['order_status'] = 4;
            endif;
            
            $this->edit($this->jobInward,['id'=>$data['ref_id']],$jobInwardPostData);
            $this->edit($this->jobCard,['id'=>$data['job_card_id']],$jobCardUpdateData);

            return ['status'=>1,'message'=>'Rejection saved successfully.','sendData'=>$this->getRejectionTrans(['ref_id'=>$data['ref_id'],'job_card_id'=>$data['job_card_id'],'in_process_id'=>$data['in_process_id']])['sendData']];
        else:
            return ['status'=>0,'message'=>['rejQty'=>'Qty not avalible for rejection.']];
        endif;
    }

    public function deleteRejection($id){
        $data['tableName'] = $this->jobRejection;
        $data['where']['id'] = $id;
        $rejectionData = $this->row($data);

        $data = array();
        $data['tableName'] = $this->jobInward;
        $data['where']['id'] = $rejectionData->job_inward_id;
        $jobInwardData = $this->row($data);

        $jobCardData = $this->jobcard->getJobcard($rejectionData->job_card_id);        

        $jobInwardPostData['rejection_qty'] = $jobInwardData->rejection_qty - $rejectionData->qty;
        $jobCardUpdateData['total_reject_qty'] = $jobCardData->total_reject_qty - $rejectionData->qty;
        $completeJobQty = $jobCardUpdateData['total_reject_qty'] + $jobCardData->total_rework_qty + $jobCardData->total_out_qty;
        if($jobCardData->qty > $completeJobQty):
            $jobCardUpdateData['order_status'] = 2;
        endif;

        $this->edit($this->jobInward,['id'=>$rejectionData->job_inward_id],$jobInwardPostData);
        $this->edit($this->jobCard,['id'=>$rejectionData->job_card_id],$jobCardUpdateData);

        $this->trash($this->jobRejection,['id'=>$id]);

        return ['status'=>1,'message'=>'Rejection deleted successfully.','sendData'=>$this->getRejectionTrans(['ref_id'=>$rejectionData->job_inward_id,'job_card_id'=>$rejectionData->job_card_id,'in_process_id'=>$rejectionData->process_id])['sendData']];
    }

//----------------------------------------------------

    
    /**** JP Real Time Machine Logs ****/
    public function realTimemLog(){
        $queryData['tableName'] = $this->machineLog;
        $queryData['select'] = 'COUNT(*) as qty,item_master.item_code,employee_master.emp_name,machine.item_name as machine_no';
        $queryData['join']['item_master'] = 'item_master.id = machine_log.part_code';
        $queryData['join']['item_master as machine'] = 'machine.id = (machine_log.machine_id+2951)';
        $queryData['join']['employee_master'] = 'employee_master.id = (machine_log.operator_id+52)';
        $queryData['where']['machine_log.log_date >= '] = date('Y-m-d H:i:s',strtotime(date('Y-m-d').' 00:00:0'));
        $queryData['where']['machine_log.log_date <= '] = date('Y-m-d H:i:s',strtotime(date('Y-m-d').' 23:59:59'));
        $queryData['group_by'][]='machine_log.machine_id';
        $queryData['group_by'][]='machine_log.operator_id';
        $result = $this->rows($queryData);	
        //print_r($this->db->last_query());exit;
        return $result;
	}
    
}
?>