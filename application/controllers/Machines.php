<?php
class Machines extends MY_Controller{
    private $indexPage = "machine/index";
    private $machineForm = "machine/form";
    private $activityForm = "machine/activity";
    private $requestForm = "purchase_request/purchase_request";
    private $assgnOprInq = "machine/assign_oprinq";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Machines";
		$this->data['headData']->controller = "machines";
		$this->data['headData']->pageUrl = "machines";
	}
	
	public function index(){
        $this->data['tableHeader'] = getMaintenanceDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->machine->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->process_name = '';
            if(!empty($row->process_id)):
                $pdata = $this->machine->getProcess($row->process_id);
                $z=0;
                foreach($pdata as $row1):
                    if($z==0) {$row->process_name .= $row1->process_name;}else{$row->process_name .= ',<br>'.$row1->process_name;}$z++;
                endforeach;
            endif;
            $sendData[] = getMachineData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMachine(){
        $this->data['machineTypes'] = $this->machineType->getMachineTypeList();
        $this->data['categoryList'] = $this->item->getCategoryList(5);
        $this->data['processData'] = $this->process->getProcessList();
        $this->data['empData'] = $this->employee->getEmpList();
        $this->load->view($this->machineForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        if(isset($data['ftype']) and $data['ftype'] == 'activities'):
            unset($data['ftype']);
            $this->saveActivity($data);
        else:
            $errorMessage = array();
            if(empty($data['item_code']))
                $errorMessage['item_code'] = "Machine no. is required.";
            //if(empty($data['machine_brand']))
                //$errorMessage['machine_brand'] = "Brand Name is required.";
            ///if(empty($data['machine_model']))
                //$errorMessage['machine_model'] = "Machine Model is required.";
            //if(empty($data['process_id']))
                //$errorMessage['process_id'] = "Process Name is required.";

            if(!empty($errorMessage)):
                $this->printJson(['status'=>0,'message'=>$errorMessage]);
            else:
                unset($data['processSelect'],$data['mTypeSelect']);
                $data['item_type']=5;
                $data['created_by'] = $this->session->userdata('loginId');
                $this->printJson($this->machine->save($data));
            endif;
        endif;
        
    }

    public function edit(){
        $this->data['dataRow'] = $this->machine->getMachine($this->input->post('id'));
        $this->data['machineTypes'] = $this->machineType->getMachineTypeList();
        $this->data['categoryList'] = $this->item->getCategoryList(5);
        $this->data['processData'] = $this->process->getProcessList();
        $this->data['empData'] = $this->employee->getEmpList();
        $this->load->view($this->machineForm,$this->data);
    }

    public function setActivity(){
        $id = $this->input->post('id');
        $this->data['activityData'] = $this->machine->getActivity();
		$this->data['dataRow'] = $this->machine->getmaintanenceData($id);
        $this->load->view($this->activityForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->machine->delete($id));
        endif;
    }
  
    public function saveActivity() {
		$data = $this->input->post();
		$errorMessage = array();
		if(empty($data['activity_id']))
			$errorMessage['activity_id'] = "Machine Activities is required.";
        if(empty($data['activity_id'][0]))
			$errorMessage['activity_error'] = "Activities is required.";     
            
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$createdBy = Array();
			foreach($data['activity_id'] as $key=>$value){$createdBy[] = $this->session->userdata('loginId');}
            $activityData = [
                'id' => $data['id'],
				'activity_id' => $data['activity_id'],
				'checking_frequancy' => $data['checking_frequancy'],
                'created_by' =>  $createdBy
            ];
            $this->printJson($this->machine->saveActivity($data['machine_id'],$activityData));
        endif;
    } 

    public function addPurchaseRequest(){
        $this->data['itemData'] = $this->item->getItemLists(5);
        $this->load->view($this->requestForm,$this->data);
    }

    public function savePurchaseRequest(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['req_item_id'][0]))
            $errorMessage['req_item_id'] = "Item Name is required.";
        if(empty($data['req_date']))
            $errorMessage['req_date'] = "Request Date is required.";
        if(empty($data['req_qty'][0]))
            $errorMessage['req_qty'] = "Request Qty. is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['item_data'] = "";$itemArray = array();
			if(isset($data['req_item_id']) && !empty($data['req_item_id'])):
				foreach($data['req_item_id'] as $key=>$value):
					$itemArray[] = [
						'req_item_id' => $value,
						'req_qty' => $data['req_qty'][$key],
						'req_item_name' => $data['req_item_name'][$key]
					];
				endforeach;
				$data['item_data'] = json_encode($itemArray);
			endif;
            unset($data['req_item_id'], $data['req_item_name'], $data['req_qty']);
            $this->printJson($this->jobMaterial->savePurchaseRequest($data));
        endif;
    }

     // Created By Meghavi @08/11/2022
    public function getAssignOprInq(){
        $assignData = $this->machine->getMachineList(); 
        $machineArray = [];
        foreach($assignData as $row){
           $dData =  $this->machine->getMachineAssignOperator(['machine_id'=>$row->id,'shift_id'=>1,'emp_type'=>'OPR']); 
           $row->dopr_id = !empty($dData->opr_id)?$dData->opr_id:'';
           $dinqData =  $this->machine->getMachineAssignOperator(['machine_id'=>$row->id,'shift_id'=>1,'emp_type'=>'INQ']);
           $row->dinq_id = !empty($dinqData->inq_id)?$dinqData->inq_id:'';

           $nData =  $this->machine->getMachineAssignOperator(['machine_id'=>$row->id,'shift_id'=>2,'emp_type'=>'OPR']); 
           $row->nopr_id = !empty($nData->opr_id)?$nData->opr_id:'';
           $nInqData =  $this->machine->getMachineAssignOperator(['machine_id'=>$row->id,'shift_id'=>2,'emp_type'=>'INQ']); 
           $row->ninq_id = !empty($nInqData->inq_id)?$nInqData->inq_id:'';
           $machineArray[]=$row;
        }
        $this->data['assignData'] = $machineArray;
        $this->data['oprList']=$this->employee->getMachineOperatorList();
        $this->data['inqList']=$this->employee->getLineInspectorList();
        $this->load->view($this->assgnOprInq,$this->data);
    }

    public function saveOprInqData(){
        $data = $this->input->post();
        $data['created_by'] = $this->session->userdata('loginId');
        $this->printJson($this->machine->saveOprInqData($data));
    }

    public function printMcAsignData($date,$shift_id,$emp_type){
      $dateRange = explode("~",$date);
      $from_date = date("Y-m-d H:i:s",strtotime($dateRange[0]));
      $to_date = (!empty($dateRange[0]))?date("Y-m-d H:i:s",strtotime($dateRange[1])):'';
      $itemData = $this->machine->getMachineList();
      $logo = base_url('assets/images/letterhead_top.png');
      $htmlHeader = '<table class="table">
                                <tr>
                                    <td style="width:20%;"><img src="' . $logo . '" ></td>
                                    
                                </tr>
                            </table>';
							
		$itemList = '<table class="table txInvHead mb-10" >
                        <tr class="txRow">
                            <td class="fs-20 text-left" style="letter-spacing: 1px;font-weight:bold;">Asigned '.(($emp_type == 'OPR')?'Operator':'Line Inspector').'</td>
                            <td class="text-right pad-right-10">'.date("d/m/Y H:i:s",strtotime($from_date)).' - '.date("d/m/Y H:i:s",strtotime($to_date)).'</td>
                        </tr>
                    </table><br>
                <table class="table vendor_challan_table mt-20">
                    <thead>
                        <tr class="text-center">
                            <th>#</th>
                            <th>Machine</th>
                            <th style="width:20%">From Date</th>
                            <th style="width:20%">To Date</th>
                            <th>'.(($emp_type == 'OPR')?'Operator':'Line Inspector').'</th>
                        </tr>
                    </thead>
                    <tbody>';
                    $i=1;
        foreach($itemData as $itm){
            $asignData = $this->machine->getMachineAsignData(['from_date'=>$from_date,'to_date'=>$to_date,'emp_type'=>$emp_type,'shift_id'=>$shift_id,'machine_id'=>$itm->id]); 
            
            $itemList .='<tr>
                <td rowspan="'.(!empty($asignData)?count($asignData):0).'">'.$i++.'</td>
                <td rowspan="'.(!empty($asignData)?count($asignData):0).'">['.$itm->item_code.'] '.$itm->item_name.'</td>
                <td class="text-center">'.(!empty($asignData[0]->from_date)?date("d-m-Y H:i:s",strtotime($asignData[0]->from_date) ):'-').'</td>
                <td class="text-center">'.(!empty($asignData[0]->to_date)?date("d-m-Y H:i:s",strtotime($asignData[0]->to_date) ):'-').'</td>
                <td class="text-center">'.(!empty($asignData[0]->emp_name)?$asignData[0]->emp_name:'-').'</td>
            </tr>';
            if(!empty($asignData[1])){
                for($j=1; $j<count($asignData);$j++){
                    $itemList .= '<tr class="text-center">
                        <td>'.(!empty($asignData[$j]->from_date)?date("d-m-Y H:i:s",strtotime($asignData[$j]->from_date)  ):'-').'</td>
                        <td>'.(!empty($asignData[$j]->to_date)?date("d-m-Y H:i:s",strtotime($asignData[$j]->to_date) ):'-').'</td>
                        <td>'.(!empty($asignData[$j]->emp_name)?$asignData[$j]->emp_name:'-').'</td>
                    </tr>';
                }
            }	
        }				
        $itemList .= '</tbody></table>';
				
		
        $pdfData = $itemList;
        // print_r($pdfData);exit; 
        //$mpdf = $this->m_pdf->load();
        $mpdf = new \Mpdf\Mpdf();
        $pdfFileName ='machine.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
        $mpdf->AddPage('P', '', '', '', '', 5, 5,28, 5, 1, 1);
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }
}
?>