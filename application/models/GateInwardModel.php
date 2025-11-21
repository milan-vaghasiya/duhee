<?php
class GateInwardModel extends MasterModel{
    private $mir = "mir";
    private $mirTrans = "mir_transaction";
    private $purchaseOrderTrans = "purchase_order_trans";
    private $stockTrans = "stock_transaction";

    public function getNextNo(){
        $queryData['tableName'] = $this->mir;
        $queryData['select'] = "ifnull(MAX(trans_no + 1),1) as next_no";
        $queryData['where']['trans_type'] = 2;
        return $this->row($queryData)->next_no;
    }

    public function getDTRows($data){
        $data['tableName'] = $this->mir;

        if($data['status'] != 0):
            $data['select'] = "mir.id,mir.trans_prefix,mir.trans_no,mir.grn_type,DATE_FORMAT(mir.trans_date,'%d-%m-%Y') as trans_date,mir.qty,party_master.party_name,item_master.full_name as item_name,purchase_order_master.po_prefix,purchase_order_master.po_no,mir.qty_kg,mir.inward_qty,gate_entry.trans_prefix as entry_prefix,gate_entry.trans_no as entry_no";
            $data['leftJoin']['party_master'] = "party_master.id = mir.party_id";
            $data['leftJoin']['item_master'] = "item_master.id = mir.item_id";
            $data['leftJoin']['purchase_order_master'] = "purchase_order_master.id = mir.po_id";
            $data['leftJoin']['mir as gate_entry'] = "gate_entry.id = mir.ref_id";
            
            $data['where']['mir.trans_status'] = ($data['status'] == 1)?0:1;
            $data['where']['mir.trans_type'] = 2;
            
            if($data['grn_type'] == 1):
                $data['where']['mir.grn_type'] = 1; //GRN REGULER
                $data['where']['item_master.item_type !='] = 3;
            else:
                $data['where']['mir.grn_type'] = 2; //GRN RM
                $data['where']['item_master.item_type'] = 3;
            endif;
            
            $data['order_by']['mir.id'] = "DESC";

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "CONCAT(mir.trans_prefix,LPAD(mir.trans_no, 4, 0))";
            $data['searchCol'][] = "DATE_FORMAT(mir.trans_date,'%d-%m-%Y')";
            $data['searchCol'][] = "party_master.party_name";
            $data['searchCol'][] = "item_master.full_name";
            $data['searchCol'][] = "mir.inward_qty";
            $data['searchCol'][] = "mir.qty";
            $data['searchCol'][] = "mir.qty_kg";
            $data['searchCol'][] = "purchase_order_master.po_no";
            $data['searchCol'][] = "CONCAT(gate_entry.trans_prefix,LPAD(gate_entry.trans_no, 4, 0))";

            $columns = array('', '', 'CONCAT(mir.trans_prefix,mir.trans_no)', 'mir.trans_date', 'party_master.party_name', 'item_master.item_name', 'mir.inward_qty','mir.qty', 'mir.qty_kg','purchase_order_master.po_no','gate_entry.trans_no');
            if(isset($data['order'])):
                $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
            endif;
        else:
            $data['select'] = "mir.id,mir.trans_prefix,mir.trans_no,DATE_FORMAT(mir.trans_date,'%d-%m-%Y') as trans_date,mir.qty,party_master.party_name,item_master.full_name as item_name,mir.inv_no,ifnull(DATE_FORMAT(mir.inv_date,'%d-%m-%Y'),'') as inv_date,mir.doc_no,ifnull(DATE_FORMAT(mir.doc_date,'%d-%m-%Y'),'') as doc_date";
            $data['leftJoin']['party_master'] = "party_master.id = mir.party_id";
            $data['leftJoin']['item_master'] = "item_master.id = mir.item_id";
            
            $data['where']['mir.trans_status'] = $data['status'];
            $data['where']['mir.trans_type'] = 1;
            
            $data['order_by']['mir.id'] = "DESC";

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "CONCAT(mir.trans_prefix,LPAD(mir.trans_no, 4, 0))";
            $data['searchCol'][] = "DATE_FORMAT(mir.trans_date,'%d-%m-%Y')";
            $data['searchCol'][] = "party_master.party_name";
            $data['searchCol'][] = "item_master.full_name";
            $data['searchCol'][] = "mir.qty";
            $data['searchCol'][] = "mir.inv_no";
            $data['searchCol'][] = "ifnull(DATE_FORMAT(mir.inv_date,'%d-%m-%Y'),'')";
            $data['searchCol'][] = "mir.doc_no";
            $data['searchCol'][] = "ifnull(DATE_FORMAT(mir.doc_date,'%d-%m-%Y'),'')";

            $columns = array('', '', 'CONCAT(mir.trans_prefix,mir.trans_no)', 'mir.trans_date', 'party_master.party_name', 'item_master.item_name', 'mir.qty',  'mir.inv_no','mir.inv_date','mir.doc_no','mir.doc_date');
            if(isset($data['order'])):
                $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
            endif;
        endif;

		return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            
            if(!empty($data['id'])):
                $inwardData = $this->getGateInwardData(['trans_prefix'=>$data['trans_prefix'],'trans_no'=>$data['trans_no']]);
               
                foreach($inwardData as $row){
                    if (!in_array($row->id, $data['mir_id'])):
                        $this->trash($this->mir,['id'=>$row->id]);
                       
                        $this->trash($this->mirTrans,['mir_id'=>$row->id]);
                        
                    endif;

                    $setData = array();
                    $setData['tableName'] = $this->purchaseOrderTrans;
                    $setData['where']['id'] = $row->po_trans_id;
                    $setData['set']['rec_qty'] = 'rec_qty, - '.$row->qty;
                    $this->setValue($setData);
                }
                
                
                $this->store($this->mir,['id'=>$data['ref_id'],'trans_status'=>0]);
            else:
                $data['trans_no'] = $this->getNextNo();
            endif;

            foreach($data['mir_id'] as $key => $value):
                //$nextBatchNo = (!empty($data['item_stock_type']))?$this->getNextBatchOrSerialNo(['trans_id'=>$value,'item_id'=>$data['item_id'],'heat_no'=>$data['heat_no'][$key]]):['batch_no'=>"General Batch",'serial_no'=>0];               
                $itemData = $this->item->getItem($data['item_id'][$key]);

                $masterData = [
                    'id' => $value,
                    'ref_id' => $data['ref_id'],
                    'grn_type' => $data['grn_type'],
                    'trans_type' => 2,
                    'party_id' => $data['party_id'],
                    'item_id' => $data['item_id'][$key],
                    //'material_grade' => $data['material_grade'],
                    'po_id' => $data['po_id'][$key],
                    'item_stock_type' => $data['item_stock_type'][$key],
                    'trans_prefix' => $data['trans_prefix'],
                    'trans_no' => $data['trans_no'],
                    'trans_date' => $data['trans_date'],
                    'po_trans_id' => $data['po_trans_id'][$key],
                    'qty' => $data['batch_qty'][$key],
                ];
                if(empty($value)):
                    $masterData['created_by'] = $this->loginId;
                    $masterData['created_at'] = date("Y-m-d H:i:s");
                else:
                    $masterData['updated_by'] = $this->loginId;
                    $masterData['updated_at'] = date("Y-m-d H:i:s");
                endif;
                $result = $this->store($this->mir,$masterData,'Gate Inward');
                $masterData['id'] = (empty($masterData['id']))?$result['insert_id']:$masterData['id'];

                $batchData = [
                    'id' => $data['mir_trans_id'][$key],
                    'mir_id' => $masterData['id'],
                    'type' => 1,
                    'location_id' => $data['location_id'][$key],
                    'qty' => $data['batch_qty'][$key],
                    'item_id' => $masterData['item_id'],
                    'heat_no' => $data['heat_no'][$key],
                    'mill_heat_no' => $data['mill_heat_no'][$key],
                    'expire_date' => $data['expire_date'][$key],
                    'is_delete' => 0
                ];

                if($itemData->batch_stock == 1):
                    $batchData['batch_no'] = $data['batch_no'][$key];                
                elseif($itemData->batch_stock  == 2):
                    $batchData['batch_no'] = $itemData->item_code.sprintf(n2y(date('Y'))."%03d",$data['trans_no']);
                else:
                    $batchData['batch_no'] = "General Batch";
                    $batchData['serial_no'] = 0;
                endif;
                if(!empty($data['trans_prefix']) && !empty($data['trans_no'])):
                    $batchData['batch_no'] = $data['trans_prefix'].sprintf("%04d",$data['trans_no']);
                endif;

                if(empty($value)):
                    $batchData['created_by'] = $this->loginId;
                    $batchData['created_at'] = date("Y-m-d H:i:s");
                else:
                    $batchData['updated_by'] = $this->loginId;
                    $batchData['updated_at'] = date("Y-m-d H:i:s");
                endif;
               
                $batch = $this->store($this->mirTrans,$batchData);
                $batchDataId = (!empty($batchData['id']))?$batchData['id']:$batch['insert_id'];

                if(!empty($masterData['po_trans_id'])):
                    $setData = array();
                    $setData['tableName'] = $this->purchaseOrderTrans;
                    $setData['where']['id'] = $masterData['po_trans_id'];
                    $setData['set']['rec_qty'] = 'rec_qty, + '.$data['batch_qty'][$key];
                    $this->setValue($setData);
                    
                    /** If Po Order Qty is Complete then Close PO **/
                    $poTrans = $this->getPoTransactionRow($masterData['po_trans_id']);
                    if($poTrans->rec_qty >= $poTrans->qty):
                        $this->store($this->purchaseOrderTrans,["id"=>$masterData['po_trans_id'], "order_status"=>1]);
                    else:
                        $this->store($this->purchaseOrderTrans,["id"=>$masterData['po_trans_id'], "order_status"=>0]);
                    endif;
                endif;
                
            endforeach;

            $transStatus = 1;
            $this->store($this->mir,['id'=>$data['ref_id'],'trans_status'=>$transStatus]);            

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    public function acceptGI($postData){
        try{
            $this->db->trans_begin();
            
            $acceptData['id'] = $postData['id'];
            $acceptData['trans_status'] = $postData['status'];
            $acceptData['accepted_by'] = $this->loginID;
            $acceptData['accepted_at'] = date('Y-m-d H:i:s');
            $acceptData['trans_status'] = $postData['status'];
            $this->store($this->mirTrans,$acceptData);
            
            //$mirData = $this->getMIRTransById($postData['id']);//print_r($mirData);exit;
            if($postData['status'] != 3){
                $checkGIStatus = $this->checkGIBatchStatus($postData['mir_id']);
                if(count($checkGIStatus) > 0 ){$this->store($this->mir,['id'=>$postData['mir_id'],'trans_status'=>0]);}
                else{$this->store($this->mir,['id'=>$postData['mir_id'],'trans_status'=>1]);}
            }
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>"GI Accepted Successfully"];
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function checkGIBatchStatus($mir_id){
        $queryData = array();
        $queryData['tableName'] = $this->mirTrans;
        $queryData['select'] = "mir_transaction.id,mir_transaction.mir_id";
        $queryData['where']['mir_transaction.type'] = 1;
        $queryData['where']['mir_transaction.mir_id'] = $mir_id;
        $queryData['where_in']['mir_transaction.trans_status'] = [0,2];
        $result = $this->rows($queryData);
        return $result;
    }

    public function getMIRTransById($id){
        $queryData = array();
        $queryData['tableName'] = $this->mirTrans;
        $queryData['where']['id'] = $id;
        return $this->row($queryData);
    }

    public function getPoTransactionRow($id){
		$data['tableName'] = $this->purchaseOrderTrans;
		$data['where']['id'] = $id;
		return $this->row($data);
	}

    /* public function getNextBatchOrSerialNo($data){
		$result = array(); $code = "";

        $itemData = $this->item->getItem($data['item_id']);
        $code = (!empty($itemData->batch_stock) && $itemData->batch_stock == 2)?$itemData->item_code:"";

		if(!empty($data['trans_id'])):
            $queryData = array();
			$queryData['select'] = "serial_no,heat_no";
			$queryData['tableName'] = $this->mirTrans;
            $queryData['where']['type'] = 1;
			$queryData['where']['id'] = $data['trans_id'];
			$result = $this->row($queryData);

			if(!empty($result->serial_no) && $data['heat_no'] == $result->heat_no):
                $code .= sprintf(n2y(date('Y'))."%03d",$result->serial_no);
				return ['status'=>1,'batch_no'=>$code,'serial_no'=>$result->serial_no];
			endif;			
		endif;
		
		if(!empty($itemData->batch_stock) && $itemData->batch_stock == 1):
            $queryData = array();
            $queryData['select'] = "serial_no,heat_no";
			$queryData['tableName'] = $this->mirTrans;
			$queryData['where']['item_id'] = $data['item_id'];
            $queryData['where']['type'] = 1;
			$queryData['where']['heat_no'] = $data['heat_no'];
			$result = $this->row($queryData);
			
			if(!empty($result->serial_no)):
                $code .= sprintf(n2y(date('Y'))."%03d",$result->serial_no);
				return ['status'=>1,'batch_no'=>$code,'serial_no'=>$result->serial_no];
			endif;
		endif;

		$queryData = array();
		$queryData['select'] = "ifnull(MAX(serial_no) + 1,1) as serial_no";
		$queryData['tableName'] = $this->mirTrans;
        $queryData['where']['type'] = 1;
		$queryData['where']['item_id'] = $data['item_id'];
		$queryData['where']['is_delete'] = 0;
		$queryData['where']['YEAR(created_at)'] = date("Y");
		$serial_no = $this->specificRow($queryData)->serial_no;
		$code .= sprintf(n2y(date('Y'))."%03d",$serial_no);
		return ['status'=>1,'batch_no'=>$code,'serial_no'=>$serial_no];
	} */

    public function getGateInward($id){
        $queryData = array();
        $queryData['tableName'] = $this->mir;
        $queryData['select'] = "mir.*,item_master.item_name,item_master.full_name,item_master.item_code,item_master.batch_stock,item_master.location,item_master.item_type,item_master.wkg,item_master.material_grade,purchase_order_master.po_prefix,purchase_order_master.po_no";
        $queryData['leftJoin']['item_master'] = "item_master.id = mir.item_id";
		$queryData['leftJoin']['purchase_order_master'] = "purchase_order_master.id = mir.po_id";
        $queryData['where']['mir.id'] = $id;
        $result = $this->row($queryData);

        $result->batchItems = $this->getGateInwardItems($id);

        return $result;
    }

    public function getGateInwardItems($id){
        $queryData = array();
        $queryData['tableName'] = $this->mirTrans;
        $queryData['select'] = "mir_transaction.*,location_master.location as location_name,item_master.item_name,item_master.full_name,item_master.item_code,item_master.batch_stock,item_master.location,item_master.item_type,item_master.wkg,item_master.material_grade";
        $queryData['leftJoin']['location_master'] = "mir_transaction.location_id = location_master.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = mir_transaction.item_id";
        $queryData['where']['mir_transaction.type'] = 1;
        $queryData['where']['mir_transaction.mir_id'] = $id;
        $result = $this->rows($queryData);

        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $gateInward = $this->getGateInward($id);
            
            $inwardData = $this->getGateInwardData(['trans_prefix'=>$gateInward->trans_prefix,'trans_no'=>$gateInward->trans_no]);
               
            foreach($inwardData as $row){
                if($row->trans_status > 0){
                    return ['status'=>0,'message'=>"You can not delete this grn"];
                }else{
                   
                    
                    if(!empty($row->po_trans_id)):
                        $setData['tableName'] = $this->purchaseOrderTrans;
                        $setData['where']['id'] = $row->po_trans_id;
                        $setData['set']['rec_qty'] = 'rec_qty, - '.$row->qty;
                        $this->setValue($setData);
                        
                        /** If Po Order Qty is Complete then Close PO **/
                        $poTrans = $this->getPoTransactionRow($row->po_trans_id);
                        if($poTrans->rec_qty >= $poTrans->qty):
                            $this->store($this->purchaseOrderTrans,["id"=>$row->po_trans_id, "order_status"=>1]);
                        else:
                            $this->store($this->purchaseOrderTrans,["id"=>$row->po_trans_id, "order_status"=>0]);
                        endif;
                    endif;

                    $this->trash($this->mir,['id'=>$row->id]);
                    $this->trash($this->mirTrans,['mir_id'=>$row->id]);
                }
                
            }

            $result = $this->store($this->mir,['id'=>$gateInward->ref_id,'trans_status'=>0]);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    public function getPallatePrintData($id){
        $queryData = array();
        $queryData['tableName'] = $this->mirTrans;
        $queryData['select'] = "mir_transaction.*,mir.trans_no,mir.trans_prefix,mir.trans_date,mir.qty as gi_qty,item_master.full_name,mTrans.batch_no as mBatch_no,mTrans.qty as batch_qty";
        $queryData['leftJoin']['mir'] = "mir_transaction.mir_id = mir.id";
        $queryData['leftJoin']['mir_transaction as mTrans'] = "mir_transaction.ref_id = mTrans.id";
        $queryData['leftJoin']['item_master'] = "mTrans.item_id = item_master.id";
        $queryData['where']['mir_transaction.type'] = 2;
        $queryData['where']['mir_transaction.mir_id'] = $id;
        $result = $this->rows($queryData);
        return $result;
    }
    
     public function getIrPrintData($id){
        $queryData = array();
        $queryData['tableName'] = $this->mirTrans;
        $queryData['select'] = "mir_transaction.*,item_master.full_name,mir.trans_date,mir.trans_no,mir.trans_prefix,mir.party_id,party_master.party_name";
        $queryData['leftJoin']['item_master'] = "mir_transaction.item_id = item_master.id";
        $queryData['leftJoin']['mir'] = "mir_transaction.mir_id = mir.id";
        $queryData['leftJoin']['party_master'] = "mir.party_id = party_master.id";
        $queryData['where']['mir_transaction.type'] = 1;
        $queryData['where']['mir_transaction.mir_id'] = $id;
        $result = $this->rows($queryData);
        return $result;
    }

    public function getGateInwardData($postData){
        $queryData = array();
        $queryData['tableName'] = $this->mir;
        $queryData['select'] = "mir.*,item_master.item_name,item_master.full_name,item_master.item_code,item_master.batch_stock,item_master.location,item_master.item_type,item_master.wkg,item_master.material_grade,purchase_order_master.po_prefix,purchase_order_master.po_no";
        $queryData['leftJoin']['item_master'] = "item_master.id = mir.item_id";
		$queryData['leftJoin']['purchase_order_master'] = "purchase_order_master.id = mir.po_id";
        $queryData['where']['mir.trans_prefix'] = $postData['trans_prefix'];
        $queryData['where']['mir.trans_no'] = $postData['trans_no'];
        $result = $this->rows($queryData);
        $inwardData = array();
        if(!empty($result)){
            foreach($result  as $row){
                $row->batchItems = $this->getGateInwardItems($row->id);
                $inwardData[] = $row;
            }
        }
        
        // print_r($inwardData);
        return $inwardData;
    }
}
?>