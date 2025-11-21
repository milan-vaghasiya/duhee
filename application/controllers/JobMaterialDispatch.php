<?php 
class JobMaterialDispatch extends MY_Controller{
    private $indexPage = "job_material_dispatch/index";
    private $job_material_issue = "job_material_dispatch/job_material_issue";
    private $requestForm = "job_material_dispatch/purchase_request";
    private $toolConsumption = "job_material_dispatch/tool_consumption";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Jobcard Material Dispatch";
		$this->data['headData']->controller = "jobMaterialDispatch";
		$this->data['headData']->pageUrl = "jobMaterialDispatch";
	}
	
	public function index($status = 0){
        $header = ($status == 2)?"allocatedMaterial" : "jobMaterialDispatch";
        $this->data['status'] = $status;
        $this->data['tableHeader'] = getStoreDtHeader($header);
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->jobMaterial->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
            $row->issue_qty = 0;
            $row->issue_date = "";
            if($status == 2):
                $row->req_qty = 0;
                $row->issue_qty = 0;
            endif;
            if($status != 2):
                $row->issue_qty = $this->issueRequisition->getIssueMaterialData($row->id)->req_qty;  
                $issueData = $this->issueRequisition->getMaxIssueDate($row->id);
                $row->issue_date = $issueData->req_date;
            endif;
            $row->tab_status = $status;
            $sendData[] = getJobMaterialIssueData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    public function dispatch(){
        $id = $this->input->post('id');
        $dispatchData = $this->purchaseRequest->getPurchaseRequest($id);

        $locationName = array();
        if(!empty($dispatchData->location_id)):
            $locationId = explode(',',$dispatchData->location_id);            
            foreach($locationId as $lid):
                $locationData = $this->store->getStoreLocation($lid);
                $locationName[] = "[ ".$locationData->store_name." ] ".$locationData->location;
            endforeach;
        endif;
        $dispatchData->location_name = implode(",",$locationName);
        $dispatchData->ref_id = $id;
        $dispatchData->id = $id;

        if ($dispatchData->used_at == 0) {
            $dispatchData->whom_to_handover = (!empty($dispatchData->handover_to) ? $this->item->getItem($dispatchData->handover_to)->item_code : '');
        } else {
            $dispatchData->whom_to_handover = $this->party->getParty($dispatchData->handover_to)->party_name;
        }

        $this->data['dataRow'] = $dispatchData;

        $issueData = $this->issueRequisition->getIssueMaterialData($id);
        $this->data['issueData'] = $issueData;        

        $this->data['batchWiseStock'] = $this->store->getItemStockBatchWise(['item_id' => $dispatchData->req_item_id, 'trans_id' => $id, "req_type" => '20', 'stock_effect' => 0, 'ref_id' => $dispatchData->req_from,'stock_required'=>1]);
        $this->data['machineList'] = $this->machine->getMachineList();
        $this->load->view($this->job_material_issue,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $data['created_by'] = $this->loginId;
        $data['approved_by'] = $this->loginId;
        $this->printJson($this->jobMaterial->save($data));
    }

    public function editAllocatedMaterial(){
        $id = $this->input->post('id');
        $this->data['allocatedMaterial'] = $this->jobMaterial->editAllocatedMaterial($id);
        $this->load->view("job_material_dispatch/edit_allocated_material",$this->data);
    }

    public function updateAllocatedQty(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['id']))
            $errorMessage['general_error'] = "Somthing is Wrong.";
        if(empty($data['ref_type']))
            $errorMessage['trans_type'] = "Please select opration.";
        if(empty($data['qty']))
            $errorMessage['qty'] = "Qty is required.";

        if(!empty($data['ref_type'])):
            if($data['ref_type'] == 1):
                $stockData = $this->store->getItemStockBatchWise(['item_id'=>$data['item_id'],'location_id'=>$data['location_id'],'batch_no'=>$data['ref_batch'],'stock_required'=>1,'single_row'=>1]);
                
                if($data['qty'] > $stockData->qty):
                    $errorMessage['qty'] = "Stock not avalible.";
                endif;
            endif;
            if($data['ref_type'] == -1):
                $allocatedMaterial = $this->jobMaterial->editAllocatedMaterial($data['id']);
                if($data['qty'] > $allocatedMaterial->pending_qty):
                    $errorMessage['qty'] = "Invalid Qty.";
                endif;
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->jobMaterial->updateAllocatedQty($data));
        endif;
    }

    public function save1()
    {
        $data = $this->input->post(); //print_r($data);exit;
        if (empty($data['handover_to']))
             $errorMessage['handover_to'] = "Whom to Handover is required.";
        
        $newQty = array_sum($data['batch_quantity']);
        if ($newQty<=0)
             $errorMessage['batch_qty'] = "Qty is required.";
        if ($newQty > $data['pending_issue'])
             $errorMessage['batch_qty'] = "Invalid Qty";
             
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            // $data['dispatch_by'] = $this->session->userdata('loginId');
            $data['created_by'] = $this->session->userdata('loginId');
            if($data['issue_type']==2){
                $data['approved_by'] = $this->session->userdata('loginId');
            }
            $data['location_id'] = implode(",", $data['location']);
            $data['batch_no'] = implode(",", $data['batch_number']);
            $data['batch_qty'] = implode(",", $data['batch_quantity']);
            $data['stock_type'] = implode(",", $data['stock_type']);
            unset($data['location'], $data['batch_number'], $data['batch_quantity'],$data['pending_issue'],$data['issue_type'],$data['req_emp_id']);

            $this->printJson($this->jobMaterial->save($data));
        endif;
    }
}
?>