<?php
class Departments extends MY_Controller
{
    private $indexPage = "hr/department/index";
    private $departmentForm = "hr/department/form";
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Departments";
		$this->data['headData']->controller = "hr/departments";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('departments');   
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows(){
        $result = $this->department->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;$row->leave_auths = '';$c=0;
			/*if(!empty($row->leave_authorities)):
				$la = explode(",",$row->leave_authorities);
				if(!empty($la))
				{
					foreach($la as $empid)
					{
						$row->leave_auths = "";
						$emp = $this->department->getLeaveAuthority($empid);
						if(!empty($emp)):
							if($c==0){$row->leave_auths .= $emp->emp_name;}else{$row->leave_auths .= '<br>'.$emp->emp_name;}$c++;
						else:
							$row->leave_auths = "";
						endif;
					}
				}
			endif;*/
            $sendData[] = getDepartmentData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	
    public function addDepartment(){
        // $this->data['empData'] = $this->department->getEmployees();
        $this->data['sectionData'] = explode(',', $this->department->getMasterOptions()->section); 
        $this->load->view($this->departmentForm,$this->data);
    }
    
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['name']))
            $errorMessage['name'] = "Department name is required.";
        /*if(empty($data['section']))
            $errorMessage['section'] = "Section is required.";*/

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			unset($data['sectionSelect']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->department->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['sectionData'] = explode(',', $this->department->getMasterOptions()->section); 
        $this->data['dataRow'] = $this->department->getDepartment($id);
        // $this->data['empData'] = $this->department->getEmployees();
        $this->load->view($this->departmentForm,$this->data);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->department->delete($id));
        endif;
    }
    
}
?>