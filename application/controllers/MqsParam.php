<?php
class MqsParam extends MY_Controller
{
    private $indexPage = "mqs_param/index";
    private $formPage = "mqs_param/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "MATERIAL QUALITY STANDARD";
		$this->data['headData']->controller = "mqsParam";
		$this->data['headData']->pageUrl = "mqsParam";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->materialGrade->getDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getMaterialDataForMqs($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMQSParameter(){
        $data = $this->input->post();
        $this->data['grade_id'] = $data['id'];
        $this->data['processList'] = $this->mqs->getMQSParameterList(4);
        $parameters = $this->mqs->getMQSReport(['grade_id'=>$data['id']]);
      
        if(!empty($parameters)){ 
            $this->data['parameterList'] = $parameters;
        }else{
            $this->data['parameterList'] = $this->mqs->getMQSParameterList('2,3');
        }
       

        $this->load->view($this->formPage,$this->data);
    }
    
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['process_id']))
            $errorMessage['process_id'] = "Process is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->mqs->save($data));
        endif;
    }
}
?>