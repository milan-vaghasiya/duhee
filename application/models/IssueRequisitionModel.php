<?php

class IssueRequisitionModel extends MasterModel
{
    private $requisitionLog = "requisition_log";
    private $material_issue = "material_issue";
    private $itemMaster = "item_master";
    private $materialReturn = "material_return";
    private $stockTransaction = "stock_transaction";

    public function getDTRows($data)
    {
        $data['tableName'] = $this->requisitionLog;
        $data['leftJoin']['item_master'] = "item_master.id = requisition_log.req_item_id";
        $data['leftJoin']['item_master wtu'] = "wtu.id = requisition_log.fg_item_id";
        $data['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
        if (!empty($data['mType'])) {
            $data['where_in']['item_master.item_type'] = $data['mType'];
        }

        if ($data['status'] == 2) {
            $data['select'] = "requisition_log.*,item_master.item_name,item_master.description,item_master.make_brand,unit_master.unit_name,item_master.qty as stock_qty,item_master.full_name,reqlog.used_at as usedAt, reqlog.log_no as req_no,reqlog.req_type as reuisition_type,issueBy.emp_name as issue_by,wtu.item_name as wtu_name";
            $data['where']['requisition_log.log_type'] = 2;
            $data['where']['requisition_log.order_status'] = 2;
            $data['leftJoin']['requisition_log as reqlog'] = "reqlog.id = requisition_log.ref_id";
            //$data['leftJoin']['employee_master'] = "employee_master.id = reqlog.handover_to";
            $data['leftJoin']['employee_master as issueBy'] = "issueBy.id = requisition_log.created_by";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "DATE_FORMAT(requisition_log.req_date,'%d-%m-%Y')";
            $data['searchCol'][] = "CONCAT('ISU',LPAD(requisition_log.log_no, 5, '0'))";
            $data['searchCol'][] = "CONCAT('REQ',LPAD(reqlog.log_no, 5, '0'))";
            $data['searchCol'][] = "item_master.full_name";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "requisition_log.req_qty";
            $data['searchCol'][] = "requisition_log.remark";
            $data['searchCol'][] = "";
            $columns = array('', 'requisition_log.req_date', 'requisition_log.log_no', 'reqlog.req_no', 'item_master.full_name', '', '', 'requisition_log.req_qty', 'requisition_log.remark', '');
        }
        if ($data['status'] == 1) {
            $data['tableName'] = $this->requisitionLog;
            $data['select'] = "requisition_log.*,item_master.item_name,item_master.description,item_master.make_brand,unit_master.unit_name,item_master.qty as stock_qty,item_master.full_name,reqlog.log_no as req_no,reqlog.req_type as reuisition_type,issueBy.emp_name as issue_by,wtu.item_name as wtu_name";
            $data['leftJoin']['item_master'] = "item_master.id = requisition_log.req_item_id";
            $data['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
            $data['leftJoin']['requisition_log as reqlog'] = "reqlog.id = requisition_log.ref_id";
            //$data['leftJoin']['employee_master'] = "employee_master.id = reqlog.handover_to";
            $data['leftJoin']['employee_master as issueBy'] = "issueBy.id = requisition_log.created_by";
            $data['where']['requisition_log.log_type'] = 2;
            $data['where']['requisition_log.order_status'] = 1;
            // $data['leftJoin']['requisition_log as indent'] = "indent.id = requisition_log.ref_id";
            if (!empty($data['due_status']) && $data['due_status'] == 1) {
                $data['where']['DATE(reqlog.delivery_date) <'] = date("Y-m-d");
            }
            if (!empty($data['due_status']) && $data['due_status'] == 2) {
                $data['where']['DATE(reqlog.delivery_date) >='] = date("Y-m-d");
            }
            $data['searchCol'][] = "";
            $data['searchCol'][] = "DATE_FORMAT(requisition_log.req_date,'%d-%m-%Y')";
            $data['searchCol'][] = "CONCAT('ISU',LPAD(requisition_log.log_no, 5, '0'))";
            $data['searchCol'][] = "CONCAT('REQ',LPAD(reqlog.log_no, 5, '0'))";
            $data['searchCol'][] = "item_master.full_name";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "requisition_log.req_qty";
            $data['searchCol'][] = "requisition_log.remark";
            $data['searchCol'][] = "";
            $columns = array('', 'requisition_log.req_date', 'requisition_log.log_no', 'reqlog.req_no', 'item_master.full_name', '', '', 'requisition_log.req_qty', 'requisition_log.remark', '');
        }
        
        if ($data['status'] == 0) {
            $data['tableName'] = $this->requisitionLog;
            $data['select'] = "requisition_log.*,item_master.item_name,item_master.description,item_master.make_brand,unit_master.unit_name,item_master.qty as stock_qty,item_master.full_name,wtu.item_name as wtu_name";
            $data['leftJoin']['item_master'] = "item_master.id = requisition_log.req_item_id";
            $data['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
            $data['where_in']['requisition_log.order_status'] = 4;
            $data['where']['requisition_log.log_type'] = 1;
            $data['where']['requisition_log.approved_by !='] = 0;
            if (!empty($data['due_status']) && $data['due_status'] == 1) {
                $data['where']['DATE(requisition_log.delivery_date) <'] = date("Y-m-d");
            }
            if (!empty($data['due_status']) && $data['due_status'] == 2) {
                $data['where']['DATE(requisition_log.delivery_date) >'] = date("Y-m-d");
            }
            if (!empty($data['due_status']) && $data['due_status'] == 3) {
                $data['where']['DATE(requisition_log.delivery_date)'] = date("Y-m-d");
            }
            $data['searchCol'][] = "";
            $data['searchCol'][] = "requisition_log.urgency";
            $data['searchCol'][] = "CONCAT('REQ',LPAD(requisition_log.log_no, 5, '0'))";
            $data['searchCol'][] = "DATE_FORMAT(requisition_log.req_date,'%d-%m-%Y')";
            $data['searchCol'][] = "item_master.full_name";
            $data['searchCol'][] = "item_master.qty";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "requisition_log.req_qty";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $columns = array('', '', 'requisition_log.req_no', 'requisition_log.req_date', 'item_master.full_name', 'item_master.stock_qty', '', '', 'requisition_log.req_qty', '', '', '', '', '', '', '');
        }
        
        // Order By Descending
        $data['order_by']['requisition_log.req_date'] = "DESC";
        $data['order_by']['requisition_log.id'] = "DESC";
        
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }

    public function getDTRowsForMaterialReturn($data)
    {
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "requisition_log.*,req.log_no as req_no,item_master.item_name,item_master.description,item_master.make_brand,unit_master.unit_name,item_master.qty as stock_qty,item_master.full_name,reqlog.log_no as req_no,reqlog.req_type as reuisition_type,issueBy.emp_name as issue_by";
        $data['leftJoin']['item_master'] = "item_master.id = requisition_log.req_item_id";
        $data['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
        $data['leftJoin']['requisition_log as req'] = "req.id = requisition_log.ref_id";
        $data['where']['requisition_log.log_type'] = 2;
        $data['where']['requisition_log.order_status'] = $data['status'];
        if (!empty($data['mType'])) {
            $data['where_in']['item_master.item_type'] = $data['mType'];
        }

        $data['leftJoin']['requisition_log as reqlog'] = "reqlog.id = requisition_log.ref_id";
        if ($data['status'] == 2) {
            $data['leftJoin']['employee_master as issueBy'] = "issueBy.id = requisition_log.approved_by";
        }
        if ($data['status'] == 1) {
            $data['leftJoin']['employee_master as issueBy'] = "issueBy.id = requisition_log.created_by";
        }
        if ($this->loginID != '1') {
            $data['customWhere'][] = '(reqlog.created_by=' . $this->loginID . ' OR  FIND_IN_SET(' . $this->loginID . ',reqlog.auth_detail)) ';
        }
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(requisition_log.req_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT('REQ',LPAD(req.log_no, 5, '0'))";
        $data['searchCol'][] = "CONCAT('ISU',LPAD(requisition_log.log_no, 5, '0'))";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "requisition_log.req_qty";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $columns = array('', 'requisition_log.req_date', 'req.log_no', 'requisition_log.req_no', 'item_master.full_name', 'requisition_log.req_qty', '', '');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function save($data)
    {
        try {
            $this->db->trans_begin();
            $issueType = $data['issue_type'];
            unset($data['issue_type']);
            $empData = $this->employee->getEmp($data['req_emp_id']);
            unset($data['req_emp_id']);
            $strQuery['tableName'] = "location_master";
            $strQuery['where']['other_ref'] = $empData->emp_dept_id;
            $strQuery['where']['store_type'] = 2;
            $strResult = $this->row($strQuery);
            $batch_qty = array();
            $batch_no = array();
            $location_id = array();
            $batch_qty = explode(",", $data['batch_qty']);
            $batch_no = explode(",", $data['batch_no']);
            $stockType = explode(",", $data['stock_type']);
            $location_id = explode(",", $data['location_id']);
            $size = explode(",", $data['size']);
            unset($data['batch_qty'], $data['batch_no'], $data['location_id'], $data['stock_type'],$data['size']);
            if (!empty($data['id'])) {
                $this->remove('stock_transaction', ['ref_id' => $data['id'], 'ref_type' => 16]);
                $issueTransData = $this->getIssueMaterialData($data['id']);
                if (!empty($issueTransData->dispatch_qty) and $issueTransData->dispatch_qty > 0) :
                    $setData = array();
                    $setData['tableName'] = $this->itemMaster;
                    $setData['where']['id'] = $issueTransData->req_item_id;
                    $setData['set']['qty'] = 'qty, + ' . $issueTransData->dispatch_qty;
                    $qryresult = $this->setValue($setData);
                endif;
            }
            if (empty($data['id'])) {
                $data['log_no'] = $this->nextIssueNo($issueType);
            }
            $data['log_type'] = 2;
            if ($issueType == 1) {
                $data['order_status'] = 1;
            } else {
                $data['order_status'] = 2;
            }
            $data['req_qty'] = array_sum($batch_qty);
            $saveIssueData = $this->store($this->requisitionLog, $data);
            $issueId = (!empty($data['id']) ? $data['id'] : $saveIssueData['insert_id']);
            foreach ($batch_qty as $bk => $bv) :
                if (!empty($bv) && $bv > 0) {
                    $stockQueryData['id'] = "";
                    $stockQueryData['location_id'] = $location_id[$bk];
                    if (!empty($batch_no[$bk])) {
                        $stockQueryData['batch_no'] = $batch_no[$bk];
                    }
                    $stockQueryData['trans_type'] = 2;
                    $stockQueryData['item_id'] = $data['req_item_id'];
                    $stockQueryData['qty'] = '-' . $bv;
                    $stockQueryData['ref_type'] = 16;
                    $stockQueryData['ref_id'] = $issueId;
                    $stockQueryData['ref_no'] = $data['ref_id'];
                    $stockQueryData['ref_date'] =  $data['req_date'];
                    $stockQueryData['created_by'] = $data['created_by'];
                    $stockQueryData['stock_type'] = $stockType[$bk];
                    $stockQueryData['size'] = $size[$bk];
                    $stockResult = $this->store('stock_transaction', $stockQueryData);
                    $location = ($issueType == 1) ? $this->ALLOT_RM_STORE->id : $strResult->id;
                    $bookItemQuery = [
                        'id' => '',
                        'location_id' => $location,
                        'trans_type' => 1,
                        'item_id' => $data['req_item_id'],
                        'qty' => $bv,
                        'ref_type' => 16,
                        'ref_id' => $issueId,
                        'ref_no' => $stockResult['insert_id'],
                        'batch_no' => $batch_no[$bk],
                        'ref_batch' => $strResult->id,
                        'ref_date' => $data['req_date'],
                        'size' => $size[$bk],
                        'created_by' => $data['created_by'],
                        'stock_effect' => 0
                    ];
                    $this->store('stock_transaction', $bookItemQuery);
                }
            endforeach;
            $setData = array();
            $setData['tableName'] = $this->itemMaster;
            $setData['where']['id'] = $data['req_item_id'];
            $setData['set']['qty'] = 'qty, - ' . array_sum($batch_qty);
            $qryresult = $this->setValue($setData);
            //job card status update
            if ($issueType == 2) :
                $reqData = $this->purchaseRequest->getPurchaseRequest($data['ref_id']);
                $issueTransData = $this->getIssueMaterialData($data['ref_id']);
                if ($issueTransData->req_qty >= $reqData->req_qty) {
                    $this->edit($this->requisitionLog, ['id' => $data['ref_id']], ['order_status' => 1]);
                }
            endif;
            
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Material Issue suucessfully.','issue_id'=> $issueId];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getIssueMaterialData($id)
    {
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "SUM(req_qty) as req_qty";
        $data['where']['log_type'] = 2;
        $data['where']['order_status'] = 2;
        $data['where']['ref_id'] = $id;
        return $this->row($data);
    }

    public function getAllotMaterialData($id)
    {
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "SUM(req_qty) as req_qty";
        $data['where']['log_type'] = 2;
        $data['where']['order_status'] = 1;
        $data['where']['ref_id'] = $id;
        return $this->row($data);
    }

    public function getIndentMaterialData($id)
    {
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "SUM(req_qty) as req_qty";
        $data['where']['log_type'] = 3;
        $data['where']['ref_id'] = $id;
        return $this->row($data);
    }

    public function getIssueMaterialLog($id)
    {
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = 'requisition_log.*,item_master.full_name,item_master.item_code,employee_master.emp_name,approve.emp_name as approved_name,unit_master.unit_name,item_category.category_name, machine.item_name as machine_name, machine.item_code as machine_code,item_master.no_of_corner,item_master.tool_life,um.unit_name as tool_unit,reqLog.handover_to as handoverTo';
        $data['leftJoin']['requisition_log as reqLog'] = 'reqLog.ref_id = requisition_log.id';
        $data['leftJoin']['item_master'] = 'item_master.id=requisition_log.req_item_id';
        $data['leftJoin']['item_master as machine'] = 'machine.id = requisition_log.machine_id';
        $data['leftJoin']['unit_master'] = 'unit_master.id=item_master.unit_id';
        $data['leftJoin']['employee_master'] = 'employee_master.id=requisition_log.created_by';
        $data['leftJoin']['employee_master as approve'] = 'approve.id=requisition_log.approved_by';
        $data['leftJoin']['item_category'] = 'item_category.id=item_master.category_id';
        $data['leftJoin']['unit_master um'] = 'um.id=item_master.tool_life_unit';
        $data['where']['requisition_log.id'] = $id;
        return $this->row($data);
    }

    public function nextIssueNo($order_status)
    {
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "MAX(log_no) as issue_no";
        $data['where']['requisition_log.log_type'] = 2;
        $data['where']['requisition_log.order_status'] = $order_status;
        $maxNo = $this->specificRow($data)->issue_no;
        $nextIndentNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextIndentNo;
    }

    public function batchWiseItemStock($data)
    {
        $i = 1;
        $tbody = "";
        $queryData = array();
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(stock_transaction.qty) as qty,stock_transaction.size,stock_transaction.batch_no,stock_type,location_id,location_master.store_name,location_master.location,item_master.item_type,stock_transaction.item_id,item_master.size as master_size";
        $queryData['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
        $queryData['join']['location_master'] = "location_master.id = stock_transaction.location_id";
        $queryData['where']['stock_transaction.item_id'] = $data['item_id'];
        $queryData['where']['stock_effect'] = 1;
        $queryData['where']['location_master.store_type !='] = 4; // Not Scrap
        if (!empty($data['req_type'])) {
            $queryData['where']['stock_type'] = $data['req_type']; // Fresh/Used
        }
        $queryData['order_by']['stock_transaction.id'] = "asc";
        $queryData['group_by'][] = "stock_transaction.location_id,stock_transaction.batch_no,stock_transaction.size";
        $result = $this->rows($queryData);
        // $this->printQuery();
        $resultSet = array();
        if (!empty($result)) {
            $batch_no = array();
            foreach ($result as $row) {
                
                if($row->item_type == 2){$row->size = !empty($row->size)?$row->size:$row->master_size;}
                $resultSet[] =$row;
                $batch_no = (!empty($data['trans_id'])) ? explode(",", $data['  ']) : $data['batch_no'];
                $batch_qty = (!empty($data['trans_id'])) ? explode(",", $data['batch_qty']) : $data['batch_qty'];
                if ($row->qty > 0 || !empty($batch_no) && in_array($row->batch_no, $batch_no)) :
                    if (!empty($batch_no) && in_array($row->batch_no, $batch_no)) :
                        $arrayKey = array_search($row->batch_no, $batch_no);
                        $qty = $batch_qty[$arrayKey];
                        $cl_stock = (!empty($data['trans_id'])) ? floatVal($row->qty + $batch_qty[$arrayKey]) : floatVal($row->qty);
                    else :
                        $qty = "";
                        $cl_stock = floatVal($row->qty);
                    endif;
                    $tbody .= '<tr>';
                    $tbody .= '<td class="text-center">' . $i . '</td>';
                    $tbody .= '<td>[' . $row->store_name . '] ' . $row->location . '</td>';
                    $tbody .= '<td>' . $row->batch_no. (!empty($row->size)?' ( '.$row->size.' )':'') .  '</td>';
                    $tbody .= '<td>' . floatVal($row->qty) . '</td>';
                    $tbody .= '<td>
                            <input type="text" name="batch_quantity[]" class="form-control batchQty floatOnly" data-rowid="' . $i . '" data-cl_stock="' . $cl_stock . '" min="0" value="' . $qty . '" />
                            <input type="hidden" name="batch_number[]" id="batch_number' . $i . '" value="' . $row->batch_no . '" />
                            <input type="hidden" name="location[]" id="location' . $i . '" value="' . $row->location_id . '" />
                            <input type="hidden" name="stock_type[]" id="stock_type' . $i . '" value="' . $row->stock_type . '" />
                            <input type="hidden" name="size[]" id="size' . $i . '" value="' . $row->size . '" />
                            <div class="error batch_qty' . $i . '"></div>
                        </td>';
                    $tbody .= '</tr>';
                    $i++;
                endif;
            }
        } else {
            $tbody = '<tr><td class="text-center" colspan="5">No Data Found.</td></tr>';
        }
        return ['status' => 1, 'batchData' => $tbody,'resultSet'=>$resultSet];
    }

    public function materialIssueFrmAllot($data)
    {
        $logData = $this->getIssueMaterialLog($data['id']);
        $stockQuery['tableName'] = "stock_transaction";
        $stockQuery['where']['ref_id'] = $data['id'];
        $stockQuery['where']['ref_type'] = 16;
        $stockQuery['where']['trans_type'] = 1;
        $stockData = $this->rows($stockQuery);
        foreach ($stockData as $row) :
            $bookItemQuery = [
                'id' => $row->id,
                'location_id' => $row->ref_batch,
            ];
            $this->store('stock_transaction', $bookItemQuery);
        endforeach;
        $log_no = $this->nextIssueNo(2);
        $result = $this->store($this->requisitionLog, ['id' => $data['id'], 'order_status' => 2, 'approved_by' => $data['approved_by'], 'log_no' => $log_no]);
        $reqData = $this->purchaseRequest->getPurchaseRequest($logData->ref_id);
        $issueTransData = $this->getIssueMaterialData($logData->ref_id);
        if ($issueTransData->req_qty >= $reqData->req_qty) {
            $result = $this->store($this->requisitionLog, ['id' => $logData->ref_id, 'order_status' => 1]);
        }
        return $result;
    }

    public function getReturnMaterialData($id)
    {
        $data['tableName'] = $this->materialReturn;
        $data['select'] = "SUM(qty) as qty";
        $data['where']['trans_type'] = 0;
        $data['where']['ref_id'] = $id;
        $result = $this->row($data);
        return $result;
    }

    public function getIssueMaterialList()
    {
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "requisition_log.*,item_master.full_name";
        $data['leftJoin']['item_master'] = 'item_master.id=requisition_log.req_item_id';
        $data['where']['order_status'] = 2;
        $data['where']['log_type'] = 2;
        return $this->rows($data);
    }

    public function delete($id)
    {
        $issueData = $this->getIssueMaterialLog($id);
        if ($issueData->log_type != 3) {
            $this->remove("stock_transaction", ['ref_id' => $id, 'ref_type' => 16]);
            $this->store($this->requisitionLog, ['id' => $issueData->ref_id, 'order_status' => 4]);
        }
        return $this->trash($this->requisitionLog, ['id' => $id]);
    }

    //Created By Karmi @06/05/2022
    public function getMaxIssueDate($id)
    {
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "MAX(req_date) as req_date,MAX(log_no) as req_no_max";
        $data['where']['log_type'] = 2;
        $data['where']['order_status'] = 2;
        $data['where']['ref_id'] = $id;
        return $this->row($data);
    }


    public function getDTRowsMaterialReturn($data)
    {
        $data['tableName'] = 'requisition_log';
        $data['select'] = "stock_transaction.batch_no,SUM(abs(stock_transaction.qty)) as issue_qty,requisition_log.id,requisition_log.is_returnable,requisition_log.req_qty,requisition_log.log_no,requisition_log.req_date,req.log_no as req_no,item_master.item_name,item_master.description,item_master.make_brand,unit_master.unit_name,item_master.qty as stock_qty,item_master.full_name,reqlog.log_no as req_no,reqlog.req_type as reuisition_type,issueBy.emp_name as issue_by,IFNULL(material_return.return_qty,0) as return_qty,stock_transaction.size";
        $data['leftJoin']['stock_transaction'] = "requisition_log.id = stock_transaction.ref_id";
        $data['leftJoin']['item_master'] = "item_master.id = requisition_log.req_item_id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $data['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
        $data['leftJoin']['requisition_log as req'] = "req.id = requisition_log.ref_id";
        $data['leftJoin']['( SELECT SUM(qty) as return_qty,ref_id,batch_no FROM material_return WHERE  is_delete = 0 GROUP BY ref_id,batch_no) as material_return'] = "material_return.batch_no = stock_transaction.batch_no AND material_return.ref_id = requisition_log.id";

        $data['where']['requisition_log.log_type'] = 2;
        $data['where']['stock_transaction.ref_type'] = 16;
        $data['where']['stock_transaction.trans_type'] = 2;
        $data['where']['requisition_log.order_status'] = $data['status'];
        $data['where']['item_category.is_return'] = 1;
        $data['having'][] ='(issue_qty-return_qty) > 0' ;
        if (!empty($data['mType'])) {
            $data['where_in']['item_master.item_type'] = $data['mType'];
        }

        $data['leftJoin']['requisition_log as reqlog'] = "reqlog.id = requisition_log.ref_id";
        if ($data['status'] == 2) {
            $data['leftJoin']['employee_master as issueBy'] = "issueBy.id = requisition_log.approved_by";
        }
        if ($data['status'] == 1) {
            $data['leftJoin']['employee_master as issueBy'] = "issueBy.id = requisition_log.created_by";
        }
        if ($this->loginID != '1') {
            $data['customWhere'][] = '(reqlog.created_by=' . $this->loginID . ' OR  FIND_IN_SET(' . $this->loginID . ',reqlog.auth_detail)) ';
        }
        
        $data['group_by'][] = 'req.id,stock_transaction.batch_no';
        $data['order_by']['requisition_log.req_date'] = 'DESC';
        $data['order_by']['requisition_log.id'] = 'DESC';
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(requisition_log.req_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT('REQ',LPAD(req.log_no, 5, '0'))";
        $data['searchCol'][] = "CONCAT('ISU',LPAD(requisition_log.log_no, 5, '0'))";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "requisition_log.req_qty";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        
        $columns = array('', 'requisition_log.req_date', 'req.log_no', 'requisition_log.req_no', 'item_master.full_name', 'requisition_log.req_qty', '', '');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        // print_r($this->db->last_query());
        return $result;
    }

    public function getReturnMaterialDataBatchWise($id, $batch_no)
    {
        $data['tableName'] = $this->materialReturn;
        $data['select'] = "SUM(qty) as qty";
        $data['where']['ref_id'] = $id;
        $data['where']['batch_no'] = $batch_no;
        $result = $this->row($data);
        return $result;
    }
}
