<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* Common Table Header */
function tableHeader($data){
	$c=0;$colsAlignment=array();$srno_position=1;
    $html = '<thead class="thead-info"><tr>';
    foreach($data as $row):
        $name = $row['name'];
        $style = (isset($row['style']))?$row['style']:"";
		//$html .= '<th style="'.$style.'">'.$name.'</th>';
        $orderable = (isset($row['orderable']))?$row['orderable']:"true";
		$html .= '<th style="'.$style.'" data-orderable="'.$orderable.'">'.$name.'</th>';
		
        if(isset($row['srnoPosition'])):
			$srno_position = $row['srnoPosition'];
        endif;
		
		if(isset($row['textAlign']) and $row['textAlign']=="left"):
			$colsAlignment['left'][]= $c;
		elseif(isset($row['textAlign']) and $row['textAlign']=="right"):
			$colsAlignment['right'][]= $c;
		elseif(isset($row['textAlign']) and $row['textAlign']=="center"):
			$colsAlignment['center'][]= $c;
		endif;
        $c++;
    endforeach;
    $html .= '</tr></thead>';
    return [$html,json_encode($colsAlignment),$srno_position];
}

/* get Pagewise Table Header */
function getDtHeader($page){
	
	/* Party Header */
    $data['parties'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['parties'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
	$data['parties'][] = ["name"=>"Company Name"];
	$data['parties'][] = ["name"=>"Contact Person"];
    $data['parties'][] = ["name"=>"Contact No."];
    $data['parties'][] = ["name"=>"Business Budget"];
	
	/* Item Header */
    $data['items'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['items'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['items'][] = ["name"=>"Item Code"];
    $data['items'][] = ["name"=>"Item Name"];
    $data['items'][] = ["name"=>"HSN Code"];
    $data['items'][] = ["name"=>"Category"];
    $data['items'][] = ["name"=>"Unit"];
    //$data['items'][] = ["name"=>"Opening Qty"];
    //$data['items'][] = ["name"=>"Stock Qty"];
    //$data['items'][] = ["name"=>"Manage Stock"];

	/* Product Header */
    $data['products'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['products'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['products'][] = ["name"=>"Part Code"];
    $data['products'][] = ["name"=>"Part Name"];
    $data['products'][] = ["name"=>"HSN Code"];
    //$data['products'][] = ["name"=>"Price"];
    $data['products'][] = ["name"=>"Category Name"];
    $data['products'][] = ["name"=>"Drawing No"];
    //$data['products'][] = ["name"=>"Opening Qty"];
    //$data['products'][] = ["name"=>"Stock Qty"];
    //$data['products'][] = ["name"=>"Manage Stock"];
	
	
    /* Product Inspection Header */
    $data['productInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['productInspection'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['productInspection'][] = ["name" => "Inspection Type"];
    $data['productInspection'][] = ["name" => "Inspection Date"];
    $data['productInspection'][] = ["name" => "Product Name"];
    $data['productInspection'][] = ["name" => "Qty."];

	/* ISO Reports Header */
    $data['iso'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['iso'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['iso'][] = ["name"=>"Documents"];
    $data['iso'][] = ["name"=>"Document No."];
    $data['iso'][] = ["name"=>"Rev. No. & Date"];
    $data['iso'][] = ["name"=>"Category"];

    /* Purchase Invoice Header  created by meghavi 26-11-21 6:15 */

    $data['purchaseInvoice'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['purchaseInvoice'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['purchaseInvoice'][] = ["name"=>"Vou No."];
	$data['purchaseInvoice'][] = ["name"=>"Inv No."];
    $data['purchaseInvoice'][] = ["name"=>"Inv Date"];
    $data['purchaseInvoice'][] = ["name"=>"Supplier Name"];
    $data['purchaseInvoice'][] = ["name"=>"Amount"];

	return tableHeader($data[$page]);
}

/* Create Action Button */
function getActionButton($buttons){
	$action = '<div class="actionWrapper" style="position:relative;">
					<div class="actionButtons actionButtonsRight">
						<a class="mainButton btn-instagram " href="javascript:void(0)"><i class="fa fa-cog"></i></a>
						<div class="btnDiv">'.$buttons.'</div>
					</div>
				</div>';
	return $action;
}

/* Party Table Data */
function getPartyData0($data){

    $title = 'Party';//($data->party_category == 1 ? "Customer": ($data->party_category == 2 ? "Vendor":"Supplier"));
    $deleteParam = $data->id.",'".$title."'";$pcat =  "'".$data->party_category."'";
    $editParam = "{'id' : ".$data->id.", 'party_category': ".$pcat.", 'modal_id' : 'modal-xl', 'form_id' : 'editParty', 'title' : 'Update ".$title."'}";
    $approvalParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'partyApproval', 'title' : 'Party Approval : ".$data->party_name."', 'fnedit' : 'partyApproval', 'fnsave' : 'savePartyApproval'}";

    $otherParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md','button' : 'close', 'form_id' : 'otherDetails', 'title' : 'Party Details', 'fnedit' : 'getOtherDetail'}";
    $otherButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Other Details" flow="down" onclick="edit('.$otherParam.');"><i class="fa fa-info"></i></a>';
    
    $docsParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'both', 'form_id' : 'docsDetail', 'title' : 'Document Detail : ".$data->party_name."', 'fnedit' : 'getDocsDetail', 'fnsave' : 'saveDocs'}";
    $docsButton = '<a class="btn btn-info btn-bank permission-modify" href="javascript:void(0)" datatip="Document Detail" flow="down" onclick="edit('.$docsParam.');"><i class="fas fa-file"></i></a>';
    
    $contactParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl','button' : 'close', 'form_id' : 'contactDetail', 'title' : 'Contact Detail : ".$data->party_name."', 'fnedit' : 'getContactDetail', 'fnsave' : 'saveContact'}";
    $contactButton = '<a class="btn btn-warning btn-contact permission-modify" href="javascript:void(0)" datatip="Contact Detail" flow="down" onclick="edit('.$contactParam.');"><i class="ti-id-badge"></i></a>';

    $bankParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'bankDetail', 'title' : 'bank Detail : ".$data->party_name."', 'fnedit' : 'getBankDetail', 'fnsave' : 'saveBank'}";
    $bankButton = '<a class="btn btn-info btn-bank permission-modify" href="javascript:void(0)" datatip="Bank Detail" flow="down" onclick="edit('.$bankParam.');"><i class="icon-Bank"></i></a>';

    $addDetailParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'addressDetail', 'title' : 'Address Detail : ".$data->party_name."', 'fnedit' : 'getAddressDetail', 'fnsave' : 'saveAddressDetail'}";
    $addDetailButton = '<a class="btn btn-danger btn-contact permission-modify" href="javascript:void(0)" datatip="Address Detail" flow="down" onclick="edit('.$addDetailParam.');"><i class="fa fa-address-book"></i></a>';

    $approvalButton = '';
    if(empty($data->approved_by)){
        $approvalButton = '<a class="btn btn-info btn-approval permission-modify" href="javascript:void(0)" datatip="Party Approval" flow="down" onclick="edit('.$approvalParam.');"><i class="fa fa-check" ></i></a>';
    }
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
    //if($data->party_category == 1){ $action = getActionButton($approvalButton.$addDetailButton.$contactButton.$bankButton.$editButton.$deleteButton);}
    //else { $action = getActionButton($approvalButton.$editButton.$deleteButton); }
    $action = getActionButton($otherButton.$approvalButton.$docsButton.$addDetailButton.$contactButton.$bankButton.$editButton.$deleteButton);
    //$category = ($data->party_category == 1?"Customer":($data->party_category == 3?"Supplier":"Vendor"));
    return [$action,$data->sr_no,$data->party_name,$data->contact_person,$data->party_mobile,$data->business_budget];
}



/* Item Table Data */
function getItemData($data){
    $deleteParam = $data->id.",'Item'";
    $editParam = "{'id' : ".$data->id.",'item_type' :".$data->item_type.", 'modal_id' : 'modal-xl', 'form_id' : 'editItem', 'title' : 'Update Item', 'fnsave' : 'save'}";
    
    $approvalParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'itemApproval', 'title' : 'Item Approval : <br><small>".$data->item_name."</small>', 'fnedit' : 'itemApproval', 'fnsave' : 'saveItemApproval'}";
    
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $approvalButton = '';
    if(empty($data->approved_by)){
        $approvalButton = '<a class="btn btn-info btn-approval permission-modify" href="javascript:void(0)" datatip="Item Approval" flow="down" onclick="edit('.$approvalParam.');"><i class="fa fa-check" ></i></a>';
    }
    
    $hsnDetailBtn="";$specificationDetailBtn="";$storageDetailBtn="";$technicalDetailBtn="";
    
    $hsnParam = "{'id' : ".$data->id.",'item_type' :".$data->item_type.", 'modal_id' : 'modal-md', 'form_id' : 'hsnDetail', 'title' : 'HSN /SAC Detail', 'fnsave' : 'saveItemDetails' ,'fnedit' : 'addHSNDetail'}";
    $hsnDetailBtn = '<a class="btn btn-secondary btn-edit permission-modify text-center" href="javascript:void(0)"  datatip="HSN /SAC Detail" flow="down" onclick="edit('.$hsnParam.');" ><i class="fas fa-hospital-symbol" style="font-size: 19px;"></i></a>';

    $specificationParam = "{'id' : ".$data->id.",'item_type' :".$data->item_type.", 'modal_id' : 'modal-md', 'form_id' : 'specificationDetail', 'title' : 'Item Specification', 'fnsave' : 'saveItemDetails' ,'fnedit' : 'addItemSpecification'}";
    $specificationDetailBtn='<a class="btn btn-facebook btn-edit permission-modify" href="javascript:void(0)" datatip="Specification" flow="down" onclick="edit('.$specificationParam.');"><i class=" fab fa-stripe-s" ></i></a>';

    $storageParam = "{'id' : ".$data->id.",'item_type' :".$data->item_type.", 'modal_id' : 'modal-lg', 'form_id' : 'storageDetail', 'title' : 'Storage Detail', 'fnsave' : 'saveItemDetails' ,'fnedit' : 'addStorageDetail'}";
    $storageDetailBtn='<a class="btn btn-warning btn-edit permission-modify" href="javascript:void(0)" datatip="Storage Detail" flow="down" onclick="edit('.$storageParam.');"><i class=" fas fa-database" ></i></a>';

    $techParam = "{'id' : ".$data->id.",'item_type' :".$data->item_type.", 'modal_id' : 'modal-lg', 'form_id' : 'techDetail', 'title' : 'Technical Detail', 'fnsave' : 'saveItemDetails' ,'fnedit' : 'addTechnicalDetail'}";
    $technicalDetailBtn='<a class="btn btn-primary btn-edit permission-modify" href="javascript:void(0)" datatip="Technical Detail" flow="down" onclick="edit('.$techParam.');"><i class="fab fa-tumblr" ></i></a>';
    //$action = getActionButton($approvalButton.$editButton.$deleteButton);    
    $action = getActionButton($approvalButton.$hsnDetailBtn.$specificationDetailBtn.$storageDetailBtn.$technicalDetailBtn.$editButton.$deleteButton); 
    
    $updateStockBtn = "";
    /* $updateStockBtn = ($data->rm_type == 0)?'<button type="button" class="btn waves-effect waves-light btn-outline-warning itemStockUpdate permission-modify" data-id="'.$data->id.'" data-item_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="addStockTrans" data-form_title="Update Stock">Update Stock</button>':''; */

	$mq = '';
    if($data->stock_qty < $data->min_qty){ $mq = 'text-danger'; }
	$qty = '<a href="'.base_url('reports/productReport/itemWiseStock/'.$data->id).'" class="'.$mq.'">'.$data->stock_qty.' ('.$data->unit_name.')</a>';

    $openingStock = '<button type="button" class="btn waves-effect waves-light btn-outline-primary itemOpeningStock permission-modify" data-id="'.$data->id.'" data-item_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="addOpeningStock" data-form_title="Opening Stock">Opening Stock</button>';
	$hsnDetail = '';
	if(!empty($data->hsnDetail)){$hsnDetail = '<br><small>'.$data->hsnDetail.'</small>';}
    //return [$action,$data->sr_no,$data->item_code,$data->full_name,$data->hsn_code,$data->opening_qty.' ('.$data->unit_name.')',$qty,$openingStock.' '.$updateStockBtn];
    return [$action,$data->sr_no,$data->item_code,$data->full_name,$data->hsn_code,$data->category_name ,$data->unit_name];

}

/* Product Table Data */
function getProductData($data){
    $deleteParam = $data->id.",'Product'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'editProduct', 'title' : 'Update Product'}";

    $setProductProcess = '<a href="javascript:void(0)" class="btn btn-info setProductProcess permission-modify" datatip="Set Product Process" data-id="'.$data->id.'" data-product_name="'.htmlentities($data->item_name).'" data-button="both" data-modal_id="modal-md" data-function="addProductProcess" data-form_title="Set Product Process" flow="down"><i class="fas fa-cogs"></i></a>';

    $viewProductProcess = '<a href="javascript:void(0)" class="btn btn-purple viewItemProcess permission-modify" datatip="View Process" data-id="'.$data->id.'" data-product_name="'.htmlentities($data->item_name).'" data-button="close" data-modal_id="modal-lg" data-function="viewProductProcess" data-form_title="View Product Process" flow="down"><i class="fa fa-list"></i></a>';

    $productKit = '<a href="javascript:void(0)" class="btn btn-warning productKit permission-modify" datatip="Product BOM" data-id="'.$data->id.'" data-product_name="'.htmlentities($data->item_name).'" data-button="both" data-modal_id="modal-lg" data-function="addProductKitItems" data-form_title="Product BOM" flow="down"><i class="fas fa-dolly-flatbed"></i></a>';

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	
	$mq = '';
    if($data->stock_qty < $data->min_qty){ $mq = 'text-danger'; }
	$qty = '<a href="'.base_url('reports/productReport/itemWiseStock/'.$data->id).'" class="'.$mq.'">'.$data->stock_qty.' ('.$data->unit_name.')</a>';
    
    $openingStock = '<button type="button" class="btn waves-effect waves-light btn-outline-primary itemOpeningStock permission-modify" data-id="'.$data->id.'" data-item_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="addOpeningStock" data-form_title="Opening Stock">Opening Stock</button>';
	
	$action = getActionButton($editButton.$deleteButton);
	$hsnDetail = '';
	if(!empty($data->hsnDetail)){$hsnDetail = '<br><small>'.$data->hsnDetail.'</small>';}
    
    $full_name = '<a href="'.base_url('products/getProductProfile/'.$data->id).'"datatip="Reference Data" flow="down">'.$data->full_name.'</a>';
    return [$action,$data->sr_no,$data->item_code,$full_name,$data->hsn_code.$hsnDetail,$data->category_name,$data->drawing_no];
}


/* Product Inspection Table Data */
function getProductInspectionData($data){
    $deleteParam = $data->id.",'Product Inspection'";

    if($data->type == 1):
        $type = "OK";
    elseif($data->type == 2):
        $type = "Reject";
    else:
        $type = "Scrape";
    endif;

    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($delete);

    return [$action,$data->sr_no,$type,date("d-m-Y",strtotime($data->inspection_date)),$data->item_name,$data->qty."(".$data->unit_name.")"];
}

/* Print Decimal Without 0 Precision */
function printDecimal($val){return number_format($val,0,'','');}

/* Ignore Single/Double Quote **/
function trimQuotes($val){return str_replace('"','\"',$val);}

/** Date Format **/
function formatDate($date,$format='d-m-Y'){return (!empty($date)) ? date($format,strtotime($date)) : '';}

/** GET PREFIX ARRAY **/
function getPrefix($prefix,$explodeBy = '/'){return explode($explodeBy,$prefix);}

/** GET NO WITH FORMATED PREFIX **/
function getPrefixNumber($prefix,$no,$explodeBy = '/'){ $prfx = explode($explodeBy,$prefix);return $prfx[0].'/'.$no.'/'.$prfx[1]; }

/* Convert Time to Seconds */
/*function timeToSeconds($time) {
    //list($h, $m, $s) = explode(':', $time);
    //return ($h * 3600) + ($m * 60) + $s;
    list($h, $m) = explode(':', $time);
    return ($h * 3600) + ($m * 60);
}*/

/* Convert Seconds to Time */
/*function secondsToTime($seconds) {
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    return sprintf('%02d:%02d', $h, $m);
}*/

/* Purchase Invoice Data  Created By Meghavi 26-11-2021 6:15 */
function getPurchaseInvoiceData($data){
    $deleteParam = $data->trans_main_id.",'Invoice'";

    $printBtn = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Invoice" flow="down" data-id="'.$data->trans_main_id.'" data-function="purchaseInvoice_pdf"><i class="fa fa-print"></i></a>';

    $editButton = '<a class="btn btn-success btn-edit permission-modify"  datatip="Edit" flow="down" href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($printBtn.$editButton.$deleteButton);    

    return [$action,$data->sr_no,$data->trans_number,$data->doc_no,formatDate($data->trans_date),$data->party_name,$data->net_amount];
   
}

function getVoucherNameLong($entryType){
    switch($entryType){
        case 1:
            return "Sales Enquiry";
        case 2:
            return "Sales Quotation";
        case 3:
            return "Quotation Revision";
        case 4:
            return "Sales Order";
        case 7:
            return "Delivery Challan";
        case 6: //Manufacturing (Domestics)
            return "Sales Invoice";
        case 7: //Jobwork (Domestics)
            return "Sales Invoice";
        case 8: //Manufacturing (Export)
            return "Sales Invoice";
        case 9:
            return "Proforma Invoice";
        case 10: //Commercial Invoice
            return "Sales Invoice";
        case 11: //Custom Invoice
            return "Sales Invoice";
        case 12:
            return "Purchase Invoice";
        case 13:
            return "Credit Note";
        case 14:
            return "Debit Note";
        case 15:
            return "Cash/Bank Received";
        case 16:
            return "Cash/Bank Paid";
        case 17:
            return "Journal Voucher";
        case 18:
            return "GST Expense";
        case 19:
                return "JobWork Invoice";
        case 20:
            return "Scrap Invoice";
        default:
			return "";
    }
}

function getVoucherNameShort($entryType){
    switch($entryType){
        case 1:
            return "SEnq";
        case 2:
            return "SQuo";
        case 3:
            return "QRev";
        case 4:
            return "SOrd";
        case 7:
            return "Chln";
        case 6: //Manufacturing (Domestics)
            return "Sale";
        case 7: //Jobwork (Domestics)
            return "Sale";
        case 8: //Manufacturing (Export)
            return "Sale";
        case 9:
            return "PrIn";
        case 10: //Commercial Invoice
            return "Sale";
        case 11: //Custom Invoice
            return "Sale";
        case 12:
            return "Purc";
        case 13:
            return "C.N.";
        case 14:
            return "D.N.";
        case 15:
            return "BCRct";
        case 16:
            return "BCPmt";
        case 17:
            return "Jrnl";
        case 18:
            return "GExp";
        case 19:
            return "JWInv";
        case 20:
            return "SCRPInv";
        default:
			return "";
    }
}

function getSystemCode($type,$isChild,$gstType=0){
	$retVal = "";
	if($isChild == false){
		switch($type){	
			case 12: // Purchase Invoice
				$retVal = "PURACC";
				break;
			case 6: // Sales Invoice
				$retVal = "SALESACC";
				break;
            case 7: // Sales Invoice
                $retVal = "SALESACC";
                break;
            case 8: // Sales Invoice
                $retVal = "SALESACC";
                break;
            case 10: // Sales Invoice
                $retVal = "SALESACC";
                break;
            case 11: // Sales Invoice
                $retVal = "SALESACC";
                break;
			case 13: // Credit Note
				$retVal = "SALESACC";
				break;	
			case 14: // Debit Note
				$retVal = "PURACC";
				break;
		}
	}else{
		switch($type){	
			case 12: // Purchase Invoice
				$retVal = ($gstType == 3)?"PURTFACC":"PURGSTACC";
				break;
			case 6: // Sales Invoice
				$retVal = ($gstType == 3)?"SALESTFACC":"SALESGSTACC";
				break;
            case 7: // Sales Invoice
                $retVal = ($gstType == 3)?"SALESTFACC":"SALESGSTACC";
                break;
            case 8: // Sales Invoice
                $retVal = ($gstType == 3)?"SALESTFACC":"SALESGSTACC";
                break;
            case 10: // Sales Invoice
                $retVal = ($gstType == 3)?"SALESTFACC":"SALESGSTACC";
                break;
            case 11: // Sales Invoice
                $retVal = ($gstType == 3)?"SALESTFACC":"SALESGSTACC";
                break;
			case 13: // Credit Note
				$retVal = ($gstType == 3)?"SALESTFACC":"SALESGSTACC";
				break;	
			case 14: // Debit Note
				$retVal = ($gstType == 3)?"PURTFACC":"PURGSTACC";
				break;
		}
	}
	return $retVal;
}

function getSPAccCode($entryType,$gstType,$spType){
    switch($entryType){
        /* Purchase Invoice Case Start */
        case $entryType == 12 && $gstType == 1 && $spType == 1:
            $retVal = "PURGSTACC";    
            break;
        case $entryType == 12 && $gstType == 2 && $spType == 1:
            $retVal = "PURIGSTACC";    
            break;
        case $entryType == 12 && $gstType == 2 && $spType == 2:
            $retVal = "IMPORTGSTACC";    
            break;
        case $entryType == 12 && $gstType == 1 && $spType == 3:
            $retVal = "PURJOBGSTACC";    
            break;
        case $entryType == 12 && $gstType == 2 && $spType == 3:
            $retVal = "PURJOBIGSTACC";    
            break;
        case $entryType == 12 && $gstType == 3:
            $retVal = "PURTFACC";    
            break;
        /* Purchase Invoice Case End */


        /* Debit Note Case Start */
        case $entryType == 14 && $gstType == 1 && $spType == 1:
            $retVal = "PURGSTACC";    
            break;
        case $entryType == 14 && $gstType == 2 && $spType == 1:
            $retVal = "PURIGSTACC";    
            break;
        case $entryType == 14 && $gstType == 2 && $spType == 2:
            $retVal = "IMPORTGSTACC";    
            break;
        case $entryType == 14 && $gstType == 1 && $spType == 3:
            $retVal = "PURJOBGSTACC";    
            break;
        case $entryType == 14 && $gstType == 2 && $spType == 3:
            $retVal = "PURJOBIGSTACC";    
            break;
        case $entryType == 14 && $gstType == 3:
            $retVal = "PURTFACC";    
            break;
        /* Debit Note Case End */


        /* Sales Invoice Case Start */
        case $entryType == 6 && $gstType == 1 && $spType == 1:
            $retVal = "SALESGSTACC";    
            break;
        case $entryType == 6 && $gstType == 2 && $spType == 1:
            $retVal = "SALESIGSTACC";    
            break;
        case $entryType == 6 && $gstType == 2 && $spType == 2:
            $retVal = "EXPORTGSTACC";    
            break;
        case $entryType == 6 && $gstType == 1 && $spType == 3:
            $retVal = "SALESJOBGSTACC";    
            break;
        case $entryType == 6 && $gstType == 2 && $spType == 3:
            $retVal = "SALESJOBIGSTACC";    
            break;
        case $entryType == 6 && $gstType == 3:
            $retVal = "SALESTFACC";    
            break;

        case $entryType == 7 && $gstType == 1 && $spType == 1:
            $retVal = "SALESGSTACC";    
            break;
        case $entryType == 7 && $gstType == 2 && $spType == 1:
            $retVal = "SALESIGSTACC";    
            break;
        case $entryType == 7 && $gstType == 2 && $spType == 2:
            $retVal = "EXPORTGSTACC";    
            break;
        case $entryType == 7 && $gstType == 1 && $spType == 3:
            $retVal = "SALESJOBGSTACC";    
            break;
        case $entryType == 7 && $gstType == 2 && $spType == 3:
            $retVal = "SALESJOBIGSTACC";    
            break;
        case $entryType == 7 && $gstType == 3:
            $retVal = "SALESTFACC";    
            break;

        case $entryType == 8 && $gstType == 1 && $spType == 1:
            $retVal = "SALESGSTACC";    
            break;
        case $entryType == 8 && $gstType == 2 && $spType == 1:
            $retVal = "SALESIGSTACC";    
            break;
        case $entryType == 8 && $gstType == 2 && $spType == 2:
            $retVal = "EXPORTGSTACC";    
            break;
        case $entryType == 8 && $gstType == 1 && $spType == 3:
            $retVal = "SALESJOBGSTACC";    
            break;
        case $entryType == 8 && $gstType == 2 && $spType == 3:
            $retVal = "SALESJOBIGSTACC";    
            break;
        case $entryType == 8 && $gstType == 3:
            $retVal = "SALESTFACC";    
            break;

        case $entryType == 10 && $gstType == 1 && $spType == 1:
            $retVal = "SALESGSTACC";    
            break;
        case $entryType == 10 && $gstType == 2 && $spType == 1:
            $retVal = "SALESIGSTACC";    
            break;
        case $entryType == 10 && $gstType == 2 && $spType == 2:
            $retVal = "EXPORTGSTACC";    
            break;
        case $entryType == 10 && $gstType == 1 && $spType == 3:
            $retVal = "SALESJOBGSTACC";    
            break;
        case $entryType == 10 && $gstType == 2 && $spType == 3:
            $retVal = "SALESJOBIGSTACC";    
            break;
        case $entryType == 10 && $gstType == 3:
            $retVal = "SALESTFACC";    
            break;

        case $entryType == 11 && $gstType == 1 && $spType == 1:
            $retVal = "SALESGSTACC";    
            break;
        case $entryType == 11 && $gstType == 2 && $spType == 1:
            $retVal = "SALESIGSTACC";    
            break;
        case $entryType == 11 && $gstType == 2 && $spType == 2:
            $retVal = "EXPORTGSTACC";    
            break;
        case $entryType == 11 && $gstType == 1 && $spType == 3:
            $retVal = "SALESJOBGSTACC";    
            break;
        case $entryType == 11 && $gstType == 2 && $spType == 3:
            $retVal = "SALESJOBIGSTACC";    
            break;
        case $entryType == 11 && $gstType == 3:
            $retVal = "SALESTFACC";    
            break;
        /* Sales Invoice Case End */

        /* Credit Note Case Start */
        case $entryType == 13 && $gstType == 1 && $spType == 1:
            $retVal = "SALESGSTACC";    
            break;
        case $entryType == 13 && $gstType == 2 && $spType == 1:
            $retVal = "SALESIGSTACC";    
            break;
        case $entryType == 13 && $gstType == 2 && $spType == 2:
            $retVal = "EXPORTGSTACC";    
            break;
        case $entryType == 13 && $gstType == 1 && $spType == 3:
            $retVal = "SALESJOBGSTACC";    
            break;
        case $entryType == 13 && $gstType == 2 && $spType == 3:
            $retVal = "SALESJOBIGSTACC";    
            break;
        case $entryType == 13 && $gstType == 3:
            $retVal = "SALESTFACC";    
            break;
        /* Credit Note Case End */

        default: 
            $retVal = "";
            break;
    }
    return $retVal;
}

function getCrDrEff($type){
	$result = array();
	switch($type){
		case 12: //Purchase Invoice
			$result['vou_type'] = "CR";
			$result['opp_type'] = "DR";		
			break;	

		case 6: //Sales Invoice
			$result['vou_type'] = "DR";
			$result['opp_type'] = "CR";	
			break;  
        case 7: //Sales Invoice
            $result['vou_type'] = "DR";
            $result['opp_type'] = "CR";	
            break;
        case 8: //Sales Invoice
            $result['vou_type'] = "DR";
            $result['opp_type'] = "CR";	
            break;
        case 10: //Sales Invoice
            $result['vou_type'] = "DR";
            $result['opp_type'] = "CR";	
            break;
        case 11: //Sales Invoice
            $result['vou_type'] = "DR";
            $result['opp_type'] = "CR";	
            break;
		case 13: //Credit Note
			$result['vou_type'] = "CR";
			$result['opp_type'] = "DR";		
			break;	
		case 14: //Debit Note
			$result['vou_type'] = "DR";
			$result['opp_type'] = "CR";	
			break;
		case 15: //Bank/Cash Receipt
			$result['vou_type'] = "DR";
			$result['opp_type'] = "CR";	
			break;
		case 16: //Bank/Cash Payment
			$result['vou_type'] = "CR";
			$result['opp_type'] = "DR";	
			break;
		case 18: //GST Expense
			$result['vou_type'] = "CR";
			$result['opp_type'] = "DR";	
			break;
	}
	return $result;
}

function getExpArrayMap($input){
	$expAmount=0;
	for($i=1; $i<=25 ; $i++):
		$result['exp'.$i.'_acc_id'] = (isset($input['exp'.$i.'_acc_id']))?$input['exp'.$i.'_acc_id']:0;
		$result['exp'.$i.'_per'] = (isset($input['exp'.$i.'_per']))?$input['exp'.$i.'_per']:0;
		$result['exp'.$i.'_amount'] = (isset($input['exp'.$i.'_amount']))?$input['exp'.$i.'_amount']:0;
		$expAmount += $result['exp'.$i.'_amount'];
	endfor;
	$result['exp_amount'] = $expAmount;
	return $result;
}

?>