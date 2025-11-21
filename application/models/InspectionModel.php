<?php
class InspectionModel extends MasterModel
{
    private $requisitionLog = "requisition_log";
    private $material_issue = "material_issue";
    private $itemMaster = "item_master";
    private $materialReturn = "material_return";
    private $stockTransaction = "stock_transaction";
    private $transMain = "trans_main";
    private $transChild = "trans_child";

    public function getDTRows($data)
    { 
        $data['tableName'] = $this->materialReturn;
        $data['select'] = 'material_return.*,item_master.full_name,requisition_log.log_no';//, SUM(material_return.qty) as return_qty, SUM(CASE WHEN material_return.return_status = 1 THEN material_return.qty ELSE 0 END ) as used_qty ,SUM(CASE WHEN material_return.return_status = 2 THEN material_return.qty ELSE 0 END ) as fresh_qty ,SUM(CASE WHEN material_return.return_status = 3 THEN material_return.qty ELSE 0 END ) as miss_qty, SUM(CASE WHEN material_return.return_status = 4 THEN material_return.qty ELSE 0 END ) as broken_qty';
        $data['leftJoin']['requisition_log'] = 'requisition_log.id=material_return.ref_id';
        $data['leftJoin']['item_master'] = 'item_master.id=requisition_log.req_item_id';
        if(empty($data['status'])){
            $data['where']['material_return.trans_type'] = $data['status'];
        }elseif($data['status'] == 1){
            $data['where']['material_return.trans_type'] = $data['status'];
            $data['customWhere'][]="material_return.qty > material_return.regranding_qty";
        }elseif($data['status'] == 2){
            $data['where']['material_return.trans_type'] = $data['status'];
            $data['customWhere'][]="material_return.qty > material_return.regranding_qty";

        }
        $data['order_by']['requisition_log.log_no'] = 'ASC';

        $data['searchCol'][] = "requisition_log.log_no";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "requisition_log.req_qty";

        $columns = array('', '', 'material_return.trans_date', 'item_master.item_name', 'material_return.req_qty', '', '');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }

    public function getInspectionData($ref_id)
    {
        $data['tableName'] = $this->materialReturn;
        $data['select'] = 'SUM(qty) as qty';
        $data['where']['material_return.trans_type'] = 1;
        $data['where']['material_return.ref_id'] = $ref_id;
        return $this->row($data);
    }

    public function getInspectionDataById($id)
    {
        $data['tableName'] = $this->materialReturn; 
        $data['select'] = 'material_return.*, requisition_log.req_item_id, item_master.full_name as convert_item_name';
        $data['leftJoin']['requisition_log'] = 'requisition_log.id = material_return.ref_id';
        $data['leftJoin']['item_master'] = 'item_master.id = material_return.convert_item_id';
        $data['where']['material_return.trans_type'] = 1;
        $data['where']['material_return.id'] = $id;
        return $this->row($data);
    }

    public function getReturnMatrialData($issueNo)
    {
        $data['tableName'] = $this->materialReturn;
        $data['select'] = 'SUM(CASE WHEN return_status =1 THEN qty ELSE 0 END ) as used_qty ,SUM(CASE WHEN return_status =2 THEN qty ELSE 0 END ) as fresh_qty ,SUM(CASE WHEN return_status =3 THEN qty ELSE 0 END ) as miss_qty,SUM(CASE WHEN return_status =4 THEN qty ELSE 0 END ) as broken_qty';
        $data['where']['material_return.trans_type'] = 0;
        $data['where']['material_return.ref_id'] = $issueNo;
        return $this->row($data);
    }

    public function save($data)
    {
        $qty = 0;
        foreach ($data['qty'] as $key => $value) {
            $materialInspQuery = [
                'id' => '',
                'ref_id' => $data['ref_id'],
                'trans_date' => $data['trans_date'],
                'trans_type' => $data['trans_type'],
                'qty' => $value,
                'inspection_status' => $data['inspection_status'][$key],
                'reason' => $data['reason'][$key],
                'created_by' => $data['created_by']
            ];
            $inspResult = $this->store($this->materialReturn, $materialInspQuery);

            if ($data['inspection_status'][$key] != 6 &&  $data['inspection_status'][$key] != 7) {
                /** Stock added in given location */
                $stockPlusQuery = [
                    'id' => '',
                    'item_id' => $data['item_id'][$key],
                    'ref_id' => $data['ref_id'],
                    'ref_type' => 23,
                    'location_id' => $data['location_id'][$key],
                    'batch_no' => (!empty($data['batch_no'][$key])?$data['batch_no'][$key]:'General Batch'),
                    'trans_type' => 1,
                    'qty' => $value,
                    'stock_type' => $data['stock_type'][$key],
                    'trans_ref_id' => $inspResult['insert_id'],
                ];

                $this->store($this->stockTransaction, $stockPlusQuery);
                $qty += $value;
            }
            if ($data['inspection_status'][$key] == 7) {
                $qty += $value;

                $issueLog = $this->issueRequisition->getIssueMaterialLog($data['ref_id']);
                $reqData = $this->issueRequisition->getIssueMaterialLog($issueLog->ref_id);
                $empData = $this->employee->getEmp($reqData->created_by);

                $strQuery['tableName'] = "location_master";
                $strQuery['where']['other_ref'] = $empData->emp_dept_id;
                $strQuery['where']['store_type'] = 2;
                $strResult = $this->row($strQuery);
                /** Stock Remove from Inspection Store And Add in returned store */

                $stockQuery = [
                    'id' => '',
                    'item_id' => $data['req_item_id'],
                    'ref_id' => $data['ref_id'],
                    'ref_type' => 23,
                    'location_id' => $strResult->id,
                    'trans_type' => 1,
                    'stock_effect' => 0,
                    'qty' => $value,
                    'trans_ref_id' => $inspResult['insert_id'],
                ];
                $this->store($this->stockTransaction, $stockQuery);
            }
        }

        /** Stock Remove from Inspection Store */

        $stockMinusQuery = [
            'id' => '',
            'item_id' => $data['req_item_id'],
            'ref_id' => $data['ref_id'],
            'ref_type' => 23,
            'location_id' => $this->INSP_STORE->id,
            'trans_type' => 2,
            'stock_effect' => 0,
            'qty' => '-' . $qty,
            'stock_effect' => 0,
        ];

        $result = $this->store($this->stockTransaction, $stockMinusQuery);
        return $result;
    }

    public function getInspectionDetail($transNo)
    {
        $data['tableName'] = $this->materialReturn;
        $data['select'] = 'material_return.*,requisition_log.req_item_id';
        $data['leftJoin']['requisition_log'] = "requisition_log.id=material_return.ref_id";
        $data['where']['material_return.trans_type'] = 1;
        $data['where']['material_return.id'] = $transNo;

        return $this->row($data);
    }

    public function delete($id)
    {
        $inspData = $this->getInspectionDetail($id);

        $this->remove($this->stockTransaction, ['trans_ref_id' => $id, 'ref_id' => $inspData->ref_id, 'ref_type' => 23]);

        if ($inspData->return_status != 7) {
            $stockMinusQuery = [
                'id' => '',
                'item_id' => $inspData->req_item_id,
                'ref_id' => $inspData->ref_id,
                'ref_type' => 23,
                'location_id' => $this->INSP_STORE->id,
                'trans_type' => 1,
                'qty' => $inspData->qty,
            ];

            $result = $this->store($this->stockTransaction, $stockMinusQuery);
        }
        return $this->trash($this->materialReturn, ['id' => $id]);
    }

    public function getInspectionTrans($ref_id)
    {
        $data['tableName'] = $this->materialReturn;
        $data['select'] = 'material_return.*';
        $data['where']['material_return.trans_type'] = 1;
        $data['where']['material_return.ref_id'] = $ref_id;

        return $this->rows($data);
    }
    
    public function saveInspection($data){
        $inspData = [
            'id' => $data['id'],
            'used_qty' => $data['used_qty'],
            'fresh_qty' => $data['fresh_qty'],
            'scrap_qty' => $data['scrap_qty'],
            'regranding_qty' => $data['regranding_qty'],
            'regrinding_reason' => $data['regrinding_reason'],
            'convert_qty' => $data['convert_qty'],
            'convert_item_id' => $data['convert_item_id'],
            'broken_qty' => $data['broken_qty'],
            'missed_qty' =>  $data['miss_qty'],
            'trans_type' =>  1
        ];
        return $this->store($this->materialReturn, $inspData);
    }

    public function saveInspLocation($data){
         /** Stock Remove from Inspection Store */
         $stockMinusQuery = [
            'id' => '',
            'item_id' => $data['item_id'],
            'ref_id' => $data['id'],
            'ref_type' => 23,
            'location_id' => $this->INSP_STORE->id,
            'batch_no'=>$data['batch_no'],
            'size'=>$data['size'],
            'trans_type' => 2,
            'stock_effect' => 0,
            'qty' => '-' . $data['qty'],
            'stock_effect' => 0,
        ];
        $this->store($this->stockTransaction, $stockMinusQuery);

        if(!empty($data['used_qty']) && $data['used_qty'] > 0){
            $stockPlusQuery = array();
            $stockPlusQuery = [
                'id' => '',
                'item_id' => $data['item_id'],
                'ref_id' => $data['id'],
                'ref_type' => 23,
                'trans_type' => 1,
                'location_id' => $data['location_used'],
                'batch_no' => (!empty($data['batch_used'])?$data['batch_used']:'General Batch'),
                'size'=>$data['size'],
                'qty' => $data['used_qty'],
                'stock_type' => 'USED'
            ];
            $this->store($this->stockTransaction, $stockPlusQuery);
        } if(!empty($data['fresh_qty']) && $data['fresh_qty'] > 0){
            $stockPlusQuery = array();
            $stockPlusQuery = [
                'id' => '',
                'item_id' => $data['item_id'],
                'ref_id' => $data['id'],
                'ref_type' => 23,
                'trans_type' => 1,
                'location_id' => $data['location_fresh'],
                'batch_no' => (!empty($data['batch_fresh'])?$data['batch_fresh']:'General Batch'),
                'size'=>$data['size'],
                'qty' => $data['fresh_qty'],
                'stock_type' => 'FRESH'
            ];
            $this->store($this->stockTransaction, $stockPlusQuery);
        } if(!empty($data['scrap_qty']) && $data['scrap_qty'] > 0){
            $stockPlusQuery = array();
            $stockPlusQuery = [
                'id' => '',
                'item_id' => $data['item_id'],
                'ref_id' => $data['id'],
                'ref_type' => 23,
                'trans_type' => 1,
                'location_id' => $this->SCRAP_STORE->id,
                'batch_no' => (!empty($data['batch_scrap'])?$data['batch_scrap']:'General Batch'),
                'size'=>$data['size'],
                'qty' => $data['scrap_qty'],
                'stock_type' => 'USED'
            ];
            $this->store($this->stockTransaction, $stockPlusQuery);
        } if(!empty($data['regranding_qty']) && $data['regranding_qty'] > 0){
            $stockPlusQuery = array();
            $stockPlusQuery = [
                'id' => '',
                'item_id' => $data['item_id'],
                'ref_id' => $data['id'],
                'ref_type' => 23,
                'trans_type' => 1,
                'location_id' => $this->REGRIND_STORE->id,
                'batch_no' => (!empty($data['batch_regranding'])?$data['batch_regranding']:'General Batch'),
                'size'=>$data['size'],
                'qty' => $data['regranding_qty'],
                'stock_type' => 'USED'
            ];
            $this->store($this->stockTransaction, $stockPlusQuery);
        } if(!empty($data['broken_qty']) && $data['broken_qty'] > 0){
            $stockPlusQuery = array();
            $stockPlusQuery = [
                'id' => '',
                'item_id' => $data['item_id'],
                'ref_id' => $data['id'],
                'ref_type' => 23,
                'trans_type' => 1,
                'location_id' => $data['location_broken'],
                'batch_no' => (!empty($data['batch_broken'])?$data['batch_broken']:'General Batch'),
                'size'=>$data['size'],
                'qty' => $data['broken_qty'],
                'stock_type' => 'USED'
            ];
            $this->store($this->stockTransaction, $stockPlusQuery);
        } if(!empty($data['accepted_qty']) && $data['accepted_qty'] > 0){
            $stockPlusQuery = array();
            $stockPlusQuery = [
                'id' => '',
                'item_id' => $data['item_id'],
                'ref_id' => $data['id'],
                'ref_type' => 23,
                'trans_type' => 1,
                'location_id' => $this->MISPLACED_STORE->id,
                'batch_no' => $data['batch_no'],
                'qty' => $data['accepted_qty'],
                'size'=>$data['size'],
                'stock_type' => 'USED',
                'stock_effect'=>0
            ];
            $this->store($this->stockTransaction, $stockPlusQuery);
        } if(!empty($data['convert_qty']) && $data['convert_qty'] > 0){
            $stockPlusQuery = array();
            $stockPlusQuery = [
                'id' => '',
                'item_id' => $data['convert_item_id'],
                'ref_id' => $data['id'],
                'ref_type' => 23,
                'trans_type' => 1,
                'location_id' => $data['location_convert'],
                'batch_no' => (!empty($data['batch_convert'])?$data['batch_convert']:'General Batch'),
                'size'=>$data['size'],
                'qty' => $data['convert_qty'],
                'stock_type' => 'USED'
            ];
            $this->store($this->stockTransaction, $stockPlusQuery);
        }
        return  $this->store($this->materialReturn, ['id'=>$data['id'],'trans_type'=>2]);
    }

    public function getRegrindingDTRows($data)
    {
        $data['tableName'] = $this->materialReturn;
        $data['select'] = 'material_return.*,item_master.full_name,requisition_log.log_no'; 
        $data['leftJoin']['requisition_log'] = 'requisition_log.id=material_return.ref_id';
        $data['leftJoin']['item_master'] = 'item_master.id=requisition_log.req_item_id';
        $data['where']['material_return.entry_type'] = 1;
        $data['where']['material_return.regranding_qty >'] = 0;
        $data['customWhere'][] = "material_return.regranding_qty > material_return.inspection_status";
        $data['order_by']['requisition_log.log_no'] = 'ASC';

        $data['searchCol'][] = "requisition_log.log_no";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "requisition_log.qty";

        $columns = array('', '', 'material_return.trans_date', 'item_master.item_name', 'material_return.qty', '', '');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }

    public function getRegrindingItemData($id)
    {
        $queryData = array();
        $queryData['tableName'] = $this->materialReturn;
        $queryData['select'] = 'material_return.*,item_master.full_name,item_master.item_name,item_master.item_code,requisition_log.log_no,requisition_log.req_item_id,item_master.size,item_master.id as item_id,rejection_comment.remark as regrindingReason';
        $queryData['leftJoin']['requisition_log'] = 'requisition_log.id=material_return.ref_id';
        $queryData['leftJoin']['item_master'] = 'item_master.id=requisition_log.req_item_id';		
        $queryData['leftJoin']['rejection_comment'] = 'rejection_comment.id=material_return.regrinding_reason';		
        $queryData['leftJoin']['( SELECT size,item_id,ref_type,ref_date,batch_no,is_delete FROM stock_transaction WHERE is_delete = 0 AND ref_type = 32 GROUP BY item_id, batch_no ORDER BY ref_date DESC ) AS st']  ='st.item_id = item_master.id AND st.batch_no = material_return.batch_no';

        $queryData['where_in']['material_return.id'] = $id;
        $result = $this->rows($queryData);
        $itemData = array();
        if(!empty($result)){
            foreach($result as $row){
                $stkData = $this->stockTransac->getCurrentSizeOfRegindingItems(['item_id'=>$row->item_id,'ref_type'=>32,'batch_no'=>$row->batch_no]);
                $row->size = !empty($stkData->size)?$stkData->size:$row->size;
                $itemData[] = $row;
            }
        }
        return $itemData;
    }

    public function saveChallan($masterData, $itemData)
    {
        
        try {
            $this->db->trans_begin();
            $chResult = $this->store($this->transMain, $masterData);
            $challan_id = $chResult['insert_id'];
            foreach ($itemData['item_id'] as $key => $value) :
                if(!empty($itemData['qty'][$key])){
                    $query['tableName'] = $this->materialReturn;
                    $query['select'] = 'material_return.*,requisition_log.req_item_id';
                    $query['leftJoin']['requisition_log'] = "requisition_log.id=material_return.ref_id";
                    $query['where']['material_return.id'] = $itemData['ref_id'][$key];
                    $retrnData =  $this->row($query);
                    $transData = [
                        'id' => $itemData['id'][$key],
                        'trans_main_id' => $challan_id,
                        'ref_id' => $itemData['ref_id'][$key],
                        'entry_type' => $masterData['entry_type'],
                        'item_id' => $value,
                        'item_name' =>  $itemData['item_name'][$key],
                        'item_code' =>  $itemData['item_code'][$key],
                        'qty' => $itemData['qty'][$key],
                        'rev_no'=> $itemData['length_dia'][$key],
                        'batch_no' => $itemData['batch_no'][$key],
                        'quote_rev_no' => $itemData['quote_rev_no'][$key],
                        'created_by' => $masterData['created_by']
                    ];
                    $transResult = $this->store($this->transChild, $transData);
                    /** Stock Remove from Inspection Store */
                    $stockMinusQuery = [
                        'id' => '',
                        'location_id' => ($retrnData->entry_type == 1)?$this->INSP_STORE->id:$this->SUPLY_REJ_STORE->id,
                        'batch_no'=>$itemData['batch_no'][$key],
                        'trans_type' => 2,
                        'item_id' => $value,
                        'qty' => '-' . $itemData['qty'][$key],
                        'size'=> $itemData['length_dia'][$key],
                        'ref_type' => 31,
                        'ref_id' => $challan_id,
                        'ref_no'=>$masterData['trans_number'],
                        'trans_ref_id' => $transResult['insert_id'],
                        'ref_date'=>$masterData['trans_date'],
                        'stock_effect' => 0,
                        'stock_type' => 'USED',
                        'created_by'=>$this->session->userdata('loginId')
                    ];
                    $this->store($this->stockTransaction, $stockMinusQuery);
                    $stockPlusQuery = array();
                    $stockPlusQuery = [
                        'id' => '',
                        'location_id' => $this->REGRIND_STORE->id,
                        'batch_no' => $itemData['batch_no'][$key],
                        'trans_type' => 1,
                        'item_id' => $value,
                        'qty' => $itemData['qty'][$key],
                        'size'=> $itemData['length_dia'][$key],
                        'ref_type' => 31,
                        'ref_id' => $challan_id,
                        'ref_no'=>$masterData['trans_number'],
                        'trans_ref_id' => $transResult['insert_id'],
                        'ref_date'=>$masterData['trans_date'],
                        'stock_effect' => 0,
                        'stock_type' => 'USED',
                        'created_by'=>$this->session->userdata('loginId')
                    ];
                    $this->store($this->stockTransaction, $stockPlusQuery);

                    $setData = array();
                    $setData['tableName'] = $this->materialReturn;
                    $setData['where']['id'] = $itemData['ref_id'][$key];
                    $setData['set']['inspection_status'] = 'inspection_status, + '.$itemData['qty'][$key];
                    $result = $this->setValue($setData);     
                }       
            endforeach;
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $chResult;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getRegrindingChallanDTRows($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = "trans_main.*,party_master.party_name,trans_child.trans_status";
        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $data['where']['trans_main.entry_type'] = 5;
        $data['where']['trans_main.order_type'] = 3;
        $data['where']['trans_child.trans_status'] = $data['status'];
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";
        $data['group_by'][] ="trans_child.trans_main_id";

        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
		$data['searchCol'][] = "CONCAT(trans_main.trans_prefix,trans_main.trans_no)";
        $data['searchCol'][] = "trans_main.party_name";

       
        $columns =array('','','trans_main.trans_date','trans_main.trans_no','trans_main.party_name');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}

		return $this->pagingRows($data);
    }

    public function getChallanMasterData($id){
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] ="trans_main.*,party_master.party_name,party_master.party_address,party_master.gstin";
        $queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $queryData['where']['trans_main.id'] = $id;
        $challanData = $this->row($queryData);
        return $challanData;
    }
    public function getChallanTransactions($postData){
        $queryData['tableName'] = $this->transChild;
        $queryData['select']="trans_child.*,item_master.full_name,item_master.size,rejection_comment.remark as regrinding_reason";
        $queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = trans_child.quote_rev_no";
        $queryData['where']['trans_main_id'] =$postData['trans_main_id'];
        // $queryData['where']['entry_type'] = 21;
        return $this->rows($queryData);
    }

    public function challanTransRow($id){
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*,trans_main.trans_number,item_master.full_name,item_master.size,rejection_comment.remark as regrinding_reason";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = trans_child.quote_rev_no";
        $queryData['where']['trans_child.id'] = $id;
        return $this->row($queryData);
    }

    public function saveReceiveItem($data){
        try {
            $this->db->trans_begin();
            foreach($data['dispatch_qty'] as $key=>$value){
                if($value > 0){
                    $size = (!empty($data['diameter'][$key])?$data['diameter'][$key]:0).'X'.(!empty($data['length'][$key])?$data['length'][$key]:0).'X'.(!empty($data['flute_length'][$key])?$data['flute_length'][$key]:0);

                    $chData = $this->challanTransRow($data['trans_child_id'][$key]);
                    $result = $this->store($this->transChild,['id'=>$data['trans_child_id'][$key],'dispatch_qty'=>$value,'location_id'=>$data['location_id'][$key],'cod_date'=>$data['ref_date'][$key],'drg_rev_no'=>$data['ref_no'][$key],'grn_data'=>$size,'trans_status'=>1]);
                    
                    /*$stockMinusQuery = [
                        'id' => '',
                        'location_id' => $this->REGRIND_STORE->id,
                        'batch_no'=>$chData->batch_no,
                        'trans_type' => 2,
                        'item_id' =>$chData->item_id,
                        'qty' => '-' . $value,
                        'ref_type' => 32,
                        'ref_id' => $chData->trans_main_id,
                        'ref_no' => $data['ref_no'][$key],
                        'trans_ref_id' => $data['trans_child_id'][$key],
                        'ref_date' => $data['ref_date'][$key],
                        'ref_batch'=>$chData->trans_number,
                        'size'=>$size,
                        'stock_effect' => 0,
                        'stock_type' => 'USED',
                        'created_by'=>$this->session->userdata('loginId')
                    ];
                    $stkResult = $this->store($this->stockTransaction, $stockMinusQuery);
                    $stockPlusQuery = array();
                    $stockPlusQuery = [
                        'id' => '',
                        'location_id' => $data['location_id'][$key],
                        'batch_no' => $chData->batch_no,
                        'trans_type' => 1,
                        'item_id' => $chData->item_id,
                        'qty' => $value,
                        'ref_type' => 32,
                        'ref_id' => $chData->trans_main_id,
                        'ref_no' => $data['ref_no'][$key],
                        'trans_ref_id' => $stkResult['insert_id'],
                        'ref_date' => $data['ref_date'][$key],
                        'size'=>$size,
                        'stock_type' => 'USED',
                        'created_by'=>$this->session->userdata('loginId')
                    ];
                    $this->store($this->stockTransaction, $stockPlusQuery);*/
                }
            }
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status'=>1,'message' => 'Item receive successfully'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    
    public function getRegrindingInspectionDTRows($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = "trans_child.*,trans_main.trans_date,trans_main.trans_prefix,trans_main.trans_no,party_master.party_name,trans_child.trans_status,item_master.full_name,rejection_comment.remark as regrinding_reason";
        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $data['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $data['leftJoin']['rejection_comment'] = "rejection_comment.id = trans_child.quote_rev_no";
        $data['where']['trans_main.entry_type'] = 5;
        $data['where']['trans_main.order_type'] = 3;
        $data['where']['trans_child.trans_status'] = 1;
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
		$data['searchCol'][] = "CONCAT(trans_main.trans_prefix,trans_main.trans_no)";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "trans_child.batch_no";
        $data['searchCol'][] = "DATE_FORMAT(trans_child.cod_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_child.drg_rev_no";
        $data['searchCol'][] = "trans_child.rev_no";
        $data['searchCol'][] = "trans_child.grn_data";
        $data['searchCol'][] = "rejection_comment.remark";

       
        $columns =array('','','trans_main.trans_date','trans_main.trans_no','trans_main.party_name,item_master.full_name,trans_child.batch_no,trans_child.cod_date,trans_child.drg_rev_no,trans_child.rev_no,trans_child.grn_data,rejection_comment.remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}

		return $this->pagingRows($data);
    }

    public function saveInspectedChallanItem($data){
        try {
            $this->db->trans_begin();
            $chData = $this->challanTransRow($data['id']);
            $convertedItemId = $data['converted_item_id'];unset($data['converted_item_id']);
            $result = $this->store($this->transChild,$data);
            
            $stockMinusQuery = [
                'id' => '',
                'location_id' => $this->REGRIND_STORE->id,
                'batch_no'=>$chData->batch_no,
                'trans_type' => 2,
                'item_id' =>$chData->item_id,
                'qty' => '-' . $chData->qty,
                'ref_type' => 32,
                'ref_id' => $chData->trans_main_id,
                'ref_no' => $chData->drg_rev_no,
                'trans_ref_id' => $chData->id,
                'ref_date' => date("Y-m-d"),
                'ref_batch'=>$chData->trans_number,
                'size'=>$chData->rev_no,
                'stock_effect' => 0,
                'stock_type' => 'USED',
                'created_by'=>$this->session->userdata('loginId')
            ];
            $stkResult = $this->store($this->stockTransaction, $stockMinusQuery);
            $location_id = $chData->location_id;
            if($data['trans_status'] == 2 || $data['trans_status'] == 5){$location_id = $chData->location_id;}
            else if($data['trans_status'] == 3){$location_id = $this->SCRAP_STORE->id;}
            else if($data['trans_status'] == 4){$location_id = $this->SUPLY_REJ_STORE->id;}
            if($data['trans_status'] != 5){
                $stockPlusQuery = array();
                $stockPlusQuery = [
                    'id' => '',
                    'location_id' => $location_id,
                    'batch_no' => $chData->batch_no,
                    'trans_type' => 1,
                    'item_id' => $chData->item_id,
                    'qty' => $chData->qty,
                    'ref_type' => 32,
                    'ref_id' => $chData->trans_main_id,
                    'ref_no' =>$chData->drg_rev_no,
                    'trans_ref_id' => $stkResult['insert_id'],
                    'ref_date' =>date("Y-m-d"),
                    'size'=>$chData->grn_data,
                    'stock_type' => 'USED',
                    'created_by'=>$this->session->userdata('loginId')
                ];
                if($data['trans_status'] == 4){ $stockPlusQuery['stock_effect'] = 0; }
                $this->store($this->stockTransaction, $stockPlusQuery);
            }else{
                
                $nextBatchNo = $this->gateReceipt->getNextBatchOrSerialNo(['trans_id'=>"",'item_id'=>$convertedItemId,'heat_no'=>'']);
                $batchData = [
                    'id' => '',
                    'mir_id' => 0,
                    'type' => 1,
                    'ref_id'=> $chData->id,
                    'location_id' => $location_id,
                    'qty' =>  $chData->qty,
                    'item_id' => $convertedItemId,
                    'serial_no'=>$nextBatchNo['serial_no'],
                    'batch_no'=>$nextBatchNo['batch_no'],
                    'heat_no' => $chData->batch_no,
                    'mill_heat_no' =>$chData->item_id,
                    'forging_tracebility' => '',
                    'heat_tracebility' => '',   
                    'created_by'=>$this->loginId,                 
                    'created_at'=> date("Y-m-d H:i:s"),                 
                    'is_delete' => 0
                ];
                $batch = $this->store('mir_transaction',$batchData);
                $stockPlusQuery = array();
                $stockPlusQuery = [
                    'id' => '',
                    'location_id' => $location_id,
                    'batch_no' =>$nextBatchNo['batch_no'],
                    'trans_type' => 1,
                    'item_id' =>$convertedItemId,
                    'qty' => $chData->qty,
                    'ref_type' => 33,
                    'ref_id' => $chData->trans_main_id,
                    'ref_no' =>$chData->drg_rev_no,
                    'trans_ref_id' => $stkResult['insert_id'],
                    'ref_date' =>date("Y-m-d"),
                    'ref_batch'=>$batch['insert_id'],
                    'size'=>$chData->grn_data,
                    'stock_type' => 'USED',
                    'created_by'=>$this->session->userdata('loginId')
                ];
                $this->store($this->stockTransaction, $stockPlusQuery);

            }
            if($data['trans_status'] == 4){
                $returnData = [
                    'id' => '',
                    'ref_id' => $data['id'],
                    'entry_type'=>2,
                    'batch_no' => $chData->batch_no,
                    'qty' => $chData->qty,
                    'regranding_qty' => $chData->qty,
                    'trans_date' => date("Y-m-d"),
                    'trans_type' => 1,
                    'reason' => $data['item_remark'],
                    'created_by' => $this->session->userdata('loginId'),
                ];
                $this->store($this->materialReturn, $returnData);  
            }
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status'=>1,'message' => 'Item receive successfully'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getReRegrindingDTRows($data)
    {
        $data['tableName'] = $this->materialReturn;
        $data['select'] = 'material_return.*,item_master.full_name,trans_child.drg_rev_no,trans_child.rev_no,trans_child.grn_data,party_master.party_name'; 
        $data['leftJoin']['trans_child'] = 'trans_child.id=material_return.ref_id';
        $data['leftJoin']['trans_main'] = 'trans_main.id=trans_child.trans_main_id';
        $data['leftJoin']['item_master'] = 'item_master.id=trans_child.item_id';
        $data['leftJoin']['party_master'] = 'party_master.id=trans_main.party_id';
        $data['where']['material_return.entry_type'] =2;
        $data['where']['material_return.regranding_qty >'] = 0;
        $data['customWhere'][] = "material_return.regranding_qty > material_return.inspection_status";
        $data['order_by']['material_return.id'] = 'DESC';

        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_child.drg_rev_no";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "material_return.batch_no";
        $data['searchCol'][] = "trans_child.rev_no";
        $data['searchCol'][] = "trans_child.grn_data";

        $columns = array('', '', 'material_return.trans_date', 'trans_child.drg_rev_no', 'party_master.party_name', 'item_master.full_name', 'material_return.batch_no','trans_child.rev_no','trans_child.grn_data');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }

    public function getReRegrindingItemData($id)
    {
        $queryData = array();
        $queryData['tableName'] = $this->materialReturn;
        $queryData['select'] = 'material_return.*,trans_child.item_id as req_item_id,item_master.full_name,item_master.item_name,item_master.item_code,item_master.size,item_master.id as item_id,rejection_comment.remark as regrindingReason';
        $queryData['leftJoin']['trans_child'] = 'trans_child.id=material_return.ref_id';
        $queryData['leftJoin']['trans_main'] = 'trans_main.id=trans_child.trans_main_id';
        $queryData['leftJoin']['item_master'] = 'item_master.id=trans_child.item_id';
        $queryData['leftJoin']['party_master'] = 'party_master.id=trans_main.party_id';
        $queryData['leftJoin']['rejection_comment'] = 'rejection_comment.id=material_return.regrinding_reason';		

        $queryData['where_in']['material_return.id'] = $id;
        $result = $this->rows($queryData);
        $itemData = array();
        if(!empty($result)){
            foreach($result as $row){
                $stkData = $this->stockTransac->getCurrentSizeOfRegindingItems(['item_id'=>$row->item_id,'ref_type'=>32,'batch_no'=>$row->batch_no]);
                $row->size = !empty($stkData->size)?$stkData->size:$row->size;
                $itemData[] = $row;
            }
        }
        return $itemData;
    }

}
