<?php
class QcChallan extends MY_Controller{
    private $indexPage = "qc_challan/index";
    private $formPage = "qc_challan/form";
    private $returnPage = "qc_challan/return_form";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "QC Challan";
		$this->data['headData']->controller = "qcChallan";
		$this->data['headData']->pageUrl = "qcChallan";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->qcChallan->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;  
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getQcChallanData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    public function createChallan($id){
		$this->data['headData']->pageUrl = "qcChallan";
        $this->data['trans_prefix'] = 'QCH/'.$this->shortYear.'/';
        $this->data['trans_no'] = $this->qcChallan->nextTransNo(1);
        $this->data['partyData'] = $this->party->getVendorList();
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['itemData']  = $this->qcInstrument->getActiveInstruments();
        $this->data['challanItem'] = $this->qcChallan->getInstrumentForChallan($id);
        $this->data['machineData'] = $this->machine->getMachineList();
        $this->load->view($this->formPage,$this->data);
    }

    public function addChallan(){
        $this->data['trans_prefix'] = 'QCH/'.$this->shortYear.'/';
        $this->data['trans_no'] = $this->qcChallan->nextTransNo(1);
        $this->data['partyData'] = $this->party->getVendorList();
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['itemData']  = $this->qcInstrument->getActiveInstruments(); 
        $this->data['machineData'] = $this->machine->getMachineList();
        $this->load->view($this->formPage,$this->data);
    }
    
    public function getPartyAndMachine(){
        $challan_type = $this->input->post('challan_type'); 
        $options = '<option value="0">IN-HOUSE</option>';
        $machineoptions = '<option value="">Select Machine</option>';
        if($challan_type != 1){
            $partyData = $this->party->getVendorList();
            foreach($partyData as $row):
                $options .= '<option value="'.$row->id.'">'.$row->party_name.'</option>';
            endforeach;
            
        }else{
            $deptData = $this->department->getDepartmentList();
            foreach($deptData as $row):
                $options .= '<option value="'.$row->id.'">'.$row->name.'</option>';
            endforeach;
            
            $machineData = $this->machine->getMachineList();
            foreach($machineData as $row):
                $machineoptions .= '<option value="'.$row->id.'">'.((!empty($row->item_code))?'['.$row->item_code.'] '.$row->item_name:$row->item_name).'</option>';
            endforeach;
        }
        $this->printJson(['status'=>1,'options'=>$options,'machineoptions'=>$machineoptions]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_no']))
            $errorMessage['trans_no'] = "Challan No is required."; 
        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Challan Date is required.";
        //if(empty($data['party_id']))
           // $errorMessage['party_id'] = "Issue From is required.";
        
        if(empty($data['item_id'][0]))
            $errorMessage['item_name_error'] = "Items is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $masterData = [
                'id' => $data['id'],
                'trans_prefix' => $data['trans_prefix'],  
                'trans_no' => $data['trans_no'],
                'challan_type' => $data['challan_type'],
                'trans_date' => $data['trans_date'],
                'party_id' => $data['party_id'],
                'machine_id' => $data['machine_id'],
                'remark' => $data['remark'],
                'created_by' => $this->session->userdata('loginId')
            ];

            $itemData = [
                'id' => $data['trans_id'],
                'item_id' => $data['item_id'],
                'batch_no' => $data['batch_no'],
                'item_remark' => $data['item_remark'],
                'entry_type' => 1,
                'created_by' => $this->session->userdata('loginId')
            ];

            $this->printJson($this->qcChallan->save($masterData,$itemData));
        endif;
    }

    public function edit($id){
        $this->data['partyData'] = $this->party->getVendorList();
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['itemData']  = $this->qcInstrument->getActiveInstruments();
        $this->data['dataRow'] = $this->qcChallan->getQcChallan($id);
        $this->data['machineData'] = $this->machine->getMachineList();
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->qcChallan->deleteChallan($id));
		endif;
	}

    public function returnChallan(){
		$id = $this->input->post('id');
        $this->data['dataRow'] = $this->qcChallan->getQcChallanTransRow($id);
        $this->data['locationList'] = $this->store->getNextStoreLevel(42);
        $this->load->view($this->returnPage,$this->data);
    }

    public function saveReturnChallan(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['receive_at']))
            $errorMessage['receive_at'] = "Receive date is required.";
        if(empty($data['to_location']))
            $errorMessage['to_location'] = "Receive Location is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['receive_by'] = $this->loginId;
            $this->printJson($this->qcChallan->saveReturnChallan($data));
        endif;
    }

    public function confirmChallan(){
        $data = $this->input->post();
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->qcChallan->confirmChallan($data));
		endif;
    }
}
?>