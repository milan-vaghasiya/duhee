<?php
class GateReceiptOtherModel extends MasterModel{
    private $mirTrans = "mir_transaction";
    private $mir = "mir";
    private $stockTrans = "stock_transaction";

    public function getDTRows($data){
        $data['tableName'] = $this->mirTrans;
        $data['select'] = "mir_transaction.*,mir.item_stock_type,mir.trans_date,mir.trans_prefix,mir.trans_no,mir.qty as total_qty,party_master.party_name,item_master.full_name as item_name,item_master.item_type";
        $data['leftJoin']['mir'] = "mir.id = mir_transaction.mir_id";
        $data['leftJoin']['party_master'] = "party_master.id = mir.party_id";
        $data['leftJoin']['item_master'] = "item_master.id = mir.item_id";
        $data['where']['item_master.item_type !='] = 3;
        $data['where']['mir_transaction.type'] = 1;
        
        if($data['status'] == 0){$data['where']['mir_transaction.trans_status != '] = 3;}
        if($data['status'] == 1){$data['where']['mir_transaction.trans_status'] = 3;}
        
        $data['where']['mir.trans_type'] = 2;
        $data['group_by'][] = 'mir_transaction.mir_id';

        $data['searchCol'][] = ""; 
        $data['searchCol'][] = "";
        $data['searchCol'][] = "CONCAT(mir.trans_prefix,mir.trans_no)";
        $data['searchCol'][] = "DATE_FORMAT(mir.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "mir.qty";

        $columns = array('', '', 'CONCAT(mir.trans_prefix,mir.trans_no)', 'mir.trans_date', 'party_master.party_name', 'item_master.item_name', 'mir.qty');
        if(isset($data['order'])):
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        endif;

        return $this->pagingRows($data);
    }

    public function getGateReceiptOtherData($mir_id,$stockType = -1){
        $queryData = array();
        $queryData['tableName'] = $this->mirTrans;
        $queryData['select'] = "mir_transaction.*,item_master.full_name,item_master.item_type,mir.item_stock_type";
        $queryData['leftJoin']['mir'] = "mir.id = mir_transaction.mir_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = mir_transaction.item_id";
        $queryData['where']['mir_transaction.mir_id'] = $mir_id;
        $queryData['where']['item_master.item_type != '] = 3;
        $queryData['where']['mir_transaction.type'] = 1;
        if($stockType >= 0):
            $queryData['where']['mir.item_stock_type'] = $stockType;
        endif;
        $mirTransData = $this->rows($queryData);
        return $mirTransData;
    }

    public function acceptGI($postData){
        try{
            $this->db->trans_begin();

            $mirTransData = $this->getGateReceiptOtherData($postData['mir_id']);
            
            foreach($mirTransData as $row):
                $acceptData['id'] = $row->id;
                $acceptData['accepted_by'] = $this->loginID;
                $acceptData['accepted_at'] = date('Y-m-d H:i:s');
                $acceptData['trans_status'] = $postData['status'];
                $this->store($this->mirTrans,$acceptData);

                if($postData['status'] == 3):
                    $this->edit($this->stockTrans,['ref_type'=>1,'ref_id'=>$row->mir_id,'trans_ref_id'=>$row->id],['stock_effect' => 1]); 
                endif;
            endforeach;
            
            if($postData['status'] != 3){
                $checkGIStatus = $this->gateInward->checkGIBatchStatus($postData['mir_id']);
                if(count($checkGIStatus) > 0 ){
                    $this->store($this->mir,['id'=>$postData['mir_id'],'trans_status'=>0]);
                }else{
                    $this->store($this->mir,['id'=>$postData['mir_id'],'trans_status'=>1]);
                }
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

    public function saveMaterialInspection($data){
        try{
            $this->db->trans_begin();

            foreach($data['item_data'] as $row):
                $acceptData['id'] = $row['mir_trans_id'];
                $acceptData['accepted_by'] = $this->loginID;
                $acceptData['accepted_at'] = date('Y-m-d H:i:s');
                $acceptData['trans_status'] = $row['status'];
                $acceptData['inspection_data'] = json_encode($row);
                $this->store($this->mirTrans,$acceptData);

                $this->edit($this->stockTrans,['ref_type'=>1,'ref_id'=>$row['mir_id'],'trans_ref_id'=>$row['mir_trans_id']],['stock_effect' => 1,"qty"=>$row['ok_qty']]); 

                
                $queryData = array();
                $queryData['tableName'] = "stock_transaction";
                $queryData['where']['trans_ref_id'] = $row['mir_trans_id'];
                $queryData['where']['ref_id'] = $row['mir_id'];
                $queryData['where']['ref_type'] = 1;
                $transData = $this->row($queryData);

                if(!empty($row['short_qty'])):
                    $stockQueryData['id']="";
                    $stockQueryData['location_id']=$transData->location_id;
                    $stockQueryData['batch_no'] = $transData->batch_no;
                    $stockQueryData['trans_type']=1;
                    $stockQueryData['item_id']=$transData->item_id;
                    $stockQueryData['qty']=$row['short_qty'];
                    $stockQueryData['ref_type']=1;
                    $stockQueryData['ref_id']=$transData->ref_id;
                    $stockQueryData['trans_ref_id']=$transData->trans_ref_id;
                    $stockQueryData['ref_no']=$transData->ref_no;
                    $stockQueryData['ref_date']=date("Y-m-d");
                    $stockQueryData['ref_batch']=$transData->ref_batch;
                    $stockQueryData['created_by']=$this->loginId;
                    $stockQueryData['stock_effect'] = 0;
                    $this->store($this->stockTrans,$stockQueryData);
                endif;

                if(!empty($row['rej_qty'])):
                    $stockQueryData['id']="";
                    $stockQueryData['location_id']=$transData->location_id;
                    $stockQueryData['batch_no'] = $transData->batch_no;
                    $stockQueryData['trans_type']=1;
                    $stockQueryData['item_id']=$transData->item_id;
                    $stockQueryData['qty']=$row['rej_qty'];
                    $stockQueryData['ref_type']=1;
                    $stockQueryData['ref_id']=$transData->ref_id;
                    $stockQueryData['trans_ref_id']=$transData->trans_ref_id;
                    $stockQueryData['ref_no']=$transData->ref_no;
                    $stockQueryData['ref_date']=date("Y-m-d");
                    $stockQueryData['ref_batch']=$transData->ref_batch;
                    $stockQueryData['created_by']=$this->loginId;
                    $stockQueryData['stock_effect'] = 0;
                    $this->store($this->stockTrans,$stockQueryData);
                endif;
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>"GI Accepted Successfully"];
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
}
?>