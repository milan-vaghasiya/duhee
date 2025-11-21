<?php
class JobWork extends MY_Controller{
    private $indexPage = "job_work/index";
    private $indexReturn = "job_work/indexReturn";
    private $indexCompleted = "job_work/indexCompleted";
    private $returnForm = "job_work/job_work_return";
    private $outForm = "job_work/job_work_form";
    private $approveForm = "job_work/job_work_approve";
    
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Job Work";
		$this->data['headData']->controller = "jobWork";
		$this->data['headData']->pageUrl = "jobWork";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
    
	//Changed By Karmi @11/05/2022
    public function getDTRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->jobWork->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
			$row->job_status=''; $row->print_status_tab='';
            $row->sr_no = $i++;
			$returnData = $this->jobWork->getUnapprovedCount($row->id);
			$unApprovred_count = count($returnData);
			
			if(!empty($unApprovred_count)):
				$row->job_status = '<span class="badge badge-pill badge-warning m-1"><b>Unapproved( '.$unApprovred_count.' )</b></span>';
			elseif($row->received_qty <= 0): 
				$row->job_status = '<span class="badge badge-pill badge-danger m-1"><b>Pending</b></span>';
			else: 
				$row->job_status = '<span class="badge badge-pill badge-success m-1"><b>Approved</b></span>';
			endif;
			if(!empty($row->print_status)):
			    $row->print_status_tab = '<span class="badge badge-pill badge-success m-1"><b>'.date('d-m-Y H:i:s',strtotime($row->print_status)).'</b></span>';
			endif;
			
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getJobWorkData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }   
    
	public function indexReturn()
    {
        $this->data['tableHeader'] =  getProductionHeader('jobWorkReturn');
        $this->load->view($this->indexReturn, $this->data);
    }
    
	//Changed By Karmi @17/05/2022
    public function getDTReturnRows($status=2){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->jobWork->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $row->job_trans_no; 
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getJobWorkReturnTabData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }  
    
	public function indexCompleted()
    {
		$this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexCompleted, $this->data);
    }
    
	//Changed By Karmi @17/05/2022
    public function getDTCompletedRows($status=1){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->jobWork->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;  $row->print_status_tab='';
            $row->controller = $this->data['headData']->controller;
			$row->job_status = '<span class="badge badge-pill badge-success m-1"><b>Completed</b></span>';
			
			if(!empty($row->print_status)):
			    $row->print_status_tab = '<span class="badge badge-pill badge-success m-1"><b>'.date('d-m-Y H:i:s',strtotime($row->print_status)).'</b></span>';
			endif;
            $sendData[] = getJobWorkData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    } 
    
	//Changed By Karmi @10/05/2022
    public function jobWorkOut(){
		$this->data['jobworkPrefix'] = "JW/".$this->shortYear."/";
        $this->data['jobworkNo'] = $this->jobWork->getNextJobworkNo();
		$this->data['vendorList']=$this->party->getVendorList();
		$this->data['vehicleData'] = explode(',', $this->item->getMasterOptions()->vehicle_no);
		//$this->data['productList'] = $this->item->getItemList(); 
		//$this->data['processList'] = $this->jobWork->getProcessListForJobWork();
		$this->load->view($this->outForm,$this->data);
	}
	
    public function getWorkOrderListVendorWise(){
        $vendor_id=$this->input->post('vendor_id');
        $woData=$this->jobWork->getWorkOrderListVendorWise($vendor_id);       
        $options='<option value="">Select Work Order</option>';
        if(!empty($woData)){
            foreach($woData as $row){
                $options.='<option value="'.$row->id.'">'.$row->trans_number.'</option>';
            }
        }
        $this->printJson(['status'=>1,'options'=>$options]);
    }
    
    public function getWorkOrderTransList(){
        $job_order_id=$this->input->post('job_order_id');
        $woData=$this->jobWork->getWorkOrderTransList($job_order_id);       
        $options='<option value="">Select Item</option>';
        if(!empty($woData)){
            foreach($woData as $row){
                $options.='<option value="'.$row->id.'" data-item_id="'.$row->item_id.'" data-item_name="'.$row->full_name.'" data-process_id="'.$row->process_id.'" data-price="'.$row->price.'" data-com_unit="'.$row->com_unit.'" data-unit_name="'.$row->unit_name.'" data-com_unit_name="'.$row->com_unit_name.'">'.$row->full_name.' ['.$row->process_name.']</option>';
            }
        }
        $this->printJson(['status'=>1,'options'=>$options]);
    }
    
    //Changed By Karmi @10/05/2022
	public function save(){ 
		$data = $this->input->post(); 
		$errorMessage = array();
		if(empty($data['trans_number']))
			$errorMessage['trans_number'] = "Jobwork is required.";
		if(empty($data['trans_date']))
			$errorMessage['trans_date'] = "Date is required.";
		if(empty($data['vendor_id']))
			$errorMessage['vendor_id'] = "Vendor is required.";
		if(empty($data['qty']))
			$errorMessage['qty'] = "Qty is required.";
		if(empty($data['job_order_trans_id'][0]))
			$errorMessage['generalError'] = "Item Details is required.";
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$orderTransData = Array(); $itemData = Array(); $com_qty =0;$total_value=0;$cgst_amount=0;$sgst_amount=0;$igst_amount=0;$net_amount=0;
			foreach($data['trans_ref_id'] as $key=>$value):
				$orderTransData = $this->jobWork->getJobWorkOrderTransData($value);
				
				$com_qty = $orderTransData->wpp * $data['qty'][$key]; 
				$total_value = $com_qty * $orderTransData->value_rate;
				$cgst_amount = ($total_value * $orderTransData->cgst) / 100;
				$sgst_amount = ($total_value * $orderTransData->sgst) / 100;
				$igst_amount = ($total_value * $orderTransData->igst) / 100;
				$net_amount = $total_value + $igst_amount ;
				$itemData['id'] = $data['trans_id'];
				$itemData['ref_id'][] = "";
				$itemData['job_order_id'][] = $orderTransData->order_id;
				$itemData['item_id'][] = $orderTransData->item_id;
				$itemData['location_id'] = $data['location_id'];
				$itemData['batch_no'] = $data['batch_no'];
				$itemData['process_id'][] = $orderTransData->process_id;
				$itemData['qty'] = $data['qty'];
				$itemData['wpp'][] = $orderTransData->wpp;
				$itemData['variance'][] = $orderTransData->variance;
				$itemData['scarp_per_pcs'][] = $orderTransData->scarp_per_pcs;
				$itemData['scarp_rate_pcs'][] = $orderTransData->scarp_rate_pcs;
				$itemData['value_rate'][]= $orderTransData->value_rate;
				$itemData['unit_id'][] = $orderTransData->com_unit;
				$itemData['com_qty'][] =  $com_qty;
				$itemData['price'][]= $orderTransData->process_charge;
				$itemData['total_value'][] = $total_value;
				$itemData['cgst_amount'][] = $cgst_amount;
				$itemData['sgst_amount'][] = $sgst_amount;
				$itemData['igst_amount'][] = $igst_amount;
				$itemData['net_amount'][] = $net_amount;
				$itemData['remark'] = $data['trans_remark'];
			endforeach;
			$masterData = [
				'id' => $data['id'],
				'trans_no' => $data['trans_no'],
				'trans_prefix' => $data['trans_prefix'],
				'trans_number' => $data['trans_number'],
				'trans_date' => $data['trans_date'],
				'vendor_id' => $data['vendor_id'],
				'vendor_name' => $data['vendor_name'],
				//'job_order_id' => $data['job_order_id'],
				'vehicle_no' => $data['vehicle_no'],
				'ewb_no' => $data['ewb_no'],
				'remark' => $data['remark'],
				'created_by' => $this->session->userdata('loginId')
			];
            $this->printJson($this->jobWork->saveJobworkOutward($masterData,$itemData));
		endif;	
	}
	
	//Changed By Karmi @10/05/2022
	public function edit(){
		$id = $this->input->post('id');
		$dataRow=$this->jobWork->getJobWork($id); //print_r($id);exit;
		$this->data['dataRow'] = $dataRow;
		$this->data['vendorList']=$this->party->getVendorList();
		$this->data['orderList']=$this->jobWork->getWorkOrderListVendorWise($dataRow->vendor_id);
		$this->data['itemList']=$this->jobWork->getWorkOrderTransList($dataRow->job_order_id);
		$this->data['productList'] = $this->item->getItemList(); 
		$this->data['processList'] = $this->jobWork->getProcessListForJobWork();
		$this->data['vehicleData'] = explode(',', $this->item->getMasterOptions()->vehicle_no);
		$this->load->view($this->outForm,$this->data);
	}
	
	public function delete(){
		$id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobWork->deleteJobwork($id));
        endif;
	}
	//Changed By Karmi @10/05/2022
    public function jobWorkReturn(){
		$id = $this->input->post('id');
		$dataRow=$this->jobWork->getJobWorkForReturn($id); //print_r($dataRow); exit;
		$transData = $this->jobWork->getJobworkTransBasedOnTransId($id);
		$this->data['convertedProduct'] = $this->jobWorkOrder->getJobworkChallanTrans($transData->job_order_id,$dataRow[0]->item_id,$dataRow[0]->process_id); 
		$this->data['dataRow'] = $dataRow; 
        $this->data['locationlist'] = $this->store->getStoreLocationList();
        $this->data['jobWorkReturnData'] = $this->jobWork->getJobWorkReturnData($id); 
		$this->load->view($this->returnForm,$this->data);
	}
	
	//Changed By Karmi @10/05/2022
	public function jobWorkReturnSave(){
		$data = $this->input->post(); //print_r($data);exit;
		$errorMessage = array();
		if(empty($data['trans_date']))
			$errorMessage['trans_date'] = "Date is required.";
		if($data['in_qty'][0] > 0  || $data['in_com_qty'][0] > 0 || $data['rej_qty'][0] > 0 || $data['wp_qty'][0] > 0):
			if($data['in_qty'][0] > $data['pending_qty'][0]):
				$errorMessage['in_qty'.$data['ref_id'][0]] = "Invalid Qty.";
			endif;
		else:
			$errorMessage['in_qty'.$data['ref_id'][0]] = "In Qty is Required.";	
		endif;
		// if($data['in_com_qty'][0] < 0):
		// 	$errorMessage['in_com_qty'.$data['ref_id'][0]] = "Comm. Qty is Required.";
		// else:
		    $revComQty = (($data['in_qty'][0] * $data['com_qty'][0]) / $data['qty'][0]);
		    $variance = ($revComQty * $data['variance'][0]) / 100;
		    $plus = $revComQty + $variance; $minus = $revComQty - $variance;
		    if($minus > $data['in_com_qty'][0] &&  $plus < $data['in_com_qty'][0]):
			    $errorMessage['com_qty'.$data['ref_id'][0]] = "Comm. Qty is Invalid.";
		    endif;
		//endif; 
		unset($data['variance'][0]);
		
		if(empty($data['job_order_trans_id'][0]))
			$errorMessage['job_order_trans_id'] = "Converted Item is required.";
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$data['created_by'] = $this->loginId;
			$this->printJson($this->jobWork->jobWorkReturnSave($data));
		endif;
	}
	function jobworkOutChallan00($id){
		$jobData = $this->jobWork->getJobworkTransForPrint($id);
		$partyData = $this->party->getParty($jobData->vendor_id);
		$orderData = $this->jobWork->getorderTrans($jobData->job_order_trans_id);
		$cityData = $this->party->getcity($partyData->city_id);
		$stateData = $this->party->getstate($partyData->state_id) ;
		$city = (!empty($cityData)) ? $cityData->name : '';
		$state = (!empty($stateData)) ? $stateData->name : '';
		$space = '&nbsp;';
		//print_r($partyData);exit;
		$companyData = $this->db->where('id',1)->get('company_info')->row();
		$response="";$logo=base_url('assets/images/logo.png');
		
		
		$baseSection = '<table >
							<tr style="">
								<td rowspan="2" style="width:70%;vertical-align:top;">
									<b>'.$partyData->party_name.'</b><br>
									<span style="font-size:12px;">'.$partyData->party_address.'<br></span>
									<span style="font-size:12px;">'.$city.'('.$state.')<br></span>
									<span style="font-size:12px;">'.$space.$partyData->gstin.'</span><br>
								</td>
								<td class="text-right" style="padding-right: 40px; " height="25"> '.getPrefixNumber($jobData->trans_prefix,$jobData->trans_no).' </td>
							</tr>
							<tr>
								<td class="text-right"   style="padding-right: 40px; "height="25"> '.date("d-m-Y",strtotime($jobData->trans_date)).' </td>
								
							</tr>
							<tr>
								
								<td class="text-left"   style="padding-left: 120px; "height="25"> '.$jobData->vehicle_no.'<br>'.$jobData->ewb_no.' </td>
								<td class="text-right"   style="padding-right: 70px; "height="25"> '.$jobData->process_name.' </td>
								
							</tr>
							<tr>
							</tr>
						</table>';
		
			
			$i=1;$taxble_amt = $jobData->qty * $jobData->value_rate;
			$cgst_amt = ($taxble_amt * $orderData->cgst)/100;
			$sgst_amt = ($taxble_amt * $orderData->sgst)/100;
			$total = $taxble_amt + $cgst_amt  + $sgst_amt;
			
			$itemList ='<table style="padding-top:20px;"><tr>
				<td style="vertical-align:top;padding:10px;">'.$jobData->full_name.'</td>
				<td style="vertical-align:top;padding:10px;">'.$orderData->hsn_code.'</td>
				<td style="vertical-align:top;padding:10px;">'.$jobData->item_code.'</td>
				<td style="vertical-align:top;padding:10px;">'.$jobData->qty.'</td>
				<td style="vertical-align:top;padding:10px;">'.$jobData->unit_name.'</td>
				<td style="vertical-align:top;padding:10px;">'.$taxble_amt.'</td>
			</tr></table>';
			$cgstSection = '<table style="padding-top:20px;">
								<tr>
									<td style="vertical-align:top;padding-left:50px;padding-top:10px;">'.$orderData->cgst.'&nbsp;&nbsp;&nbsp;&nbsp;'.$cgst_amt.'</td>
									<td style="vertical-align:top;padding-left:50px;padding-top:10px;"></td>
								</tr>
								<tr>
									<td style="vertical-align:top;padding-left:50px;padding-top:10px;">'.$orderData->sgst.'&nbsp;&nbsp;&nbsp;&nbsp;'.$sgst_amt.'</td>
									<td style="vertical-align:top;padding-left:150px;padding-top:10px;">'.$total.'</td>
								</tr>
							</table>';
		$originalCopy = '<div style="width:210mm;height:140mm;">'.$baseSection.$itemList.$cgstSection.'</div>';
		
		$pdfData = $originalCopy;
		
		//print_r($pdfData); exit;
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	
	function jobworkOutChallan($id){
		$jobData = $this->jobWork->getJobworkTransForPrint($id);
		$this->jobWork->updateChallanPrintStatus($id);
		$partyData = $this->party->getParty($jobData->vendor_id);
		//$orderData = $this->jobWork->getorderTransBasedOnOrderId($jobData->job_order_id);
		$orderData = $this->jobWork->getJobWorkOrderTransItemWise($jobData->job_order_id,$jobData->item_id,$jobData->process_id);
		//print_r($orderData);exit;
		$cityData = $this->party->getcity($partyData->city_id);
		$stateData = $this->party->getstate($partyData->state_id) ;
		$city = (!empty($cityData)) ? $cityData->name : '';
		$state = (!empty($stateData)) ? $stateData->name : '';
		$space = '&nbsp;';
		//print_r($partyData);exit;
		$companyData = $this->db->where('id',1)->get('company_info')->row();
		$response="";$logo=base_url('assets/images/logo.png');
		
		$taxble_amt = $jobData->qty * $orderData->value_rate;
		$cgst_amt = ($taxble_amt * $orderData->cgst)/100;
		$sgst_amt = ($taxble_amt * $orderData->sgst)/100;
		$netAmt = $taxble_amt + $cgst_amt  + $sgst_amt;
		
		// Challan Detail - Left side
		$vendorText = '<span style="font-size:15px;font-weight:bold;">'.$partyData->party_name.'</span>';
		$vaddressText = '<span style="font-size:13px;">'.$partyData->party_address.'</span> <span style="font-size:13px;">'.$city.'('.$state.')</span>';
		$vgstText = '<span style="font-size:13px;">'.$space.$partyData->gstin.'</span>';
		$vehicleText = '<span style="font-size:13px;">'.$jobData->vehicle_no.'</span>';
		$ewbText = '<span style="font-size:13px;">'.$jobData->ewb_no.'</span>';
		
		// Challan Detail - Right side
		$chnoText = '<span style="font-size:14px;font-weight:bold;">'.$jobData->trans_prefix.$jobData->trans_no.'</span>';
		$chdateText = '<span style="font-size:14px;font-weight:bold;">'.date("d-m-Y",strtotime($jobData->trans_date)).'</span>';
		$processText = '<p style="font-size:14px;font-weight:bold;">'.$jobData->process_name.'</p>';
		
		// Product Detail
		$itemText = '<span style="font-size:12px;">'.$jobData->full_name.'</span>';
		$hsnText = '<span style="font-size:12px;">'.$orderData->hsn_code.'</span>';
		$itemCodeText = '<span style="font-size:12px;">'.$jobData->item_code.'</span>';
		$qtyText = '<span style="font-size:12px;">'.$jobData->qty.'</span>';
		$unitText = '<span style="font-size:12px;">'.$jobData->unit_name.'</span>';
		$valueText = '<span style="font-size:12px;font-weight:bold;">'.sprintf('%0.2f',$taxble_amt).'</span>';
		
		// Bottom Summary
		$cgstText = '<span style="font-size:12px;">'.sprintf('%0.2f',$cgst_amt).'</span>';
		$sgstText = '<span style="font-size:12px;">'.sprintf('%0.2f',$sgst_amt).'</span>';
		$lotText = '<span style="font-size:12px;">L010</span>';
		$netamtText = '<span style="font-size:13px;font-weight:bold;">'.sprintf('%0.2f',$netAmt).'</span>';
		
		//print_r($vendorText.'*'.$vaddressText.'*'.$vgstText.'*'.$vehicleText.'*'.$ewbText.$chnoText.'*'.$chdateText.'*'.$processText.'*'.$itemText.'*'.$hsnText.'*'.$itemCodeText.'*'.$qtyText.'*'.$unitText.'*'.$valueText.'*'.$cgstText.'*'.$sgstText.'*'.$lotText.'*'.$netamtText); exit;
		
		$pdfFileName=$jobData->trans_prefix.$jobData->trans_no.'_'.time().'.pdf';
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [203, 203]]);
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->setTitle($jobData->trans_prefix.$jobData->trans_no);
		
		// Vendor Detail
		$mpdf->WriteFixedPosHTML ($vendorText,18,52,90,5); // [ Text, ML,MT,W,H,OVERFLOW]
		$mpdf->WriteFixedPosHTML ($vaddressText,18,57,90,12);
		$mpdf->WriteFixedPosHTML ($vgstText,45,67.5,90,4);
		$mpdf->WriteFixedPosHTML ($vehicleText,37,71.5,90,4);
		$mpdf->WriteFixedPosHTML ($ewbText,37,76,90,4);
		
		// Challan Detail
		$mpdf->WriteFixedPosHTML ($chnoText,147,51,50,5);
		$mpdf->WriteFixedPosHTML ($chdateText,147,57,50,5);
		$mpdf->WriteFixedPosHTML ($processText,132,68,60,25);
		
		// Product Detail
		$mpdf->WriteFixedPosHTML ($itemText,18,88,50,15);
		$mpdf->WriteFixedPosHTML ($hsnText,69,88,20,5);
		$mpdf->WriteFixedPosHTML ($itemCodeText,87,88,30,6);
		$mpdf->WriteFixedPosHTML ($qtyText,111,88,20,6);
		$mpdf->WriteFixedPosHTML ($unitText,133,88,20,6,'auto');
		$mpdf->WriteFixedPosHTML ($valueText,164,88,20,6,'auto');
		
		// Bottom Summary
		$mpdf->WriteFixedPosHTML ($cgstText,27,103,20,5);
		$mpdf->WriteFixedPosHTML ($sgstText,27,110,20,5);
		$mpdf->WriteFixedPosHTML ($lotText,76,108,30,6);
		$mpdf->WriteFixedPosHTML ($netamtText,105,110,30,6);
		
		$mpdf->Output($pdfFileName,'I');
	}
	//Created By Karmi @11/05/2022
	public function getBatchNo(){
		$item_id = $this->input->post('item_id');
        $location_id = $this->input->post('location_id');
        $batchData = $this->jobWork->locationWiseBatch($item_id,$location_id);
        $options = '<option value="">Select Batch No.</option>';
        foreach($batchData as $row):
			if($row->qty > 0):
				$options .= '<option value="'.$row->batch_no.'">'.$row->batch_no.'</option>';
			endif;
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }
	//Created By NYN @15/05/2022  
	public function getJobOrderByVendor(){
		$vendor_id = $this->input->post('vendor_id');
        $jobOrderData = $this->jobWork->getJobOrderByVendor($vendor_id);
		$itemOptions = '<option value="">Select Item</option>';
		if(!empty($jobOrderData)):
			foreach($jobOrderData as $row):
				$itemOptions .= '<option value="'.$row->order_id.'" data-ref_id="'.$row->id.'"  data-item_id="'.$row->id.'" data-item_name="'.$row->full_name.'"  data-price="'.$row->process_charge.'"  data-unit_name="'.$row->unit_name.'"  data-process_name="'.$row->process_name.'" data-process_id="'.$row->process_id.'" data-unit_id="'.$row->com_unit.'" >'.$row->full_name.' ['.$row->process_name.']</option>';
			endforeach;
		endif;
        $this->printJson(['status'=>1,'itemOptions'=>$itemOptions]);
	}
	
	
    //Created By JP @06/06/2022
    public function vehicleSearch(){
		$this->printJson($this->jobWork->vehicleSearch());
	}
	
	public function approveJobWork(){
		$id = $this->input->post('id');
		$this->data['jobWorkReturnData'] = $this->jobWork->getJobWorkReturnData($id); 
		$this->load->view($this->approveForm,$this->data);
	}

	//Created By Karmi @27/06/2022
	public function approveReturn(){
		$id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobWork->approveReturn($id));
        endif;
	}

	//Created By Karmi @27/06/2022
	public function rejectReturn(){
		$id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobWork->rejectReturn($id));
        endif;
	}
}
?>