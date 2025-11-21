<?php
class MasterOptions extends MY_Controller{
    private $indexPage = "master_options";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Master Options";
		$this->data['headData']->controller = "masterOptions";
        $this->data['headData']->pageUrl = "masterOptions";
	}
	
	public function index(){
        $this->data['dataRow'] = $this->masterOption->getMasterOptions();
        $this->load->view($this->indexPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        // if(empty($data['material_grade']))
        //     $errorMessage['material_grade'] = "Material Grade is required.";
        if(empty($data['color_code']))
            $errorMessage['color_code'] = "Color Code is required.";
        if(empty($data['thread_types']))
            $errorMessage['thread_types'] = "Thread Types is required.";
        if(empty($data['machine_idle_reason']))
            $errorMessage['machine_idle_reason'] = "Machine Idle Reason is required.";
        if(empty($data['ppap_level']))
            $errorMessage['ppap_level'] = "PPAP Level is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->masterOption->save($data));
        endif;
    }
	
	public function salaryConfig(){
        $this->data['dataRow'] = $this->masterOption->getMasterOptions();
        $this->load->view('salary_config',$this->data);
    }
    
    public function saveSalaryConfig(){
		$data = $this->input->post();
        $errorMessage = array();
        if(empty($data['basic_per']))
            $errorMessage['basic_per'] = "Basic(%) is required.";
        if(empty($data['pf_per']))
            $errorMessage['pf_per'] = "PF(%) is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->masterOption->save($data));
        endif;
    }
}
?>