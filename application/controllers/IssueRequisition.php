<?php
class IssueRequisition extends MY_Controller
{
    private $indexPage = "issue_requisition/index";
    private $form = "issue_requisition/form";
    private $indentIndex = "issue_requisition/indent_index";
    private $indentForm = "purchase_indent/form";
    private $otherDetail = "send_pr/other_detail";
    private $materialAllocated = "issue_requisition/material_allocated";
    private $materialIssue = "issue_requisition/material_issue";

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Issue Requisition";
        $this->data['headData']->controller = "issueRequisition";
        $this->data['headData']->pageUrl = "issueRequisition";
    }

    public function index(){
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->data['mType'] = '1~2~3~4~5';
        $this->load->view($this->indexPage, $this->data);
    }

    public function qcIndex(){
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->data['mType'] = '6~7';
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($mType = '',$due_status = 0)
    {
        $data = $this->input->post();
        $data['status'] = 0;
        $data['due_status'] = $due_status;
        $data['mType'] = (!empty($mType))? str_replace('~',',',$mType) : '';
        $result = $this->issueRequisition->getDTRows($data);
        $sendData = array();
        $i = 1;
        $count = 0;
        foreach ($result['data'] as $row) :

            $row->sr_no = $i++;
            $row->allot_qty = $this->issueRequisition->getIssueMaterialData($row->id)->req_qty;
            $row->issue_qty = $this->issueRequisition->getAllotMaterialData($row->id)->req_qty;
            $row->indent_qty = $this->issueRequisition->getIndentMaterialData($row->id)->req_qty;
            $row->issue_date = $this->issueRequisition->getMaxIssueDate($row->id)->req_date;
            $issueData = $this->issueRequisition->getMaxIssueDate($row->id);
            $row->issue_date = $issueData->req_date;
            $row->issue_no =  $issueData->req_no_max;
            if ($row->used_at == 0) {
                $row->whom_to_handover = (!empty($row->handover_to)) ? $this->employee->getEmp($row->handover_to)->emp_name : '';
            } else {
                $row->whom_to_handover = (!empty($row->handover_to)) ? $this->party->getParty($row->handover_to)->party_name : '';
            }
            $row->approved_by = (!empty($row->approved_by) ? $this->employee->getEmp($row->approved_by)->emp_name : '');
            $pending_qty = $row->req_qty - (!empty($row->allot_qty) ? $row->allot_qty : 0) - (!empty($row->issue_qty) ? $row->issue_qty : 0);
            $row->priority_label = '<span class="badge badge-pill badge-info m-1">Low</span>';
            if ($row->urgency == 1) :
                $row->priority_label = '<span class="badge badge-pill badge-warning m-1">Medium</span>';
            endif;
            if ($row->urgency == 2) :
                $row->priority_label = '<span class="badge badge-pill badge-danger m-1">High</span>';
            endif;
            if ($pending_qty > 0) {
                if ($row->order_status == 0) :
                    $row->order_status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
                elseif ($row->order_status == 1) :
                    $row->order_status_label = '<span class="badge badge-pill badge-success m-1">Complete</span>';
                elseif ($row->order_status == 2) :
                    $row->order_status_label = '<span class="badge badge-pill badge-info m-1">Accepted</span>';
                elseif ($row->order_status == 3) :
                    $row->order_status_label = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
                endif;
                $row->controller = "issueRequisition";
                $sendData[] = getRequisitionIssueData($row);
            }
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function issueMaterial()
    {
        $ref_id = $this->input->post('ref_id');
        $id = $this->input->post('id');
        $dispatchData = $this->purchaseRequest->getPurchaseRequest($ref_id);
        $this->data['jobCardData'] = $this->jobcard->getJobcardList();
        $this->data['processList'] = $this->process->getProcessList();
        $whereToUse = $this->item->getItem($dispatchData->fg_item_id);
        $machine = $this->item->getItem($dispatchData->machine_id);

        $this->data['itemData'] = $this->item->getItemList();
        $this->data['dataRow'] = $dispatchData;
        $this->data['dataRow']->where_to_use = !empty($whereToUse->full_name) ? $whereToUse->full_name : '';
        $this->data['dataRow']->machine = !empty($machine->item_name) ? $machine->item_name : '';
        $this->data['empData'] = $this->employee->getEmpList();
        $this->data['partyData'] = $this->party->getVendorList();
        
        $used_type = ($dispatchData->req_type == 1) ? 'FRESH' : 'USED';
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['dataRow']->ref_id = $ref_id;
        $this->data['dataRow']->id = $id;
        $this->data['dataRow']->used_at = $dispatchData->used_at;
        $issueData = $this->issueRequisition->getIssueMaterialData($ref_id);
        $this->data['issueData'] = $issueData;
        $this->data['allotData'] = $this->issueRequisition->getAllotMaterialData($ref_id);
        $batch_no = !empty($issueData->batch_no) ? $issueData->batch_no : '';
        $location_id = !empty($issueData->location_id) ? $issueData->location_id : '';
        $batch_qty = !empty($issueData->batch_qty) ? $issueData->batch_qty : '';
        $this->data['batchWiseStock'] = $this->issueRequisition->batchWiseItemStock(['item_id' => $dispatchData->req_item_id, 'batch_no' => $batch_no, 'location_id' => $location_id, 'batch_qty' => $batch_qty, 'trans_id' => $id, "req_type" => $used_type]);
        //print_r($this->data['batchWiseStock']); exit;
        $this->load->view($this->form, $this->data);
    }

    public function save()
    {
        $data = $this->input->post(); 
        if (empty($data['handover_to']))
             $errorMessage['handover_to'] = "Whom to Handover is required.";
        $newQty = array_sum($data['batch_quantity']);
        if ($newQty<=0)
             $errorMessage['batch_qty'] = "Qty is required.";
        if ($newQty > $data['pending_issue'])
             $errorMessage['batch_qty'] = "Invalid Qty";
             
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $issueData = $this->purchaseRequest->getPurchaseRequest($data['ref_id']);
            $data['reqn_type'] = $issueData->reqn_type;
            $data['req_type'] = $issueData->req_type;
            $data['planning_type'] = $issueData->planning_type;
            $data['delivery_date'] = $issueData->delivery_date;
            $data['current_stock'] = $issueData->current_stock;
            $data['urgency'] = $issueData->urgency;
            $data['fg_item_id'] = $issueData->fg_item_id;
            $data['machine_id'] = $issueData->machine_id;
            $data['used_at'] = $issueData->used_at;
            $data['auth_detail'] = $issueData->auth_detail;
            $data['approved_by'] = $issueData->approved_by;
            $data['approved_at'] = $issueData->approved_at;
            
            $data['created_by'] = $this->session->userdata('loginId');
            if($data['issue_type']==2){
                $data['approved_by'] = $this->session->userdata('loginId');
            }

            $data['location_id'] = implode(",", $data['location']);
            $data['batch_no'] = implode(",", $data['batch_number']);
            $data['batch_qty'] = implode(",", $data['batch_quantity']);
            $data['stock_type'] = implode(",", $data['stock_type']);
            $data['size'] = implode(",", $data['size']);
            unset($data['location'], $data['batch_number'], $data['batch_quantity'],$data['pending_issue']);
            
            $result = $this->issueRequisition->save($data);
            // $issueId = $result['issue_id'];
           $this->printJson($result);
        endif;
    }

    public function generateIndent()
    {
        $data = $this->input->post();//print_r($data);exit;
        $this->data['itemData'] = $this->item->getItemLists();
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['planningType'] = $this->purchaseRequest->getPurchasePlanningType();
        if(!empty($data['ref_id'])){
        $dataRow = $this->purchaseRequest->getPurchaseRequest($data['ref_id']);
        $itemData = $this->item->getItem($dataRow->req_item_id);
        $this->data['dataRow'] = new stdClass();

        $this->data['dataRow']->unit_id = $itemData->unit_id;
        $this->data['dataRow']->unit_name = $itemData->unit_name;
        $this->data['dataRow']->req_item_id = $itemData->id;
        $this->data['dataRow']->item_description = $itemData->description;
        $this->data['dataRow']->min_qty = $itemData->min_qty;
        $this->data['dataRow']->max_qty = $itemData->max_qty;
        $this->data['dataRow']->make_brand = $itemData->make_brand;
        $this->data['dataRow']->lead_time = $itemData->lead_time;
        $this->data['dataRow']->ref_id = $data['ref_id'];
        $this->data['dataRow']->item_type = $itemData->item_type;
        $this->data['dataRow']->category_id = $itemData->category_id;
        $this->data['dataRow']->family_id = $itemData->family_id;
        $this->data['dataRow']->auth_detail = $dataRow->auth_detail;
        $this->data['dataRow']->used_at = $dataRow->used_at;
        $this->data['dataRow']->handover_to = $dataRow->handover_to;
        $this->data['dataRow']->fg_item_id = $dataRow->fg_item_id;
        $this->data['dataRow']->is_returnable = $dataRow->is_returnable;
        $this->data['dataRow']->reqn_type = $dataRow->reqn_type;
        $this->data['dataRow']->req_from = $dataRow->req_from;
        }
        if (!empty($data['id'])) {
            $this->data['dataRow'] = $this->purchaseIndent->getPurchaseIndent($data['id']);
            $this->data['stockData'] = $this->purchaseRequest->getItemStockData($this->data['dataRow']->req_item_id);
        } else {

            $this->data['indentNo'] = $this->purchaseIndent->nextIndentNo();
            $this->data['stockData'] = $this->purchaseRequest->getItemStockData($dataRow->req_item_id);
        }

        $this->data['fgNMcData'] = $this->item->getItemLists('1,5');
        $this->data['empData'] = $this->employee->getEmpList();
        $this->data['partyData'] = $this->party->getVendorList();
        $this->data['loginId'] = $this->session->userdata('loginId');

        $this->data['itemTypeList'] = $this->itemCategory->mainCategoryList();
        $this->data['categoryList'] = $this->itemCategory->getCategoryList();
        $this->data['familyGroup'] = $this->item->getfamilyGroupList();
        // print_r($this->data['fgNMcData']);
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['locationData'] = $this->store->getStoreLocationWithoutProcess();
        $this->data['approve_type'] = (!empty($data['approve_type']) ? $data['approve_type'] : '');

        $this->load->view($this->indentForm, $this->data);
    }

    public function savePurchaseIndent()
    {
        $data = $this->input->post();
        // print_r($data);exit;
        $errorMessage = array();

        if (empty($data['req_item_id']))
            $errorMessage['req_item_id'] = "Item Name is required.";

        if (empty($data['schedule_qty']))
            $errorMessage['req_qty'] = "Qty. is required.";
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :

            $data['req_qty'] = array_sum(explode(',', $data['schedule_qty']));

            if ($data['approve_type'] == 1) {
                $data['approved_by'] = $this->loginId;
                $data['approved_at'] = date("Y-m-d H:i:s");
            } else {
                $data['created_by']  = $this->session->userdata('loginId');
            }
            unset($data['approve_type']);
            // $data['log_type']=2;
            $this->printJson($this->purchaseIndent->save($data));
        endif;
    }

    public function purchaseIndent()
    {
        $this->data['tableHeader'] = getStoreDtHeader('purchaseIndent');
        $this->load->view($this->indentIndex, $this->data);
    }

    public function getDTIndentRows($status = '')
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->purchaseIndent->getDTRows($data);
        $sendData = array();
        $i = 1;
        $count = 0;

        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;

            $qtyArray = (!empty($row->schedule_qty)) ? explode(",", $row->schedule_qty) : '';
            $dateArray = (!empty($row->schedule_date)) ? explode(",", $row->schedule_date) : '';

            $qtyStr = "";
            if (!empty($qtyArray)) :
                for ($j = 0; $j < count($qtyArray); $j++) :
                    if ($j != 0) {
                        $qtyStr .= ", ";
                    }
                    $qtyStr .= $qtyArray[$j] . '[' . $dateArray[$j] . ']';
                endfor;
            endif;
            $row->sc_qty = $qtyStr;

            $authArr = explode(",", $row->auth_detail);
            $row->approveFlag = (in_array($this->loginId, $authArr) ? 1 : '');
            if ($row->order_status == 0) :
                $row->order_status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
            elseif ($row->order_status == 1) :
                $row->order_status_label = '<span class="badge badge-pill badge-success m-1">Complete</span>';
            elseif ($row->order_status == 2) :
                $row->order_status_label = '<span class="badge badge-pill badge-info m-1">Accepted</span>';
            elseif ($row->order_status == 3) :
                $row->order_status_label = '<span class="badge badge-pill badge-dark m-1">Reject</span>';
            endif;

            $sendData[] = getPurchaseIndentDataForApproval($row);
        endforeach;
        $result['data'] = $sendData;
        // print_r($this->db->last_query());
        $this->printJson($result);
    }

    public function approvePreq()
    {
        $data = $this->input->post();

        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Something went wrong...Please try again.']);
        else :
            $this->printJson($this->purchaseIndent->approvePreq($data));
        endif;
    }

    public function viewPurchaseReq()
    {
        $data = $this->input->post();
        $this->data['dataRow'] = $this->purchaseIndent->getPurchaseIndent($data['id']);
        $itemData = $this->item->getItem($this->data['dataRow']->req_item_id);
        $this->data['dataRow']->unit_id = $itemData->unit_id;
        $this->data['dataRow']->item_description = $itemData->description;
        $this->data['dataRow']->min_qty = $itemData->min_qty;
        $this->data['dataRow']->max_qty = $itemData->max_qty;
        $this->data['dataRow']->make_brand = $itemData->make_brand;
        $this->data['dataRow']->lead_time = $itemData->lead_time;
        $this->data['dataRow']->drawing_no = $itemData->drawing_no;
        $this->data['dataRow']->item_image = $itemData->item_image;
        $this->load->view("purchase_request/purchase_req_view", $this->data);
    }

    public function getOtherDetail()
    {
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->purchaseRequest->getEntryLog($id, 'requisition_log');
        $this->load->view($this->otherDetail, $this->data);
    }

    public function getHandoverData()
    {
        $used_at = $this->input->post('used_at');
        $handover = '<option value="">Select</option>';
        if (empty($used_at)) :
            $empData = $this->employee->getEmpList();
            foreach ($empData as $row) :
                // $selected = (empty($dataRow->handover_to) && !empty($this->loginId) && $this->loginId == $row->id) ? 'selected' : '';
                $handover .= "<option value='" . $row->id . "'   data-row='" . json_encode($row) . "'>[" . $row->emp_code . "] " . $row->emp_name . " </option>";
            endforeach;
        else :
            $partyData = $this->party->getVendorList();
            foreach ($partyData as $row) :
                $handover .= "<option value='" . $row->id . "' data-row='" . json_encode($row) . "'>[" . $row->party_code . "] " . $row->party_name . " </option>";
            endforeach;
        endif;

        $this->printJson(['status' => 1, 'handover' => $handover]);
    }

    public function getStoreLocation()
    {
        $data = $this->input->post();
        $used_type = ($data['req_type'] == 1) ? 'FRESH' : 'USED';
        $batchWiseStock = $this->issueRequisition->batchWiseItemStock(['item_id' => $data['item_id'], 'batch_no' => '', 'location_id' => '', 'batch_qty' => '', 'trans_id' => '', "req_type" => $used_type]);
        $this->printJson(['status' => 1, 'batchWiseStock' => $batchWiseStock['batchData']]);
    }

    public function materialIssueFrmAllot()
    {
        $data = $this->input->post();
        $data['approved_by'] = $this->session->userdata('loginId');
        $this->printJson($this->issueRequisition->materialIssueFrmAllot($data));
    }

    public function index2()
    {
        $this->data['tableHeader'] =  getStoreDtHeader('materialAllocated');
        $this->data['mType'] = '1~2~3~4~5';
        $this->load->view($this->materialAllocated, $this->data);
    }

    public function index3()
    {
        $this->data['tableHeader'] = getStoreDtHeader('materialIssue');
        $this->data['mType'] = '1~2~3~4~5';
        $this->load->view($this->materialIssue, $this->data);
    }
    
    public function qcIndex2()
    {
        $this->data['tableHeader'] =  getStoreDtHeader('materialAllocated');
        $this->data['mType'] = '6~7';
        $this->load->view($this->materialAllocated, $this->data);
    }

    public function qcIndex3()
    {
        $this->data['tableHeader'] = getStoreDtHeader('materialIssue');
        $this->data['mType'] = '6~7';
        $this->load->view($this->materialIssue, $this->data);
    }


    public function getDTAllocRows($mType = '', $due_status = 0)
    {
        $data = $this->input->post();
        $data['status'] = 1;
        $data['due_status'] = $due_status;
        $data['mType'] = (!empty($mType))? str_replace('~',',',$mType) : '';
        $result = $this->issueRequisition->getDTRows($data);
        $sendData = array();
        $i = 1;
        $count = 0;
        foreach ($result['data'] as $row) :

            $row->sr_no = $i++;
            $row->allot_qty = $this->issueRequisition->getIssueMaterialData($row->id)->req_qty;
            $row->issue_qty = $this->issueRequisition->getAllotMaterialData($row->id)->req_qty;
            $row->indent_qty = $this->issueRequisition->getIndentMaterialData($row->id)->req_qty;
            if ($row->order_status == 0) :
                $row->order_status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
            elseif ($row->order_status == 1) :
                $row->order_status_label = '<span class="badge badge-pill badge-success m-1">Complete</span>';
            elseif ($row->order_status == 2) :
                $row->order_status_label = '<span class="badge badge-pill badge-info m-1">Accepted</span>';
            elseif ($row->order_status == 3) :
                $row->order_status_label = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
            endif;
            if ($row->used_at == 0) {
                $row->whom_to_handover = (!empty($row->handover_to) ? $this->employee->getEmp($row->handover_to)->emp_name : '');
            } else {
                $row->whom_to_handover = $this->party->getParty($row->handover_to)->party_name;
            }
            $row->controller = "issueRequisition";
            $sendData[] = getMaterialAllocatedData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function getDTIssueRows($mType = '', $status = 2)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $data['mType'] = (!empty($mType))? str_replace('~',',',$mType) : '';
        $result = $this->issueRequisition->getDTRows($data);
        $sendData = array();
        $i = 1;
        $count = 0;
        foreach ($result['data'] as $row) :

            $row->sr_no = $i++;
            $row->allot_qty = $this->issueRequisition->getIssueMaterialData($row->id)->req_qty;
            $row->issue_qty = $this->issueRequisition->getAllotMaterialData($row->id)->req_qty;
            $row->indent_qty = $this->issueRequisition->getIndentMaterialData($row->id)->req_qty;
            if ($row->order_status == 0) :
                $row->order_status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
            elseif ($row->order_status == 1) :
                $row->order_status_label = '<span class="badge badge-pill badge-success m-1">Complete</span>';
            elseif ($row->order_status == 2) :
                $row->order_status_label = '<span class="badge badge-pill badge-info m-1">Accepted</span>';
            elseif ($row->order_status == 3) :
                $row->order_status_label = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
            endif;
            $row->whom_to_handover = '';
            if(!empty($row->handover_to) AND $row->handover_to < 500):
                if ($row->usedAt == 0) {
                    $row->whom_to_handover = (!empty($row->handover_to)) ? $this->employee->getEmp($row->handover_to)->emp_name : '';
                } else {
                    $row->whom_to_handover = (!empty($row->handover_to)) ? $this->party->getParty($row->handover_to)->party_name : '';
                }
            endif;
            $row->controller = "issueRequisition";
            $sendData[] = getMaterialIssueData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Something went wrong...Please try again.']);
        else :

            $this->printJson($this->issueRequisition->delete($id));
        endif;
    }

    public function edit()
    {
        $data = $this->input->post();
        $this->data['itemData'] = $this->item->getItemLists(str_replace('~', ',', '1~2~3~4~5~6~7'));
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['planningType'] = $this->purchaseRequest->getPurchasePlanningType();
        $this->data['dataRow'] = $this->purchaseIndent->getPurchaseIndent($data['id']);
        $itemData = $this->item->getItem($this->data['dataRow']->req_item_id);
        $this->data['dataRow']->unit_id = $itemData->unit_id;
        $this->data['dataRow']->item_description = $itemData->description;
        $this->data['dataRow']->min_qty = $itemData->min_qty;
        $this->data['dataRow']->max_qty = $itemData->max_qty;
        $this->data['dataRow']->make_brand = $itemData->make_brand;
        $this->data['dataRow']->lead_time = $itemData->lead_time;

        $this->data['locationData'] = $this->store->getStoreLocationWithoutProcess();
        $this->load->view($this->indentForm, $this->data);
    }

    public function closePreq()
    {
        $data = $this->input->post();

        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Something went wrong...Please try again.']);
        else :
            $this->printJson($this->purchaseRequest->closePreq($data));
        endif;
    }
    
    function printMaterialIssueDetail($id){
        $this->data['issueData'] = $this->issueRequisition->getIssueMaterialLog($id);
        $reqData = $this->issueRequisition->getIssueMaterialLog($this->data['issueData']->ref_id);
        if ($reqData->used_at == 0) {
            $reqData->whom_to_handover = (!empty($reqData->handoverTo) ? $this->employee->getEmp($reqData->handoverTo)->emp_name : '');
        } else {
            $reqData->whom_to_handover = (!empty($reqData->handoverTo) ?$this->party->getParty($reqData->handoverTo)->party_name:'');
        }
        $reqData->machine_id=(!empty($reqData->machine_id))?$this->item->getItem($reqData->machine_id)->item_name:'';
        $reqData->fg_item_id=(!empty($reqData->fg_item_id))?$this->item->getItem($reqData->fg_item_id)->item_name:'';
        $this->data['reqData']=$reqData;
        $this->data['companyData'] = $this->issueRequisition->getCompanyInfo();
        $response = "";
        $logo = base_url('assets/images/logo.png');
        $this->data['letter_head'] = base_url('assets/images/letterhead_top.png');

        $pdfData = $this->load->view('issue_requisition/print_issue', $this->data, true);
        $htmlHeader = '<img src="' . $this->data['letter_head'] . '" class="img">';
        $htmlFooter = '';
        
        $mpdf = $this->m_pdf->load();
        $pdfFileName = 'Material Issue-' . sprintf("ISU%05d", $this->data['issueData']->log_no) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
        $stylesheet = file_get_contents(base_url('assets/css/style.css?v=' . time()));
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetTitle(sprintf("ISU%05d", $this->data['issueData']->log_no));
        $mpdf->SetWatermarkImage($logo, 0.03, array(120,40));
        $mpdf->showWatermarkImage = true;
        $mpdf->SetProtection(array('print'));
        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->AddPage('P', '', '', '', '', 5, 5, 25, 5, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-P');
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }

    public function generateDirectIndent()
    {
        $data = $this->input->post();
        $this->data['itemData'] = $this->item->getItemLists();
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['planningType'] = $this->purchaseRequest->getPurchasePlanningType();
        $this->data['dataRow'] = new stdClass();

        $this->data['fgNMcData'] = $this->item->getItemLists('1,5');
        $this->data['empData'] = $this->employee->getEmpList();
        $this->data['partyData'] = $this->party->getVendorList();
        $this->data['loginId'] = $this->session->userdata('loginId');

        $this->data['itemTypeList'] = $this->itemCategory->mainCategoryList();
        $this->data['categoryList'] = $this->itemCategory->getCategoryList();
        $this->data['familyGroup'] = $this->item->getfamilyGroupList();
        // print_r($this->data['fgNMcData']);
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['locationData'] = $this->store->getStoreLocationWithoutProcess();
        $this->data['approve_type'] = (!empty($data['approve_type']) ? $data['approve_type'] : '');
        $this->data['indentNo'] = $this->purchaseIndent->nextIndentNo();
        $this->load->view($this->indentForm, $this->data);
    }
    
    function printToolLife($id){
        $issueData = $this->issueRequisition->getIssueMaterialLog($id);
	    $logo=base_url('assets/images/logo.png');
        if ($issueData->used_at == 0) {
            $issueData->whom_to_handover = (!empty($issueData->handover_to) ? $this->employee->getEmp($issueData->handover_to)->emp_name : '');
        } else {
            $issueData->whom_to_handover = (!empty($issueData->handover_to) ?$this->party->getParty($issueData->handover_to)->party_name:'');
        }
		$tbody = '';
        for($i=1;$i<21;$i++):
            $tbody .= '<tr>';
                $tbody .= '<td class="text-center" height="40"></td>';
                $tbody .= '<td></td>';
                $tbody .= '<td class="text-right"></td>';
                $tbody .= '<td class="text-right"></td>';
                $tbody .= '<td class="text-right"></td>';
                $tbody .= '<td class="text-right"></td>';
                $tbody .= '<td class="text-right"></td>';
                $tbody .= '<td class="text-right"></td>';
                $tbody .= '<td class="text-right"></td>';
                $tbody .= '<td class="text-right"></td>';
                $tbody .= '<td class="text-right"></td>';
                $tbody .= '<td class="text-right"></td>';
            $tbody .= '</tr>';
        endfor;

		$pdfData = '<div class="row">
            <div class="col-12">
                <table class="table top-table text-left item-list-bb tdw-100px1" style="border:1px solid #000000;padding:5px; text-align: right; margin-top:5px;">
                    <tr>
                        <td style="width:14%;"><b>Issue No</b></td><td style="width:16%;">'. sprintf("ISU%05d", $issueData->log_no) .'</td>
                        <td style="width:14%;"><b>Item Code</b></td><td style="width:25%;">'. $issueData->item_code.'</td>
                        <td style="width:14%;"><b>Category</b></td><td style="width:16%;">'.$issueData->category_name.'</td>
                    </tr>
                    <tr>
                        <td><b>Issued At</b></td><td>'. (!empty($issueData->issue_date)? date("d-m-Y H:i:s", strtotime($issueData->issue_date)):'') .' </td>
                        <td><b>Item Name</b></td><td colspan="3">'. $issueData->full_name.'</td>
                    </tr>
                    <tr>
                        <td><b>Issue Qty</b></td><td>'. floatVal($issueData->req_qty) . ' ' . $issueData->unit_name .'</td>
                        <td><b>No Of Corner</b></td><td>'.$issueData->no_of_corner.'</td>
                        <td><b>Tool Life</b></td><td>'.$issueData->tool_life.' <small>'.$issueData->tool_unit.'</small></td>
                    </tr>
                    <tr>
                        <td><b>Issue Condition</b></td><td>'.((!empty($issueData->req_type))?(($issueData->req_type == 1) ? 'Fresh' : 'Used'):"" ).'</td>
                        <td><b>Machine No.</b></td><td>'.(!empty($issueData->machine_code) ? '['.$issueData->machine_code.'] '.$issueData->machine_name : $issueData->machine_name).'</td>
                        <td><b>Operator Name</b></td><td>'.$issueData->whom_to_handover.'</td>
                    </tr>
                </table>
                <table class="table item-list-bb" style="margin-top:5px;">
                    <tr>
                        <th height="40">Date.</th>
                        <th>Shift(D/N)</th>
                        <th>AAPPL Code</th>
                        <th>Operation</th>
                        <th>Machine No.</th>
                        <th>Tools Sr No.</th>
                        <th>Tool Option</th>
                        <th>Corner No.</th>
                        <th>No. Of Job Production</th>
                        <th>Total Qty</th>
                        <th>Operator Sign.</th>
                        <th>Super Sign.</th>
                    </tr>
                    '.$tbody.'
                </table>
            </div>
        </div>';

	    $htmlHeader = '<table class="table">
                <tr>
    				<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
    				<td class="org_title text-center" style="font-size:1.2rem;width:50%">TOOL LIFE MONITORING REPORT<br>(CONSUMABLE)</td>
    				<td style="width:25%;" class="text-right"><span style="font-size:0.9rem;">R-PROD-09(00/01.10.17)<br>Tool Report Prepared By</td>
    			</tr>
            </table>';
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='tool_life_monitoring_report.pdf';
		$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
        
        $mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->AddPage('P','','','','',5,5,25,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
}
