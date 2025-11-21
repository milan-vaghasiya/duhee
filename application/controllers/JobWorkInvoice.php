<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class JobWorkInvoice extends MY_Controller
{
    private $indexPage = "jobwork_invoice/index";
    private $invoiceForm = "jobwork_invoice/form";
    
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "JobWork Invoice";
		$this->data['headData']->controller = "jobWorkInvoice";
		$this->data['headData']->pageUrl = "jobWorkInvoice";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
    
    //Changed By Karmi @11/05/2022
    public function getDTRows(){
        $columns =array('','','trans_main.inv_no','trans_main.inv_date','trans_main.party_name','trans_main.net_amount');
        $result = $this->jobWorkInvoice->getDTRows($this->input->post(),$columns);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getJobWorkInvoiceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	
	public function addJobWorkInvoice(){
		$this->data['ref_id']= '';
		$this->data['trans_prefix'] = $this->transModel->getTransPrefix(19);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(19);
        $this->data['vendorList']=$this->party->getVendorList();
		$this->data['itemData'] = $this->item->getItemList(0);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(1);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(1);
		//$this->data['spAccounts'] = $this->ledger->getLedgerList(["'PA'"]);
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'","'ED'","'EI'","'ID'","'II'"]);
		$this->data['terms'] = $this->terms->getTermsList();
		$this->load->view($this->invoiceForm,$this->data);
	}
	
    //Changed By Karmi @02/07/2022
    public function getVendorJobWork(){
		$data = $this->input->post();
		$this->printJson( $this->jobWorkInvoice->getVendorJobWork($data));      
	}
	
	public function createInvoice(){
		$data = $this->input->post();$data = $this->input->post(); 
		$this->data['ref_id'] = implode(",",$data['ref_id']); 
		$orderItems = $this->jobWorkInvoice->getJobworkItemData($this->data['ref_id']); 
		//print_r($orderItems);exit;
			
		$orderData = new stdClass();
		$orderData->party_id = $data['party_id'];
		$orderData->id = implode(",",$data['ref_id']);
		$this->data['gst_type'] = $this->party->getParty($data['party_id'])->gst_type;
		//$orderItems['gst_type'] = $this->data['gst_type'];
		$this->data['orderItems'] = $orderItems;
		$this->data['orderData'] = $orderData;
		$this->data['ref_id']= $orderData->id;
		$this->data['party_id'] = $data['party_id'];
		$this->data['party_name'] = $data['party_name'];
		$this->data['terms'] = $this->terms->getTermsList();
		$this->data['trans_prefix'] = $this->transModel->getTransPrefix(19);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(19);
		$this->data['itemData'] = $this->item->getItemList(0);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['hsnData'] = $this->hsnModel->getHSNList();
        $this->data['vendorList']=$this->party->getVendorList();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(1);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(1);
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'","'ED'","'EI'","'ID'","'II'"]);
		$this->load->view($this->invoiceForm,$this->data);
	}
	
	public function getItemData(){
		$id = $this->input->post('itemId');
        $result = $this->item->getItem($id);
        $result->unit_name = $this->item->itemUnit($result->unit_id)->unit_name;
		$this->printJson($result);
    }
    
	public function savePurchaseInvoice(){
		$data=$this->input->post();// print_r($data);exit;
		$errorMessage = array();
        $data['currency'] = '';$data['inrrate'] = 0;
        if(empty($data['party_id'])):
            $errorMessage['party_id'] = "Party Name is required.";
		else:
			$partyData = $this->party->getParty($data['party_id']); 
			// if(floatval($partyData->inrrate) <= 0):
			// 	$errorMessage['party_id'] = "Currency not set.";
			// else:
			// 	$data['currency'] = $partyData->currency;
			// 	$data['inrrate'] = $partyData->inrrate;
			// endif;
		endif;
		/* if(empty($data['sp_acc_id']))
			$errorMessage['sp_acc_id'] = "Purchase A/c. is required."; */
        if(empty($data['inv_date']))
            $errorMessage['inv_date'] = 'Date is required.';
        if(empty($data['item_id'][0]))
            $errorMessage['item_id'] = 'Item Name is required.';
		/* if(empty($data['term_id'][0]))
			$errorMessage['term_id'] = "Terms Conditions is required."; */
			
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
				'id' => $data['id'],
				'entry_type' => $data['entry_type'],
				'from_entry_type' => $data['reference_entry_type'],
				'ref_id' => $data['reference_id'],
				'trans_no' => $data['inv_no'], 
				'trans_prefix' => $data['inv_prefix'],
				'trans_number' => getPrefixNumber($data['inv_prefix'],$data['inv_no']),
				'trans_date' => date('Y-m-d',strtotime($data['inv_date'])), 
				'party_id' => $data['party_id'],
				'opp_acc_id' => $data['party_id'],
				'sales_type' => $data['sales_type'],
				'party_name' => $data['party_name'],
				'party_state_code' => $data['party_state_code'],
				'gstin' => $data['gstin'],
				'gst_applicable' => $data['gst_applicable'],
				'gst_type' => $data['gst_type'], 
				'doc_no'=>$data['doc_no'],
				'doc_date'=>date('Y-m-d',strtotime($data['inv_date'])),
				'challan_no'=>$data['challan_no'],
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
				'net_amount' => $data['net_amount'],
			];
			$this->printJson($this->jobWorkInvoice->save($masterData,$itemData,$expenseData));
		endif;
	}
	
	public function edit($id){
		$this->data['invoiceData'] = $this->jobWorkInvoice->getInvoice($id);
		$this->data['itemData'] = $this->item->getItemList(0);
		$this->data['unitData'] = $this->item->itemUnits();
		$this->data['hsnData'] = $this->hsnModel->getHSNList();
		$this->data['vendorList']=$this->party->getVendorList();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsList();
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(1);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(1);
		$this->data['spAccounts'] = $this->ledger->getLedgerList(["'PA'"]);
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'","'ED'","'EI'","'ID'","'II'"]);		
		$this->load->view($this->invoiceForm,$this->data);	
	}
	
	public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
		else:
			$this->printJson($this->jobWorkInvoice->delete($id));
		endif;
	}
	
	public function getItemList(){
        $this->printJson($this->jobWorkInvoice->getItemList($this->input->post('id')));
    }
    
    //Created By Karmi @02/07/2022
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
		$salesData = $this->jobWorkInvoice->getInvoice($sales_id);
		$companyData = $this->jobWorkInvoice->getCompanyInfo();

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
						<th style="width:7%;">SR.NO.</th>
						<th class="text-left">A. CODE</th>
						<th class="text-left">PARTICULARS</th>
						<th style="width:10%;">HSN CODE</th>
						<th style="width:10%;">QTY</th>
						<th style="width:10%;">UNIT</th>
						<th style="width:10%;">RATE</th>
						<th style="width:11%;">AMOUNT</th>
					</tr></thead><tbody>';

		// Terms & Conditions

		$blankLines = 10;
		if (!empty($header_footer)) {$blankLines = 10;}
		$terms = '<table class="table">';
		$t = 0;
		$tc = new StdClass;
		if (!empty($salesData->terms_conditions)) {
			$tc = json_decode($salesData->terms_conditions);
			$blankLines = 12 - count($tc);
			if (!empty($header_footer)) {$blankLines = 12 - count($tc);}
			
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
					</tr>
					<tr>
						<td colspan="2" height="35"></td>
					</tr>
					<tr>
						<td colspan="2" style="vertical-align:bottom;text-align:right;font-size:1rem;padding:5px 2px;"><b>Authorised Signature</b></td>
					</tr></table>';

		//$totalPage = 0;
		//$totalItems = count($salesData->itemData);
		
		$subTotal=0;$lastPageItems = '';$pageCount = 0; $i=1;
		$tamt=0;$cgst=9;$sgst=9;$cgst_amt=0;$sgst_amt=0;$netamt=0;$igst=0;$hsnCode='';$total_qty=0;$page_qty = 0;$page_amount = 0;
		$pageData = array();
		
		$itmLine=18;if(!empty($header_footer)){$itmLine=18;}
		$orderData = $salesData->itemData;
		
		$totalItems = count($orderData);
		$firstArr = $orderData;$secondArr = Array();$lastPageRow = $totalItems;$pagedArray = Array();$rowPerPage = $itmLine;
		if($totalItems > $itmLine)
		{
			$rowPerPage = ($totalItems > $itmLine) ? $itmLine : $itmLine ;
			$lastPageRow = $totalItems % $rowPerPage;
			//$lastPageRow = $totalItems / $rowPerPage;
			$firstArr = array_slice($orderData,($totalItems - $lastPageRow),$lastPageRow);
			$secondArr = array_slice($orderData,0,($totalItems - $lastPageRow));
		}
		
		$pagedArray = array_chunk($secondArr,$rowPerPage);
		
		$pagedArray[] = $firstArr;
		$blankLines = $itmLine - $lastPageRow;
		
		$x=1;$totalPage = count($pagedArray);$i=1;$highestGst = 0;$itmGst = Array(); $challan_no = "";
		foreach($pagedArray as $tempData){
			$page_qty = 0;$page_amount = 0;$pageItems = '';
			$maxGSTPer =0;
			if (!empty($tempData)) {
				$maxGSTPer = max(array_column($tempData,'gst_per'));
				$jobChallanNo = array_column($tempData,'job_challan_no');
				$keys = array_keys($jobChallanNo,'0');
				foreach($jobChallanNo as $key=>$value): 
					if(in_array($key,$keys)): unset($key); endif;
				endforeach;
				$ch_no = array_unique($jobChallanNo);
				$challan_no = implode(", ",$ch_no);
				foreach ($tempData as $row) {
				    $itemCode = (!empty($row->item_code))?$row->item_code:'';
				    $pageItems .= '<tr>';
					$pageItems .= '<td class="text-center" height="30">' . $i . '</td>';
					$pageItems .= '<td class="text-left">' . $itemCode. '</td>';
					$pageItems .= '<td class="text-left">' . $row->item_name. '</td>';
					$pageItems .= '<td class="text-center">' . $row->hsn_code . '</td>';
					$pageItems .= '<td class="text-center">' . sprintf('%0.2f', $row->qty) . '</td>';
					$pageItems .= '<td class="text-center">' . $row->unit_name . '</td>';
					$pageItems .= '<td class="text-right">' . sprintf('%0.2f', $row->price) . '</td>';
					$pageItems .= '<td class="text-right">' . sprintf('%0.2f', $row->taxable_amount) . '</td>';
					$pageItems .= '</tr>';

					$total_qty += $row->qty;
					$page_qty += $row->qty;
					$page_amount += $row->taxable_amount;
					$subTotal += $row->taxable_amount;
					$i++;
				}
			}
			if ($x == $totalPage) {
				$pageData[$x-1] = '';
				$lastPageItems = $pageItems;
			} else {
				/*$pageItems.='<tr>';
					$pageItems.='<th class="text-right" style="border:1px solid #000;" colspan="5">Page Total</th>';
					$pageItems.='<th class="text-right" style="border-top:1px solid #000000;">'.sprintf('%0.3f', $page_qty).'</th>';
					$pageItems.='<th class="text-right" style="border-top:1px solid #000000;"></th>';
					$pageItems.='<th class="text-right" style="border-top:1px solid #000000;">'.sprintf('%0.2f', $page_amount).'</th>';
				$pageItems.='</tr>';*/
				$pageData[$x-1] = $itemList . $pageItems . '</tbody></table><div class="text-right"><i>Continue to Next Page</i></div>';
			}
			//$pageCount += $pageRow;
			$x++;
		}

		$taxableAmt = $subTotal;
		$fgst = round(($salesData->freight_gst / 2), 2);
		$rwspan = 4;

		$gstRow = '<tr>';
		$gstRow .= '<td colspan="2" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">CGST</td>';
		$gstRow .= '<td class="text-right" style="border-top:0px !important;">' . sprintf('%0.2f', ($salesData->cgst_amount + $fgst)) . '</td>';
		$gstRow .= '</tr>';

		$gstRow .= '<tr>';
		$gstRow .= '<td colspan="2" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">SGST</td>';
		$gstRow .= '<td class="text-right" style="border-top:0px !important;">' . sprintf('%0.2f', ($salesData->sgst_amount + $fgst)) . '</td>';
		$gstRow .= '</tr>';

		$totalCols = 9;
		$itemList .= $lastPageItems;
		if ($i < $blankLines) {
			for ($z = $i; $z <= $blankLines; $z++) {
				$itemList .= '<tr><td  height="30">&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
			}
		}

		$beforExp = "";
		$afterExp = "";
		$tax = "";
		$expenseList = $this->expenseMaster->getActiveExpenseList(2);
		$taxList = $this->taxMaster->getActiveTaxList(2);
		$invExpenseData = (!empty($salesData->expenseData)) ? $salesData->expenseData : array();
		$rowCount = 1;
		$maxGSTPerStr = ($salesData->gst_type != 3 && $maxGSTPer > 0)?" (".round($maxGSTPer,2)."%)":"";
		foreach ($expenseList as $row) {
			$expAmt = 0;
			$amtFiledName = $row->map_code . "_amount";
			if (!empty($invExpenseData) && $row->map_code != "roff") :
				$expAmt = $invExpenseData->{$amtFiledName};
			endif;
			if ($expAmt > 0) {
				if ($row->position == 1) {
					$beforExp .= '<tr>
									<td colspan="2" class="text-right" style="border-top:1px solid #000;border-right:1px solid #000;">' . $row->exp_name.$maxGSTPerStr . '</td>
									<td class="text-right" style="border-top:1px solid #000;border-left:0px solid #000;">' . $expAmt . ' </td>
								</tr>';
				} else {
					$afterExp .= '<tr>
									<td colspan="2" class="text-right" style="border-top:1px solid #000;border-right:1px solid #000;">' . $row->exp_name . '</td>
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
				$gstPer = $maxGSTPer;
				if($taxRow->map_code == 'sgst' OR $taxRow->map_code == 'cgst'){$gstPer = round(($maxGSTPer/2),2);}
				$tax .= '<tr>
				<td colspan="2" class="text-right" style="border-top:1px solid #000;border-right:1px solid #000;">' . $taxRow->name . ' '.$gstPer.' %</td>';
				$tax .= '<td class="text-right" style="border-top:1px solid #000;border-left:0px solid #000;">' . $taxAmt . '</td></tr>';
				$rowCount++;
			endif;
		endforeach;

		$itemList .= '<tr>';
		$itemList .= '<td colspan="4" class="text-right" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Total Qty</b></td>';
		$itemList .= '<th class="text-right" style="border-top:1px solid #000;border-left:0px solid #000;">' . sprintf('%0.2f', $total_qty) . '</th>';
		$itemList .= '<th colspan="2" rowspan="2" class="text-right" style="border-top:1px solid #000;border-left:0px solid #000;">Taxable Amount</th>';
		$itemList .= '<th class="text-right" rowspan="2" style="border-top:1px solid #000;border-left:0px solid #000;">' . sprintf('%0.2f', $salesData->taxable_amount) . '</th>';
		$itemList .= '</tr>';

		$itemList .= '<tr>';
		$itemList .= '<td colspan="5" rowspan="' . ($rowCount+1) . '" class="text-left" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Bank Name : </b>' . $companyData->company_bank_name . '<br>
			<b>A/c. No. : </b>' . $companyData->company_acc_no . '<br>
			<b>IFSC Code : </b>' . $companyData->company_ifsc_code . '<br>
			<b>Branch : </b>' . $companyData->company_bank_branch . '
			</td>';
		$itemList .= '</tr>';
		$itemList .= $beforExp;
		
		$itemList .= $tax;
		$itemList .= $afterExp;
		$itemList .= '<tr><td colspan="2" class="text-right" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;">Round Off</td>';
		$itemList .= '<td class="text-right" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;">' . sprintf('%0.2f', $salesData->round_off_amount) . '</td></tr>';


		$itemList .= '<tr>';
		$itemList .= '<td colspan="5"  class="text-left" style="vartical-align:top;border:1px solid #000;border-left:0px;"><i><b>Bill Amount In Words (' . $partyData->currency . ') : </b>' . numToWordEnglish($salesData->net_amount) . '</i></td>';
		$itemList .= '<th colspan="2" class="text-right" style="border-top:1px solid #000;border-right:1px solid #000;font-size:13px;">Payable Amount</th>';
		$itemList .= '<th class="text-right" height="40" style="border-top:1px solid #000;border-left:0px;font-size:14px;">' . sprintf('%0.2f', $salesData->net_amount) . '</th>';
		$itemList .= '</tr>';
		$itemList .= '<tbody></table>';
		
		$pageData[$totalPage-1] .= $itemList;
		$pageData[$totalPage-1] .= '<b><u>Terms & Conditions : </u></b><br>' . $terms . '';

		$invoiceType = array();
		$invType = array("ORIGINAL", "DUPLICATE", "TRIPLICATE", "EXTRA COPY");
		$i = 0;
		foreach ($invType as $it) {
			$invoiceType[$i++] = '<table style="margin-bottom:5px;">
									<tr>
										<th style="width:35%;letter-spacing:2px;" class="text-left fs-15 text-black" >GSTIN: ' . $companyData->company_gst_no . '</th>
										<th style="width:30%;letter-spacing:2px;" class="text-center fs-15 text-black">JOBWORK INVOICE</th>
										<th style="width:35%;letter-spacing:2px;" class="text-right fs-15 text-black">' . $it . '</th>
									</tr>
								</table>';
		}
		$gstJson = json_decode($partyData->json_data);
		$partyAddress = (!empty($gstJson->{$salesData->gstin}) ? $gstJson->{$salesData->gstin} : '');
		$baseDetail = '<table class="poTopTable" style="margin-bottom:5px;">
						<tr>
							<td style="width:60%;">
								<table>
									<tr><td style="vartical-align:top;"><b>BILL TO</b></td></tr>
									<tr><td style="vertical-align:top;"><b>' . $salesData->party_name . '</b></td></tr>
									<tr><td class="text-left" style="">' . (!empty($partyAddress->party_address) ? $partyAddress->party_address : '') . '</td></tr>
									<tr><td class="text-left" style=""><b>GSTIN : ' . $salesData->gstin . '</b></td></tr>
								</table>
							</td>
							<td style="width:40%;" colspan="3">
								<table>
									<tr><td style="vertical-align:top;"><b>Invoice No.</b></td><td>: ' . $salesData->doc_no . '</td></tr>
									<tr><td style="vertical-align:top;"><b>Invoice Date</b></td><td>: ' .  date('d/m/Y', strtotime($salesData->trans_date)). '</td></tr>
									<tr><td style="vertical-align:top;"><b>Challan No</b></td><td>: ' . $challan_no . '</td></tr>
								</table>
							</td>
						</tr>
					</table>';

		$orsp = '';
		$drsp = '';
		$trsp = '';
		$htmlHeader = '<img src="' . $letter_head . '">';
		$htmlFooter = '<table class="table top-table">
		                <tr>
		                    <td style="width:50%;font-size:12px;">This is computer generated invoice.</td>
		                    <td style="width:25%;font-size:12px;text-align:right;">E. & O.E.</td>
		                </tr>
		            </table>
		            <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;font-size:12px;">INV No. & Date : ' . $salesData->doc_no . '/' . formatDate($salesData->trans_date) . '</td>
							<td style="width:25%;font-size:12px;"></td>
							<td style="width:25%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';

		$i = 1;
		$p = 'P';
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName = base_url('assets/uploads/job_work/jinv_' . $sales_id . '.pdf');
		$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v=' . time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));

		$fpath = '/assets/uploads/job_work/jinv_' . $sales_id . '.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/bill_style.css'));
		$mpdf->WriteHTML($stylesheet, 1);
		$mpdf->SetDisplayMode('fullpage');

		if (!empty($header_footer)) {
			$mpdf->SetWatermarkImage($logo, 0.08, array(120, 60),array(48,68));
			$mpdf->showWatermarkImage = true;
			$mpdf->SetHTMLHeader($htmlHeader);
			$mpdf->SetHTMLFooter($htmlFooter);
		}

		if (!empty($original)) {
			foreach ($pageData as $pg) {
				if (!empty($header_footer)) {
					$mpdf->AddPage('P', '', '', '', '', 5, 5, 40, 5, 3, 3, '', '', '', '', '', '', '', '', '', 'A4-P');
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv1">' . $invoiceType[0] . $baseDetail . $pg . '</div></div>');
				} else {
					$mpdf->AddPage('P', '', '', '', '', 5, 5, 40, 5, 3, 3, '', '', '', '', '', '', '', '', '', 'A4-P');
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">' . $invoiceType[0] . $baseDetail . $pg . '</div></div>');
				}
			}
		}

		if (!empty($duplicate)) {
			foreach ($pageData as $pg) {
				if (!empty($header_footer)) {
					$mpdf->AddPage('P', '', '', '', '', 5, 5, 53, 5, 3, 3, '', '', '', '', '', '', '', '', '', 'A4-P');
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv1">' . $invoiceType[1] . $baseDetail . $pg . '</div></div>');
				} else {
					$mpdf->AddPage('P', '', '', '', '', 5, 5, 53, 5, 3, 3, '', '', '', '', '', '', '', '', '', 'A4-P');
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">' . $invoiceType[1] . $baseDetail . $pg . '</div></div>');
				}
			}
		}

		if (!empty($triplicate)) {
			foreach ($pageData as $pg) {
				if (!empty($header_footer)) {
					$mpdf->AddPage('P', '', '', '', '', 5, 5, 53, 5, 3, 3, '', '', '', '', '', '', '', '', '', 'A4-P');
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv1">' . $invoiceType[2] . $baseDetail . $pg . '</div></div>');
				} else {
					$mpdf->AddPage('P', '', '', '', '', 5, 5, 53, 5, 3, 3, '', '', '', '', '', '', '', '', '', 'A4-P');
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">' . $invoiceType[2] . $baseDetail . $pg . '</div></div>');
				}
			}
		}

		for ($x = 0; $x < $extra_copy; $x++) {
			foreach ($pageData as $pg) {
				if (!empty($header_footer)) {
					$mpdf->AddPage('P', '', '', '', '', 5, 5, 53, 5, 3, 3, '', '', '', '', '', '', '', '', '', 'A4-P');
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv1">' . $invoiceType[3] . $baseDetail . $pg . '</div></div>');
				} else {
					$mpdf->AddPage('P', '', '', '', '', 5, 5, 53, 5, 3, 3, '', '', '', '', '', '', '', '', '', 'A4-P');
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">' . $invoiceType[3] . $baseDetail . $pg . '</div></div>');
				}
			}
		}

		$mpdf->Output($pdfFileName, 'I');
	}
}
?>