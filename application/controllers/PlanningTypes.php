<?php
class PlanningTypes extends MY_Controller
{
    private $indexPage = "planning_types/index";
    private $ptForm = "planning_types/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "PlanningTypes";
		$this->data['headData']->controller = "planningTypes";
	}

    public function index(){
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->planningTypes->getDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getPlanningData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPlanningTypes(){
        $id = $this->input->post('id'); 
        $this->load->view($this->ptForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['planning_type']))
            $errorMessage['planning_type'] = "Planning Type is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->planningTypes->save($data));
        endif;
    }

    public function edit(){
        $this->data['dataRow'] = $this->planningTypes->getPlanning($this->input->post('id'));
        $this->load->view($this->ptForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->planningTypes->delete($id));
        endif;
    }
}
?>