<?php
class PackingModel extends MasterModel{
    private $packingMaster = "packing_master";
    private $packingTrans = "packing_transaction";
    private $itemKit = "item_kit";
    private $stockTrans = "stock_transaction";
    private $itemMaster = "item_master";
    private $exportPacking = "export_packing";

    public function getNetxNo(){
        $data['tableName'] = $this->packingMaster;
        $data['select'] = "MAX(trans_no) as trans_no";
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo; 
    }

    public function getPrefix(){
        $prefix = 'PCK/';
        return $prefix.$this->shortYear.'/';
    }

    public function getDTRows($data){ 
        $data['tableName'] = $this->packingTrans;
        $data['select'] = "packing_master.id,packing_master.trans_number,packing_master.trans_date,item_master.item_code,item_master.item_name,packing_transaction.qty_box,packing_transaction.total_box,packing_transaction.total_box_qty,packing_transaction.remark,(CASE WHEN packing_transaction.so_trans_id = 0 THEN 'Self Packing' ELSE CONCAT(trans_main.trans_prefix,trans_main.trans_no) END) as so_no,(packing_transaction.total_box_qty - packing_transaction.dispatch_qty) as pending_qty";
        $data['leftJoin']['packing_master'] = "packing_transaction.packing_id = packing_master.id";
        $data['leftJoin']['item_master'] = "packing_master.item_id = item_master.id";
        $data['leftJoin']['trans_child'] = "packing_transaction.so_trans_id = trans_child.id";
        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";

        if($data['status'] == 0):
            $data['where']['(packing_transaction.total_box_qty - packing_transaction.dispatch_qty) >'] = 0;
        else:
            $data['where']['(packing_transaction.total_box_qty - packing_transaction.dispatch_qty) <='] = 0;
        endif;
        
        $data['order_by']['packing_master.trans_no'] = "DESC";

        $data['searchCol'][] = "packing_master.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(packing_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT(item_master.item_code,item_master.item_name)";
        $data['searchCol'][] = "packing_transaction.qty_box";
        $data['searchCol'][] = "packing_transaction.total_box";
        $data['searchCol'][] = "packing_transaction.total_box_qty";
        $data['searchCol'][] = "(packing_transaction.total_box_qty - packing_transaction.dispatch_qty)";
        $data['searchCol'][] = "packing_master.remark";
        
		$columns =array('','');
        foreach($data['searchCol'] as $key=>$value):
                $columns[] = $value;
        endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getItemCurrentStock($postData){ 
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(qty) as qty";
        $queryData['where']['item_id'] = $postData['item_id'];
        if(!empty($postData['location_id'])){$queryData['where']['location_id'] = $postData['location_id'];}
        if(!empty($postData['batch_no'])){$queryData['where']['batch_no'] = $postData['batch_no'];}
        return $this->row($queryData);
    }

    public function batchWiseItemStock($item_id){
		$data['tableName'] = "stock_transaction";
		$data['select'] = "SUM(stock_transaction.qty) as qty,stock_transaction.batch_no,stock_transaction.location_id,location_master.store_name,location_master.location";
        $data['leftJoin']['location_master'] = "stock_transaction.location_id = location_master.id";
		$data['where']['stock_transaction.item_id'] = $item_id;
		$data['order_by']['stock_transaction.id'] = "asc";
        $data['group_by'][] = "stock_transaction.location_id";
		$data['group_by'][] = "stock_transaction.batch_no";
		return $this->rows($data);
	}

    public function getPacking($id){
        $queryData['tableName'] = $this->packingMaster;
        $queryData['select'] = "packing_master.*,item_master.item_code,item_master.item_name";   
        $queryData['leftJoin']['item_master'] = "packing_master.item_id = item_master.id";
        $queryData['where']['packing_master.id'] = $id;
        $result = $this->row($queryData);
        $result->items = $this->getPackingTrans($id);
        return $result;
    }

    public function getPackingTrans($id){
        $queryData['tableName'] = $this->packingTrans;
        $queryData['select'] = "packing_transaction.*,item_master.item_code as box_item_code,item_master.item_name as box_item_name";   
        $queryData['leftJoin']['item_master'] = "packing_transaction.box_item_id = item_master.id";
        $queryData['where']['packing_id'] = $id;
        return $this->rows($queryData);
    }

    public function getPackingTransRow($id){
        $queryData['tableName'] = $this->packingTrans; 
        $queryData['select'] = "packing_transaction.*,item_master.item_code as box_item_code,item_master.item_name as box_item_name";     
        $queryData['leftJoin']['item_master'] = "packing_transaction.box_item_id = item_master.id";
        $queryData['where']['packing_transaction.id'] = $id;
        return $this->row($queryData);
    }

    public function save($data){
        try {
            $this->db->trans_begin();

            if(!empty($data['id'])):
                /** Remove Stock Transaction **/
                
                $this->remove($this->stockTrans,['ref_id'=>$data['id'],'ref_type'=>36]);
                $this->trash($this->packingTrans,['packing_id'=>$data['id']]);
            endif;

            $materialData = $data['material_data'];unset($data['material_data']);
            $result = $this->store($this->packingMaster,$data,'Packing');
            $packingId = (empty($data['id']))?$result['insert_id']:$data['id'];

            foreach($materialData as $row):
                if(empty($row['id'])):
                    $row['created_by'] = $this->loginId;
                else:
                    $row['updated_by'] = $this->loginId;
                endif;
                $row['packing_id'] = $packingId;
                $row['is_delete'] = 0;

                $transResult = $this->store($this->packingTrans,$row);
                $transId = (empty($row['id']))?$transResult['insert_id']:$row['id'];

                /* Box Stock Deduction */
                $stockQueryData = array();
                $stockQueryData['id']="";
                $stockQueryData['location_id']=$this->PACK_MTR_STORE->id;
                //$stockQueryData['batch_no'] = "GB";
                $stockQueryData['trans_type']=2;
                $stockQueryData['item_id']=$row['box_item_id'];
                $stockQueryData['qty'] = ($row['total_box'] * -1);
                $stockQueryData['ref_type']=36;
                $stockQueryData['ref_id']=$packingId;
                $stockQueryData['trans_ref_id']=$transId;
                $stockQueryData['ref_no']=$data['trans_number'];
                $stockQueryData['ref_date']=$data['trans_date'];
                $stockQueryData['created_by']=$this->loginID; 
                $this->store($this->stockTrans,$stockQueryData);

                if(!empty($row['so_trans_id'])):
                    $setData = array();
                    $setData['tableName'] = "trans_child";
                    $setData['where']['id'] = $row['so_trans_id'];
                    $setData['set']['packing_qty'] = 'packing_qty, + ' . $row['total_box_qty'];
                    $this->setValue($setData);
                endif;
                $batchData = json_decode($row['batch_detail'],false);
                $i=1; $totalQty = 0;$stockReduceIds = [];
                foreach($batchData as $batchRow):
                    /* Product Stock Deduction From Packing Area */
                    $stockQueryData = array();
                    $stockQueryData['id']="";
                    $stockQueryData['location_id']=$batchRow->location_id;
                    $stockQueryData['batch_no'] = $batchRow->batch_no;
                    $stockQueryData['trans_type']=2;
                    $stockQueryData['item_id']=$data['item_id'];
                    $stockQueryData['qty'] = ($batchRow->batch_qty * -1);
                    $stockQueryData['ref_type']=36;
                    //$stockQueryData['size']=$row['qty_box'];
                    $stockQueryData['ref_id']=$packingId;
                    $stockQueryData['trans_ref_id']=$transId;
                    $stockQueryData['ref_no']=$data['trans_number'];
                    $stockQueryData['ref_batch']=$row['so_trans_id'];
                    $stockQueryData['ref_date']=$data['trans_date'];
                    $stockQueryData['created_by']=$this->loginID;
                    $stockReduce = $this->store($this->stockTrans,$stockQueryData);
                    
                    $stockReduceIds[] = $stockReduce['insert_id'];// Array of Stock Trans Id of Minus Stock
                    $totalQty += $batchRow->batch_qty;
                    
                    /* Product Stock Plus to Ready to Dispatch Store */
                    /*$stockQueryData['location_id']=$this->RTD_STORE->id;
                    $stockQueryData['batch_no'] = $batchRow->batch_no;
                    $stockQueryData['trans_type']=1;
                    $stockQueryData['qty'] = $batchRow->batch_qty;
                    $stockPlus = $this->store($this->stockTrans,$stockQueryData);*/
                endforeach;
            
                // Merge Multiple Batch to Packing Batch
                $stockQueryData = array();
                $stockQueryData['id']="";
                $stockQueryData['location_id']=$this->RTD_STORE->id;
                $stockQueryData['batch_no'] = $data['trans_number'];
                $stockQueryData['trans_type']=1;
                $stockQueryData['qty'] = $totalQty;
                $stockQueryData['item_id']=$data['item_id'];
                $stockQueryData['ref_type']=36;
                $stockQueryData['size']=$row['qty_box'];
                $stockQueryData['ref_id']=$packingId;
                $stockQueryData['trans_ref_id']=$transId;
                $stockQueryData['ref_no']=$data['trans_number'];
                $stockQueryData['ref_batch']=implode(',',$stockReduceIds); // Stock Trans Id of Minus Stock
                $stockQueryData['ref_date']=$data['trans_date'];
                $stockQueryData['created_by']=$this->loginID;
                $stockReduce = $this->store($this->stockTrans,$stockQueryData);
            endforeach;
            
            
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function delete($id){
        $packingOrderData = $this->getPacking($id);

        if($packingOrderData->is_delete == 0):
            /** Remove Stock Transaction **/
            $this->remove($this->stockTrans,['ref_id'=>$id,'ref_type'=>36]);
            $this->edit($this->packingTrans,['packing_id'=>$id],['is_delete'=>1]);
            return $this->trash($this->packingMaster,['id'=>$id],"Packing");
        else:
            return ['status' => 1 ,'message' => 'Packing deleted successfully.'];
        endif;
    }

    public function getPackingTransBySo($sales_trans_id){
        $queryData['tableName'] = $this->packingTrans;
        $queryData['select'] = "packing_transaction.*,item_master.item_code as box_item_code,item_master.item_name as box_item_name";   
        $queryData['leftJoin']['item_master'] = "packing_transaction.box_item_id = item_master.id";
        $queryData['where']['so_trans_id'] = $sales_trans_id;
        return $this->rows($queryData);
    }

    public function getPackingMaterial(){
        $data['tableName'] = $this->itemMaster;    
        $data['where']['item_master.item_type'] = 9;
        return $this->rows($data);
    }

    /* Created By NYN @16/11/2022 */
    public function savePackingStandard($data){
        try{
            $this->db->trans_begin();

            if ($this->checkDuplicateStandard($data['item_id'], $data['box_id']) > 0) :
                $errorMessage['gerenal_error'] = "Box already added.";
				$result = ['status' => 0, 'message' => $errorMessage];
            else:
                $this->edit($this->itemMaster,['id'=>$data['item_id']],['wt_pcs' => $data['wt_pcs']]); unset($data['wt_pcs']);
                $result = $this->store('packing_kit',$data,'Packing Standard');
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
    
    /* Created By NYN @16/11/2022 */
    public function checkDuplicateStandard($item_id, $box_id){
        $data['tableName'] = 'packing_kit';
		$data['where']['item_id'] = $item_id;
		$data['where']['box_id'] = $box_id;
		return $this->numRows($data);
    }

    /* Created By NYN @16/11/2022 */
    public function deletePackingStandard($id){
        try{
            $this->db->trans_begin();

            $result = $this->trash('packing_kit',['id'=>$id],'Packing Standard');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    /* Created By NYN @16/11/2022 */
    public function getProductPackStandard($data){
        $queryData['tableName'] = 'packing_kit';
        $queryData['select'] = "packing_kit.*,item_master.item_name,item_master.size,finish.wt_pcs";
        $queryData['leftJoin']['item_master'] = "packing_kit.box_id = item_master.id";
        $queryData['leftJoin']['item_master as finish'] = "packing_kit.item_id = finish.id";
        $queryData['where']['packing_kit.item_id'] = $data['item_id'];
        return $this->rows($queryData); 
    }
	
	public function saveDispatchMaterial($data){ 
		try{
            $this->db->trans_begin();
			/*** UPDATE STOCK TRANSACTION DATA ***/
			foreach($data['location'] as $key=>$value):
				$stockQueryData['id']="";
				$stockQueryData['location_id'] = $value;
				if(!empty( $data['batch_number'][$key])){
					$stockQueryData['batch_no'] = $data['batch_number'][$key];
				}
				$stockQueryData['trans_type']=2;
				$stockQueryData['item_id']=$data['item_id'];
				$stockQueryData['qty'] = "-".$data['batch_quantity'][$key];
				$stockQueryData['ref_type'] = 4;
				$stockQueryData['ref_id'] = $data['trans_main_id'];
				$stockQueryData['trans_ref_id'] = $data['trans_child_id'];
				$stockQueryData['ref_no'] = getPrefixNumber($data['trans_prefix'],$data['trans_no']);
				$stockQueryData['ref_date']=date('Y-m-d');
				$stockQueryData['created_by']=$this->loginID;
				$this->store($this->stockTrans,$stockQueryData);

				$totalStock = $data['totalQty'];

				$qryData = array();
				$qryData['tableName'] = $this->packingTrans; 
				$qryData['leftJoin']['packing_master'] = "packing_transaction.packing_id = packing_master.id";
				$qryData['where']['packing_master.item_id'] = $data['item_id'];
				$qryData['order_by']['packing_master.trans_date'] = 'ASC';
				$packData = $this->rows($qryData);
				foreach($packData as $pack){
					$stockeEffect = 0;
					if($pack->total_qty >= $totalStock){
						$stockeEffect = $totalStock;
					}else{
						$stockeEffect = $pack->total_qty;
					}

					$setData = Array();
					$setData['tableName'] = $this->packingTrans;
					$setData['where']['id'] = $pack->id;
					$setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$stockeEffect;
					$this->setValue($setData);

					$totalStock -= $stockeEffect;
					if($totalStock == 0){ break;}
				}
				
				$setData = Array();
				$setData['tableName'] = 'trans_child';
				$setData['where']['id'] = $data['trans_child_id'];
				$setData['set']['stock_eff'] = 'stock_eff, + 1';
				$setData['set']['packing_qty'] = 'packing_qty, + '.$data['totalQty'];
				$this->setValue($setData);
			endforeach;
			
			$result = ['status'=>1,'message'=>'Material Dispatch SuccessFully.'];
			if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function saveExportPacking($data){
        try {
            $this->db->trans_begin();
            if(!empty($data['id'])){
                $packData = $this->getExportData(['trans_no'=>$data['trans_no'],'packing_type'=>$data['packing_type']]);
                $itemData = array_column($data['item_data'],'id');
                foreach($packData as $row){
                    if (!in_array($row->id, $itemData)):
                        
                        if($data['packing_type'] ==2){
                            $setData = Array();
                            $setData['tableName'] = 'packing_request';
                            $setData['where']['id'] = $row->req_id;
                            $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$row->total_qty;
                            $this->setValue($setData);
                            $this->remove($this->stockTrans,['ref_id'=>$row->id,'ref_type'=>35]);
                        }
                        $this->trash($this->exportPacking,['id'=>$row->id]);
                    endif;
                }
              
            }
            foreach($data['item_data'] as $row):
                if(empty($row['id'])):
                    $row['created_by'] = $this->loginId;
                else:
                    $exportData = $this->getExportDetail($row['id']);
                    $setData = Array();
                    $setData['tableName'] = 'packing_request';
                    $setData['where']['id'] = $row['req_id'];
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$exportData->total_qty;
                    $this->setValue($setData);
                    
                    $this->remove($this->stockTrans,['ref_id'=>$row['id'],'ref_type'=>35]);

                    $row['updated_by'] = $this->loginId;
                endif;
                $netWt = round(($row['total_qty'] * $row['wt_pcs']),3);
                
                $exportData = [
                    'id'=>$row['id'],
                    'packing_type'=>$data['packing_type'],
                    'item_id'=>$row['item_id'],
                    'trans_no'=>$data['trans_no'],
                    'trans_prefix'=>$data['trans_prefix'],
                    'package_no'=>$row['package_no'],
                    'packing_date'=>$data['packing_date'],
                    'req_id'=>$row['req_id'],
                    'party_id'=>$row['party_id'],
                    'so_id'=>$row['so_id'],
                    'so_trans_id'=>$row['so_trans_id'],
                    'qty_box'=>$row['qty_per_box'],
                    'total_box'=>$row['total_box'],
                    'total_qty'=>$row['total_qty'],
                    'wpp'=>$row['wt_pcs'],
                    'pack_weight'=>$row['packing_wt'],
                    'wooden_weight'=>$row['wooden_wt'],
                    'wooden_size'=>$row['box_size'],
                    'net_wt'=>$netWt,
                    'gross_wt'=>($netWt + $row['packing_wt'])
                ];
                if(empty($row['id'])){ 
                    $exportData['created_by'] = $data['created_by']; 
                }else{ 
                    $exportData['updated_by'] = $data['updated_by']; 
                }
                $result = $this->store($this->exportPacking,$exportData,'Packing');
                $packingId = (empty($row['id']))?$result['insert_id']:$row['id'];
  
                if($data['packing_type'] == 2){
                    /* Box Stock Deduction */
                    $stockQueryData = array();
                    $stockQueryData['id']="";
                    $stockQueryData['location_id']=$row['location_id'];
                    $stockQueryData['batch_no'] = $row['batch_no'];
                    $stockQueryData['trans_type']=2;
                    $stockQueryData['item_id']=$row['item_id'];
                    $stockQueryData['qty'] = '-'.$row['total_qty'];
                    $stockQueryData['size'] = $row['qty_per_box'];
                    $stockQueryData['ref_type']=35;
                    $stockQueryData['ref_id']=$packingId;
                    $stockQueryData['trans_ref_id']=$row['req_id'];
                    $stockQueryData['ref_no']=$data['trans_number'];
                    $stockQueryData['ref_date']=$data['packing_date'];
                    $stockQueryData['created_by']=$this->loginID; 
                    $this->store($this->stockTrans,$stockQueryData);

                    $setData = Array();
                    $setData['tableName'] = 'packing_request';
                    $setData['where']['id'] = $row['req_id'];
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$row['total_qty'];
                    $this->setValue($setData);
                }
               
                $this->store('packing_request',['id'=>$row['req_id'],'status'=>$data['packing_type']]);
            endforeach;

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getExportDTRows($data){
        $data['tableName'] = $this->exportPacking;
        $data['select'] = "export_packing.id,export_packing.so_id,export_packing.comm_pack_id,export_packing.req_id,export_packing.packing_type,export_packing.trans_no,export_packing.trans_prefix,CONCAT(export_packing.trans_prefix,export_packing.trans_no) as trans_number,export_packing.packing_date,item_master.item_code,item_master.item_name,export_packing.qty_box,export_packing.total_box,export_packing.total_qty,export_packing.remark,(CASE WHEN export_packing.so_trans_id = 0 THEN 'Self Packing' ELSE CONCAT(trans_main.trans_prefix,trans_main.trans_no) END) as so_no,CONCAT(packing_request.trans_prefix,packing_request.trans_no) as req_no";
        $data['leftJoin']['item_master'] = "export_packing.item_id = item_master.id";
        $data['leftJoin']['trans_child'] = "export_packing.so_trans_id = trans_child.id";
        $data['leftJoin']['trans_child'] = "export_packing.so_trans_id = trans_child.id";
        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['leftJoin']['packing_request'] = "export_packing.req_id = packing_request.id";
        $data['where']['export_packing.packing_type'] = $data['packing_type'];

        if($data['status'] == 0):
            $data['where']['export_packing.comm_pack_id'] = 0;
        else:
            $data['where']['export_packing.comm_pack_id >'] = 0;
        endif;
        
        $data['order_by']['export_packing.trans_no'] = "DESC";

        $data['searchCol'][] = "CONCAT(export_packing.trans_prefix,export_packing.trans_no)";
        $data['searchCol'][] = "DATE_FORMAT(export_packing.packing_date,'%d-%m-%Y')";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "export_packing.qty_box";
        $data['searchCol'][] = "export_packing.total_box";
        $data['searchCol'][] = "export_packing.total_qty";
        $data['searchCol'][] = "export_packing.remark";
        
		$columns =array('','');
        foreach($data['searchCol'] as $key=>$value):
                $columns[] = $value;
        endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getExportData($postData){
        $queryData = array();
        $queryData['tableName'] = $this->exportPacking;
        $queryData['select'] = "export_packing.*,item_master.item_type,item_master.item_code,item_master.item_name,item_master.description,item_master.item_alias,item_master.part_no,hsn_master.hsn_code,hsn_master.description as hsn_desc,som.currency,stock_transaction.batch_no,stock_transaction.location_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = export_packing.item_id";
        $queryData['leftJoin']['hsn_master'] = "item_master.hsn_code = hsn_master.hsn_code";
        $queryData['leftJoin']['trans_main as som'] = "export_packing.so_id = som.id";
        $queryData['leftJoin']['stock_transaction'] = "export_packing.id = stock_transaction.ref_id AND stock_transaction.ref_type = 35 AND stock_transaction.trans_type=2";
        if(!empty( $postData['trans_no'])){$queryData['where']['export_packing.trans_no'] = $postData['trans_no'];}
        if(!empty( $postData['package_no'])){$queryData['where']['export_packing.package_no'] = $postData['package_no'];}
        if(!empty( $postData['packing_type'])){$queryData['where']['export_packing.packing_type'] = $postData['packing_type'];}
        if(!empty( $postData['req_id'])){$queryData['where']['export_packing.req_id'] = $postData['req_id'];}
        if(!empty( $postData['item_id'])){$queryData['where']['export_packing.item_id'] = $postData['item_id'];}
        return $this->rows($queryData);
    }

    public function getExportDataForPrint($postData){
        $queryData = array();
        $queryData['tableName'] = $this->exportPacking;
        $queryData['select'] = "export_packing.*,SUM(export_packing.total_box) as total_box,SUM(export_packing.total_qty) as total_qty,SUM(export_packing.net_wt) as netWeight,SUM(export_packing.gross_wt) as grossWeight,item_master.item_type,item_master.item_code,item_master.item_name,item_master.description,item_master.item_alias,item_master.part_no,hsn_master.hsn_code,hsn_master.description as hsn_desc,som.currency,stock_transaction.batch_no,stock_transaction.location_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = export_packing.item_id";
        $queryData['leftJoin']['hsn_master'] = "item_master.hsn_code = hsn_master.hsn_code";
        $queryData['leftJoin']['trans_main as som'] = "export_packing.so_id = som.id";
        $queryData['leftJoin']['stock_transaction'] = "export_packing.id = stock_transaction.ref_id AND stock_transaction.ref_type = 35 AND stock_transaction.trans_type=2";
        if(!empty( $postData['trans_no'])){$queryData['where']['export_packing.trans_no'] = $postData['trans_no'];}
        if(!empty( $postData['package_no'])){$queryData['where']['export_packing.package_no'] = $postData['package_no'];}
        if(!empty( $postData['packing_type'])){$queryData['where']['export_packing.packing_type'] = $postData['packing_type'];}
        if(!empty( $postData['req_id'])){$queryData['where']['export_packing.req_id'] = $postData['req_id'];}
        if(!empty( $postData['item_id'])){$queryData['where']['export_packing.item_id'] = $postData['item_id'];}
        $queryData['group_by'][] = 'export_packing.qty_box';
        $queryData['group_by'][] = 'export_packing.item_id';
        return $this->rows($queryData);
    }

    public function getExportDetail($id){
        $queryData = array();
        $queryData['tableName'] = $this->exportPacking;
        $queryData['select'] = "export_packing.*,item_master.item_type,item_master.item_code,item_master.item_name,item_master.description,item_master.item_alias,hsn_master.hsn_code,hsn_master.description as hsn_desc,som.currency,stock_transaction.batch_no,stock_transaction.location_id,party_master.party_code";
        $queryData['leftJoin']['item_master'] = "item_master.id = export_packing.item_id";
        $queryData['leftJoin']['hsn_master'] = "item_master.hsn_code = hsn_master.hsn_code";
        $queryData['leftJoin']['trans_main as som'] = "export_packing.so_id = som.id";
        $queryData['leftJoin']['party_master'] = "som.party_id = party_master.id";
        $queryData['leftJoin']['stock_transaction'] = "export_packing.id = stock_transaction.ref_id AND stock_transaction.ref_type = 35 AND stock_transaction.trans_type=2";
        $queryData['where']['export_packing.id'] = $id;
        return $this->row($queryData);
    }

    public function getNextExportNo($packing_type){
        $data['tableName'] = $this->exportPacking;
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['packing_type'] = $packing_type;
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo; 
    }

    public function deleteExportPacking($data){
        try { //print_r($data);
            $this->db->trans_begin();
            $packData = $this->getExportData(['trans_no'=>$data['trans_no'],'packing_type'=>$data['packing_type']]);
            $prevRecord = $this->getExportData(['req_id'=>$data['req_id'],'packing_type'=>($data['packing_type']==2)?1:2]);
            foreach($packData as $row){
                if($data['packing_type'] ==2){
                    $setData = Array();
                    $setData['tableName'] = 'packing_request';
                    $setData['where']['id'] = $row->req_id;
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$row->total_qty;
                    $this->setValue($setData);
                    
                    $this->remove($this->stockTrans,['ref_id'=>$row->id,'ref_type'=>35]);
                }
                $this->trash($this->exportPacking,['id'=>$row->id]);
            }
            if(!empty($prevRecord) && $data['packing_type'] ==2){
                $this->store( 'packing_request',['id'=>$data['req_id'],'status'=>1]);
            }
            if(empty($prevRecord) && ($data['packing_type'] ==1 || $data['packing_type'] ==2)){
                $this->store( 'packing_request',['id'=>$data['req_id'],'status'=>0]);
            }
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status'=>1,'message'=>"Successfully Deleted"];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    public function packingTransGroupByPackage($postData){
        $queryData = array();
        $queryData['tableName'] = $this->exportPacking;
        if(!empty( $postData['trans_no'])){$queryData['where']['export_packing.trans_no'] = $postData['trans_no'];}
        if(!empty( $postData['packing_type'])){$queryData['where']['export_packing.packing_type'] = $postData['packing_type'];}
        $queryData['group_by'][] = "export_packing.package_no";
        $queryData['order_by']['cast(export_packing.package_no as unsigned)'] = "ASC";
        $result = $this->rows($queryData);
        return $result;
    }
    
    public function getPackingData($transNo,$packing_type=2){
        $queryData = array();
        $queryData['tableName'] = $this->exportPacking;
        $queryData['where']['trans_no'] = $transNo;
        $queryData['where']['packing_type'] = $packing_type;
        $result = $this->rows($queryData);
        return $result;
    }
    
    
    public function getPackingIds($postData){
        $queryData['tableName'] = 'packing_master';
        $queryData['select'] = 'GROUP_CONCAT(packing_master.id) As pack_ids';
        $queryData['where_in']['packing_master.trans_number'] = $postData['trans_number'];
        $result = $this->row($queryData);
        return $result;
    }
}
?>