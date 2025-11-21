<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getProductionHeader($page){
    /* Vendor Header */
    $data['vendor'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['vendor'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
	$data['vendor'][] = ["name"=>"Company Name"];
	$data['vendor'][] = ["name"=>"Contact Person"];
    $data['vendor'][] = ["name"=>"Contact No."];
    $data['vendor'][] = ["name"=>"Address"];
    
    /* Process Header */
    $data['process'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['process'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['process'][] = ["name"=>"Process Name"];
    $data['process'][] = ["name"=>"Department"];
    $data['process'][] = ["name"=>"Remark"];

    /* Job Card Header */
    $data['jobcard'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Job No.","style"=>"width:9%;","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Duhee Job No.","style"=>"width:9%;","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Job Date","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Customer","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Order Qty.","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Status","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Remark"];
    $data['jobcard'][] = ["name"=>"Last Activity"];

    /* Material Request */
    $data['materialRequest'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['materialRequest'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['materialRequest'][] = ["name"=>"Job No."];
    $data['materialRequest'][] = ["name"=>"Request Date"];
    $data['materialRequest'][] = ["name"=>"Request Item Name"];
    $data['materialRequest'][] = ["name"=>"Request Item Qty"];

    /* Jobwork Order Header */
    $data['jobWorkOrder'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['jobWorkOrder'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['jobWorkOrder'][] = ["name"=>"Date"];
    $data['jobWorkOrder'][] = ["name"=>"Order No."];
    $data['jobWorkOrder'][] = ["name"=>"Vendor Name"];
    $data['jobWorkOrder'][] = ["name"=>"Remark"];

    /* Job Work Header */

    $data['jobWork'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
   // $data['jobWork'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Jobwork No.", "style" => "width:9%;", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Jobwork Date", "style" => "width:9%;", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Vendor", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Item", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Process"];
    $data['jobWork'][] = ["name" => "Qty", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Com. Qty", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Recive Qty", "textAlign" => "center"]; 
    $data['jobWork'][] = ["name" => "Recive Com. Qty", "textAlign" => "center"]; 
    $data['jobWork'][] = ["name" => "Pend. Qty", "textAlign" => "center"]; 
    $data['jobWork'][] = ["name" => "Pend. Com. Qty", "textAlign" => "center"]; 
    $data['jobWork'][] = ["name" => "Status", "textAlign" => "center"]; 
    $data['jobWork'][] = ["name" => "Print Status", "textAlign" => "center"]; 

    //Return Tab Header
    $data['jobWorkReturn'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    $data['jobWorkReturn'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['jobWorkReturn'][] = ["name" => "Jobwork No.", "style" => "width:9%;", "textAlign" => "center"];
    $data['jobWorkReturn'][] = ["name" => "Receive Date", "style" => "width:9%;", "textAlign" => "center"];
    $data['jobWorkReturn'][] = ["name" => "Ch. No.", "style" => "width:9%;", "textAlign" => "center"];
    $data['jobWorkReturn'][] = ["name" => "Vendor", "textAlign" => "center"];
    $data['jobWorkReturn'][] = ["name" => "Item", "textAlign" => "center"];
    $data['jobWorkReturn'][] = ["name" => "Process"];
    $data['jobWorkReturn'][] = ["name" => "Rec. Qty", "textAlign" => "center"]; 
    $data['jobWorkReturn'][] = ["name" => "Rec. Com. Qty", "textAlign" => "center"]; 
    $data['jobWorkReturn'][] = ["name" => "Rej. Qty", "textAlign" => "center"]; 
    $data['jobWorkReturn'][] = ["name" => "W.P. Qty", "textAlign" => "center"];
    $data['jobWorkReturn'][] = ["name" => "Invoiced Qty", "textAlign" => "center"];
    

    /* Rejection Header */
    $data['rejectionComments'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['rejectionComments'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
    $data['rejectionComments'][] = ["name"=>"Rejection Comment"];

	/* Production Operation Header */
    $data['productionOperation'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['productionOperation'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['productionOperation'][] = ["name"=>"Operation Name"];

    /* Product Option Header */
    $data['productOption'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center", "srnoPosition" => 0];
    $data['productOption'][] = ["name"=>"Part Code"];
    $data['productOption'][] = ["name"=>"BOM","style"=>"width:10%;","textAlign"=>"center"];
    $data['productOption'][] = ["name"=>"Process","style"=>"width:10%;","textAlign"=>"center"];
    $data['productOption'][] = ["name"=>"Cycle Time","style"=>"width:10%;","textAlign"=>"center"];
    $data['productOption'][] = ["name"=>"Tool","style"=>"width:10%;","textAlign"=>"center"];
    $data['productOption'][] = ["name"=>"Action","style"=>"width:15%;","textAlign"=>"center"];

    /* Idle Reason Header */
    $data['idleReason'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['idleReason'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
    $data['idleReason'][] = ["name"=>"Idle Code","style"=>"width:10%;","textAlign"=>"center"];
    $data['idleReason'][] = ["name"=>"Idle Reason"];

    /* Process Setup Header */
    $data['processSetup'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['processSetup'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['processSetup'][] = ["name"=>"Req. Date"];
    $data['processSetup'][] = ["name"=>"Status"];
    $data['processSetup'][] = ["name"=>"Setup Type"];
    $data['processSetup'][] = ["name"=>"Setter Name"];
    $data['processSetup'][] = ["name"=>"Setup Note"];
    $data['processSetup'][] = ["name"=>"Job No"];
    $data['processSetup'][] = ["name"=>"Part Name"];
    $data['processSetup'][] = ["name"=>"Process Name"];
    $data['processSetup'][] = ["name"=>"Machine"];
    $data['processSetup'][] = ["name"=>"Inspector Name"];
    $data['processSetup'][] = ["name"=>"Start Time"];
    $data['processSetup'][] = ["name"=>"End Time"];
    $data['processSetup'][] = ["name"=>"Duration"];
    $data['processSetup'][] = ["name"=>"Remark"];

    /* Line Inspection Header */
    $data['lineInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['lineInspection'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['lineInspection'][] = ["name"=>"Jobcard No."];
    $data['lineInspection'][] = ["name"=>"Process Name"];
    $data['lineInspection'][] = ["name"=>"Product Code"];
    $data['lineInspection'][] = ["name"=>"Vendor Name"];
    $data['lineInspection'][] = ["name"=>"In Qty."];
    $data['lineInspection'][] = ["name"=>"Out Qty."];
    $data['lineInspection'][] = ["name"=>"Rej. Qty."];
    $data['lineInspection'][] = ["name"=>"ReW. Qty."];
    $data['lineInspection'][] = ["name"=>"Pending Qty."];    

    /* Process Approval */
    /* $data['processApproval'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['processApproval'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['processApproval'][] = ["name"=>"Job Date"];
    $data['processApproval'][] = ["name"=>"Delivery Date"];
    $data['processApproval'][] = ["name"=>"Job Type"];
    $data['processApproval'][] = ["name"=>"Customer"];
    $data['processApproval'][] = ["name"=>"Challan No."];
    $data['processApproval'][] = ["name"=>"Product"];
    $data['processApproval'][] = ["name"=>"Order Qty."];
    $data['processApproval'][] = ["name"=>"Status"];
    $data['processApproval'][] = ["name"=>"Remark"]; */

    /* JobWork Invoice Header */    
    $data['jobWorkInvoice'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['jobWorkInvoice'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['jobWorkInvoice'][] = ["name"=>"Vou No."];
    $data['jobWorkInvoice'][] = ["name"=>"Inv No."];
    $data['jobWorkInvoice'][] = ["name"=>"Inv Date"];
    $data['jobWorkInvoice'][] = ["name"=>"Vendor Name"];
    $data['jobWorkInvoice'][] = ["name"=>"Amount"];
    
    /* Job Work Header */
    $data['outSource'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    $data['outSource'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['outSource'][] = ["name" => "Challan Date", "textAlign" => "center"];
    $data['outSource'][] = ["name" => "Challan No.", "style" => "width:9%;", "textAlign" => "center"];
    $data['outSource'][] = ["name" => "Job No.", "style" => "width:9%;", "textAlign" => "center"];
    $data['outSource'][] = ["name" => "Vendor"];
    $data['outSource'][] = ["name" => "Product"];
    $data['outSource'][] = ["name" => "Process"];
    $data['outSource'][] = ["name" => "Out Qty", "textAlign" => "center"];
    $data['outSource'][] = ["name" => "In Qty", "textAlign" => "center"];
    $data['outSource'][] = ["name" => "Pending Qty", "textAlign" => "center"];
    $data['outSource'][] = ["name" => "Return Qty", "textAlign" => "center"];

    /* Primary CFT */
    $data['primaryCFT'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    $data['primaryCFT'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Date","textAlign"=>"center"];    
    $data['primaryCFT'][] = ["name"=>"Tag No","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Machine","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Operator","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Pending Qty","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Decision","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Type","textAlign"=>"center"];

    /* Final CFT */
    $data['finalCFT'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    $data['finalCFT'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Primary Tag No","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Tag No","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Machine","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Operator","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Pending Qty","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Decision","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Rej/Rw Reason","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Rej/Rw Stage","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Rej/Rw By","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Type","textAlign"=>"center"];

    /* UD CFT */
    $data['underDeviation'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    $data['underDeviation'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['underDeviation'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['underDeviation'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['underDeviation'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['underDeviation'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['underDeviation'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['underDeviation'][] = ["name"=>"Pending Qty","textAlign"=>"center"];
    $data['underDeviation'][] = ["name"=>"Description","textAlign"=>"center"];
    $data['underDeviation'][] = ["name"=>"Reason","textAlign"=>"center"];
    $data['underDeviation'][] = ["name"=>"Special Marking","textAlign"=>"center"];

    /* Rework */
    $data['rework'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    $data['rework'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['rework'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['rework'][] = ["name"=>"Tag No","textAlign"=>"center"];
    $data['rework'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['rework'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['rework'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['rework'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['rework'][] = ["name"=>"Rej/Rw Reason","textAlign"=>"center"];
    $data['rework'][] = ["name"=>"Rej/Rw Stage","textAlign"=>"center"];
    $data['rework'][] = ["name"=>"Rej/Rw By","textAlign"=>"center"];

    /* Product Setup For Setter */
    $data['productSetup'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    $data['productSetup'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['productSetup'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['productSetup'][] = ["name"=>"Request No","textAlign"=>"center"];
    $data['productSetup'][] = ["name"=>"Request By","textAlign"=>"center"];
    $data['productSetup'][] = ["name"=>"Jobcard","textAlign"=>"center"];
    $data['productSetup'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['productSetup'][] = ["name"=>"Item","textAlign"=>"center"];
    $data['productSetup'][] = ["name"=>"Machine","textAlign"=>"center"];
    $data['productSetup'][] = ["name"=>"QC Inspector","textAlign"=>"center"];
    $data['productSetup'][] = ["name"=>"Status","textAlign"=>"center"];

    /* Product Setter Report */
    $data['productSetterReport'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    $data['productSetterReport'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['productSetterReport'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['productSetterReport'][] = ["name"=>"Start Time","textAlign"=>"center"];
    $data['productSetterReport'][] = ["name"=>"End Time","textAlign"=>"center"];
    $data['productSetterReport'][] = ["name"=>"Note","textAlign"=>"center"];
    $data['productSetterReport'][] = ["name"=>"Status","textAlign"=>"center"];
    $data['productSetterReport'][] = ["name"=>"Inspector Note","textAlign"=>"center"];


     /* Product Setup For Setter */
     $data['asignSetupInspector'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
     $data['asignSetupInspector'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
     $data['asignSetupInspector'][] = ["name"=>"Date","textAlign"=>"center"];
     $data['asignSetupInspector'][] = ["name"=>"Request No","textAlign"=>"center"];
     $data['asignSetupInspector'][] = ["name"=>"Request By","textAlign"=>"center"];
     $data['asignSetupInspector'][] = ["name"=>"Jobcard","textAlign"=>"center"];
     $data['asignSetupInspector'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
     $data['asignSetupInspector'][] = ["name"=>"Item","textAlign"=>"center"];
     $data['asignSetupInspector'][] = ["name"=>"Machine","textAlign"=>"center"];
     $data['asignSetupInspector'][] = ["name"=>"QC Inspector","textAlign"=>"center"];
     $data['asignSetupInspector'][] = ["name"=>"Status","textAlign"=>"center"];

     /* Product Setup Approval */
    $data['setupApproval'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    $data['setupApproval'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['setupApproval'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['setupApproval'][] = ["name"=>"Request No","textAlign"=>"center"];
    $data['setupApproval'][] = ["name"=>"Jobcard","textAlign"=>"center"];
    $data['setupApproval'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['setupApproval'][] = ["name"=>"Item","textAlign"=>"center"];
    $data['setupApproval'][] = ["name"=>"Machine","textAlign"=>"center"];
    $data['setupApproval'][] = ["name"=>"Setter","textAlign"=>"center"];
    $data['setupApproval'][] = ["name"=>"Setter Note","textAlign"=>"center"];
    $data['setupApproval'][] = ["name"=>"Status","textAlign"=>"center"];
    $data['setupApproval'][] = ["name"=>"Inspector Note","textAlign"=>"center"];

    /** PIR Report */
    $data['pir'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    $data['pir'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['pir'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['pir'][] = ["name"=>"Pir No","textAlign"=>"center"];
    $data['pir'][] = ["name"=>"Jobcard","textAlign"=>"center"];
    $data['pir'][] = ["name"=>"Process NO","style"=>"width:9%;","textAlign"=>"center"];
    $data['pir'][] = ["name"=>"Item","textAlign"=>"center"];
    $data['pir'][] = ["name"=>"Machine","textAlign"=>"center"];
    $data['pir'][] = ["name"=>"PIR By","textAlign"=>"center"];
    $data['pir'][] = ["name"=>"Remark","textAlign"=>"center"];

    /** Pending PIR Report */
    $data['pendingPir'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    $data['pendingPir'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['pendingPir'][] = ["name"=>"Jobcard","textAlign"=>"center"];
    $data['pendingPir'][] = ["name"=>"Part Name","textAlign"=>"center"];
    $data['pendingPir'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['pendingPir'][] = ["name"=>"Machine","textAlign"=>"center"];
    $data['pendingPir'][] = ["name"=>"No Of PIR","textAlign"=>"center"];
	
    /* PendingChallan Report */
    $data['pendingChallan'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    $data['pendingChallan'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['pendingChallan'][] = ["name"=>"Vendor"];
    $data['pendingChallan'][] = ["name"=>"Job No."];
    $data['pendingChallan'][] = ["name"=>"Job Date"];
    $data['pendingChallan'][] = ["name"=>"Product"];
    $data['pendingChallan'][] = ["name"=>"Ok Qty."];
    $data['pendingChallan'][] = ["name"=>"Pend. Qty"];
	
    
    /* Heat Treatment */
    $data['heatTreatment'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    $data['heatTreatment'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['heatTreatment'][] = ["name"=>"Date"];
    $data['heatTreatment'][] = ["name"=>"Carb Batch No"];
    $data['heatTreatment'][] = ["name"=>"Furnace No"];
    $data['heatTreatment'][] = ["name"=>"Product"];
    $data['heatTreatment'][] = ["name"=>"Job No."];
    $data['heatTreatment'][] = ["name"=>"Qty"];
    $data['heatTreatment'][] = ["name"=>"W/P"];
    $data['heatTreatment'][] = ["name"=>"Kgs"];
    $data['heatTreatment'][] = ["name"=>"Total Lot"];
    $data['heatTreatment'][] = ["name"=>"Total Kgs"];
    
    /*External Heat Treatment */
    $data['externalHeatTreatment'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    $data['externalHeatTreatment'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['externalHeatTreatment'][] = ["name"=>"Part Code"];
    $data['externalHeatTreatment'][] = ["name"=>"Part No."];
    $data['externalHeatTreatment'][] = ["name"=>"Part Type"];
    $data['externalHeatTreatment'][] = ["name"=>"Green Drg No."];
    $data['externalHeatTreatment'][] = ["name"=>"Green Rev No."];
    $data['externalHeatTreatment'][] = ["name"=>"Carb Drg No."];
    $data['externalHeatTreatment'][] = ["name"=>"Carb Rev No."];
    $data['externalHeatTreatment'][] = ["name"=>"Material Grade"];  
    $data['externalHeatTreatment'][] = ["name"=>"Status"];
    
    return tableHeader($data[$page]);
}

/* Process Table Data */
function getProcessData($data){
    $deleteParam = $data->id.",'Process'";$editButton="";$deleteButton="";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editProcess', 'title' : 'Update Process'}";

    if(empty($data->process_type)){
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    }
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->process_name,$data->dept_name,$data->remark];
}

/* Job Card Table Data */
function getJobcardData($data){
    $deleteParam = $data->id.",'Jobcard'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'editJobcard', 'title' : 'Update Jobcard'}";
    $reqParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'requiredTest', 'title' : 'Requirement'}";

    $editButton="";$deleteButton = "";$startOrder = "";$holdOrder = "";$restartOrder = '';$closeOrder="";$reopenOrder = "";$dispatchBtn = ''; $shortClose = ''; $updateJob = ''; $approveBtn = '';$printBtn="";

    if($data->order_status != 7){
        //Regular Order
        if(empty($data->md_status) && empty($data->ref_id) && empty($data->order_status)):
            $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

            $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';        
        endif;

        $shortClose = '<a class="btn btn-instagram btn-shortClose changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Short Close" flow="down" data-val="6" data-id="'.$data->id.'"><i class="sl-icon-close"></i></a>';
        if($data->material_status == 1):
            $dispatchBtn = '<a class="btn btn-success btn-request permission-write" href="javascript:void(0)" datatip="Material Request" flow="down" data-id="'.$data->id.'" data-function="materialRequest"><i class="fas fa-paper-plane" ></i></a>';
        elseif($data->material_status == 2):
            $allotParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'allotMAterial', 'title' : 'Allot Material','fnedit':'materialAllocate','fnsave':'saveAllocatedMaterial'}";
            $dispatchBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Material Allocate" flow="down" onclick="edit('.$allotParam.');"><i class="fas fa-paper-plane" ></i></a>';
               
        endif;
    }else{
        $approveBtn = '<a class="btn btn-primary btn-approve permission-approve" href="javascript:void(0)" onclick="approveJobcard('.$data->id.',7);" datatip="Approve" flow="down"><i class="fa fa-check"></i></a>';
    }
    if($data->order_status == 0):
        if($data->md_status ==1 || $data->md_status ==2):
            $startOrder = '<a class="btn btn-success btn-start materialReceived permission-modify" href="javascript:void(0)" datatip="Material Received" flow="down" data-val="3" data-id="'.$data->id.'"><i class="fa fa-check" ></i></a>';
        elseif($data->md_status ==3):
            $startOrder = '<a class="btn btn-success btn-start changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Start" flow="down" data-val="1" data-id="'.$data->id.'"><i class="ti-control-play" ></i></a>';
        endif;
    elseif($data->order_status == 2):
        $holdOrder = '<a class="btn btn-danger btn-hold changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Hold" flow="down" data-val="3" data-id="'.$data->id.'"><i class="ti-control-pause" ></i></a>';
        
        if(isset($data->pendingQty)):
            $updateQtyParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-lg', 'form_id' : 'updateJobQty', 'title' : 'Update Jobcard Qty [".$data->job_number."] Pending Qty.: ".$data->pendingQty."', 'fnedit':'updateJobQty','button':'close'}";
            $updateJob = '<a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Update Job Qty." flow="down" onclick="edit(' . $updateQtyParam . ');"><i class="ti-exchange-vertical"></i></a>';
        endif;
        
        $printParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-lg', 'form_id' : 'printJobcard', 'title' : 'Print Job Card', 'fnedit':'printJobcard','button':'close'}";
        $printBtn = '<a class="btn btn-warning permission-modify" href="javascript:void(0)" datatip="Print" flow="down" onclick="edit(' . $printParam . ');"><i class="fas fa-print"></i></a>';
    elseif($data->order_status == 3):
        $restartOrder = '<a class="btn btn-success btn-restart changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Restart" flow="down" data-val="2" data-id="'.$data->id.'"><i class="ti-control-play" ></i></a>';
    elseif($data->order_status == 4):
        $shortClose = '';
        $closeOrder = '<a class="btn btn-dark btn-close changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Close" flow="down" data-val="5" data-id="'.$data->id.'"><i class="ti-close" ></i></a>';
    elseif($data->order_status == 5):
        $shortClose = '';
        $reopenOrder = '<a class="btn btn-primary btn-reoprn changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Reopen" flow="down" data-val="4" data-id="'.$data->id.'"><i class="ti-reload" ></i></a>';
    endif;
	$jobNo = '<a href="'.base_url($data->controller."/view/".$data->id).'">'.$data->job_number.'</a>';
	
	// last activity
    $firstdate = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
    $seconddate = date('Y-m-d', strtotime('-2 day', strtotime(date('Y-m-d'))));
    $thirdate = date('Y-m-d', strtotime('-3 day', strtotime(date('Y-m-d'))));
    $lastAdate = date('Y-m-d', strtotime($data->last_activity)); 

    $color='';
    if($lastAdate >= $firstdate) { $color="text-primary"; } 
	elseif($lastAdate == $seconddate) { $color="text-dark"; } 
	else { $color="text-danger"; }

    $last_activity = '<a href="javascript:void(0);" class="'.$color.' viewLastActivity" data-trans_id="'.$data->id.'" data-job_no="'.$data->job_number.'" datatip="View Last Activity" flow="down"><b>'.$data->last_activity.'</b></a>';

	//$last_activity = '<a href="javascript:void(0);" class="viewLastActivity" data-trans_id="'.$data->trans_id.'" data-job_no="'.(getPrefixNumber($data->job_prefix,$data->job_no)).'" datatip="View Last Activity" flow="down">'.$data->last_activity.'</a>';

    $type = ($data->job_category == 0) ? 'Manufacturing' : 'Jobwork';

    $generateScrape = "";
    if($data->order_status == 5):
        $generateScrapeParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'generateScrape', 'title' : 'Generate Scrape' , 'fnedit' : 'generateScrape' , 'fnsave' : 'saveScrape' }";

        $generateScrape = '<a class="btn btn-dark btn-edit permission-modify" href="javascript:void(0)" datatip="Generate Scrape" flow="down" onclick="edit('.$generateScrapeParam.');"><i class="icon-Trash-withMen" style="font-size:18px;font-weight: 900;" ></i></a>';
    endif;

    $action = getActionButton($printBtn.$approveBtn.$updateJob.$dispatchBtn.$startOrder.$holdOrder.$restartOrder.$closeOrder.$shortClose.$reopenOrder.$generateScrape.$editButton.$deleteButton);
    return [$action,$data->sr_no,$jobNo,$data->wo_no,date("d-m-Y",strtotime($data->job_date)),$data->party_code,$data->full_name,floatVal($data->qty),$data->order_status_label,$data->remark,$last_activity];
}


/* Material Request Data */
function getMaterialRequest($data){
    $deleteParam = $data->id.",'Request'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'materialRequest', 'title' : 'Material Request'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,(!empty($data->job_no))?$data->job_number:"General Request",(!empty($data->req_date))?date("d-m-Y",strtotime($data->req_date)):"",$data->req_item_name,$data->req_qty." ( ".$data->unit_name." )"];
}

/* Jobwork Order Data */
function getJobWorkOrderData($data){
    $closeOrder="";$printBtnFull="";$editButton="";$deleteButton="";
    if($data->is_active == 1){
        $deleteParam = $data->id.",'Job Work Order'";
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editJobOrder', 'title' : 'Update Job Work Order'}";
        $closeParam = $data->id.",'Job Work Order'";

        $editButton = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success permission-write" datatip="Edit" flow="down"><i class="ti-pencil-alt" ></i></a>'; //'<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
        $closeOrder = '<a href="javascript:void(0)" class="btn btn-dark" onclick="closeOrder('.$closeParam.');" datatip="De-Active" flow="down"><i class="ti-close"></i></a>';
    }
    $printBtnFull = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/jobworkOrderChallanFull/'.$data->id).'" target="_blank" datatip="Full Page Print" flow="down"><i class="fas fa-print" ></i></a>';
	$action = getActionButton($closeOrder.$printBtnFull.$editButton.$deleteButton);
    return [$action,$data->sr_no,formatDate($data->order_date),getPrefixNumber($data->trans_prefix,$data->trans_no),$data->party_name,$data->remark];
}

function getJobWorkData($data){
    $returnBtn =""; $printBtn=""; $editButton="";$deleteButton=""; $ewbBtn=""; $approveBtn=""; $pending_qty =0;
    if($data->is_approve == 0):
        if($data->received_qty <= 0):
            $deleteParam = $data->id.",'Job Work'";
            $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
        else:
            $approveParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'approveJobwork', 'title' : 'Approve Jobwork', 'button':'close', 'fnedit':'approveJobWork'}";
            $approveBtn = '<a class="btn btn-primary btn-approve permission-approve" href="javascript:void(0)" onclick="edit('.$approveParam.');" datatip="Approve" flow="down"><i class="fa fa-check"></i></a>';
        endif;
        $editParam = "{'id' : ".$data->jobwork_id.", 'modal_id' : 'modal-xl', 'form_id' : 'editJobwork', 'title' : 'Update Jobwork'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

        $returnParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'jobWorkReturn', 'title' : 'Return Jobwork [ ".$data->full_name." - ".$data->process_name." ]', 'fnedit':'jobWorkReturn', 'fnsave':'jobWorkReturnSave'}";
        $returnBtn = '<a class="btn btn-info btn-edit" href="javascript:void(0)" datatip="Return Jobwork" flow="down" onclick="edit('.$returnParam.');"><i class="fas fa-reply"></i></a>';
    endif;
    
    if(empty($data->ewb_no)):
        $ewbParam = "{'id' : ".$data->jobwork_id.",party_id:".$data->vendor_id.", 'modal_id' : 'modal-xl', 'form_id' : 'generateEwayBill', 'title' : 'Eway Bill'}";
        $ewbBtn = '<a class="btn btn-dark permission-write" href="javascript:void(0)" datatip="Eway Bill" flow="down" onclick="ewayBill('.$ewbParam.');"><i class="fa fa-truck" ></i></a>';
    endif;
    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/jobworkOutChallan/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    
    $pending_qty = floatVal($data->qty) - floatVal($data->received_qty);
    $pencom_qty = floatVal($data->com_qty) - floatVal($data->received_com_qty);
    $pencom_qty = round(($pending_qty * $data->wpp),2);
    
    $action = getActionButton($returnBtn.$printBtn.$ewbBtn.$approveBtn.$editButton.$deleteButton);
    return [$action,$data->trans_number,date("d-m-Y",strtotime($data->entry_date)),$data->party_name,$data->full_name,$data->process_name,floatVal($data->qty),floatVal($data->com_qty),floatVal($data->received_qty),floatVal($data->received_com_qty),floatVal($pending_qty),floatVal($pencom_qty),$data->job_status,$data->print_status_tab];
}

/* Rejection Comment Table Data */
function getRejectionCommentData($data){
    if($data->type == 1):
        $deleteParam = $data->id.",'Rejection Comment'";
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editRejectionComment', 'title' : 'Update Rejection Comment'}";
    
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	    $action = getActionButton($editButton.$deleteButton);
        return [$action,$data->sr_no,$data->remark];
    else:
        $deleteParam = $data->id.",'Idle Reason'";
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editRejectionComment', 'title' : 'Update Idle Reason'}";
    
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
   
	    $action = getActionButton($editButton.$deleteButton);
        return [$action,$data->sr_no,$data->code,$data->remark];
    endif;
}

/* Production Opration Data */
function getProductionOperationData($data){
    $deleteParam = $data->id.",'Production Operation'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editProductionOperation', 'title' : 'Update Production Operation'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->operation_name];
}

/* Product Option Data */
function getProductOptionData($data){

	$btn = '<div class="btn-group" role="group" aria-label="Basic example">
				<button type="button" class="btn btn-twitter productKit permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->full_name.'" data-button="both" data-modal_id="modal-lg" data-function="addProductKitItems" data-form_title="Create Material BOM" datatip="BOM" flow="down"><i class="fas fa-dolly-flatbed"></i></button>
				
				<button type="button" class="btn btn-info addProductProcess permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_code.'" data-button="close" data-modal_id="modal-lg" data-function="addProductProcess" data-form_title="Set Product Process" datatip="View Process" flow="down"><i class="fa fa-list"></i></button>

				<!-- <button type="button" class="btn btn-info viewItemProcess permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_code.'" data-button="close" data-modal_id="modal-xl" data-function="viewProductProcess" data-form_title="Set Product Process" datatip="View Process" flow="down"><i class="fa fa-list"></i></button> -->
				
				<button type="button" class="btn btn-twitter addProductOption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_code.'" data-button="both" data-modal_id="modal-lg" data-function="addCycleTime" data-fnsave="saveCT" data-form_title="Set Cycle Time" datatip="Cycle Time" flow="down"><i class="fa fa-clock"></i></button>
				
				<button type="button" class="btn btn-info addProductOption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_code.'" data-button="both" data-modal_id="modal-lg" data-function="addToolConsumption" data-fnsave="saveToolConsumption" data-form_title="Set Tool Consumption" datatip="Tool Consumption" flow="left"><i class="fas fa-wrench"></i></button>
            </div>';

    return [$data->sr_no,$data->full_name,$data->bom,$data->process,$data->cycleTime,$data->tool,$btn];
}

/* Process Setup Data */
function getProcessSetupData($data){
    $acceptBtn = "";$editButton = "";
    if(empty($data->setup_start_time)):
        $acceptBtn = '<a class="btn btn-success permission-write" onclick="acceptJob('.$data->id.')" href="javascript:void(0)"  datatip="Accept" flow="down"><i class="fa fa-check"></i></a>'; 
    else:
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editProcessSetup', 'title' : 'Process Setup', 'fnedit' : 'processSetup'}";

        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Finish Setup" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    endif;    

    $action = getActionButton($acceptBtn.$editButton);
    return [$action,$data->sr_no,date("d-m-Y",strtotime($data->request_date)),$data->status,$data->setup_type_name,$data->setter_name,$data->setup_note,$data->job_number,$data->item_code,$data->process_name,(!empty($data->machine_code) || !empty($data->machine_name))?'[ '.$data->machine_code.' ] '.$data->machine_name:"",$data->inspector_name,(!empty($data->setup_start_time))?date("d-m-Y h:i:s A",strtotime($data->setup_start_time)):"",(!empty($data->setup_end_time))?date("d-m-Y h:i:s A",strtotime($data->setup_end_time)):"",$data->duration,$data->setter_note];
}

/* Line Inspection Data */
function getLineInspectionData($data){
    $btnParam = ['ref_id'=>$data->id,'product_id'=>$data->product_id,'process_id'=>$data->process_id,'job_card_id'=>$data->job_card_id,'product_name'=>$data->product_code,'process_name'=>$data->process_name,'pending_qty'=>$data->pending_qty,'mindate'=>$data->minDate,'modal_id'=>'modal-xxl','form_id'=>'lineInspectionFrom','title'=>'Line Inspection'];

    $button = "<a class='btn btn-warning getForward permission-modify' href='javascript:void(0)' datatip='Forward' flow='down' onclick='lineInspection(".json_encode($btnParam).");'><i class='fas fa-paper-plane' ></i></a>";

    $action = getActionButton($button);

    return [$action,$data->sr_no,$data->job_number,$data->process_name,$data->product_code,(!empty($data->party_name))?$data->party_name:"In House",$data->in_qty,$data->out_qty,$data->rejection_qty,$data->rework_qty,$data->pending_qty];
}
///--------------------
/* Process Approval Table Data */
/* function getProcessApprovalData($data){
    $jobNo = '<a href="'.base_url($data->controller.'/list/'.$data->id).'">'.$data->job_prefix.$data->job_no.'</a>';

    $type = (empty($data->ref_id))?'Regular':'Rework';

    return [$data->sr_no,$jobNo,date("d-m-Y",strtotime($data->job_date)),date("d-m-Y",strtotime($data->delivery_date)),$type,$data->party_code,$data->challan_no,$data->item_code,$data->qty,$data->order_status,$data->remark];
} */


//JobWork Invoice Data created BY Karmi 13/05/2022

function getJobWorkInvoiceData($data){
    $deleteParam = $data->trans_main_id.",'Invoice'";

    $printBtn = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Invoice" flow="down" data-id="'.$data->trans_main_id.'" data-function="purchaseInvoice_pdf"><i class="fa fa-print"></i></a>';

    $editButton = '<a class="btn btn-success btn-edit permission-modify"  datatip="Edit" flow="down" href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($printBtn.$editButton.$deleteButton);    

    return [$action,$data->sr_no,$data->trans_number,$data->doc_no,formatDate($data->trans_date),$data->party_name,$data->net_amount];
   
}

//Created By Karmi @17/05/2022 For Return Tab Data
function getJobWorkReturnTabData($data){
    $approveParam = $data->id.",'recive Qty'";
    $returnBtn =""; $printBtn=""; $editButton="";$deleteButton="";$pending_qty =0;
    $printBtn = '<a class="btn btn-info btn-edit" href="'.base_url($data->controller.'/jobworkOutChallan/'.$data->ref_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    $recive_qty = $data->qty + $data->rej_qty + $data->wp_qty;
    $approveButton = '<a class="btn btn-success permission-modify" href="javascript:void(0)" onclick="approveReturn('.$approveParam.');" datatip="Approve" flow="down"><i class="fa fa-check"></i></a>';
    $JobTransNo = '<strong style="font-weight: bold;color:red;">'.$data->job_trans_no.'</strong>';
    if($data->received_qty > 0):
        $approveButton = '';
        $JobTransNo = $data->job_trans_no;
    endif;
    $action = getActionButton($printBtn);
    return [$action,$data->sr_no,$data->trans_number,date("d-m-Y",strtotime($data->entry_date)),$data->challan_no,$data->party_name,$data->full_name,$data->process_name,floatVal($recive_qty),floatVal($data->com_qty),floatval($data->rej_qty),floatval($data->wp_qty),floatval($data->bill_qty)];
}

/* Outsource Table Data */
function getOutsourceData($data){
    $returnBtn="";$inwardButton="";  $deleteButton ="";
    if($data->outsource_qty == 0):
        $deleteParam = $data->challan_id.",'Vendor Challan'";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="right"><i class="ti-trash"></i></a>';
    endif;

    $outParam = "{'job_approval_id' : " . $data->job_approval_id . ", 'job_trans_id' : ".$data->id." ,'modal_id' : 'modal-xl', 'form_id' : 'outWard', 'title' : 'Process Moved','button' : 'close'}";  
    $moveBtn = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Receive Material" flow="right" onclick="vendorMaterialReturn(' . $outParam . ');"><i class="fas fa-paper-plane"></i></a>';
    $printChallanBtn = '<a class="btn btn-primary" href="'.base_url($data->controller.'/jobworkOutChallan/'.$data->challan_id).'" target="_blank" datatip="Print" flow="right"><i class="fas fa-print" ></i></a>';
    $printChallan = '<a class="btn btn-primary" href="'.base_url($data->controller.'/jobworkOutsourceChallan/'.$data->challan_id).'" target="_blank" datatip="Subsidiary Print" flow="right"><i class="fas fa-print" ></i></a>';


    $action = getActionButton($printChallan.$printChallanBtn.$returnBtn.$moveBtn.$deleteButton);

    return [$action,$data->sr_no,date('d-m-Y',strtotime($data->trans_date)),$data->trans_number,$data->job_number,$data->party_name,$data->full_name,$data->process_name,floatVal($data->qty),floatVal($data->outsource_qty),floatVal($data->pending_qty),''];
}

/** Get Primary CFT Data */
function getPrimaryCFTData($data){
    $okBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-md', 'form_id' : 'okOutWard', 'title' : 'Ok ','button' : 'both','fnedit' : 'convertToOk','fnsave' : 'saveCFTQty'}";
    $rejBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-lg', 'form_id' : 'rejOutWard', 'title' : 'Rejection ','button' : 'both','fnedit' : 'convertToRej','fnsave' : 'saveCFTQty'}";
    $rwBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-lg', 'form_id' : 'rwOutWard', 'title' : 'Rework ','button' : 'both','fnedit' : 'convertToRw','fnsave' : 'saveCFTQty'}";
    $hldBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-md', 'form_id' : 'suspOutWard', 'title' : 'Hold ','button' : 'both','fnedit' : 'convertToHold','fnsave' : 'saveCFTQty'}";
    $udBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-md', 'form_id' : 'udOutWard', 'title' : 'Under Deviation ','button' : 'both','fnedit' : 'convertToUD','fnsave' : 'saveCFTQty'}";

    $operation_type ='';
    if($data->operation_type == 1){ $operation_type = 'Rejection'; }
    elseif($data->operation_type == 2){ $operation_type = 'Rework'; }
    elseif($data->operation_type == 3){ $operation_type = 'Hold'; }
    elseif($data->operation_type == 4){  $operation_type = 'OK'; }
    elseif($data->operation_type == 5){ $operation_type = 'Under Deviation';  }

	$okBtn='';$rejBtn='';$rwBtn='';$hldBtn='';$printBtn = "";$udBtn = '';
    if($data->pending_qty > 0 && $data->entry_type != 2){
        $okBtn='<a  onclick="edit('. $okBtnParam . ')"  class="btn btn-success btn-edit permission-modify" datatip="Ok" flow="down"><i class="ti-check"></i></a>';
        $rejBtn = '<a onclick="edit(' . $rejBtnParam . ')"  class="btn btn-danger btn-edit permission-modify" datatip="Rejection" flow="down"><i class="ti-close"></i></a>';
        $rwBtn = '<a  onclick="edit('. $rwBtnParam . ')"  class="btn btn-info btn-edit permission-modify" datatip="Rework" flow="down"><i class=" fas fa-retweet"></i></a>';
        $hldBtn='<a onclick="edit(' . $hldBtnParam . ')"  class="btn btn-primary" datatip="Hold" flow="down"><i class="fas fa-pause"></i></a>';
        $udBtn='<a onclick="edit(' . $udBtnParam . ')"  class="btn btn-warning" datatip="Under Deviation" flow="down"><i class="fab fa-dochub"></i></a>';
    }else{
        if($data->operation_type == 1):
            $printBtn = '<a href="' . base_url('production/primaryCFT/printTag/REJ/'. $data->id) . '" target="_blank" class="btn btn-dark waves-effect waves-light" datatip="Rejection Tag" flow="down"><i class="fas fa-print"></i></a>';
        elseif($data->operation_type == 2):
            $printBtn = '<a href="' . base_url('production/primaryCFT/printTag/REW/'. $data->id) . '" target="_blank" class="btn btn-info waves-effect waves-light" datatip="Rework Tag" flow="down"><i class="fas fa-print"></i></a>';
        elseif($data->operation_type == 3):
            $printBtn = '<a href="' . base_url('production/primaryCFT/printTag/SUSP/'. $data->id) . '" target="_blank" class="btn btn-warning waves-effect waves-light" datatip="Suspected Tag" flow="down"><i class="fas fa-print"></i></a>';
        endif;
    }
    $action = getActionButton($okBtn.$rejBtn.$rwBtn.$hldBtn.$udBtn.$printBtn);
    $color=''; if($data->entry_type == 4 || $data->ref_type==4){ $color='text-danger font-weight-bold'; }
    $refType = '<span class="'.$color.'" >'.(($data->entry_type == 4  || $data->ref_type==4)?'Under Deviation':'Reguler').'</span>';
    return [$action,$data->sr_no,$data->job_number,$data->full_name,formatDate($data->entry_date),((!empty($data->tag_prefix) && !empty($data->tag_no))?$data->tag_prefix.sprintf('%05d',$data->tag_no):''),$data->process_name,$data->item_code,$data->emp_name,$data->qty,$data->pending_qty,$operation_type,$refType];
}

/** Get Final CFT Data */
function getFinalCFTData($data){
    $okBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-md', 'form_id' : 'okOutWard', 'title' : 'Ok ','button' : 'both','fnedit' : 'convertToOk','fnsave' : 'saveCFTQty'}";
    $rejBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-lg', 'form_id' : 'rejOutWard', 'title' : 'Rejection ','button' : 'both','fnedit' : 'convertToRej','fnsave' : 'saveCFTQty'}";
    $rwBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-lg', 'form_id' : 'rwOutWard', 'title' : 'Rework ','button' : 'both','fnedit' : 'convertToRw','fnsave' : 'saveCFTQty'}";
    $udBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-md', 'form_id' : 'udOutWard', 'title' : 'Under Deviation ','button' : 'both','fnedit' : 'convertToUD','fnsave' : 'saveCFTQty'}";

    $okBtn='';$rejBtn='';$rwBtn=''; $confirmBtn='';$udBtn='';$rejScrapBtn='';$printBtn='';
    if($data->entry_type == 2 || $data->entry_type == 4){
        $okBtn='<a  onclick="edit('. $okBtnParam . ')"  class="btn btn-success btn-edit permission-modify" datatip="Ok" flow="down"><i class="ti-check"></i></a>';
        $rejBtn = '<a onclick="edit(' . $rejBtnParam . ')"  class="btn btn-danger btn-edit permission-modify" datatip="Rejection" flow="down"><i class="ti-close"></i></a>';
        $rwBtn = '<a  onclick="edit('. $rwBtnParam . ')"  class="btn btn-info btn-edit permission-modify" datatip="Rework" flow="down"><i class=" fas fa-retweet"></i></a>';
        $confirmBtn = '<a class="btn btn-primary btn-approve permission-approve" href="javascript:void(0)" onclick="confirmCft('.$data->id.');" datatip="Confirm" flow="down"><i class=" fas fa-thumbs-up
        "></i></a>';
        $udBtn='<a onclick="edit(' . $udBtnParam . ')"  class="btn btn-warning" datatip="Under Deviation" flow="down"><i class="fab fa-dochub"></i></a>';

    }else{
        if($data->operation_type == 1){
            $rejScrapBtn = '<a class="btn btn-primary btn-approve permission-approve" href="javascript:void(0)" onclick="convertToScrap('.$data->id.');" datatip="Convert To Rej Scrap" flow="down"><i class="icon-Folder-Trash"></i></a>';
            if($data->stage_type == 3){
                $printBtn = '<a href="' . base_url('production/finalCFT/printTag/REJ/'. $data->id) . '" target="_blank" class="btn btn-dark waves-effect waves-light" datatip="Rejection Tag" flow="down"><i class="fas fa-print"></i></a>';
            }else{
                $printBtn = '<a href="' . base_url('production/primaryCFT/printTag/REJ/'. $data->id) . '" target="_blank" class="btn btn-dark waves-effect waves-light" datatip="Rejection Tag" flow="down"><i class="fas fa-print"></i></a>';
            }
        }
        
    }
	$operation_type ='';
    if($data->operation_type == 1){ $operation_type = 'Rejection';
    }elseif($data->operation_type == 2){ $operation_type = 'Rework'; }
    elseif($data->operation_type == 3){ $operation_type = 'Hold'; }
    elseif($data->operation_type == 4){ $operation_type = 'OK'; }
    elseif($data->operation_type == 5){ $operation_type = 'Under Deviation'; }
	
    $action = getActionButton($printBtn.$rejScrapBtn.$confirmBtn.$okBtn.$rejBtn.$rwBtn.$udBtn);
    $color=''; if($data->ref_type == 4 || $data->ref_type==3){ $color='text-danger font-weight-bold'; }
    $refType = '<span class="'.$color.'" >'.(($data->ref_type == 4 || $data->ref_type==3)?'Under Deviation':'Reguler').'</span>';
    return [ $action,$data->sr_no,$data->job_number,$data->full_name,formatDate($data->entry_date),((!empty($data->pcft_tag_prefix) && !empty($data->pcft_tag_no))?$data->pcft_tag_prefix.sprintf('%05d',$data->pcft_tag_no):''),((!empty($data->tag_prefix) && !empty($data->tag_no))?$data->tag_prefix.sprintf('%05d',$data->tag_no):''),$data->process_name,$data->item_code,$data->emp_name,$data->qty,$data->pending_qty,$operation_type,$data->rejection_reason,$data->parameter,((!empty($data->party_name) && $data->mfg_by == 2)?$data->party_name:'In House'),$refType];
}

/** Get Under Deviation CFT Data */
function getUdCFTData($data){
    
    $rejBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-md', 'form_id' : 'rejOutWard', 'title' : 'Rejection ','button' : 'both','fnedit' : 'convertToUdRej','fnsave' : 'saveCFTQty'}";
   

    $okBtn='';$rejBtn='';$rwBtn=''; $confirmBtn='';
    if($data->entry_type != 4){
        $okBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-md', 'form_id' : 'okOutWard', 'title' : 'UD Ok ','button' : 'both','fnedit' : 'convertToUdOk','fnsave' : 'saveCFTQty'}";
        $okBtn='<a  onclick="edit('. $okBtnParam . ')"  class="btn btn-success btn-edit permission-modify" datatip="Ok" flow="down"><i class="ti-check"></i></a>';
        $rejBtn = '<a onclick="edit(' . $rejBtnParam . ')"  class="btn btn-danger btn-edit permission-modify" datatip="Rejection" flow="down"><i class="ti-close"></i></a>';
    }

    $action = getActionButton($confirmBtn.$okBtn.$rejBtn.$rwBtn);
    return [ $action,$data->sr_no,formatDate($data->entry_date),$data->job_number,$data->full_name,$data->process_name,floatval($data->qty),floatval($data->pending_qty),$data->remark,$data->rr_stage,$data->rw_process_id];
}
/** Get Rework Data */
function getReworkData($data){
    
	$reworkBtn ='<a  href="'.base_url($data->controller."/reworkDetail/".$data->id).'"  class="btn btn-success btn-edit permission-modify" datatip="Rework Movement" flow="down"><i class=" fas fa-eye"></i></a>'; 
    $action = getActionButton($reworkBtn);
    return [ $action,$data->sr_no,formatDate($data->entry_date),($data->tag_prefix.sprintf("%04d",$data->tag_no)),$data->job_number,$data->full_name,$data->process_name,$data->qty,$data->rejection_reason,$data->parameter,(!empty($data->party_name)?$data->party_name:'In House')];
}

/**  Product Setup Data Setter */
function getProductSetupData($data){
    $reqNo = ($data->req_prefix.sprintf("%03d",$data->req_no));$acceptBtn='';
    if(empty($data->status)){ $acceptBtn ='<a class="btn btn-success permission-write" onclick="acceptRequest('.$data->id.')" href="javascript:void(0)"  datatip="Accept" flow="down"><i class="fa fa-check"></i></a>'; }
    else{ $reqNo ='<a  href="'.base_url($data->controller."/setterReportIndex/".$data->id).'"   datatip="Setter Reports" flow="down">'. ($data->req_prefix.sprintf("%03d",$data->req_no)).'</a>';  }

    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/sar_pdf/'.$data->id).'" target="_blank" datatip="SAR Print" flow="down"><i class="fas fa-print" ></i></a>';
    $action = getActionButton($printBtn.$acceptBtn);
    return [ $action,$data->sr_no,formatDate($data->created_at),$reqNo,$data->emp_name,$data->job_number,$data->process_name,$data->full_name,(!empty($data->machine_code)?'['.$data->machine_code.'] ':'').$data->machine_name,$data->qc_inspector,$data->status_label];
}

/**  Product Setter Report Data*/
function getProductSetterReportData($data){
    $editBtn ='';$deleteButton='';$completeBtn = '';
    if($data->setup_status == 1){
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xxl', 'form_id' : 'setupApproval', 'title' : 'Edit'}";
        $editBtn= '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class=" fas fa-edit " ></i></a>';
    
        $deleteParam = $data->id.",".$data->setup_id;
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

     
        $completeBtn = '<a class="btn btn-facebook btn-delete permission-remove" href="javascript:void(0)" onclick="completeReport('.$deleteParam.');" datatip="Compelete" flow="down"><i class=" fas fa-check-circle"></i></a>';

    }
    $action = getActionButton($completeBtn.$editBtn.$deleteButton);
    return [ $action,$data->sr_no,formatDate($data->created_at),$data->setup_start_time,$data->setup_end_time,$data->setter_note,$data->setup_status_label,$data->qci_note];
}

/**  Product Setup Data Setter */
function getAsignSetupInspData($data){
    $reqNo = ($data->req_prefix.sprintf("%03d",$data->req_no));$asignBtn='';
    
    if(empty($data->qci_id)){ 
        $asignParam="{'id' : " . $data->id . " }";

        $asignBtn='<a  onclick="asignInspector('. $asignParam . ')"  class="btn btn-success btn-edit permission-modify" datatip="Asign Inspector" flow="down"><i class="ti-check"></i></a>';
    }
    $action = getActionButton($asignBtn);
    return [ $action,$data->sr_no,formatDate($data->created_at),$reqNo,$data->emp_name,$data->job_number,$data->process_name,$data->full_name,(!empty($data->machine_code)?'['.$data->machine_code.'] ':'').$data->machine_name,$data->qc_inspector,$data->status_label];
}

/**  Product Setup Data Setter */
function getSetupApprovalData($data){
    $reqNo = ($data->req_prefix.sprintf("%03d",$data->req_no));
    $acceptBtn='';$aprvBtn = '';
    if(empty($data->qc_accepted_at))
    { 
        $acceptBtn ='<a class="btn btn-success permission-write" onclick="acceptRequest('.$data->id.')" href="javascript:void(0)"  datatip="Accept" flow="down"><i class="fa fa-check"></i></a>'; 
    }elseif($data->setup_status == 4){
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xxl', 'form_id' : 'setupApproval', 'title' : 'Setup Approval','fnedit' : 'getSetupApproval','fnsave' : 'saveSetupApprovalData'}";

        $aprvBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Setup Approve" flow="down" onclick="edit('.$editParam.');"><i class=" fas fa-location-arrow " ></i></a>';
    }
    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/sar_pdf/'.$data->setup_id).'" target="_blank" datatip="SAR Print" flow="down"><i class="fas fa-print" ></i></a>';
    $action = getActionButton($printBtn.$acceptBtn.$aprvBtn);
    return [ $action,$data->sr_no,formatDate($data->created_at),$reqNo,$data->job_number,$data->process_name,$data->full_name,(!empty($data->machine_code)?'['.$data->machine_code.'] ':'').$data->machine_name,$data->emp_name,$data->setter_note,$data->setup_status_label,$data->qci_note];
}

function getPIRData($data){
    $editBtn = '';$deleteBtn='';
    //$printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/pir_pdf/'.$data->id).'" target="_blank" datatip="PIR Print" flow="down"><i class="fas fa-print" ></i></a>';
    
    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/printPir/'.$data->id).'" target="_blank" datatip="PIR Print" flow="down"><i class="fas fa-print" ></i></a>';
    
    if($data->order_status == 2){  
        $editBtn = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit " datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        $deleteParam = $data->id;
        $deleteBtn = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    }
   
    $action = getActionButton($printBtn.$editBtn.$deleteBtn);
    return [ $action,$data->sr_no,formatDate($data->trans_date),$data->trans_no,$data->job_number,$data->process_name,$data->full_name,(!empty($data->machine_code)?'['.$data->machine_code.'] ':'').$data->machine_name,$data->emp_name,$data->remark];
}

function getPendingPIRData($data){
   
   
    $pirBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/addPirReport/'.$data->job_card_id.'/'.$data->process_id.'/'.$data->machine_id).'"  datatip="Add Report" flow="down"><i class=" fas fa-clipboard-list
    " ></i></a>';
    $action = getActionButton($pirBtn);
    return [ $action,$data->sr_no,$data->job_number,$data->full_name,$data->process_name,(!empty($data->machine_code)?'['.$data->machine_code.'] ':'').$data->machine_name,$data->no_of_pir];
}

function getPendingChallanData($data){
    $creatParam = "{'party_id' : ".$data->vendor_id.",'party_name' : '".$data->party_name."'}";

    $createBtn = '<a href="javascript:void(0)" class="btn btn-info btn-delete permission-remove" onclick="createVendorChallan('.$creatParam.');" datatip="Create Challan" flow="down"><i class=" fas fa-plus-circle
    "></i></a>';
    $action = getActionButton($createBtn);
    return [ $action,$data->sr_no,$data->party_name,$data->job_number,formatDate($data->entry_date),$data->full_name,$data->qty,$data->pending_qty];
}

/* Heat Treatment Table Data */
function getHeatTreatmentData($data){
    $deleteParam = $data->id.",' Lot'";$editButton="";$deleteButton="";$completeBtn ="";$printBtn ="";
    if($data->status == 0){
        $editButton = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit " datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
        $completeParam = $data->id.",' Lot'";
        $completeBtn = '<a class="btn btn-success btn-delete permission-remove" href="javascript:void(0)" onclick="complete('.$completeParam.');" datatip="Complete" flow="down"><i class="fas fa-check-circle"></i></a>';

        $printBtn = '<a class="btn btn-info btn-edit permission-approve" href="'.base_url($data->controller.'/printMsOutput/'.$data->id).'" target="_blank" datatip="Print Test Report" flow="down"><i class="fas fa-print" ></i></a>';

    }

    $msOutputBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/addMSResultOutput/'.$data->id).'"  datatip="Add MS Result" flow="down"><i class=" fas fa-clipboard-list" ></i></a>';
  
	$action = getActionButton($msOutputBtn.$printBtn.$completeBtn.$editButton.$deleteButton);
    return [$action,$data->sr_no,formatDate($data->trans_date),$data->trans_number,$data->furnace_no,$data->item_name,$data->batch_no,$data->qty,$data->wt_pcs,$data->kgs,$data->total_nos,$data->total_kgs];
}

/*External  Heat Treatment Table Data */
function getExternalHeatTreatmentData($data){ 
    $deleteParam = $data->id.",'External Heat Treatment'";$editBtn="";$deleteButton="";   $approvalButton=""; $downBottomBtn = ""; $downBatchBtn ="";
    if(empty($data->approve_by)):
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'addExternalHT', 'title' : 'Edit'}";
        $editBtn= '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="fas fa-edit" ></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

        $approvalParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'externalHtApproval', 'title' : 'External Ht Approval', 'fnedit' : 'approveExternalHT', 'fnsave' : 'saveApproveExternalHT'}";
        $approvalButton = '<a class="btn btn-info btn-approval permission-approve" href="javascript:void(0)" datatip="External Ht Approval" flow="down" onclick="edit('.$approvalParam.');"><i class="fa fa-check" ></i></a>';
    
    endif;

    $item_code = '<a href="'.base_url("externalHeatTreatment/externalProfile/".$data->id).'" datatip="View Profile" flow="down">'.$data->item_code.'</a>';

    
	$action = getActionButton($approvalButton.$editBtn.$deleteButton);
    return [$action,$data->sr_no,$item_code,$data->part_no,$data->category_name,$data->drawing_no,$data->rev_no,$data->carb_drg_no,$data->carb_rev_no,$data->material_grade,$data->status];
}


?>