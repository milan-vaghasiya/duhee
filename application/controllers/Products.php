<?php
class Products extends MY_Controller
{
    private $indexPage = "product/index";
    private $productForm = "product/form";
    private $productProcessForm = "product/product_process";
    private $viewProductProcess = "product/view_product_process";
    private $productKitItem = "product/product_kit";
    private $requestForm = "purchase_request/purchase_request"; 
    private $item_detail_form ="product/item_detail_form";
    private $hsn_form ="product/hsn_form";
    private $specification_form ="product/specification_form";
    private $storage_form ="product/storage_form";
    private $technical_form ="product/technical_form";
    private $profileData ="product/product_profile";

    private $automotiveArray = ["1"=>'Yes',"2"=>"No"];
    private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Products";
		$this->data['headData']->controller = "products";
		$this->data['headData']->pageUrl = "products";
	}

    public function index(){
        $this->data['tableHeader'] = getDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){        
        $result = $this->item->getDTRows($this->input->post(),1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getProductData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addProduct($item_type=1,$active =1){
        $this->data['item_type'] = $item_type;
        $this->data['active'] = $active;
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['categoryList'] = $this->item->getCategoryList(1);
        $this->data['familyGroup'] = $this->item->getfamilyGroupList();
        $this->data['industryList'] = $this->masterDetail->getTypeforItem(1);
        $this->data['classList'] = $this->masterDetail->getTypeforItem(4);
        $this->data['hsnData'] = $this->item->getHsnList();
        $this->data['materialGrade'] = $this->materialGrade->getMaterialGrades();
        $this->load->view($this->productForm,$this->data);
    }
	
	public function getHsnData(){
		$hsnCode = $this->input->post('hsnCode');
        $result = $this->item->getHsnData($hsnCode);
		$this->printJson($result);
	}

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['item_name']))
             $errorMessage['item_name'] = "Item Name is required.";
        if(empty($data['unit_id']))
             $errorMessage['unit_id'] = "Unit is required.";
        if(empty($data['category_id']))
             $errorMessage['category_id'] = "Category is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:	
            if(!empty($data['item_code']) && !empty($data['item_name']) && !empty($data['part_no'])):
				$data['full_name'] = $data['item_code'].'-'.$data['item_name'].'-'.$data['part_no'];
			endif;	
			if($_FILES['item_image']['name'] != null || !empty($_FILES['item_image']['name'])):
                $this->load->library('upload');
				$_FILES['userfile']['name']     = $_FILES['item_image']['name'];
				$_FILES['userfile']['type']     = $_FILES['item_image']['type'];
				$_FILES['userfile']['tmp_name'] = $_FILES['item_image']['tmp_name'];
				$_FILES['userfile']['error']    = $_FILES['item_image']['error'];
				$_FILES['userfile']['size']     = $_FILES['item_image']['size'];
				
				$imagePath = realpath(APPPATH . '../assets/uploads/items/');
				$config = ['file_name' => time()."_order_item_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

				$this->upload->initialize($config);
				if (!$this->upload->do_upload()):
					$errorMessage['item_image'] = $this->upload->display_errors();
					$this->printJson(["status"=>0,"message"=>$errorMessage]);
				else:
					$uploadData = $this->upload->data();
					$data['item_image'] = $uploadData['file_name'];
				endif;
			else:
				unset($data['item_image']);
			endif;

            unset($data['processSelect']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->item->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['unitData'] = $this->item->itemUnits();
        // $this->data['customerList'] = $this->party->getCustomerList();
        // $this->data['automotiveArray'] = $this->automotiveArray;
        $this->data['dataRow'] = $this->item->getItem($id);
        // $this->data['gstPercentage'] = $this->gstPercentage;
        // $this->data['processData'] = $this->process->getProcessList();
        $this->data['categoryList'] = $this->item->getCategoryList(1);
        $this->data['familyGroup'] = $this->item->getfamilyGroupList();
		//$this->data['itemClass'] = $this->item->getItemClass();
        $this->data['hsnData'] = $this->item->getHsnList();
        // $this->data['productProcess'] = $this->item->getProductProcessForSelect($id);
        // $this->data['empData'] = $this->employee->getEmpList();
        $this->data['industryList'] = $this->masterDetail->getTypeforItem(1);
        $this->data['classList'] = $this->masterDetail->getTypeforItem(4);
        $this->data['materialGrade'] = $this->materialGrade->getMaterialGrades();
		// $this->data['materialGrades'] = explode(',', $this->item->getMasterOptions()->material_grade);
        $this->load->view($this->productForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->item->delete($id));
        endif;
    }

    public function addProductProcess(){
        $id = $this->input->post('id');        
        $this->data['processData'] = $this->process->getProcessList();
        $this->load->view($this->productProcessForm,$this->data);
    }

    public function saveProductProcess(){
        $data = $this->input->post(); //print_r($data); exit;
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

    public function setProcessView($id)
    {
        $processData = $this->item->getItemProcess($id);
        $operationData = $this->operation->getOperationList();
        $mTypeData = $this->machineType->getMachineTypeList();
        $processHtml = '';
        $pfcData=$this->controlPlan->getItemWisePfc(['item_id'=>$id]);
        if (!empty($processData)) :
            $i = 1; $html = ""; $options=Array(); $opt='';
            foreach ($processData as $row) :
                $opt='';
                foreach($pfcData as $pfc):
                    $selected = (!empty($row->pfc_process) && (in_array($pfc->id, explode(',',$row->pfc_process)))) ? "selected" : "";
                     $opt .= '<option value="'.$pfc->id.'" data-id="'.$row->id.'" '.$selected.'>['.$pfc->process_no.'] '.$pfc->process_description.'</option>';
                endforeach;
                $options[$row->id] = $opt;
            endforeach;
            $productOperation = $options;
            
            // $this->data['machineType']="";$options=Array();$opt='';
    		// foreach ($processData as $row) :
    		// 	$opt='';
    		// 	$ops = $this->item->getMachineTypeForSelect($row->id);
    		// 	foreach($mTypeData as $mType):
    		// 		$selected = (!empty($ops) && (in_array($mType->id, explode(',',$ops)))) ? "selected" : "";
    		// 		 $opt .= '<option value="'.$mType->id.'" data-id="'.$row->id.'" '.$selected.'>'.$mType->typeof_machine.'</option>';
    		// 	endforeach;
    		// 	$options[$row->id] = $opt;
    		// endforeach;
    		// $machineType = $options;

            foreach ($processData as $row) :
                $processHtml .= '<tr id="'.$row->id.'">
                        <td class="text-center">'.$i++.'</td>
                        <td>'.$row->process_name.'</td>
                        <td class="text-center">'.$row->sequence.'</td>
                        <td><select name="operationSelect" id="operationSelect'.$row->id.'" data-input_id="operation_id'.$row->id.'" class="form-control jp_multiselect operation_id" multiple="multiple">'.
                                $productOperation[$row->id]
                            .'</select>
                            <input type="hidden" name="operation_id" id="operation_id'.$row->id.'" data-id="'.$row->id.'" value="'.$row->pfc_process.'" />
                            <input type="hidden" class="form-control" name="noof_operation" id="noof_operation'.$row->id.'" data-id="'.$row->id.'" value="'.$row->noof_operation.'" />
                            <input type="hidden" name="typeof_machine" id="typeof_machine'.$row->id.'" data-id="'.$row->id.'" value="'.$row->typeof_machine.'" />
                        </td>
                        
                </tr>';
            endforeach;
        else :
            $processHtml .= '<tr><td colspan="4" class="text-center">No Data Found.</td></tr>';
        endif;
        return ['status' => 1, "processHtml" => $processHtml];
    }

    public function viewProductProcess(){
        $id = $this->input->post('id');
        $this->data['processDataList'] = $this->process->getProcessList();
        $this->data['operationDataList'] = $this->operation->getOperationList();
        $this->data['productProcess'] = $this->item->getProductProcessForSelect($id);
        $this->data['processData'] = $this->item->getItemProcess($id); 

		$this->data['productOperation']="";$options=Array();$opt='';
		foreach ($this->data['processData'] as $row) :
			$opt='';
			$ops = $this->item->getProductOperationForSelect($row->id);
			foreach($this->data['operationDataList'] as $operation):
				$selected = (!empty($ops) && (in_array($operation->id, explode(',',$ops)))) ? "selected" : "";
				 $opt .= '<option value="'.$operation->id.'" data-id="'.$row->id.'" '.$selected.'>'.$operation->operation_name.'</option>';
			endforeach;
			$options[$row->id] = $opt;
		endforeach;
		$this->data['productOperation'] = $options;
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
        $this->data['rawMaterial'] = $this->item->getItemLists("3");
        $this->data['process'] = $this->item->getProductWiseProcessList($id);
        $this->load->view($this->productKitItem,$this->data);
    }

    public function saveProductKit(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['ref_item_id'][0])){
			$errorMessage['kit_item_id'] = "Item Name is required.";
		}
		if(empty($data['qty'][0])){
			$errorMessage['kit_item_qty'] = "Qty. is required.";
		}
		
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

    public function addPurchaseRequest(){
        $this->data['itemData'] = $this->item->getItemLists(1);
        $this->data['fgNMcData'] = $this->item->getItemLists('1,5');
        $this->data['empData'] = $this->employee->getEmpList();
        $this->data['unitData'] = $this->item->itemUnits();     
        $this->data['planningType']=$this->purchaseRequest->getPurchasePlanningType();
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

    public function addHSNDetail(){
        $id = $this->input->post('id');
        $this->data['hsnData'] = $this->item->getHsnList();
        $this->data['dataRow'] = $this->item->getItem($id); 
        $this->load->view($this->hsn_form,$this->data);
    }

    public function addItemSpecification(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->item->getItem($id); 
        $this->data['unitData'] = $this->item->itemUnits();
        $this->load->view($this->specification_form,$this->data);
    }

    public function addStorageDetail(){

        $id = $this->input->post('id');
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['dataRow'] = $this->item->getItem($id); 
        $this->load->view($this->storage_form,$this->data);
    }

    public function addTechnicalDetail(){
        $id = $this->input->post('id');
        $this->data['materialGrades']=$this->materialGrade->getMaterialGrades();
        $this->data['dataRow'] = $this->item->getItem($id); 
        $this->load->view($this->technical_form,$this->data);
    }

    public function saveItemDetails(){
        $data = $this->input->post(); 
        if(!empty($data['hsn_code'])):
            $hsnData = $this->hsnModel->getHSNDetailByCode($data['hsn_code']);
            $data['gst_per'] = $hsnData->igst;
        endif;
        $this->printJson($this->item->saveItemDetails($data));
    }
    
        /* Created By :- Avruti @06/11/2023 */
    public function getProductProfile($id){
      $this->data['item_id'] = $id;
        $this->data['productData'] = $this->item->getItem($id);
        $this->data['bomData'] = $this->item->getProductKitData($id); 
        $this->data['processData'] = $this->item->getItemProcess($id); 
        $this->data['processDocBody'] = $this->prodProcessTableData(['item_id'=>$id]);
        $this->load->view($this->profileData,$this->data);
    }

    public function saveProcessDocuments(){
        $data = $this->input->post();

        $errorMessage = array();
        if(empty($data['prd_drg_no'])){ 
            $errorMessage['prd_drg_no'] = "Drawing No is required."; }
        
        if(empty($_FILES['file_upload']['name'][0])  || $_FILES['file_upload']['name'][0] == null){ $errorMessage['file_upload'] = "File is required."; }
            
        $file_upload = array();
        if($_FILES['file_upload']['name'][0] != null || !empty($_FILES['file_upload']['name'][0])):
            foreach ($_FILES['file_upload']['tmp_name'] as $key => $value):
                $this->load->library('upload');
                $_FILES['userfile']['name']     = $_FILES['file_upload']['name'][$key];
                $_FILES['userfile']['type']     = $_FILES['file_upload']['type'][$key];
                $_FILES['userfile']['tmp_name'] = $_FILES['file_upload']['tmp_name'][$key];
                $_FILES['userfile']['error']    = $_FILES['file_upload']['error'][$key];
                $_FILES['userfile']['size']     = $_FILES['file_upload']['size'][$key];
                
                $imagePath = realpath(APPPATH . '../assets/uploads/prod_process_doc/');
                $config = ['file_name' => time()."_file_upload"."_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

                $this->upload->initialize($config);
                if (!$this->upload->do_upload()):
                    $errorMessage['file_upload'] = $this->upload->display_errors();
                    $this->printJson(["status"=>0,"message"=>$errorMessage]);
                else:
                    $uploadData = $this->upload->data();
                    $file_upload[] = $uploadData['file_name'];
                endif;
            endforeach;
        else:
            unset($data['file_upload']);
        endif; 
        if(!empty($file_upload)):
            $data['file_upload'] = implode(",~",$file_upload);
            endif; 

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->item->saveProcessDocuments($data);
            $this->printJson($this->prodProcessTableData(['item_id'=>$data['item_id']]));
        endif;
    }

    public function prodProcessTableData($data){
        $processData = $this->item->getProdProcessTableData(['item_id'=>$data['item_id']]);
        $tbodyData=""; $i=1; 
        if(!empty($processData)):
            $i=1; 
            foreach($processData as $row):
                $downBtn =''; 
                $process_name = (empty($row->process_id)? "Main Drawings" : $row->process_name);
				if(!empty($row->file_upload)):
					$fileData = explode(',~',$row->file_upload);
					foreach($fileData as $key=>$value):
						$downBtn .= '<a href="'.base_url('assets/uploads/prod_process_doc/'.$value).'" class="mr-2" target="_blank"><i class="fa fa-eye"></i></a>';
					endforeach;
				endif;
				
                $tbodyData.= '<tr class="text-center">';
                $tbodyData.= '<td>'.$i++.'</td>';
                $tbodyData.= '<td>'.$process_name.'</td>';
                $tbodyData.= '<td>'.$row->prd_drg_no.'</td>';
                $tbodyData.= '<td>'.$downBtn.'</td>';
                $tbodyData.= '<td>';
                $tbodyData.= '<button type="button" onclick="trashProcessDocuments('.$row->id.','.$row->item_id.');" class="btn btn-sm btn-outline-danger btn-delete" datatip="Delete"><i class="ti-trash"></i></button>';
                $tbodyData.= '</td>';
                $tbodyData.= '</tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
        endif;
        return ['status'=>1,"tbodyData"=>$tbodyData];
    }

    /* Created By :- Avruti @06/11/2023 */
    public function deleteProcessDocuments(){
        $data = $this->input->post();
        if (empty($data['id'] && $data['item_id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->item->deleteProcessDocuments($data);
            $this->printJson($this->prodProcessTableData(['item_id'=>$data['item_id']]));
        endif;
    }
}
