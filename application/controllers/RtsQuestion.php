<?php
class RtsQuestion extends MY_Controller
{
    private $indexPage = "rts_question/index";
    private $headingFormPage = "rts_question/quest_heading";
    private $questIndexPage = "rts_question/quest_index";
    private $questFormPage = "rts_question/quest_form";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "RTS Question";
		$this->data['headData']->controller = "rtsQuestion";
		$this->data['headData']->pageUrl = "rtsQuestion";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader('rtsQuestionHeading');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($ref_id = 0){
        $data = $this->input->post(); $data['ref_id'] = $ref_id;$data['type']=2;
        $result = $this->rtsQuestion->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getRtsQuestionHeadingData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function questionIndex($id){
        $this->data['ref_id'] = $id;
        $this->data['questData'] =$this->rtsQuestion->getRTSQuest($id);
        $this->data['tableHeader'] = getConfigDtHeader('rtsQuestion');
        $this->load->view($this->questIndexPage,$this->data);
    }

    public function getQuestionDTRows($ref_id){
        $data = $this->input->post(); $data['ref_id'] = $ref_id;$data['type']=3;
        $result = $this->rtsQuestion->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getRtsQuestionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }


    public function addQuestionHeading(){
        $this->load->view($this->headingFormPage,$this->data);
    }

    public function addQuestion($ref_id){
        $this->data['ref_id'] = $ref_id;
        $this->load->view($this->questFormPage,$this->data);
    }
    public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['description']))
            $errorMessage['description'] = "required.";
		

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->rtsQuestion->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->rtsQuestion->getRTSQuest($id);
        $this->load->view($this->headingFormPage,$this->data);
    }

    public function editQuestion(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->rtsQuestion->getRTSQuest($id);
        $this->load->view($this->questFormPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->rtsQuestion->delete($id));
        endif;
    }
    
}
?>