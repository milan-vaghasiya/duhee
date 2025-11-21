<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class SalesInvoice extends MY_Controller{	
	private $indexPage = "sales_invoice/index";
	private $itemwiseInvoice = "sales_invoice/itemwise_invoice";
    private $invoiceForm = "sales_invoice/form";
    private $blForm = "sales_invoice/bl_form";
    private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.01,"val"=>'0.01%'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Sales Invoice";
		$this->data['headData']->controller = "salesInvoice";
		$this->data['headData']->pageUrl = "salesInvoice";
	}
	
	public function index(){
		$this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);    
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($sales_type=""){
		$data = $this->input->post(); $data['sales_type'] = $sales_type;$data['list_type'] = 'LISTING';
        $result = $this->salesInvoice->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++; $row->po_no = ''; $row->ref_no = '';
            /*if(!empty($row->from_entry_type)):
               $refData = $this->salesInvoice->getInvoice($row->ref_id);
               $row->po_no = $refData->doc_no;
            endif;*/
            $row->controller = $this->data['headData']->controller;
			$row->tp = 'BILLWISE';
			$row->listType = 'LISTING';
            $sendData[] = getSalesInvoiceData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	public function itemwiseInvoice(){
		//$this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);    
        $this->load->view($this->itemwiseInvoice,$this->data);
    }
	public function getItemWiseDTRows($sales_type=""){
		$data = $this->input->post(); $data['sales_type'] = $sales_type;$data['list_type'] = 'LISTING';
        $result = $this->salesInvoice->getItemWiseDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++; $row->po_no = ''; $row->ref_no = '';
            /*if(!empty($row->from_entry_type)):
               $refData = $this->salesInvoice->getInvoice($row->ref_id);
               $row->po_no = $refData->doc_no;
            endif;*/
            $row->controller = $this->data['headData']->controller;
			$row->tp = 'ITEMWISE';
			$row->listType = 'LISTING';
            $sendData[] = getSalesInvoiceData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	//  Create By : JP @24-05-2022 11:25 AM
	public function getInvoiceSummary($jsonData=''){
		if(!empty($jsonData)){$postData = (Array) json_decode(urldecode(base64_decode($jsonData)));}
        else{$postData = $this->input->post();}
        //print_r($postData);exit;
        $result = $this->salesInvoice->getInvoiceSummary($postData);
		$reportTitle = 'BILL WISE SALES REGISTER';
		$report_date = date('d-m-Y',strtotime($postData['from_date'])).' to '.date('d-m-Y',strtotime($postData['to_date']));

		$companyData = $this->salesInvoice->getCompanyInfo();
		$logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
		$logo = base_url('assets/images/' . $logoFile);
		$letter_head = base_url('assets/images/letterhead_top.png');
		$InvData = $this->salesInvoice->getSalesInvDataBillWise($postData); //print_r($InvData);exit;

		$tbody="";$thead=""; $i=1;

		$thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
		$thead .= '<tr>
						<th>#</th>
						<th>Invoice No.</th>
						<th>Invoice Date</th>
						<th>Customer Name</th>
						<th>Taxable Amount</th>
						<th>Net Amount</th>							
				</tr>';
		foreach($InvData as $row):
			$tbody .= '<tr>
				<td>'.$i++.'</td>
				<td>'.$row->trans_number.'</td>
				<td>'.date("d-m-Y",strtotime($row->trans_date)).'</td>
				<td>'.$row->party_name.'</td>
				<td class="text-right">'.$row->taxable_amount.'</td>
				<td class="text-right">'.$row->net_amount.'</td>
				</tr>';
		endforeach;
			
		$pdfData = '<table id="commanTable" class="table table-bordered">
							<thead class="thead-info" id="theadData">'.$thead.'</thead>
							<tbody id="receivableData">'.$tbody.'</tbody>
						</table>';
		$htmlHeader = '<img src="' . $letter_head . '">';
		$htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
					<tr>
						<td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%"></td>
						<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
						<td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%"></td>
					</tr>
				</table>
				<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
					<tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
				</table>
				<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:10px;">
					<tr>
						<td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">Date : '.$report_date.'</td>
						<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
						<td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">
						    '.$result->taxable_amount.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$result->net_amount.'
						</td>
					</tr>
				</table>';
				
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
					<tr>
						<td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
						<td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
					</tr>
				</table>';
		
		$custOption = '<select name="party_id" id="party_id" class="form-control single-select cstfilter" style="width:35%;"><option value="">All Customer</option>';
        $customerList = $this->salesInvoice->getCustomerListOnlySales($this->loginId,$postData);
        if(!empty($customerList)){
            foreach($customerList as $row){
                $select = (!empty($postData['party_id']) AND $postData['party_id'] == $row->id) ? 'selected' : '';
	        	$custOption .= '<option value="'.$row->id.'" '.$select.'>'.$row->party_name.' | '.$row->city_name.'</option>';
            }
        }
		$custOption .= '</select>';
		if(!empty($postData['pdf']))
		{
    		$mpdf = new \Mpdf\Mpdf();
    		$filePath = realpath(APPPATH . '../assets/uploads/');
    		$pdfFileName = $filePath.'/SalesRegister.pdf';
    		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
    		$mpdf->WriteHTML($stylesheet, 1);
    		$mpdf->SetDisplayMode('fullpage');
    		$mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
    		$mpdf->showWatermarkImage = true;
    		$mpdf->SetTitle($reportTitle);
    		$mpdf->SetHTMLHeader($htmlHeader);
    		$mpdf->SetHTMLFooter($htmlFooter);
    		$mpdf->AddPage('L','','','','',5,5,30,5,3,3,'','','','','','','','','','A4-L');
    		$mpdf->WriteHTML($pdfData);
    		
    		ob_clean();
    		$mpdf->Output($pdfFileName, 'I');
		}
		else{$this->printJson(['taxable_amount'=>$result->taxable_amount,'gst_amount'=>$result->gst_amount,'net_amount'=>$result->net_amount,'custOption'=>$custOption]);}

		
    }

	//  Create By : Karmi @26-05-2022
	public function getInvoiceSummarybillWise($jsonData=''){
		if(!empty($jsonData)){$postData = (Array) json_decode(urldecode(base64_decode($jsonData)));}
        else{$postData = $this->input->post();}
        $result = $this->salesInvoice->getInvoiceSummary($postData);
		$reportTitle = 'ITEM WISE SALES REGISTER';
		$report_date = date('d-m-Y',strtotime($postData['from_date'])).' to '.date('d-m-Y',strtotime($postData['to_date']));

		$companyData = $this->salesInvoice->getCompanyInfo();
		$logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
		$logo = base_url('assets/images/' . $logoFile);
		$letter_head = base_url('assets/images/letterhead_top.png');
		$InvData = $this->salesInvoice->getSalesInvDataItemWise($postData); //print_r($InvData);exit;

		$tbody="";$thead=""; $i=1;

		$thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
		$thead .= '<tr>
					<th class="text-center">#</th>
					<th class="text-center">Invoice No.</th>
					<th class="text-center">Invoice Date</th>
					<th class="text-left">Customer Name</th>
					<th class="text-right">Item Name</th>
					<th class="text-right">Qty</th>
					<th class="text-right">Rate</th>
					<th class="text-right">Discount</th>
					<th class="text-right">Amount</th>
				</tr>';
		foreach($InvData as $row):
			$tbody .= '<tr>
				<td>'.$i++.'</td>
				<td>'.$row->trans_number.'</td>
				<td>'.date("d-m-Y",strtotime($row->trans_date)).'</td>
				<td>'.$row->party_name.'</td>
				<td>'.$row->item_name.'</td>
				<td>'.$row->qty.'</td>
				<td>'.$row->price.'</td>
				<td  class="text-right">'.$row->disc_amount.'</td>
				<td  class="text-right">'.$row->amount.'</td>
				</tr>';
		endforeach;
			
		$pdfData = '<table id="commanTable" class="table table-bordered">
							<thead class="thead-info" id="theadData">'.$thead.'</thead>
							<tbody id="receivableData">'.$tbody.'</tbody>
						</table>';
		$htmlHeader = '<img src="' . $letter_head . '">';
		$htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
					<tr>
						<td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%"></td>
						<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
						<td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%"></td>
					</tr>
				</table>
				<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
					<tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
				</table>
				<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:10px;">
					<tr>
						<td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">Date : '.$report_date.'</td>
						<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
						<td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">'.$result->taxable_amount.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$result->net_amount.'</td>
					</tr>
				</table>';
				
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
					<tr>
						<td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
						<td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
					</tr>
				</table>';
		
		/*** Custome Select Box ***/
		$custOption = '<select name="party_id" id="party_id" class="form-control single-select cstfilter" style="width:35%;"><option value="">All Customer</option>';
        $customerList = $this->salesInvoice->getCustomerListOnlySales($this->loginId,$postData);
        if(!empty($customerList)){
            foreach($customerList as $row){
                $select = (!empty($postData['party_id']) AND $postData['party_id'] == $row->id) ? 'selected' : '';
	        	$custOption .= '<option value="'.$row->id.'" '.$select.'>'.$row->party_name.' | '.$row->city_name.'</option>';
            }
        }
		$custOption .= '</select>';
		
		if(!empty($postData['pdf']))
		{
    		$mpdf = new \Mpdf\Mpdf();
    		$filePath = realpath(APPPATH . '../assets/uploads/');
    		$pdfFileName = $filePath.'/SalesRegister.pdf';
    		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
    		$mpdf->WriteHTML($stylesheet, 1);
    		$mpdf->SetDisplayMode('fullpage');
    		$mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
    		$mpdf->showWatermarkImage = true;
    		$mpdf->SetTitle($reportTitle);
    		$mpdf->SetHTMLHeader($htmlHeader);
    		$mpdf->SetHTMLFooter($htmlFooter);
    		$mpdf->AddPage('L','','','','',5,5,30,5,3,3,'','','','','','','','','','A4-L');
    		$mpdf->WriteHTML($pdfData);
    		
    		ob_clean();
    		$mpdf->Output($pdfFileName, 'I');
		}
		else{$this->printJson(['taxable_amount'=>$result->taxable_amount,'net_amount'=>$result->net_amount,'custOption'=>$custOption]);}

		
    }

	public function createInvoice(){
		$data = $this->input->post();
		$invMaster = new stdClass();
        $invMaster = $this->party->getParty($data['party_id']);  
		$this->data['gst_type']  = (!empty($invMaster->gstin))?((substr($invMaster->gstin,0,2) == 24)?1:2):1;		
		$this->data['from_entry_type'] = $data['from_entry_type'];
		
		if(!empty($data['inv_id'])):
			$invData = $this->salesInvoice->getInvoice($data['inv_id']);unset($invData->itemData);
			$this->data['invoiceData'] = $invData;
		endif;
		$this->data['invMaster'] = $invMaster;
		$invItems = ($data['from_entry_type'] == 5)?$this->challan->getChallanItems($data['ref_id']):$this->salesOrder->getOrderItemsOnTransIds($data['ref_id']);
		$this->data['invItems'] = $invItems;
		
		if($data['from_entry_type'] == 4):
		    $refIds = array_unique(array_column($invItems,'trans_main_id'));
		    $data['ref_id'] = $refIds;
		    $this->data['ref_id'] = implode(',',$refIds);
		else:
		    $this->data['ref_id'] = implode(",",$data['ref_id']);
		endif;
		
		$soData = $this->salesOrder->getOrderByRefid($data['ref_id']);
		if($data['from_entry_type'] == 5):
		    //print_r($this->db->last_query());
		    //print_r($soData);exit;
			$this->data['soTransNo'] = (!empty($soData->ref_trans_number))?$soData->ref_trans_number:"";
			$this->data['challanNo'] = (!empty($soData->trans_number))?$soData->trans_number:"";
		else:
			$this->data['soTransNo'] = (!empty($soData->trans_number))?$soData->trans_number:"";
			$this->data['challanNo'] = "";
		endif;
		
		$this->data['trans_prefix'] = $this->transModel->getTransPrefix(6);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(6);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemLists("1,10");
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsList();
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
		$this->data['spAccounts'] = $this->ledger->getLedgerList(["'SA'"]); 
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'","'ED'","'EI'","'ID'","'II'"]);
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['transportData'] = $this->transport->getTransportList();
        $this->load->view($this->invoiceForm,$this->data);
	}

	public function createInvoiceOnCustomInv(){
		$data = $this->input->post();
		$invMaster = new stdClass();
        $invMaster = $this->party->getParty($data['party_id']);  
		$customInvoiceData = $this->customInvoice->getCustomInvoiceData($data['ref_id'][0]);
		$invNo = explode("/",$customInvoiceData->doc_no);
		$gst_applicable = ($customInvoiceData->export_type == "(Supply Meant For Export With Payment Of IGST)")?1:0;
		$sp_acc_id = ($customInvoiceData->export_type == "(Supply Meant For Export With Payment Of IGST)")?532:533;
		$this->data['entry_type'] = 8;
		$this->data['sales_type'] = 2;
		$this->data['gst_type']  = 2;		
		$this->data['from_entry_type'] = $data['from_entry_type'];
		$this->data['ref_id'] = implode(",",$data['ref_id']);
		$invMaster->currency = $customInvoiceData->currency;
		$this->data['invMaster'] = $invMaster;
		$itemData = $this->customInvoice->getCustomInvoiceItems($data['ref_id']);
		$itemList = array();

		foreach($itemData as $row):
			$row->qty = $row->qty - $row->dispatch_qty;
			if($row->qty > 0):
				$batchData = $this->store->batchWiseItemStock(['item_id'=>$row->item_id])['result'];
				if(!empty($batchData)):
					foreach($batchData as $batch):
						if($row->packing_trans_id == $batch->trans_ref_id):
							$cl_stock = floatVal($batch->qty);
							$row->batch_qty = ($cl_stock > $row->qty)?$row->qty:$cl_stock;
							$row->location_id = $batch->location_id;
							$row->batch_no = $batch->batch_no;
							$row->packing_trans_id = $batch->trans_ref_id;
							$row->rev_no = $batch->trans_ref_id;
						endif;
					endforeach;
				endif;
				$itemList[] = $row;
			endif;
		endforeach;
		
		//print_r($customInvoiceData->so_id);exit;
		if(!empty($customInvoiceData->so_id)):
			$soData = $this->salesOrder->getOrderByRefid(explode(",",$customInvoiceData->so_id));
			$this->data['soTransNo'] = (!empty($soData->trans_number))?$soData->trans_number:"";
		else:
			$this->data['soTransNo'] = "";
		endif;
		
		$this->data['invItems'] = $itemList;
		$this->data['trans_prefix'] = $this->transModel->getTransPrefix(8);
        $this->data['nextTransNo'] = (!empty($invNo[1]))?$invNo[1]:$this->transModel->nextTransNo(8);
		$this->data['trans_date'] = $customInvoiceData->doc_date;
		$this->data['gst_applicable'] = $gst_applicable;
		$this->data['sp_acc_id'] = $sp_acc_id;
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemLists("1,10");
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsList();
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
		$this->data['spAccounts'] = $this->ledger->getLedgerList(["'SA'"]); 
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'","'ED'","'EI'","'ID'","'II'"]);
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['transportData'] = $this->transport->getTransportList();
        $this->load->view($this->invoiceForm,$this->data);
	}

    public function addInvoice(){
		$this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = "";
		$this->data['gst_type'] = 1;
		$this->data['trans_prefix'] = $this->transModel->getTransPrefix(6);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(6);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemLists("1,10");
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsList();
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
		$this->data['spAccounts'] = $this->ledger->getLedgerList(["'SA'"]); 
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'","'ED'","'EI'","'ID'","'II'"]);
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['transportData'] = $this->transport->getTransportList();
        $this->load->view($this->invoiceForm,$this->data);
    }

	public function getBatchNo(){
        $item_id = $this->input->post('item_id');
        $location_id = $this->input->post('location_id');
        $batchData = $this->item->locationWiseBatchStock($item_id,$location_id);
        $options = '<option value="">Select Batch No.</option>';
        foreach($batchData as $row):
			if($row->qty > 0):
				$options .= '<option value="'.$row->batch_no.'" data-stock="'.$row->qty.'">'.$row->batch_no.'</option>';
			endif;
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

	public function getItemData(){
		$id = $this->input->post('itemId');
        $result = $this->item->getItem($id);
        $unitData = $this->item->itemUnit($result->unit_id);
        $result->unit_name = $unitData->unit_name;
        $result->description = $unitData->description;
		$this->printJson($result);
	}
	
	public function save(){
		$data = $this->input->post();
		//print_r($data);exit;
		$errorMessage = array();
		if($data['sales_type'] == 2 && empty($data['inrrate']))
			$errorMessage['inrrate'] = "INR Rate is required.";
		if(empty($data['party_id'])):
			$errorMessage['party_id'] = "Party name is required.";
		else:
			$partyData = $this->party->getParty($data['party_id']); 
			if(floatval($partyData->inrrate) <= 0):
				$errorMessage['party_id'] = "Currency not set.";
			else:
				$data['currency'] = (!empty($data['currency']))?$data['currency']:$partyData->currency;
				$data['inrrate'] = (!empty($data['inrrate']))?$data['inrrate']:$partyData->inrrate;
			endif;
			
			$data['credit_period'] = $partyData->credit_days;
		endif;
		if(empty($data['sp_acc_id']))
			$errorMessage['sp_acc_id'] = "Sales A/c. is required.";
		if(empty($data['item_id'][0]))
			$errorMessage['item_name_error'] = "Product is required.";
		
		if(!empty($data['item_id'])):
			$i=1;
			foreach($data['item_id'] as $key=>$value):
				if(empty($data['price'][$key])):
					$errorMessage['price'.$i] = "Price is required.";
				elseif($data['stock_eff'][$key] == 1):
					/* $cStock = $this->store->getItemCurrentStock($value,$this->RTD_STORE->id);
					$currentStock = (!empty($cStock)) ? $cStock->qty : 0;
					$old_qty = 0;
					if(!empty($data['trans_id'][$key])):
						$transData = $this->salesInvoice->salesTransRow($data['trans_id'][$key]);
						if(!empty($transData)){$old_qty = $transData->qty;}
					endif;
					if(($currentStock + $old_qty) < $data['qty'][$key]):
						$errorMessage["qty".$i] = "Stock not available.";
					endif;

					if(empty($data['batch_no'][$key])):
						$errorMessage['batch_no'.$i] = "Batch Details is required.";
					endif; */
					$qty_error=Array();
					foreach(explode(',',$data['location_id'][$key]) as $lkey=>$lid){
						$stockQ = Array();
						$stockQ['item_id'] = $value;$stockQ['location_id'] = $lid;$stockQ['batch_no'] = explode(',',$data['batch_no'][$key])[$lkey];
						$stockData = $this->store->getItemStockGeneral($stockQ);
						$packing_qty = (!empty($stockData)) ? $stockData->qty : 0;
						$old_qty = 0;
						if(!empty($data['trans_id'][$key])):
							$oldCHData = $this->salesInvoice->salesTransRow($data['trans_id'][$key]);
							$oldBatches = explode(',',$oldCHData->batch_no);$oldLocations = explode(',',$oldCHData->location_id);
							if(in_array($stockQ['batch_no'],$oldBatches)){
								$batchQtyKey = array_search($stockQ['batch_no'],$oldBatches);
								$old_qty = explode(',',$oldCHData->batch_qty)[$batchQtyKey];
							}
						endif;

						if(($packing_qty + $old_qty) < explode(',',$data['batch_qty'][$key])[$lkey]):
							$qty_error[]= $stockQ['batch_no'];
						endif;
					}
					if(!empty($qty_error)){$errorMessage["qty".$i] = "Stock not available. Batch No. = ".implode(', ',$qty_error);}
		
					if(empty($data['batch_no'][$key])):
						$errorMessage['batch'.$i] = "Batch Details is required.";
					endif;
				endif;
				$i++;
			endforeach;
		endif;
		
		if(empty($data['term_id'][0]))
			$errorMessage['term_id'] = "Terms Conditions is required.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$data['terms_conditions'] = "";$termsArray = array();
			if(isset($data['term_id']) && !empty($data['term_id'])):
				foreach($data['term_id'] as $key=>$value):
					$termsArray[] = [
						'term_id' => $value,
						'term_title' => $data['term_title'][$key],
						'condition' => $data['condition'][$key]
					];
				endforeach;
				$data['terms_conditions'] = json_encode($termsArray);
			endif;

			$gstAmount = 0;
			if($data['gst_type'] == 1):
				if(isset($data['cgst_amount'])):
					$gstAmount = $data['cgst_amount'] + $data['sgst_amount'];
				endif;	
			elseif($data['gst_type'] == 2):
				if(isset($data['igst_amount'])):
					$gstAmount = $data['igst_amount'];
				endif;
			endif;
			
			$masterData = [ 
				'id' => $data['sales_id'],
				'entry_type' => $data['entry_type'],
				'from_entry_type' => $data['reference_entry_type'],
				'ref_id' => $data['reference_id'],
				'trans_no' => $data['inv_no'], 
				'trans_prefix' => $data['inv_prefix'],
				'trans_number' => getPrefixNumber($data['inv_prefix'],$data['inv_no']),
				'trans_date' => date('Y-m-d',strtotime($data['inv_date'])), 
				'credit_period' => $data['credit_period'],
				'party_id' => $data['party_id'],
				'opp_acc_id' => $data['party_id'],
				'sp_acc_id' => $data['sp_acc_id'],
				'party_name' => $data['party_name'],
				'party_state_code' => $data['party_state_code'],
				'gstin' => $data['gstin'],
				'gst_applicable' => $data['gst_applicable'],
				'gst_type' => $data['gst_type'],
				'sales_type' => $data['sales_type'], 
				'challan_no' => $data['challan_no'], 
				'doc_no'=>$data['so_no'],
				'doc_date'=>date('Y-m-d',strtotime($data['inv_date'])),
				'gross_weight' => $data['gross_weight'],
				'total_packet' => $data['total_packet'],
				'eway_bill_no' => $data['eway_bill_no'],
				'lr_no' => $data['lrno'],
				'transport_name' => $data['transport'],
				'shipping_address' => $data['supply_place'],
				'total_amount' => array_sum($data['amount']) + array_sum($data['disc_amt']),
				'taxable_amount' => $data['taxable_amount'],
				'gst_amount' => $gstAmount,
				'igst_acc_id' => (isset($data['igst_acc_id']))?$data['igst_acc_id']:0,
				'igst_per' => (isset($data['igst_per']))?$data['igst_per']:0,
				'igst_amount' => (isset($data['igst_amount']))?$data['igst_amount']:0,
				'sgst_acc_id' => (isset($data['sgst_acc_id']))?$data['sgst_acc_id']:0,
				'sgst_per' => (isset($data['sgst_per']))?$data['sgst_per']:0,
				'sgst_amount' => (isset($data['sgst_amount']))?$data['sgst_amount']:0,
				'cgst_acc_id' => (isset($data['cgst_acc_id']))?$data['cgst_acc_id']:0,
				'cgst_per' => (isset($data['cgst_per']))?$data['cgst_per']:0,
				'cgst_amount' => (isset($data['cgst_amount']))?$data['cgst_amount']:0,
				'cess_acc_id' => (isset($data['cess_acc_id']))?$data['cess_acc_id']:0,
				'cess_per' => (isset($data['cess_per']))?$data['cess_per']:0,
				'cess_amount' => (isset($data['cess_amount']))?$data['cess_amount']:0,
				'cess_qty_acc_id' => (isset($data['cess_qty_acc_id']))?$data['cess_qty_acc_id']:0,
				'cess_qty' => (isset($data['cess_qty']))?$data['cess_qty']:0,
				'cess_qty_amount' => (isset($data['cess_qty_amount']))?$data['cess_qty_amount']:0,
				'tcs_acc_id' => (isset($data['tcs_acc_id']))?$data['tcs_acc_id']:0,
				'tcs_per' => (isset($data['tcs_per']))?$data['tcs_per']:0,
				'tcs_amount' => (isset($data['tcs_amount']))?$data['tcs_amount']:0,
				'tds_acc_id' => (isset($data['tds_acc_id']))?$data['tds_acc_id']:0,
				'tds_per' => (isset($data['tds_per']))?$data['tds_per']:0,
				'tds_amount' => (isset($data['tds_amount']))?$data['tds_amount']:0,
				'disc_amount' => array_sum($data['disc_amt']),
				'apply_round' => $data['apply_round'], 
				'round_off_acc_id'  => (isset($data['roff_acc_id']))?$data['roff_acc_id']:0,
				'round_off_amount' => (isset($data['roff_amount']))?$data['roff_amount']:0, 
				'net_amount' => $data['net_inv_amount'],
				'terms_conditions' => $data['terms_conditions'],
                'remark' => $data['remark'],
                'currency' => $data['currency'],
                'inrrate' => $data['inrrate'],
				'vou_name_s' => getVoucherNameShort($data['entry_type']),
				'vou_name_l' => getVoucherNameLong($data['entry_type']),
				'ledger_eff' => 1,
				'created_by' => $this->session->userdata('loginId')
			];

			$transExp = getExpArrayMap($data);
			$expAmount = $transExp['exp_amount'];
			$expenseData = array();
            if($expAmount > 0):
				unset($transExp['exp_amount']);    
				$expenseData = $transExp;
			endif;

			$accType = getSystemCode($data['entry_type'],false);
            if(!empty($accType)):
				$spAcc = $this->ledger->getLedgerOnSystemCode($accType);
                $masterData['vou_acc_id'] = (!empty($spAcc))?$spAcc->id:0;
            else:
                $masterData['vou_acc_id'] = 0;
            endif;
			
			$itemData = [
				'id' => $data['trans_id'],
				'from_entry_type' => $data['from_entry_type'],
				'ref_id' => $data['ref_id'],
				'item_id' => $data['item_id'],
				'item_name' => $data['item_name'],
				'item_type' => $data['item_type'],
				'item_code' => $data['item_code'],
				'item_desc' => $data['item_desc'],
				'unit_id' => $data['unit_id'],
				'unit_name' => $data['unit_name'],
				'location_id' => $data['location_id'],
				'batch_no' => $data['batch_no'],
				'batch_qty' => $data['batch_qty'],
				'packing_trans_id' => $data['packing_trans_id'],
				'stock_eff' => $data['stock_eff'],
				'hsn_code' => $data['hsn_code'],
				'qty' => $data['qty'],
				'price' => $data['price'],
				'org_price' => $data['org_price'],
				'amount' => $data['amount'],
				'taxable_amount' => $data['amount'],				
				'gst_per' => $data['gst_per'],
				'gst_amount' => $data['igst_amt'],
				'igst_per' => $data['igst'],
				'igst_amount' => $data['igst_amt'],
				'sgst_per' => $data['sgst'],
				'sgst_amount' => $data['sgst_amt'],
				'cgst_per' => $data['cgst'],
				'cgst_amount' => $data['cgst_amt'],
				'disc_per' => $data['disc_per'],
				'disc_amount' => $data['disc_amt'],
				'item_remark' => $data['item_remark'],
				'net_amount' => $data['net_amount']
			];

			$this->printJson($this->salesInvoice->save($masterData,$itemData,$expenseData));
		endif;
	}

	public function edit($id){
		$this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = "";
		$this->data['invoiceData'] = $this->salesInvoice->getInvoice($id);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemLists("1,10");
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsList();
		$this->data['invMaster'] = $this->party->getParty($this->data['invoiceData']->party_id);  
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
		$this->data['spAccounts'] = $this->ledger->getLedgerList(["'SA'"]); 
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'","'ED'","'EI'","'ID'","'II'"]);
        $this->data['transportData'] = $this->transport->getTransportList();
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->load->view($this->invoiceForm,$this->data);
	}

	public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->salesInvoice->deleteInv($id));
		endif;
	}

	public function batchWiseItemStock(){
		$data = $this->input->post();
        $result = $this->challan->batchWiseItemStock($data);
        $this->printJson($result);
	}

	public function getInvoiceNo(){
		$type = $this->input->post('sales_type');
		if($type == "1"):
			$trans_prefix = $this->transModel->getTransPrefix(6);
        	$nextTransNo = $this->transModel->nextTransNo(6);
			$entry_type = 6;
		elseif($type == "2"):
			$trans_prefix = $this->transModel->getTransPrefix(8);
        	$nextTransNo = $this->transModel->nextTransNo(8);
			$entry_type = 8;
		elseif($type == "3"):
			$trans_prefix = $this->transModel->getTransPrefix(7);
        	$nextTransNo = $this->transModel->nextTransNo(7);
			$entry_type = 7;
		endif;

		$this->printJson(['status'=>1,'trans_prefix'=>$trans_prefix,'nextTransNo'=>$nextTransNo,'entry_type'=>$entry_type]);
	}

	public function getPartyItems(){
		$this->printJson($this->item->getPartyItems($this->input->post('party_id')));
	}
	
		//Created By Meghavi @03/09/2022
	public function getBlData(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->salesInvoice->getInvoice($id);	
        $this->load->view($this->blForm,$this->data);
    }

    public function updateBlData(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['bl_no']))
            $errorMessage['bl_no'] = "B.L. No. is required.";
        if(empty($data['bl_date']))
            $errorMessage['bl_date'] = "B.L. Date is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->salesInvoice->saveBlData($data));
        endif;
    }
	
	/**
	 *Updated By Mansee @ 29-12-2021 503,504,511,512
	 */
    public function invoice_pdf()
	{
		$postData = $this->input->post();
		$original=0;$duplicate=0;$triplicate=0;$header_footer=0;$extra_copy=0;
		if(isset($postData['original'])){$original=1;}
		if(isset($postData['duplicate'])){$duplicate=1;}
		if(isset($postData['triplicate'])){$triplicate=1;}
		if(isset($postData['header_footer'])){$header_footer=1;}
		if(!empty($postData['extra_copy'])){$extra_copy=$postData['extra_copy'];}
		
		$sales_id=$postData['printsid'];
		$salesData = $this->salesInvoice->getInvoice($sales_id);
		$companyData = $this->salesInvoice->getCompanyInfo();
		
		$partyData = $this->party->getParty($salesData->party_id);
		
		$response="";
		$letter_head=base_url('assets/images/letterhead_top.png');
		
        $sign_img = base_url('assets/uploads/emp_sign/sign_'.(281).'.png');
        $authorise_sign = '<img src="'.$sign_img.'" style="width:100px;">'; 
		$authorise_sign = ($salesData->entry_type == 8)?$authorise_sign:'';
		$currencyCode = "INR";
		$symbol = "";
		
		$response="";$inrSymbol=base_url('assets/images/inr.png');
		$headerImg = base_url('assets/images/rtth_lh_header.png');
		$footerImg = base_url('assets/images/rtth_lh_footer.png');
		$logoFile=(!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
		$logo=base_url('assets/images/'.$logoFile);
		$auth_sign=base_url('assets/images/rtth_sign.png');
		
		$gstHCol='';$gstCol='';$blankTD='';$bottomCols=2;$GSTAMT=$salesData->igst_amount;
		$subTotal=$salesData->taxable_amount;
		$itemList='<table class="table table-bordered poItemList">
					<thead><tr class="text-center">
						<th style="width:6%;">Sr.No.</th>
						<th class="text-left">Description of Goods</th>
						<th style="width:10%;">HSN/SAC</th>
						<th style="width:10%;">Qty</th>
						<th style="width:10%;">Rate<br><small>('.$partyData->currency.')</small></th>
						<th style="width:6%;">Disc.</th>
						<th style="width:8%;">GST</th>
						<th style="width:11%;">Amount<br><small>('.$partyData->currency.')</small></th>
					</tr></thead><tbody>';
		
		// Terms & Conditions
		
		$blankLines=10;if(!empty($header_footer)){$blankLines=10;}
		$terms = '<table class="table">';$t=0;$tc=new StdClass;		
		if(!empty($salesData->terms_conditions))
		{
			$tc=json_decode($salesData->terms_conditions);
			$blankLines=12 - count($tc);if(!empty($header_footer)){$blankLines=12 - count($tc);}
			foreach($tc as $trms):
				if($t==0):
					$terms .= '<tr>
									<th style="width:17%;font-size:12px;text-align:left;">'.$trms->term_title.'</th>
									<td style="width:48%;font-size:12px;">: '.$trms->condition.'</td>
									<th rowspan="'.count($tc).'" style="width:35%;vertical-align:bottom;text-align:center;font-size:1rem;padding:5px 2px;">
										For, '.$companyData->company_name.'<br>	
										'.$authorise_sign.'
									</th>
							</tr>';
				else:
					$terms .= '<tr>
									<th style="font-size:12px;text-align:left;">'.$trms->term_title.'</th>
									<td style="font-size:12px;">: '.$trms->condition.'</td>
							</tr>';
				endif;$t++;
			endforeach;
		}
		else
		{
			$tc = array();
			$terms .= '<tr>
							<td style="width:65%;font-size:12px;">Subject to RAJKOT Jurisdiction</td>
							<th rowspan="'.count($tc).'" style="width:35%;vertical-align:bottom;text-align:center;font-size:1rem;padding:5px 2px;">
								For, '.$companyData->company_name.'<br>
								<!--<img src="'.$auth_sign.'" style="width:120px;">-->
							</th>
					</tr>';
		}
		
		$terms .= '</table>';
		
		$subTotal=0;$lastPageItems = '';$pageCount = 0;
		$i=1;$tamt=0;$cgst=9;$sgst=9;$cgst_amt=0;$sgst_amt=0;$netamt=0;$igst=0;$hsnCode='';$total_qty=0;$page_qty = 0;$page_amount = 0;
		$pageData = array();$totalPage = 0;
		$salesData->itemData = $this->salesInvoice->salesTransactions($sales_id);
		$totalItems = count($salesData->itemData);
		
		$lpr = $blankLines ;$pr1 = $blankLines + 6 ;
		$pageRow = $pr = ($totalItems > $lpr) ? $pr1 : $totalItems;
		$lastPageRow = (($totalItems % $lpr)==0) ? $lpr : ($totalItems % $lpr);
		$remainRow = $totalItems - $lastPageRow;
		$pageSection = round(($remainRow/$pageRow),2);
		$totalPage = (numberOfDecimals($pageSection)==0)? (int)$pageSection : (int)$pageSection + 1;
		$totalPage = ($totalPage > 1)?($totalPage - 1):$totalPage;
		//print_r($totalPage);exit;
		for($x=0;$x<=$totalPage;$x++)
		{
			$page_qty = 0;$page_amount = 0;
			$pageItems = '';$pr = ($x==$totalPage) ? $totalItems - ($i-1) : $pr;
			//print_r($pr.','.$pageCount);print_r("<hr>");
			$tempData = $this->salesInvoice->salesTransactions($sales_id,$pr.','.$pageCount);
			if(!empty($tempData))
			{
				foreach ($tempData as $row)
				{
					$pageItems.='<tr>';
						$pageItems.='<td class="text-center" height="37">'.$i.'</td>';
						$pageItems.='<td class="text-left">'.$row->item_name.'</td>';
						$pageItems.='<td class="text-center">'.$row->hsn_code.'</td>';
						$pageItems.='<td class="text-center">'.sprintf('%0.2f', $row->qty).'</td>';
						$pageItems.='<td class="text-right">'.sprintf('%0.2f', $row->price).'</td>';
						$pageItems.='<td class="text-center">'.floatval($row->disc_per).'</td>';
						$pageItems.='<td class="text-center">'.floatval($row->igst_per).'%</td>';
						$pageItems.='<td class="text-right">'.sprintf('%0.2f', $row->amount).'</td>';
					$pageItems.='</tr>';
					
					$total_qty += $row->qty;$page_qty += $row->qty;$page_amount += $row->amount;$subTotal += $row->amount;$i++;
				}
			}
			if($x==$totalPage)
			{
				$pageData[$x]= '';
				$lastPageItems = $pageItems;
			}
			else
			{
				/*$pageItems.='<tr>';
					$pageItems.='<th class="text-right" style="border:1px solid #000;" colspan="5">Page Total</th>';
					$pageItems.='<th class="text-right" style="border-top:1px solid #000000;">'.sprintf('%0.3f', $page_qty).'</th>';
					$pageItems.='<th class="text-right" style="border-top:1px solid #000000;"></th>';
					$pageItems.='<th class="text-right" style="border-top:1px solid #000000;">'.sprintf('%0.2f', $page_amount).'</th>';
				$pageItems.='</tr>';*/
				$pageData[$x]=$itemList.$pageItems.'</tbody></table><div class="text-right"><i>Continue to Next Page</i></div>';
			}
			$pageCount += $pageRow;
		}//exit;
		$taxableAmt= $subTotal + $salesData->freight_amount;
		$fgst = round(($salesData->freight_gst / 2),2);
		$rwspan= 4;
		
		$gstRow='<tr>';
			$gstRow.='<td colspan="3" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">CGST</td>';
			$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', ($salesData->cgst_amount + $fgst)).'</td>';
		$gstRow.='</tr>';
		
		$gstRow.='<tr>';
			$gstRow.='<td colspan="3" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">SGST</td>';
			$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', ($salesData->sgst_amount + $fgst)).'</td>';
		$gstRow.='</tr>';
		
		$party_gstin = (!empty($salesData->party_state_code)) ? explode('#',$salesData->party_state_code)[0] : '';
		$party_stateCode = (!empty($salesData->party_state_code)) ? explode('#',$salesData->party_state_code)[1] : '';
		
		if(!empty($party_gstin))
		{
			if($party_stateCode!="24")
			{
				$gstRow='<tr>';
					$gstRow.='<td colspan="3" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">IGST</td>';
					$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', ($salesData->cgst_amount + $salesData->sgst_amount + $salesData->freight_gst)).'</td>';
				$gstRow.='</tr>';$rwspan= 3;
			}
		}
		$totalCols = 9;
		$itemList .= $lastPageItems;
		if($i<$blankLines)
		{
			for($z=$i;$z<=$blankLines;$z++)
			{$itemList.='<tr><td  height="37">&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';}
		}
		
		$itemList.='<tr>';
			$itemList.='<td colspan="3" class="text-right" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Total Qty</b></td>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $total_qty).'</th>';
			$itemList.='<th colspan="3" class="text-right" style="border:1px solid #000;border-left:0px solid #000;">Sub Total</th>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $subTotal).'</th>';
		$itemList.='</tr>';
		
		$itemList.='<tr>';
			$itemList.='<td colspan="4" rowspan="'.$rwspan.'" class="text-left" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Bank Name : </b>'.$companyData->company_bank_name.'<br>
			<b>A/c. No. : </b>'.$companyData->company_acc_no.'<br>
			<b>IFSC Code : </b>'.$companyData->company_ifsc_code.'
			</td>';
			$itemList.='<td colspan="3" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">P & F</td>';
			$itemList.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', $salesData->freight_amount).'</td>';
		$itemList.='</tr>';
		
		$itemList.='<tr>';
			$itemList.='<th colspan="3" class="text-right" style="border:1px solid #000;border-left:0px solid #000;">Taxable Amount</th>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $taxableAmt).'</th>';
		$itemList.='</tr>';
		
		$itemList.=$gstRow;
		
		$itemList.='<tr>';
			$itemList.='<td colspan="4" rowspan="2" class="text-left" style="vartical-align:top;border:1px solid #000;border-left:0px;"><i><b>Bill Amount In Words ('.$partyData->currency.') : </b>'.numToWordEnglish($salesData->net_amount).'</i></td>';
			$itemList.='<td colspan="3" class="text-right" style="border-right:1px solid #000;">Round Off</td>';
			$itemList.='<td class="text-right" style="border-top:0px !important;border-left:0px;">'.sprintf('%0.2f', $salesData->round_off_amount).'</td>';
		$itemList.='</tr>';
		
		$itemList.='<tr>';
			$itemList.='<th colspan="3" class="text-right" style="border-top:1px solid #000;border-right:1px solid #000;font-size:14px;">Payable Amount</th>';
			$itemList.='<th class="text-right" height="40" style="border-top:1px solid #000;border-left:0px;font-size:14px;">'.sprintf('%0.2f', $salesData->net_amount).'</th>';
		$itemList.='</tr>';
		$itemList.='<tbody></table>';
		
		$pageData[$totalPage] .= $itemList;
		$pageData[$totalPage] .= '<br><b><u>Terms & Conditions : </u></b><br>'.$terms.'';
		
		$invoiceType=array();
		$invType = array("ORIGINAL","DUPLICATE","TRIPLICATE","EXTRA COPY");$i=0;
		foreach($invType as $it)
		{
			$invoiceType[$i++]='<table style="margin-bottom:5px;">
									<tr>
										<th style="width:35%;letter-spacing:2px;" class="text-left fs-17" >GSTIN: '.$companyData->company_gst_no.'</th>
										<th style="width:30%;letter-spacing:2px;" class="text-center fs-17">TAX INVOICE</th>
										<th style="width:35%;letter-spacing:2px;" class="text-right">'.$it.'</th>
									</tr>
								</table>';
		}
		$gstJson=json_decode($partyData->json_data);
		$partyAddress=(!empty($gstJson->{$salesData->gstin})?$gstJson->{$salesData->gstin}:'');
		$baseDetail='<table class="poTopTable" style="margin-bottom:5px;">
						<tr>
							<td style="width:55%;" rowspan="3">
								<table>
									<tr><td style="vartical-align:top;"><b>BILL TO</b></td></tr>
									<tr><td style="vertical-align:top;"><b>'.$salesData->party_name.'</b></td></tr>
									<tr><td class="text-left" style="">'.(!empty($partyAddress->party_address)?$partyAddress->party_address:'').'</td></tr>
									<tr><td class="text-left" style=""><b>GSTIN : '.$salesData->gstin.'</b></td></tr>
								</table>
							</td>
							<td style="width:25%;border-bottom:1px solid #000000;border-right:0px;padding:2px;">
								<b>Invoice No. : '.getPrefixNumber($salesData->trans_prefix,$salesData->trans_no).'</b>
							</td>
							<td style="width:20%;border-bottom:1px solid #000000;border-left:0px;text-align:right;padding:2px 5px;">
								<b>Date : '.date('d/m/Y', strtotime($salesData->trans_date)).'</b>
							</td>
						</tr>
						<tr>
							<td style="width:45%;" colspan="2">
								<table>
									<tr><td style="vertical-align:top;"><b>P.O. No.</b></td><td>: '.$salesData->doc_no.'</td></tr>
									<tr><td style="vertical-align:top;"><b>Challan No</b></td><td>: '.$salesData->challan_no.'</td></tr>
									<tr><td style="vertical-align:top;"><b>Transport</b></td><td>: '.$salesData->transport_name.'</td></tr>
								</table>
							</td>
						</tr>
					</table>';
				
		$orsp='';$drsp='';$trsp='';
		$htmlHeader = '<img src="'.$letter_head.'">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;font-size:12px;">INV No. & Date : '.getPrefixNumber($salesData->trans_prefix,$salesData->trans_no).' • '.formatDate($salesData->trans_date).'</td>
							<td style="width:25%;font-size:12px;"></td>
							<td style="width:25%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = $this->m_pdf->load();
		$i=1;$p='P';
		$pdfFileName=base_url('assets/uploads/sales/sales_invoice_'.$sales_id.'.pdf');
		$fpath='/assets/uploads/sales/sales_invoice_'.$sales_id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/bill_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		
		if(!empty($header_footer))
		{
			$mpdf->SetWatermarkImage($logo,0.05,array(120,60));
			$mpdf->showWatermarkImage = true;
			$mpdf->SetHTMLHeader($htmlHeader);
			$mpdf->SetHTMLFooter($htmlFooter);
		}
		
		if(!empty($original))
		{
			foreach($pageData as $pg)
			{
				if(!empty($header_footer))
				{
					$mpdf->AddPage('P','','','','',7,7,38,7,4,6);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv1">'.$invoiceType[0].$baseDetail.$pg.'</div></div>');
				}
				else
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[0].$baseDetail.$pg.'</div></div>');
				}
			}
		}
		
		if(!empty($duplicate))
		{
			foreach($pageData as $pg)
			{
				if(!empty($header_footer))
				{
					$mpdf->AddPage('P','','','','',7,7,38,7,4,6);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv1">'.$invoiceType[1].$baseDetail.$pg.'</div></div>');
				}
				else
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[1].$baseDetail.$pg.'</div></div>');
				}
			}
		}
		
		if(!empty($triplicate))
		{
			foreach($pageData as $pg)
			{
				if(!empty($header_footer))
				{
					$mpdf->AddPage('P','','','','',7,7,38,7,4,6);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv1">'.$invoiceType[2].$baseDetail.$pg.'</div></div>');
				}
				else
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[2].$baseDetail.$pg.'</div></div>');
				}
			}
		}
		
		for($x=0;$x<$extra_copy;$x++)
		{
			foreach($pageData as $pg)
			{
				if(!empty($header_footer))
				{
					$mpdf->AddPage('P','','','','',7,7,38,7,4,6);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv1">'.$invoiceType[3].$baseDetail.$pg.'</div></div>');
				}
				else
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[3].$baseDetail.$pg.'</div></div>');
				}
			}
		}
		
		// $mpdf->Output(FCPATH.$fpath,'F');
		
		$mpdf->Output($pdfFileName,'I');
	}

	public function getItemList(){
        $this->printJson($this->salesInvoice->getItemList($this->input->post('id')));
    }
}
?>