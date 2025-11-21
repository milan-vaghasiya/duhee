<?php
class Holidays extends MY_Controller
{
    private $indexPage = "holidays/index";
    private $holidaysForm = "holidays/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Holidays";
		$this->data['headData']->controller = "holidays";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->holiday->getDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getHolidaysData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addHolidays(){
        $this->load->view($this->holidaysForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['holiday_date']))
			$errorMessage['holiday_date'] = "holiday Date is required.";
        if(empty($data['holiday_type']))
			$errorMessage['holiday_type'] = "Holiday type is required.";
        if(empty($data['title']))
			$errorMessage['title'] = "Title is required.";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->holiday->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->holiday->getholiday($id);
        $this->load->view($this->holidaysForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->holiday->delete($id));
        endif;
    }
}
?>