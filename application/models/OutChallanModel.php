<?php
class OutChallanModel extends MasterModel{
    private $transMain = "in_out_challan";
    private $transChild = "in_out_challan_trans";
    private $itemMaster = "item_master";
	private $stockTrans = "stock_transaction";

    public function nextTransNo($entry_type){
        $data['tableName'] = $this->transMain;
        $data['select'] = "MAX(challan_no) as challan_no";
        $data['where']['challan_type'] = $entry_type;
        $data['where']['challan_date >= '] = '2025-12-01';//$this->startYearDate;
        $data['where']['challan_date <= '] = $this->endYearDate;
		$trans_no = $this->specificRow($data)->challan_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):2111;        
		return $nextTransNo;
    }    
    
	public function getDTRows($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = 'in_out_challan_trans.*,in_out_challan.id as trans_main_id,in_out_challan.challan_prefix,in_out_challan.challan_no,in_out_challan.challan_type,in_out_challan.challan_date,in_out_challan.party_id,in_out_challan.party_name';
        $data['join']['in_out_challan'] = "in_out_challan.id = in_out_challan_trans.in_out_ch_id";

        if($data['status'] == 0){ 
            $data['where']['in_out_challan_trans.is_returnable'] = 1;
		    $data['customWhere'][] = '(in_out_challan_trans.qty - in_out_challan_trans.receive_qty) > 0';
            $data['where']['in_out_challan_trans.trans_type'] = 1;
        }
        if($data['status'] == 1)
        {			
		    $data['customWhere'][] = '(in_out_challan_trans.is_returnable = 0 OR (in_out_challan_trans.qty - in_out_challan_trans.receive_qty) <= 0)';
            $data['where']['in_out_challan_trans.trans_type'] = 1;
		}

        $data['searchCol'][] = "CONCAT('/',in_out_challan.challan_no)";
        $data['searchCol'][] = "DATE_FORMAT(in_out_challan.challan_date,'%d-%m-%Y')";
        $data['searchCol'][] = "in_out_challan.party_name";
        $data['searchCol'][] = "in_out_challan_trans.item_name";
        $data['searchCol'][] = "in_out_challan_trans.qty";

		$columns =array('','','in_out_challan.challan_no','in_out_challan.challan_date','in_out_challan.party_name','in_out_challan_trans.item_name','in_out_challan_trans.qty','in_out_challan_trans.item_remark');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function challanTransRow($id){
        $queryData['tableName'] = $this->transChild;
		$queryData['select'] = "in_out_challan_trans.*, item_master.item_name, item_master.item_code, in_out_challan.party_id, item_master.material_grade, item_master.wt_pcs,item_master.part_no, in_out_challan.challan_prefix, in_out_challan.challan_no,party_master.party_name, party_master.gstin, process_master.process_name, party_master.party_address, in_out_challan.challan_date";
		$queryData['leftJoin']['in_out_challan'] = "in_out_challan.id = in_out_challan_trans.in_out_ch_id";
		$queryData['leftJoin']['item_master'] = "item_master.id = in_out_challan_trans.item_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = in_out_challan.party_id";
		$queryData['leftJoin']['process_master'] = "process_master.id = in_out_challan_trans.process_id";
        $queryData['where']['in_out_challan_trans.id'] = $id;
        return $this->row($queryData);
    }

    public function challanTransRowsByRefId($id){
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['ref_id'] = $id;
        return $this->rows($queryData);
    }

    public function save($masterData,$itemData){
        try{
            $this->db->trans_begin();

            if(empty($masterData['id'])):
                $masterData['challan_no'] = $this->nextTransNo(1);
                $result = ['status'=>1,'message'=>'Challan Saved Successfully.','url'=>base_url("outChallan")];
            else:
                $mainId = $masterData['id'];
                $challanItems = $this->getOutChallanTrans($mainId);
				
                foreach($challanItems as $row):
					if($row->stock_eff == 1):
                        /** Remove Stock Transaction **/
                        $this->remove($this->stockTrans,['ref_id'=>$row->id,'trans_type'=>2,'ref_type'=>12]);
                    endif;
					
                    if((!in_array($row->id,$itemData['id'])) && $row->trans_type == 1):
                        $this->trash($this->transChild,['id'=>$row->id]);
                    endif;
                endforeach;

                $result = ['status'=>1,'message'=>'Challan updated Successfully.','url'=>base_url("outChallan")];
            endif;
            $masterDataSave = $this->store($this->transMain,$masterData);

            $masterId = (!empty($masterData['id']) ? $masterData['id'] : $masterDataSave['insert_id']);
            
            foreach($itemData['item_id'] as $key=>$value):
				$batch_qty = explode(",",$itemData['batch_qty'][$key]);
                $batch_no = explode(",",$itemData['batch_no'][$key]);
                $location_id = explode(",",$itemData['location_id'][$key]);
				
                $transData = [
                    'id' => $itemData['id'][$key],
                    'trans_type' => 1,
                    'in_out_ch_id' => $masterId,
                    'item_id' => (!empty($value) ? $value : 0),
                    'item_name' => (!empty($itemData['item_name'][$key]) ? $itemData['item_name'][$key] : NULL),
                    'qty' => $itemData['qty'][$key],
                    'is_returnable' => $itemData['is_returnable'][$key],    
                    'batch_qty' => (!empty($itemData['batch_qty'][$key]) ? $itemData['batch_qty'][$key] : NULL),
                    'batch_no' => (!empty($itemData['batch_no'][$key]) ? $itemData['batch_no'][$key] : NULL),
                    'location_id' => (!empty($itemData['location_id'][$key]) ? $itemData['location_id'][$key] : NULL),
					'stock_eff' => (!empty($itemData['stock_eff'][$key]) ? $itemData['stock_eff'][$key] : NULL),
					'gst_per' => (!empty($itemData['gst_per'][$key]) ? $itemData['gst_per'][$key] : 0),
					'hsn_code' => (!empty($itemData['hsn_code'][$key]) ? $itemData['hsn_code'][$key] : NULL),
					'price' => (!empty($itemData['price'][$key]) ? $itemData['price'][$key] : 0),
					'process_id' => (!empty($itemData['process_id'][$key]) ? $itemData['process_id'][$key] : 0),
                    'created_by' => $itemData['created_by']
                ];
                /** Insert Record in Delivery Transaction **/
                $saveTrans = $this->store($this->transChild,$transData);
				$refID = (empty($itemData['id'][$key]))?$saveTrans['insert_id']:$itemData['id'][$key];
				if($itemData['stock_eff'][$key] == 1):
                    /*** UPDATE STOCK TRANSACTION DATA ***/
                    foreach($batch_qty as $bk=>$bv):
                        $stockQueryData['id']="";
                        $stockQueryData['location_id']=$location_id[$bk];
                        if(!empty($batch_no[$bk])){$stockQueryData['batch_no'] = $batch_no[$bk];}
                        $stockQueryData['trans_type']=2;
                        $stockQueryData['item_id']=(!empty($value) ? $value : 0);
                        $stockQueryData['qty'] = "-".$bv;
                        $stockQueryData['ref_type']=12;
                        $stockQueryData['ref_id']=$refID;
                        $stockQueryData['ref_no']=getPrefixNumber($masterData['challan_prefix'],$masterData['challan_no']);
                        $stockQueryData['ref_date']=$masterData['challan_date'];
                        $stockQueryData['created_by']=$this->loginID;
                        $this->store($this->stockTrans,$stockQueryData);
                    endforeach;
                endif;
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        } 
    }

    public function getOutChallanTrans($id){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
		$queryData['select'] = "in_out_challan_trans.*, process_master.process_name";
		$queryData['leftJoin']['process_master'] = "process_master.id = in_out_challan_trans.process_id";
        $queryData['where']['in_out_ch_id'] = $id;
        return $this->rows($queryData);
    }

    public function getOutChallan($id){
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $id;
        $challanData = $this->row($queryData);
        $challanData->itemData = $this->getOutChallanTrans($id);
        return $challanData;
    }

    public function deleteChallan($id){
        try{
            $this->db->trans_begin();

            $this->trash($this->transChild,['in_out_ch_id'=>$id],'Challan');
            $result = $this->trash($this->transMain,['id'=>$id],'Challan');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        } 
    }

    public function saveReceiveItem($data){     
        try{
            $this->db->trans_begin();
			
            $setData = Array();
            $setData['tableName'] = $this->transChild;
            $setData['where']['id'] = $data['id'];
            $setData['set']['receive_qty'] = 'receive_qty, + '.$data['receive_qty'];
            $result = $this->setValue($setData);

            $transData = $this->challanTransRow($data['id']);
            if(!empty($data['batch_quantity'])){
				foreach($data['batch_quantity'] as $key=>$val){
					if(!empty($val)){
						$stockData = [
							'id' => '',
							'trans_type' => 1,
							'location_id' => (!empty($data['location'][$key]) ? $data['location'][$key] : 0),
							'batch_no' => (!empty($data['batch_number'][$key]) ? $data['batch_number'][$key] : 'General Batch'),
							'qty' => $val,
							'item_id' => $transData->item_id,
							'ref_type' => 12,
							'ref_id' => $transData->id,
							'ref_no' => (!empty($data['ref_no'][$key]) ? $data['ref_no'][$key] : NULL),
							'ref_batch' => (!empty($data['ref_batch']) ? $data['ref_batch'] : NULL),
							'ref_date' => date("Y-m-d"),
							'created_by' => $data['created_by']
						];
						$this->store($this->stockTrans,$stockData);
					}
				}
			}

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        } 
    }

    public function deleteReceiveItem($data){
        try{
            $this->db->trans_begin();
			
			$setData = Array();
            $setData['tableName'] = $this->transChild;
            $setData['where']['id'] = $data['challan_trans_id'];
            $setData['set']['receive_qty'] = 'receive_qty, - '.$data['qty'];
            $result = $this->setValue($setData);
			
            $result = $this->remove($this->stockTrans,['id'=>$data['id']],'challan');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        }
    }
    
    public function getDuheeBatch($postData){
        $queryData['tableName'] = 'stock_transaction';
        $queryData['select'] = '(SELECT job_card_id FROM job_heat_trans WHERE is_delete = 0 AND job_heat_trans.batch_no = stock_transaction.batch_no GROUP BY job_card_id) AS job_card_id';

        $queryData['where']['stock_transaction.ref_type'] = 36;
        $queryData['where_in']['stock_transaction.ref_id'] = $postData['packing_id'];
        $queryData['where']['stock_transaction.trans_type'] = 2;
        $queryData['where']['stock_transaction.location_id'] = $this->PACK_STORE->id;
        return $this->rows($queryData);
    }
}
?>