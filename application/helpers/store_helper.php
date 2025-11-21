<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');
/* get Pagewise Table Header */

function getStoreDtHeader($page)
{
    /* store header */
    $data['store'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['store'][] = ["name" => "#", "style" => "width:5%;"];
    $data['store'][] = ["name" => "Store Name"];
    $data['store'][] = ["name" => "Location"];
    $data['store'][] = ["name" => "Remark"];

    /* Dispatch Material */
    $data['jobMaterialDispatch'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center"];
    $data['jobMaterialDispatch'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['jobMaterialDispatch'][] = ["name" => "Job No.", "style" => "width:9%;"];
    $data['jobMaterialDispatch'][] = ["name" => "Request Date", "textAlign" => "center"];
    $data['jobMaterialDispatch'][] = ["name" => "Item Name"];
    $data['jobMaterialDispatch'][] = ["name" => "Allocated Qty", "textAlign" => "center"];
    $data['jobMaterialDispatch'][] = ["name" => "Requested Qty", "textAlign" => "center"];
    $data['jobMaterialDispatch'][] = ["name" => "Issue Qty", "textAlign" => "center"];
    $data['jobMaterialDispatch'][] = ["name" => "Issue Date", "textAlign" => "center"];
    $data['jobMaterialDispatch'][] = ["name" => "Pending Qty", "textAlign" => "center"];
    
    /* Allocated Material */    
    $data['allocatedMaterial'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center"];
    $data['allocatedMaterial'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['allocatedMaterial'][] = ["name" => "Job No.", "style" => "width:9%;"];
    $data['allocatedMaterial'][] = ["name" => "Allocated Date", "textAlign" => "center"];
    $data['allocatedMaterial'][] = ["name" => "Item Name"];
    $data['allocatedMaterial'][] = ["name" => "Location"];
    $data['allocatedMaterial'][] = ["name" => "Batch No."];
    $data['allocatedMaterial'][] = ["name" => "Allocated Qty", "textAlign" => "center"];
    $data['allocatedMaterial'][] = ["name" => "Issue Qty", "textAlign" => "center"];
    $data['allocatedMaterial'][] = ["name" => "Pending Qty", "textAlign" => "center"];

    /* Item Header */
    $data['storeItem'][] = ["name" => "#", "style" => "width:5%;", "srnoPosition" => '0'];
    $data['storeItem'][] = ["name" => "Item Code"];
    $data['storeItem'][] = ["name" => "Item Name"];
    $data['storeItem'][] = ["name" => "HSN Code"];
    $data['storeItem'][] = ["name" => "Opening Qty"];
    $data['storeItem'][] = ["name" => "Stock Qty"];

    /* GRN Header */
    $data['grn'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['grn'][] = ["name" => "#", "style" => "width:5%;"];
    $data['grn'][] = ["name" => "GRN No."];
    $data['grn'][] = ["name" => "GRN Date"];
    $data['grn'][] = ["name" => "Order No."];
    $data['grn'][] = ["name" => "Supplier/Customer"];
    $data['grn'][] = ["name" => "Item"];
    $data['grn'][] = ["name" => "Qty"];
    $data['grn'][] = ["name" => "UOM"];
    $data['grn'][] = ["name" => "Heat/Batch No."];
    $data['grn'][] = ["name" => "Document"];
    
    /* Capital Goods Header */
    $data['capitalGoods'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['capitalGoods'][] = ["name" => "#", "style" => "width:5%;"];
    $data['capitalGoods'][] = ["name" => "Item Name"];
    $data['capitalGoods'][] = ["name" => "Category"];
    $data['capitalGoods'][] = ["name" => "Opening Qty"];
    $data['capitalGoods'][] = ["name" => "Stock Qty"];
    $data['capitalGoods'][] = ["name" => "Manage Stock"];

    /* SendPR  Header */
    $data['sendPR'][] = ["name" => "Action", "style" => "width:5%;", "srnoPosition" => ''];
    $data['sendPR'][] = ["name" => "Urgency"];
    $data['sendPR'][] = ["name" => "Request No."];
    $data['sendPR'][] = ["name" => "Request Date"];
    $data['sendPR'][] = ["name" => "Full Name"];
    $data['sendPR'][] = ["name" => "Required Date"];
    $data['sendPR'][] = ["name" => "Used at"];
    $data['sendPR'][] = ["name" => "Request By"];
    $data['sendPR'][] = ["name" => "Req. Qty"];
    $data['sendPR'][] = ["name" => "Required Type"];
    $data['sendPR'][] = ["name" => "Approve By"];

    /* GIR Header  Avruti*/
    $data['gir'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['gir'][] = ["name" => "#", "style" => "width:5%;"];
    $data['gir'][] = ["name" => "GIR No."];
    $data['gir'][] = ["name" => "GIR Date"];
    $data['gir'][] = ["name" => "Order No."];
    $data['gir'][] = ["name" => "Supplier/Customer"];
    $data['gir'][] = ["name" => "Item"];
    $data['gir'][] = ["name" => "Qty"];
    $data['gir'][] = ["name" => "UOM"];
    $data['gir'][] = ["name" => "Heat/Batch No."];

    /* issueRequisition */
    $data['issueRequisition'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    //$data['issueRequisition'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['issueRequisition'][] = ["name" => "Urgency"];
    $data['issueRequisition'][] = ["name" => "Requisition No."];
    $data['issueRequisition'][] = ["name" => "Required On", "textAlign" => "center"];
    $data['issueRequisition'][] = ["name" => "Item Name"];
    $data['issueRequisition'][] = ["name" => "Stock Qty", "textAlign" => "center"];
    $data['issueRequisition'][] = ["name" => "Used At", "textAlign" => "center"];
    //$data['issueRequisition'][] = ["name" => "Whom To Handover", "textAlign" => "center"];
    $data['issueRequisition'][] = ["name" => "Required Type", "textAlign" => "center"];
    $data['issueRequisition'][] = ["name" => "Where To Use", "textAlign" => "center"];
    $data['issueRequisition'][] = ["name" => "Requested Qty", "textAlign" => "center"];
    $data['issueRequisition'][] = ["name" => "Alloted Qty", "textAlign" => "center"];
    $data['issueRequisition'][] = ["name" => "Issue Qty", "textAlign" => "center"];
    $data['issueRequisition'][] = ["name" => "Pending Qty", "textAlign" => "center"];
    $data['issueRequisition'][] = ["name" => "Indent Qty", "textAlign" => "center"];
    $data['issueRequisition'][] = ["name" => "Issue Date", "textAlign" => "center"];
    $data['issueRequisition'][] = ["name" => "Issue No", "textAlign" => "center"];
    $data['issueRequisition'][] = ["name" => "Request Approved By", "textAlign" => "center"];

    /* Planning Types header */
    $data['planningTypes'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['planningTypes'][] = ["name" => "#", "style" => "width:5%;"];
    $data['planningTypes'][] = ["name" => "Planning Types"];

    /* Return Issued Material */
    $data['returnIssueMaterial'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    // $data['returnIssueMaterial'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['returnIssueMaterial'][] = ["name" => "Issue Date", "textAlign" => "center"];
    $data['returnIssueMaterial'][] = ["name" => "Req No", "textAlign" => "center"];
    $data['returnIssueMaterial'][] = ["name" => "Issue No", "textAlign" => "center"];
    $data['returnIssueMaterial'][] = ["name" => "Item Name", "textAlign" => "center"];
    $data['returnIssueMaterial'][] = ["name" => "Batch No", "textAlign" => "center"];
    $data['returnIssueMaterial'][] = ["name" => "Issue Qty", "textAlign" => "center"];
    $data['returnIssueMaterial'][] = ["name" => "Return Qty", "textAlign" => "center"];
    $data['returnIssueMaterial'][] = ["name" => "Pending Qty", "textAlign" => "center"];

    /* Inspection Material */
    $data['inspectionMaterial'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" , "srnoPosition" => 0];
    $data['inspectionMaterial'][] = ["name" => "Issue No.", "style" => "width:4%;", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Item Name", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Serial No", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Return Qty", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Used", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Fresh", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Scrap", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Regrinding ", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Convert to Other", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Broken", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Missed", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center"];

    /* issueRequisition */
    $data['materialAllocated'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    //$data['materialAllocated'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['materialAllocated'][] = ["name" => "Allot Date", "textAlign" => "center"];
    $data['materialAllocated'][] = ["name" => "Allot No."];
    $data['materialAllocated'][] = ["name" => "Requisition No."];
    $data['materialAllocated'][] = ["name" => "Item Name"];
    $data['materialAllocated'][] = ["name" => "Whom To Handover"];
    $data['materialAllocated'][] = ["name" => "Required Type"];
    $data['materialAllocated'][] = ["name" => "Allot Qty"];
    $data['materialAllocated'][] = ["name" => "Allot Remark"];
    $data['materialAllocated'][] = ["name" => "Allot By"];

    /* issueRequisition */
    $data['materialIssue'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    // $data['materialIssue'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['materialIssue'][] = ["name" => "Issue Date", "textAlign" => "center"];
    $data['materialIssue'][] = ["name" => "Issue No."];
    $data['materialIssue'][] = ["name" => "Requisition No."];
    $data['materialIssue'][] = ["name" => "Item Name"];
    $data['materialIssue'][] = ["name" => "Whom To Handover"];
    $data['materialIssue'][] = ["name" => "Required Type"];
    $data['materialIssue'][] = ["name" => "Issue Qty"];
    $data['materialIssue'][] = ["name" => "Issue Remark"];
    $data['materialIssue'][] = ["name" => "Issue By"];

    /* issueRequisition */
    // $data['materialAllocatedSendPR'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center","srnoPosition" => ''];
    //$data['materialAllocated'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['materialAllocatedSendPR'][] = ["name" => "Allot Date", "textAlign" => "center", "srnoPosition" => 0];
    $data['materialAllocatedSendPR'][] = ["name" => "Allot No."];
    $data['materialAllocatedSendPR'][] = ["name" => "Requisition No."];
    $data['materialAllocatedSendPR'][] = ["name" => "Item Name"];
    $data['materialAllocatedSendPR'][] = ["name" => "Whom To Handover"];
    $data['materialAllocatedSendPR'][] = ["name" => "Required Type"];
    $data['materialAllocatedSendPR'][] = ["name" => "Allot Qty"];
    $data['materialAllocatedSendPR'][] = ["name" => "Allot Remark"];
    $data['materialAllocatedSendPR'][] = ["name" => "Allot By"];

    /* Completed PR  Header */
    $data['completedPR'][] = ["name" => "Action", "style" => "width:5%;", "srnoPosition" => ''];
    //$data['sendPR'][] = ["name" => "#", "style" => "width:5%;"];
    $data['completedPR'][] = ["name" => "Urgency"];
    $data['completedPR'][] = ["name" => "Request No."];
    $data['completedPR'][] = ["name" => "Request Date"];
    $data['completedPR'][] = ["name" => "Full Name"];
    $data['completedPR'][] = ["name" => "Required Date"];
    $data['completedPR'][] = ["name" => "Used at"];
    $data['completedPR'][] = ["name" => "Whom to Handover"];
    $data['completedPR'][] = ["name" => "Req. Qty"];
    $data['completedPR'][] = ["name" => "Required Type"];
    $data['completedPR'][] = ["name" => "Approved By"];
    // $data['completedPR'][] = ["name" => "Status"];

    /* Rejected PR  Header */
    $data['rejectedPR'][] = ["name" => "Action", "style" => "width:5%;", "srnoPosition" => ''];
    $data['rejectedPR'][] = ["name" => "Urgency"];
    $data['rejectedPR'][] = ["name" => "Request No."];
    $data['rejectedPR'][] = ["name" => "Request Date"];
    $data['rejectedPR'][] = ["name" => "Full Name"];
    $data['rejectedPR'][] = ["name" => "Required Date"];
    $data['rejectedPR'][] = ["name" => "Used at"];
    $data['rejectedPR'][] = ["name" => "Whom to Handover"];
    $data['rejectedPR'][] = ["name" => "Req. Qty"];
    $data['rejectedPR'][] = ["name" => "Required Type"];
    $data['rejectedPR'][] = ["name" => "Rejected By"];
    // $data['rejectedPR'][] = ["name" => "Status"];
    
    /* Approved PR  Header */
    $data['approvedPR'][] = ["name" => "Action", "style" => "width:5%;", "srnoPosition" => ''];
    //$data['approvedPR'][] = ["name" => "#", "style" => "width:5%;"];
    $data['approvedPR'][] = ["name" => "Urgency"];
    $data['approvedPR'][] = ["name" => "Request No."];
    $data['approvedPR'][] = ["name" => "Request Date"];
    $data['approvedPR'][] = ["name" => "Full Name"];
    $data['approvedPR'][] = ["name" => "Required Date"];
    $data['approvedPR'][] = ["name" => "Used at"];
    $data['approvedPR'][] = ["name" => "Whom to Handover"];
    $data['approvedPR'][] = ["name" => "Req. Qty"];
    $data['approvedPR'][] = ["name" => "Issue Qty"];
    $data['approvedPR'][] = ["name" => "Alloted Qty"];
    $data['approvedPR'][] = ["name" => "Pending Qty"];
    // $data['approvedPR'][] = ["name" => "Indent Qty"];
    $data['approvedPR'][] = ["name" => "Required Type"];
    $data['approvedPR'][] = ["name" => "Approved By"];
    // $data['sendPR'][] = ["name" => "Status"];
    
    /* Gate Entry */
    $data['gateEntry'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    $data['gateEntry'][] = ["name" => "#", "style" => "width:5%;", "textAlign" => "center"];
    $data['gateEntry'][] = ["name"=> "GE No.", "textAlign" => "center"];
    $data['gateEntry'][] = ["name" => "GE Date", "textAlign" => "center"];
    $data['gateEntry'][] = ["name" => "Driver Name"];
    $data['gateEntry'][] = ["name" => "Driver No."];
    $data['gateEntry'][] = ["name" => "Vehicle No."];
    $data['gateEntry'][] = ["name" => "Vehicle Type"];
    $data['gateEntry'][] = ["name" => "Transport"];
    $data['gateEntry'][] = ['name' => "Total Items"];

    /* Gate Inward Pending GE Tab Header */
    $data['pendingGE'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    $data['pendingGE'][] = ["name" => "#", "style" => "width:5%;", "textAlign" => "center"];
    $data['pendingGE'][] = ["name"=> "GE No.", "textAlign" => "center"];
    $data['pendingGE'][] = ["name" => "GE Date", "textAlign" => "center"];
    $data['pendingGE'][] = ["name" => "Party Name"];
    $data['pendingGE'][] = ["name" => "Item Name"];
    $data['pendingGE'][] = ["name" => "Qty"];
    $data['pendingGE'][] = ["name" => "Inv. No."];
    $data['pendingGE'][] = ["name" => "Inv. Date"];
    $data['pendingGE'][] = ['name' => "CH. NO."];
    $data['pendingGE'][] = ['name' => "CH. Date"];

    /* Gate Inward Pending/Compeleted Tab Header */
    $data['gateInward'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    $data['gateInward'][] = ["name" => "#", "style" => "width:5%;", "textAlign" => "center"];
    $data['gateInward'][] = ["name"=> "GI No.", "textAlign" => "center"];
    $data['gateInward'][] = ["name" => "GI Date", "textAlign" => "center"];
    $data['gateInward'][] = ["name" => "Party Name"];
    $data['gateInward'][] = ["name" => "Item Name"];
    $data['gateInward'][] = ["name" => "Inward Qty"];
    $data['gateInward'][] = ["name" => "Actual Qty"];
    $data['gateInward'][] = ["name" => "Weight (Kgs)"];
    $data['gateInward'][] = ["name" => "PO. NO."];   
    $data['gateInward'][] = ["name" => "GE NO."];   
    
    /* Purchase Indent Header */
    $data['purchaseIndent'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['purchaseIndent'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['purchaseIndent'][] = ["name"=>"Indent Date"];
    $data['purchaseIndent'][] = ["name"=>"Indent No"];
    $data['purchaseIndent'][] = ["name"=>"Item Full Name"];
    $data['purchaseIndent'][] = ["name"=>"UOM"];
    $data['purchaseIndent'][] = ["name"=>"Lead Time"];
    $data['purchaseIndent'][] = ["name"=>"Min Qty"];    
    $data['purchaseIndent'][] = ["name"=>"Max Qty"];    
    $data['purchaseIndent'][] = ["name"=>"Req. Qty"];    
    $data['purchaseIndent'][] = ["name"=>"Urgency"];
    $data['purchaseIndent'][] = ["name"=>"Delivery Date"];
    $data['purchaseIndent'][] = ["name"=>"Planning Type"];
    $data['purchaseIndent'][] = ["name"=>"Remark"];
    $data['purchaseIndent'][] = ["name"=>"Auth. of Req."]; 
    $data['purchaseIndent'][] = ["name"=>"Status"];
    
    /* Regrinding Inspection Material */
    $masterCheckBox = '<input type="checkbox" id="masterSelect" class="filled-in chk-col-success BulkRequest" value=""><label for="masterSelect">ALL</label>';
    $data['pendingRegrinding'][] = ["name"=>$masterCheckBox,"style"=>"width:10%;","textAlign"=>"center","orderable"=>"false", "srnoPosition" => 0];
    $data['pendingRegrinding'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" ];
    $data['pendingRegrinding'][] = ["name" => "Issue No.", "style" => "width:4%;", "textAlign" => "center"];
    $data['pendingRegrinding'][] = ["name" => "Item Name", "textAlign" => "center"];
    $data['pendingRegrinding'][] = ["name" => "Batch No", "textAlign" => "center"];
    $data['pendingRegrinding'][] = ["name" => "Regrinding ", "textAlign" => "center"];

    /* Regrinding Challan Data */
    $data['regrindingChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['regrindingChallan'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['regrindingChallan'][] = ["name"=>"Challan Date"];
    $data['regrindingChallan'][] = ["name"=>"Challan No"];
    $data['regrindingChallan'][] = ["name"=>"Party"];
    
    /*Regrinding Reason Data */
    $data['regrindingReason'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['regrindingReason'][] = ["name" => "#", "style" => "width:5%;"];
    $data['regrindingReason'][] = ["name" => "Reamrk"];

     /* Regrinding Challan Data */
     $data['regrindingInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
     $data['regrindingInspection'][] = ["name"=>"#","style"=>"width:5%;"];
     $data['regrindingInspection'][] = ["name"=>"Challan Date"];
     $data['regrindingInspection'][] = ["name"=>"Challan No"];
     $data['regrindingInspection'][] = ["name"=>"Party"];
     $data['regrindingInspection'][] = ["name"=>"Item"];
     $data['regrindingInspection'][] = ["name"=>"Serial No"];
     $data['regrindingInspection'][] = ["name"=>"Receive Date"];
     $data['regrindingInspection'][] = ["name"=>"In Challan No"];
     $data['regrindingInspection'][] = ["name"=>"Size"];
     $data['regrindingInspection'][] = ["name"=>"Receive Size"];
     $data['regrindingInspection'][] = ["name"=>"Regrinding Reason"];

     /* ReRegrinding Inspection Material */
    $masterCheckBox = '<input type="checkbox" id="masterSelect" class="filled-in chk-col-success BulkRequest" value=""><label for="masterSelect">ALL</label>';
    $data['reRegrinding'][] = ["name"=>$masterCheckBox,"style"=>"width:10%;","textAlign"=>"center","orderable"=>"false", "srnoPosition" => 0];
    $data['reRegrinding'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" ];
    $data['reRegrinding'][] = ["name" => "Date", "style" => "width:4%;", "textAlign" => "center"];
    $data['reRegrinding'][] = ["name" => "In Challan No", "style" => "width:4%;", "textAlign" => "center"];
    $data['reRegrinding'][] = ["name" => "Party", "style" => "width:4%;", "textAlign" => "center"];
    $data['reRegrinding'][] = ["name" => "Item Name", "textAlign" => "center"];
    $data['reRegrinding'][] = ["name" => "Batch No", "textAlign" => "center"];
    $data['reRegrinding'][] = ["name" => "Size ", "textAlign" => "center"];
    $data['reRegrinding'][] = ["name" => "Received Size ", "textAlign" => "center"];
    
    /* LIST OF STOCK VERIFICATION  */
    $data['stockVerification'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['stockVerification'][] = ["name"=>"Part Name"];
    $data['stockVerification'][] = ["name"=>"Part No."];
    $data['stockVerification'][] = ["name"=>"Stock Register Qty."];
    $data['stockVerification'][] = ["name"=>"Physical Qty."];
    $data['stockVerification'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];

    return tableHeader($data[$page]);
}
/* Store Table Data */

function getStoreData($data)
{
    $deleteParam = $data->id . ",'Store'";
    $editParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-md', 'form_id' : 'editStoreLocation', 'title' : 'Update Store Location'}";
    $editButton = '';
    $deleteButton = '';
    if (empty($data->store_type)) :
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit(' . $editParam . ');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
    $action = getActionButton($editButton . $deleteButton);
    return [$action, $data->sr_no, $data->store_name, $data->location, $data->remark];
}

/* Job Material Dispatch Table Data */
function getJobMaterialIssueData($data){
    if($data->tab_status == 2):
        $editParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-md', 'form_id' : 'editAllocatedQty', 'title' : 'Update Allocated Stock','fnedit':'editAllocatedMaterial','fnsave':'updateAllocatedQty'}";

        $edit = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit(' . $editParam . ');"><i class="ti-pencil-alt" ></i></a>';

        $action = getActionButton($edit);

        $location = "[".$data->store_name."] ".$data->location;
        return [$action,$data->sr_no,(!empty($data->job_no))?$data->job_number:"General Issue",(!empty($data->ref_date))?date("d-m-Y",strtotime($data->ref_date)):"",$data->full_name,$location,$data->batch_no,$data->qty,($data->qty - $data->pending_stock),$data->pending_stock];
    else:
        $dispatchBtn="";
        $pendingQty = $data->req_qty - $data->issue_qty;
        $pendingQty = ($pendingQty < 0)?0:floatVal(round($pendingQty,3));
        if($pendingQty > 0):
            $dispatchParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'dispatchMaterial', 'title' : 'Material Issue'}";
            $dispatchBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Material Issue" flow="down" onclick="dispatch('.$dispatchParam.');"><i class="fas fa-paper-plane"></i></a>';     
        endif;
        $action = getActionButton($dispatchBtn);

        return [$action,$data->sr_no,(!empty($data->job_no))?$data->job_number:"General Issue",(!empty($data->req_date))?date("d-m-Y",strtotime($data->req_date)):"",$data->full_name,$data->job_allocated_stock,$data->req_qty,$data->issue_qty,(!empty($data->issue_date))?date("d-m-Y",strtotime($data->issue_date)):"",$pendingQty];
    endif;
}

/* Store Item Table Data */

function getStoreItemData($data)
{
    $mq = '';
    if ($data->stock_qty < $data->min_qty) {
        $mq = 'text-danger';
    }
    $qty = '<a href="' . base_url('store/itemStockTransfer/' . $data->id) . '" class="' . $mq . '">' . $data->stock_qty . ' (' . $data->unit_name . ')</a>';
    return [$data->sr_no, $data->item_code, $data->item_name, $data->hsn_code, $data->opening_qty . ' (' . $data->unit_name . ')', $qty];
}
/* GRN Table Data */

function getGRNData($data)
{
    $deleteParam = $data->grn_id . ",'GRN'";
    $reportBtn = "";
    $edit = "";
    $viewParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-lg', 'form_id' : 'viewGIR', 'title' : 'GIR Detail','fnedit' : 'viewGIR','button' : 'close'}";
    $viewButton = '<a class="btn btn-dribbble btn-edit permission-modify" href="javascript:void(0)" datatip="GIR Detail" flow="down" onclick="edit(' . $viewParam . ');"><i class="fa fa-eye" ></i></a>';
    if ($data->qc_status == 1) {
        $reportParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-md', 'form_id' : 'grnReport', 'title' : 'GRN Report', 'fnedit' : 'getGrnReport', 'fnsave' : 'updateGrnReport'}";
        $reportBtn = '<a class="btn btn-warning btn-consumption permission-modify" href="javascript:void(0)" datatip="QC" flow="down" onclick="edit(' . $reportParam . ');"><i class="fa fa-file"></i></a>';
        $edit = '<a href="' . base_url('gir' . '/edit/' . $data->grn_id) . '" class="btn btn-success btn-edit permission-modify" datatip="Verify" flow="down"><i class="fa fa-edit"></i></a>';
    }
    // $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $action = '';
    $order_no = "";
    $action = getActionButton($viewButton . $reportBtn . $edit);
    if (!empty($data->po_no) and !empty($data->po_prefix)) :
        $order_no = getPrefixNumber($data->po_prefix, $data->po_no);
    endif;
    $qty = $data->qty - $data->short_qty - $data->rejection_qty;
    return [$action, $data->sr_no, getPrefixNumber($data->grn_prefix, $data->grn_no), formatDate($data->grn_date), $order_no, $data->party_name, $data->item_name, $qty, $data->unit_name, $data->batch_no, $data->doc_check_list];
}
/* Capital Goods Table Data */

function getCapitalGoods($data)
{
    $deleteParam = $data->id . ",'Item'";
    $editParam = "{'id' : " . $data->id . ",'item_type' :" . $data->item_type . ", 'modal_id' : 'modal-lg', 'form_id' : 'editItem', 'title' : 'Update Item', 'fnsave' : 'save'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit(' . $editParam . ');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $action = getActionButton($editButton . $deleteButton);
    $updateStockBtn = "";
    /* $updateStockBtn = ($data->rm_type == 0)?'<button type="button" class="btn waves-effect waves-light btn-outline-warning itemStockUpdate permission-modify" data-id="'.$data->id.'" data-item_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="addStockTrans" data-form_title="Update Stock">Update Stock</button>':''; */
    $mq = '';
    if ($data->qty < $data->min_qty) {
        $mq = 'text-danger';
    }
    $qty = '<a href="' . base_url('reports/productReport/itemWiseStock/' . $data->id) . '" class="' . $mq . '">' . $data->qty . ' (' . $data->unit_name . ')</a>';
    $openingStock = '<button type="button" class="btn waves-effect waves-light btn-outline-primary itemOpeningStock permission-modify" data-id="' . $data->id . '" data-item_name="' . $data->item_name . '" data-button="close" data-modal_id="modal-lg" data-function="addOpeningStock" data-form_title="Opening Stock">Opening Stock</button>';
    return [$action, $data->sr_no, $data->item_name, $data->category_name, $data->opening_qty . ' (' . $data->unit_name . ')', $qty, $openingStock . ' ' . $updateStockBtn];
}
/* SendPR Data  */

function getApprovedPRData($data)
{
    $purchaseOrder = $purchaseEnq = "";
    $editButton = "";
    $approvalBtn = "";
    $deleteButton = "";
    $rejectionBtn = "";
    $editParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-xl', 'form_id' : 'purchaseReq', 'title' : 'Requisition','fnsave' : 'savePurchaseRequest'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit(' . $editParam . ');"><i class="fa fa-edit" ></i></a>';
            
    if ($data->approveFlag == 1 || $data->created_by == $data->loginId) {
        if ($data->order_status == 0 && $data->approved_by == 0) {
            if ($data->approveFlag == 1) {
                $approveParam = "{'id' : " . $data->id . ",'approve_type' : '1', 'modal_id' : 'modal-xl', 'form_id' : 'purchaseReq', 'title' : 'Requisition','fnsave' : 'savePurchaseRequest'}";
                $approvalBtn = '<a class="btn btn-facebook btn-edit permission-modify approveRequis" href="javascript:void(0)" datatip="Approve Requisition" flow="down" onclick="edit(' . $approveParam . ');"><i class="fa fa-check" ></i></a>';
                $rejectionBtn = '<a class="btn btn-facebook rejectRequisition btn-edit permission-modify" data-id="' . $data->id . '" data-val="2" data-msg="Rejected"  href="javascript:void(0)" datatip="Reject Requisition" flow="down"><i class="ti-close"></i></a>';
            }
            // $purchaseOrder = '<a href="'.base_url('purchaseOrder/addPOFromRequest/'.$data->id).'" class="btn btn-warning btn-inv permission-write" datatip="Purchase Order" flow="down" ><i class="ti-file"></i></a>';
            // $purchaseEnq = '<a href="'.base_url('purchaseEnquiry/addEnqFromRequest/'.$data->id).'" class="btn btn-success btn-enq permission-write" datatip="Purchase Enquiry" flow="down" ><i class="fa fa-question-circle"></i></a>';
            $deleteParam = $data->id . ",'Requisition'";
            $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
        }
    }
    $otherParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-md','button' : 'close', 'form_id' : 'otherDetails', 'title' : 'Requisition Details', 'fnedit' : 'getOtherDetail'}";
    $otherButton = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Requisition Details" flow="down" onclick="edit(' . $otherParam . ');"><i class="fa fa-info"></i></a>';
    $viewParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-xl','button' : 'close', 'form_id' : 'viewRequisition', 'title' : 'Requisition Details', 'fnedit' : 'viewRequistion'}";
    $viewButton = '<a class="btn btn-info btn-warning permission-modify" href="javascript:void(0)" datatip="Requisition Details" flow="down" onclick="edit(' . $viewParam . ');"><i class="fa fa-eye"></i></a>';
    $action = getActionButton($otherButton . $approvalBtn . $viewButton . $editButton . $deleteButton);
    $rqty = (!empty($data->unit_name)) ? floatVal($data->req_qty) . ' <small>' . $data->unit_name . '</small>' : floatVal($data->req_qty);
    $req_type = "";
    if ($data->req_type == 1) {$req_type = "Fresh";} else {$req_type = "Used"; }
    $pendingQty = $data->req_qty - (!empty($data->allot_qty) ? $data->allot_qty : 0) - (!empty($data->issue_qty) ? $data->issue_qty : 0);
    $used_at = (!empty($data->used_at) ? 'Vendor' : 'In House');
    
    return [$action, $data->priority_label, sprintf("REQ%05d", $data->log_no), (!empty($data->req_date)) ? date("d-m-Y", strtotime($data->req_date)) : "", $data->full_name, (!empty($data->delivery_date)) ? date("d-m-Y", strtotime($data->delivery_date)) : "", $used_at, $data->whom_to_handover, $rqty, $data->allot_qty, $data->issue_qty, $pendingQty, $req_type, $data->emp_name];
}
/* GIR Table Data */

function getGIRData($data)
{
    $deleteParam = $data->gir_id . ",'GIR'";
    $edit = "";
    $inspection = "";
    $delete = "";
        $edit = '<a href="' . base_url($data->controller . '/edit/' . $data->gir_id) . '" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        //$inspection = '<a href="javscript:voide(0);" type="button" class="btn btn-success waves-effect waves-light getInspectedMaterial permission-modify" data-grn_id="' . $data->gir_id . '" data-trans_id="' . $data->id . '" data-grn_prefix="' . $data->gir_prefix . '" data-grn_no="' . $data->gir_no . '" data-grn_date="' . date("d-m-Y", strtotime($data->gir_date)) . '" data-item_name="' . $data->item_name . '" data-toggle="modal" data-target="#inspectionModel" datatip="Inspection" flow="down"><i class="fas fa-search"></i></a>';
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $action = '';
    $order_no = "";
    //if($data->type == 1 && $data->inspected_qty < $data->qty):
    //$action = getActionButton($edit.$inspection.$delete);
    //endif;
    //if($data->type == 2):
    $action = getActionButton($edit . $inspection . $delete);
    //endif;
    if (!empty($data->po_no) and !empty($data->po_prefix)) :
        $order_no = getPrefixNumber($data->po_prefix, $data->po_no);
    endif;
    return [$action, $data->sr_no, getPrefixNumber($data->gir_prefix, $data->gir_no), formatDate($data->gir_date), $order_no, $data->party_name, $data->item_name, $data->qty, $data->unit_name, $data->batch_no];
}
/* Issue Requisition Table Data */

function getRequisitionIssueData($data)
{
    $issue_id = (!empty($data->issue_id)) ? $data->issue_id : "''";
    $dispatchBtn = "";
    $requestParamBtn = "";
    $issueMaterialButton = "";
    if ($data->log_type == 1) {
        $title = "Material Allocate";
        if ($data->delivery_date <= date("Y-m-d")) {
            $title = "Material Issue";
        }
        $dispatchParam = "{'id' : " . $issue_id . ",'ref_id' : " . $data->id . ", 'modal_id' : 'modal-xl', 'form_id' : 'materialIssue', 'title' : '" . $title . "'}";
        $dispatchBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Book Material" flow="down" onclick="dispatch(' . $dispatchParam . ');"><i class="fas fa-paper-plane"></i></a>';
        $req_id = (!empty($data->req_id)) ? $data->req_id : "''";
        $requestParam = "{'id' : " . $req_id . ",'ref_id' : " . $data->id . ", 'modal_id' : 'modal-xl', 'form_id' : 'indentGenerate', 'title' : 'Generate Indent','fnsave' : 'savePurchaseIndent'}";
        $requestParamBtn = '<a class="btn btn-info btn-request permission-modify" href="javascript:void(0)" datatip="Indent" flow="down" onclick="request(' . $requestParam . ');"><i class="icon-Check"></i></a>';
        $dispatchBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Material Issue" flow="down" onclick="dispatch(' . $dispatchParam . ');"><i class="fas fa-paper-plane"></i></a>';
    }
    if ($data->log_type == 2 && $data->order_status == 1) {
        $issueMaterialButton = '<a class="btn btn-success" href="javascript:void(0)" onclick="issueMaterial(' . $data->id . ');" datatip="Material Issue" flow="down"><i class="fas fa-paper-plane"></i></a>';
    }
    $usedAt = "";
    if ($data->used_at == 1) :
        $usedAt = "Vendor";
    else :
        $usedAt = "In House";
    endif;
    $req_type = "";
    if ($data->req_type == 1) {$req_type = "Fresh";} else {$req_type = "Used"; }
    $action = getActionButton($issueMaterialButton . $dispatchBtn . $requestParamBtn);
    $pendingQty = $data->req_qty - (!empty($data->allot_qty) ? $data->allot_qty : 0) - (!empty($data->issue_qty) ? $data->issue_qty : 0);
    return [$action, $data->priority_label, sprintf("REQ%05d", $data->log_no), (!empty($data->req_date)) ? date("d-m-Y", strtotime($data->delivery_date)) : "", $data->full_name, floatVal($data->stock_qty) . " (" . $data->unit_name . ") ", $usedAt, $req_type, $data->wtu_name, $data->req_qty, $data->allot_qty, $data->issue_qty, $pendingQty, $data->indent_qty, (!empty($data->issue_date)) ? date("d-m-Y", strtotime($data->issue_date)) : "", (!empty($data->issue_no)) ? $data->issue_no : "", $data->approved_by];
}
/* Planning Types Table Data */

function getPlanningData($data)
{
    $deleteParam = $data->id . ",'Planning Types'";
    $editParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-md', 'form_id' : 'edit', 'title' : 'Update Planning Types'}";
    $editButton = '';
    $deleteButton = '';
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit(' . $editParam . ');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $action = getActionButton($editButton . $deleteButton);
    return [$action, $data->sr_no, $data->planning_type];
}
/* Purchase Request Data  */

function getPurchaseIndentDataForApproval($data)
{
    $purchaseOrder = $purchaseEnq = "";
    $editButton = "";
    $deleteButton = "";
    $approvereq = "";
    $closereq = "";
    $viewParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-lg', 'form_id' : 'viewRequest', 'title' : 'Indent Detail','fnedit' : 'viewPurchaseReq','button' : 'close'}";
    $viewButton = '<a class="btn btn-dribbble btn-edit permission-modify" href="javascript:void(0)" datatip="Indent Detail" flow="down" onclick="edit(' . $viewParam . ');"><i class="fa fa-eye" ></i></a>';
    $selectBox = "";
    if ($data->order_status == 0 && $data->approved_by == 0) {
        // $req_id=(!empty($data->req_id))?$data->req_id:"''";
        if ($data->approveFlag == 1) {
            $requestParam = "{'id' : " . $data->id . ",'ref_id' : " . $data->ref_id . ",'approve_type':1, 'modal_id' : 'modal-xl', 'form_id' : 'indentGenerate', 'title' : 'Generate Indent','fnsave' : 'savePurchaseIndent'}";
            $approvereq = '<a class="btn btn-info btn-request permission-modify" href="javascript:void(0)" datatip="Approve Purchase Indent" flow="down" onclick="request(' . $requestParam . ');"><i class="fa fa-check"></i></a>';
        }
        $deleteParam = $data->id . ",'Requisition'";
        $editParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-xl', 'form_id' : 'purchaseReq', 'title' : 'Requisition','fnsave' : 'savePurchaseIndent'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit(' . $editParam . ');"><i class="fa fa-edit" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
        $closereq = '<a href="javascript:void(0)" class="btn btn-dark closePreq permission-modify" data-id="' . $data->id . '" data-val="3" data-msg="Close" datatip="Close Purchase Request" flow="down" ><i class="ti-close"></i></a>';
    }
    $otherParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-md','button' : 'close', 'form_id' : 'otherDetails', 'title' : 'Requisition Details', 'fnedit' : 'getOtherDetail'}";
    $otherButton = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Indent Details" flow="down" onclick="edit(' . $otherParam . ');"><i class="fa fa-info"></i></a>';
    $urgency = "";
    if ($data->urgency == 1) {
        $urgency = "Medium";
    } elseif ($data->urgency == 2) {
        $urgency = "High";
    } else {
        $urgency = "Low";
    }
    $action = getActionButton($otherButton . $viewButton . $approvereq . $closereq . $editButton . $deleteButton);
    return [$action, $selectBox, (!empty($data->req_date)) ? date("d-m-Y", strtotime($data->req_date)) : "", sprintf("IND%05d", $data->log_no), $data->full_name, $data->unit_name, $data->lead_time, $data->min_qty, $data->max_qty, $data->sc_qty, $urgency, formatDate($data->delivery_date), $data->plan_type, $data->remark, $data->emp_name, $data->order_status_label];
}
/* Return Issue Material Table Data */

function getReturnIssueMaterialData($data)
{
    $pendingQty = $data->issue_qty - (!empty($data->return_qty) ? $data->return_qty : 0);

    $issue_id = (!empty($data->id)) ? $data->id : "''";
    $issueBtn = "";
    $title =$data->full_name.' [<small> Pending Qty : '.$pendingQty.' </small>]';
    $issueParam = "{'id' : " . $issue_id . ",'batch_no':'".$data->batch_no."','pending_qty':".$pendingQty.",'size':'".$data->size."', 'modal_id' : 'modal-xl', 'form_id' : 'materialReturn', 'title' : '".$title."','fnedit' : 'returnForm', 'fnsave' : 'saveReturnMaterial'}";
    $issueBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Return" flow="down" onclick="returnMaterial(' . $issueParam . ');"><i class="fas fa-paper-plane"></i></a>';
    $transParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-md','button' : 'close', 'form_id' : 'otherDetails', 'title' : 'Return Details', 'fnedit' : 'getReturnDetail'}";
    $transButton = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Return Details" flow="down" onclick="edit(' . $transParam . ');"><i class="fa fa-info"></i></a>';
    $action = getActionButton($issueBtn . $transButton);
    return [$action, (!empty($data->req_date)) ? date("d-m-Y", strtotime($data->req_date)) : "", sprintf("REQ%05d", $data->req_no), sprintf("ISU%05d", $data->log_no), $data->full_name,$data->batch_no, $data->issue_qty, $data->return_qty, $pendingQty];
}

function getInspectionData($data)
{
    $storeParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-lg', 'form_id' : 'storeLocation', 'title' : '".$data->full_name."','fnedit' : 'inspectionView', 'fnsave' : 'saveInspLocation'}";
    $storeBtn = '<a class="btn btn-facebook btn-sm" href="javascript:void(0)" datatip="Store Location" flow="down" onclick="edit(' . $storeParam . ');"><i class="fas fa-database"></i></i></a>';
    $used_qty = '<input id="used_qty_'.$data->id.'" value="'.$data->used_qty.'" type="text" class="form-control">';
    $fresh_qty = '<input id="fresh_qty_'.$data->id.'" value="'.$data->fresh_qty.'" type="text" class="form-control">';
    $scrap_qty = '<input id="scrap_qty_'.$data->id.'" value="'.$data->scrap_qty.'" type="text" class="form-control">';
    $regranding_qty ='<div class="input-group">
                        <input id="regranding_qty_'.$data->id.'"  value="'.$data->regranding_qty.'" type="text" class="form-control">
                        <input id="regrinding_reason_'.$data->id.'" value="0" type="hidden">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-success regrindingReason" data-btn_id="'.$data->id.'">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div><div class="error regrinding_reason'.$data->id.'"></div>';
    $convert_item = '<div class="input-group">
                        <input id="convert_qty_'.$data->id.'" value="0" type="text" class="form-control">
                        <input id="convert_item_id_'.$data->id.'" value="0" type="hidden">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-success convertItem" data-btn_id="'.$data->id.'">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div><div class="error convert_qty'.$data->id.'"></div>';
    $broken_qty = '<input id="broken_qty_'.$data->id.'" value="'.$data->broken_qty.'" type="text" class="form-control">';
    $miss_qty = '<input id="miss_qty_'.$data->id.'" value="'.$data->missed_qty.'" type="text" class="form-control">';
    $save_btn = '<input id="return_qty_'.$data->id.'" value="'.$data->return_qty.'" type="hidden">
                <button type="button" class="btn btn-success saveInspection" data-btn_id="'.$data->id.'">Save</button>
                <div class="error genral_error'.$data->id.'"></div>';
    //$action = getActionButton($vieButton);
    if(empty($data->status)){
        return [$data->sr_no, sprintf("ISU%05d", $data->log_no), $data->full_name,$data->batch_no, $data->return_qty, $used_qty, $fresh_qty, $scrap_qty, $regranding_qty, $convert_item, $broken_qty, $miss_qty, $save_btn];
    }elseif($data->status == 1){
        return [$data->sr_no, sprintf("ISU%05d", $data->log_no), $data->full_name,$data->batch_no, $data->return_qty, $data->used_qty, $data->fresh_qty, $data->scrap_qty, $data->regranding_qty, $data->convert_qty, $data->broken_qty, $data->missed_qty, $storeBtn];
    }elseif($data->status == 2){
        return [$data->sr_no, sprintf("ISU%05d", $data->log_no), $data->full_name,$data->batch_no, $data->return_qty, $data->used_qty, $data->fresh_qty, $data->scrap_qty, $data->regranding_qty, $data->convert_qty, $data->broken_qty, $data->missed_qty, $data->statusText];
    }
}

function getMaterialAllocatedData($data)
{
    //print_r($data);exit;
    $issue_id = (!empty($data->issue_id)) ? $data->issue_id : "''";
    $dispatchBtn = "";
    $requestParamBtn = "";
    $issueMaterialButton = "";
    $deleteButton = "";
    if ($data->log_type == 2 && $data->order_status == 1) {
        $issueMaterialButton = '<a class="btn btn-success" href="javascript:void(0)" onclick="issueMaterial(' . $data->id . ');" datatip="Material Issue" flow="down"><i class="fas fa-paper-plane"></i></a>';
        $deleteParam = $data->id . ",'Material'";
        $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    }
    $type = '';
    if ($data->reuisition_type == 1) :
        $type = 'fresh';
    else :
        $type = 'used';
    endif;
    $action = getActionButton($issueMaterialButton . $deleteButton);
    return [$action, date("d-m-Y", strtotime($data->req_date)), sprintf("ISU%05d", $data->log_no), sprintf("REQ%05d", $data->req_no), $data->full_name, $data->whom_to_handover, $type, $data->req_qty, $data->remark, $data->issue_by];
}

function getMaterialIssueData($data)
{
    $deleteParam = $data->id . ",'Material'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $type = '';
    if ($data->reuisition_type == 1) :
        $type = 'fresh';
    else :
        $type = 'used';
    endif;
    $printBtn = '<a class="btn btn-success btn-edit permission-approve" href="'.base_url($data->controller.'/printMaterialIssueDetail/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    $toolPrintBtn = '<a class="btn btn-info" href="'.base_url($data->controller.'/printToolLife/'.$data->id).'" target="_blank" datatip="Print Tool Life" flow="down"><i class="fas fa-print" ></i></a>';
    $action = getActionButton($printBtn.$toolPrintBtn);
    return [$action, date("d-m-Y", strtotime($data->req_date)), sprintf("ISU%05d", $data->log_no), sprintf("REQ%05d", $data->req_no), $data->full_name, $data->whom_to_handover, $type, $data->req_qty, $data->remark, $data->issue_by];
}

function getMaterialAllocDataFromSendPR($data)
{
    //print_r($data);exit;
    $issue_id = (!empty($data->issue_id)) ? $data->issue_id : "''";
    $dispatchBtn = "";
    $requestParamBtn = "";
    $issueMaterialButton = "";
    $deleteButton = "";
    if ($data->log_type == 2 && $data->order_status == 1) {
        $issueMaterialButton = '<a class="btn btn-success" href="javascript:void(0)" onclick="issueMaterial(' . $data->id . ');" datatip="Material Issue" flow="down"><i class="fas fa-paper-plane"></i></a>';
        $deleteParam = $data->id . ",'Material'";
        $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    }
    $type = '';
    if ($data->reuisition_type == 1) :
        $type = 'fresh';
    else :
        $type = 'used';
    endif;
    
    $action = getActionButton($issueMaterialButton . $deleteButton);
    return [date("d-m-Y", strtotime($data->req_date)), sprintf("ISU%05d", $data->log_no), sprintf("REQ%05d", $data->req_no), $data->full_name, $data->whom_to_handover, $type, $data->req_qty, $data->remark, (!empty($data->issue_by) ? $data->issue_by : '')];
}
/* SendPR Data  */

function getCompletedPRData($data)
{
    $purchaseOrder = $purchaseEnq = "";
    $editButton = "";
    $approvalBtn = "";
    $deleteButton = "";
    $rejectionBtn = "";
    $otherParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-md','button' : 'close', 'form_id' : 'otherDetails', 'title' : 'Requisition Details', 'fnedit' : 'getOtherDetail'}";
    $otherButton = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Requisition Details" flow="down" onclick="edit(' . $otherParam . ');"><i class="fa fa-info"></i></a>';
    $viewParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-xl','button' : 'close', 'form_id' : 'viewRequisition', 'title' : 'Requisition Details', 'fnedit' : 'viewRequistion'}";
    $viewButton = '<a class="btn btn-info btn-warning permission-modify" href="javascript:void(0)" datatip="Requisition Details" flow="down" onclick="edit(' . $viewParam . ');"><i class="fa fa-eye"></i></a>';
    $action = getActionButton($otherButton . $approvalBtn . $rejectionBtn . $viewButton . $editButton . $deleteButton);
    $rqty = (!empty($data->unit_name)) ? floatVal($data->req_qty) . ' <small>' . $data->unit_name . '</small>' : floatVal($data->req_qty);
    $req_type = "";
    if ($data->req_type == 1) {$req_type = "Fresh";} else {$req_type = "Used"; }
    $pendingQty = $data->req_qty - (!empty($data->allot_qty) ? $data->allot_qty : 0) - (!empty($data->issue_qty) ? $data->issue_qty : 0);
    $used_at = (!empty($data->used_at) ? 'Vendor' : 'In House');
    return [$action, $data->priority_label, sprintf("REQ%05d", $data->log_no), (!empty($data->req_date)) ? date("d-m-Y", strtotime($data->req_date)) : "", $data->full_name, (!empty($data->delivery_date)) ? date("d-m-Y", strtotime($data->delivery_date)) : "", $used_at, $data->whom_to_handover, $rqty, $req_type, $data->emp_name];
}
/* SendPR Data  */

function getRejectedPRData($data)
{
    $purchaseOrder = $purchaseEnq = "";
    $editButton = "";
    $approvalBtn = "";
    $deleteButton = "";
    $rejectionBtn = "";
    $otherParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-md','button' : 'close', 'form_id' : 'otherDetails', 'title' : 'Requisition Details', 'fnedit' : 'getOtherDetail'}";
    $otherButton = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Requisition Details" flow="down" onclick="edit(' . $otherParam . ');"><i class="fa fa-info"></i></a>';
    $viewParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-xl','button' : 'close', 'form_id' : 'viewRequisition', 'title' : 'Requisition Details', 'fnedit' : 'viewRequistion'}";
    $viewButton = '<a class="btn btn-info btn-warning permission-modify" href="javascript:void(0)" datatip="Requisition Details" flow="down" onclick="edit(' . $viewParam . ');"><i class="fa fa-eye"></i></a>';
    $action = getActionButton($otherButton . $viewButton);
    $rqty = (!empty($data->unit_name)) ? floatVal($data->req_qty) . ' <small>' . $data->unit_name . '</small>' : floatVal($data->req_qty);
    $req_type = "";
    if ($data->req_type == 1) {$req_type = "Fresh";} else {$req_type = "Used"; }
    $used_at = (!empty($data->used_at) ? 'Vendor' : 'In House');
    return [$action, $data->priority_label, sprintf("REQ%05d", $data->log_no), (!empty($data->req_date)) ? date("d-m-Y", strtotime($data->req_date)) : "", $data->full_name, (!empty($data->delivery_date)) ? date("d-m-Y", strtotime($data->delivery_date)) : "", $used_at, $data->whom_to_handover, $rqty, $req_type, $data->emp_name];
}
/* Approved Req. Data  */

function getSendPRData($data)
{
    $otherParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-md','button' : 'close', 'form_id' : 'otherDetails', 'title' : 'Requisition Details', 'fnedit' : 'getOtherDetail'}";
    $otherButton = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Requisition Details" flow="down" onclick="edit(' . $otherParam . ');"><i class="fa fa-info"></i></a>';
    $editButton = '';$deleteButton = '';$approvalBtn = '';
    $dataset = "{'id' : " . $data->id . "}";
    $viewParam = "{'dataset' : " . $dataset . ", 'modal_id' : 'modal-xl','button' : 'close', 'form_id' : 'viewRequisition', 'title' : 'Requisition Details', 'fname' : 'sendPR/viewRequistion'}";
    $viewButton = '<a class="btn btn-info btn-warning permission-modify" href="javascript:void(0)" datatip="Requisition Details" flow="down" onclick="ajaxCall(' . $viewParam . ');"><i class="fa fa-eye"></i></a>';
    
    if ($data->approveFlag == 1) {
        $approveParam = "{'id' : " . $data->id . ",'approve_type' : '1', 'modal_id' : 'modal-xl', 'form_id' : 'purchaseReq', 'title' : 'Requisition','fnsave' : 'savePurchaseRequest', 'savebtn_text':'Approve'}";
        $approvalBtn = '<a class="btn btn-facebook btn-edit permission-modify approveRequis" href="javascript:void(0)" datatip="Approve Requisition" flow="down" data-id="' . $data->id . '" onclick="edit(' . $approveParam . ');"><i class="fa fa-check" ></i></a>';
        
        $rejectionBtn = '<a class="btn btn-facebook rejectRequisition btn-edit permission-modify" data-id="' . $data->id . '" data-val="2" data-msg="Rejected"  href="javascript:void(0)" datatip="Reject Requisition" flow="down"><i class="ti-close"></i></a>';
    }
    //else{
        $editParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-xl', 'form_id' : 'purchaseReq', 'title' : 'Requisition','fnsave' : 'savePurchaseRequest'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit(' . $editParam . ');"><i class="fa fa-edit" ></i></a>';
        $deleteParam = $data->id . ",'Requisition'";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    //}
    $action = getActionButton($otherButton . $viewButton.$approvalBtn.$editButton . $deleteButton);
    $rqty = (!empty($data->unit_name)) ? floatVal($data->req_qty) . ' <small>' . $data->unit_name . '</small>' : floatVal($data->req_qty);
    $req_type = "";
    if ($data->req_type == 1) {$req_type = "Fresh";} else {$req_type = "Used"; }
    $used_at = (!empty($data->used_at) ? 'Vendor' : 'In House');
    $reqNo =sprintf("REQ%05d", $data->log_no);
    /*if($data->order_status != 4){
        $reqNo = '<span class="badge badge-pill badge-info m-1">Unapproved</span>';
    }*/
    return [$action, $data->priority_label,$reqNo, (!empty($data->req_date)) ? date("d-m-Y", strtotime($data->req_date)) : "", $data->full_name, (!empty($data->delivery_date)) ? date("d-m-Y", strtotime($data->delivery_date)) : "", $used_at, $data->request_by, $rqty, $req_type,$data->authDetail];
}

/* Gate Entry Data  */
function getGateEntryData($data){
    $trans_number = str_replace("/","_",$data->trans_prefix.$data->trans_no);
    $deleteParam = "'".$data->trans_prefix.$data->trans_no . "','Gate Entry'";
    $edit = "";
    $delete = "";
    if($data->status == 0):
        $edit = '<a href="' . base_url($data->controller . '/edit/' . $trans_number) . '" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
    $action = '';
    $action = getActionButton($edit  . $delete);
    return [$action,$data->sr_no,$data->trans_prefix.sprintf("%04d",$data->trans_no),formatDate($data->trans_date),$data->driver_name,$data->driver_contact,$data->vehicle_no,$data->vehicle_type_name,$data->transport_name,$data->total_item];
}

/* GateInward Data Data  */
function getGateInwardData($data){
    $action = '';$editButton='';$deleteButton="";$pallatePrint="";
    if($data->status == 0):
        $createGI = "";
        //$createGIParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'createGI', 'title' : 'Goods Receipt Note',fnsave: 'save',fnedit: 'createGI'}";
        //$createGI = '<a class="btn btn-success btn-edit permission-write" href="javascript:void(0)" datatip="Create GI" flow="down" onclick="edit('.$createGIParam.');"><i class="fa fa-plus" ></i></a>';
        $createGI = '<a class="btn btn-success btn-edit permission-write createGI" href="javascript:void(0)" data-id="'.$data->id.'" data-grn_type="'.$data->grn_type.'" data-modal_id = "modal-xl" data-form_id = "createGI" data-title = "Goods Receipt Note" datatip="Create GI" flow="down"><i class="fa fa-plus" ></i></a>';

        $action = getActionButton($createGI);
        return [$action,$data->sr_no,$data->trans_prefix.sprintf("%04d",$data->trans_no),formatDate($data->trans_date),$data->party_name,$data->item_name,$data->qty,$data->inv_no,$data->inv_date,$data->doc_no,$data->doc_date];
    else:
        $deleteParam = $data->id.",'Goods Receipt Note'";
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'editGI', 'title' : 'Update Goods Receipt Note'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	    // $pallatePrint = '<a href="'.base_url('gateInward/pallate_print/'.$data->id).'" type="button" class="btn btn-info " datatip="Pallate Print" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
	    $iirPrint = '<a href="'.base_url('gateInward/ir_print/'.$data->id).'" type="button" class="btn btn-primary" datatip="IIR Print" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
	    
	    $ge_no = (!empty($data->entry_prefix) && !empty($data->entry_no))? $data->entry_prefix.sprintf("%04d",$data->entry_no) : '';
	    $action = getActionButton($pallatePrint.$iirPrint.$editButton.$deleteButton);
        return [$action,$data->sr_no,$data->trans_prefix.sprintf("%04d",$data->trans_no),formatDate($data->trans_date),$data->party_name,$data->item_name,$data->inward_qty,$data->qty,$data->qty_kg,(!empty($data->po_no)?getPrefixNumber($data->po_prefix,$data->po_no):''),$ge_no];
    endif;
}

/*** Reginding Inspection Data */
function getRegrindingData($data)
{
    $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkRequest" value="'.$data->id.'"><label for="ref_id_'.$data->sr_no.'"></label>';
    return [$selectBox,$data->sr_no, sprintf("ISU%05d", $data->log_no), $data->full_name,$data->batch_no,$data->regranding_qty];
}

/*** Regrinding Challan */

function getRegrindingChallanData($data){
    $receiveBtn ='';
    if(empty($data->trans_status)){
        $receiveParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'receive_challan', 'title' : 'Receive Challan',fnsave: 'saveReceiveItem',fnedit: 'receiveChallan'}";
        $receiveBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Receive Challan" flow="down" onclick="edit('.$receiveParam.');"><i class="fas fa-paper-plane" ></i></a>';
    }
    
    $challanView = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'receive_challan', 'title' : 'Regrinding Challan','fnedit': 'challanView','button':'close'}";
    $challanViewBtn = '<a class="btn btn-facebook btn-edit permission-modify" href="javascript:void(0)" datatip="Challan Detail" flow="down" onclick="edit('.$challanView.');"><i class="fa fa-eye" ></i></a>';

    $printChallanBtn = '<a class="btn btn-primary" href="'.base_url($data->controller.'/regrindingChallanPrint/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    $action = getActionButton($printChallanBtn.$receiveBtn.$challanViewBtn);
    return [$action,$data->sr_no,formatDate($data->trans_date),getPrefixNumber($data->trans_prefix,$data->trans_no),$data->party_name];
}

/*Regrinding Reason Table Data */
function getRegrindingReasonData($data)
{
    $deleteParam = $data->id.",'RegrindingReason'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editRegrindingReason', 'title' : 'Update Regrinding Reason'}";
    
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->remark];
}

/*** Regrinding Challan */

function getRegrindingInspectionData($data){
    $inspBtn ='';
    $inspParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'receive_challan', 'title' : 'Inspection',fnsave: 'saveInspectedChallanItem',fnedit: 'inspectReceivedChallan'}";
    $inspBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Inspection" flow="down" onclick="edit('.$inspParam.');"><i class="fa fa-check" ></i></a>';

    $action = getActionButton($inspBtn);
    return [$action,$data->sr_no,formatDate($data->trans_date),getPrefixNumber($data->trans_prefix,$data->trans_no),$data->party_name,$data->full_name,$data->batch_no,formatDate($data->cod_date),$data->drg_rev_no,$data->rev_no,$data->grn_data,$data->regrinding_reason];
}

function getReRegrindingData($data){
    $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkRequest" value="'.$data->id.'"><label for="ref_id_'.$data->sr_no.'"></label>';
    return [$selectBox,$data->sr_no, formatDate($data->trans_date),$data->drg_rev_no,$data->party_name, $data->full_name,$data->batch_no,$data->rev_no,$data->grn_data];
}
?>
