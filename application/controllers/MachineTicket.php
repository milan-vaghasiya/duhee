<?php
class MachineTicket extends MY_Controller {
    private $indexPage = "machine_ticket/index";
    private $ticketForm = "machine_ticket/form";
    private $solutionPage = "machine_ticket/machine_solution";
    private $requestForm = "purchase_request/purchase_request";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Machine Ticket";
		$this->data['headData']->controller = "machineTicket";
		$this->data['headData']->pageUrl = "machineTicket";
	}
	
	public function index(){
        $this->data['tableHeader'] = getMaintenanceDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->ticketModel->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getMachineTicketData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMachineTicket(){
        $this->data['trans_prefix'] = "MT/".$this->shortYear."/";
        $this->data['nextTransNo'] = $this->ticketModel->nextTransNo();
        $this->data['machineData'] = $this->ticketModel->getMachineName();
        $this->data['deptData'] = $this->ticketModel->getDepartment();
        $this->load->view($this->ticketForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_no']))
            $errorMessage['trans_no'] = "Trans. no. is required.";
        if(empty($data['machine_id']))
            $errorMessage['machine_id'] = "Machine is required.";
        // if(empty($data['dept_id']))
        //     $errorMessage['dept_id'] = "Department is required.";
        if(empty($data['problem_date']))
            $errorMessage['problem_date'] = "Problem Date is required.";
        if(empty($data['problem_title']))
            $errorMessage['problem_title'] = "Problem Title is required.";
        if(empty($data['problem_detail']))
            $errorMessage['problem_detail'] = "Problem Detail is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->ticketModel->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['trans_prefix'] = "MT/".$this->shortYear."/";
        $this->data['nextTransNo'] = $this->ticketModel->nextTransNo();
        $this->data['machineData'] = $this->ticketModel->getMachineName();
        $this->data['deptData'] = $this->ticketModel->getDepartment();
        $this->data['dataRow'] = $this->ticketModel->getMachineTicket($id);
        $this->load->view($this->ticketForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->ticketModel->delete($id));
        endif;
    }

    public function getMachineSolution(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->ticketModel->getMachineTicket($id);
        $this->data['partyData'] = $this->party->getPartyList(2,3);
        $this->load->view($this->solutionPage,$this->data);
    }

    public function saveMachineSolution(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['solution_by']) && $data['m_agency']==1)
            $errorMessage['solution_by'] = "Solution By is required.";
        if(empty($data['vendor_id']) && $data['m_agency']==2)
            $errorMessage['solution_by'] = "Solution By is required.";
        if(empty($data['solution_date']))
            $errorMessage['solution_date'] = "Solution Date is required.";
        if(empty($data['solution_detail']))
            $errorMessage['solution_detail'] = "Solution Detail is required.";

            if($data['m_agency'] == 2){
                $data['solution_by'] = $data['vendor_id'];
                
           }
           unset($data['vendor_id']);
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->ticketModel->save($data));
        endif;
    }

    public function addRequisition($mType = '')
    {
        $data=$this->input->post();
        $this->data['dataRow'] = new stdClass();
        $this->data['dataRow']->reqn_type = 2;
        $this->data['dataRow']->req_from = $data['id'];
        $mcTicketData=$this->ticketModel->getMachineTicket($data['id']);
        $this->data['dataRow']->machine_id = $mcTicketData->machine_id;
        $this->data['itemData'] = $this->item->getItemLists(str_replace('~', ',', $mType));
        $this->data['fgNMcData'] = $this->item->getItemLists('1,5');
        $this->data['empData'] = $this->employee->getEmpList();
        $this->data['partyData'] = $this->party->getVendorList();
        $this->data['loginId'] = $this->session->userdata('loginId');
        $this->data['itemTypeList'] = $this->itemCategory->mainCategoryList();
        $this->data['categoryList'] = $this->itemCategory->getCategoryList();
        $this->data['familyGroup'] = $this->item->getfamilyGroupList();
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['reqNo'] = $this->purchaseRequest->nextRequisitionNo();
        $this->data['planningType'] = array(); 
        $this->load->view($this->requestForm, $this->data);
    }

    public function saveRequisition()
    {
        $data = $this->input->post();
        $errorMessage = array();

        if (empty($data['req_item_id']))
            $errorMessage['req_item_id'] = "Item Name is required.";
        if (empty($data['req_date']))
            $errorMessage['req_date'] = "Request Date is required.";
        if (empty($data['req_qty']))
            $errorMessage['req_qty'] = "Request Qty. is required.";
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            if ($data['approve_type'] == 1) {
                $data['approved_by'] = $this->loginId;
                $data['approved_at'] = date("Y-m-d H:i:s");
                $data['order_status'] = 4; 
            } else {
                $data['created_by']  = $this->session->userdata('loginId');
            }
            unset($data['approve_type'], $data['is_returnable1']);
            $data['log_type'] = 1;
            $this->printJson($this->purchaseRequest->savePurchaseRequest($data));
        endif;
    }
}
