<?php
class CommercialPackingModel extends MasterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $packingMaster = "packing_master";
    private $packingTrans = "packing_transaction";
    private $export_packing = "export_packing";

    public function getDTRows($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.*,packing_master.trans_number as packing_no";
        $data['leftJoin']['packing_master'] = "packing_master.id = trans_main.ref_id";
        $data['customWhere'][] = 'trans_main.entry_type IN ('.$data['entry_type'].')';
        if($data['status'] == 0):
            $data['where']['trans_main.trans_status'] = $data['status'];
        else:
            $data['where']['trans_main.trans_status'] = $data['status'];
            $data['where']['trans_main.trans_date >= '] = $this->startYearDate;
            $data['where']['trans_main.trans_date <= '] = $this->endYearDate;
        endif;

        $data['order_by']['trans_main.trans_no'] = "DESC";
        
        $data['searchCol'][] = "packing_master.trans_number";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "trans_main.doc_no";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_main.net_amount";

        $columns =array('','','trans_main.trans_number','packing_master.trans_number','trans_main.doc_no','','trans_main.party_name','','trans_main.net_amount');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getPackingNoList($party_id){
        $queryData = array();
        $queryData['tableName'] = $this->export_packing;
        $queryData['select'] = "export_packing.id,export_packing.package_no,export_packing.trans_no,export_packing.trans_prefix,export_packing.packing_date,comm_pack_id";
        $queryData['where']['export_packing.party_id'] = $party_id;
        $queryData['where']['export_packing.comm_pack_id'] =0;
        $queryData['where']['export_packing.packing_type'] =2;
        $queryData['group_by'][] = "trans_no";
        return $this->rows($queryData);
    }
    public function getAllPackingNoList($party_id){
        $queryData = array();
        $queryData['tableName'] = $this->export_packing;
        $queryData['select'] = "export_packing.id,export_packing.package_no,export_packing.trans_no,export_packing.trans_prefix,export_packing.packing_date,comm_pack_id";
        $queryData['where']['export_packing.party_id'] = $party_id;
        $queryData['where']['export_packing.packing_type'] =2;
        $queryData['group_by'][] = "trans_no";
        return $this->rows($queryData);
    }
    public function getPackingItemList($trans_no){
        $queryData = array();
        $queryData['tableName'] = $this->export_packing;
        $queryData['select'] = "export_packing.id,export_packing.item_id,export_packing.total_qty as qty,export_packing.wpp as price,export_packing.net_wt as amount,export_packing.gross_wt as net_amount,export_packing.wooden_weight as taxable_amount,item_master.item_type,item_master.item_code,item_master.item_name,item_master.description,item_master.item_alias,hsn_master.hsn_code,hsn_master.description as hsn_desc,som.currency,export_packing.comm_pack_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = export_packing.item_id";
        $queryData['leftJoin']['hsn_master'] = "item_master.hsn_code = hsn_master.hsn_code";
        $queryData['leftJoin']['trans_main as som'] = "export_packing.so_id = som.id";
        $queryData['where']['export_packing.trans_no'] = $trans_no;
        $queryData['where']['export_packing.packing_type'] =2;
        // $queryData['where']['export_packing.comm_pack_id'] = 0;
       
        return $this->rows($queryData);
    }

    public function getCustomerPOData($packing_ids){
        $queryData = array();
        $queryData['tableName'] = $this->export_packing;
        $queryData['select'] = "trans_main.id,trans_main.doc_no,trans_main.doc_date";
        $queryData['leftJoin']['trans_main'] = "export_packing.so_id = trans_main.id";
        $queryData['where_in']['export_packing.id'] = $packing_ids;
        $result = $this->rows($queryData);
        return $result;
    }

    public function save($masterData,$itemData){
        try{
            $this->db->trans_begin();
            if(!empty($masterData['id'])):
                $transData = $this->getCommercialPackingItems($masterData['id']);
                foreach($transData as $row):
                    $this->edit($this->export_packing,['id'=>$row->ref_id],['comm_pack_id'=>0,'status'=>0]);
                endforeach;
                $this->trash($this->transChild,['trans_main_id'=>$masterData['id']]);                
            else:
                $masterData['trans_date'] = date("Y-m-d");
                $masterData['trans_prefix'] = $this->transModel->getTransPrefix(19);
                $masterData['trans_no'] = $this->transModel->nextTransNo(19);
                $masterData['trans_number'] = getPrefixNumber($masterData['trans_prefix'],$masterData['trans_no']);
            endif;

            $result = $this->store($this->transMain,$masterData,'Commercial Packing');
            $result['url'] = base_url('commercialPacking');
            $masterData['id'] = (empty($masterData['id']))?$result['insert_id']:$masterData['id'];

            foreach($itemData as $row):
                $row['created_by'] = $masterData['created_by'];
                $row['trans_main_id'] = $masterData['id'];
                $row['entry_type'] = $masterData['entry_type'];
                $row['is_delete'] = 0;
                $trasnResult = $this->store($this->transChild,$row);
                if(empty($row['id'])){
                    $this->edit($this->export_packing,['id'=>$row['ref_id']],['comm_pack_id'=>$trasnResult['insert_id'],'status'=>1]);
                }
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

    public function getCommercialPackingData($id,$is_pdf = 0){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.*,party_master.party_address as buyer_address";
        $queryData['leftJoin']['party_master'] = "trans_main.party_id = party_master.id";
        $queryData['where']['trans_main.id'] = $id;
        $result = $this->row($queryData);
        if(!empty($result->extra_fields)):
            $jsonData = json_decode($result->extra_fields);
            foreach($jsonData as $key=>$value):
                $result->{$key} = $value;
            endforeach;
        endif;

        if($is_pdf == 1):
            $result->itemData = $this->getCommercialPackingItemsForPdf($id);
        else:
            $result->itemData = $this->getCommercialPackingItems($id);
        endif;
        return $result;
    }

    public function getCommercialPackingItems($id){ //print_r($id); exit;
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*,soi.price as so_price,export_packing.status as packing_status,LPAD(export_packing.package_no, 2, '0') as package_no,item_master.part_no as PartNo,item_master.drawing_no as DrgNo,item_master.rev_no as RevNo,som.currency";
        $queryData['leftJoin']['export_packing'] = "export_packing.id = trans_child.ref_id";
        $queryData['leftJoin']['trans_child as soi'] = "export_packing.comm_pack_id = soi.id";
        $queryData['leftJoin']['trans_main as som'] = "export_packing.so_id = som.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $queryData['where']['trans_child.trans_main_id'] = $id;
        $result = $this->rows($queryData);
        return $result;
    }
    
    public function getCommercialPackingItemsForPdf($id){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.item_id,trans_child.item_code,trans_child.item_name,trans_child.item_desc,trans_child.item_alias,trans_child.hsn_code,trans_child.hsn_desc,SUM(trans_child.qty) as qty,AVG(trans_child.price) as price,SUM(trans_child.amount) as amount,SUM(trans_child.net_amount) as net_amount,soi.price as so_price,export_packing.status as packing_status,LPAD(export_packing.package_no, 2, '0') as package_no,item_master.part_no as PartNo,item_master.drawing_no as DrgNo,item_master.rev_no as RevNo,export_packing.pack_weight,export_packing.wooden_weight";
        $queryData['leftJoin']['export_packing'] = "export_packing.id = trans_child.ref_id";
        $queryData['leftJoin']['trans_child as soi'] = "export_packing.comm_pack_id = soi.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $queryData['where']['trans_child.trans_main_id'] = $id;
        $queryData['group_by'][] = "trans_child.item_id";
        $queryData['order_by']['trans_child.id'] = "ASC";
        $result = $this->rows($queryData);
        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $transData = $this->getCommercialPackingItems($id);
            foreach($transData as $row):
                $this->edit($this->export_packing,['id'=>$row->ref_id],['comm_pack_id'=>0,'status'=>0]);
            endforeach;

            $this->trash($this->transChild,['trans_main_id'=>$id]);
            $result = $this->trash($this->transMain,['id'=>$id],'Commercial Packing');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
}
?>