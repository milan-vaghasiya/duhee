<?php
class FurnaceMaster extends MY_Controller
{
    private $indexPage = "furnace_master/index";
    private $formPage = "furnace_master/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "furnaceMaster";
		$this->data['headData']->controller = "furnaceMaster";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->furnaceModel->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getFurnaceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addFurnace(){
        $this->load->view($this->formPage);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['furnace_no']))
            $errorMessage['furnace_no'] = "Furnace No. is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->furnaceModel->save($data));
        endif;
    }

    public function edit(){
        $this->data['dataRow'] = $this->furnaceModel->getFurnaceMasterData($this->input->post('id'));
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->furnaceModel->delete($id));
        endif;
    }
}
?>