<?php
class MachineType extends MY_Controller {
    private $indexPage = "machine_type/index";
    private $typeForm = "machine_type/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Machine Type";
		$this->data['headData']->controller = "machineType";
		$this->data['headData']->pageUrl = "machineType";
	}
	
	public function index(){
        $this->data['tableHeader'] = getMaintenanceDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->machineType->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getMachineTypeData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMachineType(){
        $this->load->view($this->typeForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['typeof_machine']))
            $errorMessage['typeof_machine'] = "Machine Type is required.";
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->machineType->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->machineType->getMachineType($id);
        $this->load->view($this->typeForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->machineType->delete($id));
        endif;
    }
}
?>