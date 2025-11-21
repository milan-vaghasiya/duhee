<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getPurchaseDtHeader($page){
	/* Purchase Request Header */
    $data['purchaseRequest'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['purchaseRequest'][] = ["name"=>"#","style"=>"width:5%;"];
	// $data['purchaseRequest'][] = ["name"=>"Job No."];
    // $data['purchaseRequest'][] = ["name"=>"Material Type"];
    $data['purchaseRequest'][] = ["name"=>"Indent Date"];
    $data['purchaseRequest'][] = ["name"=>"Item Name"];
    $data['purchaseRequest'][] = ["name"=>"Item Description"];
    $data['purchaseRequest'][] = ["name"=>"UOM"];
    $data['purchaseRequest'][] = ["name"=>"Item Qty"];    
    $data['purchaseRequest'][] = ["name"=>"Item Make"];    
    $data['purchaseRequest'][] = ["name"=>"Remark"];    
    $data['purchaseRequest'][] = ["name"=>"Status"];

    /* Purchase Enquiry Header */
    $data['purchaseEnquiry'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['purchaseEnquiry'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['purchaseEnquiry'][] = ["name"=>"Enquiry No."];
    $data['purchaseEnquiry'][] = ["name"=>"Enquiry Date"];
    $data['purchaseEnquiry'][] = ["name"=>"Supplier Name"];
    $data['purchaseEnquiry'][] = ["name"=>"Item Description"];
    $data['purchaseEnquiry'][] = ["name"=>"Qty"];
    $data['purchaseEnquiry'][] = ["name"=>"Approved Price"];
    $data['purchaseEnquiry'][] = ["name"=>"Approved Date"];
    $data['purchaseEnquiry'][] = ["name"=>"Status"];
    $data['purchaseEnquiry'][] = ["name"=>"Remark"];

    /* Purchase Order Header */
    $data['purchaseOrder'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['purchaseOrder'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['purchaseOrder'][] = ["name"=>"Order No."];
    $data['purchaseOrder'][] = ["name"=>"Order Date"];
    $data['purchaseOrder'][] = ["name"=>"Supplier"];
    $data['purchaseOrder'][] = ["name"=>"Item Name"];
    $data['purchaseOrder'][] = ["name"=>"Rate"];
    $data['purchaseOrder'][] = ["name"=>"Order Qty"];
    $data['purchaseOrder'][] = ["name"=>"Received Qty"];
    $data['purchaseOrder'][] = ["name"=>"Pending Qty"];
    $data['purchaseOrder'][] = ["name"=>"Delivery Date"];
    
    /* Item Category Header */
    $data['familyGroup'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['familyGroup'][] = ["name" => "#", "style" => "width:5%;"];
    $data['familyGroup'][] = ["name" => "Family Name"];
    $data['familyGroup'][] = ["name" => "Remark"];

    
    /* Material Qc Header */
    $data['mqcParam'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['mqcParam'][] = ["name" => "#", "style" => "width:5%;"];
    $data['mqcParam'][] = ["name" => "Parameters"];
    $data['mqcParam'][] = ["name" => "Type"];
    $data['mqcParam'][] = ["name" => "Remark"];

    /* Price Amendment Header 
    Created By Mansee @ 26-11-2021 
    */
    $data['priceAmendment'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['priceAmendment'][] = ["name" => "#", "style" => "width:5%;"];
    $data['priceAmendment'][] = ["name" => "Po No"];
    $data['priceAmendment'][] = ["name" => "Item Name"];
    $data['priceAmendment'][] = ["name" => "New Price"];
    $data['priceAmendment'][] = ["name" => "Amendment Date"];
    $data['priceAmendment'][] = ["name" => "Effect From"];
    $data['priceAmendment'][] = ["name" => "Reason"];

    /* Purchase Order Schedule Header */
    /**
     *   Created By Mansee @ 26-11-2021 
     */
    $data['purchaseOrderSchedule'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['purchaseOrderSchedule'][] = ["name" => "#", "style" => "width:5%;"];
    $data['purchaseOrderSchedule'][] = ["name"=>"Order No."];
    $data['purchaseOrderSchedule'][] = ["name"=>"Order Date"];
    $data['purchaseOrderSchedule'][] = ["name"=>"Supplier"];
    $data['purchaseOrderSchedule'][] = ["name"=>"Item Name"];
    $data['purchaseOrderSchedule'][] = ["name"=>"Rate"];
    $data['purchaseOrderSchedule'][] = ["name"=>"Order Qty"];
    $data['purchaseOrderSchedule'][] = ["name"=>"Received Qty"];
    $data['purchaseOrderSchedule'][] = ["name"=>"Pending Qty"];
    $data['purchaseOrderSchedule'][] = ["name"=>"Delivery Date"];
    
    /* Price Agreement Header  Created By Meghavi */
    $data['priceAgreement'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['priceAgreement'][] = ["name" => "#", "style" => "width:5%;"];
    $data['priceAgreement'][] = ["name"=>"Order No."];
    $data['priceAgreement'][] = ["name"=>"Order Date"];
    $data['priceAgreement'][] = ["name"=>"Supplier"];
    $data['priceAgreement'][] = ["name"=>"Item Name"];
    $data['priceAgreement'][] = ["name"=>"Rate"];
    $data['priceAgreement'][] = ["name"=>"Order Qty"];
    $data['priceAgreement'][] = ["name"=>"Received Qty"];
    $data['priceAgreement'][] = ["name"=>"Pending Qty"];
    $data['priceAgreement'][] = ["name"=>"Delivery Date"];

    /* Purchase Indent Header */
    $masterCheckBox = '<input type="checkbox" id="masterSelect" class="filled-in chk-col-success BulkRequest" value=""><label for="masterSelect">ALL</label>';

    $data['purchaseIndent'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['purchaseIndent'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['purchaseIndent'][] = ["name"=>$masterCheckBox,"style"=>"width:10%;","textAlign"=>"center","orderable"=>"false"];
    $data['purchaseIndent'][] = ["name"=>"Indent Date"];
    $data['purchaseIndent'][] = ["name"=>"Indent No"];
    $data['purchaseIndent'][] = ["name"=>"Item Full Name"];
    $data['purchaseIndent'][] = ["name"=>"UOM"];
    $data['purchaseIndent'][] = ["name"=>"Lead Time"];
    $data['purchaseIndent'][] = ["name"=>"Min Order Qty"];    
    $data['purchaseIndent'][] = ["name"=>"Max Order Qty"];    
    $data['purchaseIndent'][] = ["name"=>"Req. Qty"];    
    $data['purchaseIndent'][] = ["name"=>"Urgency"];
    $data['purchaseIndent'][] = ["name"=>"Delivery Date"];
    $data['purchaseIndent'][] = ["name"=>"Planning Type"];
    $data['purchaseIndent'][] = ["name"=>"Remark"];
    $data['purchaseIndent'][] = ["name"=>"Authorizer of Requisition"]; 
    $data['purchaseIndent'][] = ["name"=>"Status"];
    
        /* Party Header */
    $data['parties'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['parties'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
	$data['parties'][] = ["name"=>"Company Name"];
    //$data['parties'][] = ["name"=>"Category"];
	$data['parties'][] = ["name"=>"Contact Person"];
    $data['parties'][] = ["name"=>"Contact No."];
    $data['parties'][] = ["name"=>"Party Code"];

    /* Common Fg Header */
    $data['commonFg'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['commonFg'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
    $data['commonFg'][] = ["name"=>"Item Code"];
    $data['commonFg'][] = ["name"=>"Item Name"];
    $data['commonFg'][] = ["name"=>"Remark"];
    
    return tableHeader($data[$page]);
}


/* Purchase Request Data  */
function getPurchaseRequestData($data){
    $purchaseOrder = $purchaseEnq =""; $approvereq=""; $closereq="";
    $viewParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'viewRequest', 'title' : 'Indent Detail','fnedit' : 'viewPurchaseReq','button' : 'close'}";

    $viewButton = '<a class="btn btn-dribbble btn-edit permission-modify" href="javascript:void(0)" datatip="Indent Detail" flow="down" onclick="edit('.$viewParam.');"><i class="fa fa-eye" ></i></a>';

    if($data->order_status == 0){
        $approvereq = '<a href="javascript:void(0)" class="btn btn-facebook approvePreq permission-modify" data-id="'.$data->id.'" data-val="2" data-msg="Approve" datatip="Approve Purchase Request" flow="down" ><i class="fa fa-check"></i></a>';
        $closereq = '<a href="javascript:void(0)" class="btn btn-dark closePreq permission-modify" data-id="'.$data->id.'" data-val="3" data-msg="Close" datatip="Close Purchase Request" flow="down" ><i class="ti-close"></i></a>';
    } elseif($data->order_status == 2){
        $purchaseOrder = '<a href="'.base_url('purchaseOrder/addPOFromRequest/'.$data->id).'" class="btn btn-warning btn-inv permission-write" datatip="Purchase Order" flow="down" ><i class="ti-file"></i></a>';
        $purchaseEnq = '<a href="'.base_url('purchaseEnquiry/addEnqFromRequest/'.$data->id).'" class="btn btn-success btn-enq permission-write" datatip="Purchase Enquiry" flow="down" ><i class="fa fa-question-circle"></i></a>';
        $closereq = '<a href="javascript:void(0)" class="btn btn-dark closePreq permission-modify" data-id="'.$data->id.'" data-val="3" data-msg="Close" datatip="Close Purchase Request" flow="down" ><i class="ti-close"></i></a>';
    }

    $action = getActionButton($viewButton.$approvereq.$closereq.$purchaseOrder.$purchaseEnq);

    //$mType = ($data->material_type == 0 ? "Consumable":"Raw Material");
    return [$action,$data->sr_no,(!empty($data->req_date))?date("d-m-Y",strtotime($data->req_date)):"",$data->item_name,$data->description,$data->unit_name,$data->req_qty,$data->make_brand,$data->remark,$data->order_status_label];
}
/* Purchase Request Data  */
function getPurchaseRequestData_old($data){
    $purchaseOrder = $purchaseEnq ="";

    if($data->order_status == 0){
        $purchaseOrder = '<a href="'.base_url('purchaseOrder/addPOFromRequest/'.$data->id).'" class="btn btn-warning btn-inv permission-write" datatip="Purchase Order" flow="down" ><i class="ti-file"></i></a>';

        $purchaseEnq = '<a href="'.base_url('purchaseEnquiry/addEnqFromRequest/'.$data->id).'" class="btn btn-success btn-enq permission-write" datatip="Purchase Enquiry" flow="down" ><i class="fa fa-question-circle"></i></a>';
    }
    
    $action = getActionButton($purchaseOrder.$purchaseEnq);

    $mType =  '' ;
    // if($data->material_type == 2 ){ $mType = 'Consumable'; }
    // if($data->material_type == 1 ){$mType = 'Finish Goods';}
    // if($data->material_type == 3 ){$mType = 'Raw Material';}
    // if($data->material_type == 5 ){$mType = 'Machine';}
    // if($data->material_type == 6 ){$mType = 'Instrument';}
    // if($data->material_type == 7 ) {$mType = 'Gauges';}
    switch($data->material_type){
        case 1: $mType = 'Finish Goods'; break;
        case 2: $mType = 'Consumable'; break;
        case 3: $mType = 'Raw Material'; break;
        case 4: $mType = 'Capital Goods'; break;
        case 5: $mType = 'Machine'; break;
        case 6: $mType = 'Instrument'; break;
        case 7: $mType = 'Gauges'; break;
        default :  $mType=''; break;
    }
    //$mType = ($data->material_type == 0 ? "Consumable":"Raw Material");
    return [$action,$data->sr_no,(!empty($data->job_no))?$data->job_prefix.$data->job_no:"General Issue",$mType,(!empty($data->req_date))?date("d-m-Y",strtotime($data->req_date)):"",$data->req_item_name,$data->req_qty];
}

/* Purchase Enquiry Data */
function getPurchaseEnquiryData($data){
    $deleteParam = $data->ref_id.",'Purchase Enquiry'";
    $closeParam = $data->ref_id.",'Purchase Enquiry'";
    $closeParam = $data->ref_id.",'Purchase Enquiry'";
    $enqComplete = "";$edit = "";$delete = "";$close = "";$reopen = ""; $approve="";$reject="";
    // if(empty($data->confirm_status)):
    //     //$enqComplete = '<a href="javascript:void(0)" class="btn btn-info btn-complete enquiryConfirmed permission-modify" data-id="'.$data->ref_id.'" data-party="'.$data->supplier_name.'" data-enqno="'.$data->enq_prefix.$data->enq_no.'" data-enqdate="'.date("d-m-Y",strtotime($data->enq_date)).'" data-button="both" data-modal_id="modal-lg" data-function="getEnquiryData" data-form_title="Purchase Enquiry Quotation" datatip="Quotation" flow="down"><i class="fa fa-check"></i></a>';
    //     //$edit = '<a href="'.base_url($data->controller.'/edit/'.$data->ref_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
    // else:
    //     if(empty($data->enq_status)):
    //         //$edit = '<a href="'.base_url('purchaseOrder/createOrder/'.$data->ref_id).'" class="btn btn-info btn-edit permission-write" datatip="Create Order" flow="down"><i class="ti-file"></i></a>';
    //     endif;
    // endif;

    if(($data->confirm_status == 0)):
        $reject = '<a href="javascript:void(0)" class="btn btn-success approvePEnquiry permission-modify" data-id="'.$data->ref_id.'" data-val="3" data-msg="Reject" datatip="Reject Enquiry" flow="down"><i class="fa fa-window-close"></i></a>';     
        $enqComplete = '<a href="javascript:void(0)" class="btn btn-info btn-complete enquiryConfirmed permission-modify" data-id="'.$data->ref_id.'" data-party="'.$data->supplier_name.'" data-enqno="'.$data->enq_prefix.$data->enq_no.'" data-enqdate="'.date("d-m-Y",strtotime($data->enq_date)).'" data-button="both" data-modal_id="modal-lg" data-function="getEnquiryData" data-form_title="Purchase Enquiry Quotation" datatip="Quotation" flow="down"><i class="fa fa-check"></i></a>';
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->ref_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" data-tooltip="tooltip" data-placement="bottom" data-original-title="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
    if(($data->confirm_status == 1)):
        $approve = '<a href="javascript:void(0)" class="btn btn-facebook approvePEnquiry permission-modify" data-id="'.$data->ref_id.'" data-val="2" data-msg="Approve" datatip="Approve Enquiry" flow="down"><i class="fa fa-check"></i></a>';
        $reject = '<a href="javascript:void(0)" class="btn btn-success approvePEnquiry permission-modify" data-id="'.$data->ref_id.'" data-val="3" data-msg="Reject" datatip="Reject Enquiry" flow="down"><i class="fa fa-window-close"></i></a>';     
    endif;    
      
    if(($data->confirm_status == 2)):
        $edit = '<a href="'.base_url('purchaseOrder/createOrder/'.$data->ref_id).'" class="btn btn-info btn-edit permission-write" datatip="Create Order" flow="down"><i class="ti-file"></i></a>';
    endif; 

    // if(empty($data->enq_ref_date)):
    //     $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" data-tooltip="tooltip" data-placement="bottom" data-original-title="Remove" flow="down"><i class="ti-trash"></i></a>';
    // /* else:
    //     if(empty($data->enq_status)):
    //         $close = '<a href="javascript:void(0)" class="btn btn-info" onclick="closeEnquiry('.$closeParam.');" datatip="Close Enquiry" flow="down"><i class="ti-close"></i></a>';
    //     else:
    //         $reopen = '<a href="javascript:void(0)" class="btn btn-info" onclick="reopenEnquiry('.$closeParam.');" datatip="Reopen Enquiry" flow="down"><i class="fa fa-retweet"></i></a>';
    //     endif; */
    // endif;

    $cnDate = (!empty($data->enq_ref_date))?date("d-m-Y",strtotime($data->enq_ref_date)):"";

    $action = getActionButton($approve.$reject.$enqComplete.$edit.$delete);//.$close.$reopen);
    return [$action,$data->sr_no,$data->enq_prefix.$data->enq_no,date("d-m-Y",strtotime($data->enq_date)),$data->supplier_name,$data->item_name,$data->confirm_qty,$data->confirm_rate, $cnDate,$data->status,$data->item_remark];
}

/* Purchase Order Table Data */
function getPurchaseOrderData($data){
    $deleteParam = $data->order_id.",'Purchase Order'";
    $grn = "";$edit = "";$delete = "";
    /** Updated By Karmi */
    if($data->order_status == 0 && $data->rec_qty < $data->qty):       
        
        //$grn = '<a href="javascript:void(0)" class="btn btn-info btn-inv createGrn permission-write" datatip="Create GIR" flow="down" data-party_id="'.$data->party_id.'" data-party_name="'.$data->party_name.'"><i class="ti-file"></i></a>';

        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->order_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
	//$edit = '<a href="'.base_url($data->controller.'/edit/'.$data->order_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

	$printBtn = '<a class="btn btn-info btn-edit permission-approve" href="'.base_url($data->controller.'/printPO/'.$data->order_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
	
	$action = getActionButton($printBtn.$grn.$edit.$delete);
		
    return [$action,$data->sr_no,getPrefixNumber($data->po_prefix,$data->po_no),formatDate($data->po_date),$data->party_name,$data->full_name,$data->price,$data->qty,$data->rec_qty,$data->pending_qty,formatDate($data->delivery_date)];
}

/* Item Category Table Data */
function getFamilyGroupData($data){
    $deleteParam = $data->id.",'Family Group'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editfamilyGroup', 'title' : 'Update familyGroup'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);    

    if($data->type == 1){
        return [$action,$data->sr_no,$data->family_name,$data->remark];
    }else{
        return [$action,$data->sr_no,$data->family_name,$data->type_name,$data->remark];   
    }
}

/* Price  Amendment data*/
/**
 *  Created By Mansee @ 26-11-2021 
 *  Updated By Mansee @ 27-11-2021 [ Note : activeParam Added ]
 */

function getPriceAmendmentData($data)
{
    $deleteParam = $data->id.",'Price Amendment'";

    // $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $editButton = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $activeParam="{'id' : ".$data->id.", 'item_id' : '".$data->item_id."', 'order_id' : '".$data->order_id."', 'effect_from' : '".$data->effect_from."','new_price':'".$data->new_price."'}";

    $activeButton = '<a class="btn btn-facebook btn-edit permission-modify" href="javascript:void(0)" datatip="Active" flow="down" onclick="activePrice('.$activeParam.');"><i class="ti-check" ></i></a>';

    $action = getActionButton($deleteButton.$activeButton);    

    return [$action,$data->sr_no,getPrefixNumber($data->po_prefix,$data->po_no),$data->item_name,$data->new_price,formatDate($data->amendment_date),formatDate($data->effect_from),$data->reason];
}

/* Purchase Order  Schedule Table Data */
/**
 *   Created By Mansee @ 26-11-2021 
 */
function getPurchaseOrderScheduleData($data){
    $deleteParam = $data->order_id.",'Purchase Order'";
    $grn = "";$edit = "";$delete = "";
    if($data->order_status == 0):
        $grn = '<a href="javascript:void(0)" class="btn btn-info btn-inv createGrn permission-write" datatip="Add Schedule" flow="down" data-party_id="'.$data->party_id.'" data-party_name="'.$data->party_name.'"><i class="ti-file"></i></a>';

        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->order_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
	
	$printBtn = '<a class="btn btn-success btn-edit permission-approve" href="'.base_url($data->controller.'/printPO/'.$data->order_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
	
	$action = getActionButton($printBtn.$grn.$edit.$delete);
		
    return [$action,$data->sr_no,getPrefixNumber($data->po_prefix,$data->po_no),formatDate($data->po_date),$data->party_name,$data->item_name,$data->price,$data->qty,$data->rec_qty,$data->pending_qty,formatDate($data->delivery_date)];
}

/* Price Agreement Table Data  Created By Meghavi*/
function getPriceAgreementData($data){
    $deleteParam = $data->order_id.",'Purchase Order'";
    $grn = "";$edit = "";$delete = "";
    if($data->order_status == 0 && $data->rec_qty < $data->qty):       
        
        $grn = '<a href="javascript:void(0)" class="btn btn-info btn-inv createGrn permission-write" datatip="Create GRN" flow="down" data-party_id="'.$data->party_id.'" data-party_name="'.$data->party_name.'"><i class="ti-file"></i></a>';

        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->order_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
	
	$printBtn = '<a class="btn btn-success btn-edit permission-approve" href="'.base_url($data->controller.'/printPO/'.$data->order_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
	
	$action = getActionButton($printBtn.$grn.$edit.$delete);
		
    return [$action,$data->sr_no,getPrefixNumber($data->po_prefix,$data->po_no),formatDate($data->po_date),$data->party_name,$data->item_name,$data->price,$data->qty,$data->rec_qty,$data->pending_qty,formatDate($data->delivery_date)];
}

/* Purchase Request Data  */
function getPurchaseIndentData($data){
    $purchaseOrder = $purchaseEnq =""; $approvereq=""; $closereq="";
    $viewParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'viewRequest', 'title' : 'Indent Detail','fnedit' : 'viewPurchaseReq','button' : 'close'}";

    $viewButton = '<a class="btn btn-dribbble btn-edit permission-modify" href="javascript:void(0)" datatip="Indent Detail" flow="down" onclick="edit('.$viewParam.');"><i class="fa fa-eye" ></i></a>';
    $selectBox ="";
    if($data->order_status ==0){
        $approvereq = '<a href="javascript:void(0)" class="btn btn-facebook approvePreq permission-modify" data-id="'.$data->id.'" data-val="2" data-msg="Approve" datatip="Approve Purchase Request" flow="down" ><i class="fa fa-check"></i></a>';
        $closereq = '<a href="javascript:void(0)" class="btn btn-dark closePreq permission-modify" data-id="'.$data->id.'" data-val="3" data-msg="Close" datatip="Close Purchase Request" flow="down" ><i class="ti-close"></i></a>';
    
    }

    elseif($data->order_status == 2){
        $purchaseOrder = '<a href="'.base_url('purchaseOrder/addPOFromRequest/'.$data->id).'" class="btn btn-warning btn-inv permission-write" datatip="Purchase Order" flow="down" ><i class="ti-file"></i></a>';
        $purchaseEnq = '<a href="'.base_url('purchaseEnquiry/addEnqFromRequest/'.$data->id).'" class="btn btn-success btn-enq permission-write" datatip="Purchase Enquiry" flow="down" ><i class="fa fa-question-circle"></i></a>';
        $closereq = '<a href="javascript:void(0)" class="btn btn-dark closePreq permission-modify" data-id="'.$data->id.'" data-val="3" data-msg="Close" datatip="Close Purchase Request" flow="down" ><i class="ti-close"></i></a>';
        $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkRequest" value="'.$data->id.'"><label for="ref_id_'.$data->sr_no.'"></label>';

    }
    $urgency="";
    if($data->urgency==1){
        $urgency="Medium";
    }
    elseif($data->urgency==2){
        $urgency="High";
    }
    else{
        $urgency="Low";
    }
    $action = getActionButton($viewButton.$approvereq.$closereq.$purchaseOrder.$purchaseEnq);
    
    //$mType = ($data->material_type == 0 ? "Consumable":"Raw Material");
    return [$action,$data->sr_no,$selectBox,(!empty($data->req_date))?date("d-m-Y",strtotime($data->req_date)):"",sprintf("IND%03d",$data->log_no),$data->full_name,$data->unit_name,$data->lead_time,$data->min_qty,$data->max_qty,$data->req_qty,$urgency,formatDate($data->delivery_date),$data->plan_type,$data->remark,$data->emp_name,$data->order_status_label];
   
}

function getPartyData($data){

    $title = ($data->party_category == 1 ? "Customer": ($data->party_category == 2 ? "Vendor":"Supplier"));
    $deleteParam = $data->id.",'".$title."'";
    $editParam = "{'id' : ".$data->id.", 'party_category': ".$data->party_category.", 'modal_id' : 'modal-xl', 'form_id' : 'editParty', 'title' : 'Update ".$title."'}";
    $approvalParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'partyApproval', 'title' : 'Party Approval', 'fnedit' : 'partyApproval', 'fnsave' : 'savePartyApproval'}";

    $approvalButton = '<a class="btn btn-info btn-approval permission-approve" href="javascript:void(0)" datatip="Party Approval" flow="down" onclick="edit('.$approvalParam.');"><i class="fa fa-check" ></i></a>';

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $gstJsonBtn="";$contactBtn="";$appointmentBtn="";$followupBtn="";
    if($data->party_category == 1):
        $gstParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'close', 'form_id' : 'gstDetail', 'title' : 'GST Detail', 'fnedit' : 'getGstDetail', 'fnsave' : 'saveGst'}";
        $gstJsonBtn = '<a class="btn btn-warning btn-contact permission-modify" href="javascript:void(0)" datatip="GST Detail" flow="down" onclick="edit('.$gstParam.');"><i class="fab fa-google"></i></a>';

        $contactParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'close', 'form_id' : 'contactDetail', 'title' : 'Contact Detail', 'fnedit' : 'getContactDetail', 'fnsave' : 'saveContact'}";
        $contactBtn = '<a class="btn btn-info btn-contact permission-modify" href="javascript:void(0)" datatip="Contact Detail" flow="down" onclick="edit('.$contactParam.');"><i class="fa fa-address-book"></i></a>';

        if($data->party_type == 2)
        {            
            $appointmentParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'both', 'form_id' : 'getAppointments', 'title' : 'Appointment Form', 'fnedit' : 'getAppointments', 'fnsave' : 'setAppointment'}";
            $appointmentBtn = '<a class="btn btn-primary btn-appointment permission-modify" href="javascript:void(0)" datatip="Appointment Form" flow="down" onclick="edit('.$appointmentParam.');"><i class="far fa-calendar-check"></i></a>';

            $followupParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'both', 'form_id' : 'getFollowup', 'title' : 'Followup Form', 'fnedit' : 'getFollowup', 'fnsave' : 'saveFollowup'}";
            $followupBtn = '<a class="btn btn-dark btn-followup permission-modify" href="javascript:void(0)" datatip="Followup Form" flow="down" onclick="edit('.$followupParam.');"><i class="fas fa-clipboard-check"></i></a>';
        }
    endif;
    $action = getActionButton($appointmentBtn.$followupBtn.$contactBtn.$gstJsonBtn.$approvalButton.$editButton.$deleteButton);

    //$category = ($data->party_category == 1?"Customer":($data->party_category == 3?"Supplier":"Vendor"));
    if($data->party_category == 1):
        $responseData = [$action,$data->sr_no,$data->party_name,$data->contact_person,$data->party_mobile,$data->party_code,$data->currency];
    elseif($data->party_category == 2):
        $responseData = [$action,$data->sr_no,$data->party_name,$data->contact_person,$data->party_mobile,$data->party_address];
    else:
        $responseData = [$action,$data->sr_no,$data->party_name,$data->contact_person,$data->party_mobile,$data->party_code];
    endif;
    return $responseData;
}

function getCommonFgData($data){
    $deleteParam = $data->id.",'Common Fg'";

    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editCommonFg', 'title' : 'Update Common Fg'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
    $prcParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'setCommonFg', 'title' : 'Set Common Fg Process', 'fnedit' : 'setProductProcess', 'button' : 'close'}";
	
	$prcButton = '<a class="btn btn-warning btn-edit permission-modify" href="javascript:void(0)" datatip="Set Process" flow="down" onclick="edit('.$prcParam.');"><i class="fa fa-list" ></i></a>';

    $action = getActionButton($prcButton.$editButton.$deleteButton);    
   
    return [$action,$data->sr_no,$data->item_code,$data->item_name,$data->description];   
}
?>