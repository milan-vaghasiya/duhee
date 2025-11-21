<?php
class Items extends MY_Controller
{
    private $indexPage = "item/index";
    private $itemForm = "item/form";
    private $itemStockUpdateForm = "item/stock_update";
    private $itemOpeningStockForm = "item/opening_update";
    private $requestForm = "purchase_request/purchase_request";
    private $item_detail_form ="item/item_detail_form";
    private $hsn_form ="item/hsn_form";
    private $specification_form ="item/specification_form";
    private $storage_form ="item/storage_form";
    private $technical_form ="item/technical_form";
    private $indexPacking = "packing_material/index";
    private $formPacking = "packing_material/form";
    
	private $automotiveArray = ["1"=>'Yes',"2"=>"No"];
    private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Items";
		$this->data['headData']->controller = "items";
	}
	
	public function index(){
        $this->data['headData']->pageUrl = "items/pitems";
        $this->data['item_type'] = 3;
        $this->data['tableHeader'] = getDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function rawMaterial(){
        $this->data['headData']->pageUrl = "items/rawMaterial";
        $this->data['item_type'] = 3;
        $this->data['tableHeader'] = getDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function consumable(){
        $this->data['headData']->pageUrl = "items/consumable";
        $this->data['item_type'] = 2;
        $this->data['tableHeader'] = getDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($item_type){  
        $result = $this->item->getDTRows($this->input->post(),$item_type);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            
            $itmStock = $this->store->getItemStock($row->id);
            $row->qty = 0;
            if(!empty($itmStock->qty)){ $row->qty = $itmStock->qty;}
            
            $sendData[] = getItemData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addItem($item_type){
        $this->data['item_type'] = $item_type;
        $this->data['unitData'] = $this->item->itemUnits();
		// $this->data['automotiveArray'] = $this->automotiveArray;
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['categoryList'] = $this->item->getCategoryList($item_type);
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['familyGroup'] = $this->item->getfamilyGroupList();
		// $this->data['itemClass'] = $this->item->getItemClass();
        $this->data['hsnData'] = $this->item->getHsnList();
        // $this->data['empData'] = $this->employee->getEmpList();
		// $this->data['docCheckList'] = explode(',',$this->item->getMasterOptions()->doc_check_list);
        $this->data['industryList'] = $this->masterDetail->getTypeforItem(1);
        $this->data['classList'] = $this->masterDetail->getTypeforItem(4);
        $this->data['materialGrade'] = $this->materialGrade->getMaterialGrades();
        //$this->load->view($this->itemForm,$this->data);
        $this->load->view($this->itemForm,$this->data);
    }
	
	public function getHsnData(){
		$hsnCode = $this->input->post('hsnCode');
        $result = $this->item->getHsnData($hsnCode);
		$this->printJson($result);
	}
    
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if($data['item_type'] == 3){
			if(empty($data['itmsize']) AND empty($data['itmshape']) AND empty($data['itmbartype']) AND empty($data['itmmaterialtype']))
				$errorMessage['item_name'] = "Item Name is required.";
		}else{
			if(empty($data['item_name']))
				$errorMessage['item_name'] = "Item Name is required.";
		}
        if(empty($data['unit_id']))
            $errorMessage['unit_id'] = "Unit is required.";
        if(empty($data['category_id']))
            $errorMessage['category_id'] = "Category is required.";
            
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if($data['item_type'] == 3):
				$data['item_name'] = $data['itmsize'].' ';
				$data['item_name'] .= $data['itmshape'].' ';
				$data['item_name'] .= $data['itmbartype'].' ';
				$data['item_name'] .= $data['itmmaterialtype'];
				$data['item_image'] =  $data['itmsize'] . '~@' . $data['itmshape'] . '~@' . $data['itmbartype'] . '~@' . $data['itmmaterialtype'];
				$data['full_name'] = $data['item_name'];
				$data['material_grade'] = $data['itmmaterialtype'];
				unset($data['itmsize'],$data['itmshape'],$data['itmbartype'],$data['itmmaterialtype']);
			else:
			    $data['full_name']='';
                if(!empty($data['item_code'])){$data['full_name'] = '['.$data['item_code'].'] ';}
                if(!empty($data['item_name'])){$data['full_name'] .= $data['item_name'];}
                if(!empty($data['part_no'])){$data['full_name'] .= $data['part_no'];}
                
                //if(!empty($data['item_code']) && !empty($data['item_name']) && !empty($data['part_no'])):
    				//$data['full_name'] .= $data['item_code'].'-'.$data['item_name'].'-'.$data['part_no'];
    			//endif;
				
			endif;
            	
// 			if($_FILES['item_image']['name'] != null || !empty($_FILES['item_image']['name'])):
//                 $this->load->library('upload');
// 				$_FILES['userfile']['name']     = $_FILES['item_image']['name'];
// 				$_FILES['userfile']['type']     = $_FILES['item_image']['type'];
// 				$_FILES['userfile']['tmp_name'] = $_FILES['item_image']['tmp_name'];
// 				$_FILES['userfile']['error']    = $_FILES['item_image']['error'];
// 				$_FILES['userfile']['size']     = $_FILES['item_image']['size'];
				
// 				$imagePath = realpath(APPPATH . '../assets/uploads/items/');
// 				$config = ['file_name' => time()."_order_item_".$_FILES['userfile']['name'],'allowed_types' => 'gif|jpg|png|jpeg|bmp','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

// 				$this->upload->initialize($config);
// 				if (!$this->upload->do_upload()):
// 					$errorMessage['item_image'] = $this->upload->display_errors();
// 					$this->printJson(["status"=>0,"message"=>$errorMessage]);
// 				else:
// 					$uploadData = $this->upload->data();
// 					$data['item_image'] = $uploadData['file_name'];
// 				endif;
// 			else:
// 				unset($data['item_image']);
// 			endif;
			unset($data['docSelect']);
// 			if(!empty($data['hsn_code'])):
// 			    $hsnData = $this->hsnModel->getHSNDetailByCode($data['hsn_code']);
// 				$data['gst_per'] = $hsnData->igst;
// 			endif;
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->item->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['unitData'] = $this->item->itemUnits();
		// $this->data['automotiveArray'] = $this->automotiveArray;
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['familyGroup'] = $this->item->getfamilyGroupList();
        // $this->data['itemClass'] = $this->item->getItemClass();
        $this->data['hsnData'] = $this->item->getHsnList();
        $this->data['dataRow'] = $this->item->getItem($id);
        // $this->data['empData'] = $this->employee->getEmpList();
        $this->data['categoryList'] = $this->item->getCategoryList($this->data['dataRow']->item_type);
		// $this->data['docCheckList'] = explode(',',$this->item->getMasterOptions()->doc_check_list);
        $this->data['industryList'] = $this->masterDetail->getTypeforItem(1);
        $this->data['classList'] = $this->masterDetail->getTypeforItem(4);
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['materialGrade'] = $this->materialGrade->getMaterialGrades();
        //$this->load->view($this->itemForm,$this->data);
        $this->load->view($this->itemForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->item->delete($id));
        endif;
    }

    public function addStockTrans(){
        $id = $this->input->post('id');
        $this->data['stockTransData'] = $this->item->getStockTrans($id);
        $this->load->view($this->itemStockUpdateForm,$this->data);
    }

    public function saveStockTrans(){
        $data = $this->input->post();
        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Date is required.";
        if(empty($data['qty']))
            $errorMessage['qty'] = "Quantity is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
		    $this->printJson(["status"=>1,"stockData"=>$this->item->saveStockTrans($data)]);
        endif;
	}

    public function deleteStockTrans(){
		$id = $this->input->post('id');
		$this->printJson($this->item->deleteStockTrans($id));
	}
	
	public function addOpeningStock(){
        $id = $this->input->post('id');
        $this->data['itemData'] = $this->item->getItemById($id);
        $this->data['supplierData'] = (!empty($this->data['itemData']->item_type)) ? $this->party->getSupplierList() : [];
        $this->data['openingStockData'] = $this->item->getItemOpeningTrans($id);
        $this->data['locationData'] = $this->stockTransac->getStoreLocationList(['store_type'=>'0,15','group_store_opt'=>1,'final_location'=>1])['storeGroupedArray']; 
        $this->load->view($this->itemOpeningStockForm,$this->data);
    }

    public function saveOpeningStock(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['location_id']))
            $errorMessage['location_id'] = "Store Location is required.";
        if(empty($data['qty']))
            $errorMessage['qty'] = "Qty. is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['ref_date'] = $this->startYearDate;
            $data['created_by'] = $this->session->userdata('loginId');
            //print_r($data);exit;
            
            $this->printJson($this->item->saveOpeningStock($data));
        endif;
    }

    public function deleteOpeningStockTrans(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->item->deleteOpeningStockTrans($id));
        endif;
    }
    
    public function getItemData(){
		$id = $this->input->post('itemId');
        $result = $this->item->getItem($id);
        $unitData = $this->item->itemUnit($result->unit_id);
        $result->unit_name = $unitData->unit_name;
        $result->description = $unitData->description;
		$this->printJson($result);
	}

    public function addRequestConsumable(){
        $this->data['itemData'] = $this->item->getItemLists(2);
        $this->data['fgNMcData'] = $this->item->getItemLists('1,5');
        $this->data['empData'] = $this->employee->getEmpList();
        $this->data['unitData'] = $this->item->itemUnits();     
        $this->data['planningType']=$this->purchaseRequest->getPurchasePlanningType();
        $this->load->view($this->requestForm,$this->data);
    }

    public function addRequestRm(){
        $this->data['itemData'] = $this->item->getItemLists(3);
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
    

    public function itemApproval(){
        $id = $this->input->post('id'); 
        $this->data['dataRow'] = $this->item->getItem($id);
        $this->load->view("item/approval_form",$this->data);
    }

    public function saveItemApproval(){
        $data = $this->input->post();

        $errorMessage = array();
        $data['approved_by'] = $this->loginId;
        if(empty($data['approved_date']))
            $errorMessage['approved_date'] = "Approved Date is required.";
        if(empty($data['approved_by']))
            $errorMessage['approved_by'] = "Approved By is required.";
        if(empty($data['approved_base']))
            $errorMessage['approved_base'] = "Approved Base is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['approved_date'] = (!empty($data['approved_date'])) ? date('Y-m-d', strtotime($data['approved_date'])) : null ;
            $this->printJson($this->item->saveItemApproval($data));
        endif;
    }
	
	/*** Get Data For Dynamic Select2 ***/	
    public function getDynamicItemList()
    {
		$postData = Array();
		$postData = $this->input->post();
		//$postData['item_type'] = 1;
		//$postData['category_id'] = 208;
		//print_r($postData);
		$htmlOptions = $this->item->getDynamicItemList($postData);
		//print_r($htmlOptions);
		$this->printJson($htmlOptions);
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
        //$data['size'] = (!empty($data['diameter'])?$data['diameter']:0).'X'.(!empty($data['length'])?$data['length']:0).'X'.(!empty($data['flute_length'])?$data['flute_length']:0);
        
        $size = Array();
        if(!empty($data['diameter'])){$size[] = $data['diameter'];}
        if(!empty($data['length'])){$size[] = $data['length'];}
        if(!empty($data['flute_length'])){$size[] = $data['flute_length'];}
        $data['size'] = (!empty($size)) ? implode('X',$size) : NULL;
        
        unset($data['diameter'],$data['length'],$data['flute_length']);
        $this->printJson($this->item->saveItemDetails($data));
    }


    public function packingMaterial(){
        $this->data['headData']->pageUrl = "items/packingMaterial";
        $this->data['item_type'] = 9;
        $this->data['tableHeader'] = getDispatchDtHeader('packingMaterial');
        $this->load->view($this->indexPacking,$this->data);
    }
    
    public function getPackingMaterialDTRows($item_type){ 
        $result = $this->item->getDTRows($this->input->post(),$item_type);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getPackingItemData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    public function addPackingMaterial($item_type){
        $this->data['item_type'] = $item_type;
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['categoryList'] = $this->item->getCategoryList($item_type);
        $this->data['hsnData'] = $this->item->getHsnList();
        $this->load->view($this->formPacking,$this->data);
    }

    public function savePackingMaterial(){
        $data = $this->input->post(); 
        $errorMessage = array();
        if(empty($data['unit_id']))
            $errorMessage['unit_id'] = "Unit is required.";
        if($data['make_brand'] == 'Polythin'){
            if(empty($data['max_tvalue_per']) OR empty($data['min_tqty_per']))
                $errorMessage['item_name'] = "Item Name is required.";
            if(empty($data['material_spec']))
                $errorMessage['material_spec'] = "Micron is required.";
        }elseif($data['make_brand'] == 'Box'){
            if(empty($data['max_tvalue_per']) OR empty($data['min_tqty_per']) OR empty($data['max_tqty_per']) OR empty($data['typeof_machine']))
                $errorMessage['item_name'] = "Item Name is required.";
        }else{
            if(empty($data['item_name']))
                $errorMessage['item_name'] = "Item Name is required.";
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if($data['make_brand'] == 'Polythin'):
				$data['full_name'] = trim($data['item_code']).' '.trim($data['category_name']).' ('.trim($data['max_tvalue_per']).'X'.trim($data['min_tqty_per']).') '.trim($data['material_spec']).' Mcr';
				$data['item_name'] = $data['full_name'];

            elseif($data['make_brand'] == 'Box'):
                $data['full_name'] = trim($data['item_code']).' '.trim($data['category_name']).' ('.trim($data['max_tvalue_per']).'X'.trim($data['min_tqty_per']).'X'.trim($data['max_tqty_per']).') '.trim($data['typeof_machine']).' PLY';
				$data['item_name'] = $data['full_name'];
            else:

                $fname = Array();
                if(!empty($data['item_code'])){$fname[] = trim($data['item_code']);}
                if(!empty($data['category_name'])){$fname[] = trim($data['category_name']);}
                if(!empty($data['item_name'])){$fname[] = trim($data['item_name']);}
                $data['full_name'] = (!empty($fname)) ? implode(' ',$fname) : '';
                $data['full_name'] = trim($data['item_code']).' '.trim($data['category_name']).' '.trim($data['item_name']);
				$data['item_name'] = $data['full_name'];

            endif;
            unset($data['category_name']);
            
			if(!empty($data['hsn_code'])):
			    $hsnData = $this->hsnModel->getHSNDetailByCode($data['hsn_code']);
				$data['gst_per'] = $hsnData->igst;
			endif;
            $data['created_by'] = $this->session->userdata('loginId');

            //print_r($data); exit;
            $this->printJson($this->item->save($data));
        endif;
    }

    public function editPackingMaterial(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->item->getItem($id);
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['categoryList'] = $this->item->getCategoryList(9);
        $this->data['hsnData'] = $this->item->getHsnList();
        $this->load->view($this->formPacking,$this->data);
    }
}
?>