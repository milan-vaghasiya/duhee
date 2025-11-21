<?php 
class QualityReportModel extends MasterModel
{
    private $stockTransaction = "stock_transaction";
    private $grnTrans = "grn_transaction";
	private $jobCard = "job_card";
    private $jobMaterialDispatch = "job_material_dispatch";
    private $jobRejection = "job_rejection";
    private $productKit = "item_kit";
    private $itemMaster = "item_master";
    private $rej_rw_management = "rej_rw_management";

    public function getBatchNoListForHistory(){
        $data['tableName'] = $this->stockTransaction;
        $data['select'] = 'batch_no';
        $data['group_by'][] = 'batch_no';
        $data['order_by']['batch_no'] = 'ASC';
        return $this->rows($data); 
    }

    public function getBatchHistory($data){
        $queryData['tableName'] = $this->stockTransaction;
		$queryData['select'] = "stock_transaction.*,item_master.item_name";
		$queryData['join']['item_master'] = "stock_transaction.item_id = item_master.id";
        $queryData['where']['stock_transaction.batch_no'] = $data['batch_no'];
        $queryData['order_by']['ref_date'] = 'ASC';
		$result = $this->rows($queryData);
	   	return $result;
    }
	
    public function getBatchList(){
        $data['tableName'] = $this->stockTransaction;
		$data['select'] = "stock_transaction.batch_no,stock_transaction.item_id,item_master.item_name,item_master.item_type";
		$data['join']['item_master'] = "stock_transaction.item_id = item_master.id";
        $data['where']['item_master.item_type'] = 3;
        $data['group_by'][] = 'batch_no';
        $data['order_by']['batch_no'] = 'ASC';
        return $this->rows($data); 
    }
	
    public function getBatchItemList($batch_no){
        $data['tableName'] = $this->stockTransaction;
		$data['select'] = "stock_transaction.batch_no,stock_transaction.item_id,item_master.item_name,item_master.item_type,item_master.item_code";
		$data['join']['item_master'] = "stock_transaction.item_id = item_master.id";
        $data['where']['stock_transaction.batch_no'] = $batch_no;
        $data['group_by'][] = 'stock_transaction.item_id';
        return $this->rows($data); 
    }

    public function getBatchTracability($data){
        $queryData['tableName'] = $this->stockTransaction;
		$queryData['select'] = "stock_transaction.*,item_master.item_name";
		$queryData['join']['item_master'] = "stock_transaction.item_id = item_master.id";
        $queryData['where']['stock_transaction.batch_no'] = $data['batch_no'];
        if(!empty($data['item_id'])){$queryData['where']['stock_transaction.item_id'] = $data['item_id'];}
        $queryData['order_by']['ref_date'] = 'ASC';
		$result = $this->rows($queryData);
	   	return $result;
    }

    public function getMIfgName($ref_id){
        $queryData['tableName'] = $this->stockTransaction;
		$queryData['select'] = "item_master.item_name,item_master.item_code,job_card.job_prefix,job_card.job_no";
		$queryData['join']['job_material_dispatch'] = "stock_transaction.ref_id = job_material_dispatch.id";
        $queryData['join']['job_card'] = "job_card.id = job_material_dispatch.job_card_id";
		$queryData['join']['item_master'] = "item_master.id = job_card.product_id";
        $queryData['where']['stock_transaction.ref_id'] = $ref_id;
		$result = $this->row($queryData);
	   	return $result; 
    }

    public function getReturnfgName($ref_id){
        $queryData['tableName'] = $this->stockTransaction;
		$queryData['select'] = "item_master.item_name,item_master.item_code,job_card.job_prefix,job_card.job_no";
		$queryData['join']['job_return_material'] = "stock_transaction.ref_id = job_return_material.id";
        $queryData['join']['job_card'] = "job_card.id = job_return_material.job_card_id";
		$queryData['join']['item_master'] = "item_master.id = job_card.product_id";
        $queryData['where']['stock_transaction.ref_id'] = $ref_id;
		$result = $this->row($queryData);
	   	return $result; 
    }

    public function getSupplierRatingItems($data){
        $queryData['tableName'] = $this->grnTrans;
        $queryData['select'] = "item_master.id,item_master.item_name";
		$queryData['join']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
        $queryData['join']['item_master'] = "item_master.id = grn_transaction.item_id";
        // $queryData['join']['purchase_order_trans'] = "purchase_order_trans.id = grn_transaction.po_trans_id";
        $queryData['where']['grn_master.party_id'] = $data['party_id'];
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['where']['grn_master.order_id != '] = 0;
        $queryData['where']['grn_transaction.po_trans_id != '] = 0;
        $queryData['where']['grn_master.type'] = 1;
        $queryData['group_by'][] = 'item_master.id';
		$result = $this->rows($queryData);
	   	return $result;
    }

    public function getSupplierRating($data){
        $queryData['tableName'] = $this->grnTrans;
        $queryData['select'] = "grn_transaction.*, grn_master.order_id, grn_master.grn_prefix, grn_master.grn_no, grn_master.grn_date, grn_master.remark, purchase_order_trans.delivery_date";
		$queryData['join']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
        $queryData['join']['purchase_order_trans'] = "purchase_order_trans.id = grn_transaction.po_trans_id";
        $queryData['where']['grn_transaction.item_id'] = $data['item_id'];
        $queryData['where']['grn_master.party_id'] = $data['party_id'];
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['where']['grn_master.order_id != '] = 0;
        $queryData['where']['grn_transaction.po_trans_id != '] = 0;
        $queryData['where']['grn_master.type'] = 1;
		$result = $this->rows($queryData);
	   	return $result;
    }
   
	public function getInspectedMaterialGBJ($data){
        $queryData['tableName'] = $this->jobMaterialDispatch;
        $queryData['select'] = "job_material_dispatch.job_card_id,job_material_dispatch.dispatch_qty, job_card.product_id";
        $queryData['join']['job_card'] = "job_material_dispatch.job_card_id = job_card.id";
        $queryData['where']['job_material_dispatch.dispatch_item_id'] = $data['item_id'];
        // $queryData['where']['job_card.party_id'] = $data['party_id'];
        $queryData['customWhere'][] = "job_material_dispatch.dispatch_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['group_by'][] = 'job_material_dispatch.job_card_id';
		$result = $this->rows($queryData);
		
		$qtyData = New StdClass;
		$qtyData->rQty = 0; $qtyData->aQty = 0; $qtyData->udQty = 0;$qtyData->insQty = 0;
		if(!empty($result)):
			foreach($result as $row):
				
				$queryData = Array();
				$queryData['tableName'] = $this->jobRejection;
				$queryData['select'] = 'SUM(qty) as rejQty,SUM(pending_qty) as pendingRejQty';
				$queryData['where']['job_card_id'] = $row->job_card_id;
				$queryData['where']['rejection_type_id'] = -1;
				$rejectionData = $this->row($queryData);
				
				$queryData = Array();
				$queryData['tableName'] = $this->productKit;
				$queryData['select'] = "item_kit.*";
				$queryData['where']['ref_item_id'] = $data['item_id'];
				$queryData['where']['item_id'] = $row->product_id;
				$kitData = $this->row($queryData);
				
				if(!empty($rejectionData) and !empty($kitData)):
					$qtyData->rQty += ($rejectionData->rejQty * $kitData->qty);
				endif;
				
				
				$qtyData->insQty += $row->dispatch_qty;
			endforeach;
		endif;
		$qtyData->aQty = $qtyData->insQty - $qtyData->rQty;
		
	   	return $qtyData;
    }

    public function getMeasuringDevice($type){
        $data['tableName'] = $this->itemMaster;
		$data['where']['item_master.item_type'] = $type;
		return $this->rows($data);
    }
    
    public function getMonthlyRejection($data = array()){
		$data['tableName'] = $this->rej_rw_management;
        $data['select'] = "rej_rw_management.*, item_master.item_code, item_master.item_name, machine.item_code as machine_code, machine.item_name as machine_name, employee_master.emp_code, employee_master.emp_name, job_transaction.qty as prod_qty, SUM(CASE WHEN rrm.operation_type = 1 THEN rrm.qty ELSE 0 END) as rejection_qty, SUM(CASE WHEN rrm.operation_type = 2 THEN rrm.qty ELSE 0 END) as rework_qty, SUM(CASE WHEN rrm.operation_type = 3 THEN rrm.qty ELSE 0 END) as hold_qty";

        $data['join']['job_transaction'] = "job_transaction.id = rej_rw_management.job_trans_id";
		$data['leftJoin']['job_card'] = "job_card.id = job_transaction.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = job_transaction.product_id";
        $data['leftJoin']['item_master machine'] = "machine.id = job_transaction.machine_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = job_transaction.operator_id";
        $data['leftJoin']['rej_rw_management rrm'] = "rrm.id = rej_rw_management.id";

        $data['where']['rej_rw_management.ref_id'] = 0;
		$data['customWhere'][] = "rej_rw_management.entry_date BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";

        if(!empty($data['item_id']) && $data['item_id'] != "All"){$data['where']['job_card.product_id'] = $data['item_id'];}
		if(!empty($data['machine_id']) && $data['machine_id'] != "All"){$data['where']['job_transaction.machine_id'] = $data['machine_id'];}
		if(!empty($data['emp_id']) && $data['emp_id'] != "All"){$data['where']['job_transaction.operator_id'] = $data['emp_id'];}

		if($data['type'] == 0){
			$data['group_by'][] = "job_transaction.operator_id";
		}else{
			$data['group_by'][] = "job_transaction.machine_id";
		}
        return $this->rows($data);
	}
}
?>