<?php
class PurchaseOrderSchedule extends MY_Controller{
    private $indexPage = 'purchase_order_schedule/index';
    private $orderForm = "purchase_order_schedule/form";
    public function __construct(){
        parent::__construct();
        $this->isLoggedin();
		$this->data['headData']->pageTitle = "Purchase Orders Schedule";
		$this->data['headData']->controller = "purchaseOrderSchedule";
		$this->data['headData']->pageUrl = "purchaseOrderSchedule";
    }

    public function index(){
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
    
    public function getDTRows(){
        $result = $this->purchaseOrderSchedule->getDTRows($this->input->post());
	
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;        
            $pq = $row->qty - $row->rec_qty;    
            $row->pending_qty = ($pq >=0 ) ? $pq : 0;;    
            $row->controller = "purchaseOrderSchedule";    
            $row->controller = "purchaseOrderSchedule";    
            $sendData[] = getPurchaseOrderScheduleData($row);
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

    public function addScheduleOrder(){
        $this->data['partyData'] = $this->party->getPartyList();
		$this->data['nextPoNo'] = $this->purchaseOrder->nextPoNo(3);
		$this->data['po_prefix'] = 'SCO/'.$this->shortYear.'/';
        $this->data['itemCategoryData'] = $this->itemCategory->getCategoryList();
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
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:	
			if(!empty($data['freight_amt'])):
				$data['freight_gst'] = ($data['freight_amt'] * 0.18);
			endif;
			if(!empty($data['packing_charge'])):
				$data['packing_gst'] = ($data['packing_charge'] * 0.18);
			endif;
			$masterData = [ 
				'id' => $data['order_id'],
				'order_type' => 3,
				// 'enq_id' => $data['enq_id'],
				'po_prefix'=>$data['po_prefix'], 
				'po_no'=>$data['po_no'], 
				'po_date' => date('Y-m-d',strtotime($data['po_date'])), 
				'gst_type' => $data['gst_type'], 
				'party_id' => $data['party_id'],			
				// 'quotation_no' => $data['quotation_no'],
				// 'quotation_date' => date('Y-m-d',strtotime($data['quotation_date'])),
				// 'reference_by' => $data['reference_by'],
				// 'destination' => $data['destination'],
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
				// 'terms_conditions' => $data['terms_conditions'],
                'created_by' => $this->session->userdata('loginId'),
				'ref_id'=>$data['ref_id'],
				//'req_id' => $data['req_id']
			];
							
			$itemData = [
				'id' => $data['trans_id'],
				'order_type' => 3,
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
				'remarks' => $data['remarks'],
				
                'created_by' => $this->session->userdata('loginId')
			];
			$this->printJson($this->purchaseOrderSchedule->save($masterData,$itemData));
		endif;
    }

    public function edit($id){
        $this->data['partyData'] = $this->party->getPartyList();
		// $this->data['itemData'] = $this->item->getItemLists("2,3");
		
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['dataRow'] = $this->purchaseOrderSchedule->getPurchaseOrder($id);
		$this->data['terms'] = $this->terms->getTermsList();
		$this->data['poList']=$this->purchaseOrderSchedule->getScheduleOrderByParty( $this->data['dataRow']->party_id);
		$this->data['fgItemList'] = $this->item->getItemList(1);
		$html="";
		
		foreach($this->data['poList']['result'] as $row)
		{
			$selected = (!empty( $this->data['dataRow']->order_id) &&  $this->data['dataRow']->order_id == $row->id) ? "selected" : '';
			$html .= '<option value="'.$row->id.'" ' . $selected . '>'.getPrefixNumber($row->po_prefix,$row->po_no).'</option>';
			
		}
		$this->data['poHtml']=$html;
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

	

	public function getPartyOrders(){
		$this->printJson($this->purchaseOrder->getPartyOrders($this->input->post('party_id')));
	}

	function printPO($id){
		$this->data['poData'] = $this->purchaseOrder->getPurchaseOrder($id);
		$this->data['companyData'] = $companyData = $this->purchaseOrder->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png');
		$icon=base_url('assets/images/icon.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$this->data['poData']->taxableAmt = $this->data['poData']->amount - $this->data['poData']->freight_amt - $this->data['poData']->packing_charge;
		
		$pdfData = $this->load->view('purchase_order/printpo',$this->data,true);
		
		$poData = $this->data['poData'];
		
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
	    /*$htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
			<tr>
				<td style="width:15%;" class="text-left"><img src="'.$logo.'" style="height:60px;"></td>
				<td style="width:70%;font-size:1.4rem;" class="org_title text-uppercase text-center">'.$companyData->company_name.'</td>
				<td style="width:15%;" class="text-right"><img src="'.$icon.'" style="height:60px;"><td>
			</tr>
		</table>
		<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
			<tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
		</table>';*/
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;" rowspan="3"></td>
							<th colspan="2">For, '.$this->data['companyData']->company_name.'</th>
						</tr>
						<tr>
							<td colspan="3" height="100"></td>
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
		$mpdf->AddPage('P','','','','',5,5,30,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

	public function getItemPriceLists(){
		$item_id=$this->input->post('item_id');
		$itemData=$this->item->getItem($item_id);
		$familydata=$this->purchaseOrder->getFamilyItem($itemData->family_id);
		$tbody="";$i=1;
		foreach($familydata as $row):
			
			
			$tbody .= '<tr class="text-center">
							<td>'.$i++.'</td>
							<td>'.$row->item_name.'</td>
							<td>'.$row->qty.'</td>
							<td>'.$row->price.'</td>	
					   </tr>';
			
		endforeach;
	$this->printJson(['status'=>1,'tbody'=>$tbody]);
	}

    public function getItemList()
    {
        $order_id=$this->input->post('order_id');
		$itemData=$this->purchaseOrderSchedule->getItemListByOrderId($order_id);
	
		$options="<option value=''>Select Item</option>";$i=1;
		foreach($itemData as $row):
			
			
			$options .= "<option data-row='".json_encode($row)."' value='".$row->id."'>[".$row->item_code."] ".$row->item_name."</option>";
			
		endforeach;
	$this->printJson(['status'=>1,'options'=>$options]);
    }

	public function getPObyParty()
	{
		// print_r($this->input->post());
		$partyData=$this->party->getParty($this->input->post('party_id'));
		$gst_type=3;
		if(!empty($partyData->gstin))
		{
			$party_stateCode="";

			$party_stateCode = (!empty($partyData->gstin)) ? substr($partyData->gstin,0,2) : '';
			if($party_stateCode!="24")
			{
				$gst_type=2;
			}
			else
			{
				$gst_type=1;
			}
		
		}
		$result=$this->purchaseOrderSchedule->getScheduleOrderByParty($this->input->post('party_id'));
		$result['gst_type']=$gst_type;
		$this->printJson($result);

	}
	public function getSheduleItemPrice()
	{
		$data = $this->input->post();
		$this->printJson($this->priceAmendment->getSheduleItemPrice($data));
	}
}
