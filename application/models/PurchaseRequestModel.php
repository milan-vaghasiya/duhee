<?php
class PurchaseRequestModel extends MasterModel
{
    private $requisitionLog = "requisition_log";
    private $purchase_planning_type = "purchase_planning_type";
    private $materialReturn = "material_return";
    private $stockTransaction = "stock_transaction";

    public function getDTRows($data)
    { 
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "requisition_log.*,item_master.item_name,item_master.item_code,item_master.part_no,item_master.full_name,item_master.make_brand,item_master.unit_id,unit_master.unit_name,emp.emp_name as whom_to_handover,employee_master.emp_name, (CASE WHEN (requisition_log.urgency = 2) THEN 'High' ELSE (CASE WHEN (requisition_log.urgency = 1) THEN 'Medium' ELSE 'Low' END) END) as priority,item_category.auth_detail as auth_detail_main";
        $data['leftJoin']['employee_master as emp'] = "emp.id = requisition_log.handover_to";
        $data['leftJoin']['employee_master'] = "employee_master.id = requisition_log.approved_by";
        $data['leftJoin']['item_master'] = "item_master.id = requisition_log.req_item_id";
        $data['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $data['where']['requisition_log.log_type'] = 1;
        $data['where']['requisition_log.order_status'] = $data['status'];
        if(!empty($data['mType'])){ $data['where_in']['item_master.item_type'] = $data['mType']; }

        if ($this->loginID != '1') {
            $data['customWhere'][] = '(requisition_log.created_by=' . $this->loginID . ' OR  FIND_IN_SET(' . $this->loginID . ',requisition_log.auth_detail)) ';
        }
        $data['order_by']['requisition_log.req_date'] = 'DESC';
        $data['order_by']['requisition_log.id'] = 'DESC';
        if ($data['status'] == 4) {
            $data['searchCol'][] = "";
            $data['searchCol'][] = "requisition_log.urgency";
            $data['searchCol'][] = "CONCAT('REQ',LPAD(requisition_log.log_no, 5, '0'))";
            $data['searchCol'][] = "DATE_FORMAT(requisition_log.req_date,'%d-%m-%Y')";
            $data['searchCol'][] = "item_master.full_name";
            $data['searchCol'][] = "DATE_FORMAT(requisition_log.delivery_date,'%d-%m-%Y')";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "emp.emp_name";
            $data['searchCol'][] = "requisition_log.req_qty";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "requisition_log.approved_by";
        }
        if ($data['status'] == 1) {
            $data['searchCol'][] = "";
            $data['searchCol'][] = "requisition_log.urgency";
            $data['searchCol'][] = "CONCAT('REQ',LPAD(requisition_log.log_no, 5, '0'))";
            $data['searchCol'][] = "DATE_FORMAT(requisition_log.req_date,'%d-%m-%Y')";
            $data['searchCol'][] = "item_master.full_name";
            $data['searchCol'][] = "DATE_FORMAT(requisition_log.delivery_date,'%d-%m-%Y')";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "emp.emp_name";
            $data['searchCol'][] = "requisition_log.req_qty";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "employee_master.emp_name";
        }
        if ($data['status'] == 2) {
            $data['searchCol'][] = "";
            $data['searchCol'][] = "requisition_log.urgency";
            $data['searchCol'][] = "CONCAT('REQ',LPAD(requisition_log.log_no, 5, '0'))";
            $data['searchCol'][] = "DATE_FORMAT(requisition_log.req_date,'%d-%m-%Y')";
            $data['searchCol'][] = "item_master.full_name";
            $data['searchCol'][] = "DATE_FORMAT(requisition_log.delivery_date,'%d-%m-%Y')";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "emp.emp_name";
            $data['searchCol'][] = "requisition_log.req_qty";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "employee_master.emp_name";
        }
        if ($data['status'] == 0) {
            $data['searchCol'][] = "";
            $data['searchCol'][] = "requisition_log.urgency";
            $data['searchCol'][] = "CONCAT('REQ',LPAD(requisition_log.log_no, 5, '0'))";
            $data['searchCol'][] = "DATE_FORMAT(requisition_log.req_date,'%d-%m-%Y')";
            $data['searchCol'][] = "item_master.full_name";
            $data['searchCol'][] = "DATE_FORMAT(requisition_log.delivery_date,'%d-%m-%Y')";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "emp.emp_name";
            $data['searchCol'][] = "requisition_log.req_qty";
            $data['searchCol'][] = "";
        }

        $columns = array('', '', 'requisition_log.log_no', 'requisition_log.req_date', 'item_master.item_name', 'requisition_log.used_at', '', 'emp.emp_name', 'requisition_log.req_qty', '', 'employee_master.emp_name', '');
        if (isset($data['order'])) { $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir']; }
        return $this->pagingRows($data);
    }

    public function getApprovedByList()
    {
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "requisition_log.approved_by,employee_master.emp_name";
        $data['leftJoin']['employee_master'] = "employee_master.id = requisition_log.approved_by";
        $data['where']['requisition_log.approved_by > '] = 0;
        $data['group_by'][] = 'requisition_log.approved_by';

        $result = $this->rows($data);
        return $result;
    }

    public function getPurchaseRequest($id)
    {
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "requisition_log.*,item_master.item_name,item_master.item_code,item_master.part_no,item_master.full_name,item_master.gst_per,item_master.price,item_master.unit_id,item_master.hsn_code, unit_master.unit_name,item_master.min_qty,item_master.max_qty,item_master.make_brand,item_master.lead_time,item_master.item_type,item_master.category_id,item_master.family_id";
        $data['leftJoin']['item_master'] = "item_master.id = requisition_log.req_item_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['leftJoin']['purchase_planning_type'] = "purchase_planning_type.id = requisition_log.planning_type";
        $data['where']['requisition_log.id'] = $id;
        $result = $this->row($data);
        $result->fgitem_name = (!empty($result->fgitem_id)) ? $this->item->getItem($result->fgitem_id)->item_name : "";
        return $result;
    }



    public function closePreq($data)
    {
        $this->store($this->requisitionLog, ['id' => $data['id'], 'order_status' => $data['val']]);
        return ['status' => 1, 'message' => 'Purchase Order ' . $data['msg'] . ' successfully.'];
    }

    /*  Change By : Avruti @7-12-2021 04:00 PM
        update by : 
        note : Sales Enquiry No
    */
    public function getPurchaseOrder()
    {
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "requisition_log.*";
        $data['where_in']['requisition_log.order_status'] = '2';
        $resultData = $this->rows($data);

        $html = "";
        if (!empty($resultData)) :
            $i = 1;
            foreach ($resultData as $row) :
                $itemdata = json_decode($row->item_data);
                if (!empty($itemdata)) :
                    foreach ($itemdata as $item) :
                        $item_name = '';
                        $item_type = '';
                        $req_qty = '';
                        if ($i == 1) {
                            $item_name = $item->req_item_name;
                            $req_qty = $item->req_qty;
                        } else {
                            $item_name .= '<br>' . $item->req_item_name;
                            $req_qty .= '<br>' . $item->req_qty;
                        }
                        $html .= '<tr>
                                    <td class="text-center">
                                        <input type="checkbox" id="md_checkbox_' . $i . '" name="pr_id[]" class="filled-in chk-col-success" value="' . $row->id . '"  ><label for="md_checkbox_' . $i . '" class="mr-3"></label>
                                        
                                    </td>
                                    <td class="text-center">' . $item_name . '</td>
                                    <td class="text-center">' . $req_qty . '</td>
                                </tr>';
                        $i++;
                    endforeach;
                endif;
            endforeach;
        else :
            $html = '<tr><td class="text-center" colspan="3">No Data Found</td></tr>';
        endif;
        return ['status' => 1, 'htmlData' => $html, 'result' => $resultData];
    }

    public function createPurchaseOrder($data)
    {
        if (!empty($data)) : //print_r($data['pr_id']);exit;
            $senddata = array();
            foreach ($data['pr_id'] as $key => $value) :
                $data['tableName'] = $this->requisitionLog;
                $data['select'] = "requisition_log.*";
                $data['where']['requisition_log.id'] = $value;
                $prdata = $this->row($data);

                $result = array();
                $itemData = json_decode($prdata->item_data);
                if (!empty($itemData)) :
                    foreach ($itemData as $item) :

                        $qryData = array();
                        $qryData['tableName'] = 'item_master';
                        $qryData['select'] = "item_master.item_name,item_master.item_code,item_master.item_type,item_master.gst_per,item_master.price,item_master.unit_id,item_master.hsn_code,item_master.description,item_master.item_make, unit_master.unit_name";
                        $qryData['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
                        $qryData['where']['item_master.id'] = $item->req_item_id;
                        $result = $this->row($qryData);

                        $result->fgitem_name = (!empty($result->fgitem_id)) ? $this->item->getItem($result->fgitem_id)->item_name : "";
                        $result->igst = $result->gst_per;
                        $result->sgst = $result->cgst = round(($result->gst_per / 2), 2);
                        $result->igst_amt = $result->sgst_amt = $result->cgst_amt = $result->amount = $result->net_amount = 0;
                        $result->disc_per = $result->disc_amt = 0;
                        $result->delivery_date = date('Y-m-d');
                        $result->amount = round(($item->req_qty * $result->price), 2);
                        if ($result->gst_per > 0) :
                            $result->igst_amt = round((($result->amount * $result->gst_per) / 100), 2);
                            $result->sgst_amt = $result->cgst_amt = round(($result->igst_amt / 2));
                        endif;
                        $result->item_id = $item->req_item_id;
                        $result->qty = $item->req_qty;
                        $result->fgitem_id = 0;
                        $result->fgitem_name = '';
                        unset($item->req_item_id, $item->req_qty);

                        $senddata[] = $result;
                    endforeach;
                endif;
            endforeach;

            return $senddata;
        endif;
    }

    public function getPurchasePlanningType()
    {
        $data['tableName'] = $this->purchase_planning_type;
        return $this->rows($data);
    }

    public function getItemStockData($item_id)
    {

        $itmData = $this->item->getItem($item_id);
        $stockData = $this->store->getItemStock($item_id);
        $freshStock = $this->store->getItemStock($item_id,'FRESH');
        $usedStock = $this->store->getItemStock($item_id,'USED');
        $wip_qty = 0;
        /* Finished Good WIP Stock */
        if ($itmData->item_type == 1) {
            $data = array();
            $data['tableName'] = "job_card";
            $data['select'] = "SUM(job_card.qty-job_card.stored_qty) as wip_qty";
            $data['where']['product_id'] = $item_id;
            $data['where']['job_card.order_status'] = 2;
            $resultData = $this->row($data);
            $wip_qty = (!empty($resultData->wip_qty)) ? $resultData->wip_qty : 0;
        }
        /* ROW Material WIP Stock */
        if ($itmData->item_type == 3) {
            $data = array();
            $data['tableName'] = "job_bom";
            $data['select'] = "SUM(job_bom.dispatch_qty) as wip_qty";
            $data['leftJoin']['job_card'] = "job_card.id=job_bom.job_card_id";
            $data['where']['job_card.order_status'] = 2;
            $data['where']['job_bom.ref_item_id'] = $item_id;
            $resultData = $this->row($data);
            $wip_qty = (!empty($resultData->wip_qty)) ? $resultData->wip_qty : 0;
        }

        /* Pending Purchase Order  */
        $po_qty = 0;

        $data = array();
        $data['tableName'] = "purchase_order_trans";
        $data['select'] = "SUM(purchase_order_trans.qty-purchase_order_trans.rec_qty) as po_qty";
        $data['where']['item_id'] = $item_id;
        $data['where']['purchase_order_trans.order_status'] = 0;
        $resultData = $this->row($data);
        $po_qty = (!empty($resultData->po_qty)) ? $resultData->po_qty : 0;


        $pi_pending_qty = 0;

        $data = array();
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "SUM(req_qty) as pi_pending_qty";
        $data['where']['req_item_id'] = $item_id;
        $data['where']['requisition_log.order_status'] = 0;
        $data['where']['requisition_log.log_type'] = 3;
        $resultData = $this->row($data);
        $pi_pending_qty = (!empty($resultData->pi_pending_qty)) ? $resultData->pi_pending_qty : 0;


        $pi_qty = 0;

        $data = array();
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "SUM(req_qty) as pi_qty";
        $data['where']['req_item_id'] = $item_id;
        $data['where']['requisition_log.order_status'] = 2;
        $data['where']['requisition_log.log_type'] = 3;
        $resultData = $this->row($data);
        $pi_qty = (!empty($resultData->pi_qty)) ? $resultData->pi_qty : 0;

        /*** Pending Requisition Qty */
        $requisitionQty = 0;
        $data = array();
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "SUM(req_qty) as req_qty";
        $data['where']['req_item_id'] = $item_id;
        $data['where']['requisition_log.order_status'] = 0;
        $data['where']['requisition_log.log_type'] = 1;
        $resultData = $this->row($data);
        $requisitionQty = (!empty($resultData->req_qty)) ? $resultData->req_qty : 0;

        /*** JObcard Allocated Qty */
        $allocatedQty = 0;
        $data = array();
        $data['tableName'] = "stock_transaction";
        $data['select'] = "SUM(stock_transaction.qty) as allocated_qty";
        $data['where']['item_id'] = $item_id;
        $data['where']['stock_transaction.location_id'] = $this->RM_ALLOT_STORE->id;
        $resultData = $this->row($data);
        $allocatedQty = (!empty($resultData->allocated_qty)) ? $resultData->allocated_qty : 0;

        $current_stock = (!empty($stockData->qty))?$stockData->qty:0;
        $fresh_stock = (!empty($freshStock->qty))?$freshStock->qty:0;
        $used_stock = (!empty($usedStock->qty))?$usedStock->qty:0;
        /*$html = '<tr>
        <td id="current_stock"><input type="hidden" name="current_stock" id="current_stock_val" value="' .$current_stock . '">' . $fresh_stock . ' ' . $itmData->unit_name . '</td>
        <!--<td id="used_stock">' . $used_stock . ' ' . $itmData->unit_name . '</td>-->
        <td id="wip_stock"><input type="hidden" name="wip_stock"  value="' . $wip_qty . '">' . $wip_qty . ' ' . $itmData->unit_name . '</td>
        <td id="job_allocated_stock"><input type="hidden" name="job_allocated_stock"  value="' . $allocatedQty . '">' . $allocatedQty . ' ' . $itmData->unit_name . '</td>
        <td id="pending_po_stock"><input type="hidden" name="pending_po_stock"  value="' . $po_qty . '">' . $po_qty . ' ' . $itmData->unit_name . '</td>
        <td id="pending_req_stock"><input type="hidden" name="pending_req_stock"  value="' . $requisitionQty . '">' . floatVal($requisitionQty) . ' ' . $itmData->unit_name . '</td>
        <td id="pending_indent_stock"><input type="hidden" name="pending_indent_stock"  value="' . $pi_pending_qty . '">' . $pi_pending_qty . ' ' . $itmData->unit_name . '</td>
        <td id="pending_indent_apr_stk"><input type="hidden" name="pending_indent_apr_stk"  value="' . $pi_qty . '">' . $pi_qty . ' ' . $itmData->unit_name . '</td>
        <td>' . ($itmData->qty + $wip_qty + $po_qty + $pi_pending_qty + $pi_qty + $requisitionQty + $allocatedQty) . '</td>
        </tr>';*/

        $html = '<tr>
        <td id="current_stock">' . $fresh_stock . ' ' . $itmData->unit_name . '</td>
        <td id="wip_stock">' . $wip_qty . ' ' . $itmData->unit_name . '</td>
        <td id="job_allocated_stock">' . $allocatedQty . ' ' . $itmData->unit_name . '</td>
        <td id="pending_po_stock">' . $po_qty . ' ' . $itmData->unit_name . '</td>
        <td id="pending_req_stock">' . floatVal($requisitionQty) . ' ' . $itmData->unit_name . '</td>
        <td id="pending_indent_stock">' . $pi_pending_qty . ' ' . $itmData->unit_name . '</td>
        <td id="pending_indent_apr_stk">' . $pi_qty . ' ' . $itmData->unit_name . '</td>
        <td>' . ($itmData->qty + $wip_qty + $po_qty + $pi_pending_qty + $pi_qty + $requisitionQty + $allocatedQty) . '</td>
        </tr>';

        $data = array();
        $authDetail = '<div class="col-md-12 form-group bg-orange" style="border:1px solid #000000;padding:5px 7px;" >Authorised By : ';
        $data['tableName'] = "item_category";
        $data['select'] = "employee_master.emp_code, employee_master.emp_name";
        $data['leftJoin']['item_master'] = 'item_master.category_id = item_category.id';
        $data['leftJoin']['employee_master'] = 'FIND_IN_SET(employee_master.id, item_category.auth_detail)';
        $data['where']['item_master.id'] = $item_id;;
        $authData = $this->rows($data);
        if (!empty($authData)) {
            $auths = array();
            foreach ($authData as $row) {
                $auths[] = '[' . $row->emp_code . '] ' . $row->emp_name;
            };
            $authDetail = '<div class="col-md-12 form-group bg-light-info" style="border:1px solid #000000;padding:5px 7px;"><b>Authorised By : </b>';
            $authDetail .= implode(', ', $auths) . '</div>';
        }

        return ["status" => 1, "html" => $html, "authDetail" => $authDetail];
    }

    public function approveRequisition($data)
    {
        $this->store($this->requisitionLog, ['id' => $data['id'], 'approved_by' => $this->loginId]);
        return ['status' => 1, 'message' => 'Requisition ' . $data['msg'] . ' successfully.'];
    }

    public function deleteRequisition($id)
    {
        return $this->trash($this->requisitionLog, ['id' => $id], 'Requisition');
    }

    public function nextRequisitionNo()
    {
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "MAX(log_no) as req_no";
        $data['where']['requisition_log.log_type'] = 1;
        $maxNo = $this->specificRow($data)->req_no;
        $nextReqNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextReqNo;
    }

    public function savePurchaseRequest($data)
    {
        $result = $this->store($this->requisitionLog, $data);
        return $result;
    }

    public function saveReturnMaterial($data)
    {
        try {
            $this->db->trans_begin();
            $issueLog = $this->issueRequisition->getIssueMaterialLog($data['ref_id']);
            $qty = 0;
            foreach ($data['used_qty'] as $key => $value) {
                $qty += ($value + $data['fresh_qty'][$key] + $data['missed_qty'][$key] + $data['broken_qty'][$key] + $data['scrap_qty'][$key]+$data['regranding_qty'][$key]);
                $returnData = [
                    'id' => '',
                    'item_id' => $issueLog->req_item_id,
                    'ref_id' => $data['ref_id'],
                    'batch_no' => $data['batch_no'],
                    'qty' => $qty,
                    'used_qty' => $value,
                    'fresh_qty' => $data['fresh_qty'][$key],
                    'missed_qty' => $data['missed_qty'][$key],
                    'broken_qty' => $data['broken_qty'][$key],
                    'scrap_qty' => $data['scrap_qty'][$key],
                    'regranding_qty' => $data['regranding_qty'][$key],
                    'trans_date' => $data['trans_date'],
                    'trans_type' => $data['trans_type'],
                    'size' => $data['size'],
                    //'return_status' => $data['return_status'][$key],
                    'reason' => $data['reason'][$key],
                    'created_by' => $data['created_by'],
                ];
                $this->store($this->materialReturn, $returnData);  
            }

            $reqData = $this->issueRequisition->getIssueMaterialLog($issueLog->ref_id);
            $empData = $this->employee->getEmp($reqData->created_by);

            $strQuery['tableName'] = "location_master";
            $strQuery['where']['other_ref'] = $empData->emp_dept_id;
            $strQuery['where']['store_type'] = 2;
            $strResult = $this->row($strQuery);
            $stockMinusQuery = [
                'id' => '',
                'item_id' => $issueLog->req_item_id,
                'ref_id' => $data['ref_id'],
                'ref_type' => 17,
                'location_id' => $strResult->id,
                'batch_no' => $data['batch_no'],
                'trans_type' => 2,
                'qty' => '-' . $qty,
                'ref_id' => $data['ref_id'],
                'size' => $data['size'],
                'ref_no' => $issueLog->log_no,
                'stock_effect'=>0,
                'created_by'=>$this->session->userdata('loginId')
            ];

            $result = $this->store($this->stockTransaction, $stockMinusQuery);
            $stockPlusQuery = [
                'id' => '',
                'item_id' => $issueLog->req_item_id,
                'ref_id' => $data['ref_id'],
                'ref_type' => 17,
                'location_id' => $this->INSP_STORE->id,
                'batch_no' => $data['batch_no'],
                'trans_type' => 1,
                'qty' => $qty,
                'ref_id' => $data['ref_id'],
                'trans_ref_id' => $result['insert_id'],
                'size' => $data['size'],
                'stock_effect'=>0,
                'created_by'=>$this->session->userdata('loginId')
                
            ];

            $result = $this->store($this->stockTransaction, $stockPlusQuery);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getReturnTransaction($id)
    {
        $data['tableName'] = $this->materialReturn;
        $data['where']['material_return.trans_type'] = 0;
        $data['where']['material_return.ref_id'] = $id;
        $result = $this->rows($data);
        return $result;
    }

    public function deleteReturn($id)
    {

        $data['tableName'] = $this->materialReturn;
        $data['where']['material_return.id'] = $id;
        $returnData = $this->row($data);
        if ($returnData->return_status != 3 && $returnData->return_status != 4) {
            $issueLog = $this->issueRequisition->getIssueMaterialLog($returnData->ref_id);
            $reqData = $this->issueRequisition->getIssueMaterialLog($issueLog->ref_id);
            $empData = $this->employee->getEmp($reqData->created_by);

            $strQuery['tableName'] = "location_master";
            $strQuery['where']['other_ref'] = $empData->emp_dept_id;
            $strQuery['where']['store_type'] = 102;
            $strResult = $this->row($strQuery);

            $stockMinusQuery = [
                'id' => '',
                'item_id' => $issueLog->req_item_id,
                'ref_id' => $returnData->ref_id,
                'ref_type' => 17,
                'location_id' => $this->INSP_STORE->id,
                'trans_type' => 2,
                'qty' => '-' . $returnData->qty,
                'ref_no' => $issueLog->log_no
            ];

            $result = $this->store($this->stockTransaction, $stockMinusQuery);
            $stockPlusQuery = [
                'id' => '',
                'item_id' => $issueLog->req_item_id,
                'ref_type' => 17,
                'location_id' => $strResult->id,
                'trans_type' => 1,
                'qty' => $returnData->qty,
                'ref_id' => $returnData->ref_id,
            ];

            $this->store($this->stockTransaction, $stockPlusQuery);
        }
        $result = $this->trash($this->materialReturn, ['id' => $id]);
        return $result;
    }

    public function getReturnStock($item_id)
    {

        $data['tableName'] = $this->stockTransaction;
        $data['select'] = 'stock_transaction.*,requisition_log.log_no,requisition_log.req_date,issue.id as req_id';
        $data['leftJoin']['requisition_log'] = 'stock_transaction.ref_id=requisition_log.id';
        $data['leftJoin']['requisition_log as issue'] = 'issue.ref_id=requisition_log.id';

        $data['where']['stock_transaction.item_id'] = $item_id;
        $data['where']['stock_transaction.location_id'] = $this->LOGIN_STORE->id;
        $data['where']['stock_transaction.ref_type'] = 16;
        $result = $this->rows($data);
        return $result;
    }

    //Created By Karmi @06/05/2022 For Reject Requisition

    public function rejectRequisition($data)
    {
        $this->store($this->requisitionLog, ['id' => $data['id'], 'order_status' => $data['val'], 'approved_by' => $this->loginId, 'approved_at' => date("Y-m-d H:i:s")]);
        return ['status' => 1, 'message' => 'Requisition ' . $data['msg'] . ' successfully.'];
    }
}
