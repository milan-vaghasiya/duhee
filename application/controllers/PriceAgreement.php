<?php
class PriceAgreement extends MY_Controller{
    private $indexPage = 'price_agreement/index';
    private $orderForm = "price_agreement/form";
    public function __construct(){
        parent::__construct();
        $this->isLoggedin();
		$this->data['headData']->pageTitle = "Price Agreement";
		$this->data['headData']->controller = "priceAgreement";
		$this->data['headData']->pageUrl = "priceAgreement";
    }

    public function index(){
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
    
    public function getDTRows(){
        $data = $this->input->post(); $data['order_type'] = 2;
        $result = $this->purchaseOrder->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;        
            $pq = $row->qty - $row->rec_qty;    
            $row->pending_qty = ($pq >=0 ) ? $pq : 0;;    
            $row->controller = "priceAgreement";    
            $row->controller = "priceAgreement";    
            $sendData[] = getPriceAgreementData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function createOrder($id){
		$this->data['enquiryData'] = $this->purchaseEnquiry->getEnquiry($id);
		$this->data['partyData'] = $this->party->getPartyList();
        $this->data['itemData'] = $this->item->getItemList();
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['po_prefix'] = 'PO/'.$this->shortYear.'/';
        $this->data['nextPoNo'] = $this->purchaseOrder->nextPoNo();
		$this->data['fgItemList'] = $this->item->getItemList(1);
		$this->data['terms'] = $this->terms->getTermsList();
        $this->load->view($this->orderForm,$this->data);
	}

    public function addPurchaseOrder(){
        $this->data['partyData'] = $this->party->getPartyList();
		$this->data['nextPoNo'] = $this->purchaseOrder->nextPoNo();
		$this->data['po_prefix'] = 'PO/'.$this->shortYear.'/';
        $this->data['itemData'] = $this->item->getItemLists("2,3");
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['fgItemList'] = $this->item->getItemList(1);
		$this->data['terms'] = $this->terms->getTermsList();
        $this->load->view($this->orderForm,$this->data);
	}

    public function getItemData(){
		$id = $this->input->post('itemId');
        $result = $this->item->getItem($id);
        $result->unit_name = $this->item->itemUnit($result->unit_id)->unit_name;
		$this->printJson($result);
	}

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['order_type'])){
        	if(empty($data['party_id']))
            	$errorMessage['party_id'] = "Party Name is required.";
		}
        if(empty($data['po_no']))
            $errorMessage['po_no'] = 'PO. No. is required.';
        if(empty($data['item_id'][0]))
            $errorMessage['item_id'] = 'Item Name is required.';
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
			
			if(!empty($data['freight_amt'])):
				$data['freight_gst'] = ($data['freight_amt'] * 0.18);
			endif;
			if(!empty($data['packing_charge'])):
				$data['packing_gst'] = ($data['packing_charge'] * 0.18);
			endif;


			$masterData = [ 
				'id' => $data['order_id'],
				'order_type' => $data['order_type'],
				'enq_id' => $data['enq_id'],
				'po_prefix'=>$data['po_prefix'], 
				'po_no'=>$data['po_no'], 
				'po_date' => date('Y-m-d',strtotime($data['po_date'])), 
				'gst_type' => $data['gst_type'], 
				'party_id' => $data['party_id'],			
				'quotation_no' => $data['quotation_no'],
				'quotation_date' => date('Y-m-d',strtotime($data['quotation_date'])),
				'reference_by' => $data['reference_by'],
				'destination' => $data['destination'],
				'amount' => $data['amount_total'],
				'freight_amt' => $data['freight_amt'],
				'freight_gst' => $data['freight_gst'],
				'packing_charge' => $data['packing_charge'],
				'packing_gst' => $data['packing_gst'],
				'igst_amt' => $data['igst_amt_total'], 
				'cgst_amt' => $data['cgst_amt_total'], 
				'sgst_amt' => $data['sgst_amt_total'], 
				'disc_amt' => $data['disc_amt_total'],
				'round_off' => $data['round_off'], 
				'net_amount' => $data['net_amount_total'],
				'remark' => $data['remark'],
				'terms_conditions' => $data['terms_conditions'],
                'created_by' => $this->session->userdata('loginId'),
				'req_id' => $data['req_id']
			];
							
			$itemData = [
				'id' => $data['trans_id'],
				'item_id' => $data['item_id'],
				'unit_id' => $data['unit_id'],
				'fgitem_id' => $data['fgitem_id'],
                'fgitem_name' => $data['fgitem_name'],
				'hsn_code' => $data['hsn_code'],
				'delivery_date' => $data['delivery_date'],
				'qty' => $data['qty'],
				'price' => $data['price'],
				'igst' => $data['igst'],
				'sgst' => $data['sgst'],
				'cgst' => $data['cgst'],
				'igst_amt' => $data['igst_amt'],
				'sgst_amt' => $data['sgst_amt'],
				'cgst_amt' => $data['cgst_amt'],
				'amount' => $data['amount'],
				'disc_per' => $data['disc_per'],
				'disc_amt' => $data['disc_amt'],
				'net_amount' => $data['net_amount'],
                'created_by' => $this->session->userdata('loginId')
			];
			$this->printJson($this->purchaseOrder->save($masterData,$itemData));
		endif;
    }

    public function edit($id){
        $this->data['partyData'] = $this->party->getPartyList();
		$this->data['itemData'] = $this->item->getItemLists("2,3");
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['dataRow'] = $this->purchaseOrder->getPurchaseOrder($id);
		$this->data['terms'] = $this->terms->getTermsList();
		$this->data['fgItemList'] = $this->item->getItemList(1);
        $this->load->view($this->orderForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->purchaseOrder->deleteOrder($id));
		endif;
    }   

	/* NYN */
	public function addPOFromRequest($id){
		$this->data['req_id'] = $id;
        $this->data['partyData'] = $this->party->getPartyList();
		$this->data['nextPoNo'] = $this->purchaseOrder->nextPoNo();
		$this->data['po_prefix'] = 'PO/'.$this->shortYear.'/';
		$this->data['itemData'] = $this->item->getItemLists("2,3");
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['fgItemList'] = $this->item->getItemList(1);
		$this->data['terms'] = $this->terms->getTermsList();
        $this->data['reqItem'] = $this->purchaseRequest->getPurchaseRequest($id);
        $this->load->view($this->orderForm,$this->data);
	}

	public function getPartyOrders(){
		$this->printJson($this->purchaseOrder->getPartyOrders($this->input->post('party_id')));
	}

	function printPO($id){
		$this->data['poData'] = $this->purchaseOrder->getPurchaseOrder($id);
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$this->data['poData']->taxableAmt = $this->data['poData']->amount - $this->data['poData']->freight_amt - $this->data['poData']->packing_charge;
		
		$pdfData = $this->load->view('price_agreement/printpo',$this->data,true);
		
		$poData = $this->data['poData'];
		$prepare = $this->employee->getEmp($poData->created_by);
		$prepareBy = $prepare->emp_name.' <br>('.formatDate($poData->created_at).')'; 
		$approveBy = '';
		if(!empty($poData->is_approve)){
			$approve = $this->employee->getEmp($poData->is_approve);
			$approveBy .= $approve->emp_name.' <br>('.formatDate($poData->approve_date).')'; 
		}
		
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;" rowspan="3"></td>
							<th colspan="2">For, '.$this->data['companyData']->company_name.'</th>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center">'.$prepareBy.'</td>
							<td style="width:25%;" class="text-center">'.$approveBy.'</td>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center">Prepared By</td>
							<td style="width:25%;" class="text-center">Authorised By</td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<td style="width:25%;">PO No. & Date : '.$poData->po_prefix.$poData->po_no.'-'.formatDate($poData->po_date).'</td>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,39,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

	public function getItemPriceLists(){
		$data=$this->input->post();
		// $itemData=$this->item->getItem($item_id);
		$this->printJson($this->purchaseOrder->getFamilyItem($data['item_id'],$data['family_id']));
		
		/* $tbody="";$i=1;
		foreach($familydata as $row):
			
			
			$tbody .= '<tr class="text-center">
							<td>'.$i++.'</td>
							<td>'.$row->item_name.'</td>
							<td>'.$row->qty.'</td>
							<td>'.$row->price.'</td>	
					   </tr>';
			
		endforeach;
		['status'=>1,'tbody'=>$tbody]; */
	}



}
?>