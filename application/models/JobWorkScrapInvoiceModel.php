<?php
class JobWorkScrapInvoiceModel extends MasterModel{
    private $salesMaster = "sales_invoice";
    private $salesTrans = "sales_invoice_trans";
    private $itemMaster = "item_master";
    private $stockTrans = "stock_transaction";
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $jobTransaction = "jobwork_transaction";

    public function getDTRows($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.*";
        $data['customWhere'][] = 'trans_main.entry_type IN ('.$data['entry_type'].')';
        if(!empty($data['from_date']) AND !empty($data['to_date']))
            $data['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'"; 
        $data['order_by']['trans_main.trans_no'] = "ASC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "CONCAT('/',trans_main.trans_no)";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_main.net_amount";

        $columns =array('','','trans_main.trans_no','trans_main.trans_date','trans_main.party_name','trans_main.net_amount');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }


    //Created By Karmi @13/05/2022
    public function getVendorJWScrap($id){
        $data['tableName'] = $this->jobTransaction;
        $data['select'] = "jobwork_transaction.*,party_master.party_name,party_master.party_address,party_master.gstin";
        $data['leftJoin']['jobwork'] = "jobwork_transaction.jobwork_id = jobwork.id";
        $data['leftJoin']['party_master'] = "party_master.id = jobwork.vendor_id";
        $data['where']['jobwork.vendor_id'] = $id;
        $data['where']['jobwork_transaction.scrap_weight >'] = 0;
        $data['where']['jobwork_transaction.entry_type'] = 1;
        $resultData = $this->rows($data);
     
        
        $html=""; $partData="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $part):                
                $partCode = ""; 
                    $partCode = $this->item->getItem($part->item_id)->full_name; 
                    $inv_qty = $this->getInvQtySum($part->id); 
                    $qty = $part->scrap_weight - $inv_qty->invQty;
                    if($part->scrap_weight != $inv_qty->invQty):
                        if($qty > 0):
                            $html .= '<tr>
                                    <td class="text-center">
                                        <input type="checkbox" id="md_checkbox_'.$i.'" name="ref_id[]" class="filled-in chk-col-success" value="'.$part->id.'" ><label for="md_checkbox_'.$i.'" class="mr-3 check'.$part->id.'"></label>
                                    </td>
                                    <td class="text-center">'.$partCode.'</td>
                                    <td class="text-center">'.formatDate($part->entry_date).'</td>
                                    <td class="text-center">'.$qty.'</td>
                                </tr>';
                        endif;
                    endif;
                    $i++;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="4">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }

    //Changed By Karmi @20/05/2022
    public function getJobworkScrapData($id){

        $data['tableName'] = $this->jobTransaction;
        $data['select'] = "jobwork_transaction.*,jobwork_order_trans.hsn_code,jobwork_order_trans.com_unit as unit_id,jobwork_order_trans.cgst,jobwork_order_trans.sgst,jobwork_order_trans.igst,item_master.item_name,item_master.item_code,unit_master.unit_name,item_master.item_type";
        $data['join']['item_master'] = "item_master.id = jobwork_transaction.item_id";
        $data['join']['jobwork_order_trans'] = "jobwork_order_trans.id = jobwork_transaction.job_order_trans_id";
        $data['join']['unit_master'] = "unit_master.id = jobwork_order_trans.com_unit";
        $data['where_in']['jobwork_transaction.id'] = $id;
        return $this->rows($data);

    }	

    public function getInvQtySum($id){
        $data['tableName'] = $this->transChild;
        $data['select'] = "SUM(trans_child.qty) as invQty";
        $data['where']['trans_child.entry_type'] = 20;
        $data['where']['trans_child.ref_id'] = $id;
        return $this->row($data);
        
        
    }

    public function salesTransRow($id){
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['id'] = $id;
        return $this->row($queryData);
    }

    public function save($masterData,$itemData,$expenseData,$redirect_url="jobWorkScrapInvoice"){
        try{
            $this->db->trans_begin();
            $id = $masterData['id'];		
            if(empty($id)):
                $saveInvoice = $this->store($this->transMain,$masterData);
                $salesId = $saveInvoice['insert_id'];	
                $masterData['id'] = $salesId;                

                $result = ['status'=>1,'message'=>'Scrapes Invoice saved successfully.','url'=>base_url($redirect_url)];
            else:
                $this->store($this->transMain,$masterData);
                $salesId = $id;	
                $masterData['id'] = $salesId;	
                
                $transDataResult = $this->salesTransactions($id);
                // foreach($transDataResult as $row):
                //     if($row->stock_eff == 1):
                //         /** Update Item Stock **/
                //         $setData = Array();
                //         $setData['tableName'] = $this->itemMaster;
                //         $setData['where']['id'] = $row->item_id;
                //         $setData['set']['qty'] = 'qty, + '.$row->qty;
                //         $setData['set']['packing_qty'] = 'packing_qty, + '.$row->qty;
                //         $qryresult = $this->setValue($setData);

                //         /** Remove Stock Transaction **/
                //         $this->remove($this->stockTrans,['ref_id'=>$row->id,'trans_type'=>2,'ref_type'=>5]);
                //     endif;

                //     if(!in_array($row->id,$itemData['id'])):
                //         $this->trash($this->transChild,['id'=>$row->id]);
                //     endif;
                // endforeach;

                $result = ['status'=>1,'message'=>'Sales Invoice updated successfully.','url'=>base_url($redirect_url)];
            endif;

            foreach($itemData['item_id'] as $key=>$value):
                $batch_qty = array(); $batch_no = array(); $location_id = array();
                $batchQty = explode(",",$itemData['batch_qty'][$key]);
                $batchNo = explode(",",$itemData['batch_no'][$key]);
                $locationId = explode(",",$itemData['location_id'][$key]);
                foreach($batchNo as $ak=>$av):
                    if(!empty($batchQty[$ak])):
                        $batch_qty[] = $batchQty[$ak];
                        $batch_no[] = $av;
                        $location_id[] = $locationId[$ak];
                    endif;
                endforeach;


                $salesTransData = [
                                    'id'=>$itemData['id'][$key],
                                    'trans_main_id'=>$salesId,
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
                                    'location_id' => implode(",",$location_id),
                                    'batch_no' => implode(",",$batch_no),
                                    'batch_qty' => implode(",",$batch_qty),
                                    'stock_eff' => $itemData['stock_eff'][$key],
                                    'hsn_code' => $itemData['hsn_code'][$key],
                                    'qty' => $itemData['qty'][$key],
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
                                    'created_by' => $masterData['created_by']
                                ];
                $saveTrans = $this->store($this->transChild,$salesTransData);
                $refID = (empty($itemData['id'][$key]))?$saveTrans['insert_id']:$itemData['id'][$key];
                
                // if(!empty($itemData['ref_id'][$key])):
                //     $setData = Array();
                //     $setData['tableName'] = $this->transChild;
                //     $setData['where']['id'] = $itemData['ref_id'][$key];
                //     $setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$itemData['qty'][$key];
                //     $this->setValue($setData);

                //     $queryData = array();
                //     $queryData['tableName'] = $this->transChild;
                //     $queryData['where']['id'] = $itemData['ref_id'][$key];
                //     $transRow = $this->row($queryData);

                //     if($transRow->qty <= $transRow->dispatch_qty):
                //         $this->store($this->transChild,['id'=>$itemData['ref_id'][$key],'trans_status'=>1]);
                //     endif;
                // endif;

                // if($itemData['stock_eff'][$key] == 1):
                //     /** Update Item Stock **/
                //     $setData = Array();
                //     $setData['tableName'] = $this->itemMaster;
                //     $setData['where']['id'] = $itemData['item_id'][$key];
                //     $setData['set']['qty'] = 'qty, - '.$itemData['qty'][$key];
                //     $setData['set']['packing_qty'] = 'packing_qty, - '.$itemData['qty'][$key];
                //     $qryresult = $this->setValue($setData);

                //     /*** UPDATE STOCK TRANSACTION DATA ***/
                //     foreach($batch_qty as $bk=>$bv):
                //         $stockQueryData['id']="";
                //         $stockQueryData['location_id']=$location_id[$bk];
                //         if(!empty($batch_no[$bk])){$stockQueryData['batch_no'] = $batch_no[$bk];}
                //         $stockQueryData['trans_type']=2;
                //         $stockQueryData['item_id']=$itemData['item_id'][$key];
                //         $stockQueryData['qty'] = "-".$bv;
                //         $stockQueryData['ref_type']=5;
                //         $stockQueryData['ref_id']=$refID;
                //         $stockQueryData['ref_no']=getPrefixNumber($masterData['trans_prefix'],$masterData['trans_no']);
                //         $stockQueryData['ref_date']=$masterData['trans_date'];
                //         $stockQueryData['created_by']=$this->loginID;
                //         $this->store($this->stockTrans,$stockQueryData);
                //     endforeach;
                // endif;            
            endforeach;

            // if(!empty($masterData['ref_id'])):
            //     $refIds = explode(",",$masterData['ref_id']);
            //     foreach($refIds as $key=>$value):
            //         if($masterData['from_entry_type'] == 5):
            //             $pendingItems = $this->challan->checkChallanPendingStatus($value);
            //         elseif($masterData['from_entry_type'] == 4):
            //             $pendingItems = $this->salesOrder->checkSalesOrderPendingStatus($value);
            //         endif;
            //         if(empty($pendingItems)):
            //             $this->store($this->transMain,['id'=>$value,'trans_status'=>1]);
            //         endif;
            //     endforeach;
            // endif;

            $ledgerEff = $this->transModel->ledgerEffects($masterData,$expenseData);
            if($ledgerEff == false):
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : "];
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
        $queryData = array();   
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $id;
        $invoiceData = $this->row($queryData);
        
        $invoiceData->itemData = $this->salesTransactions($id);

        $queryData = array();
        $queryData['tableName'] = "trans_expense";
        $queryData['where']['trans_main_id'] = $id;
        $invoiceData->expenseData = $this->row($queryData);
        return $invoiceData;
    }

    public function salesTransactions($id,$limit=""){
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['trans_main_id'] = $id;
        if(!empty($limit)){$queryData['limit'] = $limit;}

        $result = $this->rows($queryData);
        return $result;
    }

    public function deleteInv($id){
        try{
            $this->db->trans_begin();
            $transData = $this->getInvoice($id);
            foreach($transData->itemData as $row):
                // if(!empty($row->ref_id)):
                //     $setData = Array();
                //     $setData['tableName'] = $this->transChild;
                //     $setData['where']['id'] = $row->ref_id;
                //     $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$row->qty;
                //     $this->setValue($setData);

                //     $queryData = array();
                //     $queryData['tableName'] = $this->transChild;
                //     $queryData['where']['id'] = $row->ref_id;
                //     $transRow = $this->row($queryData);

                //     if($transRow->qty != $transRow->dispatch_qty):
                //         $this->store($this->transChild,['id'=>$row->ref_id,'trans_status'=>0]);
                //     endif;
                // endif;

                // if($row->stock_eff == 1):
                //     /** Update Item Stock **/
                //     $setData = Array();
                //     $setData['tableName'] = $this->itemMaster;
                //     $setData['where']['id'] = $row->item_id;
                //     $setData['set']['qty'] = 'qty, + '.$row->qty;
                //     $setData['set']['packing_qty'] = 'packing_qty, + '.$row->qty;
                //     $qryresult = $this->setValue($setData);

                //     /** Remove Stock Transaction **/
                //     $this->remove($this->stockTrans,['ref_id'=>$row->id,'trans_type'=>2,'ref_type'=>5]);
                // endif;
                $this->trash($this->transChild,['id'=>$row->id]);
            endforeach;

            // if(!empty($transData->ref_id)):
            //     $refIds = explode(",",$transData->ref_id);
            //     foreach($refIds as $key=>$value):
            //         if($transData->from_entry_type == 5):
            //             $pendingItems = $this->challan->checkChallanPendingStatus($value);
            //         elseif($transData->from_entry_type == 4):
            //             $pendingItems = $this->salesOrder->checkSalesOrderPendingStatus($value);
            //         endif;
            //         if(!empty($pendingItems)):
            //             $this->store($this->transMain,['id'=>$value,'trans_status'=>0]);
            //         endif;
            //     endforeach;
            // endif;
            $result = $this->trash($this->transMain,['id'=>$id],'Scrap Invoice');

            $deleteLedgerTrans = $this->transModel->deleteLedgerTrans($id);
            if($deleteLedgerTrans == false):
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : "];
            endif;
            $deleteExpenseTrans = $this->transModel->deleteExpenseTrans($id);
            if($deleteExpenseTrans == false):
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : "];
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

    public function batchWiseItemStock($data){
		
        $i=1;$tbody="";
		$locationData = $this->store->getStoreLocationList();
     
		if(!empty($locationData)){
			foreach($locationData as $lData){                
				
				foreach($lData['location'] as $batch):
                    $queryData = array();
					$queryData['tableName'] = "stock_transaction";
					$queryData['select'] = "SUM(qty) as qty,batch_no";
					$queryData['where']['item_id'] = $data['item_id'];
					$queryData['where']['location_id'] = $batch->id;

					$queryData['order_by']['id'] = "asc";
					$queryData['group_by'][] = "batch_no";
					$result = $this->rows($queryData);
					if(!empty($result)){
                        $batch_no = array();
						foreach($result as $row){
                            $batch_no = (!empty($data['trans_id']))?explode(",",$data['batch_no']):$data['batch_no'];
                            $batch_qty = (!empty($data['trans_id']))?explode(",",$data['batch_qty']):$data['batch_qty'];
                            
                            $location_id = (!is_array($data['location_id']))?explode(",",$data['location_id']):$data['location_id'];
                            if($row->qty > 0 || !empty($batch_no) && in_array($row->batch_no,$batch_no)):
                                if(!empty($batch_no) && in_array($row->batch_no,$batch_no) && in_array($batch->id,$location_id)):
                                    //$arrayKey = array_search($batch->id,$location_id);
                                    $qty = 0;
                                    foreach($batch_no as $key=>$value):
                                        if($key == array_search($batch->id,$location_id)):
                                            $qty = $batch_qty[$key];
                                            break;
                                        endif;
                                    endforeach;
                                    //$qty = (isset($batch_qty[$arrayKey]))?$batch_qty[$arrayKey]:0;
                                    $cl_stock = (!empty($data['trans_id']))?floatVal($row->qty + $qty):floatVal($row->qty);
                                else:
                                    $qty = "0";
                                    $cl_stock = floatVal($row->qty);
                                endif;                                
                                
                                $tbody .= '<tr>';
                                    $tbody .= '<td class="text-center">'.$i.'</td>';
                                    $tbody .= '<td>['.$lData['store_name'].'] '.$batch->location.'</td>';
                                    $tbody .= '<td>'.$row->batch_no.'</td>';
                                    $tbody .= '<td>'.floatVal($row->qty).'</td>';
                                    $tbody .= '<td>
                                        <input type="number" name="batch_quantity[]" class="form-control batchQty" data-rowid="'.$i.'" data-cl_stock="'.$cl_stock.'" min="0" value="'.$qty.'" />
                                        <input type="hidden" name="batch_number[]" id="batch_number'.$i.'" value="'.$row->batch_no.'" />
                                        <input type="hidden" name="location[]" id="location'.$i.'" value="'.$batch->id.'" />
                                        <div class="error batch_qty'.$i.'"></div>
                                    </td>';
                                $tbody .= '</tr>';
                                $i++;
                            endif;
						}
					}
				endforeach;
			}
		}else{
            $tbody = '<tr><td class="text-center" colspan="5">No Data Found.</td></tr>';
        }
        return ['status'=>1,'batchData'=>$tbody];
    }
    
    public function getItemList($id){        
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.item_name,trans_child.hsn_code,trans_child.igst_per,trans_child.qty,trans_child.unit_name,trans_child.price,trans_child.amount";
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['where']['trans_main.id'] = $id;
        //print_r($queryData);exit;
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

    public function getSalesInvoiceList($party_id){
        $data['tableName'] = $this->transMain;
        $data['where']['party_id'] = $party_id;
        $data['where_in']['entry_type'] = [6,7,8];
        return $this->rows($data);      
    }
	
	/*  Create By : Avruti @29-11-2021 4:00 PM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount($type=0){
		  $data['tableName'] = $this->transMain;
		
        return $this->numRows($data);
    }

    public function getSalesInvoiceList_api($limit, $start,$type=0){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.*";
        // $data['where_in']['trans_main.sales_type'] = $data['sales_type'];
        // $data['where_in']['trans_main.entry_type'] = $data['entry_type'];
        $data['customWhere'][] = 'trans_main.entry_type IN ('.$data['entry_type'].')';
        // $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.trans_no'] = "ASC";

        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }
    
    public function getSalesInvoiceTotal($postData){
        $qData['tableName'] = 'trans_main';
		$qData['select'] = "SUM(trans_main.taxable_amount) as taxable_amount,SUM(trans_main.net_amount) as net_amount";
        $data['join']['party_master'] = "party_master.id = trans_main.party_id";
		$qData['where']['party_master.sales_executive'] = $postData['sales_executive'];
		$qData['customWhere'][] = "YEAR(trans_main.trans_date) ='".date('Y',strtotime($postData['month']))."' AND MONTH(trans_main.trans_date) ='".date('m',strtotime($postData['month']))."'";
		$invData = $this->row($qData);
		return $invData;
    }

    //------ API Code End -------//
}
?>