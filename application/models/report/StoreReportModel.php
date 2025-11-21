<?php 
class StoreReportModel extends MasterModel
{
    private $stockTrans = "stock_transaction";
    private $jobDispatch = "job_material_dispatch";
    private $itemMaster = "item_master";
	private $itemGroup = "item_group";

	/* Issue Register Data */
    public function getIssueRegister($data){
        $queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'stock_transaction.*,job_material_dispatch.collected_by,job_material_dispatch.remark,job_material_dispatch.id as dispatch_id,item_master.item_name';
		$queryData['leftJoin']['job_material_dispatch'] = 'job_material_dispatch.id = stock_transaction.ref_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = stock_transaction.item_id';
		$queryData['where']['stock_transaction.ref_type'] = 3;
        $queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['stock_transaction.ref_date'] = 'ASC';

		$result = $this->rows($queryData);
		return $result;
    }

    public function getIssueItemPrice($dispatch_id){
        $queryData = array();
		$queryData['tableName'] = $this->jobDispatch;
        $queryData['select'] = 'job_material_dispatch.*,grn_transaction.price as ItemPrice';
		$queryData['join']['grn_transaction'] = 'grn_transaction.item_id = job_material_dispatch.req_item_id';
        $queryData['where']['job_material_dispatch.id'] = $dispatch_id;
        $queryData['order_by']['job_material_dispatch.dispatch_date'] = 'ASC';
        $queryData['limit'] = 1;		
        $result = $this->rows($queryData);  
		return $result;
    }

	/* Stock Register */
	public function getStockReceiptQty($data){
		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as rqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
		$queryData['where']['stock_transaction.trans_type'] = 1;
        $queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		return $this->row($queryData);
	}

	public function getStockIssuedQty($data){
		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as iqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
		$queryData['where']['stock_transaction.trans_type'] = 2;
        $queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		return $this->row($queryData);
	}

	/* Consumable */
    public function getConsumable(){
		$data['tableName'] = $this->itemMaster;
		$data['where']['item_master.item_type'] = 2;
		return $this->rows($data);
	}

	/* Raw Material */
    public function getRawMaterialReport(){
		$data['tableName'] = $this->itemMaster;
		$data['where']['item_master.item_type'] = 3;
		return $this->rows($data);
	}

	/* Group wise Item List */
    public function getItemsByGroup($data){
		$data['tableName'] = $this->itemMaster;
		$data['where']['item_master.item_type'] = $data['item_type'];
		return $this->rows($data);
	}

	/* Inventory Monitoring */
	public function getItemGroup(){
		$data['tableName'] = $this->itemGroup;
		return $this->rows($data);
	}
	
	public function getOpningStockQty($data){
		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as osqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
        $queryData['where']['stock_transaction.ref_date < '] = $data['from_date'];
		return $this->row($queryData);
	}

	public function getItemPrice($data){
        $queryData = array();
		$queryData['tableName'] = "grn_transaction";
        $queryData['select'] = 'SUM(grn_transaction.price * grn_transaction.qty) as amount';
		$queryData['join']['grn_master'] = 'grn_master.id = grn_transaction.grn_id';
        $queryData['where']['grn_transaction.item_id'] =  $data['item_id'];
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		return $this->row($queryData);  
    }

    /* Stock Statement finish producct */
	public function getFinishProduct(){
		$queryData['tableName'] = $this->itemMaster;
		$queryData['select'] = 'item_master.*,party_master.party_name';
		$queryData['join']['party_master'] = 'party_master.id = item_master.party_id';
		$queryData['where']['item_master.item_type'] = 1;
		return $this->rows($queryData);
	}

	public function getClosingStockQty($data){
		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as csqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
		$queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		return $this->row($queryData);
	}
	
	
    /* LOCATION WISE STOCK REPORT */
	public function getLocationWiseStockReport($data)
    {
        $data['tableName'] = $this->stockTrans;
        $data['select'] = 'stock_transaction.*,item_master.item_name,item_master.item_code,SUM(stock_transaction.qty) as qty';
        $data['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
		$data['where']['stock_transaction.location_id'] = $data['location_id'];
		$data['where']['item_master.item_type'] = $data['item_type'];
		$data['group_by'][] = 'stock_transaction.item_id,stock_transaction.batch_no';
		return  $this->rows($data);
    }
    
    public function getInventoryMonitor($postData){
        $data['tableName'] =  $this->itemMaster;
		$data['select'] = 'item_master.id, item_master.item_name, item_master.item_code, item_master.item_type, item_master.price, item_master.rev_no, item_master.drawing_no,item_master.min_qty, currency.inrrate,party_master.party_name,stock_transaction.location_id';
		if($postData['item_type'] != 1):
    		$data['select'] .= ',SUM(CASE WHEN stock_transaction.created_at <="'.$postData['to_date'].'" AND stock_transaction.trans_type = 1 AND stock_transaction.is_delete = 0 AND stock_transaction.ref_type != -1 THEN stock_transaction.qty ELSE 0 END) AS rqty';
    		$data['select'] .= ',SUM(CASE WHEN stock_transaction.created_at <="'.$postData['to_date'].'" AND stock_transaction.trans_type = 2 AND stock_transaction.is_delete = 0 THEN stock_transaction.qty ELSE 0 END) AS iqty';
    		$data['select'] .= ',SUM(CASE WHEN stock_transaction.ref_type = -1 AND stock_transaction.is_delete = 0 THEN stock_transaction.qty ELSE 0 END) AS opening_qty';
		else:
    		$data['select'] .= ',SUM(CASE WHEN stock_transaction.created_at <="'.$postData['to_date'].'" AND stock_transaction.trans_type = 1 AND stock_transaction.is_delete = 0 AND stock_transaction.ref_type != -1 AND stock_transaction.location_id = "'.$this->RTD_STORE->id.'" THEN stock_transaction.qty ELSE 0 END) AS rqty';
    		$data['select'] .= ',SUM(CASE WHEN stock_transaction.created_at <="'.$postData['to_date'].'" AND stock_transaction.trans_type = 2 AND stock_transaction.is_delete = 0 AND stock_transaction.location_id = "'.$this->RTD_STORE->id.'" THEN stock_transaction.qty ELSE 0 END) AS iqty';
    		$data['select'] .= ',SUM(CASE WHEN stock_transaction.ref_type = -1 AND stock_transaction.is_delete = 0 AND stock_transaction.location_id = "'.$this->RTD_STORE->id.'" THEN stock_transaction.qty ELSE 0 END) AS opening_qty';
		endif;
		
		$data['leftJoin']['stock_transaction'] = 'stock_transaction.item_id = item_master.id';
		$data['leftJoin']['party_master'] = 'item_master.party_id = party_master.id';
		$data['leftJoin']['currency'] = 'currency.currency = party_master.currency';
		
		$data['where']['item_master.item_type'] = $postData['item_type'];
		$data['where']['stock_transaction.location_id'] = $postData['location_id'];

		$data['where']['stock_transaction.is_delete'] = 0;
		$data['group_by'][] = 'stock_transaction.item_id';
		return $this->rows($data);
    }
	
	public function getLastPurchasePrice($item_id){
        $data['tableName'] = 'purchase_order_trans';
        $data['select'] = 'purchase_order_trans.price,purchase_order_trans.qty,purchase_order_trans.unit_id';
        $data['where']['purchase_order_trans.item_id'] = $item_id;
        $data['order_by']['purchase_order_trans.id'] = 'DESC';
        return $this->row($data);
    }
}
?>