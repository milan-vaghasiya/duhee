<?php
class JobMaterialDispatchModel extends MasterModel{
    private $jobCard = "job_card";
    private $jobMaterialDispatch = "job_material_dispatch";
    private $jobUsedMaterial = "job_used_material";
    private $purchaseTrans = "purchase_invoice_transaction";
    private $itemMaster = "item_master";
    private $jobBom = "job_bom";
    private $requisitionLog = "requisition_log";
    private $stockTrans = "stock_transaction";
    private $job_heat_trans = "job_heat_trans";
    private $jobApproval = "job_approval";

    public function getDTRows($data){
        if($data['status'] == 2):
            $data['tableName'] = $this->stockTrans;
            $data['select'] = "stock_transaction.id, stock_transaction.trans_ref_id, stock_transaction.ref_date,stock_transaction.location_id, stock_transaction.batch_no, stock_transaction.qty, stock_transaction.item_id, item_master.full_name, location_master.store_name, location_master.location,st.stock_qty as pending_stock,job_card.job_no,job_card.job_prefix,job_card.job_number,job_card.product_id,unit_master.unit_name";

            $data['leftJoin']['item_master'] =  "stock_transaction.item_id = item_master.id";
            $data['leftJoin']['location_master'] =  "stock_transaction.location_id = location_master.id";
            $data['leftJoin']['(SELECT SUM(qty) as stock_qty,ref_id,item_id,location_id,batch_no  FROM stock_transaction WHERE ref_type = 20 AND is_delete = 0 GROUP BY ref_id,item_id,location_id,batch_no) as st'] = "st.ref_id = stock_transaction.ref_id AND st.item_id = stock_transaction.item_id AND st.location_id = stock_transaction.location_id AND st.batch_no = stock_transaction.batch_no";
            $data['leftJoin']['job_card'] = "job_card.id = stock_transaction.ref_id";
            $data['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";

            $data['where']['stock_transaction.trans_type'] = 1;
            $data['where']['stock_transaction.ref_type'] = 20;
            $data['where']['stock_transaction.location_id !='] = $this->ALLOT_RM_STORE->id;

            $data['searchCol'][] = "job_card.job_number";
            $data['searchCol'][] = "DATE_FORMAT(stock_transaction.ref_date,'%d-%m-%Y')";
            $data['searchCol'][] = "item_master.full_name";
            $data['searchCol'][] = "st.stock_qty";

            $columns =array('','','job_card.job_no','stock_transaction.ref_date','item_master.full_name','st.stock_qty','','','','','');
		    if(isset($data['order'])):
                $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
            endif;            
        else:
            $data['tableName'] = $this->requisitionLog;
            $data['select'] = "requisition_log.*,item_master.item_name,item_master.full_name,item_master.item_code,item_master.part_no,item_master.full_name,item_master.make_brand,item_master.unit_id,unit_master.unit_name, (CASE WHEN (requisition_log.urgency = 2) THEN 'High' ELSE (CASE WHEN (requisition_log.urgency = 1) THEN 'Medium' ELSE 'Low' END) END) as priority,job_card.job_no,job_card.job_prefix,job_card.job_number,job_card.product_id";
            $data['leftJoin']['item_master'] = "item_master.id = requisition_log.req_item_id";
            $data['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
            $data['leftJoin']['job_card'] = "job_card.id = requisition_log.req_from";
            $data['where']['requisition_log.log_type'] = 1;
            $data['where']['requisition_log.reqn_type'] = 3;
            $data['where']['requisition_log.order_status'] = $data['status'];	

            $data['searchCol'][] = "job_card.job_number";
            $data['searchCol'][] = "DATE_FORMAT(requisition_log.req_date,'%d-%m-%Y')";
            $data['searchCol'][] = "item_master.full_name";
            $data['searchCol'][] = "requisition_log.req_qty";

            $columns =array('','','job_card.job_no','requisition_log.req_date','item_master.full_name','','requisition_log.req_qty','','','','');
		    if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        endif;

        
        return $this->pagingRows($data);
    }

    public function save($data){
        try {
            $this->db->trans_begin();

            if(empty($data['id'])){
                $data['log_no'] = $this->issueRequisition->nextIssueNo(2);
            }
            $saveIssueData = $this->store($this->requisitionLog, $data);
            $issueId = (!empty($data['id']) ? $data['id'] : $saveIssueData['insert_id']);

            $reqData = $this->purchaseRequest->getPurchaseRequest($data['ref_id']);

            $stockQueryData['id'] = "";
            $stockQueryData['location_id'] = $this->ALLOT_RM_STORE->id;
            $stockQueryData['batch_no'] = $reqData->batch_no;
            $stockQueryData['trans_type'] = 2;
            $stockQueryData['item_id'] = $data['req_item_id'];
            $stockQueryData['qty'] = ($data['req_qty'] * -1);
            $stockQueryData['ref_type'] = 20;
            $stockQueryData['ref_id'] = $data['req_from'];
            $stockQueryData['trans_ref_id'] = $issueId;
            $stockQueryData['ref_no'] = $data['ref_id'];
            $stockQueryData['ref_date'] =  $data['issue_date'];
            $stockQueryData['created_by'] = $data['created_by'];
            $stockQueryData['stock_type'] = "FRESH";        
            $stockQueryData['stock_effect'] = 0;        
            $stockResult = $this->store('stock_transaction', $stockQueryData);


            $bookItemQuery = [
                'id' => '',
                'location_id'=>$this->PRODUCTION_STORE->id,
                'batch_no' => $reqData->batch_no,
                'trans_type' => 1,
                'item_id' => $data['req_item_id'],
                'qty' => $data['req_qty'],
                'ref_type' => 21,
                'ref_id' => $data['req_from'],
                'trans_ref_id' => $issueId,
                'ref_no' => $stockResult['insert_id'],
                'ref_date' => $data['issue_date'],
                'stock_type' => "FRESH",
                'created_by' => $data['created_by'],
                'stock_effect'=>0
            ];

            $issueTrans = $this->store('stock_transaction', $bookItemQuery);

           
            $issueTransData = $this->issueRequisition->getIssueMaterialData($data['ref_id']);

            if ($issueTransData->req_qty >= $reqData->req_qty) {
                $this->edit($this->requisitionLog, ['id' => $data['ref_id']], ['order_status' => 1]);
            }

            $queryData = array();
            $queryData['tableName'] = $this->jobBom;
            $queryData['select'] = 'job_bom.*,item_master.item_type';
            $queryData['leftJoin']['item_master'] =  "item_master.id = job_bom.ref_item_id";
            $queryData['where']['job_bom.id'] = $reqData->ref_id;
            $bomData = $this->row($queryData);
            if(!empty($bomData)):
                $dispatch_id = (!empty($bomData->dispatch_id))?explode(",",$bomData->dispatch_id):array();
                $dispatch_id[] = $issueTrans['insert_id'];
                $dispatch_qty = $bomData->dispatch_qty + $data['req_qty'];
                $this->store($this->jobBom,['id'=>$reqData->ref_id,'dispatch_id'=>implode(",",$dispatch_id),'dispatch_qty'=>$dispatch_qty]);
            endif;

            $this->edit($this->jobCard,['id'=>$data['req_from']],['md_status'=>2]);

            if($bomData->dispatch_qty > 0){
                $heatQuery['tableName'] = $this->job_heat_trans;
                $heatQuery['select'] = "job_heat_trans.id";
                $heatQuery['where']['job_heat_trans.job_card_id'] = $data['req_from'];
                $heatQuery['where']['job_heat_trans.batch_no'] = $reqData->batch_no;
                $heatQuery['where']['job_heat_trans.process_id'] = 0;
                $heatData = $this->row($heatQuery);
                if($bomData->item_type == 3){
                    $setData = array();
                    $setData['tableName'] = $this->job_heat_trans;
                    $setData['where']['id'] = $heatData->id;
                    $setData['set']['in_qty'] = 'in_qty, + ' . $data['req_qty'];
                    $this->setValue($setData);
                }else{
                    if(!empty($heatData)){
                        $setData = array();
                        $setData['tableName'] = $this->job_heat_trans;
                        $setData['where']['id'] = $heatData->id;
                        $setData['set']['in_qty'] = 'in_qty, + ' . $data['req_qty'];
                        $this->setValue($setData);
                    }else{
                        $aprovalQuery['tableName'] = $this->jobApproval;
                        $aprovalQuery['select'] = "job_approval.id";
                        $aprovalQuery['where']['in_process_id'] = 0;
                        $aprovalQuery['where']['job_card_id'] = $data['req_from'];
                        $approvalData = $this->row($aprovalQuery);
                        $heatArray=[
                            'id'=>'',
                            'job_card_id' =>$data['req_from'],
                            'job_approval_id'=> $approvalData->id,
                            'process_id'=>0,
                            'in_qty'=>$data['req_qty'],
                            'ok_qty'=>$data['req_qty'],
                            'batch_no'=>$reqData->batch_no,
                        ];
                        $this->store($this->job_heat_trans,$heatArray);
                    }
                }
            }
         
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Material Issue suucessfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function editAllocatedMaterial($id){
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "stock_transaction.id, stock_transaction.trans_ref_id, stock_transaction.ref_batch, stock_transaction.location_id, stock_transaction.batch_no, stock_transaction.qty, stock_transaction.item_id, stock_transaction.ref_no,item_master.full_name as item_full_name, location_master.store_name, location_master.location,st.stock_qty as pending_qty";
        $queryData['leftJoin']['item_master'] =  "stock_transaction.item_id = item_master.id";
        $queryData['leftJoin']['location_master'] =  "stock_transaction.location_id = location_master.id";
        $queryData['leftJoin']['(SELECT SUM(qty) as stock_qty,ref_id,item_id,location_id,batch_no  FROM stock_transaction WHERE ref_type = 20 AND is_delete = 0 GROUP BY ref_id,item_id,location_id,batch_no) as st'] = "st.ref_id = stock_transaction.ref_id AND st.item_id = stock_transaction.item_id AND st.location_id = stock_transaction.location_id AND st.batch_no = stock_transaction.batch_no";
        $queryData['where']['stock_transaction.id'] = $id;
        $result = $this->row($queryData);
        return $result;
    }

    public function updateAllocatedQty($data){
        try {
            $this->db->trans_begin();

            $transRow = $this->store->getStockTransRow($data['id']);
            $qty = 0;
            $qty = $transRow->qty + ($data['qty'] * $data['ref_type']);
            $this->edit($this->stockTrans,['id'=>$data['id']],['qty'=>$qty]);

            $transRefRow = $this->store->getStockTransRow($data['ref_no']);
            $qty = 0;
            $qty = ((abs($transRefRow->qty) + ($data['qty'] * $data['ref_type'])) * -1);
            $this->edit($this->stockTrans,['id'=>$data['ref_no']],['qty'=>$qty]);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Qty updated suucessfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function save1($data){
        try {
            $this->db->trans_begin();

            $jobData=$this->jobcard->getJobcard($data['req_from']);
            $batch_qty = array();$batch_no = array();$location_id = array();
            $batch_qty = explode(",", $data['batch_qty']);
            $batch_no = explode(",", $data['batch_no']);
            $ref_batch = $data['ref_batch'];
            $stockType = explode(",", $data['stock_type']);
            $location_id = explode(",", $data['location_id']);
            unset($data['batch_qty'], $data['batch_no'], $data['location_id'], $data['stock_type'],$data['ref_batch']);

            if(empty($data['id'])){
                $data['log_no'] = $this->issueRequisition->nextIssueNo(2);
            }
            $data['log_type'] = 2;
            $data['order_status'] = 2;
            $data['req_qty'] = array_sum($batch_qty);
            $saveIssueData = $this->store($this->requisitionLog, $data);
            $issueId = (!empty($data['id']) ? $data['id'] : $saveIssueData['insert_id']);

            foreach ($batch_qty as $bk => $bv) :
                if($bv > 0):
                    $stockQueryData['id'] = "";
                    $stockQueryData['location_id'] = $location_id[$bk];
                    if (!empty($batch_no[$bk])) {
                        $stockQueryData['batch_no'] = $batch_no[$bk];
                    }
                    $stockQueryData['trans_type'] = 2;
                    $stockQueryData['item_id'] = $data['req_item_id'];
                    $stockQueryData['qty'] = ($bv * -1);
                    $stockQueryData['ref_type'] = 20;
                    $stockQueryData['ref_id'] = $data['req_from'];
                    $stockQueryData['trans_ref_id'] = $issueId;
                    $stockQueryData['ref_no'] = $data['ref_id'];
                    $stockQueryData['ref_batch'] = $ref_batch[$bk];
                    $stockQueryData['ref_date'] =  $data['req_date'];
                    $stockQueryData['created_by'] = $data['created_by'];
                    $stockQueryData['stock_type'] = $stockType[$bk];        
                    $stockQueryData['stock_effect'] = 0;        
                    $stockResult = $this->store('stock_transaction', $stockQueryData);
        
        
                    $bookItemQuery = [
                        'id' => '',
                        'location_id'=>$this->ALLOT_RM_STORE->id,
                        'batch_no' => $batch_no[$bk],
                        'trans_type' => 1,
                        'item_id' => $data['req_item_id'],
                        'qty' => $bv,
                        'ref_type' => 20,
                        'ref_id' => $data['req_from'],
                        'trans_ref_id' => $issueId,
                        'ref_no' => $stockResult['insert_id'],
                        'ref_batch' => $ref_batch[$bk],
                        'ref_date' => $data['req_date'],
                        'stock_type' => $stockType[$bk],
                        'created_by' => $data['created_by'],
                        'stock_effect'=>0
                    ];
        
                    $this->store('stock_transaction', $bookItemQuery);
                endif;
            endforeach;

            $reqData = $this->purchaseRequest->getPurchaseRequest($data['ref_id']);
            $issueTransData = $this->issueRequisition->getIssueMaterialData($data['ref_id']);

            if ($issueTransData->req_qty >= $reqData->req_qty) {
                $this->edit($this->requisitionLog, ['id' => $data['ref_id']], ['order_status' => 1]);
            }
            $kitData = $this->jobcard->getJobBomData($data['req_from'], $jobData->product_id);
            $ref_id= implode(",",array_column($kitData,'dispatch_id'));

            $queryData['tableName'] = $this->requisitionLog;
            $queryData['select'] = "requisition_log.*,item_master.full_name";
            $queryData['leftJoin']['item_master'] = 'item_master.id=requisition_log.req_item_id';
            $queryData['where']['log_type'] = 2;
            $queryData['where']['reqn_type'] = 3;
            $queryData['where']['req_from'] = $data['req_from'];
            $queryData['customWhere'][]="requisition_log.ref_id NOT IN(".$ref_id.")";
            $issueReqData= $this->rows($queryData);
            
            if(empty($issueReqData)){
                $this->edit($this->jobCard,['id'=>$data['req_from']],['md_status'=>2]);  
            }

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Material Issue suucessfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function save_old($data){
        unset($data['issue_type']);
        unset($data['req_emp_id']);

        $jobData=$this->jobcard->getJobcard($data['req_from']);
        $batch_qty = array();
        $batch_no = array();
        $location_id = array();
        $batchQty = explode(",", $data['batch_qty']);
        $batchNo = explode(",", $data['batch_no']);
        $stockType = explode(",", $data['stock_type']);
        $locationId = explode(",", $data['location_id']);
        unset($data['batch_qty'], $data['batch_no'], $data['location_id'], $data['stock_type']);
        if (!empty($data['id'])) {
            $this->remove('stock_transaction', ['ref_id' => $data['id'], 'ref_type' => 16]);
            $issueTransData = $this->issueRequisition->getIssueMaterialData($data['id']);

            if (!empty($issueTransData->dispatch_qty) and $issueTransData->dispatch_qty > 0) :
                $setData = array();
                $setData['tableName'] = $this->itemMaster;
                $setData['where']['id'] = $issueTransData->req_item_id;
                $setData['set']['qty'] = 'qty, + ' . $issueTransData->dispatch_qty;
                $qryresult = $this->setValue($setData);
            endif;
        }
        if (empty($data['id'])) {
            $data['log_no'] = $this->issueRequisition->nextIssueNo(2);
        }
        $data['log_type'] = 2;
        $data['order_status'] = 2;
        $data['req_qty'] = array_sum($batchQty);
        $saveIssueData = $this->store($this->requisitionLog, $data);
        $issueId = (!empty($data['id']) ? $data['id'] : $saveIssueData['insert_id']);


        foreach ($batchNo as $ak => $av) :
            if (!empty($batchQty[$ak])) :
                $batch_qty[] = $batchQty[$ak];
                $batch_no[] = $av;
                $location_id[] = $locationId[$ak];
            endif;
        endforeach;
        foreach ($batch_qty as $bk => $bv) :

            $stockQueryData['id'] = "";
            $stockQueryData['location_id'] = $location_id[$bk];
            if (!empty($batch_no[$bk])) {
                $stockQueryData['batch_no'] = $batch_no[$bk];
            }
            $stockQueryData['trans_type'] = 2;
            $stockQueryData['item_id'] = $data['req_item_id'];
            $stockQueryData['qty'] = '-' . $bv;
            $stockQueryData['ref_type'] = 3;
            $stockQueryData['ref_id'] = $data['req_from'];
            $stockQueryData['trans_ref_id'] = $issueId;
            $stockQueryData['ref_no'] = $data['ref_id'];
            $stockQueryData['ref_date'] =  $data['req_date'];
            $stockQueryData['created_by'] = $data['created_by'];
            $stockQueryData['stock_type'] = $stockType[$bk];

            $stockResult = $this->store('stock_transaction', $stockQueryData);


            $bookItemQuery = [
                'id' => '',
                'location_id'=>$this->ALLOT_RM_STORE->id,
                'batch_no' => $jobData->job_number,
                'trans_type' => 1,
                'item_id' => $data['req_item_id'],
                'qty' => $bv,
                'ref_type' => 20,
                'ref_id' => $data['req_from'],
                'trans_ref_id' => $issueId,
                'ref_no' => $stockResult['insert_id'],
                'ref_batch' => (!empty($batch_no[$bk]) ? $batch_no[$bk] : ''),
                'ref_date' => $data['req_date'],
                'stock_type' => $stockType[$bk],
                'created_by' => $data['created_by'],
                'stock_effect'=>0
            ];

            $this->store('stock_transaction', $bookItemQuery);
        endforeach;


        $reqData = $this->purchaseRequest->getPurchaseRequest($data['ref_id']);
        $issueTransData = $this->issueRequisition->getIssueMaterialData($data['ref_id']);

        if ($issueTransData->req_qty >= $reqData->req_qty) {
            $this->edit($this->requisitionLog, ['id' => $data['ref_id']], ['order_status' => 1]);
        }
        $kitData = $this->jobcard->getJobBomData($data['req_from'], $jobData->product_id);
        $ref_id= implode(",",array_column($kitData,'dispatch_id'));

        $queryData['tableName'] = $this->requisitionLog;
        $queryData['select'] = "requisition_log.*,item_master.full_name";
        $queryData['leftJoin']['item_master'] = 'item_master.id=requisition_log.req_item_id';
        $queryData['where']['log_type'] = 2;
        $queryData['where']['reqn_type'] = 3;
        $queryData['where']['req_from'] = $data['req_from'];
        $queryData['customWhere'][]="requisition_log.ref_id NOT IN(".$ref_id.")";
        $issueReqData= $this->rows($queryData);
        
        if(empty($issueReqData)){
            $this->edit($this->jobCard,['id'=>$data['req_from']],['md_status'=>2]);  
        }
        return ['status' => 1, 'message' => 'Material Issue suucessfully.'];
    }
}
?>