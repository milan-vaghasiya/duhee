<?php 
class JobWorkInvoiceModel extends MasterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
	private $grnMain = "grn_master";
    private $grnTrans = "grn_transaction";
    private $jobWork = "jobwork";
    private $jobTransaction = "jobwork_transaction";
	
	public function getDTRows($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = 'trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.dispatch_qty, trans_child.cod_date,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,party_master.party_name,trans_main.remark,trans_main.ref_by,trans_main.order_type,trans_main.net_amount, trans_main.doc_no,trans_main.trans_number';
        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $data['where']['trans_child.entry_type'] = 19;
		$data['group_by'][]='trans_child.trans_main_id';
		$data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "CONCAT('/',trans_main.trans_no)";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "trans_main.net_amount";
      
		$columns =array('','','trans_main.trans_no','trans_main.trans_date','','party_master.party_name','trans_child.item_name','trans_main.net_amount');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    } 
    
    //Changed By Karmi @02/07/2022
        public function getVendorJobWork($data){
        $data['tableName'] = $this->jobTransaction;
        $data['select'] = "jobwork_transaction.*,party_master.party_name,party_master.party_address,party_master.gstin";
        $data['leftJoin']['jobwork'] = "jobwork_transaction.jobwork_id = jobwork.id";
        $data['leftJoin']['party_master'] = "party_master.id = jobwork.vendor_id";
        $data['where']['jobwork.vendor_id'] = $data['party_id'];
        $data['customWhere'][] = 'jobwork_transaction.bill_qty < jobwork_transaction.com_qty';
        if(!empty($data['from_date']) && !empty($data['to_date'])){
        $data['customWhere'][] = "jobwork_transaction.entry_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";}
        $data['where']['jobwork_transaction.entry_type'] = 2;
        $data['where']['jobwork_transaction.is_approve > '] = 0;
		$data['order_by']['jobwork_transaction.entry_date'] = "ASC";
        $resultData = $this->rows($data);
        
        $html=""; $partData="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $part):                
                $partCode = ""; 
                    $partData = $this->item->getItem($part->item_id); 
                    $partCode = (!empty($partData->full_name))? $partData->full_name : '';
                    if($part->com_qty != $part->bill_qty && $part->com_qty > 0):
                        $html .= '<tr>
                                <td class="text-center">
                                    <input type="checkbox" id="ref_id_'.$i.'" name="ref_id[]" data-rowid="'.$i.'" data-id="'.$part->id.'" class="filled-in chk-col-success bulkTags " value="'.$part->id.'" ><label for="ref_id_'.$i.'" class="mr-3 check'.$part->id.'"></label>
                                </td>
                                <td class="text-center">'.$partCode.'</td>
                                <td class="text-center">'.formatDate($part->entry_date).'</td>
                                <td class="text-center">'.$part->challan_no.'</td>
                                <td class="text-center">'.floatval($part->com_qty).'</td>
                                <td class="text-center">'.floatval($part->bill_qty).'</td>
                                <td class="text-center">'.(floatval($part->com_qty) - floatval($part->bill_qty)).'</td>
                                <td class="text-center">'.$part->rej_qty.'</td>
                                <td class="text-center">'.floatval($part->wp_qty).'</td>
                            </tr>';
                        $i++;
                    endif;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="8">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }
    
    //Created By Karmi @13/05/2022
    public function getJobworkTrans($id){
        $data['tableName'] = $this->jobTransaction;
        $data['select'] = "jobwork_transaction.*,process_master.process_name,item_master.item_name,item_master.full_name";
        $data['join']['process_master'] =  "process_master.id = jobwork_transaction.process_id";
        $data['join']['item_master'] =  "item_master.id = jobwork_transaction.item_id";
        $data['where']['jobwork_transaction.jobwork_id'] = $id;
        $data['where']['jobwork_transaction.entry_type'] = 2;
        return $this->rows($data);
    }
    //Changed By Karmi @10/05/2022
    public function getJobworkItemData($id){
        $data['tableName'] = $this->jobTransaction;
        $data['select'] = "jobwork_transaction.*,item_master.hsn_code,item_master.unit_id,hsn_master.cgst,hsn_master.sgst,hsn_master.igst,item_master.item_name,item_master.item_code,unit_master.unit_name,item_master.item_type";
        $data['join']['item_master'] = "item_master.id = jobwork_transaction.item_id";
        $data['join']['hsn_master'] = "item_master.hsn_code = hsn_master.hsn";
        $data['join']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['where_in']['jobwork_transaction.id'] = $id;
        $result = $this->rows($data);
        //print_r($this->db->last_query());exit;
        return $result;
    }	
	public function checkDuplicateINV($party_id,$inv_no,$id){
        $data['tableName'] = $this->transMain;
        $data['where']['trans_no'] = $inv_no;
        $data['where']['party_id'] = $party_id;
        $data['where']['entry_type'] = 19;
        if(!empty($id))
            $data['where']['id != '] = $id;
        return $this->numRows($data);
	}
	
	public function save($masterData,$itemData,$expenseData){
		try{
            $this->db->trans_begin();
			$purchaseId = $masterData['id'];
			
			$checkDuplicate = $this->checkDuplicateINV($masterData['party_id'],$masterData['trans_no'],$purchaseId);
			if($checkDuplicate > 0):
				$errorMessage['inv_no'] = "Invoice No. is Duplicate.";
				return ['status'=>0,'message'=>$errorMessage];
			else:
				//save purchase master data
				$purchaseInvSave = $this->store($this->transMain,$masterData);
				$purId = (empty($purchaseId))?$purchaseInvSave['insert_id']:$masterData['id'];
                $masterData['id'] = $purId;
				if(!empty($purchaseId)):
                    $invoiceData = $this->getInvoice($purchaseId); 
                    foreach($invoiceData->itemData as $row):  
                        $setData = array();        
                        $setData['tableName'] = $this->jobTransaction;        
                        $setData['where']['id'] = $row->ref_id;        
                        $setData['set']['bill_qty'] = 'bill_qty, - ' .$row->qty ;        
                        $this->setValue($setData);        
                    endforeach;
					$this->trash($this->transChild,['trans_main_id'=>$purId]);
				endif;
					
				//save purchase items
				foreach($itemData['item_id'] as $key=>$value):
					$transData = [
						'id'=>$itemData['id'][$key],
						'trans_main_id'=>$purId,
						'entry_type' => $masterData['entry_type'],
						'currency' => $masterData['currency'],
						'inrrate' => $masterData['inrrate'],
						'from_entry_type' => $itemData['from_entry_type'][$key],
						'ref_id' => $itemData['ref_id'][$key],
						'item_id'=>$value,
						'item_name' => $itemData['item_name'][$key],
						'item_type' => $itemData['item_type'][$key],
						'item_code' => $itemData['item_code'][$key],
						'item_desc' => $itemData['item_desc'][$key],
						'unit_id' => $itemData['unit_id'][$key],
						'unit_name' => $itemData['unit_name'][$key],
						'location_id' => $itemData['location_id'][$key],
						'batch_no' => $itemData['batch_no'][$key],
						'hsn_code' => $itemData['hsn_code'][$key],
						'qty' => $itemData['qty'][$key],
                        'p_or_m' => 1,
						'price' => $itemData['price'][$key],
						'amount' => $itemData['amount'][$key] + $itemData['disc_amount'][$key],
						'taxable_amount' => $itemData['taxable_amount'][$key],
						'gst_per' => $itemData['gst_per'][$key],
						'gst_amount' => $itemData['igst_amount'][$key],
						'igst_per' => $itemData['igst_per'][$key],
						'igst_amount' => $itemData['igst_amount'][$key],
						'cgst_per' => $itemData['cgst_per'][$key],
						'cgst_amount' => $itemData['cgst_amount'][$key],
						'sgst_per' => $itemData['sgst_per'][$key],    
						'sgst_amount' => $itemData['sgst_amount'][$key],
						'disc_per' => $itemData['disc_per'][$key],
						'disc_amount' => $itemData['disc_amount'][$key],
						'item_remark' => $itemData['item_remark'][$key],
						'net_amount' => $itemData['net_amount'][$key],
						'created_by' => $masterData['created_by'],
						'is_delete' => 0
					];
					$this->store($this->transChild,$transData);
					
                    if(!empty($itemData['ref_id'][$key])):
                        $setData = array();
                        $setData['tableName'] = $this->jobTransaction;
                        $setData['where']['id'] = $itemData['ref_id'][$key];
                        $setData['set']['bill_qty'] = 'bill_qty, + ' . $itemData['qty'][$key];
                        $this->setValue($setData);
                    endif;
                   
                endforeach;
                $this->transModel->ledgerEffects($masterData,$expenseData);
				$result = ['status'=>1,'message'=>'Purchase Invoice saved successfully.','url'=>base_url("JobWorkInvoice")];	
			endif;
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}				
	}
	public function getInvoice($id){    
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $id;
        $invoiceData = $this->row($queryData);
		$invoiceData->itemData = $this->purchaseTransactions($id);
        $queryData = array();
        $queryData['tableName'] = "trans_expense";
        $queryData['where']['trans_main_id'] = $id;
        $invoiceData->expenseData = $this->row($queryData);
        return $invoiceData;
    }
	
    //changed BY Karmi @02/07/2022
	public function purchaseTransactions($id){
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*,jobwork_transaction.challan_no as job_challan_no";
        $queryData['leftJoin']['jobwork_transaction'] = "jobwork_transaction.id = trans_child.ref_id";
        $queryData['where']['trans_main_id'] = $id;
        $queryData['where']['jobwork_transaction.entry_type'] = 2;
        return $this->rows($queryData);
    }
    
    public function delete($id){
        
		try{
            $this->db->trans_begin();
			$invoiceData = $this->getInvoice($id); 
			$this->trash($this->transChild,['trans_main_id'=>$id]);
			$result = $this->trash($this->transMain,['id'=>$id],'JobWork Invoice');
            foreach($invoiceData->itemData as $row):
               
                $setData = array();
                $setData['tableName'] = $this->jobTransaction;
                $setData['where']['id'] = $row->ref_id;
                $setData['set']['bill_qty'] = 'bill_qty, - ' .$row->qty ;
                $this->setValue($setData);
            endforeach;
    
            
            $this->transModel->deleteLedgerTrans($id);
            $this->transModel->deleteExpenseTrans($id);
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
		return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}
	public function getItemList($id){
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.item_name,trans_child.hsn_code,trans_child.igst_per,trans_child.qty,trans_child.unit_name,trans_child.price,trans_child.amount";
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['where']['trans_main.id'] = $id;
        $resultData = $this->rows($queryData);
        $html="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):              
                $html .= '<tr>
                            <td class="text-center">'.$i.'</td>
                            <td class="text-center">'.$row->item_name.'</td>
                            <td class="text-center">'.$row->hsn_code.'</td>
                            <td class="text-center">'.$row->igst_per.'</td>
                            <td class="text-center">'.$row->qty.'</td>
                            <td class="text-center">'.$row->unit_name.'</td>
                            <td class="text-center">'.$row->price.'</td>
                            <td class="text-center">'.$row->amount.'</td>
                          </tr>';
                $i++;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="5">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }
	/*  Create By : Avruti @29-11-2021 4:00 PM
        update by : 
        note : 
    */
    //---------------- API Code Start ------//
    public function getCount($type=0){
		$data['tableName'] = $this->transChild;
		$data['where']['trans_child.entry_type'] = 12;
        return $this->numRows($data);
    }
    public function getPurchaseInvoiceList_api($limit, $start,$type=0){
		$data['tableName'] = $this->transChild;
        $data['select'] = 'trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.dispatch_qty, trans_child.cod_date,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,trans_main.party_name,trans_main.remark,trans_main.ref_by,trans_main.order_type,trans_main.net_amount';
        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where']['trans_child.entry_type'] = 12;
		$data['group_by'][]='trans_child.trans_main_id';
		$data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }
    //------ API Code End -------//
}
?>