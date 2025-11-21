<?php 
class MaterialRequest extends MY_Controller{
    private $indexPage = "material_request/index";
    private $requestForm = "material_request/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Material Request";
		$this->data['headData']->controller = "materialRequest";
		$this->data['headData']->pageUrl = "materialRequest";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->jobMaretialRequest->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
            $row->req_item_name = (!empty($row->req_item_id))?$this->item->getItem($row->req_item_id)->item_name:"";
            $row->unit_name = (!empty($row->req_item_id))?$this->item->itemUnit($this->item->getItem($row->req_item_id)->unit_id)->unit_name:"";
            $sendData[] = getMaterialRequest($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addRequest(){
        $this->data['jobCardData'] = $this->jobcard->getJobcardList();
        $this->data['processList'] = $this->process->getProcessList();
        $this->data['itemData'] = $this->item->getItemList(3);
        $this->load->view($this->requestForm,$this->data);
    }

    public function getItemOptions(){
        $type = $this->input->post('type');
        $itemData = $this->item->getItemList();
        $options = '<option value="">Select Item Name</option>';
        foreach($itemData as $row):
			if($row->item_type == $type):             
				$options .= '<option value="'.$row->id.'" data-stock="'.$row->qty.' '.$row->unit_name.'">'.$row->item_name.'</option>';     
			endif;
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['req_item_id']))
            $errorMessage['req_item_id'] = "Request Item Name is required.";
        if(empty($data['req_qty']))
            $errorMessage['req_qty'] = "Request Qty. is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->jobMaretialRequest->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['jobCardData'] = $this->jobcard->getJobcardList();
        $this->data['processList'] = $this->process->getProcessList();
        $this->data['itemData'] = $this->item->getItemList(3);
        $this->data['dataRow'] = $this->jobMaretialRequest->getRequestData($id);
        $this->load->view($this->requestForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobMaretialRequest->delete($id));
        endif;
    }
}
?>