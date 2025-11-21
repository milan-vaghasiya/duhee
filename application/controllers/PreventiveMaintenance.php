<?php
class PreventiveMaintenance extends MY_Controller{
    private $indexPage = "preventive_maintenance/index";
    private $formPage = "preventive_maintenance/form";
    private $editFormPage = "preventive_maintenance/edit_form";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Preventive Maintenance";
		$this->data['headData']->controller = "preventiveMaintenance";
		$this->data['headData']->pageUrl = "preventiveMaintenance";
	}
	
	public function index(){
        $this->data['tableHeader'] = getMaintenanceDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->prevMaintenance->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getPreventiveMaintenanceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMaintenancePlan(){
        $this->load->view($this->formPage,$this->data);
    }

    public function getMachineActivities(){
        $data = $this->input->post();
        $mcActivityData=$this->prevMaintenance->getMachineActivities($data['maintence_frequancy']);
        $tbody='';
        if(!empty($mcActivityData)){
            $i=1;
            foreach($mcActivityData as $row){
                $lastMaintData=$this->prevMaintenance->getLastMaintainanceDate($row->machine_id,$row->activity_id);
                $last_maintence_date = (!empty($lastMaintData->last_maintence_date)?$lastMaintData->last_maintence_date:date("Y-m-d"));
                $due_date = '';
                if($data['maintence_frequancy'] == 'Quarterly'){ $due_date = date("Y-m-d",strtotime($last_maintence_date.' +3 months')); }
                elseif($data['maintence_frequancy'] == 'Half Yearly'){ $due_date = date("Y-m-d",strtotime($last_maintence_date.' +6 months')); }
                elseif($data['maintence_frequancy'] == 'Yearly'){ $due_date = date("Y-m-d",strtotime($last_maintence_date.' +12 months')); }
                $tbody .='<tr>
                    <td>'.$i.'</td>
                    <td>['.$row->item_code.'] '.$row->item_name.'</td>
                    <td>'.$row->activities.'</td>
                    <td>'.formatDate($last_maintence_date).'</td>
                    <td>'.formatDate($due_date).'</td>
                    <td>
                    <input type="date" class="form-control" name="schedule_date[]">
                    <input type="hidden" class="form-control" name="last_maintence_date[]" value="'.$last_maintence_date.'">
                    <input type="hidden" class="form-control" name="due_date[]" value="'.$due_date.'">
                    <input type="hidden" class="form-control" name="id[]">
                    <input type="hidden" class="form-control" name="machine_id[]" value="'.$row->machine_id.'">
                    <input type="hidden" class="form-control" name="activity_id[]" value="'.$row->activity_id.'">
                    </td>
                </tr>';
                $i++;
            }
        }else{
            $tbody.='<tr>
                <td colspan="6" class="text-center">No data available.</td>
            </tr>';
        }
        $this->printJson(['status'=>1,'tbodyData'=>$tbody]);
    }

    public function save(){
        $data=$this->input->post();
        $errorMessage = array();
        if(empty($data['maintence_frequancy']))
            $errorMessage['maintence_frequancy'] = "Frequancy is required.";
        
        $count=0;
        if(!isset($data['schedule_date'])):
            $errorMessage['general_error'] = "Schedule Date is required.";
        else:
            foreach($data['schedule_date'] as $key=>$value){
                if(!empty($value)){ $count++;}
            }
            if(empty($count)){
                $errorMessage['general_error'] = "Schedule Date is required.";
            }
        endif;
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->prevMaintenance->save($data));
        endif;
    }

    public function edit(){
        $data=$this->input->post();
        $this->data['dataRow']=$this->prevMaintenance->getMaintanancePlan($data['id']);
        $this->data['partyData'] = $this->party->getPartyList(2,3);
        $this->load->view($this->editFormPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->prevMaintenance->delete($id));
        endif;
    }

    public function saveUpdatedPlan(){
        $data=$this->input->post();
        $errorMessage = array();
        if(empty($data['actual_date']))
            $errorMessage['actual_date'] = "Actual Date is required.";
        if(empty($data['solution_by']) && $data['m_agency']==1)
            $errorMessage['solution_by'] = "Solution By is required.";
        if(empty($data['vendor_id']) && $data['m_agency']==2)
            $errorMessage['solution_by'] = "Solution By is required.";
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['vendor']=($data['m_agency'] == 1)?$data['solution_by']:$data['vendor_id'];
            unset($data['solution_by'],$data['vendor_id']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->prevMaintenance->saveUpdatedPlan($data));
        endif;
    }
}
