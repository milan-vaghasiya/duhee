<?php
class GateEntry extends MY_Controller{
    private $indexPage = "gate_entry/index";
    private $form = "gate_entry/form";

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Gate Entry Register";
		$this->data['headData']->controller = "gateEntry";  
        $this->data['headData']->pageUrl = "gateEntry";      
    }

    public function index(){        
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
		$this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->gateEntry->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;        
            $row->controller = $this->data['headData']->controller;    
            $row->status = $status;
            $sendData[] = getGateEntryData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function add(){
        $this->data['transportList'] = $this->transport->getTransportList();
        $this->data['vehicleTypeList'] = $this->vehicleType->getVehicleTypeList();
        $this->data['partyList'] = $this->party->getPartyListOnCategory("1,2,3");
        $this->data['itemList'] = $this->item->getItemLists("2,3");
        $this->data['next_no'] = $this->gateEntry->getNextNo();
        $this->data['trans_prefix'] = "GE/".n2y(date("Y"));
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['driver_name']))
            $errorMessage['driver_name'] = "Driver Name is required.";
        if(empty($data['driver_contact']))
            $errorMessage['driver_contact'] = "Driver Contact No. is required.";
        // if(empty($data['vehicle_type']))
        //     $errorMessage['vehicle_type'] = "vehicle Type is required.";
        // if(empty($data['vehicle_no']))
        //     $errorMessage['vehicle_no'] = "Vehicle No. is required.";
        if(empty($data['inv_no']) && empty($data['doc_no']))
            $errorMessage['inv_no'] = "Invoice No OR Challan No is required";

        if(!empty($data['inv_no']) && empty($data['inv_date']))
            $errorMessage['inv_date'] = "Invoice Date is required";

        if(!empty($data['doc_no']) && empty($data['doc_date']))
            $errorMessage['doc_date'] = "Challan Date is required";

        if(!empty($data['vehicle_no']) && strlen(trim($data['vehicle_no'])) < 7 || !empty($data['vehicle_no']) && strlen(trim($data['vehicle_no'])) > 10)
            $errorMessage['vehicle_no'] = "Invalid Vehicle No.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['trans_prefix'] = "GE/".n2y(date("Y"));
            $data['trans_type'] = 1;
            $data['trans_no'] = (empty($data['is_edit']))?$this->gateEntry->getNextNo():$data['trans_no'];
            $this->printJson($this->gateEntry->save($data));
        endif;
    }

    public function edit($trans_number){
        $trans_number = str_replace("_","/",$trans_number);
        $this->data['transportList'] = $this->transport->getTransportList();
        $this->data['vehicleTypeList'] = $this->vehicleType->getVehicleTypeList();
        $this->data['partyList'] = $this->party->getPartyListOnCategory("1,2,3");
        $this->data['itemList'] = $this->item->getItemLists("2,3");
        $this->data['is_edit'] = 1;
        $this->data['dataRow'] = $this->gateEntry->getGateEntryData($trans_number);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->gateEntry->delete($id));
        endif;
    }
}
?>