<?php
class EwayBill extends MY_Controller{
    private $ewbForm = "eway_bill/eway_bill_form";
	
    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "E-Way Bill";
        $this->data['headData']->controller = "ewaybill";
		/* $this->load->library('phpqrcode/qrlib');
        $this->load->helper('file'); */
	}

    public function addEwayBill(){
        $party_id = $this->input->post('party_id');
        $partyData = $this->party->getParty($party_id);
        $this->data['challan_id'] = $this->input->post('id');
        $this->data['party_id'] = $party_id;
        $this->data['distance'] = (!empty($partyData->distance))?$partyData->distance:"";
        $this->data['jwChallanData'] = $this->jobWork->getJobWorkChallan($this->input->post('id'));
        $this->data['transportData'] = $this->transport->getTransportList();
        $this->data['stateList'] = $this->party->getStates(101);
        $this->load->view($this->ewbForm,$this->data);
    }

    public function getEwbAddress(){
		$from_address = "";$from_pincode = "";$ship_address = "";$ship_pincode = "";
		$from_city="";$from_state="";$ship_city="";$ship_state="";
		$data = $this->input->post();
		
		$partyData = $this->party->getParty($data['party_id']);
		$orgData = $this->masterModel->getCompanyInfo();
		
		$fromCity = $this->party->getCitiesList(4030);//$this->db->where(['state_id' => 4030, 'is_status' => 1])->get("cities")->result();	
		$shipCity = $this->party->getCitiesList($partyData->state_id);//$this->db->where(['state_id' => $partyData->state_id, 'is_status' => 1])->get("cities")->result();
		
		if ($data['transaction_type'] == 1) {
			$fromCityOptions = '<option value="">Select City</option>';
			foreach($fromCity as $row):
				$fromCitySelected = ($orgData->company_city == $row->name)?"selected":"";
				$fromCityOptions .= '<option value="'.$row->id.'" '.$fromCitySelected.'>'.$row->name.'</option>';
			endforeach;

			$shipCityOptions = '<option value="">Select City</option>';
			foreach($shipCity as $row):
				$shipCitySelected = ($partyData->city_id == $row->id)?"selected":"";
				$shipCityOptions .= '<option value="'.$row->id.'" '.$shipCitySelected.'>'.$row->name.'</option>';
			endforeach;
			
			$from_address = $orgData->company_address;
			$from_pincode = $orgData->company_pincode;
			$ship_address = $partyData->party_address;
			$ship_pincode = $partyData->party_pincode;

			$from_city=$fromCityOptions;
			$from_state=4030;
			
			$ship_city=$shipCityOptions;
			$ship_state=$partyData->state_id;

		} elseif ($data['transaction_type'] == 2) {	
			$fromCityOptions = '<option value="">Select City</option>';
			foreach($fromCity as $row):
				$fromCitySelected = ($orgData->company_city == $row->name)?"selected":"";
				$fromCityOptions .= '<option value="'.$row->id.'" '.$fromCitySelected.'>'.$row->name.'</option>';
			endforeach;
	
			$from_address = $orgData->company_address;
			$from_pincode = $orgData->company_pincode;
			$ship_address = "";
			$ship_pincode = "";

			$from_city=$fromCityOptions;
			$from_state=4030;
			
			$ship_city="";
			$ship_state="";
		} elseif ($data['transaction_type'] == 3) {	

			$from_address = "";
			$from_pincode = "";
			$ship_address = $partyData->party_address;
			$ship_pincode = $partyData->party_pincode;

			$shipCityOptions = '<option value="">Select City</option>';
			foreach($shipCity as $row):
				$shipCitySelected = ($partyData->city_id == $row->id)?"selected":"";
				$shipCityOptions .= '<option value="'.$row->id.'" '.$shipCitySelected.'>'.$row->name.'</option>';
			endforeach;

			$from_city="";
			$from_state="";
			$ship_city=$shipCityOptions;
			$ship_state=$partyData->state_id;
		} elseif ($data['transaction_type'] == 4) {
			$from_address = "";
			$from_pincode = "";
			$ship_address = "";
			$ship_pincode = "";

			$from_city="";
			$from_state="";
			$ship_city="";
			$ship_state="";
		}

		$this->printJson(["status" => 1, "from_address" => $from_address, "from_pincode" => $from_pincode, "ship_address" => $ship_address, "ship_pincode" => $ship_pincode,"from_city"=>$from_city,"from_state"=>$from_state,"ship_city"=>$ship_city,"ship_state"=>$ship_state]);
	}

    public function generateEwb(){
        $data = $this->input->post();  
		$errorMessage = array();
        if(empty($data['doc_type']))
            $errorMessage['doc_type'] = "Document Type is required.";
        if(empty($data['supply_type']))
            $errorMessage['supply_type'] = "Supply Type is required.";
        if(empty($data['sub_supply_type']))
            $errorMessage['sub_supply_type'] = "Sub Supply Type is required.";
        if(empty($data['trans_mode']))
            $errorMessage['trans_mode'] = "Transport Mode is required.";
        if(empty($data['trans_distance']))
            $errorMessage['trans_distance'] = "Trans. Distance is required.";
        if(empty($data['transport_id']) && empty($data['vehicle_no']))
            $errorMessage['vehicle_no'] = "Vehicle no. is required.";
        if(!isset($data['ref_id']))
            $errorMessage['ref_id'] = "Please select recoreds.";
		if(empty($data['vehicle_no']))
            $data['trans_mode'] = "";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else: 
            //test authData
            /* $authData = [
                'fromGst' => '05AAACG0904A1ZL',
                'euser' => '05AAACG0904A1ZL',
                'epass' => 'abc123@@'
            ]; */

            //live authData
            $authData = [
                'fromGst' => "24AAFCA1569E1ZZ",
                'euser' => "APPLIEDAUT_API_APL",
                'epass' => "Aa@1234$"
            ];
            $authToken = $this->ewayBill->getEwbAuthToken($authData);
            if($authToken['status'] == 2):
                $this->printJson($authToken);
            else:
                $storeEwbData = $this->ewayBill->save($data);
                $ewbJsonSingle = $this->ewayBill->ewbJsonSingle($data);

                $authData['token'] = $authToken['token'];
                $postData['ewbJson'] = $ewbJsonSingle;
                $postData['doc_type'] = $data['doc_type'];
                $postData['ref_id'] = $data['ref_id'];
                $postData['ewb_id'] = $storeEwbData['id'];
                $this->printJson($this->ewayBill->generateEwayBill($postData,$authData));
            endif;
        endif;    
    }
    
    public function cancelEwb(){
        $data = $this->input->post();  
		$errorMessage = array();
        if(empty($data['ewbNo']))
            $errorMessage['ewbNo'] = "Eway Bill Number Not found.";
        if(empty($data['cancelRsnCode']))
            $errorMessage['cancelRsnCode'] = "Cancellation Reason is required.";
        if(($data['cancelRsnCode'] == 4) and empty($data['cancelRmrk']))
            $errorMessage['cancelRmrk'] = "Remark is required";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else: 
            //test authData
            /* $authData = [
                'fromGst' => '05AAACG0904A1ZL',
                'euser' => '05AAACG0904A1ZL',
                'epass' => 'abc123@@'
            ]; */

            //live authData
            $authData = [
                'fromGst' => "24AAFCA1569E1ZZ",
                'euser' => "APPLIEDAUT_API_APL",
                'epass' => "Aa@1234$"
            ];
            $authToken = $this->ewayBill->getEwbAuthToken($authData);
            if($authToken['status'] == 2):
                $this->printJson($authToken);
            else:
                $storeEwbData = $this->ewayBill->save($data);
                $ewbJsonSingle = $this->ewayBill->ewbJsonSingle($data);

                $authData['token'] = $authToken['token'];
                $postData['ewbJson'] = $ewbJsonSingle;
                $postData['doc_type'] = $data['doc_type'];
                $postData['ref_id'] = $data['ref_id'];
                $postData['ewb_id'] = $storeEwbData['id'];
                $this->printJson($this->ewayBill->generateEwayBill($postData,$authData));
            endif;
        endif;    
    }
    
    public function ewb_pdf($eway_bill_no)
	{
        $ewbData = $this->db->where('eway_bill_no',$eway_bill_no)->get('eway_bill_master')->row();
		$invTable= ($ewbData->doc_type == 'INV') ? "sales_master" : "delivery_challan";
        $salesData = $this->db->where('eway_bill_no',$eway_bill_no)->get($invTable)->row();
		$inrSymbol=base_url('assets/images/inr.png');
		$logo=base_url('assets/images/wellsun_favicon.png');
		$orgData = $this->db->where("org_id",$salesData->unt_id)->get('organization')->row();
		//$ewbJson = json_decode($ewbData->json_data)->billLists[0];
		$ewbJsonData = json_decode($ewbData->json_data);
		if(!empty($ewbJsonData->billLists[0])):
			$ewbJson = $ewbJsonData->billLists[0];
		else:
			$ewbJson = $ewbJsonData;
		endif;
		$supplyType = ($ewbJson->supplyType=='O') ? 'Outward' : 'Inward';
		$subSupplyType = Array('','Supply','Import','Export','Job Work','For Own Use','Job Work Returns','Sales Returns','Others','SKD/CKD','Line Sales','Recipient Not Known','Exhibition or Fairs');
		$transMode = Array('','Road','Rail','Air','Ship');
		$reasonForTransportation = $supplyType .'-'.$subSupplyType[(int)$ewbJson->subSupplyType];
		$vehicle = (!empty($ewbJson->vehicleNo)) ? $ewbJson->vehicleNo : $ewbJson->transporterId ;
		$qrText = 'EWB No.: '.$ewbData->eway_bill_no.', From:'.$ewbJson->fromGstin.', Valid Untill: '.date("d/m/Y h:i:s A",strtotime($ewbData->valid_up_to));
		$ewbQrCode = $this->getQRCode($qrText,$eway_bill_no);
		$totalItams = ((count($ewbJson->itemList) - 1) > 0) ? ' (+'.(count($ewbJson->itemList) - 1). ' Items)' : '' ;
		$ewbPartB = '<br>';
		
		$ewbPartA = '<div class="barcode">'.$ewbQrCode.'</div>
					<table class="table ewbTable">
						<tr><th class="ewbTitle bg-light"colspan="3">PRINT E-WAY BILL</th></tr>
						<tr><th style="width:35%;">E-Way Bill No: </th><td style="width:80px;">&nbsp;</td><td>'.$ewbData->eway_bill_no.'</td></tr>
						<tr><th>E-Way Bill Date: </th><td>&nbsp;</td><td>'.date("d/m/Y h:i:s A",strtotime($ewbData->eway_bill_date)).'</td></tr>
						<tr><th>Generated By: </th><td>&nbsp;</td><td>'.$ewbJson->fromGstin.'-'.$ewbJson->fromTrdName.'</td></tr>
						<tr><th>Valid From: </th><td>&nbsp;</td><td>'.date("d/m/Y h:i:s A",strtotime($ewbData->eway_bill_date)).' ['.$ewbJson->transDistance.'Kms]</td></tr>
						<tr><th>Valid Until: </th><td>&nbsp;</td><td>'.date("d/m/Y h:i:s A",strtotime($ewbData->valid_up_to)).'</td></tr>
						
						<tr><th class="ewbTitle bg-light"colspan="3">PART – A</th></tr>
						<tr><th>GSTIN of Supplier </th><td>&nbsp;</td><td>'.$ewbJson->fromGstin.'-'.$ewbJson->fromTrdName.'</td></tr>
						<tr><th>Place of Dispatch </th><td>&nbsp;</td><td>'.$ewbJson->fromPlace.'-'.$ewbJson->fromPincode.'</td></tr>
						<tr><th>GSTIN of Recipient </th><td>&nbsp;</td><td>'.$ewbJson->toGstin.'-'.$ewbJson->toTrdName.'</td></tr>
						<tr><th>Place of Delivery </th><td>&nbsp;</td><td>'.$ewbJson->toPlace.'-'.$ewbJson->toPincode.'</td></tr>
						<tr><th>Document No. </th><td>&nbsp;</td><td>'.$ewbJson->docNo.'</td></tr>
						<tr><th>Document Date </th><td>&nbsp;</td><td>'.$ewbJson->docDate.'</td></tr>
						<tr><th>Value of Goods </th><td>&nbsp;</td><td><img src="'.$inrSymbol.'" width="10"> '.$ewbJson->totInvValue.'</td></tr>
						<tr><th>Transaction Type </th><td>&nbsp;</td><td>Combination of 2 and 3</td></tr>
						<tr><th>HSN Code </th><td>&nbsp;</td><td>'.$ewbJson->mainHsnCode.$totalItams.'</td></tr>
						<tr><th>Reason for Transportation </th><td>&nbsp;</td><td>'.$reasonForTransportation.'</td></tr>
						<tr><th>Transporter </th><td>&nbsp;</td><td>'.$ewbJson->transporterId.'</td></tr>
					</table>';
		if(!empty($ewbJson->transMode)):
			$ewbPartB = '<table class="table ewbBottomTable">
							<tr><th class="ewbTitle bg-light"colspan="7">PART – B</th></tr>
							<tr>
								<th>Mode</th><th>Vehicle / Trans<br>Doc No & Dt</th><th>From</th><th>Entered Date</th>
								<th>Entered By</th><th>CEWB No. (If any)</th><th>Multi Veh. Info (If any)</th>
							</tr>
							<tr>
								<td>'.$transMode[$ewbJson->transMode].'</td>
								<td>'.$vehicle.'</td>
								<td>'.$ewbJson->fromPlace.'</td>
								<td>'.date("d/m/Y<\b\\r />h:i:s A",strtotime($ewbData->eway_bill_date)).'</td>
								<td>'.$ewbJson->fromGstin.'</td>
								<td>-</td>
								<td>-</td>
							</tr>
						</table>';
		endif;
		$ewbBarcode = '<div class="barcode" style="margin-bottom:20px;"><barcode code="'.$ewbData->eway_bill_no.'" type="C128A" height="1" text="1" /><br>'.$ewbData->eway_bill_no.'</div>';
		
		$ewbHtml = $ewbPartA.$ewbPartB.$ewbBarcode;
		// echo $ewbHtml;
		$mpdf = $this->m_pdf->load();
		$pdfFileName=base_url('assets/uploads/eway_bill/'.$eway_bill_no.'.pdf');
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('P','','','','',10,10,10,10,10,10);
		$mpdf->WriteHTML('<div class="ewbOuter">'.$ewbHtml.'</div>');
		$mpdf->Output($pdfFileName,'I');
		
	}
	
	public function ewb_detail_pdf($eway_bill_no)
	{
		$ewbData = $this->db->where('eway_bill_no', $eway_bill_no)->get('eway_bill_master')->row();
		$invTable = ($ewbData->doc_type == 'INV') ? "sales_master" : "delivery_challan";
		$salesData = $this->db->where('eway_bill_no', $eway_bill_no)->get($invTable)->row();

		$inrSymbol = base_url('assets/images/inr.png');
		$logo = base_url('assets/images/wellsun_favicon.png');
		$orgData = $this->db->where("org_id", $salesData->unt_id)->get('organization')->row();
		//$ewbJson = json_decode($ewbData->json_data)->billLists[0];
		$ewbJsonData = json_decode($ewbData->json_data);
		if (!empty($ewbJsonData->billLists[0])) :
			$ewbJson = $ewbJsonData->billLists[0];
		else :
			$ewbJson = $ewbJsonData;
		endif;
		$supplyType = ($ewbJson->supplyType == 'O') ? 'Outward' : 'Inward';
		$subSupplyType = array('', 'Supply', 'Import', 'Export', 'Job Work', 'For Own Use', 'Job Work Returns', 'Sales Returns', 'Others', 'SKD/CKD', 'Line Sales', 'Recipient Not Known', 'Exhibition or Fairs');
		$transMode = array('', 'Road', 'Rail', 'Air', 'Ship');
		$reasonForTransportation = $supplyType . '-' . $subSupplyType[(int)$ewbJson->subSupplyType];
		$vehicle = (!empty($ewbJson->vehicleNo)) ? $ewbJson->vehicleNo : $ewbJson->transporterId;
		$qrText = 'EWB No.: ' . $ewbData->eway_bill_no . ', From:' . $ewbJson->fromGstin . ', Valid Untill: ' . date("d/m/Y h:i:s A", strtotime($ewbData->valid_up_to));
		$ewbQrCode = $this->getQRCode($qrText, $eway_bill_no,50);
		$totalItams = ((count($ewbJson->itemList) - 1) > 0) ? ' (+' . (count($ewbJson->itemList) - 1) . ' Items)' : '';
		$ewbPartB = '<br>';

		$ewbPartHeader = '<table style="padding-top:0px;margin-top:0px;margin-bottom:0px;border-bottom:1px solid #888;">
		<tr>
		<td style="width:30%;">' . date("d/m/y H:i A") . '</td>
		<td style="text-align:center;font-size:28;width:40%;font-weight:bold;">e-Way Bill</td>
		<td style="width:20%;font-size:12px;text-align:right;">E-Way Bill System</td>
		<td style="text-align:right;width:10%;">' . $ewbQrCode . '</td>
		</tr>
		</table>';
		$transactionType = "";
		if ($ewbJson->transactionType == 1) {
			$transactionType = "Regular";
		} elseif ($ewbJson->transactionType == 2) {
			$transactionType = "Bill To - Ship To";
		} elseif ($ewbJson->transactionType == 3) {
			$transactionType = "Bill From - Dispatch From";
		} elseif ($ewbJson->transactionType == 4) {
			$transactionType = "Combination of 2 and 3";
		}
		$outward_type = "";
		if ($ewbJson->supplyType == 'O') {
			$outward_type = "Outward";
		} else {
			$outward_type = "Inward";
		}
		$sub_outward_type = "";
		if ($ewbJson->subSupplyType == 1) {
			$sub_outward_type = "Supply";
		} 
		elseif ($ewbJson->subSupplyType == 2) {
			$sub_outward_type = "Import";
		} 
		elseif ($ewbJson->subSupplyType == 3) {
			$sub_outward_type = "Export";
		} 
		elseif ($ewbJson->subSupplyType == 4) {
			$sub_outward_type = "Job Work";
		} 
		elseif ($ewbJson->subSupplyType == 5) {
			$sub_outward_type = "For Own Use";
		} 
		elseif ($ewbJson->subSupplyType == 6) {
			$sub_outward_type = "Job Work Return";
		} 
		elseif ($ewbJson->subSupplyType == 7) {
			$sub_outward_type = "Sales Return";
		} 
		elseif ($ewbJson->subSupplyType == 8) {
			$sub_outward_type = "Others";
		} 
		elseif ($ewbJson->subSupplyType == 9) {
			$sub_outward_type = "SKD/CKD";
		} 
		elseif ($ewbJson->subSupplyType == 10) {
			$sub_outward_type = "Line Sales";
		} 
		elseif ($ewbJson->subSupplyType == 11) {
			$sub_outward_type = "Recipient Not Known";
		} 
		elseif ($ewbJson->subSupplyType == 12) {
			$sub_outward_type = "Exhibition or Fairs";
		} 
		$docType="";
		if($ewbJson->docType=='INV')
		{
			$docType="Tax Invoice";
		}
		elseif($ewbJson->docType=='CHL')
		{
			$docType="Delivery Challan";
		}
		$ewbPartA = '<table class="ewbTable" style="margin-top:0px;pedding-top:10px;"  cellpadding="5">
						<tr>
						<th colspan="4" style="text-align:left;">1. E-WAY BILL Details</th>
					
						</tr>
						<tr>
							<td  style="text-align:left;">E-Way Bill No: <b>' . $ewbData->eway_bill_no . '</b></td>
							<td  style="text-align:left;">Generated Date:<b>' . date("d/m/Y h:i:s A", strtotime($ewbData->eway_bill_date)) . '</b></td>
							<td  style="">Generated By:<b>' . $ewbJson->fromGstin . '</b></td>
						</tr>

						<tr>
							<td  style="">Valid Upto:<b>' . date("d/m/Y h:i:s A", strtotime($ewbData->valid_up_to)) . '</b></td>
							<td  style="">Mode: <b>' . $transMode[$ewbJson->transMode] . '</b></td>
							<td style="width:30%">Approx Distance:<b>' . $ewbJson->transDistance . 'Kms</b></td>
						</tr>

						<tr>
						<td>Type: <b>' . $outward_type.' - '.$sub_outward_type . '</b></td>
						<td>Document Details:<b>' . $docType . ' - ' . $ewbJson->docNo . ' - ' . $ewbJson->docDate . '</b></td>
						<td>Transaction type: <b>' . $transactionType . '</b></td>
						
						</tr>
					</table>';
		$partyData = $this->party->getParty($ewbData->party_id);
		$stateData = $this->db->where('id', $partyData->state_id)->get("states")->row();
		$ewbPartB = '<hr>
		<table class="ewbTable" style="border-bottom:1px solid #888;">
		<tr>
		<th colspan="2" style="text-align:left;">2. Address Details</th>
		</tr>
		
		</table>
		<table class="table ewbTable table-bordered text-left " style="border-bottom:1px solid #888;" >
		<tr >
		<th style="width:50%;border-right:1px solid;">From</th>
		<th style="width:50%;border-right:1px solid">To</th>
		</tr>
		<tr >
		<td  style="width:50%;border-right:1px solid" >
		GSTIN :  ' . $orgData->gstin . '
		</td>
		<td style="width:50%;border-right:1px solid">
		GSTIN : ' .  $partyData->gstin . '
		</td>
		</tr>
		
		<tr>
		<td style="width:50%;border-right:1px solid">'.$orgData->org_name.'</td>
		<td style="width:50%;">' . $partyData->party_name . '</td>
		</tr>
		<tr>
		<td style="width:50%;border-right:1px solid">'.$orgData->org_state.'</td>
		<td style="width:50%;">' . $stateData->name . '</td>
		</tr>
		<tr>
		<tr><td style="border-right:1px solid"></td><td style="border-right:1px solid"></td></tr>
		
		<tr>
		<td style="width:50%;border-right:1px solid;">:: Dispatch From ::</td>
		<td style="width:50%;">:: Ship To ::</td>
		</tr>
		<tr>
		<td style="width:50%;border-right:1px solid">' . $ewbJson->fromAddr1.$ewbJson->fromAddr2 . '</td>
		<td style="width:50%;">' . $ewbData->ship_address . '</td>
		</tr>
		<tr>
		<td style="width:50%;border-right:1px solid">' . $ewbJson->fromPlace . ' - ' . $ewbJson->fromPincode . '</td>
		<td style="width:50%;">' . $ewbJson->toPlace . ' - '  . $ewbJson->toPincode . '</td>
		</tr>
		<tr>
		<td style="width:50%;border-right:1px solid">' . (!empty($ewbJson->fromState)?$ewbJson->fromState:'') . '</td>
		<td style="width:50%;">' . (!empty($ewbJson->toState)?$ewbJson->toState:'')  . ' </td>
		</tr>
	
		</table>';

		$ewbPartC = '<table class="ewbTable"><tr><th colspan="5" style="text-align:left;">3. Goods Details</th></tr></table>
					<table style="margin-top:0px;pedding-top:10px;" class="table ewbBottomTable">
						<tr>
						<th style="">HSN Code</th>
						<th style="width:35%">Product Name & Desc. </th>
						<th style="">Quantity</th>
						<th style="">Taxable Amount Rs.</th>
						<th style="">Tax Rate (C+S+I+Cess+CessNon.Advol)</th>
						</tr>';
		$j=0;
		foreach ($ewbJson->itemList as $row) {
			$ewbPartC .= '<tr style="border-top:1px solid">
							<td  style="">' . $row->hsnCode . '</td>
							<td  style="">' . $row->productName . '<br>' . $row->productDesc . '</td>
							<td  style="">' . $row->quantity . '</td>
							<td  style="">' . $row->taxableAmount . '</td>
							<td  style="">' . (floatval($row->sgstRate) + floatval($row->cgstRate) + floatval($row->igstRate) + floatval($row->cessRate) + floatval($row->cessNonAdvol)) . '</td>
							</tr>';$j++;
		}
		if($j<11)
		{
			for($i=$j;$i<11;$i++)
			{$ewbPartC .= '<tr style="border-top:1px solid"><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>';}
		}
		$ewbPartC .=	'</table>
		<table class="ewbTable" cellpadding="5">
						<tr>
						<td style="">Tot. Taxable Amt : <b>' . $ewbJson->totalValue . '</td>
						<td style="">CGST Amt : <b>' . $ewbJson->cgstValue . '</b></td>
						<td style="">SGST Amt : <b>' . $ewbJson->sgstValue . '</td>
						<td style="">IGST Amt : <b>' . $ewbJson->igstValue . '</td>
						<td style="">CESS Amt : <b>' . $ewbJson->cessValue . '</td>
						<td style="">CESS Non.Advol Amt : <b>' . $ewbJson->cessNonAdvolValue . '
						</td>
						</tr>
						<tr>
						
						<td style="text-align:left;font-size:11px">Other Amt : <b>' . $ewbJson->totalValue . '</b></td>
						<td style="text-align:left;font-size:11px" colspan="5">Total Inv.Amt : <b>' . $ewbJson->totInvValue . '</b></td>
						</tr>
						</table>';

		$ewbPartD = '<hr><table style="margin-top:0px;border-bottom:1px solid #888;" class="ewbTable" cellpadding="5">
		<tr>
		<th colspan="2" style="text-align:left;">4. Transportation Details</th>
		</tr>
		<tr>
		<td style="">Transporter ID & Name : <b>' . $ewbJson->transporterId . ' & ' . $ewbJson->transporterName . '</b> </td>
		<td style="">Transporter Doc. No & Date :<b>' . $ewbJson->transDocNo . ' & ' . $ewbJson->transDocDate . '</td>
		</tr>
		</table>';

		if (!empty($ewbJson->transMode)) :
			$ewbPartE = '<table style="margin-top:0px;"  cellpadding="5"><tr><th colspan="2" style="text-align:left;">5. Vehicle Details</th></tr></table>
			<table class="table ewbBottomTable" cellpadding="5">
				
							<tr>
								<th style="">Mode</th>
								<th style="">Vehicle / Trans<br>Doc No & Dt</th>
								<th style="">From</th><th style="">Entered Date</th>
								<th style="">Entered By</th>
								<th style="">CEWB No. (If any)</th>
								<th style="">Multi Veh. Info (If any)</th>
							</tr>
							<tr>
								<td style="">' . $transMode[$ewbJson->transMode] . '</td>
								<td style="">' . $vehicle . '</td>
								<td style="">' . $ewbJson->fromPlace . '</td>
								<td style="">' . date("d/m/Y<\b\\r />h:i:s A", strtotime($ewbData->eway_bill_date)) . '</td>
								<td style="">' . $ewbJson->fromGstin . '</td>
								<td style="">-</td>
								<td style="">-</td>
							</tr>
						</table>';
		endif;
		$ewbBarcode = '<div class="barcode" style="margin-bottom:20px;"><barcode code="' . $ewbData->eway_bill_no . '" type="C128A" height="0.7" text="1" /><br>' . $ewbData->eway_bill_no . '</div>';

		$ewbHtml = $ewbPartHeader . $ewbPartA . $ewbPartB . $ewbPartC . $ewbPartD . $ewbPartE . $ewbBarcode;
		//echo $ewbHtml;exit;
		//$mpdf = $this->m_pdf->load();
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName = base_url('assets/uploads/eway_bill/' . $eway_bill_no . '.pdf');
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet, 1);
		$mpdf->SetDisplayMode('fullpage');
		//$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('P', '', '', '', '', 5, 5, 0, 0, 0, 0);
		$mpdf->WriteHTML($ewbHtml);
		$mpdf->Output($pdfFileName, 'I');
	}
}
?>