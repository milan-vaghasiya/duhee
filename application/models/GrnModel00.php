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
        $data['select'] = "grn_transaction.*,grn_master.grn_no,grn_master.grn_prefix,grn_master.grn_date,grn_master.challan_no,	grn_master.remark,party_master.party_name,item_master.item_name";
        $data['join']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
        $data['join']['party_master'] = "party_master.id = grn_master.party_id";
        $data['join']['item_master'] = "item_master.id = grn_transaction.item_id";

        $data['searchCol'][] = "grn_master.grn_no";
        $data['searchCol'][] = "DATE_FORMAT(grn_master.grn_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "grn_transaction.batch_no";
        $data['searchCol'][] = "item_master.item_name";
        return $this->pagingRows($data);
	}

	public function purchaseMaterialInspectionList($data,$columns){
		$data['tableName'] = $this->grnItemTable;
        $data['select'] = "grn_transaction.*,item_master.item_name,grn_master.grn_no,grn_master.grn_prefix,grn_master.grn_date";
        $data['join']['item_master'] = "item_master.id = grn_transaction.item_id";
		$data['join']['grn_master'] = "grn_master.id = grn_transaction.grn_id";

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
			$this->store($this->grnTable,$masterData);
			
			$data['where']['grn_id'] = $purchaseId;
			$data['tableName'] = $this->grnItemTable;
			$ptransArray = $this->rows($data);
			
			foreach($ptransArray as $value):
				if(!in_array($value->id,$itemData['id'])):						
					$this->trash($this->grnItemTable,['id'=>$value->id]);
				endif;
				if(!empty($value->po_trans_id)):
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
							'color_code' => $itemData['color_code'][$key],
							'created_by' => $itemData['created_by']
						];
			$this->store($this->grnItemTable,$transData);
			
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
			if(!empty($row->po_trans_id)):
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
		
		foreach($data['item_id'] as $key=>$value):
			if(empty($data['inspected_id'][$key])):
				$dataRow = [
					'id' => "",
					'inspection_date' => date('Y-m-d'),
					'grn_id' => $data['grn_id'],
					'ptrans_id' => $data['ptrans_id'][$key],
					'item_id' => $value,
					'recived_qty' => $data['recived_qty'][$key],
					'inspection_status' => $data['inspection_status'][$key],
					'inspected_qty' => $data['inspected_qty'][$key],
					'ud_qty' => $data['ud_qty'][$key],
					'reject_qty' => $data['reject_qty'][$key],
					'scrape_qty' => $data['scrape_qty'][$key],
					'short_qty' => $data['short_qty'][$key],
                    'created_by' => $data['created_by']
				]; 
				$this->store('purchase_inspection',$dataRow);

				/** Update Item Stock **/				
				$setData['tableName'] = $this->itemMaster;
				$setData['where']['id'] = $value;
				$setData['set']['qty'] = 'qty, + '.($data['inspected_qty'][$key] + $data['ud_qty'][$key]);
				$setData['set']['reject_qty'] = 'reject_qty, + '.$data['reject_qty'][$key];
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
						$stockQueryData['qty']=($data['inspected_qty'][$key] + $data['ud_qty'][$key]);
						$stockQueryData['ref_type']=1;
						$stockQueryData['ref_id']=$data['ptrans_id'][$key];
						$stockQueryData['ref_date']=date('Y-m-d');
						$stockQueryData['created_by']=$this->loginID;
						$this->store($this->stockTrans,$stockQueryData);
					endif;
				endif;
				
				$grnItemTableData = [
					'id' => $data['ptrans_id'][$key],
					'inspected_qty' => ($data['inspected_qty'][$key] + $data['ud_qty'][$key]),
					'remaining_qty' => ($data['inspected_qty'][$key] + $data['ud_qty'][$key])
				];
				$this->store($this->grnItemTable,$grnItemTableData);
			else:
                $queryData['tableName'] = "purchase_inspection";
                $queryData['where']['id'] = $data['inspected_id'][$key];
				$inspectedData = $this->row($queryData);

				$dataRow = [
					'id' => $data['inspected_id'][$key],
					'inspection_date' => date('Y-m-d'),
					'grn_id' => $data['grn_id'],
					'ptrans_id' => $data['ptrans_id'][$key],
					'item_id' => $value,
					'recived_qty' => $data['recived_qty'][$key],
					'inspection_status' => $data['inspection_status'][$key],
					'inspected_qty' => $data['inspected_qty'][$key],
					'ud_qty' => $data['ud_qty'][$key],
					'reject_qty' => $data['reject_qty'][$key],
					'scrape_qty' => $data['scrape_qty'][$key],
					'short_qty' => $data['short_qty'][$key]
				]; 
				$this->store('purchase_inspection',$dataRow);

				$itemMasterData = array();
				$itemStock = $this->item->getItem($value);
				$inspectQty = ($data['inspected_qty'][$key] + $data['ud_qty'][$key]);
				$inspectedTotalQty = $inspectedData->inspected_qty + $inspectedData->ud_qty ;
				if($inspectedTotalQty > $inspectQty):
					$varInspectedQty = $inspectedTotalQty - $inspectQty;
					$itemMasterData['qty'] = $itemStock->qty - $varInspectedQty;
				elseif($inspectedTotalQty < $inspectQty):
					$varInspectedQty =  $inspectQty - $inspectedTotalQty;
					$itemMasterData['qty'] = $itemStock->qty + $varInspectedQty;
				endif;

				if($inspectedData->reject_qty > $data['reject_qty'][$key]):
					$varRejectQty = $inspectedData->reject_qty - $data['reject_qty'][$key];
					$itemMasterData['reject_qty'] = $itemStock->reject_qty - $varRejectQty;
				elseif($inspectedData->reject_qty < $data['reject_qty'][$key]):
					$varRejectQty =  $data['reject_qty'][$key] - $inspectedData->reject_qty;
					$itemMasterData['reject_qty'] = $itemStock->reject_qty + $varRejectQty;
				endif;

				if($inspectedData->scrape_qty > $data['scrape_qty'][$key]):
					$varScrapeQty = $inspectedData->scrape_qty - $data['scrape_qty'][$key];
					$itemMasterData['scrape_qty'] = $itemStock->scrape_qty - $varScrapeQty;
				elseif($inspectedData->scrape_qty < $data['scrape_qty'][$key]):
					$varScrapeQty =  $data['scrape_qty'][$key] - $inspectedData->scrape_qty;
					$itemMasterData['scrape_qty'] = $itemStock->scrape_qty + $varScrapeQty;
				endif;

				if($inspectedData->short_qty > $data['short_qty'][$key]):
					$varShortQty = $inspectedData->short_qty - $data['short_qty'][$key];
					$itemMasterData['short_qty'] = $itemStock->short_qty - $varShortQty;
				elseif($inspectedData->short_qty < $data['short_qty'][$key]):
					$varShortQty =  $data['short_qty'][$key] - $inspectedData->short_qty;
					$itemMasterData['short_qty'] = $itemStock->short_qty + $varShortQty;
				endif;

				if(!empty($itemMasterData)):
					$itemMasterData['id'] = $value;
					$this->store($this->itemMaster,$itemMasterData);
				endif;
				
				/*** UPDATE STOCK TRANSACTION DATA ***/
				if($inspectQty > 0):
					$stQuery['tableName'] = $this->stockTrans;
					$stQuery['where']['ref_id'] = $data['ptrans_id'][$key];
					$stQuery['where']['ref_type'] = 1;
					$oldStockData = $this->row($stQuery);
					
					$stockID = (!empty($oldStockData)) ? $oldStockData->id : "";
					$ptransItem = $this->getgrnItemTableRow($data['ptrans_id'][$key]);
					if(!empty($ptransItem)):
						$stockQueryData['id']= $stockID;
						$stockQueryData['location_id']=$ptransItem->location_id;
						if(!empty($ptransItem->batch_no)){$stockQueryData['batch_no'] = $ptransItem->batch_no;}
						$stockQueryData['trans_type']=1;
						$stockQueryData['item_id']=$ptransItem->item_id;
						$stockQueryData['qty']=$inspectQty;
						$stockQueryData['ref_type']=1;
						$stockQueryData['ref_id']=$data['ptrans_id'][$key];
						$stockQueryData['ref_id']=$data['ptrans_id'][$key];
						$stockQueryData['created_by']=$this->loginID;
						$this->store($this->stockTrans,$stockQueryData);
					endif;
				else:
					$this->remove($this->stockTrans,['ref_id'=>$data['ptrans_id'][$key],'ref_type'=>1]);
				endif;

				$queryData = array();
				$queryData['tableName'] = $this->grnItemTable;
				$queryData['where']['id'] = $data['ptrans_id'][$key];
				$purchaseTrans = $this->row($queryData);

				if($purchaseTrans->remaining_qty == "0.000" || $purchaseTrans->remaining_qty == $purchaseTrans->inspected_qty):
					$grnItemTableData = [
						'id' => $data['ptrans_id'][$key],
						'inspected_qty' => $inspectQty,
						'remaining_qty' => $inspectQty
					];
					$this->store($this->grnItemTable,$grnItemTableData);
				endif;
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

}
?>