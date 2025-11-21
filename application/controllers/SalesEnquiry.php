<?php
class SalesEnquiry extends MY_Controller{
    private $indexPage = 'sales_enquiry/index';
    private $enquiryForm = "sales_enquiry/form";    
    private $quotationForm = "sales_enquiry/sales_quotation";
    private $confirmQuotation = "sales_enquiry/confirm_quotation";
	private $automotiveArray = ["2"=>"No","1"=>'Yes'];
    private $feasible_form = "sales_enquiry/feasible_form";  
	private $appointmentMode = array(0 => '--', 1 => "Phone", 2 => "Email", 3 => "Visit", 4 => "Other");
    
    public function __construct(){
        parent::__construct();
        $this->isLoggedin();
		$this->data['headData']->pageTitle = "Sales Enquiries";
		$this->data['headData']->controller = "salesEnquiry";
		$this->data['headData']->pageUrl = "salesEnquiry";
    }

    public function index($status=0){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller); 
        $this->data['status'] = $status; 
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->salesEnquiry->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;            
            $transCount = $this->salesEnquiry->getFisiblityCount($row->trans_main_id);
            $row->fisibleCount = $transCount->fisibal;
            $row->quotedCount = $transCount->quoted;
            $row->notfisibalCount = $transCount->notfisibal;
            $row->notFisibalTab = $data['status'];
            if($row->trans_status == 0):
				if($status == 2){$row->status = '<span class="badge badge-pill badge-warning m-1">Regreted</span>';}
				else{$row->status = '<span class="badge badge-pill badge-danger m-1">Pending</span>';}
			elseif($row->trans_status == 1):
				if($status == 2){$row->status = '<span class="badge badge-pill badge-warning m-1">Regreted</span>';}
				else{$row->status = '<span class="badge badge-pill badge-success m-1">Confirmed</span>';}
			elseif($row->trans_status == 2):
				$row->status = '<span class="badge badge-pill badge-info m-1">Closed</span>';
			else:
			    $row->status = '<span class="badge badge-pill badge-warning m-1">Regreted</span>';
			endif;
            // if($row->)
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getSalesEnquiryData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addEnquiry($lead_id=""){
        $this->data['from_entry_type'] = 0;
        $this->data['trans_prefix'] = $this->transModel->getTransPrefix(1);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(1);
        $this->data['lead_id'] = $lead_id;     
        $this->data['unitData'] = $this->item->itemUnits();     
		$this->data['automotiveArray'] = $this->automotiveArray;
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['customerData'] = $this->party->getCustomerList(0);
        $this->data['itemRemark'] = $this->feasibilityReason->getFeasibilityReasonList();
        $this->data['salesExecutives'] = $this->employee->getsalesExecutives();
        $this->load->view($this->enquiryForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['party_name']))
            $errorMessage['party_id'] = "Customer Name is required.";
        if(empty($data['trans_no']))
            $errorMessage['trans_no'] = 'Enquiry No. is required.';
        if(empty($data['item_name'][0]))
            $errorMessage['item_name_error'] = 'Item Name is required.';
        if(empty($data['unit_id'][0]))
            $errorMessage['unit_id'] = 'Unit is required.';

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:	
            
            $masterData = [ 
                'id' => $data['enq_id'],
                'entry_type' => $data['form_entry_type'],
                'trans_prefix' => $data['trans_prefix'],
                'trans_no'=>$data['trans_no'], 
                'trans_date' => date('Y-m-d',strtotime($data['trans_date'])), 
                'party_id' => $data['party_id'],
                'party_name' => $data['party_name'],
				'contact_person'=>$data['contact_person'],
				'contact_no'=>$data['contact_no'],
				'contact_email'=>$data['contact_email'],
				'party_phone'=>$data['party_phone'],
				'party_email'=>$data['party_email'],
				'party_address'=>$data['party_address'],
				'party_pincode'=>$data['party_pincode'],
				//'sales_executive'=>$data['sales_executive'],
                'ref_by' => $data['ref_by'],
                'remark' => $data['remark'],
                'created_by' => $this->session->userdata('loginId')
            ];
                            
            $itemData = [
                'id' => $data['trans_id'],
                'from_entry_type' => $data['from_entry_type'],
                'item_id' => $data['item_id'],
                'item_type' => $data['item_type'],
                'item_code' => $data['item_code'],
                'item_name' => $data['item_name'],
                'item_desc' => $data['item_desc'],
                'hsn_code' => $data['hsn_code'],
                'price' => $data['price'],
                'gst_per' => $data['gst_per'],
                'qty' => $data['qty'],
                'unit_id' => $data['unit_id'],
                'unit_name' => $data['unit_name'],
                'automotive' => $data['automotive'],
                'drg_rev_no' => $data['drg_rev_no'],
                'rev_no' => $data['rev_no'],
                'batch_no' => $data['batch_no'],
                //'grn_data' => $data['grn_data'],
                'created_by' => $this->session->userdata('loginId')
            ];
            $this->printJson($this->salesEnquiry->save($masterData,$itemData));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $dataRow = $this->salesEnquiry->getSalesEnquiry($id);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['automotiveArray'] = $this->automotiveArray;
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['customerData'] = $this->party->getCustomerList(0);
        $this->data['itemRemark'] = $this->feasibilityReason->getFeasibilityReasonList();
        $this->data['salesExecutives'] = $this->employee->getsalesExecutives(); 
        $result = $this->party->getContactDetailForEdit($dataRow);
        $dataRow->contact_person = $result->person;
        $dataRow->contact_no = $result->mobile;
        $dataRow->contact_email = $result->email;
        $this->data['dataRow'] = $dataRow;
        $this->load->view($this->enquiryForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->salesEnquiry->deleteEnquiry($id));
		endif;
    }

	public function itemSearch(){
		$this->printJson($this->salesEnquiry->itemSearch());
	}

    public function closeEnquiry(){
        $id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->salesEnquiry->closeEnquiry($id));
		endif;
    }

    public function reopenEnquiry(){
        $id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->salesEnquiry->reopenEnquiry($id));
		endif;
    }
    
    public function getFeasibleData(){
        $data = $this->input->post(); 
        $transData = $this->salesEnquiry->getFeasibleData($data); 

        $tbody = ''; $i=1; 
        if(!empty($transData)):
            foreach($transData as $row):
                $btn = ($data['status'] == 2)?'<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary getRegretedData" data-trans_main_id="'.$row->trans_main_id.'" data-id="'.$row->id.'" ><i class="ti-reload"></i></a>':'';
                $tbody .= '<tr class="text-center">
                    <td>'.$i++.'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->qty.'</td>
                    <td>'.$row->unit_name.'</td>
                    <td>'.$row->drg_rev_no.'</td>
                    <td>'.$row->rev_no.'</td>
                    <td>'.$row->batch_no.'</td>
                    <td>'.$row->feasible.'</td>
                    <td>'.$row->feasibleReason.'</td>
                    <td>'.$btn.'</td>
                </tr>';
            endforeach;
        else:
            $tbody .= '<tr>
                <td class="text-center" colspan="6">No Data Found</td>
            </tr>';
        endif;

        $this->printJson(['status'=>1,'tbody'=>$tbody]);
    }

    public function getRegretedData(){
        $data = $this->input->post();  
        $this->printJson($this->salesEnquiry->getRegretedData($data)); 
    }

    //-----------------------------------------------------------------------------------
	
    /* public function getEnquiryData(){
        $enq_id = $this->input->post('enq_id');
        $this->data['dataRow'] = $this->salesEnquiry->getSalesEnquiryById($enq_id);
        $this->data['enquiryItems'] = $this->salesEnquiry->getEnquiryData($enq_id);
        $this->data['quote_prefix'] = 'SQ/21-22/';
        $this->data['quote_no'] = $this->salesEnquiry->nextQuoteNo();
        $this->load->view($this->quotationForm,$this->data);
    } */

    /* public function saveQuotation(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['item_name'][0])):
            $errorMessage['item_name_error'] = "Please select Items.";
        else:
            foreach($data['qty'] as $key=>$value):
                if(empty($value)):
                    $errorMessage['qty'.$data['trans_id'][$key]] = "Qty is required.";
                endif;
            endforeach;

            foreach($data['price'] as $key=>$value):
                if(empty($value)):
                    $errorMessage['price'.$data['trans_id'][$key]] = "Price is required.";
                endif;
            endforeach;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->salesEnquiry->saveQuotation($data));
        endif;
    }  */   

    /* public function getQuotationItems(){
        $quote_id = $this->input->post('quote_id');
        $this->data['quotationItems'] = $this->salesEnquiry->getQuotationItems($quote_id);
        $this->load->view($this->confirmQuotation,$this->data);
    } */

    /* public function saveConfirmQuotation(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_id'][0])):
            $errorMessage['item_name_error'] = "Please select Items.";
        else:
            foreach($data['confirm_price'] as $key=>$value):
                if(empty($value)):
                    $errorMessage['confirm_price'.$data['trans_id'][$key]] = "Confirm Price is required.";
                endif;
            endforeach;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['confirm_by'] = $this->session->userdata('loginId');
            $this->printJson($this->salesEnquiry->saveConfirmQuotation($data));
        endif;
    } */
    
    public function changeParty(){
        $data = $this->input->post();  
        $this->printJson($this->salesEnquiry->changeParty($data)); 
    }
    
    public function feasibleForm(){
        $id = $this->input->post('id');
        $this->data['id'] = $id;
        $this->data['itemRemark'] = $this->feasibilityReason->getFeasibilityReasonList();
        $this->load->view($this->feasible_form,$this->data);
    }

    public function saveFeasibleForm(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['item_remark']))
            $errorMessage['item_remark'] = "Reason is required.";
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->salesEnquiry->saveFeasibleForm($data));
        endif;
    }

    /* Appointments & Followups */
    public function getAppointments()
	{
		$data = $this->input->post();
		$this->data['ref_id'] = $data['id'];
		$data['entry_type'] = 2;
		$this->data['appointmentMode'] = $this->appointmentMode;
        $this->data['followupData'] = $followupData = $this->salesEnquiry->getEnquiry($data['id']);
		$this->load->view('sales_enquiry/appointment_form', $this->data);
	}

	public function setAppointment()
	{
		$data = $this->input->post();
		if (empty($data['contact_person']))
			$errorMessage['contact_person'] = "Contact Person is required.";
		if (empty($data['mode']))
			$errorMessage['mode'] = "Mode is required.";

		if (!empty($errorMessage)):
			$this->printJson(['status' => 0, 'message' => $errorMessage]);
		else:
			$data['contact_person'] = ucwords($data['contact_person']);
			$data['appointment_date'] = formatDate($data['appointment_date'], 'Y-m-d');
			$data['appointment_time'] = formatDate($data['appointment_time'], 'H:i:s');
			$data['created_at'] = date("Y-m-d H:i:s");
			$data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->salesEnquiry->setAppointment($data));
		endif;
	}

    public function getFollowup()
	{
		$data = $this->input->post();
		$this->data['ref_id'] = $data['id'];
		$this->data['entry_type'] = 1;		
		$this->data['appointmentMode'] = $this->appointmentMode;
        $this->data['followupData'] = $followupData = $this->salesEnquiry->getEnquiry($data['id']);
		$this->load->view('sales_enquiry/followup_form', $this->data);
	}

    public function saveFollowup()
	{
		$data = $this->input->post();
		if (empty($data['contact_person']))
			$errorMessage['contact_person'] = "Contact Person is required.";
		if (empty($data['mode']))
			$errorMessage['mode'] = "Mode is required.";

		if (!empty($errorMessage)):
			$this->printJson(['status' => 0, 'message' => $errorMessage]);
		else:
			$data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->salesEnquiry->saveFollowup($data));
		endif;
	}

    public function deleteAppointment()
	{
		$data = $this->input->post();
		if (empty($data['id'])):
			$this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
		else:
            $this->printJson($this->salesEnquiry->deleteAppointment($data['id']));
		endif;
	}

    public function closeAppointment()
	{
		$data = $this->input->post();
		$this->data['appointmentMode'] = $this->appointmentMode;
		$this->data['appointmentData'] = $this->salesEnquiry->getAppointmentDetail($data['id']);
		$this->load->view('sales_enquiry/appointment_status', $this->data);
	}

	public function saveAppointmentStatus()
	{
		$data = $this->input->post();
		$errorMessage = array();
		if (empty($data['notes']))
			$errorMessage['notes'] = "Reason is required.";

		if (!empty($errorMessage)):
			$this->printJson(['status' => 0, 'message' => $errorMessage]);
		else:
			$this->printJson($this->salesEnquiry->setAppointment($data));
		endif;
	}

    public function followup()
    {
        $this->data['headData']->pageUrl = "salesEnquiry";
        $this->data['tableHeader'] = getSalesDtHeader("seFollowup");
        $this->load->view('sales_enquiry/followup_index', $this->data);
    }

    public function getSEFollowupDTRows()
    {
        $data=$this->input->post();
        $result = $this->salesEnquiry->getSEFollowupDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            if($row->mode == 1){$row->mode = "Phone";}
            elseif($row->mode == 2){$row->mode = "Email";}
            elseif($row->mode == 3){$row->mode = "Visit";}
            elseif($row->mode == 3){$row->mode = "Other";}
            else{$row->mode = "--";}
            $sendData[] = getSEFollowupData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function appointment()
    {
        $this->data['headData']->pageUrl = "salesEnquiry";
        $this->data['tableHeader'] = getSalesDtHeader("seAppointment");
        $this->load->view('sales_enquiry/appointment_index', $this->data);
    }

    public function getSEAppointmentDTRows()
    {
        $data=$this->input->post();
        $result = $this->salesEnquiry->getSEAppointmentDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            if($row->mode == 1){$row->mode = "Phone";}
            elseif($row->mode == 2){$row->mode = "Email";}
            elseif($row->mode == 3){$row->mode = "Visit";}
            elseif($row->mode == 3){$row->mode = "Other";}
            else{$row->mode = "--";}

            if($row->status == 0):
				{$row->status_label = '<span class="badge badge-pill badge-info m-1">Open</span>';}
			elseif($row->status == 1):
				{$row->status_label = '<span class="badge badge-pill badge-warning m-1">Cancel</span>';}
			else:
				$row->status_label = '<span class="badge badge-pill badge-success m-1">Completed</span>';
			endif;

            $sendData[] = getSEAppointmentData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
}
?>