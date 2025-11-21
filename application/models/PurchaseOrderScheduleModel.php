<?php
class PurchaseOrderScheduleModel extends MasterModel{
    private $purchaseOrderMaster = "purchase_order_master";
    private $purchaseOrderTrans = "purchase_order_trans";
	private $purchaseEnquiryMaster = "purchase_enquiry";
    private $itemMaster = "item_master";
	
    public function nextPoNo(){
        $data['select'] = "MAX(po_no) as po_no";
        $data['tableName'] = $this->purchaseOrderMaster;
		$po_no = $this->specificRow($data)->po_no;
		$nextPoNo = (!empty($po_no))?($po_no + 1):1;
		return $nextPoNo;
    }

    public function getDTRows($data){
        $data['select'] = "purchase_order_trans.*,purchase_order_master.po_no,purchase_order_master.po_prefix,purchase_order_master.po_date,purchase_order_master.party_id,purchase_order_master.net_amount,party_master.party_name,item_master.item_name";
        $data['leftJoin']['purchase_order_master'] = "purchase_order_master.id = purchase_order_trans.order_id";
        $data['leftJoin']['party_master'] = "purchase_order_master.party_id = party_master.id";
        $data['leftJoin']['item_master'] = "item_master.id = purchase_order_trans.item_id";
        $data['where']['purchase_order_trans.order_status != '] = 2;
		$data['where']['purchase_order_trans.order_type'] = 3;
        $data['tableName'] = $this->purchaseOrderTrans;

        $data['searchCol'][] = "purchase_order_master.po_no";
        $data['searchCol'][] = "DATE_FORMAT(purchase_order_master.po_date, '%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "DATE_FORMAT(purchase_order_trans.delivery_date, '%d-%m-%Y')";

		$columns =array('','','purchase_order_master.po_no','purchase_order_master.po_date','party_master.party_name','item_master.item_name','','','','','purchase_order_trans.delivery_date');
		
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
		
    }
  
    public function getPurchaseOrder($id){
		$data['tableName'] = $this->purchaseOrderMaster;
		$data['select'] = "purchase_order_master.*,party_master.party_name,party_master.contact_person,party_master.contact_email, party_master.party_mobile,party_master.party_address,purchase_enquiry.enq_prefix,purchase_enquiry.enq_no,purchase_enquiry.enq_date";
		$data['leftJoin']['party_master'] = "purchase_order_master.party_id = party_master.id";
        $data['leftJoin']['purchase_enquiry'] = "purchase_enquiry.id = purchase_order_master.enq_id";
        $data['where']['purchase_order_master.id'] = $id;
        $result = $this->row($data);
		//print_r($this->db->last_query());
		$result->itemData = $this->getPurchaseOrderTransactions($id);
		
		//exit;
		return $result;
	}
	
	public function getPurchaseOrderTransactions($id){
        $data['tableName'] = $this->purchaseOrderTrans;
        $data['select'] = "purchase_order_trans.*,item_master.item_name,item_master.item_code,unit_master.unit_name";
        $data['join']['item_master'] = "item_master.id = purchase_order_trans.item_id";
        $data['join']['unit_master'] = "unit_master.id = purchase_order_trans.unit_id";
        $data['where']['purchase_order_trans.order_id'] = $id;
        return $this->rows($data);
    }  

	public function getOrderItems($orderIds){
		$data['tableName'] = $this->purchaseOrderTrans;
        $data['select'] = "purchase_order_trans.*,item_master.item_name,item_master.item_code,unit_master.unit_name";
        $data['join']['item_master'] = "item_master.id = purchase_order_trans.item_id";
        $data['join']['unit_master'] = "unit_master.id = purchase_order_trans.unit_id";
		$data['where']['purchase_order_trans.order_status'] = 0;
        $data['where_in']['purchase_order_trans.order_id'] = $orderIds;
        return $this->rows($data);
	}

    public function save($masterData,$itemData){
        $orderId = $masterData['id'];
		// $req_id = $masterData['req_id']; unset($masterData['req_id']);
		
		if($this->checkDuplicateOrder($masterData['party_id'],$masterData['po_no'],$masterData['order_type'],$orderId) > 0):
			$errorMessage['po_no'] = "PO. No. is duplicate.";
			return ['status'=>0,'message'=>$errorMessage];
		endif;

		if(empty($orderId)):			
			//save purchase master data
			$purchaseOrderSave = $this->store($this->purchaseOrderMaster,$masterData);
			$orderId = $purchaseOrderSave['insert_id'];		
			
			if(!empty($req_id)){ $this->store("purchase_request",["id"=>$req_id,"ref_id"=>2,"order_status"=>1]); }

			if(!empty($masterData['enq_id'])):
				$this->store($this->purchaseEnquiryMaster,['id'=>$masterData['enq_id'],'enq_status'=>1]);
			endif;

			$result = ['status'=>1,'message'=>'Purchase order saved successfully.','url'=>base_url("purchaseOrder")];			
		else:
			$this->store($this->purchaseOrderMaster,$masterData);
			
			$data['select'] = "id";
			$data['where']['order_id'] = $orderId;
			$data['tableName'] = $this->purchaseOrderTrans;
			$ptransIdArray = $this->rows($data);
			
			foreach($ptransIdArray as $key=>$value):
				if(!in_array($value->id,$itemData['id'])):		
					$this->trash($this->purchaseOrderTrans,['id'=>$value->id]);
				endif;
			endforeach;
			
			$result = ['status'=>1,'message'=>'Purchase Order updated successfully.','url'=>base_url("purchaseOrderSchedule")];
        
		endif;

		foreach($itemData['item_id'] as $key=>$value):
			$transData = [
							'id' => $itemData['id'][$key],
							'order_id' => $orderId,
							'order_type' =>$masterData['order_type'],
							'item_id' => $value,
							'unit_id' => $itemData['unit_id'][$key],
							'fgitem_id' => $itemData['fgitem_id'][$key],
							'fgitem_name' => $itemData['fgitem_name'][$key],
							'hsn_code' => $itemData['hsn_code'][$key],
							'delivery_date' => $itemData['delivery_date'][$key],
							'qty' => $itemData['qty'][$key],
							'price' => $itemData['price'][$key],
							'igst' => $itemData['igst'][$key],
							'sgst' => $itemData['sgst'][$key],
							'cgst' => $itemData['cgst'][$key],
							'igst_amt' => $itemData['igst_amt'][$key],
							'sgst_amt' => $itemData['sgst_amt'][$key],
							'cgst_amt' => $itemData['cgst_amt'][$key],
							'amount' => $itemData['amount'][$key],
							'remarks' => $itemData['remarks'][$key],
							'disc_per' => $itemData['disc_per'][$key],
							'disc_amt' => $itemData['disc_amt'][$key],
							'net_amount' => $itemData['net_amount'][$key],

                            
							'created_by' => $itemData['created_by']
						];
			$this->store($this->purchaseOrderTrans,$transData);
		endforeach;

		return $result;		
    }

    public function checkDuplicateOrder($partyId,$poNo,$order_type,$id = ""){
        $data['tableName'] = $this->purchaseOrderMaster;
        $data['where']['party_id'] = $partyId;
        $data['where']['po_no'] = $poNo;        
        $data['where']['order_type'] = $order_type;
		if(!empty($id))
            $data['where']['id != '] = $id;
		return $this->numRows($data);
    }
        
    public function deleteOrder($id){
		$orderData = $this->getPurchaseOrder($id);
        //order transation delete
		$where['order_id'] = $id;
		$this->trash($this->purchaseOrderTrans,$where);

		if(!empty($orderData->enq_id)):
			$this->store($this->purchaseEnquiryMaster,['id'=>$orderData->enq_id,'enq_status'=>0]);
		endif;
        
        //order master delete
		return $this->trash($this->purchaseOrderMaster,['id'=>$id],'Purchase Order');
    }

	public function getPartyOrders($id){
        $queryData['tableName'] = $this->purchaseOrderMaster;
        $queryData['select'] = "id,po_no,po_prefix,po_date";
        $queryData['where']['order_status'] = 0;
        $queryData['where']['party_id'] = $id;
        $resultData = $this->rows($queryData);
        
        $html="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):
                $html .= '<tr>
                            <td class="text-center">
                                <input type="checkbox" id="md_checkbox_'.$i.'" name="ref_id[]" class="filled-in chk-col-success" value="'.$row->id.'"  ><label for="md_checkbox_'.$i.'" class="mr-3"></label>
                            </td>
                            <td class="text-center">'.getPrefixNumber($row->po_prefix,$row->po_no).'</td>
                            <td class="text-center">'.formatDate($row->po_date).'</td>
                          </tr>';
                $i++;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="3">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }

	public function getFamilyItem($id){
        $data['tableName'] = $this->itemMaster;
        $data['where']['family_id'] = $id;
        return $this->rows($data);
    }

	public function getScheduleOrderByParty($id){
        $queryData['tableName'] = $this->purchaseOrderMaster;
        $queryData['select'] = "id,po_no,po_prefix,po_date,remark";
        $queryData['where']['order_status'] = 0;
		$queryData['where']['order_type'] = 2;
        $queryData['where']['party_id'] = $id;
        $resultData = $this->rows($queryData);
        
        $html="<option value=''>Select Purchse Order</option>";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):
                $html .= '<option value="'.$row->id.'">'.getPrefixNumber($row->po_prefix,$row->po_no).' ('.$row->remark.')</option>';
                $i++;
            endforeach;
        else:
            $html = '<option value="">No Data Found</option>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }
    public function getItemListByOrderId($order_id=0){
		$data['tableName'] = $this->purchaseOrderTrans;
	    $data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price,item_master.unit_id,item_master.qty,unit_master.unit_name";
        $data['leftJoin']['item_master']="item_master.id=purchase_order_trans.item_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['where']['purchase_order_trans.order_id'] = $order_id;
		return $this->rows($data);
	}

}
?>