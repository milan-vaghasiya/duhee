<?php
class Responsibility extends MY_Controller
{
    private $indexPage = "responsibility/index";
    private $FormPage = "responsibility/form";

	public function __construct()
    {
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Responsibility";
		$this->data['headData']->controller = "responsibility";
        $this->data['headData']->pageUrl = "responsibility";
	}
	
	public function index()
    {
        $this->data['tableHeader'] = getSalesDtHeader('responsibility');
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows()
    {
        $result = $this->responsibility->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getResponsibilityData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addResponsibility()
    {
        $this->load->view($this->FormPage, $this->data);
    }

    public function save()
    {
        $data = $this->input->post();
		$errorMessage = array();
        if(empty($data['remark']))
			$errorMessage['remark'] = "Responsibility is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->responsibility->save($data));
        endif;
    }

    public function edit()
    {     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->responsibility->getEmployeeResponsibility($id);
        $this->load->view($this->FormPage, $this->data);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
        else:
            $this->printJson($this->responsibility->delete($id));
        endif;
    }
}
?>