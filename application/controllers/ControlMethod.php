<?php
class ControlMethod extends MY_Controller
{
    private $indexPage = "control_method/index";
    private $formPage = "control_method/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "controlMethod";
		$this->data['headData']->controller = "controlMethod";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->controlMethod->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getControlMethodData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addControlMethod(){
        $this->load->view($this->formPage);
    }

    public function save(){
        $data = $this->input->post(); //print_r($data);exit;
        $errorMessage = array();
        if(empty($data['control_method']))
            $errorMessage['control_method'] = "Control Method is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['control_method'] = strtoupper($data['control_method']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->controlMethod->save($data));
        endif;
    }

    public function edit(){
        $this->data['dataRow'] = $this->controlMethod->getControlMethod($this->input->post('id'));
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->controlMethod->delete($id));
        endif;
    }
}
?>