<?php
class JobWorkOrderModel extends MasterModel{
    private $jobworkOrder = "jobwork_order";
    private $jobworkOrderTrans = "jobwork_order_trans";

    public function getNextOrderNo(){
        $data['tableName'] = $this->jobworkOrder;
        $data['select'] = "MAX(trans_no) as jobOrderNo";
        $jobOrderNo = $this->specificRow($data)->jobOrderNo;
		$nextOrderNo = (!empty($jobOrderNo))?($jobOrderNo + 1):1;
		return $nextOrderNo;
    }

    public function getDTRows($data){
        $data['tableName'] = $this->jobworkOrder;
        $data['select'] = "jobwork_order.*,party_master.party_code,party_master.party_name";
        $data['leftJoin']['party_master'] = "party_master.id = jobwork_order.vendor_id";

        //Changed By Karmi @16/05/2022
        if($data['status'] == 1){$data['where']['jobwork_order.is_active'] = 1;}
        else{$data['where']['jobwork_order.is_active'] = 0;}        

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(jobwork_order.order_date,'%d-%m-%Y')";
        $data['searchCol'][] = "jobwork_order.trans_no";
        $data['searchCol'][] = "party_master.party_name"; 
        $data['searchCol'][] = "jobwork_order.remark";

		$columns =array('','','jobwork_order.order_date','jobwork_order.trans_no','party_master.party_name','jobwork_order.remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function save($masterData,$itemData){
        try{
            $this->db->trans_begin();
            if(empty($masterData['id'])):
                $masterData['trans_prefix'] = "JWO/".$this->shortYear."/";
                $masterData['trans_no'] = $this->getNextOrderNo();
                $masterData['trans_number'] = getPrefixNumber($masterData['trans_prefix'],$masterData['trans_no']);
                $challan = $this->store($this->jobworkOrder,$masterData);
                $challanId = $challan['insert_id'];
                $result = ['status'=>1,'message'=>'Jobwork Challan Successfully.','url'=>base_url("jobWorkOrder")];
            else:
                $this->store($this->jobworkOrder,$masterData);
                $challanId = $masterData['id'];
                $challanItems = $this->getJobworkChallanTrans($challanId);
                foreach($challanItems as $row){
                    if(!in_array($row->id,$itemData['id'])):
                        $this->trash($this->jobworkOrderTrans,['id'=>$row->id]);
                    endif;
                }
                $result = ['status'=>1,'message'=>'Jobwork Challan updated Successfully.','url'=>base_url("jobWorkOrder")];
            endif;

            
            foreach($itemData['item_id'] as $key=>$value):
                $gstData= $this->item->getHsnData($itemData['hsn_code'][$key]);
                $orderTransData = array();
                $orderTransData = $this->jobWork->getSameOrderTrans($value,$itemData['converted_product'][$key],$itemData['process_id'][$key],$masterData['vendor_id'],$masterData['id']);
                if(!empty($orderTransData)):
                        $this->store($this->jobworkOrderTrans,['id'=>$orderTransData->id,'is_active'=>0]);
                        //Master Delete
                        //$orders = $this->jobWorkOrder->getorderTrans($orderTransData->order_id);
                        // if(count($orders) == 0):
                        //     $this->store($this->jobworkOrder,['id'=>$orderTransData->id,'is_delete'=>1]);
                        // endif;
                endif;
                $transData = [
                    'id'=>$itemData['id'][$key],
                    'order_id'=>$challanId,
                    'item_id'=>$value,
                    'converted_product'=>$itemData['converted_product'][$key],
                    'process_id'=>$itemData['process_id'][$key],
                    'com_unit'=>$itemData['com_unit'][$key],
                    'process_charge'=>$itemData['process_charge'][$key],
                    'wpp'=>$itemData['wpp'][$key],
                    'hsn_code'=>$itemData['hsn_code'][$key],
                    'value_rate'=>$itemData['value_rate'][$key],
                    'variance'=>$itemData['variance'][$key],
                    'scarp_per_pcs'=>$itemData['scarp_per_pcs'][$key],
                    'scarp_rate_pcs'=>$itemData['scarp_rate_pcs'][$key],
                    'cgst'=>$gstData->cgst,
                    'sgst'=>$gstData->sgst,
                    'igst'=>$gstData->igst,
                    'created_by' => $masterData['created_by']
                ];
                
                /** Insert Record in Delivery Transaction **/
                $saveTrans = $this->store($this->jobworkOrderTrans,$transData);



            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getJobWorkOrder($id){
        $data['tableName'] = $this->jobworkOrder;
        $data['where']['id'] = $id;
        $result = $this->row($data);
        $result->itemData = $this->getJobworkChallanTrans($id);
        return $result;
    }

    public function getJobworkChallanTrans($challan_id,$item_id="",$process_id=""){
        $data['tableName'] = $this->jobworkOrderTrans;
        $data['select'] = "jobwork_order_trans.*,item_master.item_name,item_master.item_code,process_master_jobwork.process_name,unit_master.unit_name,im.full_name as converted_item,im.id as converted_product";
        $data['leftJoin']['item_master'] = "item_master.id = jobwork_order_trans.item_id";
        $data['leftJoin']['item_master as im'] = "im.id = jobwork_order_trans.converted_product";
        $data['leftJoin']['process_master_jobwork'] = "process_master_jobwork.id = jobwork_order_trans.process_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = jobwork_order_trans.com_unit";
        $data['where']['jobwork_order_trans.order_id'] = $challan_id;
        if(!empty($item_id)){ $data['where']['jobwork_order_trans.item_id'] = $item_id; }    
        if(!empty($process_id)){ $data['where']['jobwork_order_trans.process_id'] = $process_id; }
        return $this->rows($data);
    }

    public function delete($id){
        return $this->trash($this->jobworkOrder,['id'=>$id],"Job Work Order");
    }

    public function getJobworkOutData($id){
        $data['tableName'] = $this->jobworkOrder;
        $data['select'] = "jobwork_order.*,item_master.item_name,item_master.item_code, party_master.party_name,party_master.party_address,party_master.gstin";
        $data['join']['item_master'] =  "item_master.id = jobwork_order.product_id";
        $data['join']['party_master'] = "party_master.id = jobwork_order.vendor_id";
        $data['where']['jobwork_order.vendor_id !='] = 0;
        $data['where']['jobwork_order.id'] = $id;
        return $this->row($data);
    }

    //Created By Karmi @14/05/2022
    public function getorderTrans($id){
        $data['tableName'] = $this->jobworkOrderTrans;
        $data['where']['order_id'] = $id;
        $result = $this->rows($data);
        return $result;
    }
    
    //Created By Karmi @16/05/2022
    public function closeOrder($id){
        $this->store($this->jobworkOrder,['id'=>$id,'is_active'=>0]);
        $transOrders = $this->getorderTrans($id);
        foreach ($transOrders as $row) :
            $this->store($this->jobworkOrderTrans,['id'=>$row->id,'is_active'=>0]);
        endforeach;
		return ['status'=>1,'message'=>'JobWork Order De-Activated Successfully.'];
	}
}
?>