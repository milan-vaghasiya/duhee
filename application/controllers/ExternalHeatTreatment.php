<?php
class ExternalHeatTreatment extends MY_Controller{
    private $indexPage = "external_heat_treatment/index";
    private $formPage = "external_heat_treatment/form";
    private $profile = "external_heat_treatment/external_profile";
    private $approveForm = "external_heat_treatment/approval_form";

    
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "SQF Master";
		$this->data['headData']->controller = "externalHeatTreatment";
	}
	
	public function index(){
        $this->data['headData']->pageUrl = "externalHeatTreatment";
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->externalHeatTreatment->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            if(empty($row->approve_by)):
				$row->status = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
			else:
				$row->status = '<span class="badge badge-pill badge-success m-1">Approve</span>';      
            endif;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getExternalHeatTreatmentData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }


    public function addExternalHT(){
        $this->data['itemList'] = $this->item->getItemList(1);
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['item_id']))
            $errorMessage['item_id'] = "Item Name Is required."; 
        
        if(!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->externalHeatTreatment->save($data));
        endif;
    }
    
    public function edit(){
        $id = $this->input->post('id');
        $this->data['itemList'] = $this->item->getItemList(1);
        $this->data['dataRow'] = $this->externalHeatTreatment->getHeatTreatment($id);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
		else:
			$this->printJson($this->externalHeatTreatment->delete($id));
		endif;
	}

      /* External profile */
      public function externalProfile($id){
        $htData = $this->externalHeatTreatment->getHeatTreatment($id);
        $this->data['htData'] = $htData;
        $this->load->view($this->profile,$this->data);
    }

    public function saveBatchQtyDetails(){
        $data = $this->input->post();
		$errorMessage = array();
        if(empty($data['load_style']))
            $errorMessage['load_style'] = "Load Style Is required."; 
        if(empty($data['batch_qty']))
            $errorMessage['batch_qty'] = "Batch Qty Is required."; 
        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $htData = $this->externalHeatTreatment->getHeatTreatment($data['id']);

            //$batch_qty = ($data['noof_ring'] * $data['noof_layer'] * $data['noof_compart']);
            $batch_wt = ($htData->wt_pcs * $data['batch_qty']);
            $batch_gross_wt = ($batch_wt + $data['fixture_wt']);
           // $load_style =($data['noof_ring']."*" .$data['noof_layer']."*". $data['noof_compart']);
            $batchData = [
                'id'=>$data['id'],
                'item_id' => $data['item_id'],
                'noof_ring' => $data['noof_ring'],
                'noof_layer' => $data['noof_layer'],
                'noof_compart' => $data['noof_compart'],
                'load_style' => $data['load_style'],
                'batch_qty' => $data['batch_qty'],
                'batch_wt' => $batch_wt,
                'fixture_wt' => $data['fixture_wt'],
                'batch_gross_wt' => $batch_gross_wt,
                'created_by' =>  $this->session->userdata('loginId')
            ]; 
                $this->externalHeatTreatment->saveBatchQtyDetails($batchData);

            $tbodyData="";$i=1; 
            if(!empty($htData)):
                $i=1;
                    $tbodyData.= '<tr>
                            <td class="text-center">'.$i++.'</td>
                            <td class="text-center">'.$htData->noof_ring.'</td>
                            <td class="text-center">'.$htData->noof_layer.'</td>
                            <td class="text-center">'.$htData->noof_compart.'</td>
                            <td class="text-center">'.$htData->load_style.'</td>
                            <td class="text-center">'.$htData->batch_qty.'</td>
                            <td class="text-center">'.$htData->batch_wt.'</td>
                            <td class="text-center">'.$htData->fixture_wt.'</td>
                            <td class="text-center">'.$htData->batch_gross_wt.'</td>
                            </tr>';
            else:
                $tbodyData.= '<tr><td colspan="9" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }

    public function saveProcessingDetail(){
        $data = $this->input->post();
		$errorMessage = array();
       
        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $htData = $this->externalHeatTreatment->getHeatTreatment($data['id']);

            $total_time = round(($data['ct_ht'] + $data['uniformity'] + $data['carbur_time'] + $data['diffusion_time'] + $data['quench_time']) / 60,2);
            $batch_cost = ($total_time * 3100);
            $cost_pcs = ($batch_cost / $htData->batch_qty);
            $cost_kg = ((!empty($cost_pcs) && !empty($htData->wt_pcs)) ? ($cost_pcs / $htData->wt_pcs) : 0);

            $batchData = [
                'id'=>$data['id'],
                'item_id' => $data['item_id'],
                'cp_per' => $data['cp_per'],
                'ct_ht' => $data['ct_ht'],
                'uniformity' => $data['uniformity'],
                'carbur_time' => $data['carbur_time'],
                'carbur_temp' => $data['carbur_temp'],
                'diffusion_time' => $data['diffusion_time'],
                'quench_temp' => $data['quench_temp'],
                'quench_time' => $data['quench_time'],
                'total_time' => $total_time,
                'batch_cost' => $batch_cost,
                'cost_pcs' => $cost_pcs,
                'cost_kg' => $cost_kg,
                'created_by' =>  $this->session->userdata('loginId')
            ]; 
                $this->externalHeatTreatment->saveBatchQtyDetails($batchData);

            $tbodyData="";$i=1; 
            if(!empty($htData)):
                $i=1;
                    $tbodyData.= '<tr>
                                <td class="text-center">'.$i++.'</td>
                                <td class="text-center">'.$htData->cp_per.'</td>
                                <td class="text-center">'.$htData->ct_ht.'</td>
                                <td class="text-center">'.$htData->uniformity.'</td>
                                <td class="text-center">'.$htData->carbur_time.'</td>
                                <td class="text-center">'.$htData->carbur_temp.'</td>
                                <td class="text-center">'.$htData->diffusion_time.'</td>
                                <td class="text-center">'.$htData->quench_temp.'</td>
                                <td class="text-center">'.$htData->quench_time.'</td>
                                <td class="text-center">'.$htData->total_time.'</td>
                                <td class="text-center">'.$htData->batch_cost.'</td>
                                <td class="text-center">'.$htData->cost_pcs.'</td>
                                <td class="text-center">'.$htData->cost_kg.'</td>
                            </tr>';
            else:
                $tbodyData.= '<tr><td colspan="13" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }

    public function saveOtherCast(){
        $data = $this->input->post();
		$errorMessage = array();
       
        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $htData = $this->externalHeatTreatment->getHeatTreatment($data['id']);

            $total_cost = round(($htData->cost_pcs + $data['glass_cost'] + $data['wire_cost'] + $data['separator_cost']),2);

            $batchData = [
                'id'=>$data['id'],
                'item_id' => $data['item_id'],
                'glass_qty' => $data['glass_qty'],
                'glass_cost' => $data['glass_cost'],
                'wire_qty' => $data['wire_qty'],
                'wire_cost' => $data['wire_cost'],
                'separator_qty' => $data['separator_qty'],
                'separator_cost' => $data['separator_cost'],
                'total_cost' => $total_cost,
                'created_by' =>  $this->session->userdata('loginId')
            ]; 
                $this->externalHeatTreatment->saveBatchQtyDetails($batchData);

            $tbodyData="";$i=1; 
            if(!empty($htData)):
                $i=1;
                    $tbodyData.= '<tr>
                                <td class="text-center">'.$i++.'</td>
                                <td class="text-center">'.$htData->glass_qty.'</td>
                                <td class="text-center">'.$htData->glass_cost.'</td>
                                <td class="text-center">'.$htData->wire_qty.'</td>
                                <td class="text-center">'.$htData->wire_cost.'</td>
                                <td class="text-center">'.$htData->separator_qty.'</td>
                                <td class="text-center">'.$htData->separator_cost.'</td>
                                <td class="text-center">'.$htData->total_cost.'</td>
                            </tr>';
            else:
                $tbodyData.= '<tr><td colspan="8" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }
    
    public function approveExternalHT()
    {
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->externalHeatTreatment->getExternalHtapproval($id);
        $this->data['ext_ht_id'] = $id;
        $this->load->view($this->approveForm, $this->data);
    }

    public function saveApproveExternalHT()
    {
        $data = $this->input->post();

        $errorMessage = array();
        if (empty($data['approve_date']))
            $errorMessage['approve_date'] = "Approved Date is required.";
        if (empty($data['approve_by']))
            $errorMessage['approve_by'] = "Approved By is required.";
        if (empty($data['remark']))
            $errorMessage['remark'] = "Remark is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['approve_date'] = (!empty($data['approve_date'])) ? date('Y-m-d', strtotime($data['approve_date'])) : null;
            $this->printJson($this->externalHeatTreatment->saveApproveExternalHT($data));
        endif;
    }


    public function uploadBottomFile(){ 
        $data = $this->input->post();
        if(isset($_FILES['bottom_layer'])):
            if($_FILES['bottom_layer']['name'] != null || !empty($_FILES['bottom_layer']['name'])):
                $this->load->library('upload');
                $_FILES['userfile']['name']     = $_FILES['bottom_layer']['name'];
                $_FILES['userfile']['type']     = $_FILES['bottom_layer']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['bottom_layer']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['bottom_layer']['error'];
                $_FILES['userfile']['size']     = $_FILES['bottom_layer']['size'];
                
                $imagePath = realpath(APPPATH . '../assets/uploads/bottom_layer/');
				$config = ['file_name' => time()."_order_item_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

                $this->upload->initialize($config);
                if (!$this->upload->do_upload()):
                    $errorMessage['bottom_layer'] = $this->upload->display_errors();
                    $this->printJson(["status"=>0,"message"=>$errorMessage]);
                else:
                    $uploadData = $this->upload->data();
                    $data['bottom_layer'] = $uploadData['file_name'];
                endif;
            else:
                unset($data['bottom_layer']);
            endif;
        endif;
        $this->printJson($this->externalHeatTreatment->uploadBottomFile($data));
    }

    public function uploadBatchFile(){ 
        $data = $this->input->post();
        if(isset($_FILES['batch_no'])):
            if($_FILES['batch_no']['name'] != null || !empty($_FILES['batch_no']['name'])):
                $this->load->library('upload');
                $_FILES['userfile']['name']     = $_FILES['batch_no']['name'];
                $_FILES['userfile']['type']     = $_FILES['batch_no']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['batch_no']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['batch_no']['error'];
                $_FILES['userfile']['size']     = $_FILES['batch_no']['size'];
                
                $imagePath = realpath(APPPATH . '../assets/uploads/batch_no/');
				$config = ['file_name' => time()."_order_item_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

                $this->upload->initialize($config);
                if (!$this->upload->do_upload()):
                    $errorMessage['batch_no'] = $this->upload->display_errors();
                    $this->printJson(["status"=>0,"message"=>$errorMessage]);
                else:
                    $uploadData = $this->upload->data();
                    $data['batch_no'] = $uploadData['file_name'];
                endif;
            else:
                unset($data['batch_no']);
            endif;
        endif;
        $this->printJson($this->externalHeatTreatment->uploadBatchFile($data));
    }
}
?>