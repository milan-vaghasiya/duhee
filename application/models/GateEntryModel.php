<?php
class GateEntryModel extends MasterModel{
    private $mir = "mir";
    private $mirTrans = "mir_transaction";

    public function getNextNo(){
        $queryData['tableName'] = $this->mir;
        $queryData['select'] = "ifnull(MAX(trans_no + 1),1) as next_no";
        $queryData['where']['trans_type'] = 1;
        return $this->row($queryData)->next_no;
    }

    public function getDTRows($data){
        $data['tableName'] = $this->mir;
        $data['select'] = "count(*) as total_item,mir.trans_no,mir.trans_prefix,mir.trans_date,mir.driver_name,mir.driver_contact,mir.vehicle_no,mir.transporter,mir.vehicle_type,vehicle_types.vehicle_type as vehicle_type_name,transport_master.transport_name";
        
        $data['leftJoin']['vehicle_types'] = "vehicle_types.id = mir.vehicle_type";
        $data['leftJoin']['transport_master'] = "transport_master.id = mir.transporter";
        
        $data['where']['mir.trans_status'] = $data['status'];
        $data['where']['mir.trans_type'] = 1;
        
        $data['order_by']['mir.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "CONCAT(mir.trans_prefix,mir.trans_no)";
		$data['searchCol'][] = "DATE_FORMAT(mir.trans_date,'%d-%m-%Y')";
		$data['searchCol'][] = "mir.driver_name";
		$data['searchCol'][] = "mir.driver_contact";
		$data['searchCol'][] = "mir.vehicle_no";
		$data['searchCol'][] = "vehicle_types.vehicle_type";
		$data['searchCol'][] = "transport_master.transport_name";
        $data['searchCol'][] = "";

        $data['group_by'][] = "CONCAT(mir.trans_prefix,mir.trans_no)";

		$columns = array('', '', 'CONCAT(mir.trans_prefix,mir.trans_no)', 'mir.trans_date', 'mir.driver_name', 'mir.driver_contact', 'mir.vehicle_no', 'vehicle_types.vehicle_type', 'transport_master.transport_name','');
		if (isset($data['order'])) {
			$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
		}

		return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if(!empty($data['is_edit'])):
                $this->gateEntry->delete($data['trans_prefix'].$data['trans_no']);
            endif;

            // foreach($data['item'] as $row):
            //     $row['id'] = $row['trans_id']; unset($row['trans_id']);
            //     $row['trans_prefix'] = $data['trans_prefix'];
            //     $row['trans_type'] = $data['trans_type'];
            //     $row['trans_no'] = $data['trans_no'];
            //     $row['trans_date'] = $data['trans_date'];
            //     $row['driver_name'] = $data['driver_name'];
            //     $row['driver_contact'] = $data['driver_contact'];
            //     $row['transporter'] = $data['transporter'];
            //     $row['vehicle_type'] = $data['vehicle_type'];
            //     $row['vehicle_no'] = $data['vehicle_no'];
            //     $row['is_delete'] = 0;
            //     $row['inv_date'] = (!empty($row['inv_date']))?$row['inv_date']:null;
            //     $row['doc_date'] = (!empty($row['doc_date']))?$row['doc_date']:null;
            //     $row['inv_no'] = strtoupper($row['inv_no']);
            //     $row['doc_no'] = strtoupper($row['doc_no']);
            //     $row['item_remark'] = $row['item_remark'];
            //     if(empty($data['is_edit'])):
            //         $row['created_by'] = $this->loginId;
            //         $row['created_at'] = date("Y-m-d H:i:s");
            //     else:
            //         $row['updated_by'] = $this->loginId;
            //         $row['updated_at'] = date("Y-m-d H:i:s");
            //     endif;
            //     $this->store($this->mir,$row);
            // endforeach;

            $this->store($this->mir,$data);
            $result = ['status'=>1,'message'=>"Gate Entry saved successfully."];

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getGateEntryData($trans_number){
        $queryData['tableName'] = $this->mir;
        $queryData['select'] = "mir.*";
        $queryData['where']["CONCAT(mir.trans_prefix,mir.trans_no)"] = $trans_number;
        $queryData['group_by'][] = "CONCAT(mir.trans_prefix,mir.trans_no)";
        $result = $this->row($queryData);

        $result->itemData = $this->getGateEntryItems($trans_number);
        return $result;
    }

    public function getGateEntryItems($trans_number){
        $queryData['tableName'] = $this->mir;
        $queryData['select'] = "mir.*,vehicle_types.vehicle_type as vehicle_type_name,transport_master.transport_name,party_master.party_name,item_master.full_name as item_name";        
        $queryData['leftJoin']['vehicle_types'] = "vehicle_types.id = mir.vehicle_type";
        $queryData['leftJoin']['transport_master'] = "transport_master.id = mir.transporter";
        $queryData['leftJoin']['item_master'] = "item_master.id = mir.item_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = mir.party_id";
        $queryData['where']["CONCAT(mir.trans_prefix,mir.trans_no)"] = $trans_number;
        return $this->rows($queryData);
    }

    public function getGateEntry($id){
        $queryData = array();
        $queryData['tableName'] = $this->mir;
        $queryData['select'] = "mir.*,item_master.item_name,item_master.full_name,item_master.item_code,item_master.batch_stock,item_master.location,item_master.item_type,item_master.wkg,item_master.material_grade";
        $queryData['leftJoin']['item_master'] = "item_master.id = mir.item_id";
        $queryData['where']['mir.id'] = $id;
        $result = $this->row($queryData);
        return $result;
    }

    public function delete($trans_number){
        try{
            $this->db->trans_begin();

            $result = $this->trash($this->mir,['CONCAT(mir.trans_prefix,mir.trans_no)'=>$trans_number],'Gate Entry');

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