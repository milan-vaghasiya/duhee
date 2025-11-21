<?php 
class GrnModel extends MasterModel
{
	private $grnTable = "grn_master";
    private $grnItemTable = "grn_transaction";
    private $purchaseOrderMaster = "purchase_order_master";
    private $purchaseOrderTrans = "purchase_order_trans";
    private $itemMaster = "item_master";
    private $stockTrans = "stock_transaction";
	
    public function nextGrnNo(){
        $data['select'] = "MAX(grn_no) as grnNo";  
        $data['tableName'] = $this->grnTable;
        $data['where']['is_delete'] = 0;
		$grnNo = $this->specificRow($data)->grnNo;
		$nextGrnNo = (!empty($grnNo))?($grnNo + 1):1;
		return $nextGrnNo;
    }
	
	public function getDTRows($data){
        $data['tableName'] = $this->grnItemTable;
        $data['select'] = "grn_transaction.*,grn_master.grn_no,grn_master.grn_prefix,grn_master.grn_date,grn_master.challan_no,	grn_master.remark,grn_master.type,party_master.party_name,item_master.item_name,purchase_order_master.po_no,purchase_order_master.po_prefix,unit_master.unit_name";
        $data['join']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
        $data['join']['party_master'] = "party_master.id = grn_master.party_id";
        $data['join']['item_master'] = "item_master.id = grn_transaction.item_id";
        $data['leftJoin']['purchase_order_master'] = "purchase_order_master.id = grn_master.order_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = grn_transaction.unit_id";
		$data['where']['grn_transaction.qc_status']=1;
        $data['order_by']['grn_master.grn_date'] = "DESC";
        $data['order_by']['grn_master.id'] = "DESC";

        $data['searchCol'][] = "grn_master.grn_no";
        $data['searchCol'][] = "DATE_FORMAT(grn_master.grn_date,'%d-%m-%Y')";
        $data['searchCol'][] = "purchase_order_master.po_prefix";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "grn_transaction.qty";
        $data['searchCol'][] = "unit_master.unit_name";
        $data['searchCol'][] = "grn_transaction.batch_no";
        $data['searchCol'][] = "grn_transaction.color_code";

		$columns =array('','','grn_master.grn_no','grn_master.grn_date','purchase_order_master.po_prefix','party_master.party_name','item_master.item_name','grn_transaction.qty','unit_master.unit_name,grn_transaction.batch_no','grn_transaction.color_code');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}

        return $this->pagingRows($data);
	}

	public function purchaseMaterialInspectionList($data,$columns){
		$data['tableName'] = $this->grnItemTable;
        $data['select'] = "grn_transaction.*,item_master.item_name,grn_master.grn_no,grn_master.grn_prefix,grn_master.grn_date";
        $data['leftJoin']['item_master'] = "item_master.id = grn_transaction.item_id";
		$data['leftJoin']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
		$data['where']['grn_master.type'] = 1;

        $data['searchCol'][] = "grn_master.grn_no";
        $data['searchCol'][] = "DATE_FORMAT(grn_master.grn_date,'%d-%m-%Y')";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "grn_transaction.qty";
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
	}
	
	public function checkDuplicateGRN($party_id,$grn_no,$id){
        $data['tableName'] = $this->grnTable;
        $data['where']['grn_no'] = $grn_no;
        $data['where']['party_id'] = $party_id;
        if(!empty($id))
            $data['where']['id != '] = $id;

        return $this->numRows($data);
	}
	
	public function save($masterData,$itemData){
		$purchaseId = $masterData['id'];
		
		$checkDuplicate = $this->checkDuplicateGRN($masterData['party_id'],$masterData['grn_no'],$purchaseId);
		if($checkDuplicate > 0):
			$errorMessage['grn_no'] = "GRN No. is Duplicate.";
			return ['status'=>0,'message'=>$errorMessage];
		endif;

		if(empty($purchaseId)):				
			//save purchase master data
			$purchaseInvSave = $this->store($this->grnTable,$masterData);
			$purchaseId = $purchaseInvSave['insert_id'];			
			
			$result = ['status'=>1,'message'=>'GRN saved successfully.','url'=>base_url("grn")];			
		else:
			$data = Array();
			$data['tableName'] = $this->grnTable;
			$data['where']['id'] = $purchaseId;
			$grnData = $this->row($data);

			$this->store($this->grnTable,$masterData);
			
			$data = Array();
			$data['where']['grn_id'] = $purchaseId;
			$data['tableName'] = $this->grnItemTable;
			$ptransArray = $this->rows($data);
			// print_r($ptransArray);exit;
			
			foreach($ptransArray as $value):
				if(!in_array($value->id,$itemData['id'])):						
					$this->trash($this->grnItemTable,['id'=>$value->id]);
				endif;
				if($grnData->type == 2):
					$setData = array();
					$setData['tableName'] = $this->itemMaster;
					$setData['where']['id'] = $value->item_id;
					$setData['set']['qty'] = 'qty, - '.$value->qty;
					$qryresult = $this->setValue($setData);
					
					$this->remove($this->stockTrans,['ref_id'=>$value->id,'ref_type'=>1,'item_id'=>$value->item_id]);
				endif;

				if(!empty($value->po_trans_id)):
					$setData = array();
					$setData['tableName'] = $this->purchaseOrderTrans;
					$setData['where']['id'] = $value->po_trans_id;
					$setData['set']['rec_qty'] = 'rec_qty, - '.$value->qty;
					$qryresult = $this->setValue($setData);

					/** If Po Order Qty is Complete then Close PO **/
					$poTrans = $this->getPoTransactionRow($value->po_trans_id);
					if($poTrans->rec_qty >= $poTrans->qty):
						$this->store($this->purchaseOrderTrans,["id"=>$value->po_trans_id, "order_status"=>1]);
					else:
						$this->store($this->purchaseOrderTrans,["id"=>$value->po_trans_id, "order_status"=>0]);
					endif;
				endif;
			endforeach;		
			
			$result = ['status'=>1,'message'=>'GRN updated successfully.','url'=>base_url("grn")];
		endif;

		//save purchase items
		foreach($itemData['item_id'] as $key=>$value):
			$transData = [
							'id' => $itemData['id'][$key],
							'grn_id' => $purchaseId,
							'batch_no' => $itemData['batch_no'][$key],
							'po_trans_id' => $itemData['po_trans_id'][$key],
							'item_id' => $value,
							'unit_id' => $itemData['unit_id'][$key],
							'fgitem_id' => $itemData['fgitem_id'][$key],
							'fgitem_name' => $itemData['fgitem_name'][$key],
							'location_id' => $itemData['location_id'][$key],
							'qty' => $itemData['qty'][$key],
							'qty_kg' => $itemData['qty_kg'][$key],
							'price' => $itemData['price'][$key],
							'color_code' => $itemData['color_code'][$key],
							'created_by' => $itemData['created_by']
						];
			if(empty($itemData['id'][$key]) && $masterData['type'] == 2):
				$transData['inspected_qty'] = $itemData['qty'][$key];
				$transData['remaining_qty'] = $itemData['qty'][$key];	
			elseif(!empty($itemData['id'][$key]) && $masterData['type'] == 2):
				$queryData = array();
				$queryData['tableName'] = $this->grnItemTable;
				$queryData['where']['id'] = $itemData['id'][$key];
				$transRow = $this->row($queryData);
				if($transRow->qty == $transRow->inspected_qty):
					$transData['inspected_qty'] = $itemData['qty'][$key];
					$transData['remaining_qty'] = $itemData['qty'][$key];
				endif;
			endif;

			$transRowSave = $this->store($this->grnItemTable,$transData);
			$trans_id = 0;
			$trans_id = (!empty($itemData['id'][$key])) ? $itemData['id'][$key] : $transRowSave['insert_id'];


			if($masterData['type'] == 2):
				$setData = array();
				$setData['tableName'] = $this->itemMaster;
				$setData['where']['id'] = $value;
				$setData['set']['qty'] = 'qty, + '.$itemData['qty'][$key];
				$qryresult = $this->setValue($setData);
				
				/*** UPDATE STOCK TRANSACTION DATA ***/
				$stockQueryData['id']="";
				$stockQueryData['location_id']=$itemData['location_id'][$key];
				if(!empty($itemData['batch_no'][$key])){$stockQueryData['batch_no'] = $itemData['batch_no'][$key];}
				$stockQueryData['trans_type']=1;
				$stockQueryData['item_id']=$value;
				$stockQueryData['qty']=$itemData['qty'][$key];
				$stockQueryData['ref_type']=1;
				$stockQueryData['ref_id']=$trans_id;
				$stockQueryData['ref_no']=getPrefixNumber($masterData['grn_prefix'],$masterData['grn_no']);
				$stockQueryData['ref_date']=$masterData['grn_date'];
				$stockQueryData['created_by']=$this->loginID;
				$this->store($this->stockTrans,$stockQueryData);
			endif;
			
			if(!empty($itemData['po_trans_id'][$key])):
				$setData['tableName'] = $this->purchaseOrderTrans;
				$setData['where']['id'] = $itemData['po_trans_id'][$key];
				$setData['set']['rec_qty'] = 'rec_qty, + '.$itemData['qty'][$key];
				$qryresult = $this->setValue($setData);
				
				/** If Po Order Qty is Complete then Close PO **/
				$poTrans = $this->getPoTransactionRow($itemData['po_trans_id'][$key]);
				if($poTrans->rec_qty >= $poTrans->qty):
					$this->store($this->purchaseOrderTrans,["id"=>$itemData['po_trans_id'][$key], "order_status"=>1]);
				else:
					$this->store($this->purchaseOrderTrans,["id"=>$itemData['po_trans_id'][$key], "order_status"=>0]);
				endif;
			endif;
			
			
		endforeach;

		if(!empty($masterData['order_id'])):
			$poIds = explode(",",$masterData['order_id']);
			foreach($poIds as $key=>$value):
				$queryData = array();
				$queryData['tableName'] = $this->purchaseOrderTrans;
				$queryData['select'] = "COUNT(id) as pendingItems";
				$queryData['where']['order_id'] = $value;
				$queryData['where']['order_status'] = 0;
				$pendingItems = $this->specificRow($queryData)->pendingItems;
				
				if(empty($pendingItems)):
					$this->store($this->purchaseOrderMaster,['id'=>$value,'order_status'=>1]);
				else:
					$this->store($this->purchaseOrderMaster,['id'=>$value,'order_status'=>0]);
				endif;
			endforeach;
		endif;

		return $result;		
	}
	
	public function getPoTransactionRow($id){
		$data['tableName'] = $this->purchaseOrderTrans;
        $data['where']['id'] = $id;
        return $this->row($data);
	}
	
	public function editInv($id){
        $data['tableName'] = $this->grnTable;
        $data['where']['id'] = $id;
		$result = $this->row($data);
		// print_r($id);
        $data = array();
        $data['select'] = "grn_transaction.*,item_master.item_name,item_master.item_code,unit_master.unit_name";
        $data['join']['item_master'] = "item_master.id = grn_transaction.item_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = grn_transaction.unit_id";
        $data['where']['grn_transaction.grn_id'] = $id;
        $data['tableName'] = $this->grnItemTable;
		$result->itemData = $this->rows($data);
		return $result;
	}
	
	public function delete($id){
		$grnData = $this->editInv($id);
		foreach($grnData->itemData as $row):
			if($grnData->type == 2):
				$setData = array();
				$setData['tableName'] = $this->itemMaster;
				$setData['where']['id'] = $row->item_id;
				$setData['set']['qty'] = 'qty, - '.$row->qty;
				$qryresult = $this->setValue($setData);
				
				$this->remove($this->stockTrans,['ref_id'=>$row->id,'ref_type'=>1,'item_id'=>$row->item_id]);
			else:
			    if($row->inspected_qty > 0):
    			    $data = array();
                    $data['tableName'] = 'purchase_inspection';
                    $data['where']['ptrans_id'] = $row->id;
                    $data['where']['grn_id'] = $row->grn_id;
                    $data['where']['is_delete'] = 0;
    				$inspectedData = $this->row($data);
    				
    			    /** Update Item Stock **/	
    				$setData = array();			
    				$setData['tableName'] = $this->itemMaster;
    				$setData['where']['id'] = $row->item_id;
    				$setData['set']['qty'] = 'qty, - '.$inspectedData->inspected_qty;
    				$setData['set']['reject_qty'] = 'reject_qty, - '.$inspectedData->reject_qty;
    				$setData['set']['scrape_qty'] = 'scrape_qty, - '.$inspectedData->scrape_qty;
    				$setData['set']['short_qty'] = 'short_qty, - '.$inspectedData->short_qty;
    				$qryresult = $this->setValue($setData);
    
    				/*** UPDATE STOCK TRANSACTION DATA ***/
    				$this->remove($this->stockTrans,['ref_id'=>$inspectedData->ptrans_id,'ref_type'=>1,'item_id'=>$inspectedData->item_id]);
    				$this->trash('purchase_inspection',['id'=>$inspectedData->id],'');
				endif;
			endif;
			

			if(!empty($row->po_trans_id)):
				$setData = array();
				$setData['tableName'] = $this->purchaseOrderTrans;
				$setData['where']['id'] = $row->po_trans_id;
				$setData['set']['rec_qty'] = 'rec_qty, - '.$row->qty;
				$qryresult = $this->setValue($setData);

				/** If Po Order Qty is Complete then Close PO **/
				$poTrans = $this->getPoTransactionRow($row->po_trans_id);
				if($poTrans->rec_qty >= $poTrans->qty):
					$this->store($this->purchaseOrderTrans,["id"=>$row->po_trans_id, "order_status"=>1]);
				else:
					$this->store($this->purchaseOrderTrans,["id"=>$row->po_trans_id, "order_status"=>0]);
				endif;
			endif;
			$this->trash($this->grnItemTable,['id'=>$row->id]);
		endforeach;
        
		if(!empty($grnData->order_id)):
			$poIds = explode(",",$grnData->order_id);
			foreach($poIds as $key=>$value):
				$queryData = array();
				$queryData['tableName'] = $this->purchaseOrderTrans;
				$queryData['select'] = "COUNT(id) as pendingItems";
				$queryData['where']['order_id'] = $value;
				$queryData['where']['order_status'] = 0;
				$pendingItems = $this->specificRow($queryData)->pendingItems;				
				if(empty($pendingItems)):
					$this->store($this->purchaseOrderMaster,['id'=>$value,'order_status'=>1]);
				else:
					$this->store($this->purchaseOrderMaster,['id'=>$value,'order_status'=>0]);
				endif;
			endforeach;
		endif;

		return $this->trash($this->grnTable,['id'=>$id],'GRN');
	}

	public function getInspectedMaterial($id){
        $data['select'] = "grn_transaction.*,item_master.item_name,item_master.item_code,unit_master.unit_name";
        $data['join']['item_master'] = "item_master.id = grn_transaction.item_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = grn_transaction.unit_id";
        $data['where']['grn_transaction.id'] = $id;       
        $data['tableName'] = $this->grnItemTable;
		$result = $this->rows($data);
		
		if(!empty($result)):
			$i=1;$html="";
			foreach($result as $row):
                $data = array();
                $data['tableName'] = 'purchase_inspection';
                $data['where']['ptrans_id'] = $row->id;
                $data['where']['grn_id'] = $row->grn_id;
                $data['where']['is_delete'] = 0;
				$inspectedData = $this->row($data);
				
				$readonly = ($row->inspected_qty != $row->remaining_qty)?"readonly":"";
				$disable = ($row->inspected_qty != $row->remaining_qty)?"disabled":"";
				$inspOptions = '<option value="">Select Status</option>';
				if(!empty($inspectedData)):
					$inspected_id = $inspectedData->id;
					$inspected_qty = $inspectedData->inspected_qty;
					$ud_qty = $inspectedData->ud_qty;
					$reject_qty = $inspectedData->reject_qty;
					$scrape_qty = $inspectedData->scrape_qty;
					$short_qty = $inspectedData->short_qty;
					if($inspectedData->inspection_status == "Ok"):
						$inspOptions = '<option value="Ok" selected '.$disable.'>Ok</option><option value="Not Ok" '.$disable.'>Not Ok</option>';
					else:
						$inspOptions = '<option value="Ok" '.$disable.'>Ok</option><option value="Not Ok" selected '.$disable.'>Not Ok</option>';
					endif;
				else:
					$inspected_id = "";
					$inspected_qty = $row->qty;
					$ud_qty = 0;
					$reject_qty = 0;
					$scrape_qty = 0;
					$short_qty = 0;
					$inspOptions = '<option value="Ok" '.$disable.'>Ok</option><option value="Not Ok" '.$disable.'>Not Ok</option>';
				endif;
				
				$html .= '<tr class="text-center">
							<td>'.$i.'</td>
							<td>'.$row->batch_no.'</td>
							<td>
								'.$row->qty.'
								<input type="hidden" name="recived_qty[]" id="recived_qty'.$i.'" value="'.$row->qty.'">
								<input type="hidden" name="inspected_id[]" id="inspected_id'.$i.'" value="'.$inspected_id.'">
								<input type="hidden" name="item_id[]" id="item_id'.$i.'" value="'.$row->item_id.'">
								<input type="hidden" name="ptrans_id[]" id="ptrans_id'.$i.'" value="'.$row->id.'">
								<input type="hidden" name="ud_qty[]" id="ud_qty'.$i.'" class="form-control floatOnly" value="'.$ud_qty.'">
								<input type="hidden" name="reject_qty[]" id="reject_qty'.$i.'" class="form-control floatOnly" value="'.$reject_qty.'">
								<input type="hidden" name="scrape_qty[]" id="scrape_qty'.$i.'" class="form-control floatOnly" value="'.$scrape_qty.'">
								<input type="hidden" name="inspected_qty[]" id="inspected_qty'.$i.'" class="form-control floatOnly" value="'.$inspected_qty.'">
								<div class="error recived_qty'.$i.'"></div>
							</td>
							<td>
								<select name="inspection_status[]" id="inspection_status'.$i.'" class="form-control">'.$inspOptions.'</select>
								<div class="error inspection_status'.$i.'"></div>
							</td>
							<td>
								<input type="number" name="short_qty[]" id="short_qty'.$i.'" class="form-control floatOnly" value="'.$short_qty.'" '.$readonly.'>
								<div class="error short_qty'.$i.'"></div>
							</td>
						</tr>';
				$i++;
			endforeach;
		else:
			$html = '<tr><td class="text-center" colspan="7">No data available in table</td></tr>';
		endif;
		return $html;
	}

	public function inspectedMaterialSave($data){
		
		$grnData = $this->editInv($data['grn_id']);
		foreach($data['item_id'] as $key=>$value):
			$inspected_qty = ($data['inspection_status'][$key] == "Ok")?($data['recived_qty'][$key] - $data['short_qty'][$key]):0;
			$reject_qty = ($data['inspection_status'][$key] != "Ok")?$data['recived_qty'][$key]:0;
			$data['short_qty'][$key] = ($data['inspection_status'][$key] == "Ok")?$data['short_qty'][$key]:0;
			if(empty($data['inspected_id'][$key])):				
				$dataRow = [
					'id' => "",
					'inspection_date' => date('Y-m-d'),
					'grn_id' => $data['grn_id'],
					'ptrans_id' => $data['ptrans_id'][$key],
					'item_id' => $value,
					'recived_qty' => $data['recived_qty'][$key],
					'inspection_status' => $data['inspection_status'][$key],
					'inspected_qty' => $inspected_qty,
					'ud_qty' => $data['ud_qty'][$key],
					'reject_qty' => $reject_qty,
					'scrape_qty' => $data['scrape_qty'][$key],
					'short_qty' => $data['short_qty'][$key],
                    'created_by' => $data['created_by']
				]; 
				$this->store('purchase_inspection',$dataRow);

				if($data['inspection_status'][$key] == "Ok"):
					/** Update Item Stock **/	
					$setData = array();					
					$setData['tableName'] = $this->itemMaster;
					$setData['where']['id'] = $value;
					$setData['set']['qty'] = 'qty, + '.$inspected_qty;
					$setData['set']['reject_qty'] = 'reject_qty, + '.$reject_qty;
					$setData['set']['scrape_qty'] = 'scrape_qty, + '.$data['scrape_qty'][$key];
					$setData['set']['short_qty'] = 'short_qty, + '.$data['short_qty'][$key];
					$qryresult = $this->setValue($setData);

					/*** UPDATE STOCK TRANSACTION DATA ***/
					if($data['inspected_qty'][$key] > 0):
						$ptransItem = $this->getgrnItemTableRow($data['ptrans_id'][$key]);
						
						if(!empty($ptransItem)):
							$stockQueryData['id']="";
							$stockQueryData['location_id']=$ptransItem->location_id;
							if(!empty($ptransItem->batch_no)){$stockQueryData['batch_no'] = $ptransItem->batch_no;}
							$stockQueryData['trans_type']=1;
							$stockQueryData['item_id']=$ptransItem->item_id;
							$stockQueryData['qty']=$inspected_qty;
							$stockQueryData['ref_type']=1;
							$stockQueryData['ref_id']=$data['ptrans_id'][$key];
							$stockQueryData['ref_no']=getPrefixNumber($grnData->grn_prefix,$grnData->grn_no);
							$stockQueryData['ref_date']=$grnData->grn_date;
							$stockQueryData['created_by']=$this->loginID;
							$this->store($this->stockTrans,$stockQueryData);
						endif;
					endif;
				endif;
				
				$grnItemTableData = [
					'id' => $data['ptrans_id'][$key],
					'inspected_qty' => $data['recived_qty'][$key],
					'remaining_qty' => $data['recived_qty'][$key]
				];
				$this->store($this->grnItemTable,$grnItemTableData);
			else:
                $queryData['tableName'] = "purchase_inspection";
                $queryData['where']['id'] = $data['inspected_id'][$key];
				$inspectedData = $this->row($queryData);

				if($inspectedData->inspection_status == "Ok"):
					/** Update Item Stock **/	
					$setData = array();			
					$setData['tableName'] = $this->itemMaster;
					$setData['where']['id'] = $value;
					$setData['set']['qty'] = 'qty, - '.$inspectedData->inspected_qty;
					$setData['set']['reject_qty'] = 'reject_qty, - '.$inspectedData->reject_qty;
					$setData['set']['scrape_qty'] = 'scrape_qty, - '.$inspectedData->scrape_qty;
					$setData['set']['short_qty'] = 'short_qty, - '.$inspectedData->short_qty;
					$qryresult = $this->setValue($setData);

					/*** UPDATE STOCK TRANSACTION DATA ***/
					$this->remove($this->stockTrans,['ref_id'=>$inspectedData->ptrans_id,'ref_type'=>1,'item_id'=>$inspectedData->item_id]);
				endif;

				$dataRow = [
					'id' => $data['inspected_id'][$key],
					'inspection_date' => date('Y-m-d'),
					'grn_id' => $data['grn_id'],
					'ptrans_id' => $data['ptrans_id'][$key],
					'item_id' => $value,
					'recived_qty' => $data['recived_qty'][$key],
					'inspection_status' => $data['inspection_status'][$key],
					'inspected_qty' => $inspected_qty,
					'ud_qty' => $data['ud_qty'][$key],
					'reject_qty' => $reject_qty,
					'scrape_qty' => $data['scrape_qty'][$key],
					'short_qty' => $data['short_qty'][$key]
				]; 
				$this->store('purchase_inspection',$dataRow);				

				if($data['inspection_status'][$key] == "Ok"):
					/** Update Item Stock **/
					$setData = array();						
					$setData['tableName'] = $this->itemMaster;
					$setData['where']['id'] = $value;
					$setData['set']['qty'] = 'qty, + '.$inspected_qty;
					$setData['set']['reject_qty'] = 'reject_qty, + '.$reject_qty;
					$setData['set']['scrape_qty'] = 'scrape_qty, + '.$data['scrape_qty'][$key];
					$setData['set']['short_qty'] = 'short_qty, + '.$data['short_qty'][$key];
					$qryresult = $this->setValue($setData);

					/*** UPDATE STOCK TRANSACTION DATA ***/
					if($inspected_qty > 0):
						$ptransItem = $this->getgrnItemTableRow($data['ptrans_id'][$key]);
						// $grnData = $this->editInv($data['ptrans_id'][$key]);
						if(!empty($ptransItem)):
							$stockQueryData['id']="";
							$stockQueryData['location_id']=$ptransItem->location_id;
							if(!empty($ptransItem->batch_no)){$stockQueryData['batch_no'] = $ptransItem->batch_no;}
							$stockQueryData['trans_type']=1;
							$stockQueryData['item_id']=$ptransItem->item_id;
							$stockQueryData['qty']=$inspected_qty;
							$stockQueryData['ref_type']=1;
							$stockQueryData['ref_id']=$data['ptrans_id'][$key];
							$stockQueryData['ref_no']=getPrefixNumber($grnData->grn_prefix,$grnData->grn_no);
							$stockQueryData['ref_date']=$grnData->grn_date;
							$stockQueryData['created_by']=$this->loginID;
							$this->store($this->stockTrans,$stockQueryData);
						endif;
					endif;
				endif;
				
				$grnItemTableData = [
					'id' => $data['ptrans_id'][$key],
					'inspected_qty' => $data['recived_qty'][$key],
					'remaining_qty' => $data['recived_qty'][$key]
				];
				$this->store($this->grnItemTable,$grnItemTableData);				
			endif;
		endforeach;

		return ['status'=>1,'message'=>'Inspected Material saved successfully.'];
	}

	public function getLotWisegrnItemTables(){
		$queryData['select'] = "grn_transaction.id,grn_transaction.batch_no,grn_transaction.remaining_qty,grn_transaction.qty,grn_transaction.item_id,item_master.item_name,unit_master.unit_name";
		$queryData['join']['item_master'] = "grn_transaction.item_id = item_master.id";
		$queryData['join']['unit_master'] = "grn_transaction.unit_id = unit_master.id";
		$queryData['where']['grn_transaction.remaining_qty !='] = "0.000";
		$queryData['where']['item_master.rm_type'] = 1;
        $queryData['tableName'] = $this->grnItemTable;
		return $this->rows($queryData);
	}

	public function getgrnItemTableRow($id){
		$data['tableName'] = $this->grnItemTable;
		$data['where']['id'] = $id;
		return $this->row($data);
	}

    public function getItemsForGRN($party_id){
		
		$itemOptions='<option value="">Select Product Name</option>';
				
		$qdata['tableName'] = $this->purchaseOrderTrans;
		$qdata['select'] = "purchase_order_trans.*,item_master.item_name as itmname,item_master.item_code,item_master.unit_id,item_master.hsn_code, item_master.gst_per,item_master.price,unit_master.unit_name,purchase_order_master.po_no";
		$qdata['join']['purchase_order_master'] = "purchase_order_master.id = purchase_order_trans.order_id";
		$qdata['join']['item_master'] = "item_master.id = purchase_order_trans.item_id";
		$qdata['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$qdata['where']['purchase_order_master.party_id'] = $party_id;
		$qdata['where']['purchase_order_trans.order_status != '] = 2;
		$qdata['customWhere'][] = "purchase_order_trans.qty > purchase_order_trans.rec_qty";
		$itemData = $this->rows($qdata);
		$selectedIds = Array();
		if(!empty($itemData)):
			foreach ($itemData as $row):
				$dataRow = json_encode($row);$dataRow = "data-data_row='".$dataRow."'";
				$year = (date('m') > 3)?date('y').'-'.(date('y') +1):(date('y')-1).'-'.date('y');
				$itemOptions .= '<option value="'.$row->item_id.'" data-po_trans_id="'.$row->id.'" data-year="'.$year.'" '.$dataRow.' >['.$row->item_code.'] '.$row->itmname.' [PO/'.$row->po_no.'/'.$year.']</option>';
			endforeach;
		endif;
		$itemData = $this->item->getItemList();
		if(!empty($itemData)):
			foreach ($itemData as $row):
				if(!in_array($row->id,$selectedIds)):
					$dataRow = json_encode($row);$dataRow = "data-data_row='".$dataRow."'";
					$itemOptions .= '<option value="'.$row->id.'" data-po_trans_id="" data-year="" '.$dataRow.' >['.$row->item_code.'] '.$row->item_name.' </option>';
				endif;
			endforeach;
		endif;
		
		return $itemOptions;
	}

	public function getItemList($type=0){
		$data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.*,unit_master.unit_name";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		if(!empty($type))
			$data['where']['item_master.item_type'] = $type;
		return $this->rows($data);
	}
    
    public function itemColorCode(){
		$data['tableName'] = $this->grnItemTable;
		$data['select'] = 'color_code';
		$result = $this->rows($data);
		$searchResult = array();
		foreach($result as $row){$searchResult[] = $row->color_code;}
		return  $searchResult;
    }

	public function getCustomerGrn($party_id){
		$data['tableName'] = $this->grnTable;
		$data['select'] = "id,grn_prefix,grn_no,grn_date";
		$data['where']['party_id'] = $party_id;
		$data['where']['type'] = 2;
		$result = $this->rows($data);
		return $result;
	}

	public function getGrnItems($id){
		$data = array();
        $data['select'] = "grn_transaction.id,grn_transaction.item_id,grn_transaction.remaining_qty,item_master.item_name,item_master.item_code,unit_master.unit_name";
        $data['join']['item_master'] = "item_master.id = grn_transaction.item_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = grn_transaction.unit_id";
        $data['where']['grn_transaction.grn_id'] = $id;
        $data['tableName'] = $this->grnItemTable;
		$result = $this->rows($data);
		return $result;
	}

	public function updateGrnReport($data){
		$data['qc_status']=2;
		$result= $this->store($this->grnItemTable,$data);

		// print_r($dataRow);
		$this->edit($this->stockTrans,['ref_id' => $data['id'],'ref_type' => 1],['location_id' =>$data['location_id']]);
		
		return $result;
	}
}
?>