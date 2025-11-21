<?php
class Jobcard extends MY_Controller
{
    private $indexPage = "jobcard/index";
    private $customerJobwork = "jobcard/customer_jobwork";
    private $jobcardForm = "jobcard/form";
    private $requiementForm = "jobcard/required_test";
    private $jobcardDetail = "jobcard/jobcard_detail";
    private $jobScrape = "jobcard/generate_scrape";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Jobcard";
		$this->data['headData']->controller = "jobcard";
	}
	
	public function index(){
        $this->data['headData']->pageUrl = "jobcard";
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->jobcard->getDTRows($this->input->post(),0);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->party_name = (!empty($row->party_name))?$row->party_name:"Self Stock";
            $row->party_code = (!empty($row->party_code))?$row->party_code:"Self Stock";
			if($row->order_status == 0):
				$row->order_status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
			elseif($row->order_status == 1):
				$row->order_status_label = '<span class="badge badge-pill badge-primary m-1">Start</span>';
            elseif($row->order_status == 2):
                $row->order_status_label = '<span class="badge badge-pill badge-warning m-1">In-Process</span>';
            elseif($row->order_status == 3):
                $row->order_status_label = '<span class="badge badge-pill badge-info m-1">On-Hold</span>';
            elseif($row->order_status == 4):
                $row->order_status_label = '<span class="badge badge-pill badge-success m-1">Complete</span>';
			else:
				$row->order_status_label = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
			endif;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getJobcardData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function customerJobWork(){
        $this->data['headData']->pageUrl = "jobcard/customerJobWork";
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->customerJobwork,$this->data);
    }

    public function customerJobWorkList(){
        $result = $this->jobcard->getDTRows($this->input->post(),1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->party_name = (!empty($row->party_name))?$row->party_name:"Self Stock";
            $row->party_code = (!empty($row->party_code))?$row->party_code:"Self Stock";
			if($row->order_status == 0):
				$row->order_status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
			elseif($row->order_status == 1):
				$row->order_status_label = '<span class="badge badge-pill badge-primary m-1">Start</span>';
            elseif($row->order_status == 2):
                $row->order_status_label = '<span class="badge badge-pill badge-warning m-1">In-Process</span>';
            elseif($row->order_status == 3):
                $row->order_status_label = '<span class="badge badge-pill badge-info m-1">On-Hold</span>';
            elseif($row->order_status == 4):
                $row->order_status_label = '<span class="badge badge-pill badge-success m-1">Complete</span>';
			else:
				$row->order_status_label = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
			endif;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getJobcardData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addJobcard(){
        $this->data['jobPrefix'] = "JOB/".$this->shortYear.'/';
        $this->data['jobNo'] = $this->jobcard->getNextJobNo(0);
        $this->data['jobwPrefix'] = "JOBW/".$this->shortYear.'/';
        $this->data['jobwNo'] = $this->jobcard->getNextJobNo(1);
        $this->data['customerData'] = $this->jobcard->getCustomerList();
        $this->data['productData'] = $this->item->getItemList(1);
        $this->load->view($this->jobcardForm,$this->data);
    }

    public function customerSalesOrderList(){
        $orderData = $this->jobcard->getCustomerSalesOrder($this->input->post('party_id'));
        $options = "<option value=''>Select Order No.</option>";
        foreach($orderData as $row):
            $options .= '<option value="'.$row->id.'">'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</option>';
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getProductList(){
		$data = $this->input->post();
		$this->printJson($this->jobcard->getProductList($data));
	}

    public function getProductProcess(){
        $data = $this->input->post();
        $this->printJson($this->jobcard->getProductProcess($data));
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if($data['party_id'] == "")
			$errorMessage['party_id'] = "Customer is required.";
		if(empty($data['product_id']))
			$errorMessage['product_id'] = "Product is required.";
		if(empty($data['qty']) || $data['qty'] == "0.000")
			$errorMessage['qty'] = "Quantity is required.";
		if(empty($data['process']))
			$errorMessage['process'] = "Product Process is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->jobcard->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->jobcard->getJobcard($id);
        $this->data['customerData'] = $this->jobcard->getCustomerList();
        $this->data['customerSalesOrder'] = $this->jobcard->getCustomerSalesOrder($this->data['dataRow']->party_id);

        $productPostData = ['sales_order_id'=>$this->data['dataRow']->sales_order_id,'product_id'=>$this->data['dataRow']->product_id];
        $this->data['productData'] = $this->jobcard->getProductList($productPostData);

        $productProcessData = ['product_id'=>$this->data['dataRow']->product_id];
        $this->data['productProcessAndRaw'] = $this->jobcard->getProductProcess($productProcessData,$id);

        $this->load->view($this->jobcardForm,$this->data);
    }

    public function view($id){
        $jobCardData = $this->jobcard->getJobcard($id);
        $jobCardData->party_code = (!empty($jobCardData->party_id))?$this->party->getParty($jobCardData->party_id)->party_code:"Self Stock";
        
        $itmData = $this->item->getItem($jobCardData->product_id);
        $jobCardData->product_name = $itmData->item_name;
        $jobCardData->product_code = $itmData->item_code;
        $jobCardData->unit_name = (!empty($itmData->unit_name)) ? $itmData->unit_name : '';
        $process = explode(",","0,".$jobCardData->process);
        $jobCardData->first_process_id = $process[1];
        $dataRows = array(); $totalCompleteQty=0;  $totalRejectQty=0;   $stages = array();$stg = array();$s=0;$runningStages = Array();$prevProcessId = 0;
        foreach($process as $key=>$value):
            $row = new stdClass;
            $jobProcessData = $this->production->getProcessWiseProduction($id,$value);
            $row->process_name = (!empty($value))?$this->process->getProcess($value)->process_name:"Initial Stage";
            $row->process_id = $value;
            $row->job_id = $id;
            $row->in_qty = (!empty($jobProcessData))?$jobProcessData->in_qty:0;
            if(!empty($value)):
                $prevProcessData = $this->production->getProcessWiseProduction($id,$prevProcessId);
                $row->inward_qty = (!empty($prevProcessData))?$prevProcessData->out_qty:0;
            else:
                $row->inward_qty = (!empty($jobProcessData))?$jobProcessData->in_qty:0;
            endif;
            $prevProcessId = $value;
            $row->rework_qty = (!empty($jobProcessData))?$jobProcessData->total_rework_qty:0;
            $row->rejection_qty = (!empty($jobProcessData))?$jobProcessData->total_rejection_qty:0;
            $row->out_qty = (!empty($jobProcessData))?$jobProcessData->out_qty:0;

            $completeQty = (!empty($jobProcessData))?($jobProcessData->total_rework_qty + $jobProcessData->total_rejection_qty + $jobProcessData->in_qty):0;

            $row->pending_qty = (!empty($jobProcessData))?($row->inward_qty - $completeQty):0;

            $processPer = ($completeQty > 0)?($completeQty * 100 / $row->inward_qty):"0";

            if($completeQty == 0):
                $row->status = '<span class="badge badge-pill badge-danger m-1">'.round($processPer,2).'%</span>';
            elseif($row->inward_qty > $completeQty):
                $row->status = '<span class="badge badge-pill badge-warning m-1">'.round($processPer,2).'%</span>';
            elseif($row->inward_qty == $completeQty):
                $row->status = '<span class="badge badge-pill badge-success m-1">'.round($processPer,2).'%</span>';
            else:
                $row->status = round($processPer,2);
            endif;            

            $row->process_approvel_data = $jobProcessData;
            $dataRows[] = $row;
			
            $totalCompleteQty += $completeQty;
            $totalRejectQty += (!empty($jobProcessData))?($jobProcessData->total_rework_qty + $jobProcessData->total_rejection_qty):0;

			if($row->in_qty == 0 and $s > 1):
				$stg[] = ['process_id' => $row->process_id, 'process_name' => $row->process_name, 'sequence' => ($s-1)];
			else:
                if(!empty($row->process_id)):
				    $runningStages[] = $row->process_id;
                endif;
			endif;
			$s++;
        endforeach; 

        $reworkData = $this->production->getReworkData($id);
        $reworkDataRows=array();
        $i=1;$prevProcessId=0;$completeQty=0;$processPer=0;$totalReworkRejectionQty = 0;
        foreach($reworkData as $row):
            $row->process_name = $this->process->getProcess($row->in_process_id)->process_name;
            $row->process_id = $row->in_process_id;
            $row->job_id = $id;
            if($i!=1):
                $prevProcessData = $this->production->getProcessWiseProduction($id,$prevProcessId,1);
                $row->inward_qty = (!empty($prevProcessData))?$prevProcessData->out_qty:0;
            else:
                $row->inward_qty = $row->in_qty;
            endif;
            $prevProcessId = $row->in_process_id;
            $row->rework_qty = $row->total_rework_qty;
            $row->rejection_qty = $row->total_rejection_qty;
            $row->out_qty = $row->out_qty;

            $completeQty = $row->total_rework_qty + $row->total_rejection_qty + $row->in_qty;

            $row->pending_qty = $row->inward_qty - $completeQty;

            $processPer = ($completeQty > 0)?($completeQty * 100 / $row->inward_qty):"0";

            if($completeQty == 0):
                $row->status = '<span class="badge badge-pill badge-danger m-1">'.round($processPer,2).'%</span>';
            elseif($row->inward_qty > $completeQty):
                $row->status = '<span class="badge badge-pill badge-warning m-1">'.round($processPer,2).'%</span>';
            elseif($row->inward_qty == $completeQty):
                $row->status = '<span class="badge badge-pill badge-success m-1">'.round($processPer,2).'%</span>';
            else:
                $row->status = round($processPer,2);
            endif;            

            $row->process_approvel_data = $row;
            $reworkDataRows[] = $row;
            $totalReworkRejectionQty += $row->total_rework_qty + $row->total_rejection_qty;
            $i++;
        endforeach;

        $jobCardData->tblId = "jobStages";
        $jobProcessPer = (!empty($totalCompleteQty))?($totalCompleteQty * 100 / (($jobCardData->qty * count($process)) - ($totalRejectQty + $totalReworkRejectionQty)) ):"0";
		$jobCardData->jobPer = round($jobProcessPer,2);
        $jobCardData->job_order_status = $jobCardData->order_status;
        if($jobCardData->order_status == 0):
            $jobCardData->order_status = '<span class="badge badge-pill badge-danger m-1">Pending - '.round($jobProcessPer,2).'%</span>';
        elseif($jobCardData->order_status == 1):
            $jobCardData->order_status = '<span class="badge badge-pill badge-primary m-1">Start - '.round($jobProcessPer,2).'%</span>';
        elseif($jobCardData->order_status == 2):
			$jobCardData->tblId = "jobStages2";
            $jobCardData->order_status = '<span class="badge badge-pill badge-warning m-1">In-Process - '.round($jobProcessPer,2).'%</span>';
        elseif($jobCardData->order_status == 3):
            $jobCardData->order_status = '<span class="badge badge-pill badge-info m-1">On-Hold - '.round($jobProcessPer,2).'%</span>';
        elseif($jobCardData->order_status == 4):
			$jobCardData->tblId = "jobStages4";
            $jobCardData->order_status = '<span class="badge badge-pill badge-success m-1">Complete - '.round($jobProcessPer,2).'%</span>';
        else:
			$jobCardData->tblId = "jobStages5";
            $jobCardData->order_status = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
        endif;
        
        $stages['stages'] = $stg;
		$stages['rnStages'] = $runningStages;
        $jobCardData->processData = $dataRows; 
        $jobCardData->reworkData = $reworkDataRows; 
        $this->data['reqMaterial'] = $this->jobcard->getProcessWiseRequiredMaterial($jobCardData);
        $this->data['dataRow'] = $jobCardData;
        $this->data['stageData'] = $stages;
		$this->data['processDataList'] = $this->process->getProcessList();
        $this->data['rawMaterial'] = $this->item->getItemLists("3");
        $this->load->view($this->jobcardDetail,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobcard->delete($id));
        endif;
    }

    public function materialRequest(){
        $id = $this->input->get_post('id');
        $this->data['job_id'] = $id;
        $this->data['jobCardData'] = $this->jobcard->getJobcard($id);        
        $this->data['disptachData'] = $this->jobcard->getRequestItemData($id);    
        $this->data['machineData'] = $this->machine->getMachineList();
        $this->load->view('jobcard/material_request',$this->data);
    }

    public function saveMaterialRequest(){
        $data = $this->input->post();
        $errorMessage = array();
        $i=1;
        if(empty($data['req_date']))
            $errorMessage['req_date'] = "Date is required.";
        if(empty($data['bom_item_id'])):
            $errorMessage['general_error'] = "Items is required.";
        else:
            foreach($data['bom_item_id'] as $key=>$value):
                if(empty($data['request_qty'][$key])):
                    $errorMessage['request_qty'.$i] = "Request Qty is required.";
                endif;
            $i++;
            endforeach;
        endif;
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->jobcard->saveMaterialRequest($data));
        endif;        
    }

    public function changeJobStatus(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobcard->changeJobStatus($data));
        endif;
    }

    public function saveJobBomItem(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['bom_item_id']))
            $errorMessage['bom_item_id'] = "Item Name is required.";
        if(empty($data['bom_qty']))
            $errorMessage['bom_qty'] = "Weight/Pcs is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $postData = [
                'id' => '',
                'job_card_id' => $data['bom_job_card_id'],
                'item_id' => $data['bom_product_id'],
                'ref_item_id' => $data['bom_item_id'],
                'qty' => $data['bom_qty'],
                'process_id' => $data['bom_process_id'],
                'created_by' => $this->loginId
            ];
            $this->printJson($this->jobcard->saveJobBomItem($postData));
        endif;
    }

    public function deleteBomItem(){
        $id = $this->input->post('id');
        $job_card_id = $this->input->post('job_card_id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobcard->deleteBomItem($id,$job_card_id));
        endif;
    }

    public function addJobStage(){
        if(empty($this->input->post('id')) OR empty($this->input->post('process_id'))):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $data = $this->input->post();
            $data['created_by'] = $this->session->userdata('loginId');
			$stageRows = $this->jobcard->addJobStage($data);			
            $this->printJson(['status'=>1,'stageRows'=> $stageRows[0],'pOptions'=> $stageRows[1]]);
        endif;
    }
    
    public function updateJobProcessSequance(){
        if(empty($this->input->post('id')) OR empty($this->input->post('process_id'))):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            //print_r($this->input->post());exit;
			$stageRows = $this->jobcard->updateJobProcessSequance($this->input->post());
            $this->printJson(['status'=>1,'stageRows'=> $stageRows[0],'pOptions'=> $stageRows[1]]);
        endif;
    }

    public function removeJobStage(){
        if(empty($this->input->post('id')) OR empty($this->input->post('process_id'))):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
			$stageRows = $this->jobcard->removeJobStage($this->input->post());
            $this->printJson(['status'=>1,'stageRows'=> $stageRows[0],'pOptions'=> $stageRows[1]]);
        endif;
    }

    public function getStoreLocation(){
        $locationData = $this->store->getStoreLocationList();
        $options = '<option value=""  data-store_name="">Select Location</option>';
        foreach($locationData as $lData):                            
            $options .= '<optgroup label="'.$lData['store_name'].'">';
            foreach($lData['location'] as $row):
                $options .= '<option value="'.$row->id.'" data-store_name="'.$lData['store_name'].'">'.$row->location.' </option>';
            endforeach;
            $options .= '</optgroup>';
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getBatchNoForReturnMaterial(){
        $job_id = $this->input->post('job_id');
        $item_id = $this->input->post('item_id');
        $this->printJson($this->jobcard->getBatchNoForReturnMaterial($job_id,$item_id));
    }
    
    public function saveScrapWeight(){
        $data = $this->input->post();

        if(empty($data['job_id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $queryData['scrap_weight'] = ""; $scrapArray = array();
			if(isset($data['process_id']) && !empty($data['process_id'])):
				foreach($data['process_id'] as $key=>$value):
					$scrapArray[] = [
						'process_id' => $value,
                        'out_w_pcs' => $data['out_w_pcs'][$key]
					];
				endforeach;
				$queryData['scrap_weight'] = json_encode($scrapArray);
			endif;
            $queryData['id'] = $data['job_id'];
            $this->printJson($this->jobcard->saveScrapWeight($queryData));
        endif;
    }

    public function generateScrape(){
        $id = $this->input->post('id');
        
        $jobCardData = $this->jobcard->getJobcard($id);
        $jobCardData->job_order_status = $jobCardData->order_status;

        $process = explode(",","0,".$jobCardData->process);
        $reqMaterial = $this->jobcard->getProcessWiseRequiredMaterial($jobCardData)['resultData'];

        $resultData = array();
        if(!empty($reqMaterial)):
            $i = 0;
            foreach($reqMaterial as $row):
                $resultData[$i]['item_id'] = $row['item_id']; 
                $resultData[$i]['item_name'] = $row['item_name']; 
                $j=0;$previousKg = 0;
                foreach($process as $key=>$value):
                    $jobProcessData = $this->production->getProcessWiseProduction($id,$value);
                    $process_name = (!empty($value))?$this->process->getProcess($value)->process_name:"Initial Stage";    
                    $finishWeight = 0;
                    $processFinishWeight = (!empty($jobCardData->scrap_weight))?json_decode($jobCardData->scrap_weight):array();
                    $finishWeightKey = array_search($value,array_column($processFinishWeight,'process_id'));
                    $finishWeight = (!empty($processFinishWeight[$finishWeightKey]->out_w_pcs))?$processFinishWeight[$finishWeightKey]->out_w_pcs:0;
                    $totalOutQty = (!empty($jobProcessData->out_qty))?$jobProcessData->out_qty:0;
                    $totalFinishWeight = $totalOutQty * $finishWeight;
                    $issueQty = (!empty($previousKg))?$previousKg:$row['issue_qty'];
                    $resultData[$i]['processData'][$j]['process_name'] = $process_name;
                    $resultData[$i]['processData'][$j]['finish_weight'] = $finishWeight;
                    $resultData[$i]['processData'][$j]['out_qty'] = $jobProcessData->out_qty;
                    $resultData[$i]['processData'][$j]['total_finish_weight'] = $totalFinishWeight;
                    $resultData[$i]['processData'][$j]['issue_qty'] = $issueQty;
                    $resultData[$i]['processData'][$j]['scrape_qty'] = $issueQty - $totalFinishWeight;
                    $previousKg = $totalFinishWeight ;
                    $j++;
                endforeach;
                $i++;
            endforeach;            
        endif;
        $this->data['job_card_id'] = $id;
        $this->data['dataRow'] = $resultData;
        $this->load->view($this->jobScrape,$this->data);        
    }

    public function saveScrape(){
        $data = $this->input->post();
        
        $this->jobcard->deleteScrape($data['job_card_id']);
        foreach($data['item_id'] as $key=>$value):
            $postData = [
                'id' => "",
                'location_id' => 2,
                'trans_type' => 1,
                'item_id' => $value,
                'qty' => $data['scrape_qty'][$key],
                'ref_type' => 13,
                'ref_id' => $data['job_card_id'],
                'ref_date' => date("Y-m-d"),
                'created_by' => $this->session->userdata('loginId')
            ];

            $result = $this->jobcard->saveScrape($postData);
        endforeach;

        $this->printJson(['status'=>1,'message'=>"Scrape saved successfully."]);
    }

    public function materialReceived(){
        $data = $this->input->post();
        $data['mr_at'] = date("Y-m-d H:i:s");
        $this->printJson($this->jobcard->materialReceived($data));
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
							<td style="width:50%;" class="text-center">'.$dataRow->insp_by.'<br><b>Inspected By </b></td>
							<td style="width:50%;" class="text-center">'.$dataRow->approve_by.'<br><b>Approved By</b></td>
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
?>