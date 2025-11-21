<?php 
class SalesReportModel extends MasterModel
{
    private $stockTrans = "stock_transaction";
    private $transMain = "trans_main";
    private $transChild = "trans_child";

    /* Customer's Order Monitoring */
    public function getOrderMonitor($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date,trans_main.remark,trans_main.ref_by,trans_main.order_type,trans_main.sales_type,trans_main.delivery_date,party_master.party_code,employee_master.emp_name';
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = trans_child.created_by";
		$queryData['where']['trans_main.entry_type'] = 4;
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';

		$result = $this->rows($queryData);
		return $result;
    }

    public function getInvoiceData($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = 'trans_main.id,trans_main.trans_date,trans_main.trans_no,trans_main.trans_prefix,trans_main.delivery_date';
        $data['where']['trans_main.ref_id'] = $data['trans_main_id'];
        $data['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";

        return $this->rows($data);
    }

    public function getDeliveredQty($item_id,$trans_main_id)
    {
        $data['tableName'] = $this->transChild;
        $data['select'] = 'SUM(trans_child.qty) as dqty';
        $data['where']['trans_child.item_id'] = $item_id;
        $data['where']['trans_child.trans_main_id'] = $trans_main_id;
        return $this->row($data);
    }
}
?>