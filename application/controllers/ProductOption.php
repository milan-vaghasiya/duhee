<?php
class ProductOption extends MY_Controller
{
    private $indexPage = "product_options/index";
    private $cycletimeForm = "product_options/ct_form";
    private $consumptionForm = "product_options/tool_form";
    private $viewProductProcess = "product_options/view_product_process";
    private $productKitItem = "product_options/product_kit";
    private $inspectionForm = "product_options/inspection_form";
    private $prdOutputForm = "product_options/production_output_form";
    private $productProcessForm = "product_options/product_process";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "ProductOption";
		$this->data['headData']->controller = "productOption";
		$this->data['headData']->pageUrl = "productOption";
	}

    public function index(){
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){        
        $result = $this->item->getDTRows($this->input->post(),1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
			$optionStatus = $this->item->checkProductOptionStatus($row->id);
			$row->bom = (!empty($optionStatus->bom)) ? '<i class="fa fa-check text-primary"></i>' : '';
			$row->process = (!empty($optionStatus->process)) ? '<i class="fa fa-check text-primary"></i>' : '';
			$row->cycleTime = (!empty($optionStatus->cycleTime)) ? '<i class="fa fa-check text-primary"></i>' : '';
			$row->tool = (!empty($optionStatus->tool)) ? '<i class="fa fa-check text-primary"></i>' : '';
            $sendData[] = getProductOptionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addCycleTime(){
        $id = $this->input->post('id'); 
        $this->data['processData'] = $this->item->getItemProcess($id);   
        $this->load->view($this->cycletimeForm,$this->data);
    }

    public function saveCT(){
        $data = $this->input->post();
        $errorMessage = array();

        $cycleTimeData = [ 
            'id' => $data['id'], 
            'process_time' => $data['process_time'], 
            'load_unload_time' => $data['load_unload_time'],
            'finished_weight' => $data['finished_weight']
        ];

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->item->saveProductProcessCycleTime($cycleTimeData));
        endif;
    }

    public function addToolConsumption(){
        $id = $this->input->post('id'); 
        $this->data['consumableData'] = $this->item->getItemLists("2");
        $this->data['toolConsumptionData'] = $this->item->getToolConsumption($id);
        $this->data['operationData'] = $this->item->getProductOperation($id);
        $this->data['processData'] = $this->process->getProcessList();
        $this->data['item_id'] = $id;
        $this->load->view($this->consumptionForm,$this->data);
    }

    public function saveToolConsumption(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['item_id'])){$errorMessage['item_id'] = "Item Name is required.";}
        

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $toolConsumptionData = Array();
            if(isset($data['id'])):
                $toolConsumptionData = [
                    'id' => $data['id'],
                    'item_id' => $data['item_id'],
                    'ref_item_id' => $data['ref_item_id'],
                    'tool_life' => $data['tool_life'],
                    'operation' => $data['operation_id'],
                    'process_id' => $data['process_id']
                ];
            else:
                $toolConsumptionData = ['item_id' => $data['item_id']];
            endif;
            $this->printJson($this->item->saveToolConsumption($toolConsumptionData));
        endif;
    }  

    //Updated By NYN 05/10/2022
    public function addProductProcess(){
        $id = $this->input->post('id');     
        $this->data['item_id'] = $id;
        $this->data['processData'] = $this->process->getProcessList();
        $this->data['prodProcessData'] = $this->item->getItemProcess($id); 
        $this->data['prodProcessTbody'] = $this->prodWiseProcess(['item_id'=>$id]);
        $this->load->view($this->productProcessForm,$this->data);
    }

    //Created By NYN 05/10/2022
    public function getItemWisePfc(){
        $data = $this->input->post(); $opt='';
        $prodProcessData = $this->item->getItemProcess($data['item_id']); 
        $pfcData = $this->controlPlan->getItemWisePfcData($data['item_id']); 
        $maxPfcNo = (!empty(array_column($prodProcessData, 'sequence')))?max(array_column($prodProcessData, 'sequence')):0;
        foreach($pfcData as $pfc):
            if($pfc->process_no > $maxPfcNo ){
                $opt .= '<option value="'.$pfc->id.'">['.$pfc->process_no.'] '.$pfc->parameter.'</option>';
            }
        endforeach;
        $this->printJson(['status'=>1,'options'=>$opt]);
    }

    //Created By NYN 05/10/2022
    public function saveProdProcess(){
        $data = $this->input->post();
       $errorMessage = array();
		if(empty($data['item_id'])){
			$errorMessage['item_id'] = "Item Name is required.";
		}
		if(empty($data['process_id'])){
			$errorMessage['process_id'] = "Process is required.";
		}
	
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
		    if(!empty($data['pfc_process'])){
		        $pfcProcess = $this->controlPlan->getPfcForProcess($data['pfc_process']);
                $data['sequence'] = max(array_column($pfcProcess, 'process_no'));
		    }
             
            $this->item->saveProdProcess($data);
            $this->printJson($this->prodWiseProcess(['item_id'=>$data['item_id']]));
        endif;
    }
    
    //Created By NYN 05/10/2022
    public function deleteProdProcess(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $pfcProcess = $this->item->deleteProdProcess($data['id']);
            
            $this->printJson($this->prodWiseProcess(['item_id'=>$data['item_id']]));
        endif;
    }

    //Created By NYN 05/10/2022
    public function prodWiseProcess($data){
        $processData = $this->item->getItemProcess($data['item_id']); 
        $i = 1; $html = "";
        if (!empty($processData)) :
            foreach ($processData as $row) : $p=1; $pfc_process='';
                $pfcTd ="";
                if(!empty($row->pfc_process)){

                    $pfcProcess = $this->controlPlan->getPfcForProcess($row->pfc_process);
                    foreach($pfcProcess as $pfc):
                        if($p==1){ $pfc_process.= '['.$pfc->process_no.'] '.$pfc->parameter; } else { $pfc_process.='<br>['.$pfc->process_no.'] '.$pfc->parameter; }$p++;
                    endforeach;
                }
                $pfcTd='<td class="text-center controlPlanEnable">' . $pfc_process . '</td>';
               
                $html.= '<tr>
                        <td class="text-center">' . $i++ . '</td>
                        <td>' . $row->process_name . '</td>
                        '. $pfcTd.'
                        <td class="text-center">
                            <a class="btn btn-outline-danger btn-sm permission-remove" href="javascript:void(0)" onclick="trashProdProcess('.$row->id.','.$row->item_id.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>
                        </td>
                    </tr>';
            endforeach;
        else :
            $html.= '<tr><td colspan="4" class="text-center">No Data Found.</td></tr>';
        endif;

        $pOption="<option value=''>Select Production Process</option>";
        $proProcessData = $this->process->getProcessList();
        foreach ($proProcessData as $row) :
            if(!in_array($row->id, array_column($processData, 'process_id'))){
                $pOption.= '<option value="' . $row->id . '">' . $row->process_name . '</option>';
            }
        endforeach;
        $pfcOption='';
       
            $pfcData = $this->controlPlan->getItemWisePfcData($data['item_id']);
            $maxPfcNo = (!empty(array_column($processData, 'sequence')))?max(array_column($processData, 'sequence')):0;  
            if(!empty($pfcData)){
                foreach($pfcData as $pfc):
                    if($pfc->process_no > $maxPfcNo ){
                        $pfcOption .= '<option value="'.$pfc->id.'">['.$pfc->process_no.'] '.$pfc->parameter.'</option>';
                    }
                endforeach;
            }

        return ['status'=>1,"resultHtml"=>$html,"pOption"=>$pOption,"pfcOption"=>$pfcOption];
    }

    public function saveProductProcess(){
        $data = $this->input->post();
        $errorMessage = "";

        if(empty($data['item_id']))
            $errorMessage .= "Somthing went wrong.";
        /* if(empty($data['process'][0]))
            $errorMessage .= " Pelase select product process."; */

        if(!empty($errorMessage)):
            $this->printJson(['status'=>2,'message'=>$errorMessage]);
        else:
            //$data['created_by'] = $this->session->userdata('loginId');
            $response = $this->item->saveProductProcess($data);
            $this->printJson($this->setProcessView($data['item_id']));
        endif;
    }

    public function setProcessView($id){
        $processData = $this->item->getItemProcess($id);
        // $operationData = $this->operation->getOperationList();
        $pfcData=$this->controlPlan->getItemWisePfc(['item_id'=>$id]);

        $processHtml = '';
        if (!empty($processData)) :
            $i = 1; $html = ""; $options=Array(); $opt='';
            foreach ($processData as $row) :
                $opt='';
                // $ops = $this->item->getProductOperationForSelect($row->id);
                // foreach($operationData as $operation):
                //     $selected = (!empty($ops) && (in_array($operation->id, explode(',',$ops)))) ? "selected" : "";
                //      $opt .= '<option value="'.$operation->id.'" data-id="'.$row->id.'" '.$selected.'>'.$operation->operation_name.'</option>';
                // endforeach;
                foreach($pfcData as $pfc):
                    $selected = (!empty($row->pfc_process) && (in_array($pfc->id, explode(',',$row->pfc_process)))) ? "selected" : "";
                     $opt .= '<option value="'.$pfc->id.'" data-id="'.$row->id.'" '.$selected.'>['.$pfc->process_no.'] '.$pfc->process_description.'</option>';
                endforeach;
                $options[$row->id] = $opt;
            endforeach;

            foreach ($processData as $row) :
                $processHtml .= '<tr id="'.$row->id.'">
                        <td class="text-center">'.$i++.'</td>
                        <td>'.$row->process_name.'</td>
                        <td class="text-center">'.$row->sequence.'</td>
                        <td><select name="operationSelect" id="operationSelect'.$row->id.'" data-input_id="pfc_process'.$row->id.'" class="form-control jp_multiselect operation_id" multiple="multiple">'.
                                $options[$row->id]
                            .'</select>
                            <input type="hidden" name="pfc_process" id="pfc_process'.$row->id.'" data-id="'.$row->id.'" value="'.$row->pfc_process.'" /></td>
                      </tr>';
            endforeach;
        else :
            $processHtml .= '<tr><td colspan="3" class="text-center">No Data Found.</td></tr>';
        endif;
        return ['status' => 1, "processHtml" => $processHtml];
    }

    public function viewProductProcess(){
        $id = $this->input->post('id');
        $this->data['processDataList'] = $this->process->getProcessList();
        $this->data['operationDataList'] = $this->operation->getOperationList();
        $this->data['productProcess'] = $this->item->getProductProcessForSelect($id);
        $this->data['processData'] = $this->item->getItemProcess($id); 
        $this->data['mTypeData'] = $this->machineType->getMachineTypeList();

		$this->data['productOperation']="";$options=Array();$opt='';
        $pfcData=$this->controlPlan->getItemWisePfc(['item_id'=>$id]);
		foreach ($this->data['processData'] as $row) :
			$opt='';
			// $ops = $this->item->getProductOperationForSelect($row->operation);
			foreach($pfcData as $pfc):
				$selected = (!empty($row->pfc_process) && (in_array($pfc->id, explode(',',$row->pfc_process)))) ? "selected" : "";
				 $opt .= '<option value="'.$pfc->id.'" data-id="'.$row->id.'" '.$selected.'>['.$pfc->process_no.'] '.$pfc->process_description.'</option>';
			endforeach;
			$options[$row->id] = $opt;
		endforeach;
		$this->data['productOperation'] = $options;

        // $this->data['machineType']="";$options=Array();$opt='';
		// foreach ($this->data['processData'] as $row) :
		// 	$opt='';
		// 	$ops = $this->item->getMachineTypeForSelect($row->id);
		// 	foreach($this->data['mTypeData'] as $mType):
		// 		$selected = (!empty($ops) && (in_array($mType->id, explode(',',$ops)))) ? "selected" : "";
		// 		 $opt .= '<option value="'.$mType->id.'" data-id="'.$row->id.'" '.$selected.'>'.$mType->typeof_machine.'</option>';
		// 	endforeach;
		// 	$options[$row->id] = $opt;
		// endforeach;
		// $this->data['machineType'] = $options;

        $this->data['item_id'] = $id;   
        $this->load->view($this->viewProductProcess,$this->data);
    }

    public function updateProductProcessSequance(){
        $data = $this->input->post();
		$errorMessage = array();		
		if(empty($data['id']))
			$errorMessage['id'] = "Item ID is required.";
		
		if(empty($errorMessage)):
			$this->printJson($this->item->updateProductProcessSequance($data));			
		endif;
    }

    public function addProductKitItems(){
        $id = $this->input->post('id');
        $this->data['productKitData'] = $this->item->getProductKitData($id);
        $this->data['rawMaterial'] = $this->item->getProductKitLists();
        $this->data['process'] = $this->item->getProductWiseProcessList($id);
        $this->load->view($this->productKitItem,$this->data);
    }

    public function saveProductKit(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['ref_item_id'][0])){$errorMessage['kit_item_id'] = "Item Name is required.";}
		if(empty($data['qty'][0])){$errorMessage['kit_item_qty'] = "Qty. is required.";}
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->item->saveProductKit($data));
		endif;
    }
	
    public function saveProductOperation(){
        $data = $this->input->post();
        $this->printJson($this->item->saveProductOperation($data));
    }
    
    public function getItemData(){
		$id = $this->input->post('itemId');
        $result = $this->item->getItem($id);
        $unitData = $this->item->itemUnit($result->unit_id);
        $result->unit_name = $unitData->unit_name;
        $result->description = $unitData->description;
		$this->printJson($result);
	}

    /* Pre Inspection */
    public function getPreInspection(){
        $item_id=$this->input->post('id');
        $this->data['param_type']=$this->input->post('param_type');
        $this->data['paramData']=$this->item->getPreInspectionParam($item_id,$this->input->post('param_type'));
        $this->data['item_id']=$item_id;
        $this->load->view($this->inspectionForm,$this->data);
    }
     
    public function savePreInspectionParam(){
        $data = $this->input->post();
		$errorMessage = array();
        if(empty($data['parameter']))
            $errorMessage['parameter'] = "Parameter is required.";
        if(empty($data['specification']))
			$errorMessage['specification'] = "Specification is required.";
        if(empty($data['lower_limit']))
			$errorMessage['lower_limit'] = "Lower Limit is required.";
        if(empty($data['upper_limit']))
			$errorMessage['upper_limit'] = "Upper Limit is required.";
        if(empty($data['measure_tech']))
			$errorMessage['measure_tech'] = "Measure. Tech. is required.";
        if($this->item->checkDuplicateParam($data['parameter'],$data['id']) > 0)
            $errorMessage['parameter'] =  "Perameter is duplicate.";

        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->item->savePreInspectionParam($data);
            $paramData = $this->item->getPreInspectionParam($data['item_id'],$data['param_type']);
            $tbodyData="";$i=1; 
            if(!empty($paramData)):
                $i=1;
                foreach($paramData as $row):
                    $tbodyData.= '<tr>
                                <td>'.$i++.'</td>
                                <td>'.$row->parameter.'</td>
                                <td>'.$row->specification.'</td>
                                <td>'.$row->lower_limit.'</td>
                                <td>'.$row->upper_limit.'</td>
                                <td>'.$row->measure_tech.'</td>
                                <td class="text-center">
                                    <button type="button" onclick="trashPreInspection('.$row->id.','.$row->item_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                </td>
                            </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }

    public function deletePreInspection(){
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->item->deletePreInspection($data['id']);
            $paramData = $this->item->getPreInspectionParam($data['item_id'],$data['param_type']);
            $tbodyData="";$i=1; 
            if(!empty($paramData)):
                $i=1;
                foreach($paramData as $row):
                    $tbodyData.= '<tr>
                                <td>'.$i++.'</td>
                                <td>'.$row->parameter.'</td>
                                <td>'.$row->specification.'</td>
                                <td>'.$row->lower_limit.'</td>
                                <td>'.$row->upper_limit.'</td>
                                <td>'.$row->measure_tech.'</td>
                                <td class="text-center">
                                    <button type="button" onclick="trashPreInspection('.$row->id.','.$row->item_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                </td>
                            </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }
    
    public function addProductionOutput(){
        $id = $this->input->post('id'); 
        $this->data['itemList'] = $this->item->getItemLists();
        $this->data['productKitData'] = $this->item->getProductOutputData($id);
        $this->data['item_id'] = $id;
        $this->load->view($this->prdOutputForm,$this->data);
    }

    public function saveProductionOutput(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['output_item_id'][0])){$errorMessage['output_item_id'] = "Item Name is required.";}
		if(empty($data['qty'][0])){$errorMessage['op_qty'] = "Qty. is required.";}
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->item->saveProductionOutput($data));
		endif;
    }
}
?>