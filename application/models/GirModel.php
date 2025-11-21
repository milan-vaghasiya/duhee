<?php
class GirModel extends MasterModel
{
	private $girTable = "gir_master";
	private $girItemTable = "gir_transaction";
	private $purchaseOrderMaster = "purchase_order_master";
	private $purchaseOrderTrans = "purchase_order_trans";
	private $itemMaster = "item_master";
	private $stockTrans = "stock_transaction";

	public function nextGirNo()
	{
		$data['select'] = "MAX(gir_no) as grnNo";
		$data['tableName'] = $this->girTable;
		$data['where']['is_delete'] = 0;
		$grnNo = $this->specificRow($data)->grnNo;
		$nextGrnNo = (!empty($grnNo)) ? ($grnNo + 1) : 1;
		return $nextGrnNo;
	}

	public function getDTRows($data)
	{
		$data['tableName'] = $this->girItemTable;
		$data['select'] = "gir_transaction.*,gir_master.gir_no,gir_master.gir_prefix,gir_master.gir_date,gir_master.challan_no,	gir_master.remark,gir_master.type,party_master.party_name,item_master.item_name,purchase_order_master.po_no,purchase_order_master.po_prefix,unit_master.unit_name";
		$data['join']['gir_master'] = "gir_master.id = gir_transaction.gir_id";
		$data['join']['party_master'] = "party_master.id = gir_master.party_id";
		$data['join']['item_master'] = "item_master.id = gir_transaction.item_id";
		$data['leftJoin']['purchase_order_master'] = "purchase_order_master.id = gir_master.order_id";
		$data['leftJoin']['unit_master'] = "unit_master.id = gir_transaction.unit_id";
		$data['order_by']['gir_master.gir_date'] = "DESC";
		$data['order_by']['gir_master.id'] = "DESC";
		
		/* if($data['status'] == 0) { $data['where']['gir_transaction.qc_status'] = 0; } 
        else { $data['where']['gir_transaction.qc_status != '] = 0; } */
		if($data['status'] == 0):
			$data['where']['gir_master.trans_status'] = 0;
		else:
			$data['where']['gir_master.trans_status'] = 1;
		endif;

		$data['searchCol'][] = "gir_master.gir_no";
		$data['searchCol'][] = "DATE_FORMAT(gir_master.gir_date,'%d-%m-%Y')";
		$data['searchCol'][] = "purchase_order_master.po_prefix";
		$data['searchCol'][] = "party_master.party_name";
		$data['searchCol'][] = "item_master.item_name";
		$data['searchCol'][] = "gir_transaction.qty";
		$data['searchCol'][] = "unit_master.unit_name";
		$data['searchCol'][] = "gir_transaction.batch_no";
		$data['searchCol'][] = "gir_transaction.color_code";

		$columns = array('', '', 'gir_master.gir_no', 'gir_master.gir_date', 'purchase_order_master.po_prefix', 'party_master.party_name', 'item_master.item_name', 'gir_transaction.qty', 'unit_master.unit_name,gir_transaction.batch_no', 'gir_transaction.color_code');
		if (isset($data['order'])) {
			$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
		}

		return $this->pagingRows($data);
	}

	public function checkDuplicateGIR($party_id, $gir_no, $id)
	{
		$data['tableName'] = $this->girTable;
		$data['where']['gir_no'] = $gir_no;
		$data['where']['party_id'] = $party_id;
		if (!empty($id))
			$data['where']['id != '] = $id;

		return $this->numRows($data);
	}

	public function save($masterData,$itemData){
		$purchaseId = $masterData['id'];
		
		$checkDuplicate = $this->checkDuplicateGIR($masterData['party_id'],$masterData['gir_no'],$purchaseId);
		if($checkDuplicate > 0):
			$errorMessage['gir_no'] = "GIR No. is Duplicate.";
			return ['status'=>0,'message'=>$errorMessage];
		endif;

		if(empty($purchaseId)):				
			//save purchase master data
			$purchaseInvSave = $this->store($this->girTable,$masterData);
			$purchaseId = $purchaseInvSave['insert_id'];			
			
			$result = ['status'=>1,'message'=>'GIR saved successfully.','url'=>base_url("gir")];			
		else:
			$data = Array();
			$data['tableName'] = $this->girTable;
			$data['where']['id'] = $purchaseId;
			$girData = $this->row($data);

			$this->store($this->girTable,$masterData);
			
			$data = Array();
			$data['where']['gir_id'] = $purchaseId;
			$data['tableName'] = $this->girItemTable;
			$ptransArray = $this->rows($data);
			// print_r($ptransArray);exit;
			
			foreach($ptransArray as $value):
				if(!in_array($value->id,$itemData['id'])):						
					$this->trash($this->girItemTable,['id'=>$value->id]);
				endif;

				$this->remove($this->stockTrans,['ref_id'=>$value->id,'ref_type'=>1,'item_id'=>$value->item_id]);

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
			
			$result = ['status'=>1,'message'=>'GIR updated successfully.','url'=>base_url("gir")];
		endif;

		//save purchase items
		foreach($itemData['item_id'] as $key=>$value):
			$transData = [
							'id' => $itemData['id'][$key],
							'gir_id' => $purchaseId,
							'po_trans_id' => $itemData['po_trans_id'][$key],
							'po_id' => $itemData['po_id'][$key],
							'item_id' => $value,
							'item_code' => $itemData['item_code'][$key],
							'item_type' => $itemData['item_type'][$key],
							'batch_stock' => $itemData['batch_stock'][$key],
							'unit_id' => $itemData['unit_id'][$key],
							'order_qty' => $itemData['order_qty'][$key],
							'inward_qty' => $itemData['inward_qty'][$key],
							'qty' => $itemData['qty'][$key],
							'qty_kg' => $itemData['qty_kg'][$key],
							'short_qty' => ($itemData['inward_qty'][$key] - $itemData['qty'][$key]),
							'batch_no' => (!empty($itemData['batch_no'][$key]))?$itemData['batch_no'][$key]:"General Batch",
							'location_id' => $itemData['location_id'][$key],
							'heat_no' => $itemData['heat_no'][$key],
							'forging_tracebility' => $itemData['forging_tracebility'][$key],
							'heat_tracebility' => $itemData['heat_tracebility'][$key],
							'serial_no' => $itemData['serial_no'][$key],
							'price' => $itemData['price'][$key],
							'created_by' => $itemData['created_by']
						]; 
			if($masterData['type'] == 2):
				$transData['order_qty'] = $itemData['qty'][$key];
				$transData['inward_qty'] = $itemData['qty'][$key];	
				$transData['short_qty'] = 0;	
			endif;

			$transRowSave = $this->store($this->girItemTable,$transData);
			$trans_id = 0;
			$trans_id = (!empty($itemData['id'][$key])) ? $itemData['id'][$key] : $transRowSave['insert_id'];
				
			/*** UPDATE STOCK TRANSACTION DATA ***/
			$stockQueryData['id']="";
			$stockQueryData['location_id']=$itemData['location_id'][$key];
			if(!empty($itemData['batch_no'][$key])){$stockQueryData['batch_no'] = $itemData['batch_no'][$key];}
			$stockQueryData['trans_type']=1;
			$stockQueryData['item_id']=$value;
			$stockQueryData['qty']=$itemData['qty'][$key];
			$stockQueryData['ref_type']=1;
			$stockQueryData['ref_id']=$trans_id;
			$stockQueryData['ref_no']=getPrefixNumber($masterData['gir_prefix'],$masterData['gir_no']);
			$stockQueryData['ref_date']=$masterData['gir_date'];
			$stockQueryData['ref_batch']=$itemData['heat_no'][$key];
			$stockQueryData['created_by']=$masterData['created_by'];
			$stockQueryData['stock_effect'] = ($masterData['type'] == 1)?0:1;
			$this->store($this->stockTrans,$stockQueryData);
			
			if(!empty($itemData['po_trans_id'][$key])):
				$setData['tableName'] = $this->purchaseOrderTrans;
				$setData['where']['id'] = $itemData['po_trans_id'][$key];
				$setData['set']['rec_qty'] = 'rec_qty, + '.$itemData['qty'][$key];
				$this->setValue($setData);
				
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
	
	public function getPoTransactionRow($id)
	{
		$data['tableName'] = $this->purchaseOrderTrans;
		$data['where']['id'] = $id;
		return $this->row($data);
	}

	public function editInv($id)
	{
		$data['tableName'] = $this->girTable;
		$data['select'] = "gir_master.*,party_master.party_name";
		$data['leftJoin']['party_master'] = "party_master.id = gir_master.party_id";
		$data['where']['gir_master.id'] = $id;
		$result = $this->row($data);
		// print_r($id);
		$data = array();
		$data['select'] = "gir_transaction.*,item_master.item_name,item_master.item_code,unit_master.unit_name";
		$data['join']['item_master'] = "item_master.id = gir_transaction.item_id";
		$data['leftJoin']['unit_master'] = "unit_master.id = gir_transaction.unit_id";
		$data['where']['gir_transaction.gir_id'] = $id;
		$data['tableName'] = $this->girItemTable;
		$result->itemData = $this->rows($data);
		return $result;
	}

	public function delete($id)
	{
		$girData = $this->editInv($id);
		foreach ($girData->itemData as $row) :
			$this->remove($this->stockTrans, ['ref_id' => $row->id, 'ref_type' => 1, 'item_id' => $row->item_id]);

			if (!empty($row->po_trans_id)) :
				$setData = array();
				$setData['tableName'] = $this->purchaseOrderTrans;
				$setData['where']['id'] = $row->po_trans_id;
				$setData['set']['rec_qty'] = 'rec_qty, - ' . $row->qty;
				$qryresult = $this->setValue($setData);

				/** If Po Order Qty is Complete then Close PO **/
				$poTrans = $this->getPoTransactionRow($row->po_trans_id);
				if ($poTrans->rec_qty >= $poTrans->qty) :
					$this->store($this->purchaseOrderTrans, ["id" => $row->po_trans_id, "order_status" => 1]);
				else :
					$this->store($this->purchaseOrderTrans, ["id" => $row->po_trans_id, "order_status" => 0]);
				endif;
			endif;
			$this->trash($this->girItemTable, ['id' => $row->id]);
		endforeach;

		if (!empty($girData->order_id)) :
			$poIds = explode(",", $girData->order_id);
			foreach ($poIds as $key => $value) :
				$queryData = array();
				$queryData['tableName'] = $this->purchaseOrderTrans;
				$queryData['select'] = "COUNT(id) as pendingItems";
				$queryData['where']['order_id'] = $value;
				$queryData['where']['order_status'] = 0;
				$pendingItems = $this->specificRow($queryData)->pendingItems;
				if (empty($pendingItems)) :
					$this->store($this->purchaseOrderMaster, ['id' => $value, 'order_status' => 1]);
				else :
					$this->store($this->purchaseOrderMaster, ['id' => $value, 'order_status' => 0]);
				endif;
			endforeach;
		endif;

		return $this->trash($this->girTable, ['id' => $id], 'GIR');
	}

	public function getInspectedMaterial($id)
	{
		$data['select'] = "gir_transaction.*,item_master.item_name,item_master.item_code,unit_master.unit_name";
		$data['join']['item_master'] = "item_master.id = gir_transaction.item_id";
		$data['leftJoin']['unit_master'] = "unit_master.id = gir_transaction.unit_id";
		$data['where']['gir_transaction.id'] = $id;
		$data['tableName'] = $this->girItemTable;
		$result = $this->rows($data);

		if (!empty($result)) :
			$i = 1;
			$html = "";
			foreach ($result as $row) :
				$data = array();
				$data['tableName'] = 'purchase_inspection';
				$data['where']['ptrans_id'] = $row->id;
				$data['where']['gir_id'] = $row->gir_id;
				$data['where']['is_delete'] = 0;
				$inspectedData = $this->row($data);

				$readonly = ($row->inspected_qty != $row->remaining_qty) ? "readonly" : "";
				$disable = ($row->inspected_qty != $row->remaining_qty) ? "disabled" : "";
				$inspOptions = '<option value="">Select Status</option>';
				if (!empty($inspectedData)) :
					$inspected_id = $inspectedData->id;
					$inspected_qty = $inspectedData->inspected_qty;
					$ud_qty = $inspectedData->ud_qty;
					$reject_qty = $inspectedData->reject_qty;
					$scrape_qty = $inspectedData->scrape_qty;
					$short_qty = $inspectedData->short_qty;
					$rejection_qty = $inspectedData->rejection_qty;
					if ($inspectedData->inspection_status == "1") :
						$inspOptions = '<option value="1" selected ' . $disable . '>Ok</option><option value="2" ' . $disable . '>Not Ok</option>';
					else :
						$inspOptions = '<option value="1" ' . $disable . '>Ok</option><option value="2" selected ' . $disable . '>Not Ok</option>';
					endif;
				else :
					$inspected_id = "";
					$inspected_qty = $row->qty;
					$ud_qty = 0;
					$reject_qty = 0;
					$scrape_qty = 0;
					$short_qty = 0;
					$rejection_qty = 0;
					$inspOptions = '<option value="1" ' . $disable . '>Ok</option><option value="2" ' . $disable . '>Not Ok</option>';
				endif;

				$html .= '<tr class="text-center">
							<td>' . $i . '</td>
							<td>
							    ' . $row->batch_no . '
								<input type="hidden" name="batch_no[]" id="batch_no' . $i . '" value="' . $row->batch_no . '">
							</td>
							<td>
								' . $row->qty . '
								<input type="hidden" name="recived_qty[]" id="recived_qty' . $i . '" value="' . $row->qty . '">
								<input type="hidden" name="inspected_id[]" id="inspected_id' . $i . '" value="' . $inspected_id . '">
								<input type="hidden" name="item_id[]" id="item_id' . $i . '" value="' . $row->item_id . '">
								<input type="hidden" name="ptrans_id[]" id="ptrans_id' . $i . '" value="' . $row->id . '">
								<input type="hidden" name="ud_qty[]" id="ud_qty' . $i . '" class="form-control floatOnly" value="' . $ud_qty . '">
								<input type="hidden" name="reject_qty[]" id="reject_qty' . $i . '" class="form-control floatOnly" value="' . $reject_qty . '">
								<input type="hidden" name="scrape_qty[]" id="scrape_qty' . $i . '" class="form-control floatOnly" value="' . $scrape_qty . '">
								<input type="hidden" name="inspected_qty[]" id="inspected_qty' . $i . '" class="form-control floatOnly" value="' . $inspected_qty . '">
								<div class="error recived_qty' . $i . '"></div>
							</td>
							<td>
								<select name="inspection_status[]" id="inspection_status' . $i . '" class="form-control">' . $inspOptions . '</select>
								<div class="error inspection_status' . $i . '"></div>
							</td>
							<td>
								<input type="number" name="short_qty[]" id="short_qty' . $i . '" class="form-control floatOnly" value="' . $short_qty . '" ' . $readonly . '>
								<div class="error short_qty' . $i . '"></div>
							</td>
							<td>
								<input type="number" name="rejection_qty[]" id="rejection_qty' . $i . '" class="form-control floatOnly" value="' . $rejection_qty . '" ' . $readonly . '>
								<div class="error rejection_qty' . $i . '"></div>
							</td>
						</tr>';
				$i++;
			endforeach;
		else :
			$html = '<tr><td class="text-center" colspan="7">No data available in table</td></tr>';
		endif;
		return $html;
	}

	public function inspectedMaterialSave00($data)
	{

		$girData = $this->editInv($data['gir_id']);
		foreach ($data['item_id'] as $key => $value) :
			$inspected_qty = ($data['inspection_status'][$key] == "Ok") ? ($data['recived_qty'][$key] - $data['short_qty'][$key]) : 0;
			$reject_qty = ($data['inspection_status'][$key] != "Ok") ? $data['recived_qty'][$key] : 0;
			$data['short_qty'][$key] = ($data['inspection_status'][$key] == "Ok") ? $data['short_qty'][$key] : 0;
			if (empty($data['inspected_id'][$key])) :
				$dataRow = [
					'id' => "",
					'inspection_date' => date('Y-m-d'),
					'gir_id' => $data['gir_id'],
					'ptrans_id' => $data['ptrans_id'][$key],
					'item_id' => $value,
					'recived_qty' => $data['recived_qty'][$key],
					'inspection_status' => $data['inspection_status'][$key],
					'inspected_qty' => $inspected_qty,
					'ud_qty' => $data['ud_qty'][$key],
					'reject_qty' => $reject_qty,
					'scrape_qty' => $data['scrape_qty'][$key],
					'short_qty' => $data['short_qty'][$key],
					'created_by' => $data['created_by'],
					'ref_batch'=>$data['party_id'][$key]
				];
				$this->store('purchase_inspection', $dataRow);

				if ($data['inspection_status'][$key] == "Ok") :
					/** Update Item Stock **/
					$setData = array();
					$setData['tableName'] = $this->itemMaster;
					$setData['where']['id'] = $value;
					$setData['set']['qty'] = 'qty, + ' . $inspected_qty;
					$setData['set']['reject_qty'] = 'reject_qty, + ' . $reject_qty;
					$setData['set']['scrape_qty'] = 'scrape_qty, + ' . $data['scrape_qty'][$key];
					$setData['set']['short_qty'] = 'short_qty, + ' . $data['short_qty'][$key];
					$qryresult = $this->setValue($setData);

					/*** UPDATE STOCK TRANSACTION DATA ***/
					if ($data['inspected_qty'][$key] > 0) :
						$ptransItem = $this->getgirItemTableRow($data['ptrans_id'][$key]);

						if (!empty($ptransItem)) :
							$stockQueryData['id'] = "";
							$stockQueryData['location_id'] = $this->GIR_STORE->id;
							if (!empty($ptransItem->batch_no)) {
								$stockQueryData['batch_no'] = $ptransItem->batch_no;
							}
							$stockQueryData['trans_type'] = 1;
							$stockQueryData['item_id'] = $ptransItem->item_id;
							$stockQueryData['qty'] = $inspected_qty;
							$stockQueryData['ref_type'] = 1;
							$stockQueryData['ref_id'] = $data['ptrans_id'][$key];
							$stockQueryData['ref_no'] = getPrefixNumber($girData->gir_prefix, $girData->gir_no);
							$stockQueryData['ref_date'] = $girData->gir_date;
							$stockQueryData['created_by'] = $this->session->userdata('loginId');
							$this->store($this->stockTrans, $stockQueryData);
						endif;
					endif;
				endif;

				$girItemTableData = [
					'id' => $data['ptrans_id'][$key],
					'inspected_qty' => $data['recived_qty'][$key],
					'remaining_qty' => $data['recived_qty'][$key]
				];
				$this->store($this->girItemTable, $girItemTableData);
			else :
				$queryData['tableName'] = "purchase_inspection";
				$queryData['where']['id'] = $data['inspected_id'][$key];
				$inspectedData = $this->row($queryData);

				if ($inspectedData->inspection_status == "Ok") :
					/** Update Item Stock **/
					$setData = array();
					$setData['tableName'] = $this->itemMaster;
					$setData['where']['id'] = $value;
					$setData['set']['qty'] = 'qty, - ' . $inspectedData->inspected_qty;
					$setData['set']['reject_qty'] = 'reject_qty, - ' . $inspectedData->reject_qty;
					$setData['set']['scrape_qty'] = 'scrape_qty, - ' . $inspectedData->scrape_qty;
					$setData['set']['short_qty'] = 'short_qty, - ' . $inspectedData->short_qty;
					$qryresult = $this->setValue($setData);

					/*** UPDATE STOCK TRANSACTION DATA ***/
					$this->remove($this->stockTrans, ['ref_id' => $inspectedData->ptrans_id, 'ref_type' => 1, 'item_id' => $inspectedData->item_id]);
				endif;

				$dataRow = [
					'id' => $data['inspected_id'][$key],
					'inspection_date' => date('Y-m-d'),
					'gir_id' => $data['gir_id'],
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
				$this->store('purchase_inspection', $dataRow);

				if ($data['inspection_status'][$key] == "Ok") :
					/** Update Item Stock **/
					$setData = array();
					$setData['tableName'] = $this->itemMaster;
					$setData['where']['id'] = $value;
					$setData['set']['qty'] = 'qty, + ' . $inspected_qty;
					$setData['set']['reject_qty'] = 'reject_qty, + ' . $reject_qty;
					$setData['set']['scrape_qty'] = 'scrape_qty, + ' . $data['scrape_qty'][$key];
					$setData['set']['short_qty'] = 'short_qty, + ' . $data['short_qty'][$key];
					$qryresult = $this->setValue($setData);

					/*** UPDATE STOCK TRANSACTION DATA ***/
					if ($inspected_qty > 0) :
						$ptransItem = $this->getgirItemTableRow($data['ptrans_id'][$key]);
						// $girData = $this->editInv($data['ptrans_id'][$key]);
						if (!empty($ptransItem)) :
							$stockQueryData['id'] = "";
							$stockQueryData['location_id'] = $ptransItem->location_id;
							if (!empty($ptransItem->batch_no)) {
								$stockQueryData['batch_no'] = $ptransItem->batch_no;
							}
							$stockQueryData['trans_type'] = 1;
							$stockQueryData['item_id'] = $ptransItem->item_id;
							$stockQueryData['qty'] = $inspected_qty;
							$stockQueryData['ref_type'] = 1;
							$stockQueryData['ref_id'] = $data['ptrans_id'][$key];
							$stockQueryData['ref_no'] = getPrefixNumber($girData->gir_prefix, $girData->gir_no);
							$stockQueryData['ref_date'] = $girData->gir_date;
							$stockQueryData['created_by'] = $this->session->userdata('loginId');
							$this->store($this->stockTrans, $stockQueryData);
						endif;
					endif;
				endif;

				$girItemTableData = [
					'id' => $data['ptrans_id'][$key],
					'inspected_qty' => $data['recived_qty'][$key],
					'remaining_qty' => $data['recived_qty'][$key]
				];
				$this->store($this->girItemTable, $girItemTableData);
			endif;
		endforeach;

		return ['status' => 1, 'message' => 'Inspected Material saved successfully.'];
	}
	public function inspectedMaterialSave($data)
	{

		// print_r($data);exit;
		foreach ($data['item_id'] as $key => $value) :
			$girItemTableData = [
				'id' => $data['ptrans_id'][$key],
				'short_qty' => $data['short_qty'][$key],
				'rejection_qty' => $data['rejection_qty'][$key],
				'insp_status' => $data['inspection_status'][$key],
				'qc_status' => 1
			];
			$this->store($this->girItemTable, $girItemTableData);
			if (($data['inspection_status'][$key]) == 1) :
				/** Stock Transaction */
				$dataRow = [
					'id' => '',
					'location_id' => $this->GIR_STORE->id,
					'batch_no'=> $data['batch_no'][$key],
					'trans_type' => 1,
					'item_id' => $value,
					'qty' => ($data['recived_qty'][$key] - $data['short_qty'][$key] - $data['rejection_qty'][$key]),
					'ref_type' => 1,
					'ref_id' => $data['ptrans_id'][$key],
					'ref_no' => $data['gir_id'],
					'ref_date' => $data['gir_date'],
					'created_by'=>$data['created_by']
				];
				// print_r($dataRow);
				$this->store($this->stockTrans, $dataRow);
				// print_r($this->db->last_query());
				/** Item Master Stock Update */
				$setData = array();
				$setData['tableName'] = $this->itemMaster;
				$setData['where']['id'] = $value;
				$setData['set']['qty'] = 'qty, + ' . ($data['recived_qty'][$key] - $data['short_qty'][$key] - $data['rejection_qty'][$key]);

				$qryresult = $this->setValue($setData);
			endif;
		endforeach;

		return ['status' => 1, 'message' => 'Inspected Material saved successfully.'];
	}

	public function getLotWisegirItemTables()
	{
		$queryData['select'] = "gir_transaction.id,gir_transaction.batch_no,gir_transaction.remaining_qty,gir_transaction.qty,gir_transaction.item_id,item_master.item_name,unit_master.unit_name";
		$queryData['join']['item_master'] = "gir_transaction.item_id = item_master.id";
		$queryData['join']['unit_master'] = "gir_transaction.unit_id = unit_master.id";
		$queryData['where']['gir_transaction.remaining_qty !='] = "0.000";
		$queryData['where']['item_master.rm_type'] = 1;
		$queryData['tableName'] = $this->girItemTable;
		return $this->rows($queryData);
	}

	public function getgirItemTableRow($id)
	{
		$data['tableName'] = $this->girItemTable;
		$data['where']['id'] = $id;
		return $this->row($data);
	}

	public function getItemsForGIR($party_id)
	{

		$itemOptions = '<option value="">Select Product Name</option>';

		$qdata['tableName'] = $this->purchaseOrderTrans;
		$qdata['select'] = "purchase_order_trans.*,item_master.item_name as itmname,item_master.item_code,item_master.unit_id,item_master.hsn_code, item_master.gst_per,item_master.price,unit_master.unit_name,purchase_order_master.po_no";
		$qdata['join']['purchase_order_master'] = "purchase_order_master.id = purchase_order_trans.order_id";
		$qdata['join']['item_master'] = "item_master.id = purchase_order_trans.item_id";
		$qdata['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$qdata['where']['purchase_order_master.party_id'] = $party_id;
		$qdata['where']['purchase_order_trans.order_status != '] = 2;
		$qdata['customWhere'][] = "purchase_order_trans.qty > purchase_order_trans.rec_qty";
		$itemData = $this->rows($qdata);
		$selectedIds = array();
		if (!empty($itemData)) :
			foreach ($itemData as $row) :
				$dataRow = json_encode($row);
				$dataRow = "data-data_row='" . $dataRow . "'";
				$year = (date('m') > 3) ? date('y') . '-' . (date('y') + 1) : (date('y') - 1) . '-' . date('y');
				$itemOptions .= '<option value="' . $row->item_id . '" data-po_trans_id="' . $row->id . '" data-year="' . $year . '" ' . $dataRow . ' >[' . $row->item_code . '] ' . $row->itmname . ' [PO/' . $row->po_no . '/' . $year . ']</option>';
			endforeach;
		endif;
		$itemData = $this->item->getItemList();
		if (!empty($itemData)) :
			foreach ($itemData as $row) :
				if (!in_array($row->id, $selectedIds)) :
					$dataRow = json_encode($row);
					$dataRow = "data-data_row='" . $dataRow . "'";
					$itemOptions .= '<option value="' . $row->id . '" data-po_trans_id="" data-year="" ' . $dataRow . ' >[' . $row->item_code . '] ' . $row->item_name . ' </option>';
				endif;
			endforeach;
		endif;

		return $itemOptions;
	}

	public function getItemList($type = 0)
	{
		$data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.*,unit_master.unit_name";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		if (!empty($type))
			$data['where']['item_master.item_type'] = $type;
		return $this->rows($data);
	}

	public function itemColorCode()
	{
		$data['tableName'] = $this->girItemTable;
		$data['select'] = 'color_code';
		$result = $this->rows($data);
		$searchResult = array();
		foreach ($result as $row) {
			$searchResult[] = $row->color_code;
		}
		return  $searchResult;
	}

	public function getCustomerGir($party_id)
	{
		$data['tableName'] = $this->girTable;
		$data['select'] = "id,gir_prefix,gir_no,gir_date";
		$data['where']['party_id'] = $party_id;
		$data['where']['type'] = 2;
		$result = $this->rows($data);
		return $result;
	}

	public function getGirItems($id)
	{
		$data = array();
		$data['select'] = "gir_transaction.id,gir_transaction.item_id,gir_transaction.remaining_qty,item_master.item_name,item_master.item_code,unit_master.unit_name";
		$data['join']['item_master'] = "item_master.id = gir_transaction.item_id";
		$data['leftJoin']['unit_master'] = "unit_master.id = gir_transaction.unit_id";
		$data['where']['gir_transaction.gir_id'] = $id;
		$data['tableName'] = $this->girItemTable;
		$result = $this->rows($data);
		return $result;
	}

	public function getGirTransactions($id){
        $queryData['tableName'] = $this->purchaseOrderTrans;
		$queryData['select'] = "purchase_order_trans.*,purchase_order_master.po_no,purchase_order_master.po_prefix,purchase_order_master.po_date,purchase_order_master.party_id,purchase_order_master.net_amount,party_master.party_name,item_master.item_name";
        $queryData['join']['purchase_order_master'] = "purchase_order_master.id = purchase_order_trans.order_id";
        $queryData['join']['party_master'] = "purchase_order_master.party_id = party_master.id";
        $queryData['join']['item_master'] = "item_master.id = purchase_order_trans.item_id";
        $queryData['where']['purchase_order_trans.order_id'] = $id;
        $queryData['where']['purchase_order_master.order_status'] = 0;
        $result = $this->rows($queryData);  
		return $result;
    }

	public function getGirOrders($id){
        $queryData['tableName'] = $this->purchaseOrderMaster;
        $queryData['where']['order_status'] = 0;
        //$queryData['where']['is_approve != '] = 0;
        $queryData['where']['party_id'] = $id;
        $resultData = $this->rows($queryData);  //print_r($resultData);exit;
        
        $html="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):   
                $partCode = array(); $qty = array();
                $partData = $this->getGirTransactions($row->id);
                foreach($partData as $part):
                    $partCode[] = $part->item_name; 
                    $qty[] = $part->qty; 
                endforeach;
                $part_code = implode(",<br> ",$partCode); $part_qty = implode(",<br> ",$qty);
                
                $html .= '<tr>
                            <td class="text-center">
                                <input type="checkbox" id="md_checkbox_'.$i.'" name="ref_id[]" class="filled-in chk-col-success" value="'.$row->id.'"  ><label for="md_checkbox_'.$i.'" class="mr-3"></label>
                            </td>
                            <td class="text-center">'.getPrefixNumber($row->po_prefix,$row->po_no).'</td>
                            <td class="text-center">'.formatDate($row->po_date).'</td>
                            <td class="text-center">'.$part_code.'</td>
                           
                            <td class="text-center">'.$part_qty.'</td>
                          </tr>'; 
                $i++;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="5">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }

	public function getNextBatchOrSerialNo($data){
		$result = array();
		if(!empty($data['trans_id'])):
			$queryData['select'] = "serial_no,heat_no";
			$queryData['tableName'] = $this->girItemTable;
			$queryData['where']['id'] = $data['trans_id'];
			$result = $this->row($queryData);

			if(!empty($result->serial_no) && $data['heat_no'] == $result->heat_no):
				return $result->serial_no;
			endif;
			
		endif;
		
		if(!empty($data['batch_stock']) && $data['batch_stock'] == 1):
            $queryData['select'] = "serial_no,heat_no";
			$queryData['tableName'] = $this->girItemTable;
			$queryData['where']['heat_no'] = $data['heat_no'];
			$result = $this->row($queryData);
			
			if(!empty($result->serial_no)):
				return $result->serial_no;
			endif;
		endif;

		$queryData = array();
		$queryData['select'] = "MAX(serial_no) as serial_no";
		$queryData['tableName'] = $this->girItemTable;
		$queryData['where']['item_id'] = $data['item_id'];
		/*if(!empty($data['batch_stock']) && $data['batch_stock'] == 1):
			$queryData['where']['heat_no'] = $data['heat_no'];
		endif;*/
		$queryData['where']['is_delete'] = 0;
		$queryData['where']['YEAR(created_at)'] = date("Y");
		$serial_no = $this->specificRow($queryData)->serial_no;
		$nextSerialNo = (!empty($serial_no)) ? ($serial_no + 1) : 1;
		return $nextSerialNo;
		
	}
}
