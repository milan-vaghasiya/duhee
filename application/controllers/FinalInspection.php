<?php
class FinalInspection extends MY_Controller{
    private $indexPage = "final_inspection/index";
    private $formPage = "final_inspection/form";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Final Inspection";
		$this->data['headData']->controller = "finalInspection";
		$this->data['headData']->pageUrl = "finalInspection";
	}

    public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->data['jobCardList'] = $this->jobcard->jobCardNoList();
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->finalInspection->getDTRows($data);
        $sendData = array();$i= $data['start']+1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getFinalInspectionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function acceptInspection(){
        $id = $this->input->post('id');
        $this->printJson($this->finalInspection->acceptInspection(['id'=>$id,'accepted_by'=>$this->session->userdata('loginId'),'accepted_at' => date("Y-m-d H:i:s")]));
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        //print_r($data);exit;
        if(empty($data['parameter_id']))
            $errorMessage['parameter_id'] = "Parameter is required.";
        if(empty($data['inspector_id']))
            $errorMessage['inspector_id'] = "Inspector Name is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->finalInspection->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        
        $this->data['inspectionData'] = $data;
        $this->data['inspectionTrans'] = $this->finalInspection->getInspectionTrans($data['id']);
        
        $this->data['inspectionParam'] = $this->finalInspection->getInspectionParam($data['product_id']);
        $this->data['inspectorData'] = $this->employee->getSetterInspectorList();
        $this->load->view($this->formPage,$this->data);
    }
}
?> 