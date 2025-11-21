<?php
class CapitalGoods extends MY_Controller
{
    private $indexPage = "capital_goods/index";
    private $itemForm = "capital_goods/form";
    private $itemOpeningStockForm = "capital_goods/opening_update";
    private $requestForm = "purchase_request/purchase_request";
    
    private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Capital Goods";
		$this->data['headData']->controller = "capitalGoods";
		$this->data['headData']->pageUrl = "capitalGoods";
	}
	
	public function index(){
        $this->data['item_type'] = 4;
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){  

        $item_type = 4;
        $result = $this->item->getDTRows($this->input->post(),$item_type);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getCapitalGoods($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addCapitalGoods(){
        $item_type = 4;
        $this->data['item_type'] = $item_type;
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['categoryList'] = $this->item->getCategoryList($item_type);
        $this->data['empData'] = $this->employee->getEmpList();

        $this->load->view($this->itemForm,$this->data);
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
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->item->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['dataRow'] = $this->item->getItem($id);
        $this->data['categoryList'] = $this->item->getCategoryList($this->data['dataRow']->item_type);
        $this->data['empData'] = $this->employee->getEmpList();

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

	public function addOpeningStock(){
        $id = $this->input->post('id');
        $this->data['openingStockData'] = $this->item->getItemOpeningTrans($id);
        $this->data['locationData'] = $this->store->getStoreLocationList();
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

    public function addPurchaseRequest(){
        $this->data['itemData'] = $this->item->getItemLists(4);
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
}
?>