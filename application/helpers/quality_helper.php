<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getQualityDtHeader($page){
    /* Gauge Header */
    $data['gauges'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['gauges'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['gauges'][] = ["name"=>"Code"];
    $data['gauges'][] = ["name"=>"Description"];
    $data['gauges'][] = ["name"=>"Required"];
    $data['gauges'][] = ["name"=>"Frequency (Months)"];

    /* Gauge Header */
    $data['gaugeSerial'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['gaugeSerial'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['gaugeSerial'][] = ["name"=>"Gauge No."];
    $data['gaugeSerial'][] = ["name"=>"Description"];
    $data['gaugeSerial'][] = ["name"=>"Make"];
    $data['gaugeSerial'][] = ["name"=>"Calibration By"];
    $data['gaugeSerial'][] = ["name"=>"Cali. Date","style"=>"width:80px;"];
    $data['gaugeSerial'][] = ["name"=>"Due Date","style"=>"width:80px;"];
    $data['gaugeSerial'][] = ["name"=>"Plan Date","style"=>"width:80px;"];

    /* Instrument Header */
    $data['instrument'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['instrument'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['instrument'][] = ["name"=>"Instruments No."];
	$data['instrument'][] = ["name"=>"Instruments Description"];
    $data['instrument'][] = ["name"=>"Least Count"];
    $data['instrument'][] = ["name"=>"Range (mm)"];
    $data['instrument'][] = ["name"=>"Cal. Freq. (Months)"];
    $data['instrument'][] = ["name"=>"History Card No."];

    /*FIR  Header */
    $data['firInward'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['firInward'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" , "srnoPosition" => 0];
    $data['firInward'][] = ["name" => "Date", "textAlign" => "center"];
    $data['firInward'][] = ["name" => "Job No", "textAlign" => "center"];
    $data['firInward'][] = ["name" => "Part", "textAlign" => "center"];
    $data['firInward'][] = ["name" => "Process", "textAlign" => "center"];
    $data['firInward'][] = ["name" => "Vendor", "textAlign" => "center"];
    $data['firInward'][] = ["name" => "Qty", "textAlign" => "center"];
    $data['firInward'][] = ["name" => "Accepted Qty", "textAlign" => "center"];
    $data['firInward'][] = ["name" => "Unaccepted Qty", "textAlign" => "center"];

    /*Pending FIR Header */
    $data['pendingFir'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['pendingFir'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" , "srnoPosition" => 0];
    $data['pendingFir'][] = ["name" => "Date", "textAlign" => "center"];
    $data['pendingFir'][] = ["name" => "Job No", "textAlign" => "center"];
    $data['pendingFir'][] = ["name" => "Part", "textAlign" => "center"];
    $data['pendingFir'][] = ["name" => "Process", "textAlign" => "center"];
    $data['pendingFir'][] = ["name" => "Vendor", "textAlign" => "center"];
    $data['pendingFir'][] = ["name" => "Qty", "textAlign" => "center"];
    $data['pendingFir'][] = ["name" => "WIP", "textAlign" => "center"];
    $data['pendingFir'][] = ["name" => "Pending Qty", "textAlign" => "center"];

    
    /*Pending FIR Header */
    $data['fir'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['fir'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" , "srnoPosition" => 0];
    $data['fir'][] = ["name" => "Date", "textAlign" => "center"];
    $data['fir'][] = ["name" => "FIR No", "textAlign" => "center"];
    $data['fir'][] = ["name" => "FG Batch No", "textAlign" => "center"];
    $data['fir'][] = ["name" => "Job No", "textAlign" => "center"];
    $data['fir'][] = ["name" => "Part", "textAlign" => "center"];
    $data['fir'][] = ["name" => "Qty", "textAlign" => "center"];
    $data['fir'][] = ["name" => "Movement Qty", "textAlign" => "center"];
    $data['fir'][] = ["name" => "Pending Qty", "textAlign" => "center"];

    /* Purchase Material Inspection Header */
    $data['materialInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['materialInspection'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['materialInspection'][] = ["name"=>"Inv No."];
    $data['materialInspection'][] = ["name"=>"Inv Date"];
    $data['materialInspection'][] = ["name"=>"Item Name"];
    $data['materialInspection'][] = ["name"=>"Received Qty"];
    $data['materialInspection'][] = ["name"=>"Status"];

    /* Assign Inspector Header */
    $data['assignInspector'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['assignInspector'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['assignInspector'][] = ["name"=>"Req. Date"];
    $data['assignInspector'][] = ["name"=>"Job Card No."];
    $data['assignInspector'][] = ["name"=>"Product Name"];
    $data['assignInspector'][] = ["name"=>"Process Name"];    
    $data['assignInspector'][] = ["name"=>"Machine No."];
    $data['assignInspector'][] = ["name"=>"Setter Name"];
    $data['assignInspector'][] = ["name"=>"Inspector Name"];
    $data['assignInspector'][] = ["name"=>"Status"];
    $data['assignInspector'][] = ["name"=>"Note"];

    /* Setup Inspection Header */
    $data['setupInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['setupInspection'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['setupInspection'][] = ["name"=>"Req. Date"];
    $data['setupInspection'][] = ["name"=>"Status"];
    $data['setupInspection'][] = ["name"=>"Setup Type"];
    $data['setupInspection'][] = ["name"=>"Setter Name"];
    $data['setupInspection'][] = ["name"=>"Setter Note"];
    $data['setupInspection'][] = ["name"=>"Job No"];
    $data['setupInspection'][] = ["name"=>"Part Name"];
    $data['setupInspection'][] = ["name"=>"Process Name"];
    $data['setupInspection'][] = ["name"=>"Machine"];
    $data['setupInspection'][] = ["name"=>"Inspector Name"];
    $data['setupInspection'][] = ["name"=>"Start Date"];
    $data['setupInspection'][] = ["name"=>"End Date"];
    $data['setupInspection'][] = ["name"=>"Duration"];
    $data['setupInspection'][] = ["name"=>"Remark"];
    $data['setupInspection'][] = ["name"=>"Attachment","textAlign"=>"center"];

    /* In Challan Header */
    $data['inChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['inChallan'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['inChallan'][] = ["name"=>"Challan No."];
    $data['inChallan'][] = ["name"=>"Challan Date"];
    $data['inChallan'][] = ["name"=>"Party Name"];
    $data['inChallan'][] = ["name"=>"Item Name"];
    $data['inChallan'][] = ["name"=>"Qty."];
    $data['inChallan'][] = ["name"=>"Remark"];

    /* Out Challan Header */
    $data['outChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['outChallan'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['outChallan'][] = ["name"=>"Challan No."];
    $data['outChallan'][] = ["name"=>"Challan Date"];
    $data['outChallan'][] = ["name"=>"Party Name"];
    $data['outChallan'][] = ["name"=>"Item Name"];
    $data['outChallan'][] = ["name"=>"Qty."];
    $data['outChallan'][] = ["name"=>"Received Qty."];
    $data['outChallan'][] = ["name"=>"Pending Qty."];
    $data['outChallan'][] = ["name"=>"Remark"];

    /* Pre Dispatch Inspect Header */
    $data['preDispatchInspect'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['preDispatchInspect'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['preDispatchInspect'][] = ["name"=>"Part Code"];
    $data['preDispatchInspect'][] = ["name"=>"Param. Count"];
    
    /* RM Inspection Data */
	$data['inspectionParam'][] = ["name"=>"#","style"=>"width:5%;","srnoPosition"=>0];
	$data['inspectionParam'][] = ["name"=>"Part Name"];
	$data['inspectionParam'][] = ["name"=>"Action","style"=>"width:10%;","textAlign"=>"center"];

    /* Gate Receipt RM for Pending GI Tbale Header */
    $data['gateReceipt'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['gateReceipt'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" , "srnoPosition" => 0];
    $data['gateReceipt'][] = ["name" => "GI No.", "textAlign" => "center"];
    $data['gateReceipt'][] = ["name" => "GI Date", "textAlign" => "center"];
    $data['gateReceipt'][] = ["name" => "Supplier", "textAlign" => "center"];
    $data['gateReceipt'][] = ["name" => "Item Name", "textAlign" => "center"];    
    $data['gateReceipt'][] = ["name" => "Batch/Serial No", "textAlign" => "center"];
    $data['gateReceipt'][] = ["name" => "Heat No", "textAlign" => "center"];
    // $data['gateReceipt'][] = ["name" => "Mill Heat No", "textAlign" => "center"];
    // $data['gateReceipt'][] = ["name" => "Forging Tracebility", "textAlign" => "center"];
    // $data['gateReceipt'][] = ["name" => "Heat Tracebility", "textAlign" => "center"];
    $data['gateReceipt'][] = ["name" => "Batch Qty", "textAlign" => "center"];

    /* Gate Receipt Other for Pending GI Tbale Header */
    $data['gateReceiptOther'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['gateReceiptOther'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" , "srnoPosition" => 0];
    $data['gateReceiptOther'][] = ["name" => "GI No.", "textAlign" => "center"];
    $data['gateReceiptOther'][] = ["name" => "GI Date", "textAlign" => "center"];
    $data['gateReceiptOther'][] = ["name" => "Supplier", "textAlign" => "center"];
    $data['gateReceiptOther'][] = ["name" => "Item Name", "textAlign" => "center"];    
    $data['gateReceiptOther'][] = ["name" => "Qty", "textAlign" => "center"];
    
    /* Control Plan Data */
	$data['controlPlan'][] = ["name"=>"#","style"=>"width:5%;","srnoPosition"=>0];
	$data['controlPlan'][] = ["name"=>"Part Name"];
	$data['controlPlan'][] = ["name"=>"Action","style"=>"width:10%;","textAlign"=>"center"];
	

	/* Reaction Plan Data */
    $data['reactionPlan'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['reactionPlan'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['reactionPlan'][] = ["name"=>"Title"];
	$data['reactionPlan'][] = ["name"=>"Action","textAlign"=>"center"];

    /* Reaction Plan Data */
    $data['indexDescription'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['indexDescription'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['indexDescription'][] = ["name"=>"Title"];
	$data['indexDescription'][] = ["name"=>"Description","textAlign"=>"center"];

    /* Sampling Plan Data */
    $data['samplingPlan'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['samplingPlan'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['samplingPlan'][] = ["name"=>"Title"];
    $data['samplingPlan'][] = ["name"=>"Control Method","textAlign"=>"center"];
   
    
    /* PFC Header */
    $data['pfc'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['pfc'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" , "srnoPosition" => 0];
    $data['pfc'][] = ["name" => "PFC Number", "textAlign" => "center"];
    $data['pfc'][] = ["name" => "Item Name", "textAlign" => "center"];
    $data['pfc'][] = ["name" => "Revision No.", "textAlign" => "center"];
    $data['pfc'][] = ["name" => "Date", "textAlign" => "center"];
    $data['pfc'][] = ["name" => "Core Team", "textAlign" => "center"];
    $data['pfc'][] = ["name" => "Jig Fixture No", "textAlign" => "center"];
    
    /* Fmea Header */
    $data['fmea'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['fmea'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" , "srnoPosition" => 0];
    $data['fmea'][] = ["name" => "FMEA Number", "textAlign" => "center"];
    $data['fmea'][] = ["name" => "PFC Operation", "textAlign" => "center"];
    $data['fmea'][] = ["name" => "Revision No.", "textAlign" => "center"];
    $data['fmea'][] = ["name" => "Date", "textAlign" => "center"];
    $data['fmea'][] = ["name" => "Cust. Rev. No.", "textAlign" => "center"];

    /* FMEA Diamention Header */
    $data['fmeaDiamention'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['fmeaDiamention'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" , "srnoPosition" => 0];
    $data['fmeaDiamention'][] = ["name" => "Parameter", "textAlign" => "center"];
    $data['fmeaDiamention'][] = ["name" => "Dimension", "textAlign" => "center"];
    $data['fmeaDiamention'][] = ["name" => "Class", "textAlign" => "center"];

    /* PFC Operation Plan Data */
	$data['pfcOperation'][] = ["name"=>"#","style"=>"width:5%;","srnoPosition"=>0];
	$data['pfcOperation'][] = ["name"=>"Process No."];
	$data['pfcOperation'][] = ["name"=>"Parameter"];
	$data['pfcOperation'][] = ["name"=>"Rev No."];
	$data['pfcOperation'][] = ["name"=>"Rev Date"];
	$data['pfcOperation'][] = ["name"=>"Action","style"=>"width:10%;","textAlign"=>"center"];

    /* FMEA Failure Mode Header */
    $data['fmeaFail'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['fmeaFail'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" , "srnoPosition" => 0];
    $data['fmeaFail'][] = ["name" => "Failure Mode	", "textAlign" => "center"];
    $data['fmeaFail'][] = ["name" => "Customer", "textAlign" => "center"];
    $data['fmeaFail'][] = ["name" => "Manufacturer", "textAlign" => "center"];
    $data['fmeaFail'][] = ["name" => "Customer Sev", "textAlign" => "center"];
    $data['fmeaFail'][] = ["name" => "Manufacturer Sev", "textAlign" => "center"];
    $data['fmeaFail'][] = ["name" => "Sev", "textAlign" => "center"];
    $data['fmeaFail'][] = ["name" => "Detection", "textAlign" => "center"];
    $data['fmeaFail'][] = ["name" => "Detec", "textAlign" => "center"];
    
    /* CP Operation Header */
    $data['cp'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['cp'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" , "srnoPosition" => 0];
    $data['cp'][] = ["name" => "CP Number", "textAlign" => "center"];
    $data['cp'][] = ["name" => "Operation", "textAlign" => "center"];
    $data['cp'][] = ["name" => "Revision No.", "textAlign" => "center"];
    $data['cp'][] = ["name" => "Date", "textAlign" => "center"];
    $data['cp'][] = ["name" => "Cust. Rev. No.", "textAlign" => "center"];

  
    /* Control Method Header */
    $data['controlMethod'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['controlMethod'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['controlMethod'][] = ["name"=>"Control Method"];
    $data['controlMethod'][] = ["name"=>"Alias"];
   
    /* CP Diamention Header */
    $data['cpDiamention'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['cpDiamention'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" , "srnoPosition" => 0];
    $data['cpDiamention'][] = ["name" => "Parameter", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "Diamention", "textAlign" => "center"];


   
    /*RQC  Header */
    $data['inwardRqc'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['inwardRqc'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" , "srnoPosition" => 0];
    $data['inwardRqc'][] = ["name" => "Job No", "textAlign" => "center"];
    $data['inwardRqc'][] = ["name" => "Part", "textAlign" => "center"];
    $data['inwardRqc'][] = ["name" => "Process", "textAlign" => "center"];
    $data['inwardRqc'][] = ["name" => "Qty", "textAlign" => "center"];
    $data['inwardRqc'][] = ["name" => "Accepted Qty", "textAlign" => "center"];
    $data['inwardRqc'][] = ["name" => "Unaccepted Qty", "textAlign" => "center"];

    /*Pending RQC Header */
    $data['rqc'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['rqc'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" , "srnoPosition" => 0];
    $data['rqc'][] = ["name" => "Date", "textAlign" => "center"];
    $data['rqc'][] = ["name" => "Job No", "textAlign" => "center"];
    $data['rqc'][] = ["name" => "Part", "textAlign" => "center"];
    $data['rqc'][] = ["name" => "Qty", "textAlign" => "center"];

    /* Material Grade For MQS header */
    $data['mqsParam'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['mqsParam'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['mqsParam'][] = ["name"=>"Material Grade"];
    $data['mqsParam'][] = ["name"=>"Standard"];
    $data['mqsParam'][] = ["name"=>"Scrap Group"];
    $data['mqsParam'][] = ["name"=>"Colour Code"];

    /* Material QC header */
    $data['materialQc'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['materialQc'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['materialQc'][] = ["name"=>"Product Name"];
    $data['materialQc'][] = ["name"=>"Raw Material"];
    $data['materialQc'][] = ["name"=>"Total Production Qty"];
    $data['materialQc'][] = ["name"=>"Report No"];
    
    /* Gauge Header With Check Box*/
    $masterGaugeSelect = '<input type="checkbox" id="masterGaugeSelect" class="filled-in chk-col-success BulkGaugeChallan" value=""><label for="masterGaugeSelect">ALL</label>';
    $data['qcGaugesChk'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['qcGaugesChk'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['qcGaugesChk'][] = ["name"=>$masterGaugeSelect,"style"=>"width:10%;","textAlign"=>"center","orderable"=>"false"];
	$data['qcGaugesChk'][] = ["name"=>"Code"];
    $data['qcGaugesChk'][] = ["name"=>"Description"];
    $data['qcGaugesChk'][] = ["name"=>"Make"];
    $data['qcGaugesChk'][] = ["name"=>"Required"];
    $data['qcGaugesChk'][] = ["name"=>"Frequency<br>(Months)"];
    $data['qcGaugesChk'][] = ["name"=>"Location"];
	$data['qcGaugesChk'][] = ["name"=>"Cal Date","style"=>"width:80px;"];
	$data['qcGaugesChk'][] = ["name"=>"Due Date","style"=>"width:80px;"];
	$data['qcGaugesChk'][] = ["name"=>"Plan Date","style"=>"width:80px;"];
     
     /* Gauge Header Without Checkbox*/
    $data['qcGauges'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['qcGauges'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['qcGauges'][] = ["name"=>"Code"];
    $data['qcGauges'][] = ["name"=>"Description"];
    $data['qcGauges'][] = ["name"=>"Make"];
    $data['qcGauges'][] = ["name"=>"Required"];
    $data['qcGauges'][] = ["name"=>"Frequency<br>(Months)"];
    $data['qcGauges'][] = ["name"=>"Location"];
	$data['qcGauges'][] = ["name"=>"Cal Date","style"=>"width:80px;"];
	$data['qcGauges'][] = ["name"=>"Due Date","style"=>"width:80px;"];
	$data['qcGauges'][] = ["name"=>"Plan Date","style"=>"width:80px;"];

    /* Instrument Header With Check Box*/
    $masterInstSelect = '<input type="checkbox" id="masterInstSelect" class="filled-in chk-col-success BulkInstChallan" value=""><label for="masterInstSelect">ALL</label>';
    $data['qcInstrumentChk'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['qcInstrumentChk'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['qcInstrumentChk'][] = ["name"=>$masterInstSelect,"style"=>"width:10%;","textAlign"=>"center","orderable"=>"false"];
	$data['qcInstrumentChk'][] = ["name"=>"Code"];
    $data['qcInstrumentChk'][] = ["name"=>"Description"];
    $data['qcInstrumentChk'][] = ["name"=>"Make"];
    $data['qcInstrumentChk'][] = ["name"=>"Required"];
    $data['qcInstrumentChk'][] = ["name"=>"Frequency<br>(Months)"];
    $data['qcInstrumentChk'][] = ["name"=>"Location"];
	$data['qcInstrumentChk'][] = ["name"=>"Cal Date","style"=>"width:80px;"];
	$data['qcInstrumentChk'][] = ["name"=>"Due Date","style"=>"width:80px;"];
	$data['qcInstrumentChk'][] = ["name"=>"Plan Date","style"=>"width:80px;"];
	
	/* Instrument Header Without Checkbox*/
    $data['qcInstrument'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['qcInstrument'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['qcInstrument'][] = ["name"=>"Code"];
    $data['qcInstrument'][] = ["name"=>"Description"];
    $data['qcInstrument'][] = ["name"=>"Make"];
    $data['qcInstrument'][] = ["name"=>"Required"];
    $data['qcInstrument'][] = ["name"=>"Frequency<br>(Months)"];
    $data['qcInstrument'][] = ["name"=>"Location"];
	$data['qcInstrument'][] = ["name"=>"Cal Date","style"=>"width:80px;"];
	$data['qcInstrument'][] = ["name"=>"Due Date","style"=>"width:80px;"];
	$data['qcInstrument'][] = ["name"=>"Plan Date","style"=>"width:80px;"];
	
	/* In Challan Header */
    $data['qcChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['qcChallan'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['qcChallan'][] = ["name"=>"Ch. No."];
    $data['qcChallan'][] = ["name"=>"Ch. Date"];
    $data['qcChallan'][] = ["name"=>"Ch. Type"];
    $data['qcChallan'][] = ["name"=>"Issue From"];
    $data['qcChallan'][] = ["name"=>"Item Name"];
    $data['qcChallan'][] = ["name"=>"Remark"];
	
	/* QC Purchase Request Header */
    $data['qcPurchaseRequest'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['qcPurchaseRequest'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['qcPurchaseRequest'][] = ["name"=>"Request Date"];
    $data['qcPurchaseRequest'][] = ["name"=>"Request No"];
    $data['qcPurchaseRequest'][] = ["name"=>"Description"];
    $data['qcPurchaseRequest'][] = ["name"=>"Make"];
    $data['qcPurchaseRequest'][] = ["name"=>"Qty"];
    $data['qcPurchaseRequest'][] = ["name"=>"Required Date"];    
    $data['qcPurchaseRequest'][] = ["name"=>"Remark"];
	
	$masterQcCheckBox = '<input type="checkbox" id="masterQcSelect" class="filled-in chk-col-success BulkQcRequest" value=""><label for="masterQcSelect">ALL</label>';
    /* QC Indent Header */
    $data['qcIndent'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['qcIndent'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['qcIndent'][] = ["name"=>$masterQcCheckBox,"style"=>"width:10%;","textAlign"=>"center","orderable"=>"false"];
    $data['qcIndent'][] = ["name"=>"Request Date"];
    $data['qcIndent'][] = ["name"=>"Request No"];
    $data['qcIndent'][] = ["name"=>"Description"];
    $data['qcIndent'][] = ["name"=>"Make"];
    $data['qcIndent'][] = ["name"=>"Qty"];
    $data['qcIndent'][] = ["name"=>"Required Date"];  

    /* QC Purchase Header */
    $data['qcPurchase'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['qcPurchase'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['qcPurchase'][] = ["name"=>"Order No."];
    $data['qcPurchase'][] = ["name"=>"Order Date"];
    $data['qcPurchase'][] = ["name"=>"Supplier"];
    $data['qcPurchase'][] = ["name"=>"Category Name"];
    $data['qcPurchase'][] = ["name"=>"Rate"];
    $data['qcPurchase'][] = ["name"=>"Order Qty"];
    $data['qcPurchase'][] = ["name"=>"Received Qty"];
    $data['qcPurchase'][] = ["name"=>"Pending Qty"];
    $data['qcPurchase'][] = ["name"=>"Delivery Date"];
    
    /* PDI Report */
    $data['pdi'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['pdi'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['pdi'][] = ["name"=>"PDI No"];
    $data['pdi'][] = ["name"=>"PDI Date"];
    $data['pdi'][] = ["name"=>"Customer Name"];
    $data['pdi'][] = ["name"=>"Item Name"];
    $data['pdi'][] = ["name"=>"Qty"];
    
    /* Calibration Item Details*/
    $data['calibrationData'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['calibrationData'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['calibrationData'][] = ["name"=>"Calibration Agency"];
    $data['calibrationData'][] = ["name"=>"Calibration No."];
    $data['calibrationData'][] = ["name"=>"Certificate File"];
    $data['calibrationData'][] = ["name"=>"Remark"];
    
    /* SAR Header */
    $data['sar'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['sar'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['sar'][] = ["name"=>"Date"];
    $data['sar'][] = ["name"=>"Jobcard"];
    $data['sar'][] = ["name"=>"Process"];
    $data['sar'][] = ["name"=>"Machine"];
    $data['sar'][] = ["name"=>"Setter"];
    $data['sar'][] = ["name"=>"Setting Time"];
    $data['sar'][] = ["name"=>"Remark"];
    
    return tableHeader($data[$page]);
}

/* Gauge Data */
function getGaugeData($data){
    $deleteParam = $data->id.",'Gauge'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editGauge', 'title' : 'Update Gauge', 'fnsave' : 'save'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $item_code = '<a href="'.base_url('gauges/indexSerial/'.$data->id).'" datatip="Serial Wise List" flow="down">'.$data->item_code.'</a>';

    $action = getActionButton($editButton.$deleteButton);    
    return [$action,$data->sr_no,$item_code,$data->item_name,$data->cal_required,$data->cal_freq];
}

/* Gauge Data Serial Wise */
function getSerialWiseData($data){
    $calParam = "{'id' : ".$data->id.",'batch_no' : '".$data->batch_no."', 'modal_id' : 'modal-lg','button' : 'close', 'form_id' : 'calibration', 'title' : 'Calibration ', 'fnedit' : 'getCalibration', 'fnsave' : 'saveCalibration'}";
    $calibrationButton = '<a class="btn btn-info btn-contact permission-modify" href="javascript:void(0)" datatip="Calibration" flow="down" onclick="editCalibration('.$calParam.');"><i class="fas fa-tachometer-alt"></i></a>';

    $lcd = (!empty($data->cal_date)) ? date('d-m-Y',strtotime($data->cal_date)) : '';
    $ncd = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-1 days")) : '';
    $pdate = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-".($data->cal_reminder+1)." days")) : '';
    if(!empty($ncd) AND (strtotime($ncd) <= strtotime(date('d-m-Y')))){$ncd = '<strong class="text-danger">'.$ncd.'</strong>';}
	if(!empty($pdate) AND (strtotime($pdate) <= strtotime(date('d-m-Y')))){$pdate = '<strong style="color:#ffbc34;">'.$pdate.'</strong>';}

    $action = getActionButton($calibrationButton);    
    return [$action,$data->sr_no,$data->batch_no,$data->item_name,$data->make_brand,$data->cal_by,$lcd,$ncd,$pdate];
}

/* Instrument Data */
function getInstrumentData($data){
    $deleteParam = $data->id.",'Instrument'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editInstrument', 'title' : 'Update Instrument', 'fnsave' : 'save'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $item_code = '<a href="'.base_url('instrument/indexSerial/'.$data->id).'" datatip="Serial Wise List" flow="down">'.$data->item_code.'</a>';

    $action = getActionButton($editButton.$deleteButton);    
    return [$action,$data->sr_no,$item_code,$data->item_name,$data->least_count,$data->instrument_range,$data->cal_freq,$data->drawing_no];
}

/* FIR Header */
function getFIRInwardData($data){
    
    $acceptBtn ='' ;
    $param = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'aceptQty', 'title' : 'Accept Inspection Qty', 'fnedit' : 'acceptFIR','fnsave' : 'saveInward'}";

    $acceptBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Accept Inspection" flow="down" onclick="edit('.$param.');"><i class=" fas fa-check-circle" ></i></a>';

    $action = getActionButton($acceptBtn);
    return [ $action,$data->sr_no,formatDate($data->entry_date),$data->job_number,$data->full_name,$data->process_name,(!empty($data->party_name)?$data->party_name:''),floatval($data->qty),floatval(abs($data->accepted_qty)),floatval($data->qty - $data->accepted_qty)];

}

/* FIR Header */
function getPendingFirData($data){
    
    // $lotBtn = '<a href="'.base_url($data->controller.'/addFirLot/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Add Report" flow="down"><i class=" fas fa-plus-circle "></i></a>';
    $param = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'addLot', 'title' : 'Add FIR Lot', 'fnedit' : 'addFirLot','fnsave' : 'saveLot'}";

    $lotBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Add Lot" flow="down" onclick="edit('.$param.');"><i class="fas fa-plus" ></i></a>';

    $action = getActionButton($lotBtn);
    return [ $action,$data->sr_no,formatDate($data->entry_date),$data->job_number,$data->full_name,$data->process_name,(!empty($data->party_name)?$data->party_name:''),floatval($data->accepted_qty),floatval(abs($data->fir_qty)),floatval($data->accepted_qty-$data->fir_qty)];

}

/* FIR Header */
function getFirData($data){
    $completeBtn="";$editBtn="";$movementBtn="";$deleteBtn = "";$setupBtn="";$seqBtn="";
    $pQty = $data->qty - $data->movement_qty-$data->total_rej_qty-$data->total_rw_qty;
    if(empty($data->status)){
        $editBtn = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="FIR Report" flow="down"><i class=" fas fa-plus-circle "></i></a>';

       
        $deleteParam = $data->id.",'FIR'";
        $deleteBtn = '<a class="btn btn-danger btn-delete " href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

        $param = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'addLot', 'title' : 'Complete', 'fnedit' : 'completeFirView','fnsave' : 'completeFir'}";
        if($data->total_ok_qty > 0){
            $completeBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Complete FIR" flow="down" onclick="completeFir('.$param.');"><i class="fas fa-check-circle" ></i></a>';
        }
        $setupParam = "{'id' : " . $data->job_approval_id . ", 'modal_id' : 'modal-lg', 'form_id' : 'setupReq', 'title' : 'Setup Request','button' : 'close','fnsave' : 'setupRequestSave'}";
        $setupBtn= '<a class="btn btn-dark btn-edit" href="javascript:void(0)" datatip="Setup Request" flow="down" onclick="setupRequest(' . $setupParam . ');"><i class=" fas fa-paper-plane"></i></a>';

        $seqParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'sequence', 'title' : 'Change Sequence', 'fnedit' : 'changeDimensionSequence','button':'close'}";
        $seqBtn = '<a class="btn btn-warning btn-edit permission-modify" href="javascript:void(0)" datatip="Change Sequence" flow="down" onclick="edit('.$seqParam.');"><i class="fas fa-align-justify " ></i></a>';
        
    }else{
        if ($data->total_ok_qty  > 0 && !empty($data->out_process_id)) :
            $moveParam = "{'id' : " . $data->job_approval_id . ",'ref_id': " . $data->id . ",'p_qty': " .((!empty($pQty) && $pQty > 0 )?$pQty:0). ", 'modal_id' : 'modal-xl', 'form_id' : 'movement', 'title' : 'Move To Next Process','button':'close','fnsave' : 'saveProcessMovement', 'fnedit' : 'processMovement','btnSave':'other'}";
            $movementBtn = '<a class="btn btn-warning btn-edit" datatip="Move to Next Process" flow="down" onclick="processMovement(' . $moveParam . ');"><i class="fa fa-step-forward"></i></a>';
        endif;
        if ($data->out_process_id == 0 && $data->total_ok_qty > 0) :
            $storeLocationParam = "{'id' : " . $data->job_card_id . ",'transid' : " . $data->job_approval_id . ",'ref_batch':" . $data->id . ",'remark':'FIR', 'modal_id' : 'modal-lg', 'form_id' : 'storeLocation', 'title' : 'Store Location','button' : 'close'}";
    
            $movementBtn= '<a class="btn btn-warning btn-edit" href="javascript:void(0)" datatip="Store Location" flow="down" onclick="storeLocation(' . $storeLocationParam . ');"><i class="fas fa-paper-plane"></i></a>';

            
        endif;
    }
    $deleteBtn = "";
    $pdfButton = '<a href="'.base_url('fir/fir_pdf/'.$data->id).'" type="button" class="btn btn-primary " datatip="Final Inspection Report Pdf" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
    $action = getActionButton($pdfButton.$seqBtn.$editBtn.$completeBtn.$movementBtn.$setupBtn.$deleteBtn);
    return [$action,$data->sr_no,formatDate($data->fir_date),$data->fir_number,$data->fg_batch_no,$data->job_number,$data->full_name,floatval($data->qty),floatval($data->movement_qty),floatval($pQty)];

}


/* Purchase Material Inspection Table Data */
function getPurchaseMaterialInspectionData($data){
    $inspection = '<a href="javscript:voide(0);" type="button" class="btn btn-success waves-effect waves-light getInspectedMaterial permission-modify" data-grn_id="'.$data->grn_id.'" data-trans_id="'.$data->id.'" data-grn_prefix="'.$data->grn_prefix.'" data-grn_no="'.$data->grn_no.'" data-grn_date="'.date("d-m-Y",strtotime($data->grn_date)).'" data-item_name="'.$data->item_name.'" data-toggle="modal" data-target="#inspectionModel" datatip="Inspection" flow="down"><i class="fas fa-search"></i></a>';

	$action = getActionButton($inspection);
    return [$action,$data->sr_no,getPrefixNumber($data->grn_prefix,$data->grn_no),date("d-m-Y",strtotime($data->grn_date)),$data->item_name,$data->qty,$data->inspection_status];
}

/* Assign Inspector Data */
function getAssignInspectorData($data){
    $editButton = "";
    if($data->status != 3):
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editAssignInspector', 'title' : 'Assign Inspector', 'fnedit' : 'assignInspector'}";

        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Assign Inspector" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    endif;

    $action = getActionButton($editButton);
    return [$action,$data->sr_no,date("d-m-Y",strtotime($data->request_date)),getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,$data->process_name,$data->machine_no,$data->setter_name,$data->inspector_name,$data->assign_status,$data->remark];
}

/* Setup Inspector Data */
function getSetupInspectionData($data){
    $editButton = "";$attachmentLink = "";$acceptInspection = "";

    if(!empty($data->inspection_start_date)):
        if(!empty($data->setup_end_time) && !empty($data->qci_id)):
            $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editSetupInspection', 'title' : 'Setup Inspection', 'fnedit' : 'setupInspection'}";

            $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Setup Inspection" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        endif;
        
        if(!empty($data->attachment)):
            $attachmentLink = '<a href="'.base_url('assets/uploads/setup_ins_report/'.$data->attachment).'" class="btn btn-outline-info waves-effect waves-light"><i class="fa fa-arrow-down"> Download</a>';
        endif;
    else:
        if(!empty($data->qci_id)):
            $acceptInspection = '<a class="btn btn-success btn-start permission-modify" href="javascript:void(0)" datatip="Accept Inspection" flow="down" onclick="acceptInspection('.$data->id.');"><i class="fas fa-check" ></i></a>';
        endif;
    endif;

    $action = getActionButton($acceptInspection.$editButton);
    return [$action,$data->sr_no,date("d-m-Y",strtotime($data->request_date)),$data->status,$data->setup_type_name,$data->setter_name,$data->setter_note,getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,$data->process_name,(!empty($data->machine_code) || !empty($data->machine_name))?'[ '.$data->machine_code.' ] '.$data->machine_name:"",$data->inspector_name,(!empty($data->inspection_start_date))?date("d-m-Y h:i:s A",strtotime($data->inspection_start_date)):"",(!empty($data->inspection_date))?date("d-m-Y h:i:s A",strtotime($data->inspection_date)):"",$data->duration,$data->qci_note,$attachmentLink];
}

/* In-Challan Data */
function getInChallanData($data){
    $deleteParam = $data->trans_main_id.",'Challan'";

    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $returnBtn = "";
    if($data->is_returnable == 1):
        $returnParams = ['item_name'=>htmlentities($data->item_name),'item_id'=>$data->item_id,'location_id'=>$data->location_id,'batch_no'=>$data->batch_no,'ref_no'=>$data->doc_no,'ref_id'=>$data->id,'pending_qty'=>($data->qty - $data->return_qty)];
        $returnBtn = "<a href='javascript:void(0)' class='btn btn-info returnItem permission-modify' datatip='Return' flow='down' data-row='".json_encode($returnParams)."' ><i class='fas fa-share'></i></a>";
    endif;

    $action = getActionButton($returnBtn.$edit.$delete);
    return [$action,$data->sr_no,$data->doc_no,formatDate($data->challan_date),$data->party_name,$data->item_name,$data->qty,$data->item_remark];
}

/* Out-Challan Data */
function getOutChallanData($data){
    $delete = ""; $edit = ""; $returnBtn = ""; $receivedItemBtn = "";
    if($data->is_returnable == 1 && $data->receive_qty < $data->qty)
    {
		$title = 'Receive Challan - '.getPrefixNumber($data->challan_prefix,$data->challan_no);
		if($data->receive_qty == 0){
			$deleteParam = $data->trans_main_id.",'Challan'";
			$delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

			$edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
		}else{
			$receivedItemParams = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'receiveChallan', 'title' : '".$title."', 'fnedit' : 'getReceiveItem', 'button' : 'close'}";
			$receivedItemBtn = '<a class="btn btn-dark btn-receive permission-modify" href="javascript:void(0)" datatip="Received Item" flow="down" onclick="edit('.$receivedItemParams.');"><i class="fas fa-info" ></i></a>';
		}
        $returnParams = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'receiveChallan', 'title' : '".$title."', 'fnedit' : 'getReceiveItemTrans', 'fnsave' : 'saveReceiveItem', 'button' : 'both'}";

        $returnBtn = '<a class="btn btn-info btn-receive permission-modify" href="javascript:void(0)" datatip="Receive" flow="down" onclick="edit('.$returnParams.');"><i class="fas fa-reply" ></i></a>';
    }
    $pending_qty = $data->qty - $data->receive_qty;
    $printBtn = '<a class="btn btn-dribbble btn-edit permission-read" href="'.base_url($data->controller.'/out_challan_print/'.$data->id).'" target="_blank" datatip="Print Out Challan" flow="down"><i class="fas fa-print" ></i></a>';
    $action = getActionButton($printBtn.$receivedItemBtn.$returnBtn.$edit.$delete);
    return [$action,$data->sr_no,getPrefixNumber($data->challan_prefix,$data->challan_no),formatDate($data->challan_date),$data->party_name,$data->item_name,floatval($data->qty),floatval($data->receive_qty),floatval($pending_qty),$data->item_remark];
}

/* get PreDispatch Inspect Data */
function getPreDispatchInspectData($data){
    $deleteParam = $data->id.",'PreDispatch Inspection'";
    $editButton = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permision-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->item_code,$data->param_count];
}

/* RM Inspection Data */
function getInspectionParamData($data){
    $btn = '<button type="button" class="btn btn-twitter addInspectionOption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->full_name.'" data-button="both" data-modal_id="modal-md" data-function="getPreInspection" data-form_title="Product Inspection" data-srposition="0" datatip="Inspection" flow="left"><i class="fas fa-info"></i></button>';

    return [$data->sr_no,$data->full_name,$btn];
}

/* Gate Receipt RM Data */
function getGateReceiptData_old($data){
    $tcBtn = '';$reportButton = '';$pdfButton='';$approveBtn='';$rejectBtn='';$completeBtn = '';
    if(!empty($data->tc_status)){
        $tcParam = "{'mir_trans_id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'tcParameter', 'title' : 'TC Parameter', 'fnedit' : 'getTcInspectionParam', 'fnsave' : 'saveTcInspectionParam'}";
        $tcBtn = '<a class="btn btn-info btn-approval permission-modify" href="javascript:void(0)" datatip="TC Parameter" flow="down" onclick="tcInspe('.$tcParam.');"><i class="ti-files" ></i></a>';
    }
    
    if(empty($data->accepted_by)):
        $acceptParam = "{'id' : " . $data->id . ", 'mir_id' : " . $data->mir_id . ", 'status' : 1}";
        $approveBtn = '<a class="btn btn-success permission-write" onclick="acceptGI('.$acceptParam.')" href="javascript:void(0)"  datatip="Accept GR" flow="down"><i class="fa fa-check"></i></a>';
        
        $rejectParam = "{'id' : " . $data->id . ", 'mir_id' : " . $data->mir_id . ", 'status' : 2}";
        $rejectBtn = '<a class="btn btn-danger permission-write" onclick="acceptGI('.$acceptParam.')" href="javascript:void(0)"  datatip="Reject GI" flow="down"><i class="fa fa-times"></i></a>';
        $tcBtn = '';
    else:
        $completeParam = "{'id' : " . $data->id . ", 'mir_id' : " . $data->mir_id . ", 'status' : 3}";
        if($data->item_type == 3):
            if($data->iir_status == 1):
                $completeBtn = '<a class="btn btn-success permission-write" onclick="acceptGI('.$completeParam.')" href="javascript:void(0)"  datatip="Complete GR" flow="down"><i class="fa fa-check"></i></a>';
            endif;
            $reportButton = '<a href="'.base_url('gateReceipt/inInspection/'.$data->id).'" type="button" class="btn btn-info " datatip="Incoming Inspection Report" flow="down"><i class="fa fa-file-alt"></i></a>'; 
            $pdfButton = '<a href="'.base_url('gateReceipt/inInspection_pdf/'.$data->id).'" type="button" class="btn btn-warning " datatip="Incoming Inspection Report Pdf" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
        else:
            $completeBtn = '<a class="btn btn-success permission-write" onclick="acceptGI('.$completeParam.')" href="javascript:void(0)"  datatip="Complete GR" flow="down"><i class="fa fa-check"></i></a>';
        endif;
    endif;
    
    if($data->trans_status == 3){$approveBtn='';$rejectBtn='';$completeBtn = '';$reportButton = '';}
    
    $completeBtn = ''; $approveBtn = ''; $tcBtn = ''; $rejectBtn = ''; $reportButton = ''; $pdfButton = '';
    $action = getActionButton($completeBtn.$approveBtn.$rejectBtn.$tcBtn.$reportButton.$pdfButton);

    return [$action,$data->sr_no,$data->trans_prefix.sprintf("%03d",$data->trans_no),formatDate($data->trans_date),$data->party_name,$data->item_name,$data->heat_no,$data->mill_heat_no,$data->forging_tracebility,$data->heat_tracebility,$data->batch_no,$data->qty];
}

function getGateReceiptData($data){
    $tcBtn = '';$reportButton = '';$pdfButton='';$approveBtn='';$rejectBtn='';$completeBtn = '';
    if(!empty($data->tc_status) && $data->item_type == 3){
        $tcParam = "{'grn_trans_id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'tcParameter', 'title' : 'TC Parameter', 'fnedit' : 'getTcInspectionParam', 'fnsave' : 'saveTcInspectionParam'}";
        $tcBtn = '<a class="btn btn-info btn-approval permission-modify" href="javascript:void(0)" datatip="TC Parameter" flow="down" onclick="tcInspe('.$tcParam.');"><i class="ti-files" ></i></a>';
    }
    
    if(empty($data->accepted_by)):
        $acceptParam = "{'id' : " . $data->id . ", 'mir_id' : " . $data->mir_id . ", 'status' : 1}";
        $approveBtn = '<a class="btn btn-success permission-write" onclick="acceptGI('.$acceptParam.')" href="javascript:void(0)"  datatip="Accept GR" flow="down"><i class="fa fa-check"></i></a>';
        
        $rejectParam = "{'id' : " . $data->id . ", 'mir_id' : " . $data->mir_id . ", 'status' : 2}";
        $rejectBtn = '<a class="btn btn-danger permission-write" onclick="acceptGI('.$rejectParam.')" href="javascript:void(0)"  datatip="Reject GI" flow="down"><i class="fa fa-times"></i></a>';
        $tcBtn = '';
    else:
        if($data->item_type == 3):
            // if($data->iir_status == 1 && empty($data->inspection_data)):
                $completeParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-lg', 'form_id' : 'materialInspection', 'title' : 'Inspection','fnedit':'materialInspection','fnsave':'saveMaterialInspection'}";
                $completeBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Inspection" flow="down" onclick="edit('.$completeParam.');"><i class="fa fa-info" ></i></a>';
            // endif;
            $reportButton = '<a href="'.base_url('gateReceipt/inInspection/'.$data->id).'" type="button" class="btn btn-info " datatip="Incoming Inspection Report" flow="down"><i class="fa fa-file-alt"></i></a>'; 
            $pdfButton = '<a href="'.base_url('gateReceipt/inInspection_pdf/'.$data->id).'" type="button" class="btn btn-warning " datatip="Incoming Inspection Report Pdf" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
        else:
            if(empty($data->inspection_data)):
                $completeParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-lg', 'form_id' : 'materialInspection', 'title' : 'Inspection','fnedit':'materialInspection','fnsave':'saveMaterialInspection'}";
                $completeBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Inspection" flow="down" onclick="edit('.$completeParam.');"><i class="fa fa-info" ></i></a>';
            endif;
        endif;

        /* if($data->item_type == 3):
            if($data->iir_status == 1):
                $completeBtn = '<a class="btn btn-success permission-write" onclick="acceptGI('.$completeParam.')" href="javascript:void(0)"  datatip="Complete GR" flow="down"><i class="fa fa-check"></i></a>';
            endif;
            $reportButton = '<a href="'.base_url('gateReceipt/inInspection/'.$data->id).'" type="button" class="btn btn-info " datatip="Incoming Inspection Report" flow="down"><i class="fa fa-file-alt"></i></a>'; 
            $pdfButton = '<a href="'.base_url('gateReceipt/inInspection_pdf/'.$data->id).'" type="button" class="btn btn-warning " datatip="Incoming Inspection Report Pdf" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
        else:
            $completeBtn = '<a class="btn btn-success permission-write" onclick="acceptGI('.$completeParam.')" href="javascript:void(0)"  datatip="Complete GR" flow="down"><i class="fa fa-check"></i></a>';
        endif; */
    endif;
    
    $testReport = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'testReport', 'title' : 'Test Report', 'fnEdit' : 'getTestReport', 'fnsave' : 'updateTestReport','button':'close'}";
    $tcButton = '<a class="btn btn-primary btn-salary permission-modify" href="javascript:void(0)" datatip="Test Report" flow="down" onclick="updateTestReport('.$testReport.');"><i class="sl-icon-bag"></i></a>';
    
    if($data->trans_status == 3){$approveBtn='';$rejectBtn='';$completeBtn = '';if($data->tc_id!='999999'){$reportButton = '';}}
    
    $action = getActionButton($completeBtn.$approveBtn.$rejectBtn.$tcBtn.$reportButton.$pdfButton.$tcButton);
    return [$action,$data->sr_no,$data->trans_prefix.sprintf("%03d",$data->trans_no),formatDate($data->trans_date),$data->party_name,$data->item_name,$data->batch_no,$data->mill_heat_no,$data->qty];
}

/* Gate Receipt Other Data */
function getGateReceiptOtherData($data){
    $approveBtn='';$rejectBtn='';$completeBtn = '';
    
    if(empty($data->accepted_by)):
        $acceptParam = "{'mir_id' : " . $data->mir_id . ", 'status' : 1}";
        $approveBtn = '<a class="btn btn-success permission-write" onclick="acceptGI('.$acceptParam.')" href="javascript:void(0)"  datatip="Accept GR" flow="down"><i class="fa fa-check"></i></a>';

        $rejectBtn = '<a class="btn btn-danger permission-write" onclick="acceptGI('.$acceptParam.')" href="javascript:void(0)"  datatip="Reject GI" flow="down"><i class="fa fa-times"></i></a>';
    else:
        if(!empty($data->item_stock_type)):
            $completeParam = "{'mir_id' : " . $data->mir_id . ", 'status' : 3}";
            $completeBtn = '<a class="btn btn-success permission-write" onclick="acceptGI('.$completeParam.')" href="javascript:void(0)"  datatip="Complete GR" flow="down"><i class="fa fa-check"></i></a>';
        else:
            $completeParam = "{'id' : " . $data->mir_id . ", 'modal_id' : 'modal-lg', 'form_id' : 'materialInspection', 'title' : 'Inspection','fnedit':'materialInspection','fnsave':'saveMaterialInspection'}";
            $completeBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Inspection" flow="down" onclick="edit('.$completeParam.');"><i class="fa fa-info" ></i></a>';
        endif;
    endif;
    
    if($data->trans_status == 3){$approveBtn='';$rejectBtn='';$completeBtn = '';}
    $approveBtn='';$rejectBtn='';$completeBtn = '';
    $action = getActionButton($completeBtn.$approveBtn.$rejectBtn);

    return [$action,$data->sr_no,$data->trans_prefix.sprintf("%03d",$data->trans_no),formatDate($data->trans_date),$data->party_name,$data->item_name,$data->total_qty];
}

/* Control Plan Data */
function getControlPlanData($data){
    $btn = '
        <a href="'.base_url("controlPlan/pfcList/".$data->id).'" class="btn btn-twitter permission-modify" target="_blank" datatip="PFC" flow="left">PFC</a>
        
        <!--<a href="'.base_url("controlPlan/fmeaList/".$data->id).'" class="btn btn-info addFmea permission-modify" target="_blank" datatip="FMEA" flow="left">FMEA</a> -->
        
        <a href="'.base_url("controlPlan/controlPlanList/".$data->id).'" class="btn btn-twitter permission-modify" target="_blank" datatip="Control Plan" flow="left">Control Plan</a>';

    return [$data->sr_no,$data->full_name,$btn];
}

/* Title Data */
function getReactionPlanData($data){ 
    $deleteParam = $data->id.",'Reaction Plan'";
    $editParam = "{'id' : ".$data->plan_no.", 'modal_id' : 'modal-lg', 'form_id' : 'addDescription','button' : 'close', 'title' : 'Update  Reaction Plan','fnedit':'editReactionPlan'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down" ><i class="ti-trash"></i></a>';

    $viewParam = "{'id' : ".$data->plan_no.", 'modal_id' : 'modal-lg', 'form_id' : 'addDescription','button' : 'close', 'title' : 'Reaction Plan','fnedit':'getReactionPlanList'}";
    $viewBtn = '<a href="javascript:void(0)"  class="btn btn-primary  permission-read" data-id="'.$data->id.'" datatip="Reaction Plan List" flow="down" onclick="edit('.$viewParam.');"><i class="fa fa-list" ></i></a>';

    $action = getActionButton($editButton);  

    return [$action,$data->sr_no,$data->title,$viewBtn];
}

/* Description Data */
function getDescriptionData($data){ 
    $deleteParam = $data->id.",'Reaction Plan'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editDescription', 'title' : 'Update  Reaction Plan', 'fnsave' : 'save'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);  

    return [$action,$data->sr_no,$data->title,$data->description];
}

/* Sampling Plan Data */
function getSamplingPlanData($data){ 
    $deleteParam = $data->id.",'Sampling Plan'";

    $editParam = "{'id' : '".$data->plan_no."', 'modal_id' : 'modal-lg', 'form_id' : 'addSamplingPlan', 'title' : 'Update SamplingPlan','fnedit':'editSamplePlan'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $viewParam = "{'control_method' : '".$data->control_method."','sample_title' : '".$data->title."', 'modal_id' : 'modal-lg', 'form_id' : 'editSamplingPlan', 'title' : 'Update SamplingPlan','fnedit':'getSamplePlanList'}";

    $sampleList = '<a href="javascript:void(0)"  class="btn btn-primary  permission-read" data-id="'.$data->id.'" datatip="Item List" flow="down" onclick="viewSamplePlan('.$viewParam.');"><i class="fa fa-list" ></i></a>';
    
    $action = getActionButton($sampleList.$editButton);  

    return [$action,$data->sr_no,$data->title,$data->control_method];
}



function getPFCData($data){

    $deleteParam = $data->id.",'Control Plan'";
    $editButton = '<a href="'.base_url($data->controller.'/editPfc/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trashPfc(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $revisionButton = '<a href="'.base_url($data->controller.'/revisionPfc/'.$data->id).'" class="btn btn-warning btn-edit permission-modify" datatip="Revision" flow="down"><i class=" fas fa-retweet"></i></a>';
    $printBtn = '<a class="btn btn-primary" href="'.base_url('controlPlan/pfc_pdf/'.$data->id).'" target="_blank" datatip="Print PFC" flow="down"><i class="fas fa-print" ></i></a>';

    $operationParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'getItemList', 'title' : 'Operation List', 'fnedit':'getOperationList','button':'close'}";

    $operationList = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Operation List" flow="down" onclick="edit('.$operationParam.');"><i class="fa fa-list" ></i></a>';

    $action = getActionButton($printBtn.$operationList.$editButton.$deleteButton);  


    return [$action,$data->sr_no,$data->trans_number,$data->full_name,$data->app_rev_no,formatDate($data->app_rev_date),$data->core_team,$data->jig_fixture_no];
}

function getFMEAData($data){

    
    $editButton = '<a href="'.base_url($data->controller.'/editDiamention/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
    $deleteParam = $data->id.",'Control Plan'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trashFMEA(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $printBtn = '<a class="btn btn-primary" href="'.base_url('controlPlan/fmea_pdf/'.$data->id).'" target="_blank" datatip="Print FMEA" flow="down"><i class="fas fa-print" ></i></a>';
    $action = getActionButton($printBtn.$editButton.$deleteButton); 
    $fmeaNo = '<a href="'.base_url($data->controller."/diamentionList/".$data->id).'" target="_blank">'.$data->trans_number.'</a>';
    return [$action,$data->sr_no,$fmeaNo,'['.$data->process_no.'] '.$data->parameter,$data->app_rev_no,formatDate($data->app_rev_date),$data->cust_rev_no];
}

/** Diamention Data */
function getFMEADiamentionData($data){
    $deleteParam = $data->id.",'FMEA'";

    $editButton = "";$deleteButton = '';
	$parameter = '<a href="'.base_url($data->controller."/fmeaFailView/".$data->id).'" target="_blank">'.$data->parameter.'</a>';
    $editButton = '<a href="'.base_url($data->controller.'/editFailureMode/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $char_class=''; if(!empty($data->char_class)){ $char_class='<img src="' . base_url('assets/images/symbols/'.$data->char_class.'.png') . '" style="width:15px;display:inline-block;" />'; }
    $action = getActionButton($editButton.$deleteButton);
    $diamention ='';
    if($data->requirement==1){ $diamention = $data->min_req.'/'.$data->max_req.' '.$data->other_req ; }
    if($data->requirement==2){ $diamention = $data->min_req.' '.$data->other_req ; }
    if($data->requirement==3){ $diamention = $data->max_req.' '.$data->other_req ; }
    if($data->requirement==4){ $diamention = $data->other_req ; }
    return [$action,$data->sr_no,$parameter,$diamention,$char_class];
}

/** Failure Mode Data */
function getFMEAFailData($data){

    // $deleteParam = $data->id.",'FMEA'";
    $causeParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'potential_cause_form', 'title' : 'Add Potential Cause', 'fnedit':'addPotentialCause','fnsave' : 'savePotentialCause','button' : 'close'}";

    $causeBtn = "";
    $causeBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Add Potential Cause" flow="down" onclick="edit('.$causeParam.');"><i class=" fas fa-plus" ></i></a>';

    // $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trashFmea(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($causeBtn);  

    $char_class=''; if(!empty($data->class)){ $char_class='<img src="' . base_url('assets/images/symbols/'.$data->class.'.png') . '" style="width:15px;display:inline-block;" />'; }

    
    return [$action,$data->sr_no,$data->failure_mode,$data->customer,$data->manufacturer,$data->cust_sev,$data->mfg_sev,$data->sev,$data->process_detection,$data->detec];
}

/* PFC Operation Data */
function getPFCOperationData($data){
    $btn = '<a href="'.base_url("controlPlan/diamentionList/".$data->item_id."/".$data->id).'" class="btn btn-info addFmea permission-modify" target="_blank" datatip="FMEA" flow="left">Add Diamantion</a>';

    return [$data->sr_no,$data->process_no,$data->parameter,$data->app_rev_no,formatDate($data->app_rev_date),$btn];
}

/*Control Method Data */
function getControlMethodData($data){ 
    $deleteParam = $data->id.",'Control Method'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editSamplingPlan', 'title' : 'Update SamplingPlan', 'fnsave' : 'save'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton);  

    return [$action,$data->sr_no,$data->control_method,$data->cm_alias];
}

function getCPData($data){
    $fmeaNo = '<a href="'.base_url($data->controller."/cpDiamentionList/".$data->id).'" target="_blank">'.$data->trans_number.'</a>';
    $printBtn = '<a class="btn btn-primary" href="'.base_url('controlPlan/cp_pdf/'.$data->id).'" target="_blank" datatip="Control Plan Print" flow="left"><i class="fas fa-print" ></i></a>';
    $editButton = '<a href="'.base_url($data->controller.'/editCPProcessDiamention/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit Process Dimension" flow="down"><i class="fa fa-edit"></i></a>';
    $deleteParam = $data->id.",'Control Plan'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trashControlPlan(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($printBtn.$editButton.$deleteButton);
    return [$action,$data->sr_no,$fmeaNo,'['.$data->process_no.'] '.$data->parameter,$data->app_rev_no,formatDate($data->app_rev_date),$data->cust_rev_no];
}

/** Diamention Data */
function getCPDiamentionData($data){
    $controlBtn = ""; $activeButton="";
    $controlParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'control_plan', 'title' : 'Add Control Method', 'fnedit':'addControlMethod','fnsave' : 'saveControlMethod','button' : 'close'}";
    $controlBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Add Control Method" flow="down" onclick="edit('.$controlParam.');"><i class=" fas fa-plus" ></i></a>';

    if(empty($data->is_active)){
        $activeParam = "{'id' : ".$data->id.",'trans_main_id':".$data->trans_main_id.",'parameter_type':".$data->parameter_type.",'is_active':'1','msg':'Are you sure you want to set this dimension as main ?'}";
        $activeButton = '<a class="btn btn-facebook btn-edit permission-modify" href="javascript:void(0)" datatip="Set as main" flow="down" onclick="activeDiamention('.$activeParam.');"><i class="fas fa-check-circle" ></i></a>';
    }else{
        $activeParam = "{'id' : ".$data->id.",'trans_main_id':".$data->trans_main_id.",'parameter_type':".$data->parameter_type.",'is_active':'0','msg':'Are you sure you want to remove this dimension from main ?'}";
        $activeButton = '<a class="btn btn-danger btn-edit permission-modify" href="javascript:void(0)" datatip="Remove from main" flow="down" onclick="activeDiamention('.$activeParam.');"><i class="far fa-times-circle" ></i></a>';
    }
    $action = getActionButton($controlBtn.$activeButton);

    $diamention ='';
    if($data->requirement==1){ $diamention = $data->min_req.'/'.$data->max_req ; }
    if($data->requirement==2){ $diamention = $data->min_req.' '.$data->other_req ; }
    if($data->requirement==3){ $diamention = $data->max_req.' '.$data->other_req ; }
    if($data->requirement==4){ $diamention = $data->other_req ; }
    return [$action,$data->sr_no,$data->parameter,$diamention];
}



/* RQC Inward Header */
function getRQCInwardData($data){
    $pending_qty =floatval($data->qty - $data->total_weight);
    $acceptBtn ='' ;$title ='Accept RQC Qty [<small>Pending Qty : '.$pending_qty.'</small>]';
    $param = "{'id' : ".$data->id.",'job_card_id' : ".$data->job_card_id.",'job_approval_id' : ".$data->jobApprovalId.",'product_id' : ".$data->product_id.",'in_process_id' : ".$data->in_process_id.", 'modal_id' : 'modal-md', 'form_id' : 'aceptQty', 'title' : '".$title."', 'fnedit' : 'acceptRqc','fnsave' : 'saveInward'}";

    $acceptBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Accept RQC" flow="down" onclick="acceptRQC('.$param.');"><i class=" fas fa-check-circle" ></i></a>';

    $action = getActionButton($acceptBtn);
    return [ $action,$data->sr_no,$data->job_number,$data->full_name,$data->process_name,floatval($data->qty),floatval(abs($data->total_weight)),$pending_qty];

}

/* FIR Header */
function getRQCData($data){
    $completeBtn="";$editBtn="";$movementBtn="";$receiveBtn="";
    
    if(empty($data->status)){
        $editBtn = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="RQC Report" flow="down"><i class=" fas fa-plus-circle "></i></a>';

        $completeBtn = '<a class="btn btn-facebook btn-edit permission-modify" href="javascript:void(0)" datatip="Complete RQC" flow="down" onclick="completeRQC('.$data->id.');"><i class="fas fa-check-circle" ></i></a>';
      
    }else{
        if ($data->ok_qty  > 0 && !empty($data->out_process_id)) :
            $pQty = $data->lot_qty - $data->movement_qty;
            $moveParam = "{'id' : " . $data->job_approval_id . ",'ref_id': " . $data->id . ",'p_qty': " .((!empty($pQty) && $pQty > 0 )?$pQty:0). ", 'modal_id' : 'modal-xl', 'form_id' : 'movement', 'title' : 'Move To Next Process','button':'close','fnsave' : 'saveProcessMovement', 'fnedit' : 'processMovement','btnSave':'other'}";
            $movementBtn = '<a class="btn btn-warning btn-edit" datatip="Move to Next Process" flow="down" onclick="processMovement(' . $moveParam . ');"><i class="fa fa-step-forward"></i></a>';
            /* Material Receive from store */
            $receiveParam = "{'job_approval_id' : " . $data->job_approval_id . ",'job_card_id':" . $data->job_card_id . ",'modal_id' : 'modal-xl', 'form_id' : 'receiveStoredMaterial', 'title' : 'Material Receive From Store','fnsave' : 'saveReceiveStoredMaterial', 'fnedit' : 'receiveStoredMaterial'}";
            $receiveBtn = '<a href="javascript:void(0)" class="btn btn-success" datatip="Material Receive From Store" flow="up" onclick="receiveStoredMaterial(' . $receiveParam . ');"> <i class="fa fa-reply" aria-hidden="true"></i> </a>';
        endif;
        if ($data->out_process_id == 0 && $data->ok_qty > 0) :
            $storeLocationParam = "{'id' : " . $data->job_card_id . ",'transid' : " . $data->job_approval_id . ",'ref_batch':" . $data->id . ",'remark':'RQC', 'modal_id' : 'modal-lg', 'form_id' : 'storeLocation', 'title' : 'Store Location','button' : 'close'}";
    
            $movementBtn= '<a class="btn btn-warning btn-edit" href="javascript:void(0)" datatip="Store Location" flow="down" onclick="storeLocation(' . $storeLocationParam . ');"><i class="fas fa-paper-plane"></i></a>';

        endif;
    }
    $pdfButton = '<a href="'.base_url('rqc/inInspection_pdf/'.$data->id).'" type="button" class="btn btn-dark " datatip="Incoming Inspection Report Pdf" flow="down" target="_blank"><i class="fas fa-print"></i></a>';

    $action = getActionButton($pdfButton.$editBtn.$completeBtn.$movementBtn.$receiveBtn);
    return [$action,$data->sr_no,formatDate($data->trans_date),$data->job_number,$data->full_name,floatval($data->lot_qty)];

}

/* Material Grade Table Data */
function getMaterialDataForMqs($data){

    $insParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'mqsParameter', 'title' : 'MQS Parameter', 'fnedit' : 'addMQSParameter'}";
    $insParamButton = '<a class="btn btn-info" href="javascript:void(0)" datatip="MQS Parameter" flow="down" onclick="edit('.$insParam.');"><i class="fa fa-file" ></i></a>';

	$action = getActionButton($insParamButton);
    return [$action,$data->sr_no,$data->material_grade,$data->standard,$data->group_name,$data->color_code];
}

function getMaterialQcData($data){
    $reportBtn = '<a href="'.base_url($data->controller.'/addReport/'.$data->product_id).'"  class="btn btn-success btn-edit " datatip="Add Report" flow="down"><i class="fa fa-plus"></i></a>';
	$action = getActionButton($reportBtn);
    return [$action,$data->sr_no,$data->full_name,$data->rm_code,floatval($data->total_ok_qty),$data->pdf_link];
}

/* Gauge Data */
function getQcGaugeData($data){
    $deleteParam = $data->id.",'Gauge'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="right"><i class="ti-trash"></i></a>';

    $inward=''; $reject=''; $editButton = '';
    if(empty($data->status)){
        $inwardParam = "{'id' : ".$data->id.", 'status' : '1', 'modal_id' : 'modal-lg', 'form_id' : 'inwardGauge', 'title' : 'Inward Gauge', 'fnedit':'inwardGauge', 'fnsave' : 'save'}";
        $inward = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Inward Gauge" flow="right" onclick="inwardGauge('.$inwardParam.');"><i class="fas fa-reply" ></i></a>';
    }elseif($data->status == 1){
        $reject = '<a href="javascript:void(0)" class="btn btn-dark rejectGauge permission-modify" data-id="'.$data->id.'" data-gauge_code="'.$data->item_code.'" datatip="Reject" flow="down"><i class="ti-close" ></i></a>';
    
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editGauge', 'title' : 'Update Gauge', 'fnsave' : 'save'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="right" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    }
    $calPrintBtn = '<a class="btn btn-warning btn-edit permission-read" href="'.base_url('qcInstrument/printCalHistoryCardData/'.$data->id).'" target="_blank" datatip="Calibration History Card" flow="down"><i class="fas fa-print" ></i></a>';

    $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkGaugeChallan" value="'.$data->id.'"><label for="ref_id_'.$data->sr_no.'"></label>';
    
    $deleteButton='';
    $action = getActionButton($calPrintBtn.$reject.$inward.$editButton.$deleteButton);    
    
    $lcd = (!empty($data->last_cal_date)) ? date('d-m-Y',strtotime($data->last_cal_date)) : '';
    $ncd = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-1 days")) : '';
    $pdate = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-".($data->cal_reminder+1)." days")) : '';
    if(!empty($ncd) AND (strtotime($ncd) <= strtotime(date('d-m-Y')))){$ncd = '<strong class="text-danger">'.$ncd.'</strong>';}
	if(!empty($pdate) AND (strtotime($pdate) <= strtotime(date('d-m-Y')))){$pdate = '<strong style="color:#ffbc34;">'.$pdate.'</strong>';}
	
    $itemCode = '<a href="'.base_url("qcInstrument/calibrationData/".$data->id).'" datatip="View Details">'.$data->item_code.'</a>';


	if(in_array($data->status,[1,5]))
	{
        return [$action,$data->sr_no,$selectBox,$itemCode,$data->item_name,$data->make_brand,$data->cal_required,$data->cal_freq,$data->location_name,$lcd,$ncd,$pdate];
	}
	else
	{
        return [$action,$data->sr_no,$itemCode,$data->item_name,$data->make_brand,$data->cal_required,$data->cal_freq,$data->location_name,$lcd,$ncd,$pdate];
	}
}

/* Instrument Data */
function getQcInstrumentData($data){
    $deleteParam = $data->id.",'Instrument'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="right"><i class="ti-trash"></i></a>';

    $inward=''; $reject=''; $editButton = '';
    if(empty($data->status)){
        $inwardParam = "{'id' : ".$data->id.", 'status' : '1', 'modal_id' : 'modal-lg', 'form_id' : 'inwardGauge', 'title' : 'Inward Instrument', 'fnedit':'inwardGauge', 'fnsave' : 'save'}";
        $inward = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Inward Instrument" flow="right" onclick="inwardGauge('.$inwardParam.');"><i class="fas fa-reply" ></i></a>';
    }elseif($data->status == 1){
        $reject = '<a href="javascript:void(0)" class="btn btn-dark rejectGauge permission-modify" data-id="'.$data->id.'" data-gauge_code="'.$data->item_code.'" datatip="Reject" flow="down"><i class="ti-close" ></i></a>';
    
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editGauge', 'title' : 'Update Instrument', 'fnsave' : 'save'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="right" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    }
    
    $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkInstChallan" value="'.$data->id.'"><label for="ref_id_'.$data->sr_no.'"></label>';
    $calPrintBtn = '<a class="btn btn-warning btn-edit permission-read" href="'.base_url('qcInstrument/printCalHistoryCardData/'.$data->id).'" target="_blank" datatip="Calibration History Card" flow="down"><i class="fas fa-print" ></i></a>';

    $deleteButton='';
    $action = getActionButton($calPrintBtn.$reject.$inward.$editButton.$deleteButton);    
    
    $lcd = (!empty($data->last_cal_date)) ? date('d-m-Y',strtotime($data->last_cal_date)) : '';
    $ncd = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-1 days")) : '';
    $pdate = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-".($data->cal_reminder+1)." days")) : '';
    if(!empty($ncd) AND (strtotime($ncd) <= strtotime(date('d-m-Y')))){$ncd = '<strong class="text-danger">'.$ncd.'</strong>';}
	if(!empty($pdate) AND (strtotime($pdate) <= strtotime(date('d-m-Y')))){$pdate = '<strong style="color:#ffbc34;">'.$pdate.'</strong>';}
	 
    $itemCode = '<a href="'.base_url("qcInstrument/calibrationData/".$data->id).'" datatip="View Details">'.$data->item_code.'</a>';
   
	if(in_array($data->status,[1,5]))
	{
        return [$action,$data->sr_no,$selectBox,$itemCode,$data->item_name,$data->make_brand,$data->cal_required,$data->cal_freq,$data->location_name,$lcd,$ncd,$pdate];
	}
	else
	{
        return [$action,$data->sr_no,$itemCode,$data->item_name,$data->make_brand,$data->cal_required,$data->cal_freq,$data->location_name,$lcd,$ncd,$pdate];
	}
}

/* QcChallan Data */
function getQcChallanData($data){
    $returnBtn=''; $caliBtn=''; $caliValBtn=''; $edit=''; $delete='';
    
    if(empty($data->receive_by)){
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->challan_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        $deleteParam = $data->challan_id.",'Challan'";
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trashQcChallan('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

        if($data->challan_type != 3){
            $rtnParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'button':'close', 'form_id' : 'returnChallan', 'title' : 'Return Challan', 'fnedit' : 'returnChallan'}";
            $returnBtn = '<a href="javascript:void(0)" class="btn btn-info permission-modify" onclick="returnQcChallan('.$rtnParam.');" datatip="Return" flow="down"><i class="fas fa-reply"></i></a>';
        }else{
            if($data->party_id == 0){
                $calValParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'calibrationValue', 'title' : 'Calibration Value ', 'fnedit' : 'getCalibrationValue', 'fnsave' : 'saveCalibrationValue'}";
                $caliValBtn = '<a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Calibration Value" flow="down" onclick="edit('.$calValParam.');"><i class="fas fa-tachometer-alt"></i></a>';
            }else{
                $calParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'close', 'form_id' : 'calibration', 'title' : 'Calibration ', 'fnedit' : 'getCalibration', 'fnsave' : 'saveCalibration'}";
                $caliBtn = '<a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Calibration" flow="down" onclick="edit('.$calParam.');"><i class="fas fa-tachometer-alt"></i></a>';
            }
        }
    }else{
        $caliBtn = '<a class="btn btn-info confirmChallan permission-modify" data-id="'.$data->id.'" data-challan_id="'.$data->challan_id.'" data-item_id="'.$data->item_id.'" href="javascript:void(0)" datatip="Confirm" flow="down"><i class="ti-check"></i></a>';
    }

    $data->party_name = (!empty($data->party_name))?$data->party_name:'IN-HOUSE';
    $data->challan_type = (($data->challan_type==1)? 'IN-House Issue': (($data->challan_type==2) ? 'Vendor Issue':'Calibration'));
    
    $action = getActionButton($caliValBtn.$caliBtn.$returnBtn.$edit.$delete);
    return [$action,$data->sr_no,$data->trans_prefix.$data->trans_no,formatDate($data->trans_date),$data->challan_type,$data->party_name,$data->item_name,$data->item_remark];
}

/* QC Purchase Table Data */
function getQCPRData($data){
    $deleteParam = $data->id.",'QC Purchase Request'";
    
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editQCPR', 'title' : 'Update QC PR'}";
    $edit = "";$delete = "";
    
    if($data->status == 0):       
        //$edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        $edit = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt"></i></a>';
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
	
	$action = getActionButton($edit.$delete);
    
	$desciption = '['.$data->req_itm_code.'] '.$data->category_name.' '.$data->size;
	if($data->item_type == 2 AND !empty($data->least_count)){$desciption .= ' ('.$data->least_count.')';}
		
    return [$action,$data->sr_no,$data->req_date,$data->req_number,$desciption,$data->make,$data->qty,formatDate($data->delivery_date),$data->reject_reason];
}

/* Qc Indent Data  */
function getQCIndentData($data){
    $rejParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editQCPR', 'fnsave' : 'rejectQCPR', 'title' : 'Reject QC PR'}";
    $rejectBtn="";
    if($data->status == 0):       
        //$edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        //$rejectBtn = '<a class="btn btn-dark btn-edit permission-modify" href="javascript:void(0)" datatip="Reject QC PR" flow="down" onclick="edit('.$rejParam.');"><i class="ti-na"></i></a>';
    endif;
    $action = getActionButton($rejectBtn);
    $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkQcRequest" value="'.$data->id.'"><label for="ref_id_'.$data->sr_no.'"></label>';
    
	$desciption = '['.$data->req_itm_code.'] '.$data->category_name.' '.$data->size;
	if($data->item_type == 2 AND !empty($data->least_count)){$desciption .= ' '.$data->least_count;}
	
    return [$action,$data->sr_no,$selectBox,$data->req_date,$data->req_number,$desciption,$data->make,$data->qty,formatDate($data->delivery_date)];
}

/* QC Purchase Table Data */
function getQCPurchaseData($data){
    $deleteParam = $data->order_id.",'QC Purchase'";
    $grn = "";$edit = "";$delete = ""; $receive = "";
    /** Updated By Karmi */
    if($data->order_status == 0 && $data->rec_qty < $data->qty):       
        //$grn = '<a href="javascript:void(0)" class="btn btn-info btn-inv createGrn permission-write" datatip="Create GIR" flow="down" data-party_id="'.$data->party_id.'" data-party_name="'.$data->party_name.'"><i class="ti-file"></i></a>';
        
        $receive = '<a href="javascript:void(0)" class="btn btn-primary purchaseReceive permission-modify" data-po_id="'.$data->order_id.'" datatip="Receive" flow="down"><i class="fas fa-reply" ></i></a>';

        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->order_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
	//$edit = '<a href="'.base_url($data->controller.'/edit/'.$data->order_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

	$printBtn = '<a class="btn btn-info btn-edit permission-approve" href="'.base_url($data->controller.'/printQP/'.$data->order_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
	
	$action = getActionButton($printBtn.$receive.$grn.$edit.$delete);
		
    return [$action,$data->sr_no,$data->po_prefix.$data->po_no,formatDate($data->po_date),$data->party_name,'['.$data->category_code.'] '.$data->category_name,$data->price,$data->qty,$data->rec_qty,$data->pending_qty,formatDate($data->delivery_date)];
}

/* PDI Table Data */
function getPDIData($data){    
    $deleteParam = $data->id;
    $deleteBtn = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $editBtn = '<a href="'.base_url('pdi/edit/'.$data->id).'" class="btn btn-success btn-edit " datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $printBtn = '<a class="btn btn-warning btn-edit permission-modify" href="'.base_url('pdi/printPDI/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print"></i></a>';
   
    $action = getActionButton($printBtn.$editBtn.$deleteBtn);
    return [ $action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->item_name,floatval($data->lot_qty)];
}

function getCalibration($data){ 
    $caliBtn = '';
    if(empty($data->cal_agency)):
        $caliParam = "{'id' : ".$data->id.",'modal_id' : 'modal-lg', 'form_id' : 'editCalibrationData', 'title' : 'Calibration', 'button' : 'both','fnedit' : 'editCalibrationData', 'fnsave' : 'saveCalibrationData'}";
        $caliBtn = '<a class="btn btn-success btn-contact permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$caliParam.');"><i class="ti-pencil-alt"></i></a>';
    else:
        $calParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'close', 'form_id' : 'calibration', 'title' : 'Calibration ', 'fnedit' : 'getCalibration', 'fnsave' : 'saveCalibration'}";
        $caliBtn = '<a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$calParam.');"><i class="ti-pencil-alt"></i></a>';
    endif;
    if(empty($data->cal_agency)):
        $download = ((!empty($data->certificate_file))?'<a href="'.base_url('assets/uploads/gauges/'.$data->certificate_file).'" target="_blank"><i class="fa fa-download"></i></a>':'');                                
    else:
        $download = ((!empty($data->certificate_file))?'<a href="'.base_url('assets/uploads/instrument/'.$data->certificate_file).'" target="_blank"><i class="fa fa-download"></i></a>':'');                                

    endif;
    $action = getActionButton($caliBtn);  
    return[$action,$data->sr_no,$data->cal_agency_name,$data->cal_certi_no,$download,$data->remark];
}

/* SAR Table Data */
function getSarData($data){    
    $editBtn = $deleteBtn = $approveBtn = "";
    if(empty($data->status)):
        $deleteParam = $data->id;
        $deleteBtn = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

        $editBtn = '<a href="'.base_url('sar/edit/'.$data->id).'" class="btn btn-success btn-edit" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $approveBtn = '<a href="'.base_url('sar/approveSar/'.$data->id).'" class="btn btn-primary btn-edit" datatip="Approve" flow="down"><i class="fa fa-check"></i></a>';
    endif;

    $printBtn = '<a class="btn btn-dribbble btn-edit permission-modify" href="'.base_url('sar/printSar/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print"></i></a>';
   
    $action = getActionButton($printBtn.$approveBtn.$editBtn.$deleteBtn);
    return [ $action,$data->sr_no,formatDate($data->trans_date),$data->job_number,$data->process_name,'['.$data->item_code.'] '.$data->item_name,$data->emp_name,$data->setting_time,$data->remark];
}
?>