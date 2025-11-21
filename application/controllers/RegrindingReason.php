<?php
class RegrindingReason extends MY_Controller
{
    private $indexPage = "regrinding_reason/index";
    private $form = "regrinding_reason/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Regrinding Reason";
		$this->data['headData']->controller = "regrindingReason";		
	}
	
	public function index(){
        $this->data['headData']->pageUrl = "regrindingReason";
        $this->data['type'] = 5;
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    } 

    public function getDTRows($type){
        $data = $this->input->post();
        $data['type'] = $type;
        $result = $this->regrindingReason->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getRegrindingReasonData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addRegrindingReason(){
        $this->load->view($this->form,$this->data);
    }
	
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['remark']))
             $errorMessage['remark'] = "Reason is required.";
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->regrindingReason->save($data));
        endif;
    }

    public function edit(){
        $this->data['dataRow'] = $this->regrindingReason->getRegrindingData($this->input->post('id'));
        $this->load->view($this->form,$this->data);
        
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->regrindingReason->delete($id));
        endif;
    }
    
}
?>