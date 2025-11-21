<?php
class MeasurementTechnique extends MY_Controller
{
    private $indexPage = "measurement_technique/index";
    private $formPage = "measurement_technique/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "MeasurementTechnique";
		$this->data['headData']->controller = "measurementTechnique";
        $this->data['headData']->pageUrl = "measurementTechnique";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->measurementTechnique->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getMeasurementTechniqueData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMeasurementTechnique(){
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['measurement_technique']))
			$errorMessage['measurement_technique'] = "Measurement Technique is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->measurementTechnique->save($data));
        endif;
    }   

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->measurementTechnique->getMeasurementTechnique($id);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->measurementTechnique->delete($id));
        endif;
    }
}
?>