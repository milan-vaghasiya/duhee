<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class Gir extends MY_Controller
{
	private $indexPage = "gir/index";
	private $girForm = "gir/form";
    private $material_inspection = "gir/material_inspection";
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Goods Inward Register";
		$this->data['headData']->controller = "gir";
	}
	
	public function index(){
		$this->data['headData']->pageUrl = "gir";
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
		$this->load->view($this->indexPage,$this->data);
	}

    public function getDTRows($status=0){
		$data=$this->input->post();$data['status'] = $status;
		$result = $this->girModel->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;        
            $row->controller = "gir";    
            $sendData[] = getGIRData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	
	public function addGIR(){
		$this->data['nextGirNo'] = $this->girModel->nextGirNo();
		$this->data['gir_prefix'] = 'GIR/'.$this->shortYear.'/';
		$this->data['itemData'] = $this->item->getItemLists("2,3");
		//$this->data['docCheckList'] = explode(',',$this->girModel->getMasterOptions()->doc_check_list);
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['partyData'] = $this->party->getPartyList("2,3");
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['fgItemList'] = $this->item->getItemList(1);		
		$this->data['colorList'] = explode(',',$this->girModel->getMasterOptions()->color_code);
		$this->load->view($this->girForm,$this->data);
	}
	
	public function createGir(){
		$data = $this->input->post();
		if($data['ref_id']):
			$orderItems = $this->purchaseOrder->getOrderItems($data['ref_id']);
			$orderData = new stdClass();
			$orderData->party_id = $data['party_id'];
			$orderData->id = implode(",",$data['ref_id']);
			$this->data['orderItems'] = $orderItems;
			$this->data['orderData'] = $orderData;
			$year = (date('m') > 3)?date('y').'-'.(date('y') +1):(date('y')-1).'-'.date('y');
			$this->data['nextGirNo'] = $this->girModel->nextGirNo();
			$this->data['gir_prefix'] = 'GIR/'.$year.'/';
			$this->data['itemData'] = $this->item->getItemList();
			$this->data['unitData'] = $this->item->itemUnits();
			$this->data['partyData'] = $this->party->getPartyList("2,3");
			$this->data['locationData'] = $this->store->getStoreLocationList();
			$this->data['fgItemList'] = $this->item->getItemList(1);
			$this->load->view($this->girForm,$this->data);
		else:
			return redirect(base_url('gir'));
		endif;
	}

	public function getPartyOrders(){
		$party_id = $this->input->post('party_id');
		$order_id = $this->input->post('order_id');
		$orderList = $this->purchaseOrder->getPartyOrders($party_id,$order_id)['result'];

		$options = '';//'<option value="">Select Order</option>';
		$order_ids = (!empty($order_id))?explode(",",$order_id):array();
		
		foreach($orderList as $row):
			$selected = (!empty($order_id) && in_array($row->id,$order_ids))?"selected":"";
			$options .= '<option value="'.$row->id.'" '.$selected.'>'.getPrefixNumber($row->po_prefix,$row->po_no).' [ '.formatDate($row->po_date).' ]</option>';
		endforeach;

		$this->printJson(['status'=>1,'options'=>$options]);
	}

	public function getOrderItems(){
		$po_id = $this->input->post('po_id');
		$edit_mode = $this->input->post('edit_mode');
		$orderItems = $this->purchaseOrder->getOrderItems($po_id,$edit_mode);

		$options = "<option value=''>Select Item Name</option>";
		foreach($orderItems as $row):
			$row->order_qty = round(($row->qty - $row->rec_qty),2);
			$row->po_trans_id = $row->id;
			$row->po_id = $row->order_id;
			unset($row->id);
			$options .= "<option value='".$row->item_id."' data-row='".json_encode($row)."'>[".$row->item_code."] ".$row->item_name."</option>";
		endforeach;
		$this->printJson(['status'=>1,'options'=>$options]);
	}
	
	public function getItemData(){
		$id = $this->input->post('itemId');
        $result = $this->item->getItem($id);
        $result->unit_name = $this->item->itemUnit($result->unit_id)->unit_name;
		$this->printJson($result);
    }
    
	public function save(){
		$data = $this->input->post();
		$errorMessage = array();
		
		if(empty($data['gir_no']))
			$errorMessage['gir_no'] = "GIR No. is required.";
		if(empty($data['party_id']))
			$errorMessage['party_id'] = "Supplier Name is required.";
		if(empty($data['item_id'][0]))
			$errorMessage['general_error'] = "Item is required.";

		if(!empty($data['item_id'])):
			foreach($data['location_id'] as $key=>$value):
				if(empty($value)):
					$errorMessage['general_error'] = "Location is required.";
					break;
				endif;
			endforeach;
		endif;
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$masterData = [ 
				'id' => $data['gir_id'],
				'type' => $data['type'],
				'order_id' => $data['order_id'],
				'gir_prefix' => $data['gir_prefix'], 
				'gir_no' => $data['gir_no'], 
				'gir_date' => date('Y-m-d',strtotime($data['gir_date'])),
				'party_id' => $data['party_id'], 
				'challan_no' => $data['challan_no'], 
				'remark' => $data['remark'],
                'created_by' => $this->session->userdata('loginId')
			];
							
			$itemData = [
				'id' => $data['trans_id'],
				'po_trans_id' => $data['po_trans_id'],
				'po_id' => $data['po_id'],
				'item_id' => $data['item_id'],
				'item_code' => $data['item_code'],
				'item_type' => $data['item_type'],
				'batch_stock' => $data['batch_stock'],
				'unit_id' => $data['unit_id'],
				'inward_qty' => $data['inward_qty'],
                'order_qty' => $data['order_qty'],				
				'qty' => $data['qty'],				
				'qty_kg' => $data['qty_kg'],
				'batch_no' => $data['batch_no'],				
				'location_id' => $data['location_id'],
				'heat_no' => $data['heat_no'],
				'forging_tracebility' => $data['forging_tracebility'],
				'heat_tracebility' => $data['heat_tracebility'],
				'serial_no' => $data['serial_no'],
				'price' => $data['price'],
                'created_by' => $this->session->userdata('loginId')
			];
			$this->printJson($this->girModel->save($masterData,$itemData));
		endif;
	}
	
	public function edit($id){
		if(empty($id)):
			return redirect(base_url('gir'));
		else:
			$this->data['girData'] = $this->girModel->editInv($id); 
			$this->data['itemData'] = $this->item->getItemLists("2,3");
            $this->data['unitData'] = $this->item->itemUnits();
            $this->data['partyData'] = $this->party->getPartyList("2,3");
			$this->data['locationData'] = $this->store->getStoreLocationList();
			$this->data['fgItemList'] = $this->item->getItemList(1);
			$this->data['colorList'] = explode(',',$this->girModel->getMasterOptions()->color_code);
			$this->load->view($this->girForm,$this->data);
		endif;
	}
	
	public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
		else:
			$this->printJson($this->girModel->delete($id));
		endif;
	}

	public function materialInspections(){
		$this->data['headData']->pageUrl = "gir/materialInspection";
		$this->data['tableHeader'] = getQualityDtHeader('materialInspection');
		$this->load->view($this->material_inspection,$this->data);
	}

    public function purchaseMaterialInspectionList(){
		$columns =array('','','grn_master.gir_no','grn_master.gir_date','item_master.item_name','grn_transaction.qty');
        $result = $this->girModel->purchaseMaterialInspectionList($this->input->post(),$columns);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;        
			$row->inspection_status="";
			if($row->inspected_qty == "0.000"):
				$row->inspection_status = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
			else:
				$row->inspection_status = '<span class="badge badge-pill badge-success m-1">Complete</span>';
			endif;
            $row->controller = "purchaseInvoice";    
            $sendData[] = getPurchaseMaterialInspectionData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function getInspectedMaterial(){
		$id = $this->input->post('id');
		$this->printJson($this->girModel->getInspectedMaterial($id));
	}

	public function inspectedMaterialSave(){
		$data = $this->input->post();
		$errorMessage = array();
		$i=1;$total_qty = 0;
		// foreach($data['item_id'] as $key=>$value):
		// 	$inspected_qty = ($data['inspection_status'][$key] == "Ok")?($data['recived_qty'][$key] - $data['short_qty'][$key]):0;
		// 	$data['reject_qty'][$key] = ($data['inspection_status'][$key] != "Ok")?$data['recived_qty'][$key]:0;
		// 	$data['short_qty'][$key] = ($data['inspection_status'][$key] == "Ok")?$data['short_qty'][$key]:0;

		// 	$total_qty = $inspected_qty + $data['ud_qty'][$key] + $data['reject_qty'][$key] + $data['scrape_qty'][$key];			
		// 	if($total_qty > $data['recived_qty'][$key]):
		// 		$errorMessage['recived_qty'.$i] = "Received Qty. mismatched.";
		// 	endif;
		// 	$i++;
		// endforeach;

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->session->userdata('loginId');
			$this->printJson($this->girModel->inspectedMaterialSave($data));
		endif;
    }	

	public function getItemsForGIR(){
		$party_id = $this->input->post('party_id');		
		$this->printJson(["status" => 1,"itemOptions" => $this->girModel->getItemsForGIR($party_id)]);
	}
	
	public function itemColorCode(){
		$this->printJson($this->girModel->itemColorCode());
	}
	
	public function setFGItems(){
		$fgitem_id = $this->input->post('fgitem_id');	
		$fgItemList = $this->item->getItemList(1);		
		$fgOpt = '';
		if(!empty($fgItemList) ):
			foreach($fgItemList as $row):
				$selected = '';
				if(!empty($fgitem_id)){if (in_array($row->id,explode(',',$fgitem_id))) {$selected = "selected";}}
				$fgOpt .= '<option value="'.$row->id.'" '.$selected.'>'.$row->item_code.'</option>';
			endforeach;
		endif;
		$this->printJson(['status'=>1,'fgOpt'=>$fgOpt]);
	}
	
	public function getItemListForSelect(){
		$item_id = $this->input->post('item_id');
		$data = $this->input->post();
        $result = $this->item->getItem($item_id); //print_r($result);exit;
		$options="";
		if(!empty($result)): 
			$options .= '<option value="">Select Doc. Check List</option>';
			$checkData = explode(',',$result->doc_check_list);
			foreach($checkData as $key=>$value):
				$selected = '';
				if(!empty($data['doc_check_list']))
				{
					if (in_array($value,explode(',',$data['doc_check_list']))) {$selected = "selected";}
				}
				else
				{
					if(!empty($result->doc_check_list)){if (in_array($value,explode(',',$result->doc_check_list))) {$selected = "selected";}}

				}
				$options .= "<option value='". $value ."' ".$selected."> ".$value."</option>";
			endforeach;
		else:
			$options .= '<option value="">Select Doc. Check List</option>';
		endif;
		
		$this->printJson(['status'=>1, 'options'=>$options]);
	}

	public function getGirOrders(){ 
		$party = $this->input->post('party_id'); 
		$this->printJson($this->girModel->getGirOrders($party));
	}

	public function getBatchOrSerialNo(){
		$data = $this->input->post();
        $nextSerialNo = $this->girModel->getNextBatchOrSerialNo($data);
		$code = sprintf(n2y(date('Y'))."%03d",$nextSerialNo);
		$this->printJson(['status'=>1,'code'=>$code,'serial_no'=>$nextSerialNo]);
    }
}
?>