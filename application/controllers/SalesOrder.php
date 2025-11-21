<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class SalesOrder extends MY_Controller
{
	private $indexPage = "sales_order/index";
	private $orderForm = "sales_order/form";
    private $orderView = "sales_order/view";
    private $closeView = "sales_order/close_view";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Sales Order";
		$this->data['headData']->controller = "salesOrder";
		$this->data['headData']->pageUrl = "salesOrder";
	}
	
	public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);    
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->salesOrder->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++; $row->ref_no = '';
			$row->pending_qty=$row->qty - $row->dispatch_qty;       
            if(empty($row->is_approve)):
				$row->order_status_label = '<span class="badge badge-pill badge-danger m-1">Unapproved</span>';
			else:	
			    $row->order_status_label = '<span class="badge badge-pill badge-success m-1">Approved</span><br>'.formatDate($row->approve_date);
			endif;
			if($row->trans_status == 2):
				$labelText = (!empty($row->close_date))?"Closed at ".date("d-m-Y",strtotime($row->close_date)):"Closed";
				$row->order_status_label = '<span class="badge badge-pill badge-dark m-1">'.$labelText.'</span><br>'.$row->close_reason;
			endif;
			if(!empty($row->from_entry_type)):
				$refData = $this->salesQuotation->getSalesQuotation($row->ref_id);
				$row->ref_no = getPrefixNumber($refData->trans_prefix,$refData->trans_no);
			endif;     
			
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getSalesOrderData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    //Created By Karmi @26/03/2022
    public function createOrder(){
		$data = $this->input->post();
		$soMaster = new stdClass();
        $soMaster = $this->party->getParty($data['party_id']);  
		$this->data['gst_type']  = (!empty($soMaster->gstin))?((substr($soMaster->gstin,0,2) == 24)?1:2):1;		
		$this->data['from_entry_type'] = $data['from_entry_type'];
		$this->data['ref_id'] = implode(",",$data['ref_id']);
		$soData = $this->salesQuotation->getQuoNOByRefid($data['ref_id']);
		$this->data['soTransNo'] = (!empty($soData->trans_number))?$soData->trans_number:"";
		
		if(!empty($data['qo_id'])):
			$orderData = $this->salesQuotation->getSalesQuotation($data['qo_id']);unset($orderData->itemData);
			$this->data['orderData'] = $orderData;
		endif; 
		$this->data['orderItems'] = $this->salesOrder->getSoItems($data['ref_id']);
		
        $this->data['trans_prefix'] = $this->transModel->getTransPrefix(4);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(4);
		$this->data['customerData'] = $this->party->getCustomerList();
		$this->data['itemData'] = $this->item->getItemList(1);
		$this->data['unitData'] = $this->item->itemUnits();        
		$this->data['terms'] = $this->terms->getTermsList();    
		$this->data['devCharge'] = $this->grnModel->getMasterOptions()->dev_charge; 
		
		$result = $this->party->getContactDetailForEdit($orderData);
        $orderData->contact_person = $result->person;
        $orderData->contact_no = $result->mobile;
        $orderData->contact_email = $result->email;
        $this->data['orderData'] = $orderData;
		$this->load->view($this->orderForm,$this->data);
	}
    
	public function createOrder_old($id){
		$this->data['from_entry_type'] = 2;
		$this->data['ref_id'] = $id;
        $this->data['trans_prefix'] = $this->transModel->getTransPrefix(4);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(4);
		$this->data['orderData'] = $this->salesQuotation->getSalesQuotation($id);
		$this->data['customerData'] = $this->party->getCustomerList();
		$this->data['itemData'] = $this->item->getItemList(1);
		$this->data['unitData'] = $this->item->itemUnits();        
		$this->data['terms'] = $this->terms->getTermsList();    
		$this->data['devCharge'] = 0;   
		$this->load->view($this->orderForm,$this->data);
	}

    public function addOrder(){
		$this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = "";
        $this->data['trans_prefix'] = $this->transModel->getTransPrefix(4);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(4);
		$this->data['customerData'] = $this->party->getCustomerList();
		$this->data['itemData'] = $this->item->getItemList(1);
		$this->data['unitData'] = $this->item->itemUnits(); 
		$this->data['terms'] = $this->terms->getTermsList();
		$this->data['currencyData'] = $this->party->getCurrency();   
		$this->data['ppapLevel'] = explode(',',$this->grnModel->getMasterOptions()->ppap_level);       
		$this->data['devCharge'] = 0;       
		$this->load->view($this->orderForm,$this->data);
	}

	public function save(){
		$data = $this->input->post();
		$errorMessage = array();
		$data['inrrate'] = 0;
		if(empty($data['party_id'])):
			$errorMessage['party_id'] = "Party name is required.";
		else:
		    if(empty($data['currency'])):
    			$partyData = $this->party->getParty($data['party_id']); 
    			if(floatval($partyData->inrrate) <= 0):
    				$errorMessage['party_id'] = "Currency not set.";
    			else:
    				$data['currency'] = $partyData->currency;
    				$data['inrrate'] = $partyData->inrrate;
    			endif;
    		endif;
		endif;
		if(empty($data['so_no']))
			$errorMessage['so_no'] = "SO. No. is required.";
		if(empty($data['item_id'][0]))
			$errorMessage['item_name_error'] = "Item name is required.";
		if(empty($data['delivery_date'][0]))
			$errorMessage['item_name_error'] = "Delivery Date is required.";
		if(empty($data['term_id'][0]))
			$errorMessage['term_id'] = "Terms Conditions is required.";

		if($data['sales_type'] == 2):
			if(empty($data['cust_po_no']))
				$errorMessage['cust_po_no'] = "Cust. PO. No. is required.";
			if(empty($data['cust_po_date']))
				$errorMessage['cust_po_date'] = "Cust. PO. Date is required.";
		endif;
		
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

			$masterData = [ 
				'id' => $data['order_id'],
				'entry_type' => $data['form_entry_type'],
				'from_entry_type' => $data['reference_entry_type'],
				'ref_id' => $data['reference_id'],
				'trans_prefix'=>$data['so_prefix'], 
				'trans_no'=>$data['so_no'], 
				'trans_date' => date('Y-m-d',strtotime($data['so_date'])), 
				'doc_no'=>$data['cust_po_no'], 
				'doc_date'=>date('Y-m-d',strtotime($data['cust_po_date'])), 
				'gst_type' => $data['gst_type'], 
				'gst_applicable' => $data['gst_applicable'],
				'party_id' => $data['party_id'],
				'party_name' => $data['party_name'],
				'party_state_code' => $data['party_state_code'],				
				'order_type' => $data['order_type'],
				'sales_type' => $data['sales_type'],
				'ref_by' => $data['reference_by'], 
				'total_amount' => ($data['amount_total'] + $data['disc_amt_total']),
				'taxable_amount' => $data['amount_total'],
				'gst_amount' => $data['igst_amt_total'],
				'freight_amount' => $data['freight_amt'],
				'igst_amount' => $data['igst_amt_total'], 
				'cgst_amount' => $data['cgst_amt_total'], 
				'sgst_amount' => $data['sgst_amt_total'], 
				'disc_amount' => $data['disc_amt_total'],
				'apply_round' => $data['apply_round'], 
				'round_off_amount' => $data['round_off'], 
				'net_amount' => $data['net_amount_total'],
				'terms_conditions' => $data['terms_conditions'],
				'challan_no' => $data['challan_no'],
				'net_weight' => $data['dev_charge'],
				'remark' => $data['remark'],
				'currency' => $data['currency'],
                'inrrate' => $data['inrrate'],
                'contact_person' => $data['contact_person'],
				'contact_no'=>$data['contact_no'],
				'contact_email'=>$data['contact_email'],
                'created_by' => $this->session->userdata('loginId')
			];
			
			$itemData = [
				'id' => $data['trans_id'],
				'from_entry_type' => $data['from_entry_type'],
				'ref_id' => $data['ref_id'],
				'item_id' => $data['item_id'],
				'item_name' => $data['item_name'],
				'item_type' => $data['item_type'],
				'item_code' => $data['item_code'],
				'item_desc' => $data['item_desc'],
				'item_alias' => $data['item_alias'],
				'unit_id' => $data['unit_id'],
				'unit_name' => $data['unit_name'],
				'hsn_code' => $data['hsn_code'],
				'drg_rev_no' => $data['drg_rev_no'],
				'qty' => $data['qty'],
				'qty_kg' => $data['qty_kg'],
				'price' => $data['price'],
				'cod_date' => $data['delivery_date'],
				'prod_target_date' => $data['prod_target_date'],
				'amount' => ($data['amount'] + $data['disc_amt']),
				'taxable_amount' => $data['amount'],
				'gst_per' => $data['gst_per'],
				'gst_amount' => $data['igst_amt'],
				'igst_per' => $data['igst'],
				'igst_amount' => $data['igst_amt'],
				'cgst_per' => $data['cgst'],
				'cgst_amount' => $data['cgst_amt'],
				'sgst_per' => $data['sgst'],
				'sgst_amount' => $data['sgst_amt'],
				'disc_per' => $data['disc_per'],
				'disc_amount' => $data['disc_amt'],
				'net_amount' => $data['total_amount'],
				'item_remark' => $data['item_remark']
			];
           
            if($_FILES['order_image']['name'] != null || !empty($_FILES['order_image']['name'])):
                $this->load->library('upload');
				$_FILES['userfile']['name']     = $_FILES['order_image']['name'];
				$_FILES['userfile']['type']     = $_FILES['order_image']['type'];
				$_FILES['userfile']['tmp_name'] = $_FILES['order_image']['tmp_name'];
				$_FILES['userfile']['error']    = $_FILES['order_image']['error'];
				$_FILES['userfile']['size']     = $_FILES['order_image']['size'];
				
				$imagePath = realpath(APPPATH . '../assets/uploads/sales_order/');
				$config = ['file_name' => time()."_order_item_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

				$this->upload->initialize($config);
				if (!$this->upload->do_upload()):
					$errorMessage['order_image'] = $this->upload->display_errors();
					$this->printJson(["status"=>0,"message"=>$errorMessage]);
				else:
					$uploadData = $this->upload->data();
					$masterData['order_file'] = $uploadData['file_name'];
				endif;
			endif;
				
			$this->printJson($this->salesOrder->save($masterData,$itemData));
		endif;
	}

	public function edit($id){
		$this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = "";
		$this->data['customerData'] = $this->party->getCustomerList();
		$this->data['itemData'] = $this->item->getItemList(1);
		$this->data['unitData'] = $this->item->itemUnits();
		$this->data['terms'] = $this->terms->getTermsList();
		$this->data['ppapLevel'] = explode(',',$this->grnModel->getMasterOptions()->ppap_level);     
		$this->data['devCharge'] = 0;   
		$this->data['currencyData'] = $this->party->getCurrency();  
		$dataRow = $this->salesOrder->getSalesOrder($id); 

		$result = $this->party->getContactDetailForEdit($dataRow);
        $dataRow->contact_person = $result->person;
        $dataRow->contact_no = $result->mobile;
        $dataRow->contact_email = $result->email;
        $this->data['dataRow'] = $dataRow;
		$this->load->view($this->orderForm,$this->data);
	}
	
	public function view($id){
		$this->data['dataRow'] = $this->salesOrder->getSalesOrder($id);
		$this->load->view($this->orderView,$this->data);
    }
    
    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->salesOrder->deleteOrder($id));
		endif;
	}

	public function completeInv(){
		$id = $this->input->post('id');
		$val = $this->input->post('val');
		$msg = $this->input->post('msg');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->salesOrder->completeInv($id,$val,$msg));
		endif;
	}

	public function getPartyOrders(){
		$this->printJson($this->salesOrder->getPartyOrders($this->input->post('party_id')));
	}
	
	public function completeOrderItem(){
		$id = $this->input->post('id');
		$val = $this->input->post('val');
		$msg = $this->input->post('msg');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->salesOrder->completeSalesOrderItem($id,$val,$msg));
		endif;
	}
	
	public function getRequiredMaterial(){
		$item_id = $this->input->post('item_id');
		$qty = $this->input->post('qty');
		$this->printJson($this->salesOrder->getRequiredMaterial($item_id,$qty));
	}
	
	public function getPartyItems(){
		$this->printJson($this->item->getPartyItems($this->input->post('party_id')));
	}

	public function approveSOrder(){
		$data = $this->input->post();
		
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->salesOrder->approveSOrder($data));
		endif;
	}
	
	public function salesOrderView(){
		$id=$this->input->post('id');
		$this->data['soData'] = $soData = $this->salesOrder->getSalesOrder($id);
		$this->data['partyData'] = $this->party->getParty($this->data['soData']->party_id);
		$this->data['companyData'] = $this->salesOrder->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png'); $unapproved = base_url('assets/images/unapproved.jpg');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$this->data['soData']->taxableAmt = $this->data['soData']->total_amount - $this->data['soData']->freight_amount - $this->data['soData']->packing_amount;
		
		
		$i=1;$totalQty = 0; $tbody='';
		if(!empty($soData->items)):
			foreach($soData->items as $row):
				$indent = (!empty($soData->enq_id)) ? '<br>Reference No:'.$soData->enq_prefix.$soData->enq_no : '';
				$delivery_date = (!empty($row->cod_date)) ? '<br><b>Delivery Date :</b>'.formatDate($row->cod_date) : '';	
				
				
                if(!empty($row->from_entry_type)){
				    $itemData = $this->salesOrder->getSOTransactionsForPrint($row->id);
				    if(!empty($itemData)){
				        $row->itemDrgNo = $itemData->itemDrgNo;
				        $row->itemRevNO = $itemData->itemRevNO;
				        $row->partNo = $itemData->partNo;
				    }
                }
                
				$drgNo = (!empty($row->itemDrgNo)) ? '<br><b>Drg. No.</b> '.$row->itemDrgNo : '';	
				$revNo = (!empty($row->itemRevNO)) ? $row->itemRevNO : '';
				
				if(!empty($revNo) AND !empty($drgNo)){ $drgNo = $drgNo.', <b>Rev. No.</b> '.$row->itemRevNO; }
				if(!empty($revNo) AND empty($drgNo)){ $drgNo = '<br> <b>Rev. No.</b> '.$row->itemRevNO; }
				
				if(!empty($row->partNo) AND !empty($drgNo)){ $drgNo = $drgNo.', <b>Part No.</b> '.$row->partNo; }
				if(!empty($row->partNo) AND empty($drgNo)){ $drgNo = '<br> <b>Part No.</b> '.$row->partNo; }	
				
				$tbody .= '<tr>';
					$tbody .=  '<td class="text-center">'.$i++.'</td>';
					$tbody .=  '<td><b>'.$row->item_name.'</b>'.$drgNo.$indent.$delivery_date.'</td>';
					$tbody .=  '<td class="text-center">'.$row->hsn_code.'</td>';
					if($soData->gst_applicable == 1){$tbody .=  '<td class="text-center">'.floatVal($row->igst_per).'</td>';}
					$tbody .=  '<td class="text-right">'.$row->qty.'</td>';
					$tbody .=  '<td class="text-center">'.$row->unit_name.'</td>';
					$tbody .=  '<td class="text-right">'.sprintf('%.3f',$row->price).'</td>';
					$tbody .=  '<td class="text-right">'.sprintf('%.2f',$row->amount).'</td>';
				$tbody .=  '</tr>';
				$totalQty += $row->qty;
			endforeach;
		endif;
		$this->data['totalQty'] = $totalQty;
		$this->data['tbody'] = $tbody;
		
		$pdfData = $this->load->view('sales_order/printso',$this->data,true);
		
		$this->printJson(['status'=>1,'pdfData'=>$pdfData]);
	}

	public function salesOrder_pdf($id,$type=0){
		$soData = $this->salesOrder->getSalesOrderForPrint($id);
		$this->data['soData'] = $soData;
		$this->data['partyData'] = $this->party->getParty($this->data['soData']->party_id);
		$this->data['companyData'] = $this->salesOrder->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png'); $unapproved = base_url('assets/images/unapproved.jpg');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$this->data['soData']->taxableAmt = $this->data['soData']->total_amount - $this->data['soData']->freight_amount - $this->data['soData']->packing_amount;
		
		$i=1;$totalQty = 0; $tbody='';
		if(!empty($soData->items)):
			foreach($soData->items as $row):
				$indent = (!empty($soData->enq_id)) ? '<br>Reference No:'.$soData->enq_prefix.$soData->enq_no : '';
				$delivery_date = (!empty($row->cod_date)) ? '<br><b>Delivery Date :</b>'.formatDate($row->cod_date) : '';	
				
				
                if(!empty($row->from_entry_type)){
				    $itemData = $this->salesOrder->getSOTransactionsForPrint($row->id);
				    if(!empty($itemData)){
				        $row->itemDrgNo = $itemData->itemDrgNo;
				        $row->itemRevNO = $itemData->itemRevNO;
				        $row->partNo = $itemData->partNo;
				    }
                }
                
				$drgNo = (!empty($row->itemDrgNo)) ? '<br><b>Drg. No.</b> '.$row->itemDrgNo : '';	
				$revNo = (!empty($row->itemRevNO)) ? $row->itemRevNO : '';
				
				if(!empty($revNo) AND !empty($drgNo)){ $drgNo = $drgNo.', <b>Rev. No.</b> '.$row->itemRevNO; }
				if(!empty($revNo) AND empty($drgNo)){ $drgNo = '<br> <b>Rev. No.</b> '.$row->itemRevNO; }
				
				if(!empty($row->partNo) AND !empty($drgNo)){ $drgNo = $drgNo.', <b>Part No.</b> '.$row->partNo; }
				if(!empty($row->partNo) AND empty($drgNo)){ $drgNo = '<br> <b>Part No.</b> '.$row->partNo; }	
				
				$row->item_name = (!empty($row->item_code)) ? '['.$row->item_code.'] '.$row->item_name : $row->item_name;
				$tbody .= '<tr>';
					$tbody .=  '<td class="text-center">'.$i++.'</td>';
					$tbody .=  '<td><b>'.$row->item_name.'</b>'.$drgNo.$indent.$delivery_date.'</td>';
					$tbody .=  '<td class="text-center">'.$row->hsn_code.'</td>';
					if($soData->gst_applicable == 1){$tbody .=  '<td class="text-center">'.floatVal($row->igst_per).'</td>';}
					$tbody .=  '<td class="text-right">'.$row->qty.'</td>';
					$tbody .=  '<td class="text-center">'.$row->unit_name.'</td>';
					$tbody .=  '<td class="text-right">'.sprintf('%.3f',$row->price).'</td>';
					$tbody .=  '<td class="text-right">'.sprintf('%.2f',$row->amount).'</td>';
				$tbody .=  '</tr>';
				$totalQty += $row->qty;
			endforeach;
		endif;
		$this->data['totalQty'] = $totalQty;
		$this->data['tbody'] = $tbody;
		$pdfData = $this->load->view('sales_order/printso',$this->data,true);
		
		$soData = $this->data['soData'];
		
		$prepare = $this->employee->getEmp($soData->created_by);
		//$prepareBy = $prepare->emp_name.' <br>('.formatDate($soData->created_at).')'; 
		$prepareBy = $prepare->emp_name; 
		$prepareSign='';$approveBy = '';$approveSign = '';
		if(!empty($soData->is_approve)){
			$approve = $this->employee->getEmp($soData->is_approve);
			//$approveBy .= $approve->emp_name.' <br>('.formatDate($soData->approve_date).')'; 
			$approveBy .= $approve->emp_name; 
			$sign_img = base_url('assets/uploads/emp_sign/sign_'.$soData->is_approve.'.png');
			$approveSign = '<img src="'.$sign_img.'" style="width:100px;">'; 
		}
		
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;" rowspan="4"></td>
							<th colspan="2">For, '.$this->data['companyData']->company_name.'</th>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center">'.$prepareSign.'</td>
							<td style="width:25%;" class="text-center">'.$approveSign.'</td>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center">'.$prepareBy.'</td>
							<td style="width:25%;" class="text-center">'.$approveBy.'</td>
						</tr>
						<tr>
							<th style="width:25%;" class="text-center">Prepared By</th>
							<th style="width:25%;margin-top:-50px;" class="text-center">Authorised By</th>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<td style="width:25%;">SO No. & Date : '.$soData->trans_prefix.$soData->trans_no.'-'.formatDate($soData->trans_date).'</td>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = $this->m_pdf->load();
		$fileName = str_replace("/","_",getPrefixNumber($soData->trans_prefix,$soData->trans_no).'.pdf');
		$filePath = realpath(APPPATH . '../assets/uploads/sales/');
		$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		if(!empty($soData->is_approve)){ $mpdf->SetWatermarkImage($logo,0.05,array(120,60));$mpdf->showWatermarkImage = true; }
		else{ $mpdf->SetWatermarkText('Not Approved Copy'); $mpdf->showWatermarkText = true;}
		//$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		//$mpdf->SetProtection(array('print'));
		$mpdf->setTitle(str_replace("/","_",getPrefixNumber($soData->trans_prefix,$soData->trans_no)));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,41,50,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		
		if(empty($type)):
			$mpdf->Output($fileName,'I');
		else:
        	$mpdf->Output($filePath.'/'.$fileName, 'F');
			return $filePath.'/'.$fileName;
		endif;
	}

	public function getItemList(){
        $this->printJson($this->salesOrder->getItemList($this->input->post('id')));
    }
    
    // Avruti @3-2-2022
	public function closeSalesOrder(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->salesOrder->getSalesOrder($id);
        $this->load->view($this->closeView,$this->data);
    }
    
    public function getSOEstimateQty(){
		$data=$this->input->post();
		$estQty=$this->salesOrder->getSOEstimateQty($data);
		$this->printJson(['status'=>1,'qty_kg'=>(!empty($estQty->qty_kg)?$estQty->qty_kg:0)]);
	}
	
    public function saveCloseSO(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['close_reason']))
            $errorMessage['close_reason'] = "Reason is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->salesOrder->saveCloseSO($data));
        endif;
    }
    
    /* Created By Jp@01092022*/
    public function sendMail(){
		$postData = $this->input->post(); 
		$soData = $this->salesOrder->getSalesOrderById($postData['id']);
    	$empData = $this->employee->getEmp($this->loginId);
		
		if(!empty($soData->contact_email)){
		    //if(!empty($empData->emp_email)){
        		$attachment = $this->salesOrder_pdf($postData['id'],1);
                $ref_no = str_replace('_','/',$postData['ref_no']);
                
                $signData['sender_name'] = 'Suresh Modha';
                $signData['sender_contact'] = '+91 9904709711';
                $signData['sender_designation'] = 'Sr. Marketing Executive';
                $signData['sign_email'] = 'export@jayjalaramind.com';
                // $signData['sender_name'] = $empData->emp_name;
                // $signData['sender_contact'] = $empData->emp_contact;
                // $signData['sender_designation'] = $empData->designation;
                // $signData['sign_email'] = $empData->emp_email;
                $contact_person = (!empty($soData->contact_person)) ? $soData->contact_person : 'Sir/Ma’am';
                
        		$emailSignature = $this->mails->getSignature($signData);
        
        
        		$mailData = array();
        		$mailData['sender_email'] = 'export@jayjalaramind.com';
        		//$mailData['receiver_email'] = 'jagdishpatelsoft@gmail.com';
        		$mailData['receiver_email'] = (!empty($soData->contact_email)) ? ','.$soData->contact_email : '';
        		$mailData['cc_email'] = '	info@jayjalaramind.com';//jagdishpatelsoft@gmail.com,rakeshkantaria74@gmail.com';
        		$mailData['bcc_email'] = 'export@jayjalaramind.com';
        		$mailData['mail_type'] = 7;
        		$mailData['ref_id'] = 0;
        		$mailData['ref_no'] = 0;
        		$mailData['created_by'] = $this->loginId;
        		$mailData['subject'] = 'SALES ACCEPTANCE';
        		
        		$mail_body = '<div style="font-size:12pt;font-family: Bookman Old Style;">';
        		    $mail_body .= '<b>Dear '.$contact_person.',</b><br><br>';
        		    $mail_body .= 'Wishing you a good day!<br>';
        		    $mail_body .= 'Thank you for sharing your purchase order.<br><br>Here, you will find the enclosed <b>SALES ACCEPTANCE</b> with Order Acceptance No.: <b>'.$ref_no.'</b> for the same.<br><br>Please find the attached file, and let me know if any query<br><br><br>';
                $mail_body .= '</div>';
        		$mail_body .= $emailSignature;
        		$mailData['mail_body'] = $mail_body;
        		
        		$result = $this->mails->sendMail($mailData, [$attachment]);
        		unlink($attachment);
        		$this->printJson($result);
            /*}
            else
            {
                $this->printJson(['status'=>0,'message'=>'Sender Email Not Found.']);
            }*/
        }
        else
        {
            $this->printJson(['status'=>0,'message'=>'Receiver Email Not Found.']);
        }
    
	}
}
?>