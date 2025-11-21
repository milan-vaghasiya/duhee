<?php
class HeatTreatmentModel extends MasterModel{
    private $jobCard = "job_card";
    private $heat_treatment_master = "heat_treatment_master";
    private $heat_treatment_trans = "heat_treatment_trans";
    private $stock_transaction = "stock_transaction";
    private $itemMaster = "item_master";

    public function getNextTransNo($data){
        $data['tableName'] = $this->heat_treatment_master;
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['YEAR(trans_date)'] =  $data['year'];
        $data['where']['MONTH(trans_date)'] = $data['month'];
        if(!empty($data['furnace_id'])){ $data['where']['furnace_id'] = $data['furnace_id']; }
        $maxNo = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($maxNo))?($maxNo + 1):1;
		return $nextTransNo;
    }

    public function getDTRows($data){       
        $style="style='margin:0rem;'"; 
        $data['tableName'] = $this->heat_treatment_master;
        $data['select'] = 'heat_treatment_master.*,GROUP_CONCAT(CONCAT(item_master.item_code,item_master.item_name) separator "<hr '.$style.'>") AS item_name,GROUP_CONCAT(heat_treatment_trans.batch_no separator "<hr '.$style.'>") AS batch_no,GROUP_CONCAT(heat_treatment_trans.qty separator "<hr '.$style.'>") AS qty,GROUP_CONCAT(heat_treatment_trans.wt_pcs separator "<hr '.$style.'>") AS wt_pcs,GROUP_CONCAT(heat_treatment_trans.total_kg separator "<hr '.$style.'>") AS kgs';
        $data['leftJoin']['heat_treatment_trans'] = "heat_treatment_trans.trans_main_id = heat_treatment_master.id";
        $data['leftJoin']['item_master'] = "item_master.id = heat_treatment_trans.item_id";
        $data['where']['heat_treatment_master.status']=$data['status'];
        $data['group_by'][] = "heat_treatment_master.id";
        $data['order_by']['heat_treatment_master.id'] = "DESC";
        
        $data['searchCol'][] = "DATE_FORMAT(heat_treatment_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "heat_treatment_master.trans_number";
        $data['searchCol'][] = "heat_treatment_master.furnace_no";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "heat_treatment_trans.batch_no";
        $data['searchCol'][] = "heat_treatment_trans.qty";
        $data['searchCol'][] = "heat_treatment_trans.wt_pcs";
        $data['searchCol'][] = "heat_treatment_trans.total_kg";
        $data['searchCol'][] = "heat_treatment_master.total_nos";
        $data['searchCol'][] = "heat_treatment_master.total_kgs";

		$columns =array('','','heat_treatment_master.trans_date','heat_treatment_master.trans_number','heat_treatment_master.furnace_no','item_master.item_name','"heat_treatment_trans.batch_no','heat_treatment_trans.qty','heat_treatment_trans.wt_pcs','heat_treatment_trans.total_kg','heat_treatment_master.total_nos','heat_treatment_master.total_kgs');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
    }

    public function save($data){
        try {
            $this->db->trans_begin();
            
            $masterData = [
                'id'=>$data['id'],
                'furnace_id'=>$data['furnace_id'],
                'furnace_no'=>$data['furnace_no'],
                'trans_number'=>$data['trans_number'],
                'trans_date'=>$data['trans_date'],
                'total_nos'=>array_sum($data['qty']),
                'total_kgs'=>array_sum($data['total_kg']),
                'created_by'=>$this->loginId
            ];
            if(empty($data['id'])){
                $data['trans_no'] = $masterData['trans_no'] = $this->getNextTransNo(['month'=>date("m",strtotime($data['trans_date'])),'year'=>date("Y",strtotime($data['trans_date'])), 'furnace_id'=>$data['furnace_id']]);
                
            }else{
                $transData = $this->getHeatTransData(['trans_main_id'=>$data['id']]);
                foreach($transData as $row){
                    if(!in_array($row->id,$data['trans_id'])){
                        $this->trash($this->heat_treatment_trans,['id'=>$row->id]);
                    }
                }
                $this->remove($this->stock_transaction,['ref_id'=>$data['id'],'ref_type'=>37]);
            }
            //$masterData['trans_number'] = $data['furnace_no'].sprintf("%02d",$data['trans_no']).n2m(date("m",strtotime($data['trans_date']))).date("y",strtotime($data['trans_date'])); 
            
            $result= $this->store($this->heat_treatment_master,$masterData);
            $trans_main_id = !empty($data['id'])?$data['id']:$result['insert_id'];
            foreach ($data['item_id'] as $key => $item_id) :
                $query['tableName'] = $this->stock_transaction;
                $query['where']['batch_no'] = $data['batch_no'][$key];
                $query['where']['trans_type'] =1;
                $batchData = $this->row($query);
                
                $childData = [
                    'id' => $data['trans_id'][$key],
                    'trans_main_id' => $trans_main_id,
                    'job_card_id' => $batchData->ref_id,
                    'item_id' => $item_id,
                    'batch_no' => $data['batch_no'][$key],
                    'qty' => $data['qty'][$key],
                    'wt_pcs' => $data['wt_pcs'][$key],
                    'total_kg' => $data['total_kg'][$key],
                    'remark' => $data['remark'][$key],
                    'created_by' => $masterData['created_by']
                ];
                $transResult = $this->store($this->heat_treatment_trans, $childData);
                $child_id = !empty($data['trans_id'][$key])?$data['trans_id'][$key]:$transResult['insert_id'];
                /** Stock Effect */
                $stockEffect =[
                    'id'=>'',
                    'item_id'=>$item_id,
                    'location_id'=>$this->HEAT_TREAT_STORE->id,
                    'batch_no'=>$data['batch_no'][$key],
                    'qty'=>'-'.$data['qty'][$key],
                    'ref_id'=>$trans_main_id,
                    'trans_ref_id'=>$child_id,
                    'ref_no'=>$masterData['trans_number'],
                    'trans_type'=>2,
                    'ref_type'=>37,
                ];
                $this->store($this->stock_transaction, $stockEffect);
            endforeach;
            $result = ['status' => 1, 'message' => "Lot saved successfully", 'url' => base_url('heatTreatment')];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getHeatTreatMasterData($postData){
        $data['tableName'] = $this->heat_treatment_master;
        $data['select'] = "heat_treatment_master.*";
        $data['where']['heat_treatment_master.id'] = $postData['id'];
		return $this->row($data);
    }

    public function getHeatTransData($postData){
        $data['tableName'] = $this->heat_treatment_trans;
        $data['select'] = "heat_treatment_trans.*,heat_treatment_master.trans_number";
        $data['leftJoin']['heat_treatment_master'] ="heat_treatment_master.id = heat_treatment_trans.trans_main_id"; 
        if(!empty($postData['trans_main_id'])){$data['where']['heat_treatment_trans.trans_main_id'] = $postData['trans_main_id'];}
		return $this->rows($data);
    }

    public function delete($id){
        try {
            $this->db->trans_begin();

            $this->trash($this->stock_transaction,['ref_id'=>$id,'ref_type'=>37]);
            $this->trash($this->heat_treatment_trans,['trans_main_id'=>$id]);
            $result = $this->trash($this->heat_treatment_master,['id'=>$id]);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    
    public function completeFurnace($id){
        try {
            $this->db->trans_begin();
            $transData = $this->getHeatTransData(['trans_main_id'=>$id]);
            foreach($transData as $row){
                $stockEffect =[
                    'id'=>'',
                    'item_id'=>$row->item_id,
                    'location_id'=>$this->PACK_STORE->id,
                    'batch_no'=>$row->trans_number.'~'.$row->batch_no,
                    'qty'=>$row->qty,
                    'ref_id'=>$row->trans_main_id,
                    'trans_ref_id'=>$row->id,
                    'trans_type'=>1,
                    'ref_type'=>38,
                ];
                $this->store($this->stock_transaction, $stockEffect);
            }
            $result = $this->store($this->heat_treatment_master,['id'=>$id,'status'=>1]);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    
    public function getItemList(){
		$data['tableName'] = $this->itemMaster;
	    $data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.full_name, item_master.item_type,item_master.wt_pcs,external_heat_treatment.batch_wt";
		$data['leftJoin']['external_heat_treatment'] = "external_heat_treatment.item_id = item_master.id";
		$data['where']['item_master.item_type'] = 1;
		return $this->rows($data);
	}
	
    public function getMSHeatTreatData($postData){
        $data['tableName'] = $this->heat_treatment_master;
       $data['select'] = "heat_treatment_master.*,job_card.wo_no,sum(heat_treatment_trans.qty) as qty,item_master.full_name,item_master.part_no,external_heat_treatment.carb_drg_no,external_heat_treatment.case_aim, item_master.material_grade, st.batch_no as batch_no,mir_transaction.mill_heat_no as mill_heat_no, GROUP_CONCAT(CONCAT(item_master.item_code,item_master.item_name) separator '<br>') AS item_name, GROUP_CONCAT(item_master.part_no separator '<br>') AS partNo, GROUP_CONCAT(mir_transaction.mill_heat_no separator '<br>') AS heat_no, GROUP_CONCAT(jobBom.material_grade separator '<br>') AS materialGrade, GROUP_CONCAT(heat_treatment_trans.batch_no separator '<br>') AS batchNo";
        $data['leftJoin']['heat_treatment_trans'] ="heat_treatment_master.id = heat_treatment_trans.trans_main_id"; 
        $data['leftJoin']['item_master'] ="item_master.id = heat_treatment_trans.item_id"; 
        $data['leftJoin']['external_heat_treatment'] ="external_heat_treatment.item_id = heat_treatment_trans.item_id";
        $data['leftJoin']['job_card'] ="job_card.id = heat_treatment_trans.job_card_id";
        $data['leftJoin']['(SELECT batch_no,item_id,ref_id FROM stock_transaction WHERE is_delete = 0 AND ref_type = 3 GROUP BY batch_no,ref_id) as st'] = "st.ref_id = heat_treatment_trans.job_card_id";
        $data['leftJoin']['(select job_bom.job_card_id,im.material_grade from job_bom left join item_master im on im.id = job_bom.item_id where job_bom.is_delete = 0) as jobBom'] = "jobBom.job_card_id = heat_treatment_trans.job_card_id";
        $data['leftJoin']['mir_transaction'] = "mir_transaction.batch_no = st.batch_no AND mir_transaction.item_id = st.item_id"; 
        $data['where']['heat_treatment_master.id'] = $postData['id'];
		return $this->row($data);
    }

    public function saveMsOutput($data){ 
        try{
			$this->db->trans_begin();
			$result = $this->store("ms_output",$data);
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
    }

    public function getMSOutPutData($postData){
        $data['tableName'] = "ms_output";
        $data['select'] = "ms_output.*,employee_master.emp_name";
        $data['leftJoin']['employee_master'] ="employee_master.id = ms_output.created_by"; 
        $data['where']['ms_output.ht_id'] = $postData['ht_id'];
		return $this->row($data);
    }
    
    public function getHeatTreatData($postData){
        $data['tableName'] = $this->heat_treatment_trans;
        $data['select'] = "heat_treatment_trans.*";
		
        if(!empty($postData['id'])){$data['where']['heat_treatment_trans.id'] = $postData['id'];}
		
		if(!empty($postData['single_row'])){
			return $this->row($data);
		}else{
			return $this->rows($data);
		}
    }
}
?>