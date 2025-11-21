<?php
class VehicleType extends MY_Controller
{
    private $indexPage = "vehicle_type/index";
    private $formPage = "vehicle_type/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "VehicleType";
		$this->data['headData']->controller = "vehicleType";
        $this->data['headData']->pageUrl = "vehicleType";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->vehicleType->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getVehicleTypeData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addVehicleType(){
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['vehicle_type']))
			$errorMessage['vehicle_type'] = "Vehicle Type is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->vehicleType->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->vehicleType->getVehicleType($id);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->vehicleType->delete($id));
        endif;
    }
}
?>