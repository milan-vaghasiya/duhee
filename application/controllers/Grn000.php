<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class Grn extends MY_Controller
{
	private $indexPage = "grn/index";
	private $grnForm = "grn/form";
    private $inspection = "grn/material_inspection";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Goods Receipt Note";
		$this->data['headData']->controller = "grn";
	}
	
	public function index(){
        $this->data['tableHeader'] = getDtHeader($this->data['headData']->controller);
		$this->load->view($this->indexPage,$this->data);
	}

    public function getDTRows(){
		$result = $this->grnModel->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;        
            $row->controller = "grn";    
            $sendData[] = getGRNData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	
	public function addGRN(){
		$this->data['nextGrnNo'] = $this->grnModel->nextGrnNo();
		$this->data['grn_prefix'] = 'GRN/'.$this->shortYear.'/';
			$this->data['itemData'] = $this->item->getItemLists("2,3");
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['partyData'] = $this->party->getPartyList();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['fgItemList'] = $this->item->getItemList(1);
		$this->data['colorList'] = $this->grnModel->itemColorCode();
		$this->load->view($this->grnForm,$this->data);
	}

	public function createGrn(){
		$data = $this->input->post();
		if($data['ref_id']):
			$orderItems = $this->purchaseOrder->getOrderItems($data['ref_id']);
			$orderData = new stdClass();
			$orderData->party_id = $data['party_id'];
			$orderData->id = implode(",",$data['ref_id']);
			$this->data['orderItems'] = $orderItems;
			$this->data['orderData'] = $orderData;
			$year = (date('m') > 3)?date('y').'-'.(date('y') +1):(date('y')-1).'-'.date('y');
			$this->data['nextGrnNo'] = $this->grnModel->nextGrnNo();
			$this->data['grn_prefix'] = 'GRN/'.$year.'/';
			$this->data['itemData'] = $this->item->getItemList();
			$this->data['unitData'] = $this->item->itemUnits();
			$this->data['partyData'] = $this->party->getPartyList();
			$this->data['locationData'] = $this->store->getStoreLocationList();
			$this->data['fgItemList'] = $this->item->getItemList(1);
			$this->load->view($this->grnForm,$this->data);
		else:
			return redirect(base_url('purchaseOrder'));
		endif;
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
		
		if(empty($data['grn_no']))
			$errorMessage['grn_no'] = "GRN No. is required.";
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
				'id' => $data['grn_id'],
				'type' => $data['type'],
				'order_id' => $data['order_id'],
				'grn_prefix' => $data['grn_prefix'], 
				'grn_no' => $data['grn_no'], 
				'grn_date' => date('Y-m-d',strtotime($data['grn_date'])),
				'party_id' => $data['party_id'], 
				'challan_no' => $data['challan_no'], 
				'remark' => $data['remark'],
                'created_by' => $this->session->userdata('loginId')
			];
							
			$itemData = [
				'id' => $data['trans_id'],
				'item_id' => $data['item_id'],
				'unit_id' => $data['unit_id'],
				'fgitem_id' => $data['fgitem_id'],
                'fgitem_name' => $data['fgitem_name'],
				'batch_no' => $data['batch_no'],
				'po_trans_id' => $data['po_trans_id'],
				'location_id' => $data['location_id'],
				'qty' => $data['qty'],
				'qty_kg' => $data['qty_kg'],
				'color_code' => $data['color_code'],
                'created_by' => $this->session->userdata('loginId')
			];
			$this->printJson($this->grnModel->save($masterData,$itemData));
		endif;
	}
	
	public function edit($id){
		if(empty($id)):
			return redirect(base_url('grn'));
		else:
			$this->data['grnData'] = $this->grnModel->editInv($id);
			$this->data['itemData'] = $this->item->getItemLists("2,3");
            $this->data['unitData'] = $this->item->itemUnits();
            $this->data['partyData'] = $this->party->getPartyList();
			$this->data['locationData'] = $this->store->getStoreLocationList();
			$this->data['fgItemList'] = $this->item->getItemList(1);
			$this->load->view($this->grnForm,$this->data);
		endif;
	}
	
	public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
		else:
			$this->printJson($this->grnModel->delete($id));
		endif;
	}

	public function materialInspection(){
		$this->data['tableHeader'] = getDtHeader('materialInspection');
		$this->load->view($this->inspection,$this->data);
	}

    public function purchaseMaterialInspectionList(){
		$columns =array('','','grn_master.grn_no','grn_master.grn_date','item_master.item_name','grn_transaction.qty');
        $result = $this->grnModel->purchaseMaterialInspectionList($this->input->post(),$columns);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;        
            $row->controller = "purchaseInvoice";    
            $sendData[] = getPurchaseMaterialInspectionData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function getInspectedMaterial(){
		$id = $this->input->post('id');
		$this->printJson($this->grnModel->getInspectedMaterial($id));
	}

	public function inspectedMaterialSave(){
		$data = $this->input->post();
		$errorMessage = array();
        //print_r($data);exit;
		$i=1;$total_qty = 0;
		foreach($data['item_id'] as $key=>$value):
			$total_qty = $data['inspected_qty'][$key] + $data['ud_qty'][$key] + $data['reject_qty'][$key] + $data['scrape_qty'][$key] + $data['short_qty'][$key];
			if($total_qty > $data['recived_qty'][$key]):
				$errorMessage['recived_qty'.$i] = "Received Qty. mismatched.";
			endif;
			$i++;
		endforeach;

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->session->userdata('loginId');
			$this->printJson($this->grnModel->inspectedMaterialSave($data));
		endif;
    }	

	public function getItemsForGRN(){
		$party_id = $this->input->post('party_id');		
		$this->printJson(["status" => 1,"itemOptions" => $this->grnModel->getItemsForGRN($party_id)]);
	}
	
	public function itemColorCode(){
		$this->printJson($this->grnModel->itemColorCode());
	}
	

}
?>