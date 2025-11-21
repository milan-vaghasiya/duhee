<?php
class PurchaseOrderModel extends MasterModel{
    private $purchaseOrderMaster = "purchase_order_master";
    private $purchaseOrderTrans = "purchase_order_trans";
	private $purchaseEnquiryMaster = "purchase_enquiry";
    private $itemMaster = "item_master";
    private $grnMaster = "grn_master";
	private $grnTrans = "grn_transaction";

    public function nextPoNo($order_type){
        $data['tableName'] = $this->purchaseOrderMaster;
        $data['select'] = "MAX(po_no) as po_no";
        $data['where']['order_type'] = $order_type;
		$po_no = $this->specificRow($data)->po_no;
		
		$nextPoNo = (!empty($po_no))?($po_no + 1):1;
		return $nextPoNo;
    }

    public function getDTRows($data){
        $data['tableName'] = $this->purchaseOrderTrans;
        $data['select'] = "purchase_order_trans.*,purchase_order_master.po_no,purchase_order_master.po_prefix,purchase_order_master.po_date,purchase_order_master.party_id,purchase_order_master.net_amount,party_master.party_name,item_master.item_name,item_master.full_name";
        $data['join']['purchase_order_master'] = "purchase_order_master.id = purchase_order_trans.order_id";
        $data['leftJoin']['party_master'] = "purchase_order_master.party_id = party_master.id";
        $data['join']['item_master'] = "item_master.id = purchase_order_trans.item_id";
        //$data['where']['purchase_order_trans.order_status != '] = 2;
        if(isset($data['order_type'])){ $data['where']['purchase_order_master.order_type'] = $data['order_type'];}
    
        if(empty($data['status'])){
            $data['where']['purchase_order_trans.order_status'] = 0;
        }else{
            $data['where']['purchase_order_trans.order_status'] = 1;
        }

        $data['searchCol'][] = "CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(purchase_order_master.po_prefix, '/', 1), '/', -1),'/',purchase_order_master.po_no,'/',SUBSTRING_INDEX(SUBSTRING_INDEX(purchase_order_master.po_prefix, '/', 2), '/', -1))";
        $data['searchCol'][] = "DATE_FORMAT(purchase_order_master.po_date, '%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "purchase_order_trans.price";
        $data['searchCol'][] = "purchase_order_trans.qty";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(purchase_order_trans.delivery_date, '%d-%m-%Y')";

		$columns =array('','','purchase_order_master.po_no','purchase_order_master.po_date','party_master.party_name','item_master.full_name','purchase_order_trans.price','purchase_order_trans.qty','','','purchase_order_trans.delivery_date');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
       
		return $this->pagingRows($data);
    }
  
    public function getPurchaseOrder($id){
		$data['tableName'] = $this->purchaseOrderMaster;
		$data['select'] = "purchase_order_master.*,party_master.party_name,party_master.contact_person,party_master.contact_email, party_master.party_mobile,party_master.gstin,party_master.party_address,purchase_enquiry.enq_prefix,purchase_enquiry.enq_no,purchase_enquiry.enq_date";
		$data['join']['party_master'] = "purchase_order_master.party_id = party_master.id";
        $data['leftJoin']['purchase_enquiry'] = "purchase_enquiry.id = purchase_order_master.enq_id";
        $data['where']['purchase_order_master.id'] = $id;
        $result = $this->row($data);
		$result->itemData = $this->getPurchaseOrderTransactions($id);
		return $result;
	}
	
	public function getPurchaseOrderTransactions($id){
        $data['tableName'] = $this->purchaseOrderTrans;
        $data['select'] = "purchase_order_trans.*,item_master.full_name,item_master.item_code,unit_master.unit_name";
        $data['leftJoin']['item_master'] = "item_master.id = purchase_order_trans.item_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = purchase_order_trans.unit_id";
        $data['where']['purchase_order_trans.order_id'] = $id;
        return $this->rows($data);
    }  

	public function getOrderItems($orderIds,$edit_mode=0){
		$data['tableName'] = $this->purchaseOrderTrans;
        $data['select'] = "purchase_order_trans.*,item_master.item_name,item_master.item_code,unit_master.unit_name,item_master.batch_stock,item_master.location,item_master.item_type";
        $data['leftJoin']['item_master'] = "item_master.id = purchase_order_trans.item_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = purchase_order_trans.unit_id";
		if(empty($edit_mode)):
			$data['where']['purchase_order_trans.order_status'] = 0;
		endif;
        $data['where_in']['purchase_order_trans.order_id'] = $orderIds;
        return $this->rows($data);
	}

    public function save($masterData,$itemData){
        $orderId = $masterData['id'];
		$req_id = $masterData['req_id']; unset($masterData['req_id']);
		$base_url = (!empty($masterData['order_type'])?base_url("purchaseOrder/rmIndex"):base_url("purchaseOrder"));
		if($this->checkDuplicateOrder($masterData['party_id'],$masterData['po_no'],$masterData['order_type'],$orderId) > 0):
			$errorMessage['po_no'] = "PO. No. is duplicate.";
			return ['status'=>0,'message'=>$errorMessage];
		endif;

		if(empty($orderId)):			
			//save purchase master data
			$purchaseOrderSave = $this->store($this->purchaseOrderMaster,$masterData);
			$orderId = $purchaseOrderSave['insert_id'];		
			
			if(!empty($req_id)){ 
				$ref_id=explode('~',$req_id);
				
				foreach($ref_id as $row)
				{
					$this->store("requisition_log",["id"=>$row,"from_ref"=>1,"order_status"=>1]); 
				}
			}
			
			if(!empty($masterData['enq_id'])):
				$this->store($this->purchaseEnquiryMaster,['id'=>$masterData['enq_id'],'enq_status'=>1]);
			endif;

			$result = ['status'=>1,'message'=>'Purchase order saved successfully.','url'=>$base_url];			
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
			
			$result = ['status'=>1,'message'=>'Purchase Order updated successfully.','url'=>$base_url];
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

	public function getPartyOrders($party_id,$order_id=""){
        $queryData['tableName'] = $this->purchaseOrderMaster;
        $queryData['select'] = "id,po_no,po_prefix,po_date";
        if(!empty($order_id)):
        	$queryData['customWhere'][] = "(order_status = 0 OR id IN (".$order_id."))";
		else:
			$queryData['where']['order_status'] = 0;
		endif;
        $queryData['where']['party_id'] = $party_id;
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

	public function getFamilyItem($item_id,$family_id){
        $data['tableName'] = $this->itemMaster;
        if(!empty($family_id)){$data['where']['family_id'] = $family_id;}else{$data['where']['id'] = $item_id;}
        $itemData = $this->rows($data);

		$tbody="";$i=1;
		if(!empty($itemData)):
			foreach($itemData as $row):
				$queryData['tableName'] = $this->grnTrans;
				$queryData['select'] = 'grn_transaction.*,grn_master.grn_date,party_master.party_name,item_master.item_name';
				$queryData['join']['grn_master'] = 'grn_master.id = grn_transaction.grn_id';
				$queryData['join']['item_master'] = 'item_master.id = grn_transaction.item_id';
				$queryData['leftJoin']['party_master'] = 'party_master.id = grn_master.party_id';
				$queryData['where']['grn_transaction.item_id'] = $row->id;
				$queryData['limit'][] = 1;
				// $queryData['group_by'][] = "grn_master.party_id";
				$queryData['order_by']['grn_master.grn_date'] = "DESC";
				// $queryData['order_by']['grn_master.id'] = "DESC";
				$queryData['order_by']['grn_transaction.price'] = "ASC";
				$result = $this->rows($queryData);

				if(!empty($result)):
					foreach($result as $grn):
						$tbody .= '<tr class="text-center">
							<td>'.$i++.'</td>
							<td>'.$grn->item_name.'</td>
							<td>'.$grn->party_name.'</td>
							<td>'.formatDate($grn->grn_date).'</td>
							<td>'.$grn->qty.'</td>
							<td>'.$grn->price.'</td>	
						</tr>';
					endforeach;
				endif;
			endforeach;
		else:
			$tbody .= '<tr class="text-center"><td colspan="6">No data found</td></tr>';
		endif;
		return ['status'=>1,'tbody'=>$tbody];
    }
    
    public function getPendingPartyWisePOItems($data){
		$queryData['tableName'] = $this->purchaseOrderTrans;
        $queryData['select'] = "purchase_order_trans.*,item_master.item_name,item_master.full_name,item_master.item_code,unit_master.unit_name,item_master.batch_stock,item_master.location,item_master.item_type,purchase_order_master.po_prefix,purchase_order_master.po_no,party_master.party_name";

        $queryData['leftJoin']['item_master'] = "item_master.id = purchase_order_trans.item_id";
        $queryData['leftJoin']['unit_master'] = "unit_master.id = purchase_order_trans.unit_id";
		$queryData['leftJoin']['purchase_order_master'] = "purchase_order_master.id = purchase_order_trans.order_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = purchase_order_master.id";

		if(!empty($data['item_id'])):
			$queryData['where']['purchase_order_trans.item_id'] = $data['item_id'];
		endif;
		
		if(!empty($data['order_id'])):
			$queryData['where']['purchase_order_trans.order_id'] = $data['order_id'];
		endif;

		if(!empty($data['party_id'])):
			$queryData['where']['purchase_order_master.party_id'] = $data['party_id'];
		endif;
		
		if(!empty($data['grn_type']) && $data['grn_type'] == 1):
			$queryData['where']['item_master.item_type !='] = 3;
		else:
			$queryData['where']['item_master.item_type'] = 3;
		endif;

		if(!empty($data['po_trans_id'])):
			$queryData['customWhere'][] = "(purchase_order_trans.id = ".$data['po_trans_id']." OR purchase_order_trans.order_status = 0)";
		elseif(!empty($data['po_id'])):
			$queryData['customWhere'][] = "(purchase_order_trans.order_id = ".$data['po_id']." OR purchase_order_trans.order_status = 0)";
		else:
			$queryData['where']['purchase_order_trans.order_status'] = 0;
		endif;
        
		if(!empty($data['group_by'])):
			$queryData['group_by'][] = $data['group_by'];
		endif;

        $result = $this->rows($queryData); //print_r($this->db->last_query()); exit;
        return $result;
	}
}
?>