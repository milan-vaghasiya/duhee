<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class JobWorkScrapInvoice extends MY_Controller{	
	private $indexPage = "jobwork_scrap_invoice/index";
    private $invoiceForm = "jobwork_scrap_invoice/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "JobWork Scrap Invoice";
		$this->data['headData']->controller = "jobworkScrapInvoice";
		$this->data['headData']->pageUrl = "jobWorkScrapInvoice";
	}
	
	public function index(){
		$this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);    
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($entry_type="20"){
		$data = $this->input->post(); $data['entry_type'] = $entry_type;
        $result = $this->jobworkScrapInvoice->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++; 
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getScrapInvoiceData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function getVendorJWScrap(){
		$this->printJson($this->jobworkScrapInvoice->getVendorJWScrap($this->input->post('party_id')));      
	}

	public function createInvoice(){ 
		$data = $this->input->post();
		$invMaster = new stdClass();
        $invMaster = $this->party->getParty($data['party_id']);  
		$this->data['gst_type']  = (!empty($invMaster->gstin))?((substr($invMaster->gstin,0,2) == 24)?1:2):1;		
		$this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = implode(",",$data['ref_id']);
		$this->data['invMaster'] = $invMaster;
		$this->data['invItems'] = $this->jobworkScrapInvoice->getJobworkScrapData($data['ref_id']); //print_r($this->data['invItems'] );exit;
		$this->data['trans_prefix'] = $this->transModel->getTransPrefix(20);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(20);
		$this->data['vendorList']=$this->party->getVendorList();
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsList();
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
		//$this->data['spAccounts'] = $this->ledger->getLedgerList(["'SA'"]); 
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'","'ED'","'EI'","'ID'","'II'"]);
        $this->load->view($this->invoiceForm,$this->data);
	}

    public function addInvoice(){
		$this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = "";
		$this->data['gst_type'] = 1;
		$this->data['trans_prefix'] = $this->transModel->getTransPrefix(20);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(20);
        $this->data['vendorList']=$this->party->getVendorList();
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsList();
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
		//$this->data['spAccounts'] = $this->ledger->getLedgerList(["'SA'"]); 
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'","'ED'","'EI'","'ID'","'II'"]);
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
		$data['currency'] = '';$data['inrrate'] = 0;
		if(empty($data['party_id'])):
			$errorMessage['party_id'] = "Party name is required.";
		else:
			$partyData = $this->party->getParty($data['party_id']); 
			
		endif;
		/* if(empty($data['sp_acc_id']))
			$errorMessage['sp_acc_id'] = "Sales A/c. is required."; */
		if(empty($data['item_id'][0]))
			$errorMessage['item_name_error'] = "Product is required.";
		
		/* if(!empty($data['item_id'])):
			$i=1;
			foreach($data['item_id'] as $key=>$value):
				if(empty($data['price'][$key])):
					$errorMessage['price'.$i] = "Price is required.";
				elseif($data['stock_eff'][$key] == 1):
					$packing_qty = $this->challan->getPackedItemQty($value)->qty;
					$old_qty = 0;
					if(!empty($data['trans_id'][$key])):
						$old_qty = $this->challan->challanTransRow($data['trans_id'][$key])->qty;
					endif;
					if(($packing_qty + $old_qty) < $data['qty'][$key]):
						$errorMessage["qty".$i] = "Stock not available.";
					endif;
				endif;
				$i++;
			endforeach;
		endif; */
		
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
				'party_id' => $data['party_id'],
				'opp_acc_id' => $data['party_id'],
				//'sp_acc_id' => $data['sp_acc_id'],
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
				'stock_eff' => $data['stock_eff'],
				'hsn_code' => $data['hsn_code'],
				'qty' => $data['qty'],
				'price' => $data['price'],
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

			// print_r($masterData);
			// print_r($itemData);
			// exit;

			$this->printJson($this->jobworkScrapInvoice->save($masterData,$itemData,$expenseData));
		endif;
	}

	public function edit($id){
		$this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = "";
		$this->data['invoiceData'] = $this->jobworkScrapInvoice->getInvoice($id);
		//print_r($this->data['invoiceData']);exit;
        $this->data['vendorList']=$this->party->getVendorList();
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsList();
		$this->data['invMaster'] = $this->party->getParty($this->data['invoiceData']->party_id);  
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
		$this->data['spAccounts'] = $this->ledger->getLedgerList(["'SA'"]); 
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'","'ED'","'EI'","'ID'","'II'"]);
        $this->load->view($this->invoiceForm,$this->data);
	}

	//Created By Karmi @06/04/2022 
	public function copyInv($id){
		$this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = "";
		$invoiceData = $this->jobworkScrapInvoice->getInvoice($id);
        $this->data['vendorList']=$this->party->getVendorList();
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsList();
		$this->data['invMaster'] = $this->party->getParty($invoiceData->party_id);  
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
		$this->data['spAccounts'] = $this->ledger->getLedgerList(["'SA'"]); 
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'","'ED'","'EI'","'ID'","'II'"]);
		$this->data['trans_prefix'] = $this->transModel->getTransPrefix(6);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(6);
		$itemData = array();
		if(!empty($invoiceData )){
			$invoiceData->id ="";
			$invoiceData->ref_id ="";
			$invoiceData->trans_no ="";
			$invoiceData->from_entry_type ="";
			foreach($invoiceData->itemData as $row)
			{
				$row->id = "";
				$row->ref_id = "";
				$row->trans_main_id ="";
				$row->from_entry_type ="";
				$itemData = $row;
			}
			
			
		}
		$this->data['invoiceData'] = $invoiceData;//print_r($this->data['invoiceData']);exit;

        $this->load->view($this->invoiceForm,$this->data);
	}

	public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->jobworkScrapInvoice->deleteInv($id));
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
			$trans_prefix = $this->transModel->getTransPrefix(14);
        	$nextTransNo = $this->transModel->nextTransNo(14);
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
		$postData = Array();
		$postData = $this->input->post();
		$postData['item_type'] = 1;
		$htmlOptions = $this->item->getFinishedGoodItems($postData);
		$this->printJson($htmlOptions);
	}
	/**
	 *Updated By Mansee @ 29-12-2021 503,504,511,512
	 */
    public function invoice_pdf_old()
	{
		$postData = $this->input->post();
		$original=0;$duplicate=0;$triplicate=0;$header_footer=0;$extra_copy=0;
		if(isset($postData['original'])){$original=1;}
		if(isset($postData['duplicate'])){$duplicate=1;}
		if(isset($postData['triplicate'])){$triplicate=1;}
		if(isset($postData['header_footer'])){$header_footer=1;}
		if(!empty($postData['extra_copy'])){$extra_copy=$postData['extra_copy'];}
		
		$sales_id=$postData['printsid'];
		$salesData = $this->jobworkScrapInvoice->getInvoice($sales_id);
		$companyData = $this->jobworkScrapInvoice->getCompanyInfo();
		
		$partyData = $this->party->getParty($salesData->party_id);
		
		$response="";
		$letter_head=base_url('assets/images/letterhead_top.png');
		
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
			$blankLines=17 - count($tc);if(!empty($header_footer)){$blankLines=17 - count($tc);}
			foreach($tc as $trms):
				if($t==0):
					$terms .= '<tr>
									<th style="width:17%;font-size:12px;text-align:left;">'.$trms->term_title.'</th>
									<td style="width:48%;font-size:12px;">: '.$trms->condition.'</td>
									<th rowspan="'.count($tc).'" style="width:35%;vertical-align:bottom;text-align:center;font-size:1rem;padding:5px 2px;">
										For, '.$companyData->company_name.'<br>
										<!--<img src="'.$auth_sign.'" style="width:120px;">-->
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
		$totalItems = count($salesData->itemData);
		
		$lpr = $blankLines ;$pr1 = $blankLines + 6 ;
		$pageRow = $pr = ($totalItems > $lpr) ? $pr1 : $totalItems;
		$lastPageRow = (($totalItems % $lpr)==0) ? $lpr : ($totalItems % $lpr);
		$remainRow = $totalItems - $lastPageRow;
		$pageSection = round(($remainRow/$pageRow),2);
		$totalPage = (numberOfDecimals($pageSection)==0)? (int)$pageSection : (int)$pageSection + 1;
		for($x=0;$x<=$totalPage;$x++)
		{
			$page_qty = 0;$page_amount = 0;
			$pageItems = '';$pr = ($x==$totalPage) ? $totalItems - ($i-1) : $pr;
			$tempData = $this->jobworkScrapInvoice->salesTransactions($sales_id,$pr.','.$pageCount);
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
		}
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
								<b>Invoice No. : '.$salesData->trans_prefix.$salesData->trans_no.'</b>
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
							<td style="width:25%;font-size:12px;">INV No. & Date : '.$salesData->trans_prefix.$salesData->trans_no.'-'.formatDate($salesData->trans_date).'</td>
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
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[1].$baseDetail.$pg.'</div></div>');
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
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[2].$baseDetail.$pg.'</div></div>');
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
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[3].$baseDetail.$pg.'</div></div>');
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
	
   
	public function invoice_pdf()
	{
		$postData = $this->input->post();
		$original = 0;
		$duplicate = 0;
		$triplicate = 0;
		$header_footer = 0;
		$extra_copy = 0;
		if (isset($postData['original'])) {
			$original = 1;
		}
		if (isset($postData['duplicate'])) {
			$duplicate = 1;
		}
		if (isset($postData['triplicate'])) {
			$triplicate = 1;
		}
		if (isset($postData['header_footer'])) {
			$header_footer = 1;
		}
		if (!empty($postData['extra_copy'])) {
			$extra_copy = $postData['extra_copy'];
		}

		$sales_id = $postData['printsid'];
		$salesData = $this->jobworkScrapInvoice->getInvoice($sales_id);
		$companyData = $this->jobworkScrapInvoice->getCompanyInfo();

		$partyData = $this->party->getParty($salesData->party_id);

		$response = "";
		$letter_head = base_url('assets/images/letterhead_top.png');

		$currencyCode = "INR";
		$symbol = "";

		$response = "";
		$inrSymbol = base_url('assets/images/inr.png');
		$headerImg = base_url('assets/images/rtth_lh_header.png');
		$footerImg = base_url('assets/images/rtth_lh_footer.png');
		$logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
		$logo = base_url('assets/images/' . $logoFile);
		$auth_sign = base_url('assets/images/rtth_sign.png');

		$gstHCol = '';
		$gstCol = '';
		$blankTD = '';
		$bottomCols = 2;
		$GSTAMT = $salesData->igst_amount;
		$subTotal = $salesData->taxable_amount;
		$itemList = '<table class="table table-bordered poItemList">
					<thead><tr class="text-center">
						<th style="width:6%;">Sr.No.</th>
						<th class="text-left">Description of Goods</th>
						<th style="width:10%;">HSN/SAC</th>
						<th style="width:10%;">Qty</th>
						<th style="width:10%;">Rate<br><small>(' . $partyData->currency . ')</small></th>
						<th style="width:6%;">Disc.</th>
						<th style="width:8%;">GST</th>
						<th style="width:11%;">Amount<br><small>(' . $partyData->currency . ')</small></th>
					</tr></thead><tbody>';

		// Terms & Conditions

		$blankLines = 10;
		if (!empty($header_footer)) {
			$blankLines = 10;
		}
		$terms = '<table class="table">';
		$t = 0;
		$tc = new StdClass;
		if (!empty($salesData->terms_conditions)) {
			$tc = json_decode($salesData->terms_conditions);
			$blankLines = 12 - count($tc);
			if (!empty($header_footer)) {
				$blankLines = 12 - count($tc);
			}
			
			foreach ($tc as $trms) :
				if ($t == 0) :
					$terms .= '<tr>
									<th style="font-size:12px;text-align:left;">' . $trms->term_title . '</th>
									<td style="font-size:12px;">: ' . $trms->condition . '</td>
							</tr>';
				else :
					$terms .= '<tr>
									<th style="font-size:12px;text-align:left;">' . $trms->term_title . '</th>
									<td style="font-size:12px;text-align:justify;">: ' . $trms->condition . '</td>
							</tr>';
				endif;
				$t++;
			endforeach;
		} else {
			$tc = array();
			/*$terms .= '<tr>
							<th style="font-size:12px;text-align:left;">Jurisdiction : </th>
							<td font-size:12px;">Subject to RAJKOT Jurisdiction</td>
					</tr>';*/
		}

		$terms .= '<tr>
							<th colspan="2" style="vertical-align:bottom;text-align:right;font-size:1rem;padding:5px 2px;">
								For, ' . $companyData->company_name . '<br>
								<!--<img src="' . $auth_sign . '" style="width:120px;">-->
							</th>
					</tr></table>';

		$subTotal = 0;
		$lastPageItems = '';
		$pageCount = 0;
		$i = 1;
		$tamt = 0;
		$cgst = 9;
		$sgst = 9;
		$cgst_amt = 0;
		$sgst_amt = 0;
		$netamt = 0;
		$igst = 0;
		$hsnCode = '';
		$total_qty = 0;
		$page_qty = 0;
		$page_amount = 0;
		$pageData = array();
		$totalPage = 0;
		$totalItems = count($salesData->itemData);

		$lpr = $blankLines;
		$pr1 = $blankLines + 6;
		$pageRow = $pr = ($totalItems > $lpr) ? $pr1 : $totalItems;
		$lastPageRow = (($totalItems % $lpr) == 0) ? $lpr : ($totalItems % $lpr);
		$remainRow = $totalItems - $lastPageRow;
		$pageSection = round(($remainRow / $pageRow), 2);
		$totalPage = (numberOfDecimals($pageSection) == 0) ? (int)$pageSection : (int)$pageSection + 1;
		for ($x = 0; $x <= $totalPage; $x++) {
			$page_qty = 0;
			$page_amount = 0;
			$pageItems = '';
			$pr = ($x == $totalPage) ? $totalItems - ($i - 1) : $pr;
			$tempData = $this->jobworkScrapInvoice->salesTransactions($sales_id, $pr . ',' . $pageCount);
			$maxGSTPer =0;
			if (!empty($tempData)) {
				$maxGSTPer = max(array_column($tempData,'gst_per'));
				foreach ($tempData as $row) {
					$pageItems .= '<tr>';
					$pageItems .= '<td class="text-center" height="30">' . $i . '</td>';
					$pageItems .= '<td class="text-left">' . $row->item_name . '</td>';
					$pageItems .= '<td class="text-center">' . $row->hsn_code . '</td>';
					$pageItems .= '<td class="text-center">' . sprintf('%0.2f', $row->qty) . '</td>';
					$pageItems .= '<td class="text-right">' . sprintf('%0.2f', $row->price) . '</td>';
					$pageItems .= '<td class="text-center">' . floatval($row->disc_per) . '</td>';
					$pageItems .= '<td class="text-center">' . floatval($row->gst_per) . '%</td>';
					$pageItems .= '<td class="text-right">' . sprintf('%0.2f', $row->amount) . '</td>';
					$pageItems .= '</tr>';

					$total_qty += $row->qty;
					$page_qty += $row->qty;
					$page_amount += $row->amount;
					$subTotal += $row->amount;
					$i++;
				}
			}
			if ($x == $totalPage) {
				$pageData[$x] = '';
				$lastPageItems = $pageItems;
			} else {
				/*$pageItems.='<tr>';
					$pageItems.='<th class="text-right" style="border:1px solid #000;" colspan="5">Page Total</th>';
					$pageItems.='<th class="text-right" style="border-top:1px solid #000000;">'.sprintf('%0.3f', $page_qty).'</th>';
					$pageItems.='<th class="text-right" style="border-top:1px solid #000000;"></th>';
					$pageItems.='<th class="text-right" style="border-top:1px solid #000000;">'.sprintf('%0.2f', $page_amount).'</th>';
				$pageItems.='</tr>';*/
				$pageData[$x] = $itemList . $pageItems . '</tbody></table><div class="text-right"><i>Continue to Next Page</i></div>';
			}
			$pageCount += $pageRow;
		}
		$taxableAmt = $subTotal;
		$fgst = round(($salesData->freight_gst / 2), 2);
		$rwspan = 4;

		$gstRow = '<tr>';
		$gstRow .= '<td colspan="3" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">CGST</td>';
		$gstRow .= '<td class="text-right" style="border-top:0px !important;">' . sprintf('%0.2f', ($salesData->cgst_amount + $fgst)) . '</td>';
		$gstRow .= '</tr>';

		$gstRow .= '<tr>';
		$gstRow .= '<td colspan="3" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">SGST</td>';
		$gstRow .= '<td class="text-right" style="border-top:0px !important;">' . sprintf('%0.2f', ($salesData->sgst_amount + $fgst)) . '</td>';
		$gstRow .= '</tr>';

		//$party_gstin = (!empty($salesData->party_state_code)) ? explode('#',$salesData->party_state_code)[0] : '';
		//$party_stateCode = (!empty($salesData->party_state_code)) ? explode('#',$salesData->party_state_code)[1] : '';

		// $party_gstin = (!empty($salesData->gstin)) ? explode('#', $salesData->gstin) : '';
		// $party_stateCode = (!empty($salesData->party_state_code)) ? explode('#', $salesData->party_state_code) : '';

		// if (!empty($party_gstin)) {
		// 	if ($party_stateCode != "24") {
		// 		$gstRow = '<tr>';
		// 		$gstRow .= '<td colspan="3" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">IGST</td>';
		// 		$gstRow .= '<td class="text-right" style="border-top:0px !important;">' . sprintf('%0.2f', ($salesData->cgst_amount + $salesData->sgst_amount + $salesData->freight_gst)) . '</td>';
		// 		$gstRow .= '</tr>';
		// 		$rwspan = 3;
		// 	}
		// }
		$totalCols = 9;
		$itemList .= $lastPageItems;
		if ($i < $blankLines) {
			for ($z = $i; $z <= $blankLines; $z++) {
				$itemList .= '<tr><td  height="30">&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
			}
		}


		$beforExp = "";
		$afterExp = "";
		$tax = "";
		$expenseList = $this->expenseMaster->getActiveExpenseList(2);
		$taxList = $this->taxMaster->getActiveTaxList(2);
		$invExpenseData = (!empty($salesData->expenseData)) ? $salesData->expenseData : array();
		$rowCount = 0;
		$maxGSTPer = ($salesData->gst_type != 3 && $maxGSTPer > 0)?" (".round($maxGSTPer,2)."%)":"";
		foreach ($expenseList as $row) {
			$expAmt = 0;
			$amtFiledName = $row->map_code . "_amount";
			if (!empty($invExpenseData) && $row->map_code != "roff") :
				$expAmt = $invExpenseData->{$amtFiledName};
			endif;
			if ($expAmt > 0) {
				if ($row->position == 1) {
					$beforExp .= '<tr>
									<td colspan="3" class="text-right" style="border-top:1px solid #000;border-right:1px solid #000;">' . $row->exp_name.$maxGSTPer . '</td>
									<td class="text-right" style="border-top:1px solid #000;border-left:0px solid #000;">' . $expAmt . ' </td>
								</tr>';
				} else {
					$afterExp .= '<tr>
									<td colspan="3" class="text-right" style="border-top:1px solid #000;border-right:1px solid #000;">' . $row->exp_name . '</td>
									<td class="text-right" style="border-top:1px solid #000;border-left:0px solid #000;">' . $expAmt . '</td>
								</tr>';
				}

				$rowCount++;
			}
		}
		foreach ($taxList as $taxRow) :
			$taxAmt = 0;
			if (!empty($salesData->id)) :
				$taxAmt = $salesData->{$taxRow->map_code . '_amount'};
			endif;
			if ($taxAmt > 0) :
				$tax .= '<tr>
				<td colspan="3" class="text-right" style="border-top:1px solid #000;border-right:1px solid #000;">' . $taxRow->name . '</td>';
				$tax .= '<td class="text-right" style="border-top:1px solid #000;border-left:0px solid #000;">' . $taxAmt . '</td></tr>';
				$rowCount++;
			endif;
		endforeach;

		$itemList .= '<tr>';
		$itemList .= '<td colspan="3" class="text-right" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Total Qty</b></td>';
		$itemList .= '<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">' . sprintf('%0.2f', $total_qty) . '</th>';
		$itemList .= '<th colspan="3" class="text-right" style="border:1px solid #000;border-left:0px solid #000;">Sub Total</th>';
		$itemList .= '<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">' . sprintf('%0.2f', $subTotal) . '</th>';
		$itemList .= '</tr>';

		$itemList .= '<tr>';
		$itemList .= '<td colspan="4" rowspan="' . ($rowCount + 2) . '" class="text-left" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Bank Name : </b>' . $companyData->company_bank_name . '<br>
			<b>A/c. No. : </b>' . $companyData->company_acc_no . '<br>
			<b>IFSC Code : </b>' . $companyData->company_ifsc_code . '
			</td>';
		$itemList .= $beforExp;
		$itemList .= '<tr><th colspan="3" class="text-right" style="border-top:1px solid #000;border-left:0px solid #000;">Taxable Amount</th>';
		$itemList .= '<th class="text-right" style="border-top:1px solid #000;border-left:0px solid #000;">' . sprintf('%0.2f', $salesData->taxable_amount) . '</th></tr>';
		$itemList .= $tax;
		$itemList .= $afterExp;
		$itemList .= '</tr>';


		$itemList .= '<tr>';
		$itemList .= '<td colspan="4"  class="text-left" style="vartical-align:top;border:1px solid #000;border-left:0px;"><i><b>Bill Amount In Words (' . $partyData->currency . ') : </b>' . numToWordEnglish($salesData->net_amount) . '</i></td>';
		$itemList .= '<th colspan="3" class="text-right" style="border-top:1px solid #000;border-right:1px solid #000;font-size:14px;">Payable Amount</th>';
		$itemList .= '<th class="text-right" height="40" style="border-top:1px solid #000;border-left:0px;font-size:14px;">' . sprintf('%0.2f', $salesData->net_amount) . '</th>';
		$itemList .= '</tr>';
		$itemList .= '<tbody></table>';

		$pageData[$totalPage] .= $itemList;
		$pageData[$totalPage] .= '<br><b><u>Terms & Conditions : </u></b><br>' . $terms . '';

		$invoiceType = array();
		$invType = array("ORIGINAL", "DUPLICATE", "TRIPLICATE", "EXTRA COPY");
		$i = 0;
		foreach ($invType as $it) {
			$invoiceType[$i++] = '<table style="margin-bottom:5px;">
									<tr>
										<th style="width:35%;letter-spacing:2px;" class="text-left fs-15 text-white" >GSTIN: ' . $companyData->company_gst_no . '</th>
										<th style="width:30%;letter-spacing:2px;" class="text-center fs-15 text-white">TAX INVOICE</th>
										<th style="width:35%;letter-spacing:2px;" class="text-right fs-15 text-white">' . $it . '</th>
									</tr>
								</table>';
		}
		$gstJson = json_decode($partyData->json_data);
		$partyAddress = (!empty($gstJson->{$salesData->gstin}) ? $gstJson->{$salesData->gstin} : '');
		$baseDetail = '<table class="poTopTable" style="margin-bottom:5px;">
						<tr>
							<td style="width:55%;" rowspan="3">
								<table>
									<tr><td style="vartical-align:top;"><b>BILL TO</b></td></tr>
									<tr><td style="vertical-align:top;"><b>' . $salesData->party_name . '</b></td></tr>
									<tr><td class="text-left" style="">' . (!empty($partyAddress->party_address) ? $partyAddress->party_address : '') . '</td></tr>
									<tr><td class="text-left" style=""><b>GSTIN : ' . $salesData->gstin . '</b></td></tr>
								</table>
							</td>
							<td style="width:25%;border-bottom:1px solid #000000;border-right:0px;padding:2px;">
								<b>Invoice No. : ' . $salesData->trans_prefix . $salesData->trans_no . '</b>
							</td>
							<td style="width:20%;border-bottom:1px solid #000000;border-left:0px;text-align:right;padding:2px 5px;">
								<b>Date : ' . date('d/m/Y', strtotime($salesData->trans_date)) . '</b>
							</td>
						</tr>
						<tr>
							<td style="width:45%;" colspan="2">
								<table>
									<tr><td style="vertical-align:top;width:75px;"><b>P.O. No.</b></td><td>: ' . $salesData->doc_no . '</td></tr>
									<tr><td style="vertical-align:top;"><b>Challan No</b></td><td>: ' . $salesData->challan_no . '</td></tr>
									<tr><td style="vertical-align:top;"><b>Transport</b></td><td>: ' . $salesData->transport_name . '</td></tr>
								</table>
							</td>
						</tr>
					</table>';

		$orsp = '';
		$drsp = '';
		$trsp = '';
		$htmlHeader = '<img src="' . $letter_head . '">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;font-size:12px;">INV No. & Date : ' . $salesData->trans_prefix . $salesData->trans_no . '-' . formatDate($salesData->trans_date) . '</td>
							<td style="width:25%;font-size:12px;"></td>
							<td style="width:25%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		//$mpdf = $this->m_pdf->load();
		$i = 1;
		$p = 'P';
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName = base_url('assets/uploads/sales/sales_invoice_' . $sales_id . '.pdf');
		$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v=' . time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));

		$fpath = '/assets/uploads/sales/sales_invoice_' . $sales_id . '.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/bill_style.css'));
		$mpdf->WriteHTML($stylesheet, 1);
		$mpdf->SetDisplayMode('fullpage');

		if (!empty($header_footer)) {
			$mpdf->SetWatermarkImage($logo, 0.08, array(120, 120),array(48,68));
			$mpdf->showWatermarkImage = true;
			$mpdf->SetHTMLHeader($htmlHeader);
			$mpdf->SetHTMLFooter($htmlFooter);
		}

		if (!empty($original)) {
			foreach ($pageData as $pg) {
				if (!empty($header_footer)) {
					$mpdf->AddPage('P', '', '', '', '', 5, 5, 54, 5, 3, 3, '', '', '', '', '', '', '', '', '', 'A4-P');
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv1">' . $invoiceType[0] . $baseDetail . $pg . '</div></div>');
				} else {
					$mpdf->AddPage('P', '', '', '', '', 5, 5, 54, 5, 3, 3, '', '', '', '', '', '', '', '', '', 'A4-P');
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">' . $invoiceType[0] . $baseDetail . $pg . '</div></div>');
				}
			}
		}

		if (!empty($duplicate)) {
			foreach ($pageData as $pg) {
				if (!empty($header_footer)) {
					$mpdf->AddPage('P', '', '', '', '', 5, 5, 54, 5, 3, 3, '', '', '', '', '', '', '', '', '', 'A4-P');
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">' . $invoiceType[1] . $baseDetail . $pg . '</div></div>');
				} else {
					$mpdf->AddPage('P', '', '', '', '', 5, 5, 54, 5, 3, 3, '', '', '', '', '', '', '', '', '', 'A4-P');
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">' . $invoiceType[1] . $baseDetail . $pg . '</div></div>');
				}
			}
		}

		if (!empty($triplicate)) {
			foreach ($pageData as $pg) {
				if (!empty($header_footer)) {
					$mpdf->AddPage('P', '', '', '', '', 5, 5, 54, 5, 3, 3, '', '', '', '', '', '', '', '', '', 'A4-P');
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">' . $invoiceType[2] . $baseDetail . $pg . '</div></div>');
				} else {
					$mpdf->AddPage('P', '', '', '', '', 5, 5, 54, 5, 3, 3, '', '', '', '', '', '', '', '', '', 'A4-P');
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">' . $invoiceType[2] . $baseDetail . $pg . '</div></div>');
				}
			}
		}

		for ($x = 0; $x < $extra_copy; $x++) {
			foreach ($pageData as $pg) {
				if (!empty($header_footer)) {
					$mpdf->AddPage('P', '', '', '', '', 5, 5, 54, 5, 3, 3, '', '', '', '', '', '', '', '', '', 'A4-P');
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">' . $invoiceType[3] . $baseDetail . $pg . '</div></div>');
				} else {
					$mpdf->AddPage('P', '', '', '', '', 5, 5, 54, 5, 3, 3, '', '', '', '', '', '', '', '', '', 'A4-P');
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">' . $invoiceType[3] . $baseDetail . $pg . '</div></div>');
				}
			}
		}

		$mpdf->Output($pdfFileName, 'I');
	}

	public function getItemList(){
        $this->printJson($this->jobworkScrapInvoice->getItemList($this->input->post('id')));
    }

	public function copyInvoice($id){
		$this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = "";
		$this->data['invoiceData'] = $this->jobworkScrapInvoice->getInvoice($id);
		$this->data['invoiceData']->trans_prefix = $this->transModel->getTransPrefix(6);
        $this->data['invoiceData']->trans_no = $this->transModel->nextTransNo(6);
        $this->data['invoiceData']->id = "";
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsList();
		$this->data['invMaster'] = $this->party->getParty($this->data['invoiceData']->party_id);  
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
		$this->data['spAccounts'] = $this->ledger->getLedgerList(["'SA'"]); 
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'","'ED'","'EI'","'ID'","'II'"]);
        $this->load->view($this->invoiceForm,$this->data);
	}

}
?>