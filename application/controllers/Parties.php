<?php
class Parties extends MY_Controller
{
    private $indexPage = "party/index";
    private $partyForm = "party/form";
    private $automotiveArray = ["1" => 'Yes', "2" => "No"];
    private $gstForm = "party/gst_form";
    private $contactForm = "party/contact_form";
    private $lead_index = "party/lead_index";
    private $lead_form = "party/lead_form";
	private $appointmentMode = array(0 => '--', 1 => "Phone", 2 => "Email", 3 => "Visit", 4 => "Other");

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Parties";
        $this->data['headData']->controller = "parties";
        $this->data['suppliedTypes'] = array('Goods', 'Services', 'Goods,Services');
        $this->data['vendorTypes'] = array('Manufacture', 'Service');
    }

    public function index()
    {
        $this->data['headData']->pageUrl = "parties";
        $this->data['party_category'] = 1;
        $this->data['tableHeader'] = getSalesDtHeader("customer");
        $this->load->view($this->indexPage, $this->data);
    }

    public function vendor()
    {
        $this->data['headData']->pageUrl = "parties/vendor";
        $this->data['party_category'] = 2;
        $this->data['processData'] = $this->process->getProcessList();
        $this->data['tableHeader'] = getProductionHeader("vendor");
        $this->load->view($this->indexPage, $this->data);
    }

    public function supplier()
    {
        $this->data['headData']->pageUrl = "parties/supplier";
        $this->data['party_category'] = 3;
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage, $this->data);
    }

    public function lead()
    {
        $this->data['headData']->pageUrl = "parties/lead";
        $this->data['party_category'] = 1;
        $this->data['tableHeader'] = getSalesDtHeader("customer");
        $this->load->view($this->lead_index, $this->data);
    }

    public function addLead()
    {
        $this->data['party_code'] = '';
        $this->load->view($this->lead_form, $this->data);
    }

    public function getLeadDTRows($party_type=0)
    {
        $data=$this->input->post();$data['party_type'] = $party_type;
        $result = $this->party->getLeadDTRows($data,$party_type);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $sendData[] = getPartyData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function getDTRows($party_category,$party_type=0)
    {
        $data=$this->input->post();$data['party_type'] = $party_type;
        $result = $this->party->getDTRows($data, $party_category);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $sendData[] = getPartyData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addParty($party_category)
    {
        $this->data['party_category'] = $party_category;
        $this->data['party_code'] = '';
	    if(in_array($party_category,[2,3])){$this->data['party_code'] = $this->party->generatePartyCode($party_category);}
        $this->data['currencyData'] = $this->party->getCurrency();
        $this->data['countryData'] = $this->party->getCountries();
        $this->data['salesExecutives'] = $this->employee->getsalesExecutives();
        $this->data['processDataList'] = $this->process->getProcessList();
        $this->data['automotiveArray'] = $this->automotiveArray;
        $this->load->view($this->partyForm, $this->data);
    }

    public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['party_name']))
            $errorMessage['party_name'] = "Company name is required.";
        if (empty($data['party_category']))
            $errorMessage['party_category'] = "Party Category is required.";
        if (empty($data['contact_person']))
            $errorMessage['contact_person'] = "Contact Person is required.";
        if (empty($data['party_mobile']))
            $errorMessage['party_mobile'] = "Contact No. is required.";
        if (empty($data['country_id'])  && $data['party_type'] != 2)
            $errorMessage['country_id'] = 'Country is required.';
        if (empty($data['supplied_types']))
            $errorMessage['supplied_types'] = 'Supplied Types are required.';
        if ($data['country_id'] == 101) {
            if (empty($data['gstin']) && $data['party_type'] != 2)
                $errorMessage['gstin'] = 'Gstin is required.';
        }
        if (empty($data['state_id'])  && $data['party_type'] != 2)  {
            if (empty($data['statename']))
                $errorMessage['state_id'] = 'State is required.';
            else
                $data['state_id'] = $this->party->saveState($data['statename'], $data['country_id']);
        }
        if ($data['party_category'] == 2) {
            if (empty($data['process_id']))
                $errorMessage['processSelect'] = 'Production Process is required.';
        } elseif($data['party_category'] == 1) {
            $data['party_code'] = $data['party_code'];
        }
        unset($data['statename'], $data['processSelect'],$data['pcode']);
        if (empty($data['city_id'])  && $data['party_type'] != 2) {
            if (empty($data['ctname']))
                $errorMessage['city_id'] = 'City is required.';
            else
                $data['city_id'] = $this->party->saveCity($data['ctname'], $data['state_id'], $data['country_id']);
        }
        unset($data['ctname']);
        if (!empty($data['opening_balance']) && empty($data['balance_type']))
            $errorMessage['opening_balance'] = "Please select Type.";
        if (empty($data['party_address'])  && $data['party_type'] != 2)
            $errorMessage['party_address'] = "Address is required.";
        if (empty($data['party_pincode'])  && $data['party_type'] != 2)
            $errorMessage['party_pincode'] = "Address Pincode is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['party_name'] = ucwords($data['party_name']);
            $this->printJson($this->party->save($data));
        endif;
    }

    public function edit()
    {
        $id = $this->input->post('id');
        $result = $this->party->getParty($id);
        $result->state = $this->party->getStates($result->country_id, $result->state_id)['result'];
        $result->city = $this->party->getCities($result->state_id, $result->city_id)['result'];
        $this->data['dataRow'] = $result;
        $this->data['processDataList'] = $this->process->getProcessList();
        $this->data['currencyData'] = $this->party->getCurrency();
        $this->data['countryData'] = $this->party->getCountries();
        $this->data['salesExecutives'] = $this->employee->getsalesExecutives();
        $this->data['automotiveArray'] = $this->automotiveArray;
        $this->load->view($this->partyForm, $this->data);
    }

    public function partyDetails()
    {
        $id = $this->input->post('id');
        $result = $this->party->getParty($id);
        $this->printJson($result);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->party->delete($id));
        endif;
    }

    public function getStates()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Something went wrong...Please try again.']);
        else :
            $this->printJson($this->party->getStates($id));
        endif;
    }

    public function getCities()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Something went wrong...Please try again.']);
        else :
            $this->printJson($this->party->getCities($id));
        endif;
    }

    public function partyApproval()
    {
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->party->getParty($id);
        $this->load->view("party/approval_form", $this->data);
    }

    public function savePartyApproval()
    {
        $data = $this->input->post();

        $errorMessage = array();
        if (empty($data['approved_date']))
            $errorMessage['approved_date'] = "Approved Date is required.";
        if (empty($data['approved_by']))
            $errorMessage['approved_by'] = "Approved By is required.";
        if (empty($data['approved_base']))
            $errorMessage['approved_base'] = "Approved Base is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['approved_date'] = (!empty($data['approved_date'])) ? date('Y-m-d', strtotime($data['approved_date'])) : null;
            $this->printJson($this->party->savePartyApproval($data));
        endif;
    }


    /**
     * Created BY Mansee @ 25-12-2021
     */
    public function getGstDetail()
    {
        $party_id = $this->input->post('id');
        $result = $this->party->getParty($party_id);
        $this->data['json_data'] = json_decode($result->json_data);
        $this->data['party_id'] = $party_id;
        $this->load->view($this->gstForm, $this->data);
    }
    
    /**
     * Created BY Mansee @ 25-12-2021
     */
    public function saveGst()
    {
        $data = $this->input->post();

        $errorMessage = array();
        if (empty($data['gstin']))
            $errorMessage['gstin'] = "GST is required.";
        if (empty($data['delivery_address']))
            $errorMessage['delivery_address'] = "Address is required.";
        if (empty($data['delivery_pincode']))
            $errorMessage['delivery_pincode'] = "Pincode is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :

            $response = $this->party->saveGst($data);

            $result = $this->party->getParty($data['party_id']);
            $json_data = json_decode($result->json_data);
            $i = 1;
            $tbodyData = "";
            if (!empty($json_data)) :
                foreach ($json_data as $key => $row) :
                    $tbodyData .= '<tr>
                                <td>' .  $i++ . '</td>
                                <td>' . $key . '</td>
                                <td>' . $row->party_address . '</td>
                                <td>' . $row->party_pincode . '</td>
                                <td>' . $row->delivery_address . '</td>
                                <td>' . $row->delivery_pincode . '</td>
                                <td class="text-center">
                                <a href="javascript:void(0);" class="btn btn-outline-danger btn-delete" onclick="trashGst(\'' . $key . '\')"><i class="ti-trash"></i></a>
                                </td>
                            </tr> ';
                endforeach;
            else :
                $tbodyData .= '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status' => 1, "tbodyData" => $tbodyData, "partyId" => $data['party_id']]);
        endif;
    }
    
    /**
     * Created BY Mansee @ 25-12-2021
     */
    public function deleteGst()
    {
        $party = $this->input->post();
        if (empty($party['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->party->deleteGst($party['id'], $party['gstin']);

            $result = $this->party->getParty($party['id']);
            $json_data = json_decode($result->json_data);
            $i = 1;
            $tbodyData = "";
            if (!empty($json_data)) :
                foreach ($json_data as $key => $row) :
                    $tbodyData .= '<tr>
                                <td>' .  $i++ . '</td>
                                <td>' . $key . '</td>
                                <td>' . $row->party_address . '</td>
                                <td>' . $row->party_pincode . '</td>
                                <td>' . $row->delivery_address . '</td>
                                <td>' . $row->delivery_pincode . '</td>
                                <td class="text-center">
                                <a href="javascript:void(0);" class="btn btn-outline-danger btn-delete" onclick="trashGst(\'' . $key . '\');"><i class="ti-trash"></i></a>
                                </td>
                            </tr> ';
                endforeach;
            else :
                $tbodyData .= '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status' => 1, "tbodyData" => $tbodyData, "partyId" => $party['id']]);
        endif;
    }
    
/* Updated By :- Sweta @12-09-2023 */
    public function getContactDetail(){
        $party_id = $this->input->post('id');
        $this->data['party_id'] = $party_id;
        $this->data['custWiseTbody'] = $this->custWiseContactData(['party_id'=>$party_id]);
        $this->load->view($this->contactForm,$this->data);
    }

    /* Updated By :- Sweta @12-09-2023 */
    public function saveContact(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['person']))
			$errorMessage['person'] = "Contact Person is required.";
        if(empty($data['mobile']))
			$errorMessage['mobile'] = "Contact Mobile is required.";
        if(empty($data['email']))
			$errorMessage['email'] = "Contact Email is required.";
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->loginId;
            $this->party->saveContact($data);
            $this->printJson($this->custWiseContactData(['party_id'=>$data['party_id']]));
		endif;
    }

    /* Updated By :- Sweta @12-09-2023 */
    public function deleteContact(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->party->deleteContact($data['id']);            
            $this->printJson($this->custWiseContactData(['party_id'=>$data['party_id']]));
        endif;
    }

    /* Created By :- Sweta @12-09-2023 */
    public function custWiseContactData($data){
        $result = $this->party->custWiseContactData($data);
        $i=1; $tbodyData="";
        if(!empty($result)) :
            foreach ($result as $row) :
                $tbodyData.= '<tr>
                        <td>'.$i.'</td>
                        <td>'.$row->person.'</td>
                        <td>'.$row->mobile.'</td>
                        <td>'.$row->email.'</td>
                        <td class="text-center">
                            <a href="javascript:void(0);" class="btn btn-outline-danger btn-delete" onclick="trashContact('.$row->id.','.$row->party_id.');"><i class="ti-trash"></i></button>
                        </td>
                    </tr>';
                    $i++;
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
        endif;
        return ['status'=>1, "tbodyData"=>$tbodyData];
    }

    public function getPartyContactDetail(){
        $party_id = $this->input->post('party_id');
        $result = $this->party->getParty($party_id);
        $data['contact_detail'] = ""; $conArr = Array();
        $conArr[0] =  [
            'person' => $result->contact_person,
            'mobile' => $result->party_mobile,
            'email' => $result->contact_email
        ];

        if(!empty($result->contact_detail)):
            $cData = json_decode($result->contact_detail); $i=1;
            foreach($cData as $row):
                $conArr[$i] =  [
                    'person' => $row->person,
                    'mobile' => $row->mobile,
                    'email' => $row->email
                ]; $i++;
            endforeach;
        endif;

        $person = ""; $mobile=""; $email="";
        foreach($conArr as $row):
            if(!empty($row['person'])){ $person.='<option value="'.$row['person'].'">'.$row['person'].'</option>'; }
            if(!empty($row['mobile'])){ $mobile.='<option value="'.$row['mobile'].'">'.$row['mobile'].'</option>'; }
            if(!empty($row['email'])){ $email.='<option value="'.$row['email'].'">'.$row['email'].'</option>'; }
        endforeach;
        
        if(empty($person)){ $person = '<option value="">Select Contact person </option>'; }
        if(empty($mobile)){ $mobile = '<option value="">Select Contact mobile </option>'; }
        if(empty($email)){ $email = '<option value="">Select Contact Email </option>'; }
        
        $this->printJson(['status' => 1, 'person' => $person, 'mobile' => $mobile, 'email' => $email]);
    }

    /* Appointments & Followups */ 
    public function getAppointments()
	{
		$data = $this->input->post();
		$this->data['ref_id'] = $data['id'];
		$data['entry_type'] = 2;
		$this->data['appointmentMode'] = $this->appointmentMode;
        $this->data['followupData'] = $followupData = $this->party->getlead($data['id']);
		$this->load->view('party/appointment_form', $this->data);
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
            $this->printJson($this->party->setAppointment($data));
		endif;
	}

    public function getFollowup()
	{
		$data = $this->input->post();
		$this->data['ref_id'] = $data['id'];
		$this->data['entry_type'] = 1;		
		$this->data['appointmentMode'] = $this->appointmentMode;
        $this->data['followupData'] = $followupData = $this->party->getlead($data['id']);
		$this->load->view('party/followup_form', $this->data);
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
            $this->printJson($this->party->saveFollowup($data));
        endif;
    }

    public function deleteAppointment()
	{
		$data = $this->input->post();
		if (empty($data['id'])):
			$this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
		else:
            $this->printJson($this->party->deleteAppointment($data['id']));
		endif;
	}

    public function closeAppointment()
	{
		$data = $this->input->post();
		$this->data['appointmentMode'] = $this->appointmentMode;
		$this->data['appointmentData'] = $this->party->getAppointmentDetail($data['id']);
		$this->load->view('sales_quotation/appointment_status', $this->data);
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
			$this->printJson($this->party->setAppointment($data));
		endif;
	}

    public function followup()
    {
        $this->data['headData']->pageUrl = "parties/lead";
        $this->data['tableHeader'] = getSalesDtHeader("leadFollowup");
        $this->load->view('party/followup_index', $this->data);
    }

    public function getLeadFollowupDTRows()
    {
        $data=$this->input->post();
        $result = $this->party->getLeadFollowupDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            if($row->mode == 1){$row->mode = "Phone";}
            elseif($row->mode == 2){$row->mode = "Email";}
            elseif($row->mode == 3){$row->mode = "Visit";}
            elseif($row->mode == 3){$row->mode = "Other";}
            else{$row->mode = "--";}   
            $sendData[] = getLeadFollowupData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function appointment()
    {
        $this->data['headData']->pageUrl = "parties/lead";
        $this->data['party_category'] = 1;
        $this->data['tableHeader'] = getSalesDtHeader("leadAppointment");
        $this->load->view('party/appointment_index', $this->data);
    }

    public function getLeadAppointmentDTRows()
    {
        $data=$this->input->post();
        $result = $this->party->getLeadAppointmentDTRows($data);
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
            
            $sendData[] = getLeadAppointmentData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function saveLead(){
        $data = $this->input->post();
        $errorMessage = array();

        if (empty($data['party_name']))
            $errorMessage['party_name'] = "Company name is required.";
        if (empty($data['party_address']))
            $errorMessage['party_address'] = "Party Address is required.";
        if (empty($data['contact_person']))
            $errorMessage['contact_person'] = "Contact Person is required.";
        if (empty($data['party_pincode']))
            $errorMessage['party_pincode'] = "Pincode is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['party_name'] = ucwords($data['party_name']);
            $this->printJson($this->party->saveLead($data));
        endif;
    }
}
