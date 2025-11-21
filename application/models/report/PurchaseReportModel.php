<?php 
class PurchaseReportModel extends MasterModel
{
    private $grnTrans = "grn_transaction";
    private $purchaseTrans = "purchase_order_trans";

    public function getPurchaseMonitoring($data){ 
        $queryData = array();    
		$queryData['tableName'] = $this->purchaseTrans;
		$queryData['select'] = 'purchase_order_trans.*,purchase_order_master.po_date,item_master.material_grade,item_master.item_name,party_master.party_name,purchase_order_master.po_prefix,purchase_order_master.po_no,purchase_order_master.remark,unit_master.unit_name';
		$queryData['join']['purchase_order_master'] = 'purchase_order_master.id = purchase_order_trans.order_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = purchase_order_trans.item_id';
		$queryData['leftJoin']['unit_master'] = 'unit_master.id = purchase_order_trans.unit_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = purchase_order_master.party_id';
        $queryData['customWhere'][] = "purchase_order_master.po_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['purchase_order_master.po_date'] = 'ASC';
		return $this->rows($queryData);
    }

	public function getPurchaseReceipt($data){ 
        $queryData = array();
		$queryData['tableName'] = 'mir';
		$queryData['select'] = 'mir.*,mir_transaction.heat_no,mir_transaction.mill_heat_no,gate_entry.inv_no as invoice_no,gate_entry.doc_no as document_no';
		$queryData['leftJoin']['mir_transaction'] = 'mir_transaction.mir_id = mir.id';
        $queryData['leftJoin']['mir as gate_entry'] ='gate_entry.id = mir.ref_id';

		$queryData['where']['mir.trans_type'] = 2;
		$queryData['where']['mir.item_id'] = $data['item_id'];
        $queryData['customWhere'][] = "mir.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['mir.trans_date'] = 'ASC';
		$result = $this->rows($queryData);
	
		return $result;
    }
    
    public function getPriceComparison($data){
        $queryData = array();
		$queryData['tableName'] = $this->purchaseTrans;
		$queryData['select'] = 'purchase_order_trans.*,purchase_order_master.po_date,item_master.item_name,party_master.party_name,purchase_order_master.po_prefix,purchase_order_master.po_no';
		$queryData['join']['purchase_order_master'] = 'purchase_order_master.id = purchase_order_trans.order_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = purchase_order_trans.item_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = purchase_order_master.party_id';
		if(!empty($data['item_type'])){$queryData['where']['item_master.item_type'] = $data['item_type'];}
		if(!empty($data['item_name'])){$queryData['where']['item_master.item_name'] = $data['item_name'];}
        $queryData['customWhere'][] = "purchase_order_master.po_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		if(!empty($data['short_by']) && $data['short_by'] == 1) {$queryData['order_by']['purchase_order_master.po_date'] = 'DESC';}
		if(!empty($data['short_by']) && $data['short_by'] == 2) {$queryData['order_by']['purchase_order_trans.price'] = 'ASC';}	
		return $this->rows($queryData);
    }
}
?>