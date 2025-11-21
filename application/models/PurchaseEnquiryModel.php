<?php
class PurchaseEnquiryModel extends MasterModel
{
	private $purchaseEnquiryMaster = "purchase_enquiry";
	private $purchaseEnquiryTrans = "purchase_enquiry_transaction";

	public function nextEnqNo()
	{
		$data['tableName'] = $this->purchaseEnquiryMaster;
		$data['select'] = "MAX(enq_no) as enq_no";
		$maxNo = $this->specificRow($data)->enq_no;
		$nextEnqNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
		return $nextEnqNo;
	}

	public function getDTRows($data)
	{
		$data['tableName'] = $this->purchaseEnquiryTrans;
		$data['select'] = "purchase_enquiry_transaction.*,purchase_enquiry.enq_no,purchase_enquiry.sub_enq_no,purchase_enquiry.enq_prefix,purchase_enquiry.enq_date,purchase_enquiry.supplier_id,purchase_enquiry.supplier_name,purchase_enquiry.supplier_name,purchase_enquiry.enq_ref_date,purchase_enquiry.enq_status";
		$data['join']['purchase_enquiry'] = "purchase_enquiry.id = purchase_enquiry_transaction.ref_id";;
		if(isset($data['entry_type']) && $data['entry_type'] == 2){
			$data['where']['purchase_enquiry.entry_type'] = 2;
			$data['where']['purchase_enquiry_transaction.from_ref_id'] = $data['ref_id'];
		}else{
			$data['where']['purchase_enquiry.entry_type'] = 1;
		}
		$data['searchCol'][] = "CONCAT(purchase_enquiry.enq_prefix,purchase_enquiry.enq_no)";
		$data['searchCol'][] = "DATE_FORMAT(purchase_enquiry.enq_date, '%d-%m-%Y')";
		$data['searchCol'][] = "purchase_enquiry.supplier_name";
		$data['searchCol'][] = "purchase_enquiry_transaction.item_name";
		$data['searchCol'][] = "purchase_enquiry_transaction.confirm_qty";
		$data['searchCol'][] = "purchase_enquiry_transaction.confirm_rate";
		$data['searchCol'][] = "DATE_FORMAT(purchase_enquiry.enq_date, '%d-%m-%Y')";
		$data['searchCol'][] = "remark";

		$columns = array('', '', 'purchase_enquiry.enq_no', 'purchase_enquiry.enq_date', 'purchase_enquiry.supplier_name', 'purchase_enquiry_transaction.item_name', 'purchase_enquiry_transaction.confirm_qty', 'purchase_enquiry_transaction.confirm_rate', '', '', 'purchase_enquiry.remark');
		if (isset($data['order'])) {
			$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
		}

		return $this->pagingRows($data);
	}

	/* public function getDTRows($data){
        $data['tableName'] = $this->purchaseEnquiryTrans;        
        $data['searchCol'][] = "DATE_FORMAT(enq_date, '%d-%m-%Y')";
        $data['searchCol'][] = "supplier_name";
        $data['searchCol'][] = "remark";    
        $data['searchCol'][] = "DATE_FORMAT(enq_ref_date, '%d-%m-%Y')";    
        $data['searchCol'][] = "CONCAT(enq_prefix,enq_no)";
		$columns =array('','','enq_no','enq_date','supplier_name','enq_ref_date','status','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    } */

	public function getEnquiry($id)
	{
		$data['tableName'] = $this->purchaseEnquiryMaster;
		$data['where']['id'] = $id;
		$result = $this->row($data);

		$result->itemData = $this->getEnquiryTrans($id);
		return $result;
	}

	public function getEnquiryTrans($id)
	{
		$data['select'] = "purchase_enquiry_transaction.*,unit_master.unit_name,unit_master.description,item_master.item_code,item_master.hsn_code,item_master.gst_per";
		$data['join']['unit_master'] = "unit_master.id = purchase_enquiry_transaction.unit_id";
		$data['leftJoin']['item_master'] = "item_master.id = purchase_enquiry_transaction.item_id";
		$data['where']['purchase_enquiry_transaction.ref_id'] = $id;
		$data['tableName'] = $this->purchaseEnquiryTrans;
		$result = $this->rows($data);
		return $result;
	}

	public function getEnquiryTransPenddingConfirm($id)
	{
		$data['where']['ref_id'] = $id;
		$data['where']['confirm_status'] = 0;
		$data['tableName'] = $this->purchaseEnquiryTrans;
		$result = $this->numRows($data);
		return $result;
	}

	public function getEnquiryTransConfirm($id)
	{
		$data['where']['ref_id'] = $id;
		$data['where']['confirm_status'] = 1;
		$data['tableName'] = $this->purchaseEnquiryTrans;
		$result = $this->numRows($data);
		return $result;
	}

	public function save($masterData, $itemData)
	{
		try {
            $this->db->trans_begin();
			$orderId = $masterData['id'];
			$req_id = $masterData['req_id'];
			unset($masterData['req_id']);

			if ($this->checkDuplicateEnquiry($masterData['supplier_name'], $masterData['enq_no'], $orderId) > 0) :
				$errorMessage['enq_no'] = "Enquiry No. is duplicate.";
				return ['status' => 0, 'message' => $errorMessage];
			else :
				$supplierId = explode(",",$masterData['supplier_id']);
				$totalSupplier = count($supplierId);
				$sub_enq_no ='A';
				if(!empty($masterData['id'])){
					$ptransIdArray = $this->getEnquiryTrans($masterData['id']);
					foreach ($ptransIdArray as $key => $value) :
						if (!in_array($value->id, $itemData['id'])) :
							$this->trash($this->purchaseEnquiryTrans, ['id' => $value->id]);
						endif;
					endforeach;
				}
				foreach($supplierId as $spl){
					//save purchase enquiry data
					$partyData = $this->party->getParty($spl);
					$enqMaster =[
						'id'=>$masterData['id'],
						'enq_prefix' => $masterData['enq_prefix'],
						'enq_no'=>$masterData['enq_no'], 
						'ref_id'=>$masterData['ref_id'], 
						'entry_type'=>$masterData['entry_type'],
						'enq_date' => date('Y-m-d',strtotime($masterData['enq_date'])), 
						'supplier_id' => $spl,
						'supplier_name' =>$partyData->party_name,
						'remark' => $masterData['remark'],
						'created_by' => $masterData['created_by']
					];
					if(empty($masterData['id'])){
						
						if($totalSupplier > 1){
							$enqMaster['sub_enq_no'] = $sub_enq_no;
						}else{
							$enqMaster['sub_enq_no'] ='';
						}
						$sub_enq_no++;
					}
					$enquirySave = $this->store($this->purchaseEnquiryMaster,$enqMaster);
					$ordId = !empty($masterData['id'])?$masterData['id']:$enquirySave['insert_id'];

					//save purchase items
					foreach ($itemData['item_name'] as $key => $value) :
						$transData = [
							'id' => $itemData['id'][$key],
							'ref_id' => $ordId,
							'from_ref_id' => $masterData['ref_id'],
							'item_name' => $value,
							'item_type' => $itemData['item_type'][$key],
							'fgitem_id' => $itemData['fgitem_id'][$key],
							'fgitem_name' => $itemData['fgitem_name'][$key],
							'unit_id' => $itemData['unit_id'][$key],
							'qty' => $itemData['qty'][$key],
							'item_remark' => $itemData['item_remark'][$key],
							'created_by' => $itemData['created_by']
						];
						$this->store($this->purchaseEnquiryTrans, $transData);
					endforeach;

					if (!empty($req_id)) {
						$ref_id = explode('~', $req_id);
						foreach ($ref_id as $row) {
							$this->store("requisition_log", ["id" => $row, "from_ref" => 1, "order_status" => 1]);
						}
					}
					
				}
			endif;
			$path =  base_url("purchaseEnquiry");
			if($masterData['entry_type'] == 2 && !empty($masterData['ref_id'])){
				$path = base_url('salesEnquiry/rfq/'.$masterData['ref_id']);
			}
					
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status' => 1, 'message' => 'Purchase Enquiry saved successfully.', 'url' =>$path];
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}	
	}

	public function checkDuplicateEnquiry($supplier_name, $enq_no, $id = "")
	{
		$data['tableName'] = $this->purchaseEnquiryMaster;
		$data['where']['supplier_name'] = $supplier_name;
		$data['where']['enq_no'] = $enq_no;
		if (!empty($id))
			$data['where']['id != '] = $id;
		return $this->numRows($data);
	}

	public function deleteEnquiry($id)
	{
		//enquiry transation delete
		$where['ref_id'] = $id;
		$this->trash($this->purchaseEnquiryTrans, $where);

		//enquiry master delete
		return $this->trash($this->purchaseEnquiryMaster, ['id' => $id], 'Purchase Enquiry');
	}

	public function getEnquiryData($enq_id)
	{
		$data = array();
		$data['tableName'] = $this->purchaseEnquiryTrans;
		$data['select'] = "purchase_enquiry_transaction.*,unit_master.unit_name,unit_master.description";
		$data['join']['unit_master'] = "unit_master.id = purchase_enquiry_transaction.unit_id";
		$data['where']['purchase_enquiry_transaction.ref_id'] = $enq_id;
		$result = $this->rows($data);

		if (!empty($result)) :
			$i = 1;
			$html = "";
			foreach ($result as $row) :
				if (empty($row->confirm_status)) :
					$checked = "";
					$disabled = "disabled";
					$html .= '<tr>
							<td class="text-center">
								<input type="checkbox" id="md_checkbox' . $i . '" class="filled-in chk-col-success itemCheck" data-rowid="' . $i . '" check="' . $checked . '" ' . $checked . ' />
								<label for="md_checkbox' . $i . '">' . $i . '</label>
							</td>
							<td>
								' . $row->item_name . '
								<input type="hidden" name="item_name[]" id="item_name' . $i . '" class="form-control" value="' . $row->item_name . '" ' . $disabled . ' />
								<input type="hidden" name="trans_id[]" id="trans_id' . $i . '" class="form-control" value="' . $row->id . '" ' . $disabled . ' />
							</td>
							<td>
								<input type="number" name="qty[]" id="qty' . $i . '" class="form-control floatOnly" value="' . $row->qty . '" min="0" ' . $disabled . ' / readOnly>
								<div class="error qty' . $row->id . '"></div>
							</td>
							<td>
								<select name="feasible[]" id="feasible' . $i . '"  class="form-control floatOnly" '  . $disabled . ' >
									<option value="1">Yes</option>
									<option value="2">No</option>
								</select>
								
								<div class="error qty' . $row->id . '"></div>
							</td>
							<td>
								<input type="text" name="rate[]" id="rate' . $i . '" class="form-control floatOnly" value="0" min="0" ' . $disabled . ' />
								<div class="error rate' . $row->id . '"></div>
							</td>
							<td>
								<input type="text" name="quote_no[]" id="quote_no' . $i . '" class="form-control" value="0" min="0" ' . $disabled . ' />
							</td>
							<td>
								<input type="date" name="quote_date[]" id="quote_date' . $i . '" class="form-control " value="0" min="0" ' . $disabled . ' />
							</td>
							<td>
								<input type="text" name="quote_remark[]" id="quote_remark' . $i . '" class="form-control" value="0" min="0" ' . $disabled . ' />
							</td>
						</tr>';
				else :
					$data = array();
					$data['tableName'] = 'item_master';
					$data['where']['id'] = $row->item_id;
					$itemData = $this->row($data);

					$checked = "checked";
					$disabled = "disabled";
					$html .= '<tr>
							<td class="text-center">
								<input type="checkbox" id="md_checkbox' . $i . '" class="filled-in chk-col-success itemCheck" data-rowid="' . $i . '" check="' . $checked . '" ' . $checked . ' ' . $disabled . ' />
								<label for="md_checkbox' . $i . '">' . $i . '</label>
							</td>
							<td>
								' . $itemData->item_name . '
								<input type="hidden" name="item_name[]" id="item_name' . $i . '" class="form-control" value="' . $itemData->item_name . '" ' . $disabled . ' />
								<input type="hidden" name="trans_id[]" id="trans_id' . $i . '" class="form-control" value="' . $row->id . '" ' . $disabled . ' />
							</td>
							<td>
								<input type="number" name="qty[]" id="qty' . $i . '" class="form-control floatOnly" value="' . $row->confirm_qty . '" min="0" ' . $disabled . ' />
								<div class="error qty' . $row->id . '"></div>
							</td>
							<td>
								<input type="number" name="rate[]" id="rate' . $i . '" class="form-control floatOnly" value="' . $row->confirm_rate . '" min="0" ' . $disabled . ' />
								<div class="error rate' . $row->id . '"></div>
							</td>
							<td>
								<input type="number" name="quote_no[]" id="quote_no' . $i . '" class="form-control floatOnly" value="0" min="0" ' . $disabled . ' />
							</td>
							<td>
								<input type="date" name="quote_date[]" id="quote_date' . $i . '" class="form-control floatOnly" value="0" min="0" ' . $disabled . ' />
							</td>
						</tr>';
				endif;
				$i++;
			endforeach;
		else :
			$html = '<tr><td colspan="6" class="text-center">No data available in table</td></tr>';
		endif;
		return $html;
	}

	public function enquiryConfirmed($enqConData)
	{
		$data = array();
		$data['tableName'] = $this->purchaseEnquiryMaster;
		$data['where']['id'] = $enqConData['enq_id'];
		$enquiryData = $this->row($data);

		$data = array();
		$data['tableName'] = $this->purchaseEnquiryTrans;
		$data['select'] = "purchase_enquiry_transaction.*,unit_master.unit_name,unit_master.description";
		$data['join']['unit_master'] = "unit_master.id = purchase_enquiry_transaction.unit_id";
		$data['where']['purchase_enquiry_transaction.ref_id'] = $enqConData['enq_id'];
		$data['where_in']['purchase_enquiry_transaction.id'] = $enqConData['trans_id'];
		$enquiryItemData = $this->rows($data);


		if (empty($enquiryData->supplier_id)) :
			$data = array();
			$data['tableName'] = "party_master";
			$data['where']['party_category'] = 3;
			$data['where']['party_name'] = $enquiryData->supplier_name;
			$supplierData = $this->row($data);

			if (empty($supplierData)) :
				$supplierSave = $this->store('party_master', ['id' => '', 'party_category' => 3, 'party_name' => $enquiryData->supplier_name]);
				$supplierId = $supplierSave['insert_id'];
			else :
				$supplierId = $supplierData->id;
			endif;
		else :
			$supplierId = $enquiryData->supplier_id;
		endif;

		$masterData = [
			'id' => $enqConData['enq_id'],
			'enq_ref_date' => date("Y-m-d"),
			'supplier_id' => $supplierId
			// 'quote_no' => $enqConData['quote_no'],
			// 'quote_date' => $enqConData['quote_date'],
			// 'quote_file' => $enqConData['quote_file']
		];

		//save purchase enquiry master data
		$this->store($this->purchaseEnquiryMaster, $masterData);

		//save purchase enquiry items
		foreach ($enquiryItemData as $key => $row) :
			$item_type = (empty($row->item_type)) ? 2 : 3;
			$itemMasterData = [
				'id' => "",
				'item_name' => $enqConData['item_name'][$key],
				'price' => $enqConData['rate'][$key],
				//'gst_per'=>$enqConData['gst_per'][$key],
				'unit_id' => $row->unit_id,
				'item_type' => $item_type
			];
			$data = array();
			$data['tableName'] = "item_master";
			$data['where']['item_type'] = $item_type;
			$data['where']['item_name'] = $enqConData['item_name'][$key];
			$item = $this->row($data);
			if (empty($item)) :
				$itemSave = $this->store('item_master', $itemMasterData);
				$itemId = $itemSave['insert_id'];
			else :
				$itemId = $item->id;
				$itemMasterData['id'] = $item->id;
				$itemSave = $this->store('item_master', $itemMasterData);
			endif;

			$transData = [
				'id' => $row->id,
				'item_id' => $itemId,
				'confirm_qty' => $enqConData['qty'][$key],
				'confirm_rate' => $enqConData['rate'][$key],
				'feasible' => $enqConData['feasible'][$key],
				'quote_remark' => $enqConData['quote_remark'][$key],
				'quote_no' => $enqConData['quote_no'][$key],
				'quote_date' => $enqConData['quote_date'][$key],
				'confirm_status' => 1
			];
			$this->store($this->purchaseEnquiryTrans, $transData);
		endforeach;

		$confirmedItems = $this->getEnquiryTransConfirm($enqConData['enq_id']);
		if ($confirmedItems <= 0) :
			$this->store($this->purchaseEnquiryMaster, ['id' => $enqConData['enq_id'], 'enq_status' => 1]);
		endif;

		return ['status' => 1, 'message' => 'Purchase Enquiry Confirmed Successfully.'];
	}

	public function closeEnquiry($id)
	{
		$this->store($this->purchaseEnquiryMaster, ['id' => $id, 'enq_status' => 1]);
		return ['status' => 1, 'message' => 'Purchase Enquiry Closed Successfully.'];
	}

	public function reopenEnquiry($id)
	{
		$this->store($this->purchaseEnquiryMaster, ['id' => $id, 'enq_status' => 0]);
		return ['status' => 1, 'message' => 'Purchase Enquiry re-open Successfully.'];
	}

	public function itemSearch()
	{
		$data['tableName'] = 'item_master';
		$data['select'] = 'item_name';
		$data['where']['item_type != '] = 1;
		$result = $this->rows($data);
		$searchResult = array();
		foreach ($result as $row) {
			$searchResult[] = $row->item_name;
		}
		return  $searchResult;
	}

	public function approvePEnquiry($data)
	{
		$this->store($this->purchaseEnquiryTrans, ['id' => $data['id'], 'confirm_status' => $data['val']]);
		return ['status' => 1, 'message' => 'Purchase Enquiry ' . $data['msg'] . ' successfully.'];
	}
}
