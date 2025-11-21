<?php
class ProcessMovement extends MY_Controller{
    
    private $productionLogFrom = "production/jobcard/production_form";
    private $storeLocation = "production/jobcard/store_location";
    private $rework_index = "production/jobcard/rework_index";
    private $rework_detail = "production/jobcard/rework_detail";
    private $movementForm = "production/jobcard/movement_form";
    private $rec_material_from_store = "production/jobcard/rec_material_from_store";

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Process Movement";
        $this->data['headData']->controller = "production/processMovement";
        $this->data['headData']->pageUrl = "production/processMovement";
    }

    public function processMovement(){
        $id = $this->input->post('id');
        $approvalData = $this->processMovement->getApprovalData($id);
        $this->data['ref_id'] = $this->input->post('ref_id');
        $ref_id = !empty($this->data['ref_id'])?$this->data['ref_id'] :0;
        $this->data['pending_qty'] = !empty($this->input->post('p_qty'))?$this->input->post('p_qty') :0;
        $handover = '<option value="">Select</option>';$send_to = 0;
        
        $this->data['processData'] =$processData = $this->process->getProcess($approvalData->out_process_id);
        if(empty($approvalData->in_process_id)):
            $mevedTo = $this->processMovement->getSendTo($approvalData->job_card_id); 
            $send_to = $mevedTo->used_at;           
            if (empty($mevedTo->used_at)) :
                $empData = $this->machine->getProcessWiseMachine($approvalData->out_process_id);

                $handover .= '<option value="0" '.(($mevedTo->handover_to == 0)?"selected":"").'>Department</option>';
                foreach ($empData as $row) :
                    $selected = ($mevedTo->handover_to == $row->id)?"selected":"";
                    $handover .= "<option value='" . $row->id . "'   data-row='" . json_encode($row) . "' ".$selected.">[" . $row->item_code . "] " . $row->item_name . " </option>";
                endforeach;
            else :
                $partyData = $this->party->getVendorList();
                foreach ($partyData as $row) :
                    $selected = ($mevedTo->handover_to == $row->id)?"selected":"";
                    $handover .= "<option value='" . $row->id . "' data-row='" . json_encode($row) . "' ".$selected.">[" . $row->party_code . "] " . $row->party_name . " </option>";
                endforeach;
            endif;
        else:
            $empData = $this->machine->getProcessWiseMachine($approvalData->out_process_id);
            $handover .= '<option value="0">Department</option>';
            foreach ($empData as $row) :
                $selected = "";
                $handover .= "<option value='" . $row->id . "'   data-row='" . json_encode($row) . "' ".$selected.">[" . $row->item_code . "] " . $row->item_name . " </option>";
            endforeach;
        endif;

        $this->data['approvalData'] = $approvalData;
        $this->data['send_to'] = $send_to;
        $this->data['handover_to'] = $handover;
        $this->data['mcList'] = $this->item->getItemList(5);
        $this->data['transHtml'] = $this->getProcessMovementTransHtml($id,$ref_id);
        $this->data['heatData'] = $this->processMovement->getHeatData(['job_approval_id'=>$id]);
        $this->load->view($this->movementForm,$this->data);
    }

    public function saveProcessMovement(){
        $data = $this->input->post();
        $errorMessage = array();

        if($data['handover_to'] == '')
            $errorMessage['handover_to'] = "Handover To is required.";
        if(empty($data['qty']))
            $errorMessage['qty'] = "Qty is required.";

        if(empty($data['batch_no']))
            $errorMessage['batch_no'] = "Batch No is required.";

        if(!empty($data['qty'])):
            if(empty($data['ref_id'])){
                $approvalData = $this->processMovement->getApprovalData($data['job_approval_id']);
                if($data['qty'] > ($approvalData->ok_qty - $approvalData->total_out_qty)):
                    $errorMessage['qty'] = "Qty not avalible for movement.";
                endif;
            }else{
                if($data['qty'] > $data['pending_qty']):
                    $errorMessage['qty'] = "Qty not avalible for movement.";
                endif;
            }
            
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['machine_id'] = ($data['send_to'] == 0)?$data['handover_to']:0;
            $data['vendor_id'] = ($data['send_to'] == 1)?$data['handover_to']:0;
            $data['location_id'] = ($data['send_to'] == 2)?$data['handover_to']:0;
            unset($data['handover_to'],$data['pending_qty']);
            $data['entry_type'] = ($data['send_to'] == 2)?7:6;
            $data['created_by'] = $this->loginId;
           
            $result = $this->processMovement->saveProcessMovement($data);
            $result['transHtml'] = $this->getProcessMovementTransHtml($data['job_approval_id'],$data['ref_id']);
            $this->printJson($result);
        endif;
    }

    public function deleteMovement(){
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $result = $this->processMovement->deleteMovement($id);
            if($result['status'] != 2):
                $result['transHtml'] = $this->getProcessMovementTransHtml($result['job_approval_id'],$result['ref_id']);
            endif;
            $this->printJson($result);
        endif;
    }

    public function getProcessMovementTransHtml($approval_id,$ref_id=''){
        $transData = $this->processMovement->getProcessMovementTrans($approval_id,$ref_id);
        $html = '';$i=1;
        if(!empty($transData)):
            foreach($transData as $row):
                $printBtn = '<a href="' . base_url('production/jobcard/printProcessIdentification/' . $row->id) . '" target="_blank" class="btn btn-sm btn-outline-info waves-effect waves-light mr-2" title="Print"><i class="fas fa-print"></i></a>';

                $deleteBtn = '';
                if($row->entry_type == 6):
                    $deleteBtn = '<button type="button" onclick="trashMovement('.$row->id.','.$row->qty.');" class="btn btn-sm btn-outline-danger waves-effect waves-light permission-remove"><i class="ti-trash"></i></button>';
                endif;

                $hadoverTo = "";$sendTo = "";
                if($row->send_to == 0):
                    if(!empty($row->machine_id)){
                        $hadoverTo = "[".$row->item_code."] ".$row->item_name;
                    }else{
                        $hadoverTo = "Department";
                    }
                    
                    $sendTo = "In House";
                elseif($row->send_to == 1):
                    $hadoverTo = "[".$row->party_code."] ".$row->party_name;
                    $sendTo = "Vendor";
                elseif($row->send_to == 2):
                    $hadoverTo = "[".$row->store_name."] ".$row->location;
                    $sendTo = "Store";
                endif;

                $html .= '<tr>
                    <td class="text-center">'.$i++.'</td>
                    <td>'.date("d-m-Y",strtotime($row->entry_date)).'</td>
                    <td>'.$sendTo.'</td>
                    <td>'.$hadoverTo.'</td>
                    <td>'.floatval($row->qty).'</td>
                    <td>'.$row->remark.'</td>
                    <td class="text-center">
                        '.$printBtn.$deleteBtn.'                        
                    </td>
                </tr>';
            endforeach;
        else:
            $html = '<tr><td colspan="7" class="text-center">No Data Found.</td></tr>';
        endif;
        return $html;
    }

    public function receiveStoredMaterial(){
        $data = $this->input->post();
        $postData = ['stock_effect'=>0,'ref_type'=>24,'ref_id'=>$data['job_card_id'],'ref_no'=>$data['job_approval_id'],'stock_required'=>1];
        $this->data['storeData'] = $this->store->getItemStockBatchWise($postData);
        $this->data['job_card_id'] = $data['job_card_id'];
        $this->data['job_approval_id'] = $data['job_approval_id'];
        $this->load->view($this->rec_material_from_store,$this->data);
    }

    public function saveReceiveStoredMaterial(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty(array_column($data['item'],'qty')))
            $errorMessage['qty_1'] = "Qty is required.";

        $data['items'] = array();
        if(!empty(array_column($data['item'],'qty'))):
            foreach($data['item'] as $key=>$row):
                if($row['qty'] > 0):
                    $postData = ['stock_effect'=>0,'ref_type'=>24,'location_id'=>$row['location_id'],'batch_no'=>$row['batch_no'],'item_id'=>$row['item_id'],'stock_required'=>1,'single_row'=>1];
                    $stockData = $this->store->getItemStockBatchWise($postData);
                    if(empty($stockData) || $row['qty'] > $stockData->qty):
                        $errorMessage["qty_".$key] = "Stock not avalible.";
                    endif;
                    $data['items'][] = $row;
                endif;
            endforeach;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->processMovement->saveReceiveStoredMaterial($data));
        endif;
    }

    public function saveAcceptedQty(){
        $data = $this->input->post();
        if(empty($data['in_qty']))
            $errorMessage['in_qty'] = "Qty is required.";

        if(!empty($data['in_qty'])):
            $aprvData = $this->processMovement->getApprovalData($data['job_approval_id']);
            // if($data['in_qty'] > $aprvData->inward_qty):
            if( $data['in_qty'] > ($aprvData->inward_qty ) - ($aprvData->in_qty-$aprvData->ch_qty)):
                $errorMessage['in_qty'] = "Qty not avalible for accept.";
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->processMovement->saveAcceptedQty($data));
        endif;
    }

    public function processApproved()
    {
        $data = $this->input->post();
        $id =  $data['id'];
        $outwardData = $this->processMovement->getApprovalData($id);
        $outwardData->pqty = ($outwardData->in_qty * $outwardData->output_qty)-($outwardData->ch_qty * $outwardData->output_qty) - ($outwardData->ih_prod_qty);
        $this->data['dataRow'] = $outwardData;
        $this->data['dataRow']->ref_id = '';
        $this->data['outwardTrans'] = $this->processMovement->getOutwardTrans($outwardData->id)['htmlData'];
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->data['operatorList'] = $this->employee->getMachineOperatorList();
        $this->data['machine'] = $this->processMovement->getCurrentProcessMachine(['job_card_id'=>$outwardData->job_card_id,'process_id'=>$outwardData->in_process_id]); 
        $this->data['shiftData'] = $this->shiftModel->getShiftList();
        $this->data['masterOption'] = $this->processMovement->getMasterOptions();
        $prdPrsData = $this->item->getPrdProcessDataProductProcessWise(['item_id' => $outwardData->product_id, 'process_id' => $outwardData->in_process_id]);
        $this->data['cycle_time'] = !empty($prdPrsData->cycle_time)?$prdPrsData->cycle_time:0;
        $typeOfMachine = !empty($prdPrsData->typeof_machine)?$prdPrsData->typeof_machine:0;
        $this->data['machineData'] = $this->item->getMachineTypeWiseMachine($typeOfMachine);
        $jobCardData = $this->jobcard->getJobcard($outwardData->job_card_id);
        $jobProcess = explode(",", $jobCardData->process);

        $stageHtml = '<option value="">Select Stage</option><option value="0" data-process_name="Row Material">Row Material</option>';
        
        if (!empty($outwardData->in_process_id)) {
            $in_process_key = array_keys($jobProcess, $outwardData->in_process_id)[0];
            foreach ($jobProcess as $key => $value) :
                if ($key <= $in_process_key) :
                    $processData = $this->process->getProcess($value);
                    $stageHtml .= '<option value="' . $processData->id . '" data-process_name="' . $processData->process_name . '">' . $processData->process_name . '</option>';
                endif;
            endforeach;
        }
        $this->data['dataRow']->stage = $stageHtml;
        $processArray = explode(",", '0,'.$jobCardData->process);
        $in_process_key = array_keys($processArray, $outwardData->in_process_id)[0];
        $this->data['heatData'] = $this->processMovement->getHeatData(['job_card_id'=>$outwardData->job_card_id,'process_id'=>$processArray[($in_process_key-1)]]);
        $this->data['idleReason'] = $this->comment->getIdleReason();
        $this->load->view($this->productionLogFrom, $this->data);
    }

    public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['job_card_id']))
            $errorMessage['job_card_id'] = "Job Card No. is required.";
        if (empty($data['product_id']))
            $errorMessage['product_id'] = "Product Name is required.";
    
		if(!is_array($data['idle_time'])){
			if (empty($data['out_qty']) && empty($data['rej_qty'])  && empty($data['rw_qty']) && empty($data['hold_qty']))
				$errorMessage['general_error'] = "Out Qty Or Rejection Qty. is required.";
		}else{
		    if (empty($data['machine_id']))
			    $errorMessage['machine_id'] = "Machine is required.";
			if (empty($data['start_time']))
				$errorMessage['start_time'] = "Start Time is required.";
			if (empty($data['end_time']))
				$errorMessage['end_time'] = "End Time is required.";
		}
			
        /*if (empty($data['out_qty']) && empty($data['rej_qty'])  && empty($data['rw_qty']) && empty($data['hold_qty']))
            $errorMessage['general_error'] = "Out Qty Or Rejection Qty. is required.";*/
    
        if (empty($data['entry_date']) or $data['entry_date'] == null or $data['entry_date'] == "") :
            $errorMessage['entry_date'] = "Date is required.";
        endif;
        if(empty($data['batch_no'])){
            $errorMessage['batch_no'] = "Batch No is required.";
        }
        $pendingQty = 0;
        $outwardData = $this->processMovement->getApprovalData($data['job_approval_id']);
        if(empty($data['vendor_id'])):
            // if (!empty($data['out_qty'])) :
                //$pendingQty = $outwardData->in_qty - $outwardData->outward_qty - $outwardData->total_prod_qty;
                
                $pendingQty = ($outwardData->in_qty * $outwardData->output_qty) - ($outwardData->ch_qty * $outwardData->output_qty) - ($outwardData->ih_prod_qty);
                
                if(empty($data['shift_id'])):
                    $errorMessage['shift_id'] = "Shift is required.";
                endif;
                if(empty($data['operator_id'])):
                    $errorMessage['operator_id'] = "Operator is required.";
                endif;
            
            // endif;
        else:
            $transData = $this->processMovement->getOutwardTransPrint($data['ref_id']);
            $pendingQty = ($transData->qty * $outwardData->output_qty) - $transData->outsource_qty;
        endif;

        $totalProdQty = (!empty($data['out_qty']))?$data['out_qty']:0 ;
        $totalProdQty += (!empty($data['rej_qty'])) ? $data['rej_qty'] : 0;
        $totalProdQty += (!empty($data['rw_qty'])) ? $data['rw_qty'] : 0; 
        $totalProdQty += (!empty($data['hold_qty'])) ? $data['hold_qty'] : 0;
        if($pendingQty < $totalProdQty) :
            $errorMessage['out_qty'] = "Qty not available.";
        endif;

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            //Movement Data
            $movementData = [
                'id' => '',
                'entry_date' => $data['entry_date'],
                'trans_type' => $data['trans_type'],
                'entry_type' => (!empty($data['entry_type'])) ? $data['entry_type'] : 0,
                'ref_id' => (!empty($data['ref_id'])) ? $data['ref_id'] : 0,
                'vendor_id' => (!empty($data['vendor_id'])) ? $data['vendor_id'] : 0,
                'job_card_id' => $data['job_card_id'],
                'job_approval_id' => $data['job_approval_id'],
                'process_id' => $data['in_process_id'],
                'product_id' => $data['product_id'],
                'qty' => !empty($data['out_qty']) ? $data['out_qty'] : 0,
                'remark' => $data['remark'],
                'cycle_time' => $data['cycle_time'],
                'production_time' => ((!empty($data['cycle_time']) ? $data['cycle_time'] : 0)*(!empty($data['production_qty']) ? $data['production_qty'] : 0))/60,
                'send_to' => 0,
                'machine_id' => !empty($data['machine_id']) ? $data['machine_id'] : '',
                'shift_id' => !empty($data['shift_id']) ? $data['shift_id'] : '',
                'operator_id' => !empty($data['operator_id']) ? $data['operator_id'] : '',
                'rej_qty' => !empty($data['rej_qty']) ? $data['rej_qty'] : 0,
                'rw_qty' => !empty($data['rw_qty']) ? $data['rw_qty'] : 0,
                'hold_qty' => !empty($data['hold_qty']) ? $data['hold_qty'] : 0,
                'in_challan_no' => !empty($data['in_challan_no']) ? $data['in_challan_no'] : '',
                'mfg_by' => !empty($data['mfg_by']) ? $data['mfg_by'] : '',
                'start_time' => !empty($data['start_time']) ? $data['start_time'] : '',
                'end_time' => !empty($data['end_time']) ? $data['end_time'] : '',
				'idle_reason' => !empty($data['idle_time']) ? $data['idle_time'] : array(),
                'batch_no' => !empty($data['batch_no']) ? $data['batch_no'] : '',
                'created_by' => $this->session->userdata('loginId')
            ];
            $result = $this->processMovement->save($movementData);
            $this->printJson($result);
        endif;
    }
    
    /* Delete Outward Trans */
    public function delete()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $result = $this->processMovement->delete($id);
            $this->printJson($result);
        endif;
    }

    public function storeLocation()
    {
        $id = $this->input->post('id');
        $transid = $this->input->post('transid');
        $ref_batch = $this->input->post('ref_batch');
        $remark = $this->input->post('remark');
        $ref_batch = !empty($ref_batch)?$ref_batch:NULL;
        $jobcardData = $this->jobcard->getJobCard($id);
        $outwardData = $this->processMovement->getApprovalData($transid);
        $outwardData->minDate = (!empty($outwardData->entry_date)) ? $outwardData->entry_date : $outwardData->job_date;
        $this->data['dataRow'] = $outwardData;

        $this->data['job_id'] = $id;
        $this->data['ref_id'] = $transid;
        $this->data['ref_batch'] = $ref_batch;
        $this->data['remark'] = !empty($remark)? $remark:'';
        $this->data['jobNo'] = $jobcardData->job_number;
        $this->data['qty'] = $jobcardData->unstored_qty;
        $this->data['pending_qty'] = $jobcardData->unstored_qty;
        $this->data['product_name'] = $this->item->getItem($jobcardData->product_id)->item_code;
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['transactionData'] = $this->processMovement->getStoreLocationTrans($id,$ref_batch,$remark);
       
        $this->data['heatData'] = $this->processMovement->getHeatData(['job_approval_id'=>$transid]);
        $this->load->view($this->storeLocation, $this->data);
    }

    public function saveStoreLocation()
    {
        $data = $this->input->post();
        $errorMessage = array();
        $jobcardData = $this->jobcard->getJobCard($data['job_id']);

        if (!empty($data['qty']) && $data['qty'] != "0.000") :
            if ($data['qty'] > $jobcardData->unstored_qty) :
                $errorMessage['qty'] = "Invalid Qty.";
            endif;
        else :
            $errorMessage['qty'] = "Qty is required.";
        endif;
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['trans_date'] = formatDate($data['trans_date'], 'Y-m-d');
            $data['product_id'] = $jobcardData->product_id;
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->processMovement->saveStoreLocation($data));
        endif;
    }

    public function deleteStoreLocationTrans()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->processMovement->deleteStoreLocationTrans($id));
        endif;
    }

    public function getRejectionBelongs()
    {
        $data = $this->input->post();
        $jobCardData = $this->jobcard->getJobcard($data['job_card_id']);
        $jobProcess = explode(",", $jobCardData->process);
        $in_process_key = array_keys($jobProcess, $data['process_id'])[0];
        $html = '<option value="">Select Stage</option>
                    <option value="0" data-process_name="Row Material">Row Material</option>';
        foreach ($jobProcess as $key => $value) :
            if ($key <= $in_process_key) :
                $processData = $this->process->getProcess($value);
                $html .= '<option value="' . $processData->id . '" data-process_name="' . $processData->process_name . '">' . $processData->process_name . '</option>';
            endif;
        endforeach;
        $ctData = $this->process->getProductProcess(['process_id' => $data['process_id'], 'item_id' => $data['part_id']]);
        $cycle_time = (!empty($ctData)) ? $ctData->master_ct : 0;



        $opOptions = '';
        $mcOptions = '<option value="" >Select Machine</option>';
        if (!empty($data['entry_type']) and $data['entry_type'] == 'REJ') {
            $machineData = $this->logSheet->getPrdLogMachines($data);
            $opOptions = '<option value="" >Select Operator</option>';
            $operatorData = $this->logSheet->getPrdLogOperators($data);
            if (!empty($operatorData)) {
                foreach ($operatorData as $row) :
                    if (!empty($row->id)) {
                        $opOptions .= '<option value="' . $row->id . '" >[ ' . $row->emp_code . ' ] ' . $row->emp_name . '</option>';
                    }
                endforeach;
            }
        } else {
            $machineData = $this->item->getProcessWiseMachine($data['process_id']);
        }
        if (!empty($machineData)) {
            foreach ($machineData as $row) :
                $mcOptions .= '<option value="' . $row->id . '" >[ ' . $row->item_code . ' ] ' . $row->item_name . '</option>';
            endforeach;
        }
        $this->printJson(['status' => 1, 'rejOption' => $html, 'rewOption' => $html, 'cycle_time' => $cycle_time, 'mcOptions' => $mcOptions, 'opOptions' => $opOptions]);
    }

    public function getRejRWBy()
    {
        $data = $this->input->post();
        $vendorData = $this->processMovement->getRejRWBy($data);
        $rejOption = '<option value="0" data-party_name="In House">In House</option>';
        if (!empty($vendorData)) :
            foreach ($vendorData as $row) :
                $rejOption .= '<option value="' . $row->vendor_id . '" data-party_name="' . $row->party_name . '">' . $row->party_name . '</option>';
            endforeach;
        endif;
        $this->printJson(['status' => 1, 'rejOption' => $rejOption]);
    }

    public function rework()
    {
        $this->data['headData']->pageUrl = "production/processMovement/rework";
        $this->data['tableHeader'] = getProductionHeader("rework");
        $this->load->view($this->rework_index, $this->data);
    }

    public function getReworkDTRow($status = 0)
    {
        $data = $this->input->post();$data['status'] =$status;
        $result = $this->processMovement->getReworkDTRow($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller =  $this->data['headData']->controller;
            $sendData[] = getReworkData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function reworkDetail($id)
    {
        $this->data['headData']->pageUrl = "production/processMovement/rework";
        $cftData = $this->primaryCFT->getRejMovementData($id);


        $process = explode(",", $cftData->rw_process_id);
        $dataRows = array();
        $totalCompleteQty = 0;
        $totalRejectQty = 0;
        $stages = array();
        $stg = array();
        $s = 0;
        $runningStages = array();
        $totalScrapQty = 0;

        foreach ($process as $process_id) :
            $row = new stdClass;
            $jobApprovalData = $this->processMovement->getProcessWiseApprovalData($cftData->job_card_id, $process_id, 2, $id);
            $rej_belongs = $this->processMovement->getRejBelongsTo($id, $process_id);
            $jobCardData = $this->jobcard->getJobcard($cftData->job_card_id);
            $row->process_id = $process_id;
            $row->process_name = (!empty($jobApprovalData->in_process_name)) ? $jobApprovalData->in_process_name : ((!empty($process_id)) ? $this->process->getProcess($process_id)->process_name : "Raw Material");
            $row->job_id = $cftData->job_card_id;
            $row->id = (!empty($jobApprovalData->id)) ? $jobApprovalData->id : 0;
            $row->product_id = $jobCardData->product_id;
            $row->product_code = $jobCardData->product_code;
            $row->vendor = (!empty($jobApprovalData->vendor)) ? $jobApprovalData->vendor : "";
            $row->inward_qty = (!empty($jobApprovalData->inward_qty)) ? $jobApprovalData->inward_qty : 0;
            $row->in_qty = (!empty($jobApprovalData->in_qty)) ? $jobApprovalData->in_qty : 0;
            $row->out_qty = (!empty($jobApprovalData->ok_qty)) ? $jobApprovalData->ok_qty : 0;
            $row->total_rejection_qty = (!empty($jobApprovalData->total_rejection_qty)) ? $jobApprovalData->total_rejection_qty : 0;
            $row->total_rej_belongs = (!empty($rej_belongs)) ? $rej_belongs : 0;
            $row->total_rework_qty = (!empty($jobApprovalData->total_rework_qty)) ? $jobApprovalData->total_rework_qty : 0;;
            $row->total_hold_qty = (!empty($jobApprovalData->total_hold_qty)) ? $jobApprovalData->total_hold_qty : 0;;

            $completeQty = $row->out_qty + $row->total_rejection_qty /* + $row->total_rework_qty */;
            $row->pending_qty = $row->in_qty - $completeQty - $row->total_rework_qty - $row->total_hold_qty;

            $row->scrap_qty = (!empty($jobApprovalData->pre_finished_weight)) ? round(($jobApprovalData->pre_finished_weight - $jobApprovalData->finished_weight) * $row->in_qty, 2) : 0;
            $totalScrapQty += $row->scrap_qty;

            $processPer = ($completeQty > 0 && $row->in_qty > 0) ? ($completeQty * 100 / $row->in_qty) : "0";
            if ($completeQty == 0) :
                $row->status = '<span class="badge badge-pill badge-danger m-1">' . round($processPer, 2) . '%</span>';
            elseif ($row->in_qty > $completeQty) :
                $row->status = '<span class="badge badge-pill badge-warning m-1">' . round($processPer, 2) . '%</span>';
            elseif ($row->in_qty == $completeQty) :
                $row->status = '<span class="badge badge-pill badge-success m-1">' . round($processPer, 2) . '%</span>';
            else :
                $row->status = '<span class="badge badge-pill badge-dark m-1">' . round($processPer, 2) . '%</span>';;
            endif;

            $row->process_approvel_data = $jobApprovalData;
            $dataRows[] = $row;

            $totalCompleteQty += $completeQty;
            $totalRejectQty += $row->total_rejection_qty;


        endforeach;
        $cftData->processData = $dataRows;
        $this->data['dataRow'] = $cftData;
        $this->load->view($this->rework_detail, $this->data);
    }

    /* public function saveRework()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['job_card_id']))
            $errorMessage['job_card_id'] = "Job Card No. is required.";
        if (empty($data['product_id']))
            $errorMessage['product_id'] = "Product Name is required.";
        if (empty($data['out_qty']) && empty($data['rej_qty'])  && empty($data['rw_qty']) && empty($data['hold_qty']))
            $errorMessage['general_error'] = "Out Qty Or Rejection Qty. is required.";
        if (empty($data['entry_date']) or $data['entry_date'] == null or $data['entry_date'] == "") :
            $errorMessage['entry_date'] = "Date is required.";
        endif;

        $pendingQty = 0;
        if(empty($data['vendor_id'])):
            $outwardData = $this->processMovement->getApprovalData($data['job_approval_id']);
            if (!empty($data['out_qty'])) :
                $pendingQty = $outwardData->in_qty - $outwardData->outward_qty - $outwardData->total_prod_qty;
            endif;
        else:
            $transData = $this->processMovement->getOutwardTransPrint($data['ref_id']);
            $pendingQty = $transData->qty - $transData->outsource_qty;
        endif;

        $totalProdQty = (!empty($data['out_qty']))?$data['out_qty']:0 ;
        $totalProdQty += (!empty($data['rej_qty'])) ? $data['rej_qty'] : 0;
        $totalProdQty += (!empty($data['rw_qty'])) ? $data['rw_qty'] : 0; 
        $totalProdQty += (!empty($data['hold_qty'])) ? $data['hold_qty'] : 0;
        if($pendingQty < $totalProdQty) :
            $errorMessage['out_qty'] = "Qty not available.";
        endif;

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            //Movement Data
            $movementData = [
                'id' => '',
                'entry_date' => $data['entry_date'],
                'trans_type' => $data['trans_type'],
                'entry_type' => !empty($data['entry_type']) ? $data['entry_type'] : 0,
                'ref_id' => !empty($data['ref_id']) ? $data['ref_id'] : 0,
                'vendor_id' => !empty($data['vendor_id']) ? $data['vendor_id'] : 0,
                'job_card_id' => $data['job_card_id'],
                'job_approval_id' => $data['job_approval_id'],
                'process_id' => $data['in_process_id'],
                'product_id' => $data['product_id'],
                'qty' => !empty($data['out_qty']) ? $data['out_qty'] : 0,
                'remark' => $data['remark'],
                'cycle_time' => $data['cycle_time'],
                'production_time' => $data['production_time'],
                'send_to' => $data['send_to'],
                'machine_id' => !empty($data['machine_id']) ? $data['machine_id'] : '',
                'shift_id' => !empty($data['shift_id']) ? $data['shift_id'] : '',
                'operator_id' => !empty($data['operator_id']) ? $data['operator_id'] : '',
                'rej_qty' => !empty($data['rej_qty']) ? $data['rej_qty'] : 0,
                'rw_qty' => !empty($data['rw_qty']) ? $data['rw_qty'] : 0,
                'hold_qty' => !empty($data['hold_qty']) ? $data['hold_qty'] : 0,
                'created_by' => $this->session->userdata('loginId')
            ];
            $result = $this->processMovement->saveRework($movementData);
            $this->printJson($result);
        endif;
    } */

    /* public function deleteRework()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $result = $this->processMovement->deleteRework($id);
            $this->printJson($result);
        endif;
    } */

    public function getAsignOperator(){
        $data=$this->input->post();
        $oprData =  $this->machine->getMachineAssignOperator(['machine_id'=>$data['machine_id'],'shift_id'=>$data['shift_id'],'emp_type'=>'OPR']); 
        $operator_id= !empty($oprData->opr_id)?$oprData->opr_id:'';
        $this->printJson(['status'=>1,'operator_id'=>$operator_id]);
    }
}
