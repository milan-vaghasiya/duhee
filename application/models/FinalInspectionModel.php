<?php
class FinalInspectionModel extends MasterModel{
    private $firMaster = "fir_master";
    private $firTrans = "fir_transaction";
    private $inspectionParam = "inspection_param";
    private $stockTransaction = "stock_transaction"; 

    public function getNetxFirNo(){
        $data['select'] = "MAX(fir_no) as fir_no";
        $data['tableName'] = $this->firMaster;
		$trans_no = $this->specificRow($data)->fir_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo;
    }

    public function getFirPrefix(){
        $prefix = 'FIR/';
        return $prefix.$this->shortYear.'/';
    }

    public function getInspectionParam($item_id){
        $queryData['tableName'] = $this->inspectionParam;
        $queryData['where']['param_type'] = 2;
        $queryData['where']['item_id'] = $item_id;
        $result = $this->rows($queryData);
        return $result;
    }

    public function getDTRows($data){
        $data['tableName'] = $this->firMaster;
        $data['select'] = "fir_master.*,item_master.item_name as product_name,item_master.item_code as product_code,(fir_master.in_qty - fir_master.inspected_qty) as pending_qty,job_card.job_prefix,job_card.job_no";        
        $data['leftJoin']['job_card'] = "job_card.id = fir_master.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = fir_master.product_id";

        if(!empty($data['job_id']))
            $data['where']['fir_master.job_card_id'] = $data['job_id'];

        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "fir_master.in_qty";
        $data['searchCol'][] = "fir_master.inspected_qty";
        $data['searchCol'][] = "(fir_master.in_qty - fir_master.inspected_qty)";

        return $this->pagingRows($data);
    }

    public function getInspectionTrans($id){
        $queryData['tableName'] = $this->firTrans;
        $queryData['select'] = "fir_transaction.*,inspection_param.parameter,employee_master.emp_name as inspector_name";
        $queryData['leftJoin']['inspection_param'] = "inspection_param.id = fir_transaction.parameter_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = fir_transaction.inspector_id";
        $queryData['where']['fir_transaction.fir_id'] = $id;
        $transData = $this->rows($queryData);
        return $transData;
    }

    public function getInspectionData($id){
        $queryData = array();
        $queryData['tableName'] = $this->firMaster;
        $queryData['where']['id'] = $id;
        $result = $this->row($queryData); 
        return $result;
    }

    public function getStockTrans($id){
        $queryData = array();
        $queryData['tableName'] = $this->stockTransaction;
        $queryData['where']['id'] = $id;
        $result = $this->row($queryData); 
        return $result;
    }

    public function acceptInspection($data){
        $this->store($this->firMaster,$data);
        return ['status'=>1,'message'=>'Inspection accepted successfully.'];
    }

    public function save($data){
        $inspectionData = $this->getInspectionData($data['id']);
        $stockTransData = $this->getStockTrans($inspectionData->ref_id);
        $this->remove($this->stockTransaction,['ref_type'=>14,'ref_id'=>$data['id']]);
        $this->trash($this->firTrans,['fir_id'=>$data['id']]);

        $okQty=0;$udQty=0;$rewQty=0;$mcrQty=0;$rmrQty=0;
        foreach($data['parameter_id'] as $key=>$value):
            $transData = [
                'id' => $data['trans_id'][$key],
                'fir_id' => $data['id'],
                'parameter_id' => $value,
                'min_qty' => $data['min_qty'][$key],
                'max_qty' => $data['max_qty'][$key],
                'ok_qty' => $data['ok_qty'][$key],
                'ud_qty' => $data['ud_qty'][$key],
                'rework_qty' => $data['rework_qty'][$key],
                'mcr_qty' => $data['mcr_qty'][$key],
                'rmr_qty' => $data['rmr_qty'][$key],
                'inspector_id' => $data['inspector_id'][$key],
                'created_by' => $data['created_by'],
                'is_delete' => 0
            ];
            $this->store($this->firTrans,$transData);
        endforeach;

        $okQty = array_sum($data['ok_qty']);
        $udQty = array_sum($data['ud_qty']);
        $rewQty = array_sum($data['rework_qty']);
        $mcrQty = array_sum($data['mcr_qty']);
        $rmrQty = array_sum($data['rmr_qty']);
        $inspectedQty = $okQty + $udQty;
        $totalQty = $okQty + $udQty + $rewQty + $mcrQty + $rmrQty;

        $stockTransDeduct = [
            'id' => "",
            'location_id' => $stockTransData->location_id, 
            'batch_no' => $stockTransData->batch_no,
            'trans_type' => 2,
            'item_id' => $data['product_id'],
            'qty' => "-".$totalQty,
            'ref_type' => 7,
            'ref_id' => $data['id'],
            'ref_no' => "",
            'ref_date' => date("Y-m-d"),
            'created_by' => $data['created_by']
        ];
        $this->store($this->stockTransaction,$stockTransDeduct);

        $stockTrans = [
            'id' => "",
            'location_id' => 4,   
            'batch_no' => $stockTransData->batch_no,        
            'trans_type' => 1,
            'item_id' => $data['product_id'],
            'qty' => $inspectedQty,
            'ref_type' => 7,
            'ref_id' => $data['id'],
            'ref_no' => "",
            'ref_date' => date("Y-m-d"),
            'created_by' => $data['created_by']
        ];
        $this->store($this->stockTransaction,$stockTrans);     

        $masterData = [
            'id' => $data['id'],
            'inspected_qty' => $totalQty,
            'ok_qty' => $okQty,
            'ud_qty' => $udQty,
            'rework_qty' => $rewQty,
            'mcr_qty' => $mcrQty,
            'rmr_qty' => $rmrQty,
            'remark' => $data['remark'],
            'status' => ($inspectionData->in_qty == $totalQty)?2:1,
            'created_by' => $data['created_by']
        ];
        return $this->store($this->firMaster,$masterData,'Final Inspection');
    }
}
?>