<?php
class PurchaseIndentModel extends MasterModel
{
    private $requisition_log = "requisition_log";
    private $purchase_planning_type = "purchase_planning_type";

    public function getDTRows($data)
    {
        
        $data['tableName'] = $this->requisition_log;
        $data['select'] = "requisition_log.*,item_master.item_name,item_master.full_name,item_master.description,item_master.make_brand,unit_master.unit_name,item_master.min_qty,item_master.max_qty,item_master.lead_time,purchase_planning_type.planning_type as plan_type,employee_master.emp_name";
        $data['leftJoin']['item_master'] = "item_master.id = requisition_log.req_item_id";
        $data['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
        $data['leftJoin']['purchase_planning_type'] = "purchase_planning_type.id = requisition_log.planning_type";
        $data['leftJoin']['requisition_log as requisition'] = "requisition.id = requisition_log.ref_id";
        $data['leftJoin']['employee_master'] = "requisition.approved_by = employee_master.id";
        $data['where']['requisition_log.log_type'] = 3;
        if (!empty($data['status'])) {
            if ($data['status'] == 3) { $data['where']['requisition_log.order_status'] = 3; }
            if ($data['status'] == 2) { $data['where']['requisition_log.order_status'] = 2; }
            if ($data['status'] == 1) { $data['where']['requisition_log.order_status'] = 1;  }
            if ($data['status'] == 0) { $data['where_in']['requisition_log.order_status'] = '0'; }
        } else {
            $data['where_in']['requisition_log.order_status'] = [0,2];
        }
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(requisition_log.req_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT('IND',LPAD(requisition_log.log_no, 5, '0'))";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "unit_master.unit_name";
        $data['searchCol'][] = "item_master.lead_time";
        $data['searchCol'][] = "item_master.min_qty";
        $data['searchCol'][] = "item_master.max_qty";
        $data['searchCol'][] = "requisition_log.req_qty";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(requisition_log.delivery_date,'%d-%m-%Y')";
        $data['searchCol'][] = "purchase_planning_type.planning_type";
        $data['searchCol'][] = "requisition_log.remark";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "";


        $columns = array('','','requisition_log.req_date', 'requisition_log.log_no', 'item_master.full_name', 'unit_master.unit_name', 'item_master.lead_time', 'item_master.min_qty', 'item_master.max_qty', 'requisition_log.req_qty', '', 'requisition_log.delivery_date', 'purchase_planning_type.planning_type', 'requisition_log.remark', 'employee_master.emp_name', '');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }

    public function getPurchaseIndent($id)
    {
        $data['tableName'] = $this->requisition_log;
        $data['select'] = "requisition_log.*,item_master.item_name,item_master.item_code,item_master.gst_per,item_master.item_type,item_master.price,item_master.unit_id,item_master.hsn_code,item_master.full_name, unit_master.unit_name,purchase_planning_type.planning_type as planningTypeName";
        $data['leftJoin']['item_master'] = "item_master.id = requisition_log.req_item_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['leftJoin']['purchase_planning_type'] = "purchase_planning_type.id = requisition_log.planning_type";
        $data['where']['requisition_log.id'] = $id;
        $result = $this->row($data);

        $result->fgitem_name = (!empty($result->fgitem_id)) ? $this->item->getItem($result->fgitem_id)->item_name : "";
        $result->igst = $result->gst_per;
        $result->sgst = $result->cgst = round(($result->gst_per / 2), 2);
        $result->igst_amt = $result->sgst_amt = $result->cgst_amt = $result->amount = $result->net_amount = 0;
        $result->disc_per = $result->disc_amt = 0;
        $result->delivery_date = date('Y-m-d');
        $result->amount = round(($result->req_qty * $result->price), 2);
        if ($result->gst_per > 0) :
            $result->igst_amt = round((($result->amount * $result->gst_per) / 100), 2);
            $result->sgst_amt = $result->cgst_amt = round(($result->igst_amt / 2));
        endif;
        $result->item_id = $result->req_item_id;
        $result->qty = $result->req_qty;
        // unset($result->req_item_id,$result->req_qty);

        return $result;
    }

    public function getPurchaseRequestForOrder($id)
    {
        $data['tableName'] = $this->requisition_log;
        $data['select'] = "requisition_log.*,item_master.full_name,item_master.item_name,item_master.item_code,item_master.gst_per,item_master.price,item_master.unit_id,item_master.hsn_code, unit_master.unit_name";//,job_card.product_id as fgitem_id";
        $data['leftJoin']['item_master'] = "item_master.id = requisition_log.req_item_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        //$data['leftJoin']['job_card'] = "requisition_log.job_card_id = job_card.id";
        $data['where_in']['requisition_log.id'] = str_replace("~", ",", $id);
        $result = $this->rows($data);
        return $result;
    }

    public function getPurchaseReqForEnq($id)
    {
        $data['tableName'] = $this->requisition_log;
        $data['select'] = "requisition_log.id,requisition_log.req_item_id,requisition_log.req_qty,item_master.item_name,item_master.item_type,item_master.item_code,item_master.item_type,item_master.price,item_master.unit_id, unit_master.unit_name,";
        $data['leftJoin']['item_master'] = "item_master.id = requisition_log.req_item_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['where']['requisition_log.id'] = $id;
        $result = $this->rows($data);


        return $result;
    }

    public function approvePreq($data)
    {
        $this->store($this->requisition_log, ['id' => $data['id'], 'order_status' => $data['val']]);
        return ['status' => 1, 'message' => 'Purchase Order ' . $data['msg'] . ' successfully.'];
    }

    public function closePreq($data)
    {
        $this->store($this->requisition_log, ['id' => $data['id'], 'order_status' => $data['val']]);
        return ['status' => 1, 'message' => 'Purchase Order ' . $data['msg'] . ' successfully.'];
    }

    /*  Change By : Avruti @7-12-2021 04:00 PM
        update by : 
        note : Sales Enquiry No
    */
    public function getPurchaseOrder()
    {
        $data['tableName'] = $this->requisition_log;
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
                $data['tableName'] = $this->requisition_log;
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
            $data['tableName'] = "job_material_dispatch";
            $data['select'] = "SUM(job_material_dispatch.dispatch_qty) as wip_qty";
            $data['leftJoin']['job_card'] = "job_card.id=job_material_dispatch.job_card_id";
            $data['where']['job_card.order_status'] = 2;
            $data['where']['job_material_dispatch.dispatch_item_id'] = $item_id;
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
        $data['tableName'] = "purchase_request";
        $data['select'] = "SUM(req_qty) as pi_pending_qty";
        $data['where']['req_item_id'] = $item_id;
        $data['where']['purchase_request.order_status'] = 0;
        $resultData = $this->row($data);
        $pi_pending_qty = (!empty($resultData->pi_pending_qty)) ? $resultData->pi_pending_qty : 0;


        $pi_qty = 0;

        $data = array();
        $data['tableName'] = "purchase_request";
        $data['select'] = "SUM(req_qty) as pi_qty";
        $data['where']['req_item_id'] = $item_id;
        $data['where']['purchase_request.order_status'] = 2;
        $resultData = $this->row($data);
        $pi_qty = (!empty($resultData->pi_qty)) ? $resultData->pi_qty : 0;

        $html = '<tr>
        <td id="current_stock"><input type="hidden" name="current_stock" value="' . $itmData->qty . '">' . $itmData->qty . ' ' . $itmData->unit_name . '</td>
        <td id="wip_stock"><input type="hidden" name="wip_stock"  value="' . $wip_qty . '">' . $wip_qty . ' ' . $itmData->unit_name . '</td>
        <td id="pending_po_stock"><input type="hidden" name="pending_po_stock"  value="' . $po_qty . '">' . $po_qty . ' ' . $itmData->unit_name . '</td>
        <td id="pending_indent_stock"><input type="hidden" name="pending_indent_stock"  value="' . $pi_pending_qty . '">' . $pi_pending_qty . ' ' . $itmData->unit_name . '</td>
        <td id="pending_indent_apr_stk"><input type="hidden" name="pending_indent_apr_stk"  value="' . $pi_qty . '">' . $pi_qty . ' ' . $itmData->unit_name . '</td>
        </tr>';

        return ["status" => 1, "html" => $html];
    }
    public function save($data)
    {
        $result = $this->store($this->requisition_log, $data);
        if (!empty($data['approved_by'])) {
            $this->approvePreq(['id' => $data['id'], 'val' => 2, 'msg' => 'Approved and Save Successfully']);
        }
        return $result;
    }

    public function nextIndentNo()
    {
        $data['tableName'] = $this->requisition_log;
        $data['select'] = "MAX(log_no) as indent_no";
        $data['where']['requisition_log.log_type'] = 3;
        $maxNo = $this->specificRow($data)->indent_no;
        $nextIndentNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextIndentNo;
    }
}
