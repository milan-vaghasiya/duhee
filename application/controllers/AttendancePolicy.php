<?php
class AttendancePolicy extends MY_Controller
{
    private $indexPage = "attendance_policy/index";
    private $policyForm = "attendance_policy/form";
    private $assign_policy = "attendance_policy/assign_policy";
    private $chargesForm = "attendance_policy/charges_form";
    private $policyType = ["1"=>"Late In","2"=>"Early Out","3"=>"Leave Without Permission"]; //,"3"=>"Short Leave"
    
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Attendance Policy";
		$this->data['headData']->controller = "attendancePolicy";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->policy->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;     
            $row->min_lbl = ($row->policy_type != 3) ? ' <small>Minutes</small>' : ' <small>Hours</small>';
            $row->penalty_lbl = ($row->penalty == 1) ? 'Yes' : 'No';
            $row->policy_type = $this->policyType[$row->policy_type];
            $sendData[] = getAttendancePolicyData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addAttendancePolicy(){
        $this->data['policyType'] = $this->policyType;
        $this->load->view($this->policyForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['policy_name']))
			$errorMessage['policy_name'] = "Policy Name is required.";
		
		if($data['policy_type'] != 3):
            if(empty($data['day_month'])):
    			$errorMessage['day_month'] = "Day/Month is required.";
    		endif;
            if(empty($data['minute_day'])):
    		    $errorMessage['minute_day'] = "Minutes/Day is required.";
    		endif;
        /*else:
            if(empty($data['day_month'])):
    			$errorMessage['day_month'] = "Half Leave(Hours) is required.";
    		endif;
            if(empty($data['minute_day'])):
    		    $errorMessage['minute_day'] = "Short Leave(Hours) is required.";
    		endif;
    		if(empty($data['penalty_hrs'])):
    		    $errorMessage['penalty_hrs'] = "Full Leave(Day) is required.";
    		endif;*/
        endif;
		
        
		if($data['penalty'] == 1):
		    if(empty($data['penalty_hrs'])):
		        $errorMessage['penalty_hrs'] = "Penalty Hours is required.";
		    endif;
	    endif;
			
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->policy->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['policyType'] = $this->policyType;
        $this->data['dataRow'] = $this->policy->getAttendancePolicy($id);
        $this->load->view($this->policyForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->policy->delete($id));
        endif;
    }

    /* Assign Policy */
    public function assignPolicy(){
        $this->data['policyData'] = $this->policy->getAttendancePolicies();
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['categoryData'] = $this->category->getCategoryList();
        $this->load->view($this->assign_policy,$this->data);
    }

    public function getAssignPolicy(){
        $data = $this->input->post();
        $result = $this->policy->getEmpList();

		$tbodyData=""; $i=1;
        if($data['policy_id'] != ""):
            foreach($result as $row):
                $check = (!empty($row->attendance_policy) AND $row->attendance_policy == $data['policy_id'])?"checked":"";
                $tbodyData .= '<tr>
                    <td class="text-center">'.$i++.'</td>
                    <td>'.$row->emp_name.'</td>
                    <td>'.$row->emp_code.'</td>
                    <td>'.$row->emp_contact.'</td>
                    <td>'.$row->name.'</td>
                    <td>'.$row->title.'</td>
                    <td>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="form-check-input filled-in" name="attendance_policy[]" value="'.$row->id.'" id="customCheck'.$row->id.'" '.$check.'>
                            <label class="form-check-label" for="customCheck'.$row->id.'"></label>
                        </div>
                    </td>
                </tr>';
            endforeach;
        else:
            $tbodyData .= '<tr><td colspan="7" style="text-align:center !important;">No data found</td></tr>';
        endif;
		$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
    }

    public function saveAssignPolicy(){
        $data = $this->input->post();
        $this->printJson($this->policy->saveAssignPolicy($data));
    }
    
    /* Employee Charges */
    public function addEmpCharges(){
        $this->data['dataRow'] = $this->policy->getMasterOptions();
        $this->load->view($this->chargesForm,$this->data);
    }
    public function saveEmpCharges(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->masterOption->save($data));
        endif;
    }
}
?>