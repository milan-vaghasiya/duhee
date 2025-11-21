<?php
class CommonFgModel extends MasterModel{
    private $itemMaster = "item_master";
    private $itemKit = "item_kit";
    private $product_process = "product_process";

    public function getDTRows($data){
        $data['tableName'] = $this->itemMaster;
        $data['where']['item_type'] = 10;
        $data['searchCol'][] = "item_code";
        $data['searchCol'][] = "item_name";
        $data['searchCol'][] = "description";
		$columns =array('','','item_code','item_name','description');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getCommonFgDetails($id){
        $data['tableName'] = $this->itemMaster;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        
            /* Save Item Data in item_master Table */
            $itemMasterData = [
                'id'=>$data['id'],
                'item_code'=>$data['item_code'],
                'item_name'=>$data['item_name'],
                'full_name'=>'['.$data['item_code'].']'.$data['item_name'],
                'make_brand'=>$data['make_brand'],
                'description'=>$data['description'],
                'item_type'=>10
            ];

            $itemMasterSave = $this->store($this->itemMaster,$itemMasterData);
            $itemId = !empty($data['id'])?$data['id']:$itemMasterSave['insert_id'];


            /* Save BOM Data in item_kit Table */
            $kitData = $this->getProductKitData($itemId);

            if(!empty($kitData)):
                foreach($kitData as $key=>$value):
                    if(!in_array($value->id,$data['kit_id'])){
                        $this->trash($this->itemKit,['id'=>$value->id,'kit_type'=>0],'');
                    }
                endforeach;
            endif;

            foreach($data['ref_item_id'] as $key=>$value):
                if(empty($data['kit_id'][$key])):
                    $itemKitData = [
                        'id'=>"",
                        'item_id'=>$itemId,
                        'ref_item_id'=>$value,
                        'qty'=>$data['qty'][$key]
                    ];
                    $this->store($this->itemKit,$itemKitData,'Item Kit Product');
                else:
                    $where['item_id'] = $itemId;
                    $where['kit_type'] = 0;
                    $where['id'] = $data['kit_id'][$key];
                    $this->edit($this->itemKit,$where,['qty'=>$data['qty'][$key]]);
                endif;
            endforeach;

            $result = ['status'=>1,'message'=>'Item saved successfully.'];

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }		
	}

    public function getProductKitData($id){
		$data['select'] = "item_kit.*,item_master.item_name,process_master.process_name";
		$data['leftJoin']['item_master'] = "item_master.id = item_kit.ref_item_id";
		$data['leftJoin']['process_master'] = "process_master.id = item_kit.process_id";
		$data['where']['item_kit.item_id'] = $id;
		$data['where']['item_kit.kit_type'] = 0;
		$data['tableName'] = $this->itemKit;
		return $this->rows($data);
	}

    public function getProcessKitData($id){
		$data['select'] = "product_process.*,process_master.process_name";
		$data['leftJoin']['process_master'] = "process_master.id = product_process.process_id";
		$data['where']['product_process.item_id'] = $id;
		$data['tableName'] = $this->product_process;
		return $this->rows($data);
	}

    public function delete($id){		
        $this->trash($this->itemKit,['item_id'=>$id]);
        $this->trash($this->product_process,['item_id'=>$id]);
        return $this->trash($this->itemMaster,['id'=>$id]);
    }
}
?>