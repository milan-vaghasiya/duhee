<?php
class Jobcard extends MY_Controller
{

    private $indexPage = "production/jobcard/index";
    private $jobcardForm = "production/jobcard/form";
    private $jobcardDetail = "production/jobcard/jobcard_detail";
    private $jobDetail = "production/jobcard/jobcard_detail1";
    private $material_return_form = "production/jobcard/material_return_form";
    private $updateJobForm = "production/jobcard/update_job";
    private $setup_request = "production/jobcard/setup_request";
    private $print_job_card = "production/jobcard/print_job_card";
    
    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Jobcard";
        $this->data['headData']->controller = "production/jobcard";
    }

    public function index()
    {
        $this->data['headData']->pageUrl = "production/jobcard";
        $this->data['tableHeader'] = getProductionHeader("jobcard");
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($status = 0)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->jobcard->getDTRows($data, 0);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->party_name = (!empty($row->party_name)) ? $row->party_name : "Self Stock";
            $row->party_code = (!empty($row->party_code)) ? $row->party_code : "Self Stock";
            if ($row->order_status == 0) :
                $row->order_status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
            elseif ($row->order_status == 1) :
                $row->order_status_label = '<span class="badge badge-pill badge-primary m-1">Start</span>';
            elseif ($row->order_status == 2) :
                $row->order_status_label = '<span class="badge badge-pill badge-warning m-1">In-Process</span>';
            elseif ($row->order_status == 3) :
                $row->order_status_label = '<span class="badge badge-pill badge-info m-1">On-Hold</span>';
            elseif ($row->order_status == 4) :
                $row->order_status_label = '<span class="badge badge-pill badge-success m-1">Complete</span>';
            elseif ($row->order_status == 7) :
                $row->order_status_label = '<span class="badge badge-pill badge-secondary m-1">In Approval</span>';
            else :
                $row->order_status_label = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
            endif;

            $lastLog = $this->jobcard->getLastTrans($row->id);
            $row->last_activity = (!empty($lastLog)) ? $lastLog->updated_at : "";

            $pendingdata = $this->jobcard->getJobPendingQty($row->id);
            if (!empty($pendingdata)) {
                $row->pendingQty = $pendingdata->ok_qty - $pendingdata->total_out_qty;
            } else {
                $row->pendingQty = 0;
            }

            $row->material_status = $this->jobcard->getMaterialStatus($row->id);

            $row->controller = $this->data['headData']->controller;
            $row->loginID = $this->session->userdata('loginId');
            $sendData[] = getJobcardData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addJobcard()
    {
        $this->data['jobPrefix'] = "JC-" . n2y(date('Y'));
        $this->data['jobNo'] = sprintf('%04d', $this->jobcard->getNextJobNo(0));
        $this->data['jobwPrefix'] = "JCW-" . $this->shortYear;
        $this->data['jobwNo'] = $this->jobcard->getNextJobNo(1);
        $this->data['customerData'] = $this->jobcard->getCustomerList();
        $this->data['productData'] = $this->item->getItemList(1);
        $this->data['machineList'] = $this->machine->getMachineList();
        $this->load->view($this->jobcardForm, $this->data);
    }

    public function save()
    {
        $data = $this->input->post(); //print_r($data);exit;
        $errorMessage = array();
        // if (!empty($data['error_msg']))
        //     $errorMessage['error_msg'] = "Some Details are missing...";
        if ($data['party_id'] == "")
            $errorMessage['party_id'] = "Customer is required.";
        if (empty($data['product_id']))
            $errorMessage['product_id'] = "Product is required.";
        if (empty($data['qty']) || $data['qty'] == "0.000")
            $errorMessage['qty'] = "Quantity is required.";
        if (empty($data['process']))
            $errorMessage['process'] = "Product Process is required.";
        /* if (empty($data['handover_to']))
            $errorMessage['handover_to'] = "Dispatch Location is required."; */
        if (empty($data['job_date'])) :
            $errorMessage['job_date'] = "Date is required.";
        else :
            if (($data['job_date'] < $this->startYearDate) or ($data['job_date'] > $this->endYearDate))
                $errorMessage['job_date'] = "Invalid Date";
        endif;
        //if($data['heat_treatment'] == ""){$errorMessage['heat_treatment'] = "Required.";}
        $kitData = $this->item->getProductKitData($data['product_id']);
        
            foreach ($kitData as $kit) :
                if(isset($data['batch_no'][$kit->ref_item_id]) && $kit->item_type == 3):
                    $batchNo = $data['batch_no'][$kit->ref_item_id];
                    if(count(array_unique($batchNo)) > 1):
                        $errorMessage['error_msg'] = "Multiple batch not alloweded";
                    endif;
                endif;
            endforeach;
        
        
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            unset($data['error_msg']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->jobcard->save($data));
        endif;
    }

    public function edit()
    {
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->jobcard->getJobcard($id); // print_r($this->data['dataRow']);exit;
        $this->data['customerData'] = $this->jobcard->getCustomerList();
        $this->data['customerSalesOrder'] = $this->jobcard->getCustomerSalesOrder($this->data['dataRow']->party_id);

        $productPostData = ['sales_order_id' => $this->data['dataRow']->sales_order_id, 'product_id' => $this->data['dataRow']->product_id];
        $this->data['productData'] = $this->jobcard->getProductList($productPostData);

        $productProcessData = ['product_id' => $this->data['dataRow']->product_id];
        $this->data['productProcessAndRaw'] = $this->jobcard->getProductProcess($productProcessData, $id);

        $this->data['allocatedMaterial'] = $this->jobcard->getAllocatedMaterial($id);

        $this->load->view($this->jobcardForm, $this->data);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->jobcard->delete($id));
        endif;
    }

    public function customerSalesOrderList()
    {
        $orderData = $this->jobcard->getCustomerSalesOrder($this->input->post('party_id'));
        $options = "<option value=''>Select Order No.</option>";
        foreach ($orderData as $row) :
            $options .= '<option value="' . $row->id . '">' . getPrefixNumber($row->trans_prefix, $row->trans_no) . '</option>';
        endforeach;
        $this->printJson(['status' => 1, 'options' => $options]);
    }

    public function getProductList()
    {
        $data = $this->input->post();
        $this->printJson($this->jobcard->getProductList($data));
    }

    public function getProductProcess()
    {
        $data = $this->input->post();
        $this->printJson($this->jobcard->getProductProcess($data));
    }

    public function materialRequest()
    {
        $id = $this->input->get_post('id');
        $this->data['job_id'] = $id;
        $this->data['jobCardData'] = $this->jobcard->getJobcard($id);
        $this->data['allocatedMaterial'] = $this->jobcard->getAllocatedMaterial($id);
        $this->data['machineList'] = $this->machine->getMachineList();
        $this->load->view('production/jobcard/material_request', $this->data);
    }

    public function getBatchNo()
    {
        $item_id = $this->input->post('item_id');
        $location_id = $this->input->post('location_id');
        $batchData = $this->item->locationWiseBatchStock($item_id, $location_id);

        $options = '<option value="">Select Batch No.</option>';
        foreach ($batchData as $row) :
            if ($row->qty > 0) :
                $options .= '<option value="' . $row->batch_no . '"  data-stock="' . $row->qty . '">' . $row->batch_no . '</option>';
            endif;
        endforeach;
        $this->printJson(['status' => 1, 'options' => $options]);
    }

    public function saveMaterialRequest()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if ($data['used_at'] == "")            
            $errorMessage['used_at'] = "Dispatch To is required.";
        if ($data['handover_to'] == '')            
            $errorMessage['handover_to'] = "Dispatch Location is required.";
        $i = 1;
        if (!empty($data['item'])) :            
            /*foreach($data['item'] as $row):                
                if(empty($row['req_qty'])):                    
                    $errorMessage['request_qty_'.$i] = "Req. Qty. is required.";                
                endif; 
                if(!empty($row['req_qty']) && $row['req_qty'] > $row['pending_qty']):                    
                    $errorMessage['request_qty_'.$i] = "Invalid Qty.";                
                endif;
                $i++;            
            endforeach;*/                        
            if (empty(array_sum(array_column($data['item'], 'req_qty')))) :                
                $errorMessage['request_qty_' . $i] = "Req. Qty. is required.";
            endif;
            foreach ($data['item'] as $row) :                               
                 if (!empty($row['req_qty']) && $row['req_qty'] > $row['pending_qty']) :                   
                    $errorMessage['request_qty_' . $i] = "Invalid Qty.";
                endif;
                $i++;
            endforeach;
        else :            $errorMessage['general_error'] = "Material is required.";
        endif;
        if (!empty($errorMessage)) :            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :            $data['created_by'] = $this->loginId;
            $this->printJson($this->jobcard->saveMaterialRequest($data));
        endif;
    }

    public function materialReceived()
    {
        $data = $this->input->post();
        $this->printJson($this->jobcard->materialReceived($data));
    }

    public function changeJobStatus()
    {
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->jobcard->changeJobStatus($data));
        endif;
    }

    public function view($id)
    {
        $jobCardData = $this->jobcard->getJobcard($id);

        if (empty($jobCardData->party_name)) {
            $jobCardData->party_name = "Self";
        }
        if (empty($jobCardData->party_code)) {
            $jobCardData->party_code = "Self Stock";
        }

        $process = explode(",", "0," . $jobCardData->process);
        $jobCardData->first_process_id = $process[1];
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
            $jobApprovalData = $this->processMovement->getProcessWiseApprovalData($id, $process_id);
            $rej_belongs = $this->processMovement->getRejBelongsTo($id, $process_id);

            $row->process_id = $process_id;
            $row->process_name = (!empty($jobApprovalData->in_process_name)) ? $jobApprovalData->in_process_name : ((!empty($process_id)) ? $this->process->getProcess($process_id)->process_name : "Raw Material");
            $row->job_id = $id;
            $row->id = (!empty($jobApprovalData->id)) ? $jobApprovalData->id : 0;
            $row->product_id = $jobCardData->product_id;
            $row->product_code = $jobCardData->product_code;
            $row->vendor = (!empty($jobApprovalData->vendor)) ? $jobApprovalData->vendor : "";
            $row->inward_qty = (!empty($jobApprovalData->inward_qty)) ? $jobApprovalData->inward_qty : 0;
            $row->outward_qty = (!empty($jobApprovalData->outward_qty)) ? $jobApprovalData->outward_qty : 0;
            $row->in_qty = (!empty($jobApprovalData->in_qty)) ? $jobApprovalData->in_qty : 0;
            $row->ok_qty = (!empty($jobApprovalData->ok_qty)) ? $jobApprovalData->ok_qty : 0;
            $row->total_rejection_qty = (!empty($jobApprovalData->total_rejection_qty)) ? $jobApprovalData->total_rejection_qty : 0;
            $row->total_rej_belongs = (!empty($rej_belongs)) ? $rej_belongs : 0;
            $row->total_rework_qty = (!empty($jobApprovalData->total_rework_qty)) ? $jobApprovalData->total_rework_qty : 0;
            $row->total_hold_qty = (!empty($jobApprovalData->total_hold_qty)) ? $jobApprovalData->total_hold_qty : 0;
            $row->total_prod_qty = (!empty($jobApprovalData->total_prod_qty)) ? $jobApprovalData->total_prod_qty : 0;
            $row->total_out_qty = (!empty($jobApprovalData->total_out_qty)) ? $jobApprovalData->total_out_qty : 0;
            $row->output_qty = (!empty($jobApprovalData->output_qty)) ? $jobApprovalData->output_qty : 0;
            $row->ch_qty = (!empty($jobApprovalData->ch_qty)) ? $jobApprovalData->ch_qty : 0;

            $row->unaccepted_qty = ($row->inward_qty ) - ($row->in_qty-$row->ch_qty);
            // $row->accepted_qty = ($row->in_qty + $row->outward_qty) - $row->ch_qty;
            $row->accepted_qty=0;
            if(!empty($jobApprovalData)){
                if($jobApprovalData->stage_type == 3 || $jobApprovalData->stage_type == 7 || $jobApprovalData->stage_type == 5){
                    $row->accepted_qty = $row->in_qty - $row->ch_qty;
                }else{
                    $row->accepted_qty = ($row->in_qty + $row->outward_qty) - $row->ch_qty;
                }
            }
            
            
            $completeQty = $row->total_prod_qty;$in_qty = ($row->in_qty * $row->output_qty);
            $row->pending_prod_qty = $in_qty - $row->total_prod_qty;
            $row->pending_prod_movement = $row->ok_qty - $row->total_out_qty;
           
            $row->scrap_qty = (!empty($jobApprovalData->pre_finished_weight)) ? round(($jobApprovalData->pre_finished_weight - $jobApprovalData->finished_weight) * $in_qty, 2) : 0;
            $totalScrapQty += $row->scrap_qty;

            $processPer = ($completeQty > 0 && $in_qty > 0) ? ($completeQty * 100 / $in_qty) : "0";
            if ($completeQty == 0) :
                $row->status = '<span class="badge badge-pill badge-danger m-1">' . round($processPer, 2) . '%</span>';
            elseif ($in_qty > $completeQty) :
                $row->status = '<span class="badge badge-pill badge-warning m-1">' . round($processPer, 2) . '%</span>';
            elseif ($in_qty == $completeQty) :
                $row->status = '<span class="badge badge-pill badge-success m-1">' . round($processPer, 2) . '%</span>';
            else :
                $row->status = '<span class="badge badge-pill badge-dark m-1">' . round($processPer, 2) . '%</span>';;
            endif;

            $row->process_approvel_data = $jobApprovalData;
            $dataRows[] = $row;

            $totalCompleteQty += $completeQty;
            $totalRejectQty += $row->total_rejection_qty;

            if ($row->inward_qty == 0 and $row->in_qty == 0 and $s > 0) :
                $stg[] = ['process_id' => $row->process_id, 'process_name' => $row->process_name, 'sequence' => ($s - 1)];
            else :
                if (!empty($row->process_id)) :
                    $runningStages[] = $row->process_id;
                endif;
            endif;
            $s++;
        endforeach;
        $completeQty = 0;
        $processPer = 0;
        $totalReworkRejectionQty = 0;

        $jobCardData->tblId = "jobStages";
        $jobProcessPer = (!empty($totalCompleteQty)) ? ($totalCompleteQty * 100 / (($jobCardData->qty * count($process)) - ($totalRejectQty + $totalReworkRejectionQty))) : "0";
        $jobCardData->jobPer = round($jobProcessPer, 2);
        $jobCardData->job_order_status = $jobCardData->order_status;
        if ($jobCardData->order_status == 0) :
            $jobCardData->order_status = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
        elseif ($jobCardData->order_status == 1) :
            $jobCardData->order_status = '<span class="badge badge-pill badge-primary m-1">Start</span>';
        elseif ($jobCardData->order_status == 2) :
            $jobCardData->tblId = "jobStages2";
            $jobCardData->order_status = '<span class="badge badge-pill badge-warning m-1">In-Process</span>';
        elseif ($jobCardData->order_status == 3) :
            $jobCardData->order_status = '<span class="badge badge-pill badge-info m-1">On-Hold</span>';
        elseif ($jobCardData->order_status == 4) :
            $jobCardData->tblId = "jobStages4";
            $jobCardData->order_status = '<span class="badge badge-pill badge-success m-1">Complete</span>';
        else :
            $jobCardData->tblId = "jobStages5";
            $jobCardData->order_status = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
        endif;

        $stages['stages'] = $stg;
        $stages['rnStages'] = $runningStages;
        $jobCardData->processData = $dataRows;
        $this->data['dataRow'] = $jobCardData;
        $this->data['stageData'] = $stages;
        $this->data['totalScrapQty'] = $totalScrapQty;
        $this->data['processDataList'] = $this->process->getProcessList();
        $this->data['jobBom'] = $this->jobcard->getProcessWiseRequiredMaterials($jobCardData)['result'];
        $reqMaterials = $this->jobcard->getMaterialIssueData($jobCardData);
        $this->data['reqMaterials'] = (!empty($reqMaterials['resultData'])) ? $reqMaterials['resultData'] : '';
        $this->load->view($this->jobcardDetail, $this->data);
    }

    public function jobDetail($id)
    {
        $jobCardData = $this->jobcard->getJobcard($id);

        if (empty($jobCardData->party_name)) {
            $jobCardData->party_name = "Self Stock";
        }

        $process = explode(",", $jobCardData->process);
        $jobApprovalData = array();
        if ($jobCardData->order_status == 0) // If Job card has not Started
        {
            foreach ($process as $process_id) {
                $prData = new Stdclass();
                if (!empty($process_id)) :
                    $prData = $this->process->getProcess($process_id);
                endif;
                $jobApprovalData['in_process_id'] = $process_id;
                $jobApprovalData['process_name'] = ((!empty($prData)) ? $prData->process_name : "Raw Material");
            }
        } else // If Job card Started
        {
            $jobApprovalData = $this->processMovement->getApprovalDataByJob($id, 1);
        }

        $jobCardData->first_process_id = $process[1];
        $dataRows = array();

        $jobCardData->tblId = "jobStages";
        $jobProcessPer = (!empty($totalCompleteQty)) ? ($totalCompleteQty * 100 / (($jobCardData->qty * count($process)) - ($totalRejectQty + $totalReworkRejectionQty))) : "0";
        $jobCardData->jobPer = round($jobProcessPer, 2);
        $jobCardData->job_order_status = $jobCardData->order_status;
        if ($jobCardData->order_status == 0) :
            $jobCardData->order_status = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
        elseif ($jobCardData->order_status == 1) :
            $jobCardData->order_status = '<span class="badge badge-pill badge-primary m-1">Start</span>';
        elseif ($jobCardData->order_status == 2) :
            $jobCardData->tblId = "jobStages2";
            $jobCardData->order_status = '<span class="badge badge-pill badge-warning m-1">In-Process</span>';
        elseif ($jobCardData->order_status == 3) :
            $jobCardData->order_status = '<span class="badge badge-pill badge-info m-1">On-Hold</span>';
        elseif ($jobCardData->order_status == 4) :
            $jobCardData->tblId = "jobStages4";
            $jobCardData->order_status = '<span class="badge badge-pill badge-success m-1">Complete</span>';
        else :
            $jobCardData->tblId = "jobStages5";
            $jobCardData->order_status = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
        endif;

        $jobApprovalData1 = $jobApprovalData;
        $jobApprovalData = array();
        $rej_belongs = $this->processMovement->getRejBelongsToStages($id);
        foreach ($jobApprovalData1 as $jaRow) {
            foreach ($rej_belongs as $rjRow) {
                if (!empty($rjRow->process_id) and $jaRow->in_process_id == $rjRow->process_id) {
                    $jaRow->total_rej_belongs = $rjRow->total_rej_belongs;
                } else {
                    $jaRow->total_rej_belongs = 0;
                }
            }
            $jobApprovalData[] = $jaRow;
        }

        $jobCardData->processData = $dataRows;
        $this->data['dataRow'] = $jobCardData;
        $this->data['jobApprovalData'] = $jobApprovalData;
        $this->data['jobBom'] = $this->jobcard->getProcessWiseRequiredMaterials($jobCardData)['result'];
        $reqMaterials = $this->jobcard->getMaterialIssueDataNew($jobCardData);
        $this->data['reqMaterials'] = (!empty($reqMaterials['resultData'])) ? $reqMaterials['resultData'] : '';
        $this->data['reqMaterialsRows'] = (!empty($reqMaterials['result'])) ? $reqMaterials['result'] : '';
        //print_r($jobApprovalData);exit;
        $this->load->view($this->jobDetail, $this->data);
    }

    public function getLastActivitLog()
    {
        $trans_id = $this->input->post('trans_id');
        $transData = $this->jobcard->getLastActivitLog($trans_id);

        $tbody = '';
        $i = 1;
        $activity = '';
        if (!empty($transData)) {
            foreach ($transData as $row) :
                $created_at = date("Y-m-d H:i", strtotime($row->created_at));
                $updated_at = date("Y-m-d H:i", strtotime($row->updated_at));
                if ($created_at == $updated_at) {
                    $activity = 'Created';
                } else {
                    $activity = 'Updated';
                }
                $empData = $this->employee->getEmp($row->created_by);
                $tbody .= '<tr>
                    <td>' . $i . '</td>
                    <td>' . formatDate($row->entry_date) . '</td>
                    <td>' . $row->qty . '</td>
                    <td>' . $row->production_time . '</td>
                    <td>' . $empData->emp_name . '</td>
                    <td>' . $activity . '</td>
                </tr>';
                $i++;
            endforeach;
        } else {
            $tbody .= '<tr>
                <td class="text-center" colspan="8">No Data Found</td>
            </tr>';
        }

        $this->printJson(['status' => 1, 'tbody' => $tbody]);
    }

    public function updateJobProcessSequance()
    {
        if (empty($this->input->post('id')) or empty($this->input->post('process_id'))) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $stageRows = $this->jobcard->updateJobProcessSequance($this->input->post());
            $this->printJson(['status' => 1, 'stageRows' => $stageRows[0], 'pOptions' => $stageRows[1]]);
        endif;
    }

    public function removeJobStage()
    {
        if (empty($this->input->post('id')) or empty($this->input->post('process_id'))) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $stageRows = $this->jobcard->removeJobStage($this->input->post());
            $this->printJson(['status' => 1, 'stageRows' => $stageRows[0], 'pOptions' => $stageRows[1]]);
        endif;
    }
    public function addJobStage()
    {
        if (empty($this->input->post('id')) or empty($this->input->post('process_id'))) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $data = $this->input->post();
            $data['created_by'] = $this->session->userdata('loginId');
            $stageRows = $this->jobcard->addJobStage($data);
            $this->printJson(['status' => 1, 'stageRows' => $stageRows[0], 'pOptions' => $stageRows[1]]);
        endif;
    }

    /* Process identification tag Print Data */
    public function printProcessIdentification($id)
    {
        $jobData = $this->processMovement->getOutwardTransPrint($id);
        $batchData = $this->jobcard->getBatchHeatByJobId($jobData->job_card_id);
        $partyName = '';
        $title = 'Process Tag';
        $process_name = '';
        $next_process = '';
        $vendor_mc = '';
        $crnt_prs_vendor_mc = '';
        $process_name = (!empty($jobData->process_name)) ? $jobData->process_name : "Raw Material";
        $next_process = (!empty($jobData->next_process)) ? $jobData->next_process : "Final Inspection";

        if ($jobData->send_to == 0) {
            $vendor_mc = (!empty($jobData->mc_name)) ? $jobData->mc_name : "IN HOUSE";
        }
        if ($jobData->send_to == 1 && $jobData->mfg_by == 2) {
            $vendor_mc = (!empty($jobData->party_name)) ? $jobData->party_name : "VENDOR";
        }
        if ($jobData->send_to == 1 && $jobData->mfg_by == 1) {
            $vendor_mc = (!empty($jobData->mc_name)) ? $jobData->mc_name : "IN HOUSE";
        }
        if ($jobData->send_to == 2) {
            $vendor_mc = (!empty($jobData->store_location)) ? $jobData->store_location : "STORE";
            $next_process = 'STORE';
        }

        $logData = $this->processMovement->getProcessMachineAndVendorList(['process_id' => $jobData->in_process_id, 'job_card_id' => $jobData->job_card_id]);
        // print_r($logData);exit;
        $machine_vendor = [];
        if (!empty($logData)) {
            foreach ($logData as $log) {
                if (!empty($log->machine_id) || $log->mfg_by == 1) {
                    $machine_vendor[] = '[' . $log->machine_code . '] ' . $log->machine_name;
                }
                if (!empty($log->vendor_id) && $log->mfg_by == 2) {
                    $machine_vendor[] = $log->party_name;
                }
            }
            $crnt_prs_vendor_mc = !empty($machine_vendor) ? implode(", ", array_unique($machine_vendor)) : '';
        }
        $crnt_process_no = '';
        $nxt_process_no = '';
        if (!empty($jobData->in_process_id)) {
            $crntprdProcess = $this->item->getPrdProcessDataProductProcessWise(['process_id' => $jobData->in_process_id, 'item_id' => $jobData->product_id]);
            if($this->CONTROL_PLAN ==1){
                $pfcData = $this->controlPlan->getPfcForProcess($crntprdProcess->pfc_process);
                $crnt_process_no = implode(",", array_column($pfcData, 'process_no'));
            }
           
        }
        if (!empty($jobData->out_process_id)) {
            $nxtPrdProcess = $this->item->getPrdProcessDataProductProcessWise(['process_id' => $jobData->out_process_id, 'item_id' => $jobData->product_id]);
            if($this->CONTROL_PLAN ==1){
                $nxtPfcData = $this->controlPlan->getPfcForProcess($nxtPrdProcess->pfc_process);
                $nxt_process_no = implode(",", array_column($nxtPfcData, 'process_no'));
            }
        }

        if (!empty($jobData->next_process)) {
            $mtitle = 'Process Tag';
            $revno = date('d.m.Y <br> h:i:s A');
        } else {
            $mtitle = 'Final Inspection	OK Material';
            $revno = 'F QA 25<br>(01/01.10.2021)';
        }

        $logo = base_url('assets/images/logo.png');


        $topSectionO = '<table class="table">
                                <tr>
                                    <td style="width:20%;"><img src="' . $logo . '" style="height:40px;"></td>
                                    <td class="org_title text-center" style="font-size:1rem;width:50%;">' . $title . '</td>
                                    <td style="width:30%;" class="text-right"><span style="font-size:0.8rem;">' . $revno . '</td>
                                </tr>
                            </table>';

        $itemList = '<table class="table tag_print_table">
					<tr class="text-center">
						<td style="width:70px;"><b>Job No</b></td>
						<td><b>Date</b></td>
						<td><b>Job Qty</b></td>
						<td><b>Batch No.</b></td>
						<td><b>Heat No.</b></td>
					</tr>
					<tr class="text-center">
						<td>' . $jobData->job_number . '</td>
						<td>' . formatDate($jobData->entry_date) . '</td>
						<td>' . floatVal($jobData->total_job_qty) . '</td>
						<td>' . $batchData['batch_no'] . '</td>
						<td>' . $batchData['heat_no'] . '</td>
					</tr>
					<tr class="bg-light">
						<td><b>Part</b></td>
						<td colspan="4">' . $jobData->full_name . '</td>
					</tr>
				</table>
				<table class="table tag_print_table">
					<tr>
						<td style="width:100px;"><b>Compl. Process</b></td>
						<td colspan="3">' . $process_name . (!empty($crnt_process_no) ? ' [' . $crnt_process_no . ']' : '') . '</td>
					</tr>
                    <tr>
						<td><b>Mc/Vendor</b></td>
						<td colspan="3">' . $crnt_prs_vendor_mc . '</td>
					</tr>
					<tr>
						<td><b>Qty.</b></td>
						<td colspan="3">' . floatVal($jobData->qty) . '</td>
					</tr>
					<tr>
						<td><b>Next Process</b></td>
						<td colspan="3">' . $next_process . (!empty($nxt_process_no) ? ' [' . $nxt_process_no . ']' : '') . '</td>
					</tr>
					<tr>
						<td><b>Mc/Vendor</b></td>
						<td colspan="3">' . $vendor_mc . '</td>
					</tr>
				</table>';
        //$originalCopy = '<div style="text-align:center;float:left;padding:1mm 1mm; ">' . $topSectionO . $itemList . '</div>';
        $originalCopy = '<div style="text-align:center;float:left;padding:1mm 1mm;rotate: -90;position: absolute;bottom:1mm;width:95mm; ">' . $topSectionO . $itemList . '</div>';

        $pdfData = $originalCopy;
        //$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [75, 80]]);
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 75]]); // Landscap
        $pdfFileName = $mtitle . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
        $mpdf->AddPage('L', '', '', '', '', 0, 0, 2, 2, 1, 1);
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }

    public function printTag($tag_type, $id)
    {
        $tagData = $this->jobcard->getTagData($id);
        $rejData = $this->processMovement->getRejCFTData(['job_trans_id' => $tagData->id, 'entry_type' => 1]);

        $vendorName = (!empty($tagData->party_name) &&  $tagData->mfg_by==2) ? $tagData->party_name : $tagData->operator_name;
        $title = "";
        $mtitle = "";
        $revno = "";
        $qtyLabel = "";
        $qty = 0;
        if ($tag_type == "REJ") :
            $mtitle = 'Rejection at M/c';
            $revno = 'R-QC-65 (00/01.10.22)';
            $qtyLabel = "Rej Qty";
            $qty = $rejData->rej_qty;
        elseif ($tag_type == "REW") :
            $mtitle = 'Rework at M/c';
            $revno = 'R-QC-66 (00/01.10.22)';
            $qtyLabel = "RW Qty";
            $qty = $rejData->rw_qty;
        elseif ($tag_type == "SUSP") :
            $mtitle = 'Suspected At M/c';
            $revno = 'R-QC-67 (00/01.10.22)';
            $qtyLabel = "Susp. Qty";
            $qty = $rejData->hold_qty;
        endif;

        $logo = base_url('assets/images/logo.png');


        $topSection = '<table class="table">
            <tr>
                <td style="width:20%;"><img src="' . $logo . '" style="height:40px;"></td>
                <td class="org_title text-center" style="font-size:1rem;width:50%;">' . $mtitle . ' <br><small><span class="text-dark">' . $title . '</span></small></td>
                <td style="width:30%;" class="text-right"><span style="font-size:0.8rem;">' . $revno . '</td>
            </tr>
        </table>';

        $itemList = '<table class="table table-bordered vendor_challan_table">
			<tr>
				<td style="font-size:0.7rem;"><b>Job No</b></td>
				<td style="font-size:0.7rem;">' . $tagData->job_number . '</td>
				<td style="font-size:0.7rem;"><b>Date</b></td>
				<td style="font-size:0.7rem;">' . formatDate($tagData->entry_date) . '</td>
			</tr>
			<tr>
				<td style="font-size:0.7rem;"><b>Part</b></td>
				<td style="font-size:0.7rem;" colspan="3">' . $tagData->full_name . '</td>
			</tr>
			<tr>
				<td style="font-size:0.7rem;"><b>Prod Qty</b></td>
				<td style="font-size:0.7rem;">' . ($tagData->qty + $rejData->rej_qty + $rejData->rw_qty + $rejData->hold_qty) . '</td>
				<td style="font-size:0.7rem;"><b>' . $qtyLabel . '</b></td>
				<td style="font-size:0.7rem;">' . $qty . '</td>
			</tr>
			<tr>
				<td style="font-size:0.7rem;"><b>Mfg. By</b></td>
				<td style="font-size:0.7rem;">' . $vendorName . '</td>
				<td style="font-size:0.7rem;"><b>M/c No</b></td>
				<td style="font-size:0.7rem;">'.(!empty($tagData->machine_code)?'[' . $tagData->machine_code . ']':'') . $tagData->machine_name . '</td>
			</tr>
			<tr>
				<td style="font-size:0.7rem;"><b>Issue By</b></td>
				<td style="font-size:0.7rem;" colspan="3">' . $tagData->emp_name . '</td>
			</tr>
		</table>';
        $pdfData = '<div style="width:97mm;height:50mm;text-align:center;float:left;padding:0mm 0.7mm;">' . $topSection . $itemList . '</div>';

        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 60]]);
        $pdfFileName = str_replace(" ", "_", str_replace("/", " ", $mtitle)) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
        $mpdf->AddPage('P', '', '', '', '', 0, 0, 2, 2, 2, 2);
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }

    function printDetailedRouteCard($id)
    {
        $this->data['jobData'] = $this->jobcard->getJobcard($id);
        $jobCardData = $this->data['jobData'];
        $this->data['companyData'] = $this->jobcard->getCompanyInfo();
        $this->data['userDetail'] = $this->employee->getEmp($this->data['jobData']->created_by);

        $reqMaterials = $this->jobcard->getMaterialIssueData($jobCardData);
        $this->data['materialDetail'] =  (!empty($reqMaterials['result'])) ? $reqMaterials['result'] : '';
        // print_r($reqMaterials);exit;
        $this->data['inhouseProduction'] = $this->processMovement->getMovementTransactions($id, 0);
        $this->data['vendorProduction'] = $this->processMovement->getMovementTransactions($id, 4);
        $response = "";
        $logo = base_url('assets/images/logo.png');
        $this->data['letter_head'] = base_url('assets/images/letterhead_top.png');
        $this->data['letter_footer'] = base_url('assets/images/lh-footer.png');


        $process = explode(",", $jobCardData->process);
        $dataRows = array();
        $totalCompleteQty = 0;
        $totalRejectQty = 0;

        foreach ($process as $key => $value) :
            $row = new stdClass;
            $jobProcessData = $this->processMovement->getProcessWiseApprovalData($id, $value);
            $row->process_name = $this->process->getProcess($value)->process_name;
            
            $operation = array();
            if($this->CONTROL_PLAN ==1){
                $prdProcess = $this->item->getPrdProcessDataProductProcessWise(['process_id' => $value, 'item_id' => $jobCardData->product_id]);
               
                $pfcData = [];
                if(!empty($prdProcess->pfc_process)){
                    $pfcData = $this->controlPlan->getPfcForProcess($prdProcess->pfc_process);
                }
                
                if(!empty($pfcData)) {
                    foreach ($pfcData as $pfc) {
                        $operation[] = '[' . $pfc->process_no . '] ' . $pfc->parameter . '';
                    }
                }
            }
            
            $row->operation = implode("<hr style='width:100%;margin:5px;'>", $operation);
            $row->process_id = $value;
            $row->job_id = $id;
            $row->regular_in_qty = (!empty($jobProcessData->in_qty)) ? $jobProcessData->in_qty : 0;
            $row->in_qty = (!empty($jobProcessData->in_qty)) ? $jobProcessData->in_qty : 0;
            $row->ok_qty = (!empty($jobProcessData->ok_qty)) ? $jobProcessData->ok_qty : 0;
            $row->total_rework_qty = (!empty($jobProcessData->total_rework_qty)) ? $jobProcessData->total_rework_qty : 0;
            $row->total_rejection_qty = (!empty($jobProcessData->total_rejection_qty)) ? $jobProcessData->total_rejection_qty : 0;
            $row->total_prod_qty = (!empty($jobProcessData->total_prod_qty)) ? $jobProcessData->total_prod_qty : 0;
            
            $row->pending_prod_qty = ($row->in_qty*$jobProcessData->output_qty) - $row->total_prod_qty;
            $totalCompleteQty += $row->total_prod_qty;
            $dataRows[] = $row;
        endforeach;
        $this->data['processDetail'] = $dataRows;

        $pdfData = $this->load->view('production/jobcard/view', $this->data, true);
        
        $htmlHeader = '<img src="' . $this->data['letter_head'] . '" class="img">';

        $mpdf = $this->m_pdf->load();
        $pdfFileName = 'DC-REG-' . $id . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->SetHTMLFooter($htmlFooter);

        $mpdf->AddPage('P', '', '', '', '', 5, 5, 35, 20, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-P');
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }

    /* Material Return , Scrap , Used in Job */
    public function materialReturn()
    {
        $data = $this->input->post();
        $this->data['dataRow'] = $data;
        $this->data['locationData'] = $this->stockTransac->getStoreLocationList(['store_type'=>'0,15,4','group_store_opt'=>1,'final_location'=>1])['storeGroupedArray']; ;
        $this->data['batchData'] = $this->jobcard->getBatchNoForReturnMaterial($data['job_card_id'], $data['dispatch_id'])['options'];
        $issueMtrData = $this->jobcard->getIssueMaterialDetail($data['job_card_id'], $data['item_id']);// print_r($issueMtrData);exit;
        $this->data['dataRow']['pendingQty'] = $issueMtrData->issue_qty - abs($issueMtrData->used_qty);
        $this->data['jobData'] = $this->jobcard->getJobcard($data['job_card_id']);
        $this->data['transData'] = $this->jobcard->getMaterialReturnTrans(['job_card_id' => $data['job_card_id'], 'item_id' => $data['item_id']])['resultHtml'];
        $this->load->view($this->material_return_form, $this->data);
    }

    public function saveMaterialReturn()
    {
        $data = $this->input->post();
        $data['created_by'] = $this->session->userdata('loginId');
       
        if (empty($data['ref_type']))
            $errorMessage['ref_type'] = "Return Type is required.";
        if ($data['ref_type'] == 10) {
            if (empty($data['location_id']))
                $errorMessage['location_id'] = "Location is required.";
            if (empty($data['batch_no']))
                $errorMessage['batch_no'] = "Batch No is required.";
        }
        if ($data['ref_type'] == 18) {
            if (empty($data['location_id']))
                $errorMessage['location_id'] = "Location is required.";
        }
        if (empty($data['qty'])){
            $errorMessage['qty'] = "Qty is required.";
        }
        if (!empty($errorMessage)) {
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        } else {
            $result = $this->jobcard->saveMaterialReturn($data);
            $this->printJson($result);
        }
    }
 
    public function deleteMaterialReturn()
    {
        $data = $this->input->post();
        $result = $this->jobcard->deleteMaterialReturn($data['id']);
        $this->printJson($result);
    }

    public function updateJobQty()
    {
        $this->data['job_card_id'] = $job_card_id = $this->input->post('id');
        $this->data['logData'] = $this->jobcard->getJobLogData($this->data['job_card_id']);
        $jobData = $this->jobcard->getJobcard($job_card_id);
        $batchData =  $this->jobcard->getMaterialIssueData($jobData)['resultData'];
        $this->data['productProcessAndRaw'] = $this->jobcard->getProductProcess(['product_id'=>$jobData->product_id,'batch_no'=>$batchData['batch_no']],$job_card_id);
        $this->load->view($this->updateJobForm, $this->data);
    }

    public function saveJobQty()
    {
        $data = $this->input->post();
        $errorMessage = array();
        $jobdata = $this->jobcard->getJobPendingQty($data['job_card_id']);
        $data['product_id'] = $jobdata->product_id;

        if ($data['log_type'] == -1) :
            $pendingQty = $jobdata->ok_qty - $jobdata->total_out_qty;
            if ($pendingQty < $data['qty']) :
                $errorMessage['qty'] = "Invalid Qty.";
            endif;
        endif;
        
        if(empty($data['qty']))
            $errorMessage['qty'] = 'Quantity is required.';

        if(!isset($data['batch_no']))
            $errorMessage['updateQtyMaterial'] = 'Material is required.';
           
        if(empty($data['bom_item_id'][0]))
            $errorMessage['updateQtyMaterial'] = 'Material is required.';
            
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->jobcard->saveJobQty($data);
            $tbody = '';
            $i = 1;
            if (!empty($result)) :
                foreach ($result as $row) :
                    $deleteParam = $row->id . ",'Jobcard Log'";
                    $logType = ($row->log_type == 1) ? '(+) Add' : '(-) Reduce';
                    $tbody .= '<tr>
                        <td>' . $i++ . '</td>
                        <td>' . formatDate($row->log_date) . '</td>
                        <td>' . $logType . '</td>
                        <td>' . $row->qty . '</td>
                        <td><a class="btn btn-sm btn-outline-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trashJobUpdateQty(' . $deleteParam . ');" datatip="Remove" flow="left"><i class="ti-trash"></i></a></td>
                    </tr>';
                endforeach;
            endif;
            $this->printJson(['status' => 1, 'tbody' => $tbody]);
        endif;
    }

    public function deleteJobUpdateQty()
    {
        $id = $this->input->post('id');
        $logdata = $this->jobcard->getJobLog($id);
        $errorMessage = '';
        if ($logdata->log_type == 1) :
            $jobdata = $this->jobcard->getJobPendingQty($logdata->job_card_id);
            $pendingQty = $jobdata->ok_qty - $jobdata->total_out_qty;
            if ($pendingQty < $logdata->qty) :
                $errorMessage = "Sorry...! You can't delete this jobcard log because This Qty. moved to next process.";
            endif;
        endif;

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $result = $this->jobcard->deleteJobUpdateQty($id);

            $tbody = '';
            $i = 1;
            if (!empty($result)) :
                foreach ($result as $row) :
                    $deleteParam = $row->id . ",'Jobcard Log'";
                    $logType = ($row->log_type == 1) ? '(+) Add' : '(-) Reduce';
                    $tbody .= '<tr>
                        <td>' . $i++ . '</td>
                        <td>' . formatDate($row->log_date) . '</td>
                        <td>' . $logType . '</td>
                        <td>' . $row->qty . '</td>
                        <td><a class="btn btn-sm btn-outline-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trashJobUpdateQty(' . $deleteParam . ');" datatip="Remove" flow="left"><i class="ti-trash"></i></a></td>
                    </tr>';
                endforeach;
            endif;
            $this->printJson(['status' => 1, 'tbody' => $tbody]);
        endif;
    }

    public function getHandoverData()
    {
        $used_at = $this->input->post('used_at');
        $out_process_id = $this->input->post('out_process_id');
       
      

        $handover = '<option value="">Select</option>';
        if (empty($used_at)) :
            $empData = $this->machine->getProcessWiseMachine($out_process_id);
            $handover .= '<option value="0">Department</option>';
            foreach ($empData as $row) :
                $handover .= "<option value='" . $row->id . "'   data-row='" . json_encode($row) . "'>[" . $row->item_code . "] " . $row->item_name . " </option>";
            endforeach;
        elseif ($used_at == 1) :
            $partyData = $this->party->getVendorList();
            foreach ($partyData as $row) :
                $handover .= "<option value='" . $row->id . "' data-row='" . json_encode($row) . "'>[" . $row->party_code . "] " . $row->party_name . " </option>";
            endforeach;
        elseif ($used_at == 2) :
            $locationData = $this->stockTransac->getStoreLocationList(['prd_movement'=>'1','group_store_opt'=>1,'final_location'=>1])['storeGroupedArray']; 
            if(!empty($locationData)):
                foreach ($locationData as $key=>$option) :
                    $handover .= '<optgroup label="' . $key . '">';
                    foreach ($option as $row) :
                        $handover .= '<option value="' . $row->id . '">' . $row->location . ' </option>';
                    endforeach;
                    $handover .= '</optgroup>';
                endforeach;
            endif;
        endif;

        $this->printJson(['status' => 1, 'handover' => $handover]);
    }

    public function setupRequest()
    {
        $id = $this->input->post('id');
        $this->data['approvalData'] = $approvalData = $this->processMovement->getApprovalData($id);
        $this->data['machine'] = $this->processMovement->getCurrentProcessMachine(['job_card_id' => $approvalData->job_card_id, 'process_id' => $approvalData->in_process_id]);
        $this->data['machineList'] = $this->machine->getMachineList();
        $this->data['setterList'] = $this->employee->getSetterList();
        $this->data['inspectorList'] = $this->employee->getSetterInspectorList();
        $this->data['htmlData'] = $this->setupReqtransView($id);
        $this->load->view($this->setup_request, $this->data);
    }

    public function setupRequestSave()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['machine_id']))
            $errorMessage['machine_id'] = "Machine is required.";

        if (empty($data['setter_id']))
            $errorMessage['setter_id'] = "Setter is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $data['created_at'] = date("Y-m-d H:i:s");
            $result = $this->jobcard->setupRequestSave($data);
            $result['htmlData'] = $this->setupReqtransView($data['job_approval_id']);
            $this->printJson($result);
        endif;
    }

    public function setupReqtransView($job_approval_id)
    {
        $setupData = $this->productSetup->getSetupRequestJobApprovalWise($job_approval_id); //print_r($setupData);print_r($this->db->last_query());
        $html = "";
        if (!empty($setupData)) {
            $i = 1;
            foreach ($setupData as $row) {

                $btn = '';
                $status = "";
                if ($row->status == 0) {
                    $status = 'Pending';
                    $btn = "<button type='button' onclick='trashSetupReq(" . $row->id . "," . $row->job_approval_id . ");' class='btn btn-sm btn-outline-danger waves-effect waves-light permission-remove' title='Delete'><i class='ti-trash'></i></button>";
                }
                if ($row->status == 1) {
                    $status = ' In Process';
                }
                if ($row->status == 2) {
                    $status = ' Finish By Setter';
                }
                if ($row->status == 3) {
                    $status = 'Approved';
                }
                if ($row->status == 4) {
                    $status = 'Send For Reset up';
                }
                if ($row->status == 5) {
                    $status = ' On Hold';
                }
                if ($row->status == 6) {
                    $status = 'Accept By Inspector';
                }

                $html .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . formatDate($row->created_at) . '</td>
                    <td>' . sprintf($row->req_prefix . "%03d", $row->req_no) . '</td>
                    <td>' . $row->emp_name . '</td>
                    <td>' . ((!empty($row->machine_code) ? '[' . $row->machine_code . '] ' : '') . $row->machine_name) . '</td>
                    <td>' . $row->qc_inspector . '</td>
                    <td>' . $status . '</td>
                    <td>' . $btn . '</td>
                </tr>';
            }
        }
        return $html;
    }

    public function trashSetupReq()
    {
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $result = $this->jobcard->trashSetupReq($data);
            $result['htmlData'] = $this->setupReqtransView($data['job_approval_id']);
            $this->printJson($result);
        endif;
    }
    
    
    /* Created By :- Sweta @02-09-2023 */
    public function printJobcard(){
        $job_card_id = $this->input->post('id');
        $this->data['job_card_id'] = $job_card_id;
        $jobData = $this->jobcard->getJobcard($job_card_id);        
        $this->data['processData'] = $this->jobcard->getJobApprovalData(['process_id'=>$jobData->process,'job_card_id'=>$job_card_id]);
        $this->load->view($this->print_job_card, $this->data);
    }

    /* Created By :- Sweta @02-09-2023 */
    public function printPir($id){
        $approvalData = $this->processMovement->getApprovalData($id);
        $jobData = $this->jobcard->getJobcard($approvalData->job_card_id);
        $prsData = $this->process->getProcess($approvalData->in_process_id);
        $this->data['job_card_id'] = $approvalData->job_card_id;
        $this->data['process_id'] = $approvalData->in_process_id;
        $this->data['process_name'] = $prsData->process_name;
        $this->data['name'] = $this->department->getDepartment($prsData->dept_id)->name; // 26-03-2024
        $this->data['machine_name'] = !empty($mcData->item_name)?$mcData->item_name:'';
        $this->data['machine_code'] = !empty($mcData->item_code)?$mcData->item_code:'';
        $this->data['jobData'] = $jobData;
        $pirData  = $this->pir->getPIRReports(['job_card_id'=>$approvalData->job_card_id,'process_id'=>$approvalData->in_process_id,'machine_id'=>'','item_id'=>$jobData->product_id,'trans_date'=>date("Y-m-d"),'singleRow'=>1]);

        $this->data['job_approval_id'] = $approvalData->id;
        if(!empty($pirData)){
            $this->data['dataRow']=$pirData;	
        }
        $pfcProcess = $this->item->getPrdProcessDataProductProcessWise(['item_id' =>$jobData->product_id, 'process_id' => $approvalData->in_process_id]);
        $this->data['paramData'] = $this->controlPlan->getCPDimenstion(['item_id'=>$jobData->product_id,'control_method'=>'Production','pfc_id'=>$approvalData->pfc_ids,'responsibility'=>'OPR','parameter_type'=>1]);
        
        $logo = base_url('assets/images/logo.png');

        $pdfData = $this->load->view('production/jobcard/print_pir',$this->data,true);
        
        $prepare = $this->employee->getEmp($approvalData->created_by);
        $prepareBy = $prepare->emp_name.' <br>('.formatDate($approvalData->created_at).')';

        // 26-03-2024
        $htmlHeader  = '<table class="table">
                    <tr>
                        <td style="width:15%;"><img src="' . $logo . '" style="max-height:40px;"></td>
                        <td class="org_title text-center" style="font-size:1.5rem;">Daily In-Process Inspection Report</td>
                        <td style="width:15%;" class="text-center">F QA 10<br>(01 / 22.03.2024)</td>
                    </tr>
                </table>';

        $htmlFooter = '<table class="table top-table" style="margin-top:10px;">
                <tr>
                    <td style="width:75%;"></td>
                    <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';

        $mpdf = new \Mpdf\Mpdf();  
        $pdfFileName = 'pir_' . $id . '.pdf';     
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetWatermarkImage($logo,0.03,array(120,60));
        $mpdf->showWatermarkImage = true;        
        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->AddPage('L','','','','',5,5,20,5,5,5,'','','','','','','','','','A4-L');
        $mpdf->WriteHTML($pdfData);        
        $mpdf->Output($pdfFileName,'I');
    }

    /* Created By :- Sweta @02-09-2023 */
    public function printFir($id){
        $approvalData = $this->processMovement->getApprovalData($id);
        $jobData = $this->jobcard->getJobcard($approvalData->job_card_id);
        $prsData = $this->process->getProcess($approvalData->in_process_id);
        $this->data['job_card_id'] = $approvalData->job_card_id;
        $this->data['process_id'] = $approvalData->in_process_id;
        $this->data['process_name'] = $prsData->process_name;
        $this->data['machine_name'] = !empty($mcData->item_name)?$mcData->item_name:'';
        $this->data['machine_code'] = !empty($mcData->item_code)?$mcData->item_code:'';
        $this->data['jobData'] = $jobData;
        $pirData  = $this->pir->getPIRReports(['job_card_id'=>$approvalData->job_card_id,'process_id'=>$approvalData->in_process_id,'machine_id'=>'','item_id'=>$jobData->product_id,'trans_date'=>date("Y-m-d"),'singleRow'=>1]);

        $this->data['job_approval_id'] = $approvalData->id;
        if(!empty($pirData)){
            $this->data['dataRow']=$pirData;	
        }
        $pfcProcess = $this->item->getPrdProcessDataProductProcessWise(['item_id' =>$jobData->product_id, 'process_id' => $approvalData->in_process_id]);
        $this->data['paramData'] =  $this->controlPlan->getCPDimenstion(['item_id'=>$jobData->product_id,'control_method'=>'FIR','pfc_id'=>$approvalData->pfc_ids,'responsibility'=>'INSP']);

        $logo = base_url('assets/images/logo.png');

        $pdfData = $this->load->view('production/jobcard/print_fir',$this->data,true);
        
        $prepare = $this->employee->getEmp($approvalData->created_by);
        $prepareBy = $prepare->emp_name.' <br>('.formatDate($approvalData->created_at).')';

        $htmlHeader  = '<table class="table">
                    <tr>
                        <td style="width:15%;"><img src="' . $logo . '" style="max-height:40px;"></td>
                        <td class="org_title text-center" style="font-size:1.5rem;">INPROCESS (PATROL) INSPECTION REPORT</td>
                        <td style="width:15%;"></td>
                    </tr>
                </table>';

        $htmlFooter = '<table class="table top-table" style="margin-top:10px;">
                <tr>
                    <td style="width:75%;"></td>
                    <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';

        $mpdf = new \Mpdf\Mpdf();   
        $pdfFileName = 'pir_' . $id . '.pdf';     
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetWatermarkImage($logo,0.03,array(120,60));
        $mpdf->showWatermarkImage = true;        
        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->AddPage('L','','','','',5,5,20,10,5,5,'','','','','','','','','','A4-L');
        $mpdf->WriteHTML($pdfData);        
        $mpdf->Output($pdfFileName,'I');
    }

    public function materialAllocate(){
        $id = $this->input->get_post('id');
        $this->data['job_id'] = $id;
        $this->data['jobCardData'] =$jobCardData= $this->jobcard->getJobcard($id);
        $bomDataHtml = "";
        $itemKit = $this->jobcard->getJobBomData($id,$jobCardData->product_id);
        // print_r($itemKit);exit;
        if (!empty($itemKit)) {
            $i=1;
            foreach ($itemKit as $row) {
                $required_qty = $jobCardData->qty*$row->qty;
                $pending_qty = $required_qty-$row->allocated_qty;
                if($pending_qty > 0){
                    $bomDataHtml .= '<thead class="thead-info">
                        <tr>
                            <th colspan="3"> 
                                Item Name : <br> <b>'.$row->full_name.'<br>
                                <div class="error item_error_'.$row->ref_item_id.'"></div>
                                <input type="hidden" name="bom_item_id[]" value="'.$row->ref_item_id.'" >
                            </th>
                            <th>
                                Bom Qty (PCS): <br> <b>'.$row->qty.'</b>
                                <input type="hidden" name="bom_qty[]" value="'.$row->qty.'" >
                            </th>
                            
                            <th>
                                Required Qty (PCS) : <br>
                                <input type="text" name="req_qty[]" value="'.$pending_qty.'" data-bom_qty="'.$row->qty.'" readOnly class="form-control text-bold" style="background: transparent;border: none;font-weight : bold" >
                            </th>
                            
                        </tr>
                        <tr>
                            <th></th>
                            <th>Location</th>
                            <th>Store</th>
                            <th>Batch</th>
                            <th>Stock</th>
                        </tr>
                    </thead>';
                    $postData = ['item_id' => $row->ref_item_id,'stock_required'=>1];
                    if($row->item_type == 10){
                        $postData['location_ref_id'] =1;
                    }
                    $stockData = $this->store->getItemStockBatchWise($postData);
                    // print_r($this->db->last_query());
                    if(!empty($stockData)){
                        $bomDataHtml .= '<tbody>';
                        foreach($stockData as $stock){
                            $bomDataHtml .= '<tr>
                                <td>
                                    <input type="checkbox" id="md_ch_'.$i.'" name="batch_no['.$row->ref_item_id.'][]" class="filled-in batchCheck chk-col-success" value="'.$stock->batch_no.'"  data-rowid="'.$i.'"><label for="md_ch_'.$i.'" class="mr-3"></label>
                                    <input type="hidden" id="location_id'.$i.'"  name="location_id['.$row->ref_item_id.'][]" value="'.$stock->location_id.'"  disabled>
                                    <input type="hidden" id="stock_qty'.$i.'"  name="stock_qty['.$row->ref_item_id.'][]" value="'.$stock->qty.'"  disabled>
                                    <input type="hidden" id="item_type'.$i.'"  name="item_type['.$row->ref_item_id.'][]" value="'.$row->item_type.'"  disabled>
                                </td>
                                <td>'.$stock->location.'</td>
                                <td>'.$stock->store_name.'</td>
                                <td>'.$stock->batch_no.'</td>
                                <td>'.$stock->qty.'</td>
                            </tr>';
                            $i++;
                        }
                        $bomDataHtml .= '</tbody>';
                        
                    }
                    else{
                        $bomDataHtml .= '<tr><th colspan="5">No stock available.</th></tr>';
                    }
                }
            }
        } 
        $this->data['bomDataHtml'] = $bomDataHtml;
        $this->load->view('production/jobcard/material_allocate', $this->data);
    }
    
    public function saveAllocatedMaterial(){
        $data = $this->input->post(); //print_r($data);exit;
        $errorMessage = array();
        $kitData = $this->jobcard->getJobBomData($data['job_card_id'],$data['product_id']);
        $rmCount=0;
        foreach ($kitData as $kit) :
            if(isset($data['batch_no'][$kit->ref_item_id])){
                $issuedBatch = $this->jobcard->getJobMaterilIssueDataBomWise($data['job_card_id'],$kit->id);
                $batchNo = $data['batch_no'][$kit->ref_item_id];
                if(count(array_unique($batchNo)) > 1 && $kit->item_type ==3){
                    $errorMessage['error_msg'] = "Multiple batch not alloweded";
                }else{
                    foreach($batchNo as $batch){
                        if($batch != $issuedBatch->batch_no  && $kit->item_type ==3){
                            $errorMessage['error_msg'] = "Multiple batch not alloweded. OLD ISSUED BATCH : ".$issuedBatch->batch_no;
                        }
                    }
                }
                if($kit->item_type==3){
                    $rmCount++;
                }
            }
        endforeach;
        if($rmCount > 1){
            $errorMessage['error_msg'] = "Multiple Raw Material not allowed";
        }
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            unset($data['error_msg']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->jobcard->saveAllocatedMaterial($data));
        endif;
    }
    
    public function printSar($job_card_id,$in_process_id){
        $this->data['dataRow'] = $dataRow = $this->jobcard->getJobcard($job_card_id);
        $this->data['processData'] = $this->process->getProcess($in_process_id);

        $approvalData = $this->processMovement->getJobApprovalDetail(['process_id'=>$in_process_id, 'job_card_id'=>$job_card_id]);
        $this->data['paramData'] =  $this->controlPlan->getCPDimenstion(['item_id'=>(!empty($approvalData->product_id)?$approvalData->product_id:''), 'pfc_id'=>(!empty($approvalData->pfc_ids)?$approvalData->pfc_ids:''), 'control_method'=>'SAR', 'parameter_type'=>2]);
        $this->data['prodParamData'] =  $this->controlPlan->getCPDimenstion(['item_id'=>(!empty($approvalData->product_id)?$approvalData->product_id:''), 'pfc_id'=>(!empty($approvalData->pfc_ids)?$approvalData->pfc_ids:''), 'control_method'=>'SAR', 'parameter_type'=>1]);

        $logo = base_url('assets/images/logo.png');
        
		$pdfData = $this->load->view('production/jobcard/print_sar',$this->data,true);

        $htmlHeader  = '<table class="table">
                    <tr>
                        <td style="width:25%;"><img src="' . $logo . '" style="max-height:40px;"></td>
                        <td class="org_title text-center" style="font-size:1.5rem;">Setup Approval Report</td>
                        <td style="width:25%;" class="text-center">F-P & M-10<br>(01 / 22.03.2024)</td>
                    </tr>
                </table>';

		$htmlFooter = '<table class="table" style="border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;" class="text-center"><br><b>Inspected By </b></td>
							<td style="width:50%;" class="text-center"><br><b>Approved By</b></td>
						</tr>
					</table>
                    <table class="table top-table">
                        <tr>
                            <td style="width:75%;"></td>
                            <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                        </tr>
                    </table>';
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='SAR-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->AddPage('P','','','','',5,5,20,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');	
    }
}