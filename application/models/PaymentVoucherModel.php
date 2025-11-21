<?php 
class PaymentVoucherModel extends MasterModel{
	private $transMain = "trans_main";

	public function getDtRows($data,$type=null){
		$data['tableName'] = $this->transMain;
		$data['select'] = "trans_main.*";
		$data['where']['extra_fields'] = $type;
		$data['where_in']['entry_type'] = "15,16";

		
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "CONCAT('/',trans_number)";
        $data['searchCol'][] = "DATE_FORMAT(trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_name";
        $data['searchCol'][] = "net_amount";
        $data['searchCol'][] = "doc_no";
        $data['searchCol'][] = "doc_date";
        $data['searchCol'][] = "remark";
		$columns = array('','','trans_number','trans_date','party_name','net_amount','doc_no','doc_date','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);

	}

	public function save($data){
		try{
			$this->db->trans_begin();

			$data['trans_number'] = getPrefixNumber($data['trans_prefix'],$data['trans_no']);
			$data['doc_date'] = (!empty($data['doc_date']))?$data['doc_date']:null;

			$result = $this->store($this->transMain,$data,'Voucher');
			$data['id'] = (empty($data['id']))?$result['insert_id']:$data['id'];	

			$this->transModel->ledgerEffects($data);

			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Exception $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
	}

	public function getVoucher($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->transMain;
        return $this->row($data);
    }  

	public function delete($id){
		try{
			$this->db->trans_begin();
			
			$result= $this->trash($this->transMain,['id'=>$id],'PaymentVoucher');
			$this->transModel->deleteLedgerTrans($id);

			if($this->db->trans_status() !== FALSE):
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