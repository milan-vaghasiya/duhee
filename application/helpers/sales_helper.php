<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getSalesDtHeader($page){	
    /* Party Header */
    $data['customer'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['customer'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
	$data['customer'][] = ["name"=>"Company Name"];
	$data['customer'][] = ["name"=>"Contact Person"];
    $data['customer'][] = ["name"=>"Contact No."];
    $data['customer'][] = ["name"=>"Party Code"];
    $data['customer'][] = ["name"=>"Currency"];
    
    /* Sales Enquiry Header */
	$data['salesEnquiry'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['salesEnquiry'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['salesEnquiry'][] = ["name"=>"Enq. No."];
    $data['salesEnquiry'][] = ["name"=>"Enq. Date"];
    $data['salesEnquiry'][] = ["name"=>"Customer Name"];
    $data['salesEnquiry'][] = ["name"=>"Item Name"];
    $data['salesEnquiry'][] = ["name"=>"Qty"];
    $data['salesEnquiry'][] = ["name"=>"Status"];
    $data['salesEnquiry'][] = ["name"=>"Quoted","style"=>"width:5%;","textAlign"=>"center"];
    $data['salesEnquiry'][] = ["name"=>"Feasible","style"=>"width:5%;","textAlign"=>"center"];
    $data['salesEnquiry'][] = ["name"=>"Reason","style"=>"width:5%;","textAlign"=>"center"];
    $data['salesEnquiry'][] = ["name"=>"Remark"];

	/* Sales Quotation Header */
    $data['salesQuotation'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['salesQuotation'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['salesQuotation'][] = ["name"=>"Quote No."];
    $data['salesQuotation'][] = ["name"=>"Quote Date"];
    $data['salesQuotation'][] = ["name"=>"Customer Name"];
    //$data['salesQuotation'][] = ["name"=>"Product Name"];
    //$data['salesQuotation'][] = ["name"=>"Qty"];
    //$data['salesQuotation'][] = ["name"=>"Quote Price"];
    //$data['salesQuotation'][] = ["name"=>"Confirmed Price"];
    $data['salesQuotation'][] = ["name"=>"Confirmed Date"];
    $data['salesQuotation'][] = ["name"=>"Enq. No."];

    /* Sales Order Header */
    $data['salesOrder'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['salesOrder'][] = ["name"=>"#","textAlign"=>"center"];
	$data['salesOrder'][] = ["name"=>"SO. No.","textAlign"=>"center"];
    $data['salesOrder'][] = ["name"=>"SO. Entry Date","style"=>"width:10%;","textAlign"=>"center"];
    $data['salesOrder'][] = ["name"=>"Slaes Type","textAlign"=>"center"];
    $data['salesOrder'][] = ["name"=>"Customer Name"];
    $data['salesOrder'][] = ["name"=>"Cust. PO.NO."];
	$data['salesOrder'][] = ["name"=>"Quot. No."];
    //$data['salesOrder'][] = ["name"=>"Product"];
    //$data['salesOrder'][] = ["name"=>"Order Qty.","textAlign"=>"center"];
    //$data['salesOrder'][] = ["name"=>"Dispatch Qty.","textAlign"=>"center"];
    //$data['salesOrder'][] = ["name"=>"Pending Qty.","textAlign"=>"center","textAlign"=>"center"];
    $data['salesOrder'][] = ["name"=>"Delivery Date","textAlign"=>"center"]; 
    $data['salesOrder'][] = ["name"=>"Status","textAlign"=>"center"]; 

    /* Proforma Invoice Header */
    $data['proformaInvoice'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['proformaInvoice'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['proformaInvoice'][] = ["name"=>"Invoice No."];
    $data['proformaInvoice'][] = ["name"=>"Invoice Date"];
    $data['proformaInvoice'][] = ["name"=>"Customer Name"]; 
    $data['proformaInvoice'][] = ["name"=>"Product Name"]; 
    $data['proformaInvoice'][] = ["name"=>"Product Amount"]; 
    $data['proformaInvoice'][] = ["name"=>"Bill Amount"]; 
    

    /* Sales Invoice Header */
    $data['salesInvoice'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['salesInvoice'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['salesInvoice'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['salesInvoice'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['salesInvoice'][] = ["name"=>"Invoice Type","textAlign"=>"center"];
    $data['salesInvoice'][] = ["name"=>"Customer Name"]; 
    $data['salesInvoice'][] = ["name"=>"Cust. PO.NO."];
    $data['salesInvoice'][] = ["name"=>"Bill Amount","textAlign"=>"right"];  


    /* Cycle Time Header */   
    /* $data['cycleTime'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['cycleTime'][] = ["name"=>"Part Code"];
    $data['cycleTime'][] = ["name"=>"Manage Time","style"=>"width:20%;"]; */

    /* Tool Consumption Header */
    /* $data['toolConsumption'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['toolConsumption'][] = ["name"=>"Tool Description"];
    $data['toolConsumption'][] = ["name"=>"Action","style"=>"width:20%;"]; */    
    
    /* Sales Invoice Header */
    $data['jobworkScrapInvoice'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['jobworkScrapInvoice'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['jobworkScrapInvoice'][] = ["name"=>"Invoice No."];
    $data['jobworkScrapInvoice'][] = ["name"=>"Invoice Date"];
    $data['jobworkScrapInvoice'][] = ["name"=>"Customer Name"]; 
    $data['jobworkScrapInvoice'][] = ["name"=>"Bill Amount"]; 
	
	/* Responsibility Header */
    $data['responsibility'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['responsibility'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['responsibility'][] = ["name"=>"Remark"];
   
    /* Purchase Enquiry Header */
    $data['rfq'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['rfq'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['rfq'][] = ["name"=>"Enquiry No."];
    $data['rfq'][] = ["name"=>"Enquiry Date"];
    $data['rfq'][] = ["name"=>"Supplier Name"];
    $data['rfq'][] = ["name"=>"Item Description"];
    $data['rfq'][] = ["name"=>"Qty"];
    $data['rfq'][] = ["name"=>"Status"];

    /* Delivery Challan Header */
    $data['deliveryChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['deliveryChallan'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['deliveryChallan'][] = ["name"=>"DC. No.","textAlign"=>"center"];
    $data['deliveryChallan'][] = ["name"=>"DC. Date","textAlign"=>"center"];
    $data['deliveryChallan'][] = ["name"=>"Customer Name"]; 
    $data['deliveryChallan'][] = ["name"=>"Invoice No."]; 
    //$data['deliveryChallan'][] = ["name"=>"Product Name"]; 
    //$data['deliveryChallan'][] = ["name"=>"Dispatch Qty.","textAlign"=>"center"];

    /* Commercial Packing Header */
    $data['commercialPacking'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['commercialPacking'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['commercialPacking'][] = ["name"=>"Com. Pac. No.","textAlign"=>"center"];
    $data['commercialPacking'][] = ["name"=>"Packing No.","textAlign"=>"center"];
	$data['commercialPacking'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['commercialPacking'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['commercialPacking'][] = ["name"=>"Customer Name"]; 
    $data['commercialPacking'][] = ["name"=>"Total Net Weight","textAlign"=>"center"]; 
    $data['commercialPacking'][] = ["name"=>"Total Gross Weight","textAlign"=>"center"]; 
	
	/* Commercial Invoice Header */
    $data['commercialInvoice'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['commercialInvoice'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['commercialInvoice'][] = ["name"=>"Com. INV. No.","textAlign"=>"center"];
    $data['commercialInvoice'][] = ["name"=>"Packing No.","textAlign"=>"center"];
	$data['commercialInvoice'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['commercialInvoice'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['commercialInvoice'][] = ["name"=>"Customer Name"]; 
    $data['commercialInvoice'][] = ["name"=>"Net Amount","textAlign"=>"center"]; 

    /* Commercial Packing Header */
    $data['customPacking'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['customPacking'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['customPacking'][] = ["name"=>"Cum. Pac. No.","textAlign"=>"center"];
    $data['customPacking'][] = ["name"=>"Packing No.","textAlign"=>"center"];
	$data['customPacking'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['customPacking'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['customPacking'][] = ["name"=>"Customer Name"]; 
    $data['customPacking'][] = ["name"=>"Total Net Weight","textAlign"=>"center"]; 
    $data['customPacking'][] = ["name"=>"Total Gross Weight","textAlign"=>"center"]; 

    /* Custom Invoice Header */
    $data['customInvoice'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['customInvoice'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['customInvoice'][] = ["name"=>"Cum. INV. No.","textAlign"=>"center"];
    $data['customInvoice'][] = ["name"=>"Packing No.","textAlign"=>"center"];
	$data['customInvoice'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['customInvoice'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['customInvoice'][] = ["name"=>"Customer Name"]; 
    $data['customInvoice'][] = ["name"=>"Net Amount","textAlign"=>"center"];

    /* Request Header */
	$data['dispatchRequest'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['dispatchRequest'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['dispatchRequest'][] = ["name"=>"Req. Date"];
	$data['dispatchRequest'][] = ["name"=>"Req. No."];
    $data['dispatchRequest'][] = ["name"=>"S.O. No."];
	$data['dispatchRequest'][] = ["name"=>"Customer"];
	$data['dispatchRequest'][] = ["name"=>"Item Name"];
	$data['dispatchRequest'][] = ["name"=>"Req. Qty","textAlign"=>"center"];
    $data['dispatchRequest'][] = ["name"=>"Ch/Inv Qty","style"=>"width:250px;","textAlign"=>"center"];
	$data['dispatchRequest'][] = ["name"=>"Pending Qty","textAlign"=>"center"];
    $data['dispatchRequest'][] = ["name"=>"Remark"];

    /* Dispatch Domestic */
    $data['dispatchDomestic'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['dispatchDomestic'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['dispatchDomestic'][] = ["name"=>"Challan Date"];
	$data['dispatchDomestic'][] = ["name"=>"Challan No."];
	$data['dispatchDomestic'][] = ["name"=>"Customer"];
	$data['dispatchDomestic'][] = ["name"=>"Item Name"];
	$data['dispatchDomestic'][] = ["name"=>"Challan Qty","textAlign"=>"center"];

    /* Lead Followup Header */
    $data['leadFollowup'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['leadFollowup'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
    $data['leadFollowup'][] = ["name"=>"Followup Date"];
    $data['leadFollowup'][] = ["name"=>"Mode"];
    $data['leadFollowup'][] = ["name"=>"Contact Person"];
    $data['leadFollowup'][] = ["name"=>"Note"];
    $data['leadFollowup'][] = ["name"=>"Company Name"];
    $data['leadFollowup'][] = ["name"=>"Party Code"];
    
    /* Lead Appointment Header */
    $data['leadAppointment'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['leadAppointment'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
    $data['leadAppointment'][] = ["name"=>"Appointment Date & Time"];
    $data['leadAppointment'][] = ["name"=>"Mode"];
    $data['leadAppointment'][] = ["name"=>"Contact Person"];
    $data['leadAppointment'][] = ["name"=>"Purpose"];
    $data['leadAppointment'][] = ["name"=>"Status"];
    $data['leadAppointment'][] = ["name"=>"Note"];
    $data['leadAppointment'][] = ["name"=>"Company Name"];
    $data['leadAppointment'][] = ["name"=>"Party Code"];

    
	/* SQ Followup Header */
    $data['sqFollowup'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['sqFollowup'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['sqFollowup'][] = ["name"=>"Followup Date"];
	$data['sqFollowup'][] = ["name"=>"Quote No."];
    $data['sqFollowup'][] = ["name"=>"Mode"];
    $data['sqFollowup'][] = ["name"=>"Contact Person"];
    $data['sqFollowup'][] = ["name"=>"Note"];
    $data['sqFollowup'][] = ["name"=>"Quote Date"];
    $data['sqFollowup'][] = ["name"=>"Customer Name"];
    
	/* SQ Appointment Header */
    $data['sqAppointment'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['sqAppointment'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['sqAppointment'][] = ["name"=>"Appointment Date & Time"];
	$data['sqAppointment'][] = ["name"=>"Quote No."];
    $data['sqAppointment'][] = ["name"=>"Mode"];
    $data['sqAppointment'][] = ["name"=>"Contact Person"];
    $data['sqAppointment'][] = ["name"=>"Purpose"];
    $data['sqAppointment'][] = ["name"=>"Status"];
    $data['sqAppointment'][] = ["name"=>"Note"];
    $data['sqAppointment'][] = ["name"=>"Quote Date"];
    $data['sqAppointment'][] = ["name"=>"Customer Name"];
 
    /* SE Followup Header */
	$data['seFollowup'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['seFollowup'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['seFollowup'][] = ["name"=>"Followup Date"];
	$data['seFollowup'][] = ["name"=>"Enq. No."];
    $data['seFollowup'][] = ["name"=>"Enq. Date"];
    $data['seFollowup'][] = ["name"=>"Customer Name"];
    $data['seFollowup'][] = ["name"=>"Item Name"];
    $data['seFollowup'][] = ["name"=>"Qty"];
    $data['seFollowup'][] = ["name"=>"Mode"];
    $data['seFollowup'][] = ["name"=>"Contact Person"];
    $data['seFollowup'][] = ["name"=>"Note"];

    /* SE Appointment Header */
	$data['seAppointment'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['seAppointment'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['seAppointment'][] = ["name"=>"Appointment Date & Time"];
	$data['seAppointment'][] = ["name"=>"Enq. No."];
    $data['seAppointment'][] = ["name"=>"Enq. Date"];
    $data['seAppointment'][] = ["name"=>"Customer Name"];
    $data['seAppointment'][] = ["name"=>"Item Name"];
    $data['seAppointment'][] = ["name"=>"Qty"];
    $data['seAppointment'][] = ["name"=>"Mode"];
    $data['seAppointment'][] = ["name"=>"Contact Person"];
    $data['seAppointment'][] = ["name"=>"Purpose"];
    $data['seAppointment'][] = ["name"=>"Status"];
    $data['seAppointment'][] = ["name"=>"Note"];

	return tableHeader($data[$page]);
}

/* Sales Enquiry Table Data */
function getSalesEnquiryData($data){
    $deleteParam = $data->trans_main_id.",'Sales Enquiry'";
    $closeParam = $data->trans_main_id.",'Sales Enquiry'";
    $edit = "";$delete = "";$close = "";$reopen = "";$quotation="";   $changeParty="";$feasibleBtn="";$appointmentBtn="";$followupBtn="";
    if(empty($data->trans_status)):
        $feasibleBtn = '<a href="javascript:void(0)" class="btn btn-warning btn-delete permission-remove" onclick="feasibilityRequest('.$data->id.',1);" datatip="Fesibility Request" flow="down"><i class="fas fa-paper-plane"></i></a>'; 

        $quotation = '<a href="'.base_url('salesQuotation/createQuotation/'.$data->trans_main_id).'" class="btn btn-info permission-write" datatip="Create Quotation" flow="down"><i class="fa fa-file-alt"></i></a>';
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
        $feasibleParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md','button' : 'both', 'form_id' : 'feasibleForm', 'title' : 'Feasible Form', 'fnedit' : 'feasibleForm', 'fnsave' : 'saveFeasibleForm'}";
        $feasibleBtn = '<a class="btn btn-dark btn-feasible permission-modify" href="javascript:void(0)" datatip="Feasible Form" flow="down" onclick="edit('.$feasibleParam.');"><i class="ti ti-check-box"></i></a>';

    
        $appointmentParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'both', 'form_id' : 'getAppointments', 'title' : 'Appointment Form', 'fnedit' : 'getAppointments', 'fnsave' : 'setAppointment'}";
        $appointmentBtn = '<a class="btn btn-info btn-appointment permission-modify" href="javascript:void(0)" datatip="Appointment Form" flow="down" onclick="edit('.$appointmentParam.');"><i class="far fa-calendar-check"></i></a>';
    
        $followupParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'both', 'form_id' : 'getFollowup', 'title' : 'Followup Form', 'fnedit' : 'getFollowup', 'fnsave' : 'saveFollowup'}";
        $followupBtn = '<a class="btn btn-primary btn-followup permission-modify" href="javascript:void(0)" datatip="Followup Form" flow="down" onclick="edit('.$followupParam.');"><i class="fas fa-clipboard-check"></i></a>';


    elseif($data->trans_status == 1 && $data->notFisibalTab != 2):
        $changeParty = '<a href="javascript:void(0);" class="btn btn-warning changeParty" data-trans_main_id="'.$data->trans_main_id.'" data-enq_no="'.(getPrefixNumber($data->trans_prefix,$data->trans_no)).'" data-party_id="'.$data->party_id.'"  datatip="Change Party" flow="down"><i class="fas fa-retweet"></i></a>';
    else:
        $edit = '';//'<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        /*if($data->trans_status == 1):
            $close = '<a href="javascript:void(0)" class="btn btn-dark" onclick="closeEnquiry('.$closeParam.');" datatip="Close Enquiry" flow="down"><i class="ti-close"></i></a>';
        else:
            $reopen = '<a href="javascript:void(0)" class="btn btn-warning" onclick="reopenEnquiry('.$closeParam.');" datatip="Reopen Enquiry" flow="down"><i class="fa fa-retweet"></i></a>';
        endif;*/
    endif;
    
    $quotedCount = ''; $fisibleCount ='';
    if($data->notFisibalTab != 2){
        if(!empty($data->quotedCount) > 0){
            $quotedCount = '<span class="badge badge-pill badge-success m-1">Quoted</span>';
        }
        else{
            $quotedCount = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
        }
        if($data->fisibleCount == 'Yes'){
            $fisibleCount = 'Yes';
        }
    } else {
        $quotedCount = "-";
        $fisibleCount = "-";
    }
    $action = getActionButton($appointmentBtn.$followupBtn.$feasibleBtn.$quotation.$edit.$delete.$close.$reopen);
    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->item_name,$data->qty,$data->status,$quotedCount,$data->feasible,$data->feasibleReason,$data->remark];
}

/* Sales Quotation Table Data */
function getSalesQuotationData($data){
    $deleteParam = $data->trans_main_id.",'Sales Quotation'";
    $closeParam = $data->trans_main_id.",'Sales Quotation'";
    $confirm = "";$edit = "";$delete = "";$saleOrder ="";$printBtn = '';$revision = ''; $mailBtn='';
    $ref_no = str_replace('/','_',getPrefixNumber($data->trans_prefix,$data->trans_no));
    $emailParam = $data->trans_main_id.",'".$ref_no."'";
    if(empty($data->confirm_by)):
        $confirm = '<a href="javascript:void(0)" class="btn btn-info confirmQuotation permission-write" data-id="'.$data->trans_main_id.'" data-quote_id="'.$data->trans_main_id.'"  data-party="'.$data->party_name.'" data-customer_id="'.$data->party_id.'" data-quote_no="'.getPrefixNumber($data->trans_prefix,$data->trans_no).'" data-quotation_date="'.date("d-m-Y",strtotime($data->trans_date)).'" data-button="both" data-modal_id="modal-lg" data-function="getQuotationItems" data-form_title="Confirm Quotation" datatip="Confirm Quotation" flow="down"><i class="fa fa-check"></i></a>';
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
		
		// $followup='<a href="javascript:void(0)" class="btn btn-warning addFolloUp permission-write" data-id="'.$data->trans_main_id.'" data-button="both" data-modal_id="modal-lg" data-function="getFollowUp" data-form_title="Follow Up" datatip="Follow Up" flow="down"><i class="fa fa-list-ul"></i></a>';
        
		
        $revision = '<a href="'.base_url($data->controller.'/reviseQuotation/'.$data->trans_main_id).'" class="btn btn-primary btn-edit permission-modify" datatip="Revision" flow="down"><i class="fa fa-retweet"></i></a>';
		$delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    else:
        //if(empty($data->trans_status)):
            $saleOrder = '<a href="javascript:void(0)" class="btn btn-info createSalesOrder permission-write" data-id="'.$data->trans_main_id.'" data-quote_id="'.$data->trans_main_id.'"  data-party="'.$data->party_name.'" data-customer_id="'.$data->party_id.'" data-quote_no="'.getPrefixNumber($data->trans_prefix,$data->trans_no).'" data-quotation_date="'.date("d-m-Y",strtotime($data->trans_date)).'" data-button="both" data-modal_id="modal-lg"  data-form_title="Create Sales Order" datatip="Create Order" flow="down"><i class="fa fa-file-alt"></i></a>';
            //$saleOrder = '<a href="'.base_url('salesOrder/createOrder/'.$data->trans_main_id).'" class="btn btn-info permission-write" datatip="Create Order" flow="down"><i class="fa fa-file-alt"></i></a>';
        //endif;
    endif;
    
    $appointmentParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'both', 'form_id' : 'getAppointments', 'title' : 'Appointment Form', 'fnedit' : 'getAppointments', 'fnsave' : 'setAppointment'}";
    $appointmentBtn = '<a class="btn btn-warning btn-appointment permission-modify" href="javascript:void(0)" datatip="Appointment Form" flow="down" onclick="edit('.$appointmentParam.');"><i class="far fa-calendar-check"></i></a>';

    $followupParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'both', 'form_id' : 'getFollowup', 'title' : 'Followup Form', 'fnedit' : 'getFollowup', 'fnsave' : 'saveFollowup'}";
    $followupBtn = '<a class="btn btn-dark btn-followup permission-modify" href="javascript:void(0)" datatip="Followup Form" flow="down" onclick="edit('.$followupParam.');"><i class="fas fa-clipboard-check"></i></a>';

    $mailBtn = '<a class="btn btn-info permission-read" href="javascript:void(0)" onclick="sendEmail('.$emailParam.')" datatip="Send Mail" flow="down"><i class="fas fa-envelope" ></i></a>';
	$mailBtn='';
	$printBtn = '<a class="btn btn-success btn-edit permission-approve" href="'.base_url($data->controller.'/printQuotation/'.$data->trans_main_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    $printRevisionBtn = '<a class="btn btn-facebook btn-edit permission-approve createSalesQuotation"  datatip="View Revised Quatation" data-id="'.$data->trans_main_id.'" data-sq_no="'.getPrefixNumber($data->trans_prefix,$data->trans_no).'" flow="down"><i class="fas fa-eye" ></i></a>';
    $action = getActionButton($printBtn.$printRevisionBtn.$confirm.$appointmentBtn.$followupBtn.$mailBtn.$revision.$edit.$delete.$saleOrder);
	
    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no).' (Rev. No.'.$data->quote_rev_no.')',date("d-m-Y",strtotime($data->trans_date)),$data->party_name,(!empty($data->cod_date))?date("d-m-Y",strtotime($data->cod_date)):"",$data->ref_no];
}

/* Sales Order Table Data */
function getSalesOrderData($data){
    $deleteParam = $data->trans_main_id.",'Sales Order'";
    $view = ""; $edit = ""; $delete = ""; $complete = ""; $invoiceCreate = "";$dispatch = ""; $approve='';$invoice = "";$itemList='';$mailBtn='';
    $ref_no = str_replace('/','_',getPrefixNumber($data->trans_prefix,$data->trans_no));
    $emailParam = $data->trans_main_id.",'".$ref_no."'";
    $closeParam = "{'id' : ".$data->trans_main_id.", 'modal_id' : 'modal-lg', 'form_id' : 'closeSalesOrder', 'title' : 'Close Sales Order', 'fnEdit' : 'closeSalesOrder', 'fnsave' : 'saveCloseSO'}";
    $printBtn = '<a class="btn btn-dribbble btn-edit permission-read" href="'.base_url($data->controller.'/salesOrder_pdf/'.$data->trans_main_id).'" target="_blank" datatip="Print Sales Order" flow="down"><i class="fas fa-print" ></i></a>';
	
    if(empty($data->trans_status)):
        if(!empty($data->is_approve == 0)){
            // $approve = '<a href="javascript:void(0)" class="btn btn-facebook approveSOrder permission-approve" data-id="'.$data->trans_main_id.'" data-val="1" data-msg="Approve" datatip="Approve Order" flow="down" ><i class="fa fa-check" ></i></a>';
            $approve = '<a href="javascript:void(0)" onclick="openView('.$data->trans_main_id.')" class="btn btn-info btn-edit permission-approve" datatip="Approve Order" flow="down"><i class="fa fa-check"></i></a>';
            $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
            $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
        }
        else{
            $approve = '<a href="javascript:void(0)" class="btn btn-facebook approveSOrder permission-approve" data-id="'.$data->trans_main_id.'" data-val="0" data-msg="Reject" datatip="Reject Order" flow="down" ><i class="ti-close" ></i></a>';
			$edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
            $dispatch = '<a href="javascript:void(0)" class="btn btn-primary createDeliveryChallan permission-write" data-id="'.$data->party_id.'" data-party_name="'.$data->party_name.'" datatip="Create Challan" flow="down"><i class="fa fa-truck" ></i></a>';
            $invoice = '<a href="javascript:void(0)" class="btn btn-primary createSalesInvoice permission-write" data-id="'.$data->party_id.'" data-party_name="'.$data->party_name.'" datatip="Create Invoice" flow="down"><i class="fa fa-file-alt" ></i></a>';
            $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->trans_main_id.'" data-party_name="'.$data->party_name.'" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';     
            $mailBtn = '<a class="btn btn-warning permission-read" href="javascript:void(0)" onclick="sendEmail('.$emailParam.')" datatip="Send Mail" flow="down"><i class="fas fa-envelope" ></i></a>';
        }
        $complete = '<a class="btn btn-info btn-solution permission-modify" href="javascript:void(0)" datatip="Short Close" flow="down" onclick="edit('.$closeParam.');"><i class="fa fa-window-close"></i></a>';
        //$complete = '<a href="javascript:void(0)" class="btn btn-warning completeOrderItem permission-modify" data-id="'.$data->id.'" data-val="2" data-msg="Closed" datatip="Short Close" flow="down" ><i class="ti-close" ></i></a>';
        //$edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        //$delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';     
    endif;    
    // $action = getActionButton($approve.$printBtn.$complete.$dispatch.$invoice.$edit.$delete);
    $mailBtn='';
    $action = getActionButton($approve.$printBtn.$itemList.$complete.$mailBtn.$edit.$delete);
    $orderType = "";
    $salesType = "";
    if($data->sales_type == 1):
        $orderType = "Manufacturing";
        $salesType = "Manufacturing (Domestics)";
    elseif($data->sales_type == 2):
        $orderType = "Manufacturing";
        $salesType = "Manufacturing (Export)";
    elseif($data->sales_type == 3):
        $orderType = "Job Order";
        $salesType = "Jobwork (Domestics)";
    endif;
	
	
	$responseData[] = $action;
	$responseData[] = $data->sr_no;
	$responseData[] = getPrefixNumber($data->trans_prefix,$data->trans_no);
    $responseData[] = formatDate($data->trans_date);
    $responseData[] = $salesType;
    $responseData[] = $data->party_name;    
    $responseData[] = $data->doc_no;
	$responseData[] = $data->ref_no;
    //$responseData[] = $data->item_name;
    //$responseData[] = floatVal($data->qty);
    //$responseData[] = floatVal($data->dispatch_qty);
    //$responseData[] = floatVal($data->pending_qty);
    $responseData[] = formatDate($data->cod_date); 	
    $responseData[] = $data->order_status_label;
	return $responseData;
}

/* Proforma Invoice Table Data */
function getProformaInvoiceData($data){
    $deleteParam = $data->trans_main_id.",'Proforma Invoice'";

    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permision-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$print = '<a href="javascript:void(0)" class="btn btn-primary btn-edit printInvoice" datatip="Print Invoice" flow="down" data-id="'.$data->trans_main_id.'"><i class="fa fa-print"></i></a>';
	
    $action = getActionButton($print.$edit.$delete);

    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->item_name,$data->net_amount,$data->inv_amount];
}

/* Sales Invoice Table Data */
function getSalesInvoiceData($data){
    $deleteParam = $data->id.",'Sales Invoice'";
    
    if($data->tp == 'ITEMWISE'){$data->id = $data->trans_main_id;}
    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
    $copyInv = '<a href="'.base_url($data->controller.'/copyInv/'.$data->id).'" class="btn btn-info btn-edit permission-modify" datatip="Copy" flow="down"><i class="ti-write"></i></a>';

    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$printExport=""; $printCustom=""; $print="";
    if($data->sales_type == 4){
        $printExport = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Export Invoice" flow="down" data-id="'.$data->id.'" data-function="export_invoice_pdf"><i class="fa fa-print"></i></a>';
    } else if($data->sales_type == 3){
        $printCustom = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Custom Invoice" flow="down" data-id="'.$data->id.'" data-function="custom_invoice_pdf"><i class="fa fa-print"></i></a>';
    } else {
        $print = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Invoice" flow="down" data-id="'.$data->id.'" data-function="invoice_pdf"><i class="fa fa-print"></i></a>';
    } $blButton='';
    if($data->entry_type == 8){
        $blParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'blData', 'title' : 'Bill of Lading', 'fnEdit' : 'getBlData', 'fnsave' : 'updateBlData'}";
        $blButton = '<a class="btn btn-primary permission-modify" href="javascript:void(0)" datatip="Bill of Lading" flow="down" onclick="edit('.$blParam.');"><i class="icon-Bitcoin"></i></a>';
    }
    
    if($data->listType == 'LISTING')
    {
        $action = getActionButton($printCustom.$printExport.$print.$blButton.$edit.$delete);
    	if($data->tp == 'BILLWISE')
    	{
    		return [$action,$data->sr_no,$data->trans_number,date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->taxable_amount,$data->gst_amount,$data->net_amount];
    	}
    	else
    	{
    		return [$action,$data->sr_no,$data->trans_number,date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->item_name.' <small>'.$data->item_remark.'</small>',floatVal($data->qty),$data->price,$data->disc_amount,$data->amount];
    	}
    }
    
    if($data->listType == 'REPORT')
    {
        if($data->tp == 'ITEMWISE'){$data->id = $data->trans_main_id;}
        $trno = $data->trans_number;
        //if(in_array($data->userRole,[-1,1,3])){$trno= '<a href="'.base_url('salesInvoice/edit/'.$data->id).'" target="_blank" datatip="Edit Invoice" flow="right"> '.$data->trans_number.'</a>';}
          
    	if($data->tp == 'BILLWISE')
    	{
    		return [$data->sr_no,$trno,date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->taxable_amount,$data->gst_amount,$data->net_amount];
    	}
    	else
    	{
    		return [$data->sr_no,$trno,date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->item_name.' <small>'.$data->item_remark.'</small>',floatVal($data->qty),$data->price,$data->disc_amount,$data->amount];
    	}
    }
}

/* 
function getCycleTimeData($data){

    $cycleTime = '<button type="button" class="btn waves-effect waves-light btn-outline-primary addCycleTime" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="both" data-modal_id="modal-md" data-function="addCycleTime" data-form_title="Set Cycle Time">Set Cycle Time</button>';

    return [$data->sr_no,$data->item_code,$cycleTime];
}
 */
/* function ToolConsumption($data){

    $toolConsumption = '<button type="button" class="btn waves-effect waves-light btn-outline-primary addToolConsumption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="both" data-modal_id="modal-md" data-function="addToolConsumption" data-form_title="Add Tool Consumption">Add Tool Consumption</button>';

    return [$data->sr_no,$data->item_code,$toolConsumption];
} */

/* Sales Invoice Table Data */
function getScrapInvoiceData($data){
    $deleteParam = $data->id.",'Scrap Invoice'";

    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$print = '<a href="javascript:void(0)" class="btn btn-primary btn-edit printInvoice permission-approve" datatip="Print Invoice" flow="down" data-id="'.$data->id.'"><i class="fa fa-print"></i></a>';
	
    $action = getActionButton($print.$edit.$delete);

    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->net_amount];
}

function getNPDSalesEnquiryData($data){
    $feasibleAcceptBtn ='';$addFeasibleBtn='';$emailBtn = ""; $npdBtn ="";$dcftBtn="";$intRTSBtn=""; $feasiblemailBtn = "";

    if($data->trans_status == 1):
        $feasibleAcceptBtn= '<a href="javascript:void(0)" class="btn btn-warning btn-delete permission-remove" onclick="feasibilityRequest('.$data->id.',2);" datatip="Fesibility Request" flow="down"><i class=" fas fa-check-circle
        "></i></a>'; 
    elseif($data->trans_status == 2):
        $addParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-md', 'form_id' : 'feasibilityForm', 'title' : 'Add Feasibility Days','fnedit' : 'addFeasibilityDays', 'fnsave' : 'saveFeasibilityDays'}";
        $addFeasibleBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Add Feasibility Days" flow="down" onclick="edit(' . $addParam . ');"><i class=" fas fa-plus"></i></a>';
        if(!empty($data->feasible)){
            $mailparam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-md', 'form_id' : 'feasibilityForm', 'title' : 'Add Feasibility Days','fnedit' : 'reviewNSendMail', 'fnsave' : 'sendMail'}";
            $emailBtn = '<a class="btn btn-facebook btn-edit permission-modify" href="javascript:void(0)" datatip="Add Feasibility Days" flow="down" onclick="sendMail(' . $mailparam . ');"><i class=" icon-Mail-Forward"></i></a>';
        }
    endif;

    if($data->trans_status > 1 && !empty($data->feasible)){
        $dcftBtn = '<a  class="btn  btn-info btn-edit decideCFT" data-button="close" data-modal_id="modal-lg"  data-enq_id="'.$data->id.'" data-function="decideCFT" data-form_title="Decide CFT" data-fnsave="saveCFT" datatip = "Decide CFT"  flow="down" > <i class="fas fa-user-plus
        "></i> </a>	';
        if(!empty($data->pending_initiate) && $data->trans_status == 2){
            $intRTSBtn = '<a class="btn  btn-primary initiateRTS" data-enq_id="'.$data->id.'" datatip = "Initiate RTS"  flow="down"><i class="mdi mdi-arrow-right-drop-circle"></i></a>	';
        }
    }
    $enqNo = getPrefixNumber($data->trans_prefix,$data->trans_no);
    if($data->trans_status == 3){
        $enqNo = '<a href="'.base_url($data->controller.'/npdParts/'.$data->id).'" target="_blank">'.getPrefixNumber($data->trans_prefix,$data->trans_no).'</a>';

        if(empty($data->feasible_email_by) && !empty($data->rts_completed)){
            $feasibleMailparam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-md', 'form_id' : 'feasibilityForm', 'title' : 'Feasibility Mail','fnedit' : 'feasibleMailSend', 'fnsave' : 'feasibleMailSendSave'}";
            $feasiblemailBtn = '<a class="btn btn-facebook btn-edit permission-modify" href="javascript:void(0)" datatip="Feasibility Mail" flow="down" onclick="feasibleMailSend(' . $feasibleMailparam . ');"><i class="icon-Mail-Forward"></i></a>';
        }
    }
    $action = getActionButton($feasiblemailBtn.$intRTSBtn .$dcftBtn.$emailBtn.$feasibleAcceptBtn.$addFeasibleBtn);

    return [$action,$data->sr_no, $enqNo,date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->item_name,$data->qty,$data->status,$data->remark];

}

/* Employee Responsibility Table Data */
function getResponsibilityData($data){
    $deleteParam = $data->id.",'Responsibility'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editEmployeeResponsibility', 'title' : 'Update Employee Responsibility'}";
    $edit = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $action = getActionButton($edit.$delete);
    return [$action,$data->sr_no,$data->remark];
}

function getRFQData($data){
    $deleteParam = $data->ref_id.",'Purchase Enquiry'";
    $closeParam = $data->ref_id.",'Purchase Enquiry'";
    $closeParam = $data->ref_id.",'Purchase Enquiry'";
    $enqComplete = "";$edit = "";$delete = "";$close = "";$reopen = ""; $approve="";$reject="";

    $cnDate = (!empty($data->enq_ref_date))?date("d-m-Y",strtotime($data->enq_ref_date)):"";
    if(($data->confirm_status == 0)):
        $reject = '<a href="javascript:void(0)" class="btn btn-success approvePEnquiry permission-modify" data-id="'.$data->ref_id.'" data-val="3" data-msg="Reject" datatip="Reject Enquiry" flow="down"><i class="fa fa-window-close"></i></a>';     
        $enqComplete = '<a href="javascript:void(0)" class="btn btn-info btn-complete enquiryConfirmed permission-modify" data-id="'.$data->ref_id.'" data-party="'.$data->supplier_name.'" data-enqno="'.$data->enq_prefix.$data->enq_no.'" data-enqdate="'.date("d-m-Y",strtotime($data->enq_date)).'" data-button="both" data-modal_id="modal-xl" data-function="getEnquiryData" data-form_title="Purchase Enquiry Quotation" datatip="Quotation" flow="down"><i class="fa fa-check"></i></a>';
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->ref_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" data-tooltip="tooltip" data-placement="bottom" data-original-title="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
    $action = getActionButton($approve.$reject.$enqComplete.$edit.$delete);
    return [$action,$data->sr_no,$data->enq_prefix.$data->enq_no.(!empty($data->sub_enq_no)?'/'.$data->sub_enq_no:''),date("d-m-Y",strtotime($data->enq_date)),$data->supplier_name,$data->item_name,$data->qty,$data->status,$data->item_remark];
}

/* Delivery Challan */
function getDeliveryChallanData($data){
    $deleteParam = $data->trans_main_id.",'Delivery Challan'";
    $invoice = "";$edit = "";$delete = "";$itemList="";$printBtn="";$backPrint ="";
    if(empty($data->trans_status)):
        $invoice = '<a href="javascript:void(0)" class="btn btn-info createInvoice permission-write" data-id="'.$data->party_id.'" data-party_name="'.$data->party_name.'" datatip="Invoice" flow="down"><i class="fa fa-file-alt" ></i></a>';    
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
    if($data->party_id == 5):
        $backPrint = '<a class="btn btn-danger btn-edit" href="'.base_url('deliveryChallan/back_pdf_forBhavani/'.$data->trans_main_id).'" target="_blank" datatip="Back Print" flow="down"><i class="fas fa-print" ></i></a>';

        $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url('deliveryChallan/challan_pdf_Forbhvani/'.$data->trans_main_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    else:
        $printBtn = '<a href="javascript:void(0)" class="btn btn-warning btn-edit printInvoice" datatip="Print Delivery Challan" flow="down" data-id="'.$data->trans_main_id.'" data-function="challan_pdf"><i class="fa fa-print"></i></a>';
    endif;
    $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->trans_main_id.'" data-party_name="'.$data->party_name.'" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';
    $action = getActionButton($printBtn.$backPrint.$invoice.$itemList.$edit.$delete);
    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),formatDate($data->trans_date),$data->party_code,$data->inv_no];
}


/* Commercial Packing Data  */
function getCommercialPackingData($data){
    $deleteParam = $data->id.",'Commercial Packing'";

    $edit = '';$delete = '';
    if($data->trans_status == 0):
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;

    $migrateItemNamesParam = $data->id.",'tc'";//first param is main table id and second param is table name alias for checking condition in model
    $migrateItemNames = '<a href="javascript:void(0)" class="btn btn-info" onclick="migrateItemNames('.$migrateItemNamesParam.');" datatip="Update Item Name" flow="down"><i class="fas fa-sync"></i></a>';


    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/commercialPackingPdf/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    $excelBtn = '<a class="btn btn-facebook btn-edit" href="'.base_url($data->controller.'/commercialPackingPdf/'.$data->id.'/EXCEL').'" target="_blank" datatip="Excel" flow="down"><i class="fa fa-file" ></i></a>';

    $data->doc_date = (!empty($data->doc_date))?date("d-m-Y",strtotime($data->doc_date)):"";
    $action = getActionButton($migrateItemNames.$excelBtn.$printBtn.$edit.$delete);
    return [$action,'',$data->trans_number,$data->packing_no,$data->doc_no,$data->doc_date,$data->party_name,$data->total_amount,$data->net_amount];
}

/* Commercial Invoice Data */
function getCommercialInvoiceData($data){
    $deleteParam = $data->id.",'Commercial Invoice'";
    
    $edit = '';$delete = '';
    if($data->trans_status == 0):
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
    
    $migrateItemNamesParam = $data->id.",'tc'";//first param is main table id and second param is table name alias for checking condition in model
    $migrateItemNames = '<a href="javascript:void(0)" class="btn btn-info" onclick="migrateItemNames('.$migrateItemNamesParam.');" datatip="Update Item Name" flow="down"><i class="fas fa-sync"></i></a>';

    
    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/commercialInvoicePdf/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    $excelBtn = '<a class="btn btn-facebook btn-edit" href="'.base_url($data->controller.'/commercialInvoicePdf/'.$data->id.'/EXCEL').'" target="_blank" datatip="Excel" flow="down"><i class="fa fa-file" ></i></a>';

    $action = getActionButton($migrateItemNames.$excelBtn.$printBtn.$edit.$delete);

    return [$action,'',$data->trans_number,$data->packing_no,$data->doc_no,((!empty($data->doc_date))?date("d-m-Y",strtotime($data->doc_date)):""),$data->party_name,$data->net_amount];
}


/* Custom Packing Data */
function getCustomPackingData($data){
    $deleteParam = $data->id.",'Custom Packing'";

    $edit = '';$delete = '';
    if($data->trans_status == 0):
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
    
    $migrateItemNamesParam = $data->id.",'tc'";//first param is main table id and second param is table name alias for checking condition in model
    $migrateItemNames = '<a href="javascript:void(0)" class="btn btn-info" onclick="migrateItemNames('.$migrateItemNamesParam.');" datatip="Update Item Name" flow="down"><i class="fas fa-sync"></i></a>';

    
    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/customPackingPdf/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    $data->doc_date = (!empty($data->doc_date))?date("d-m-Y",strtotime($data->doc_date)):"";
    $action = getActionButton($migrateItemNames.$printBtn.$edit.$delete);
    return [$action,'',$data->trans_number,$data->packing_no,$data->doc_no,$data->doc_date,$data->party_name,$data->total_amount,$data->net_amount];
}

/* Custom Invoice Data */
function getCustomInvocieData($data){
    $deleteParam = $data->id.",'Custom Invoice'";
    
    $edit = '';$delete = '';
    if($data->trans_status == 0):
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
    
    $migrateItemNamesParam = $data->id.",'tc'";//first param is main table id and second param is table name alias for checking condition in model
    $migrateItemNames = '<a href="javascript:void(0)" class="btn btn-info" onclick="migrateItemNames('.$migrateItemNamesParam.');" datatip="Update Item Name" flow="down"><i class="fas fa-sync"></i></a>';

    
    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/customInvoicePdf/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    $evdPrintBtn = '<a class="btn btn-dark btn-edit" href="'.base_url($data->controller.'/evdPdf/'.$data->id).'" target="_blank" datatip="EVD Print" flow="down"><i class="fas fa-print" ></i></a>';
    $scometPrintBtn = '<a href="javascript:void(0)" class="btn btn-info btn-edit printScomet" datatip="Scomet Print" flow="down" data-id="'.$data->id.'" data-function="scometPrint"><i class="fa fa-print"></i></a>';

    $dbkPrintBtn = '<a class="btn btn-info btn-edit" href="'.base_url($data->controller.'/dbkPdf/'.$data->id).'" target="_blank" datatip="DBK Print" flow="down"><i class="fas fa-print" ></i></a>';

    $action = getActionButton($migrateItemNames.$dbkPrintBtn.$evdPrintBtn.$scometPrintBtn.$printBtn.$edit.$delete);
    return [$action,'',$data->trans_number,$data->packing_no,$data->doc_no,((!empty($data->doc_date))?date("d-m-Y",strtotime($data->doc_date)):""),$data->party_name,$data->net_amount];
}


function getDispatchRequestData($data){
	$action='';
    if(empty($data->request_for)):
		$editButton=""; $deleteButton="";
		if(floatVal($data->req_qty) >= floatVal($data->packing_qty)){
			$editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'dispatchRequset', 'title' : 'Dispatch Requset', 'fnedit' : 'editDispatchRequset'}";
			$editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Dispatch Requset" flow="down" onclick="edit('.$editParam.');"><i class="fa fa-edit"></i></a>';
			
			$deleteParam = $data->id.",'Dispatch Request'"; 
			$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
		}
		$action = getActionButton($editButton.$deleteButton);
		
    elseif(!empty($data->request_for) && $data->request_for == 'Challan'):
        $challan = '<a href="javascript:void(0)" class="btn btn-info createChallan permission-write" data-id="'.$data->party_id.'" data-party_name="'.$data->party_name.'" datatip="Challan" flow="down"><i class="fa fa-file-alt" ></i></a>';    
        
        $action = getActionButton($challan);
    endif;
    $so_no = (!empty($data->so_prefix) && !empty($data->so_no))?getPrefixNumber($data->so_prefix,$data->so_no):'';
    $req_no = (!empty($data->trans_prefix) && !empty($data->trans_no))?getPrefixNumber($data->trans_prefix,$data->trans_no):'';
    return [$action,$data->sr_no,formatDate($data->req_date), $req_no, $so_no,'['.$data->party_code.'] '.$data->party_name,'['.$data->item_code.'] '.$data->item_name,floatVal($data->req_qty),floatVal($data->dispatch_qty),floatVal($data->pending_qty),$data->remark];
}

function getDispatchMaterialData($data){
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'dispatchMaterial', 'title' : 'Dispatch Material [".$data->item_code."]', 'fnedit' : 'addDispatchMaterial', 'fnsave' : 'saveDispatchMaterial'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Dispatch Material" flow="down" onclick="edit('.$editParam.');"><i class="ti-truck"></i></a>';
		
    $action = getActionButton($editButton);
    return [$action,$data->sr_no,formatDate($data->trans_date),getPrefixNumber($data->trans_prefix,$data->trans_no),'['.$data->party_code.'] '.$data->party_name,'['.$data->item_code.'] '.$data->item_name,floatVal($data->qty)];
}

/* SE Followup Table Data */
function getSEFollowupData($data){
    $deleteParam = $data->id.",'Followup','deleteAppointment'"; 
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($deleteButton);

    return  [$action,$data->sr_no,formatDate($data->appointment_date),$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->item_name,$data->qty,$data->mode,$data->contact_person,$data->notes];
}

/* SE Appointment Table Data */
function getSEAppointmentData($data){
    $deleteParam = $data->id.",'Followup','deleteAppointment'"; 
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
    $closeButton = "";
    if($data->status == 0):
        $closeParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'both', 'form_id' : 'closeAppointment', 'title' : 'Close Appointment', 'fnedit' : 'closeAppointment', 'fnsave' : 'saveAppointmentStatus'}";
        $closeButton = '<a class="btn btn-dark btn-appointment permission-modify" href="javascript:void(0)" datatip="Close Appointment" flow="down" onclick="edit('.$closeParam.');"><i class="ti-close"></i></a>';
    endif;

    $action = getActionButton($deleteButton.$closeButton);

    return  [$action,$data->sr_no,date("d-m-Y H:i",strtotime($data->appointment_date.' '.$data->appointment_time)),$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->item_name,$data->qty,$data->mode,$data->contact_person,$data->purpose,$data->status_label,$data->notes];
}

/* SQ Followup Table Data */
function getSQFollowupData($data){
    $deleteParam = $data->id.",'Followup','deleteAppointment'"; 
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($deleteButton);

    return  [$action,$data->sr_no,formatDate($data->appointment_date),$data->trans_number,$data->mode,$data->contact_person,$data->notes,formatDate($data->trans_date),$data->party_name];
}

/* SQ Appointment Table Data */
function getSQAppointmentData($data){
    $deleteParam = $data->id.",'Followup','deleteAppointment'"; 
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
    $closeButton = "";
    if($data->status == 0):
        $closeParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'both', 'form_id' : 'closeAppointment', 'title' : 'Close Appointment', 'fnedit' : 'closeAppointment', 'fnsave' : 'saveAppointmentStatus'}";
        $closeButton = '<a class="btn btn-dark btn-appointment permission-modify" href="javascript:void(0)" datatip="Close Appointment" flow="down" onclick="edit('.$closeParam.');"><i class="ti-close"></i></a>';
    endif;

    $action = getActionButton($deleteButton.$closeButton);

    return  [$action,$data->sr_no,date("d-m-Y H:i",strtotime($data->appointment_date.' '.$data->appointment_time)),$data->trans_number,$data->mode,$data->contact_person,$data->purpose,$data->status_label,$data->notes,formatDate($data->trans_date),$data->party_name];
}

/* Lead Followup Table Data */
function getLeadFollowupData($data){
    $deleteParam = $data->id.",'Followup','deleteAppointment'"; 
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($deleteButton);

    return  [$action,$data->sr_no,formatDate($data->appointment_date),$data->mode,$data->contact_person,$data->notes,$data->party_name,$data->party_code];
}

/* Lead Appointment Table Data */
function getLeadAppointmentData($data){
    $deleteParam = $data->id.",'Followup','deleteAppointment'"; 
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
    $closeButton = "";
    if($data->status == 0):
        $closeParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'both', 'form_id' : 'closeAppointment', 'title' : 'Close Appointment', 'fnedit' : 'closeAppointment', 'fnsave' : 'saveAppointmentStatus'}";
        $closeButton = '<a class="btn btn-dark btn-appointment permission-modify" href="javascript:void(0)" datatip="Close Appointment" flow="down" onclick="edit('.$closeParam.');"><i class="ti-close"></i></a>';
    endif;

    $action = getActionButton($deleteButton.$closeButton);

    return  [$action,$data->sr_no,date("d-m-Y H:i",strtotime($data->appointment_date.' '.$data->appointment_time)),$data->mode,$data->contact_person,$data->purpose,$data->status_label,$data->notes,$data->party_name,$data->party_code];
}

?>