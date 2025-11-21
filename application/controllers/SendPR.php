<?php
class SendPR extends MY_Controller
{
    private $indexPage = "send_pr/index";
    private $requestForm = "purchase_request/purchase_request";
    private $otherDetail = "send_pr/other_detail";
    private $returnIssueMaterial = "send_pr/return_issue_material";
    private $returnForm = "send_pr/return_form";
    private $returnTrans = "send_pr/return_trans";
    private $requisitionView = "send_pr/requisition_view";
    private $AllotReqView = "send_pr/alloted_requisition_list";
    private $completePRView = "send_pr/completed_requisition_list";
    private $rejPRView = "send_pr/rej_requisition_list"; 
    private $approvedPRView = "send_pr/approved_requisition_list";

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Requisition";
        $this->data['headData']->controller = "sendPR";
    }

    public function index()
    {
        $this->data['headData']->pageTitle = "Requisition";
        $this->data['headData']->pageUrl = "sendPR";
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage, $this->data);
    }

    public function purchasePR()
    { 
        $this->data['headData']->pageTitle = "Purchase Requisition";
        $this->data['headData']->pageUrl = "sendPR/purchasePR";
        $this->data['mType'] = '3';
        $apOptions = '<select id="approvedBy"><option value="">Select</option>';
        $approvedByList = $this->purchaseRequest->getApprovedByList();
        if (!empty($approvedByList)) {
            foreach ($approvedByList as $ap) {
                $apOptions .= '<option value="' . $ap->approved_by . '">' . $ap->emp_name . '</option>';
            }
        }
        $apOptions .= '<select>';

        $columnSelect = array();
        $columnSelect[] = '<select id="priorityFilter"><option value="">Select</option><option value="0">Low</option><option value="1">Medium</option><option value="2">High</option></select>';
        $columnSelect[] = $apOptions;
        $columnSelect[] = '<select id="statusFilter"><option value="">Select</option><option value="0">Pending</option><option value="4">Approved</option></select>';
        $this->data['selectBox'] = json_encode($columnSelect);
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->data['mType'] = '1~2~3~4~5~6~7';
        $this->data['index'] = 'purchasePR';
        $this->load->view($this->indexPage, $this->data);
    }

    public function storePR()
    {
        $this->data['headData']->pageTitle = "Store Requisition";
        $this->data['headData']->pageUrl = "sendPR/storePR";
        $this->data['mType'] = '1~2~3~4~5~6~7';
        $this->data['index'] = 'storePR';
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage, $this->data);
    }

    public function maintenancePR()
    {
        $this->data['headData']->pageTitle = "Maintenance Requisition";
        $this->data['headData']->pageUrl = "sendPR/maintenancePR";
        $this->data['mType'] = '5';
        $this->data['index'] = 'maintenancePR';
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage, $this->data);
    }

    public function salesPR()
    {
        $this->data['headData']->pageTitle = "Sales Requisition";
        $this->data['headData']->pageUrl = "sendPR/salesPR";
        $this->data['mType'] = '1';
        $this->data['index'] = 'salesPR';
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage, $this->data);
    }

    public function qualityPR()
    {
        $this->data['headData']->pageTitle = "Quality Requisition";
        $this->data['headData']->pageUrl = "sendPR/qualityPR";
        $this->data['mType'] = '6~7';
        $this->data['index'] = 'qualityPR';
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($mType = '', $status = 0)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $data['mType'] = str_replace('~',',',$mType);
        $result = $this->purchaseRequest->getDTRows($data); 
        $sendData = array();
        $i = 1;
        $count = 0;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;

            $row->allot_qty = $this->issueRequisition->getIssueMaterialData($row->id)->req_qty;
            $row->issue_qty = $this->issueRequisition->getAllotMaterialData($row->id)->req_qty;
            $row->indent_qty = $this->issueRequisition->getIndentMaterialData($row->id)->req_qty;
            
            if (!empty($row->created_by)) {
                $empData = $this->employee->getEmp($row->created_by);
                $row->request_by = (!empty($empData->emp_name) ? $empData->emp_name : '');
            } else {
                $row->request_by = '';
            }
            
            $authArr = explode(",", $row->auth_detail_main);
            $row->approveFlag = (in_array($this->loginId, $authArr) ? 1 : '');

            if ($row->order_status == 0) :
                $row->order_status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
                if ($row->approved_by > 0) :
                    $row->order_status_label = '<span class="badge badge-pill badge-success m-1">Approved</span>';
                endif;
            elseif ($row->order_status == 1) :
                $row->order_status_label = '<span class="badge badge-pill badge-success m-1">Complete</span>';
            elseif ($row->order_status == 2) :
                $row->order_status_label = '<span class="badge badge-pill badge-warning m-1">Rejected</span>';
            elseif ($row->order_status == 3) :
                $row->order_status_label = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
            elseif ($row->order_status == 4) :
                $row->order_status_label = '<span class="badge badge-pill badge-success m-1">Approved</span>';
            endif;

            $row->priority_label = '<span class="badge badge-pill badge-info m-1">Low</span>';
            if ($row->priority == 'Medium') :
                $row->priority_label = '<span class="badge badge-pill badge-warning m-1">Medium</span>';
            endif;
            if ($row->priority == 'High') :
                $row->priority_label = '<span class="badge badge-pill badge-danger m-1">High</span>';
            endif;
            $row->authDetail = $this->itemCategory->getItemAuthorisedBy($row->req_item_id)['authRow'];    

            $row->loginId = $this->session->userdata('loginId');
            $sendData[] = getSendPRData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPurchaseRequest($mType = '')
    {
        $this->data['itemData'] = $this->item->getItemLists(str_replace('~', ',', $mType));
        $this->data['fgNMcData'] = $this->item->getItemLists('1,5');
        $this->data['empData'] = $this->employee->getEmpList();
        $this->data['partyData'] = $this->party->getVendorList();
        $this->data['loginId'] = $this->session->userdata('loginId');

        $this->data['itemTypeList'] = $this->itemCategory->mainCategoryList();
        $this->data['categoryList'] = $this->itemCategory->getCategoryList();
        $this->data['familyGroup'] = $this->item->getfamilyGroupList();
        // print_r($this->data['fgNMcData']);
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['reqNo'] = $this->purchaseRequest->nextRequisitionNo();
        $this->data['planningType'] = array(); //$this->purchaseRequest->getPurchasePlanningType();
        $this->load->view($this->requestForm, $this->data);
    }

    public function getCategoryData()
    {
        $item_type = $this->input->post('item_type');
        $result = $this->item->getCategoryList($item_type);
        $options = "";
        if (!empty($result)) :
            $options .= '<option value="">Select ALL</option>';
            foreach ($result as $row) :
                $options .= '<option value="' . $row->id . '">' . $row->category_name . '</option>';
            endforeach;
        else :
            $options .= '<option value="">Select ALL</option>';
        endif;

        $this->printJson(['status' => 1, 'options' => $options]);
    }

    public function savePurchaseRequest()
    {
        $data = $this->input->post();
        $errorMessage = array();

        if (empty($data['req_item_id']))
            $errorMessage['req_item_id'] = "Item Name is required.";
        if (empty($data['req_date']))
            $errorMessage['req_date'] = "Request Date is required.";
        if (empty($data['req_qty']))
            $errorMessage['req_qty'] = "Request Qty. is required.";
        // if(empty($data['planning_type']))
        //     $errorMessage['planning_type'] = "Planning is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            if ($data['approve_type'] == 1) {
                $data['approved_by'] = $this->loginId;
                $data['approved_at'] = date("Y-m-d H:i:s");
                $data['order_status'] = 4; 
            } else {
                $data['created_by']  = $this->session->userdata('loginId');
            }
            unset($data['approve_type'], $data['is_returnable1']);
            $data['log_type'] = 1;
            $itmData  = $this->item->getItem($data['req_item_id']);
            if($itmData->item_type ==2 && !empty($itmData->size)){
                if($data['req_type'] == 2){
                    $data['size'] = (!empty($data['diameter'])?$data['diameter']:0).'X'.(!empty($data['length'])?$data['length']:0).'X'.(!empty($data['flute_length'])?$data['flute_length']:0);
                }else{
                    $data['size'] =$itmData->size;
                }
            }
            unset($data['diameter'],$data['length'],$data['flute_length']);
            $this->printJson($this->purchaseRequest->savePurchaseRequest($data));
        endif;
    }

    public function getItemStockData()
    {

        $data = $this->input->post();
        $this->printJson($this->purchaseRequest->getItemStockData($data['item_id']));
    }
    
    public function edit()
    {
        $data = $this->input->post();
        $this->data['itemData'] = $this->item->getItemLists(str_replace('~', ',', '1~2~3~4~5~6~7'));
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['planningType'] = array();
        $this->data['dataRow'] = $this->purchaseRequest->getPurchaseRequest($data['id']);
        $this->data['itemTypeList'] = $this->itemCategory->mainCategoryList();
        $this->data['categoryList'] = $this->itemCategory->getCategoryList();
        $this->data['familyGroup'] = $this->item->getfamilyGroupList();

        $itmName = (!empty($this->data['dataRow']->item_code)) ? "[" . $this->data['dataRow']->item_code . "] " . $this->data['dataRow']->item_name : $this->data['dataRow']->item_name;
        $fullItemName = (!empty($this->data['dataRow']->part_no)) ? $itmName . ' ' . $this->data['dataRow']->part_no : $itmName;
        $this->data['dataRow']->fullItemName = $fullItemName;

        $this->data['fgNMcData'] = $this->item->getItemLists('1,5');
        $this->data['empData'] = $this->employee->getEmpList();
        $this->data['partyData'] = $this->party->getVendorList();
        $this->data['loginId'] = $this->session->userdata('loginId');
        $this->data['approve_type'] = isset($data['approve_type']) ? $data['approve_type'] : '';
        $this->load->view("purchase_request/purchase_request", $this->data);
    }

    public function approvePreq()
    {
        $data = $this->input->post();

        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Something went wrong...Please try again.']);
        else :
            $this->printJson($this->purchaseRequest->approveRequisition($data));
        endif;
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Something went wrong...Please try again.']);
        else :
            $this->printJson($this->purchaseRequest->deleteRequisition($id));
        endif;
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

    public function getOtherDetail()
    {
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->purchaseRequest->getEntryLog($id, 'requisition_log');
        $this->load->view($this->otherDetail, $this->data);
    }

    public function returnMaterial($mType='',$index='')
    {
        $this->data['tableHeader'] = getStoreDtHeader('returnIssueMaterial');
        $this->data['mType'] = $mType;
        $this->data['index'] = $index;
        $this->load->view($this->returnIssueMaterial, $this->data);
    }

    public function getIssueMtrDTRows($mType='',$status = 2)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $data['mType'] = str_replace('~',',',$mType);
        $result = $this->issueRequisition->getDTRowsMaterialReturn($data);
        $sendData = array();
        $i = 1;

        foreach ($result['data'] as $row) :
            // if ($row->is_returnable == 1) :
                $row->sr_no = $i++;
                // $returnData = $this->issueRequisition->getReturnMaterialDataBatchWise($row->id,$row->batch_no);
                // $row->return_qty =!empty($returnData->qty)?$returnData->qty:0;
                $row->loginId = $this->session->userdata('loginId');
                $sendData[] = getReturnIssueMaterialData($row);
            // endif;
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function returnForm()
    {
        $id = $this->input->post('id');
        $this->data['batch_no']  = $this->input->post('batch_no');
        $this->data['pending_qty']  = $this->input->post('pending_qty');
        $this->data['size']  = $this->input->post('size');
        $this->data['ref_id'] = $id;
        $this->load->view($this->returnForm, $this->data);
    }


    public function saveReturnMaterial()
    {
        $data = $this->input->post(); 
        $errorMessage = array(); 
        
        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Return Date is required.";
        if(empty($data['used_qty'][0]) AND empty($data['fresh_qty'][0]) AND empty($data['missed_qty'][0]) AND empty($data['broken_qty'][0]) AND empty($data['scrap_qty'][0]) AND empty($data['regranding_qty'][0])):
            $errorMessage['genral_error'] = "Return Qty. is required.";
        else:
        $totalUsed = array_sum($data['used_qty']);
        $totalFresh = array_sum($data['fresh_qty']);
        $totalMissed = array_sum($data['missed_qty']);
        $totalBroken = array_sum($data['broken_qty']);
        $totalScrap = array_sum($data['scrap_qty']);
        $totalRegrading = array_sum($data['regranding_qty']);

        $data['qty'] = $totalUsed + $totalFresh + $totalMissed + $totalBroken +$totalScrap+$totalRegrading;
        // $issueLog = $this->issueRequisition->getIssueMaterialLog($data['ref_id']);
        // $returnLog = $this->issueRequisition->getReturnMaterialData($data['ref_id'],$data['batch_no']);
        // $pendingQty = $issueLog->req_qty - $returnLog->qty; 
        if ($data['qty'] > $data['pending_qty']) {
            $errorMessage['genral_error'] = "Return Qty. is not valid";
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        }

        endif;
        
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by']  = $this->session->userdata('loginId'); 
            $this->printJson($this->purchaseRequest->saveReturnMaterial($data));
        endif;
    }
    
    public function getReturnDetail()
    {
        $id = $this->input->post('id');
        $this->data['returnTransData'] = $this->purchaseRequest->getReturnTransaction($id);
        $this->load->view($this->returnTrans, $this->data);
    }

    public function deleteReturn()
    {
        $id = $this->input->post('id');
        $ref_id = $this->input->post('ref_id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Something went wrong...Please try again.']);
        else :
            $result = $this->purchaseRequest->deleteReturn($id);
            $returnData = $this->purchaseRequest->getReturnTransaction($ref_id);
            $html = "";
            if (!empty($returnData)) {
                $i = 1;
                foreach ($returnData as $row) {
                    $returnStatus = '';
                    if ($row->return_status == 1) {
                        $returnStatus = 'Used';
                    } elseif ($row->return_status == 2) {
                        $returnStatus = 'Fresh';
                    } elseif ($row->return_status == 3) {
                        $returnStatus = 'Missed';
                    } elseif ($row->return_status == 4) {
                        $returnStatus = 'Broken';
                    }
                    $html .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . $row->qty . '</td>
                    <td>' . $returnStatus . '</td>
                    <td>' . $row->reason . '</td>
                    <td>
                        <button type="button" class="btn btn-block btn-outline-danger" onclick="trashReturn(' . $row->id . ',' . $row->ref_id . ')"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>';
                }
            }
            $this->printJson(['status' => 1, 'html' => $html]);
        endif;
    }

    public function getReturnStock()
    {
        $data = $this->input->post();
        $rtrnData = $this->purchaseRequest->getReturnStock($data['item_id']);
        $tbodyHtml = "";
        if (!empty($rtrnData)) {
            foreach ($rtrnData as $row) {
                $returnQty = $this->issueRequisition->getReturnMaterialData($row->ref_id)->qty;
                $pendingReturn = $row->qty - $returnQty;
                if ($pendingReturn > 0) {
                    $tbodyHtml .= '<tr>
                    <td>' . (formatDate($row->req_date)) . '</td>
                    <td>' . sprintf('REQ%003d', $row->log_no) . '</td>
                    <td>' . ($pendingReturn) . '</td>
                </tr>';
                }
            }
        } else {
            $tbodyHtml .= '<tr><td colspan="3">No Data Available</td></tr>';
        }
        $this->printJson(['status' => 1, 'tbodyHtml' => $tbodyHtml]);
    }

    //Created By Karmi @06/05/2022 For Reject Requisition
    public function rejectRequisition()
    {
        $data = $this->input->post();

        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Something went wrong...Please try again.']);
        else :
            $this->printJson($this->purchaseRequest->rejectRequisition($data));
        endif;
    }

    public function viewRequistion()
    {
        $id = $this->input->post('id');
        $reqData = $this->purchaseRequest->getPurchaseRequest($id);
        $whereToUse = $this->item->getItem($reqData->fg_item_id);
        $machine = $this->item->getItem($reqData->machine_id);

        $this->data['itemData'] = $this->item->getItemList();
        $this->data['dataRow'] = $reqData;
        $this->data['dataRow']->where_to_use = !empty($whereToUse->full_name) ? $whereToUse->full_name : '';
        $this->data['dataRow']->machine = !empty($machine->item_name) ? $machine->item_name : '';
        if ($reqData->used_at == 0) {
            $empData= (!empty($reqData->handover_to) ? $this->employee->getEmp($reqData->handover_to) : []);
            $this->data['dataRow']->whom_to_handover = (!empty($empData) ? $empData->emp_name : '');
        } else {
            $this->data['dataRow']->whom_to_handover = $this->party->getParty($reqData->handover_to)->party_name;
        }
        $this->load->view($this->requisitionView, $this->data);
    }

    public function allotedMatrialList($mType='',$index='')
    {
        $this->data['tableHeader'] =  getStoreDtHeader('materialAllocatedSendPR');
        $this->data['mType'] = $mType;
        $this->data['index'] = $index;
        $this->load->view($this->AllotReqView, $this->data);
    }

    public function getAllotMtrDTRows($status = 1,$mType='')
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $data['mType'] = str_replace('~',',',$mType);
        $result = $this->issueRequisition->getDTRowsForMaterialReturn($data);
        $sendData = array();
        $i = 1;

        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->allot_qty = $this->issueRequisition->getIssueMaterialData($row->id)->req_qty;
            $row->issue_qty = $this->issueRequisition->getAllotMaterialData($row->id)->req_qty;
            $row->indent_qty = $this->issueRequisition->getIndentMaterialData($row->id)->req_qty;
            $row->permission = 'NO';
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
            $sendData[] = getMaterialAllocDataFromSendPR($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function completedPR($mType='',$index='')
    {
        $this->data['tableHeader'] =  getStoreDtHeader('completedPR');
        $this->data['mType'] = $mType;
        $this->data['index'] = $index;
        $this->load->view($this->completePRView, $this->data);
    }

    public function rejectedPR($mType='',$index='')
    {
        $this->data['tableHeader'] =  getStoreDtHeader('rejectedPR');
        $this->data['mType'] = $mType;
        $this->data['index'] = $index;
        $this->load->view($this->rejPRView, $this->data);
    }

    public function getCompletedDTRows($mType = '', $status = 1)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $data['mType'] = str_replace('~',',',$mType);
        $result = $this->purchaseRequest->getDTRows($data);
        $sendData = array();
        $i = 1;
        $count = 0;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->issue_qty = $this->issueRequisition->getAllotMaterialData($row->id)->req_qty;
            $row->whom_to_handover = '';
            if(!empty($row->handover_to) AND $row->handover_to < 500):
                if ($row->used_at == 0) {
                    $row->whom_to_handover = (!empty($row->handover_to) ? $this->employee->getEmp($row->handover_to)->emp_name : '');
                } else {
                    $row->whom_to_handover = $this->party->getParty($row->handover_to)->party_name;
                }
            endif;
            $authArr = explode(",", $row->auth_detail);
            $row->approveFlag = (in_array($this->loginId, $authArr) ? 1 : '');
            // print_r($authArr);
            if ($row->order_status == 0) :
                $row->order_status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
                if ($row->approved_by > 0) :
                    $row->order_status_label = '<span class="badge badge-pill badge-success m-1">Approved</span>';
                endif;
            elseif ($row->order_status == 1) :
                $row->order_status_label = '<span class="badge badge-pill badge-success m-1">Complete</span>';
            elseif ($row->order_status == 2) :
                $row->order_status_label = '<span class="badge badge-pill badge-warning m-1">Rejected</span>';
            elseif ($row->order_status == 3) :
                $row->order_status_label = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
            endif;

            $row->priority_label = '<span class="badge badge-pill badge-info m-1">Low</span>';
            if ($row->priority == 'Medium') :
                $row->priority_label = '<span class="badge badge-pill badge-warning m-1">Medium</span>';
            endif;
            if ($row->priority == 'High') :
                $row->priority_label = '<span class="badge badge-pill badge-danger m-1">High</span>';
            endif;

            $row->loginId = $this->session->userdata('loginId');
            $sendData[] = getCompletedPRData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function getRejectedDTRows($mType = '', $status = 2)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $data['mType'] = str_replace('~',',',$mType);
        $result = $this->purchaseRequest->getDTRows($data);
        $sendData = array();
        $i = 1;
        $count = 0;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;

            if ($row->used_at == 0) {
                $row->whom_to_handover = (!empty($row->handover_to) ? $this->employee->getEmp($row->handover_to)->emp_name : '');
            } else {
                $row->whom_to_handover = $this->party->getParty($row->handover_to)->party_name;
            }
            $authArr = explode(",", $row->auth_detail);
            $row->approveFlag = (in_array($this->loginId, $authArr) ? 1 : '');
            // print_r($authArr);
            if ($row->order_status == 0) :
                $row->order_status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
                if ($row->approved_by > 0) :
                    $row->order_status_label = '<span class="badge badge-pill badge-success m-1">Approved</span>';
                endif;
            elseif ($row->order_status == 1) :
                $row->order_status_label = '<span class="badge badge-pill badge-success m-1">Complete</span>';
            elseif ($row->order_status == 2) :
                $row->order_status_label = '<span class="badge badge-pill badge-warning m-1">Rejected</span>';
            elseif ($row->order_status == 3) :
                $row->order_status_label = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
            endif;

            $row->priority_label = '<span class="badge badge-pill badge-info m-1">Low</span>';
            if ($row->priority == 'Medium') :
                $row->priority_label = '<span class="badge badge-pill badge-warning m-1">Medium</span>';
            endif;
            if ($row->priority == 'High') :
                $row->priority_label = '<span class="badge badge-pill badge-danger m-1">High</span>';
            endif;

            $row->loginId = $this->session->userdata('loginId');
            $sendData[] = getRejectedPRData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function approvedPR($mType = '',$index = '')
    {
        $apOptions = '<select id="approvedBy"><option value="">Select</option>';
        $approvedByList = $this->purchaseRequest->getApprovedByList();
        if (!empty($approvedByList)) {
            foreach ($approvedByList as $ap) {
                $apOptions .= '<option value="' . $ap->approved_by . '">' . $ap->emp_name . '</option>';
            }
        }
        $apOptions .= '<select>';

        $columnSelect = array();
        $columnSelect[] = '<select id="priorityFilter"><option value="">Select</option><option value="0">Low</option><option value="1">Medium</option><option value="2">High</option></select>';
        $columnSelect[] = $apOptions;
        $columnSelect[] = '<select id="statusFilter"><option value="">Select</option><option value="0">Pending</option><option value="4">Approved</option></select>';
        $this->data['selectBox'] = json_encode($columnSelect);
        $this->data['tableHeader'] =  getStoreDtHeader('approvedPR');
        $this->data['mType'] = $mType;
        $this->data['index'] = $index;
        $this->load->view($this->approvedPRView, $this->data);
    }

    public function getApprovedDTRows($mType = '', $status = 4)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $data['mType'] = str_replace('~',',',$mType);
        $result = $this->purchaseRequest->getDTRows($data);
        $sendData = array();
        $i = 1;
        $count = 0;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->allot_qty = $this->issueRequisition->getIssueMaterialData($row->id)->req_qty;
            $row->issue_qty = $this->issueRequisition->getAllotMaterialData($row->id)->req_qty;
            $row->indent_qty = $this->issueRequisition->getIndentMaterialData($row->id)->req_qty;
            if ($row->used_at == 0) {
                $row->whom_to_handover = (!empty($row->handover_to) ? $this->employee->getEmp($row->handover_to)->emp_name : '');
            } else {
                $row->whom_to_handover = $this->party->getParty($row->handover_to)->party_name;
            }
            $authArr = explode(",", $row->auth_detail);
            $row->approveFlag = (in_array($this->loginId, $authArr) ? 1 : '');
            $row->priority_label = '<span class="badge badge-pill badge-info m-1">Low</span>';
            if ($row->priority == 'Medium') :
                $row->priority_label = '<span class="badge badge-pill badge-warning m-1">Medium</span>';
            endif;
            if ($row->priority == 'High') :
                $row->priority_label = '<span class="badge badge-pill badge-danger m-1">High</span>';
            endif;

            $row->loginId = $this->session->userdata('loginId');
            $sendData[] = getApprovedPRData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function getBatchWiseStockData(){
        $data = $this->input->post();
        $batchWiseStock = $this->store->getItemStockBatchWise(['item_id' => $data['item_id'],'stock_required'=>1,'stock_effect'=>1,'trans_id'=>'']);
        $tbodyHtml='';
        if (!empty($batchWiseStock)) {
            foreach ($batchWiseStock as $row) {
                $stkData = $this->stockTransac->getCurrentSizeOfRegindingItems(['item_id'=>$data['item_id'],'ref_type'=>32,'batch_no'=>$row->batch_no]);
                $size = !empty($stkData->size)?$stkData->size:'';
                $tbodyHtml .= '<tr>
                                    <td>' . $row->batch_no . '</td>
                                    <td>' . $size . '</td>
                                </tr>';
               
            }
        } else {
            $tbodyHtml .= '<tr><td colspan="3">No Data Available</td></tr>';
        }
        $this->printJson(['status' => 1, 'tbodyHtml' => $tbodyHtml]);
    }
}
