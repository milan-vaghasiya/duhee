<?php
class JobWorkOrder extends MY_Controller{
    private $indexPage = "job_work_order/index";
    private $orderForm = "job_work_order/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Job Work Order";
		$this->data['headData']->controller = "jobWorkOrder";
		$this->data['headData']->pageUrl = "jobWorkOrder";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=1){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->jobWorkOrder->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getJobWorkOrderData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	//Changed By Karmi @10/05/2022
    public function addOrder(){
        $this->data['jobOrderPrefix'] = "JWO/".$this->shortYear."/";
        $this->data['jobOrderNo'] = $this->jobWorkOrder->getNextOrderNo();
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->data['productList'] = $this->item->getItemList(3); 
        $this->data['processList'] = $this->jobWork->getProcessListForJobWork();
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['hsnData'] = $this->item->getHsnList();
        $this->load->view($this->orderForm,$this->data);
    }

	public function getVendorProcessList(){
		$vendor_id = $this->input->post('vendor_id');
		$vendorData = $this->party->getParty($vendor_id);
		$options = '';
		$processList = (!empty($vendorData->process_id))?explode(",",$vendorData->process_id):array();
		foreach($processList as $key=>$value):
			$processData = $this->jobWork->getProcessJobWork($value);
			$options .= '<option value="'.$processData->id.'">'.$processData->process_name.'</option>';
		endforeach;
		$this->printJson(['status'=>1,'options'=>$options]);
	}
	//Changed By Karmi @10/05/2022
    public function save(){
        $data = $this->input->post(); 
        $errorMessage = array();
        
        if(empty($data['vendor_id']))
            $errorMessage['vendor_id'] = "Vendor Name is required.";
        if(empty($data['trans_no']))
            $errorMessage['trans_no'] = "Order No is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$masterData = [
				'id' => $data['id'],
				'order_date' => $data['order_date'],
				'trans_no' => $data['trans_no'],
				'trans_prefix' => $data['trans_prefix'],
				'trans_number' => $data['trans_number'],
				'vendor_id' => $data['vendor_id'],
				'created_by' => $this->session->userdata('loginId')
			];

			$itemData = [
				'id'=>$data['trans_id'],
				'item_id'=>$data['item_id'],
				'converted_product'=>$data['converted_product'],
				'process_id'=>$data['process_id'],
				'com_unit'=>$data['com_unit'],
				'process_charge'=>$data['process_charge'],
				'wpp'=>$data['wpp'],
				'hsn_code'=>$data['hsn_code'],
				'value_rate'=>$data['value_rate'],
				'variance'=>$data['variance'],				
				'scarp_per_pcs'=>$data['scarp_per_pcs'],				
				'scarp_rate_pcs'=>$data['scarp_rate_pcs']				
			];
			

            $this->printJson($this->jobWorkOrder->save($masterData,$itemData));
        endif;
    }
	//Changed By Karmi @10/05/2022
    public function edit($id){ 
        
        $this->data['dataRow'] = $this->jobWorkOrder->getJobWorkOrder($id);
		$this->data['vendorList'] = $this->party->getVendorList();
        $this->data['productList'] = $this->item->getItemList(3);
        $this->data['processList'] = $this->jobWork->getProcessListForJobWork();
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['hsnData'] = $this->item->getHsnList();

		$this->load->view($this->orderForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobWorkOrder->delete($id));
        endif;
    }

    function jobworkOrderChallan($id)
	{
		$jobData = $this->jobWorkOrder->getJobworkOutData($id);
        $jobData->process = "";
        if(!empty($jobData->process_id)):
            $processIds = explode(",",$jobData->process_id);
            $processName = array();
            foreach($processIds as $key=>$value):
                $processName[] = $this->jobWork->getProcessJobWork($value)->process_name; 
            endforeach;
            $jobData->process = implode(", ",$processName);
        endif;

		$companyData = $this->db->where('id',1)->get('company_info')->row();
		$response="";$logo=base_url('assets/images/logo.png');
		
		$pdays = (!empty($jobData->production_days)) ? "+".$jobData->production_days." day" : "+0 day";
		
		$delivery_date = date('d-m-Y',strtotime($pdays, strtotime($jobData->created_at)));
		
		$topSectionO ='<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td style="width:30%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">JOB WORK ORDER</td>
							<td style="width:30%;" class="text-right"><span style="letter-spacing:1px;">GST No.:<span style="letter-spacing:1px;">'.$companyData->company_gst_no.'</span></td>
						</tr>
					</table>
					<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
						<tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
					</table>';
		$topSectionV ='<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td style="width:30%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">JOB WORK ORDER</td>
							<td style="width:30%;" class="text-right"><span style="letter-spacing:1px;">GST No.:<span style="letter-spacing:1px;">'.$companyData->company_gst_no.'</span><br><b>Vendor Copy</b></span></td>
						</tr>
					</table>
					<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
						<tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
					</table>';
		$baseSection = '<table class="vendor_challan_table">
							<tr style="">
								<td rowspan="2" style="width:70%;vertical-align:top;">
									<b>TO : '.$jobData->party_name.'</b><br>
									<span style="font-size:12px;">'.$jobData->party_address.'<br>
									<b>GSTIN No. :</b> <span style="letter-spacing:2px;">'.$jobData->gstin.'</span>
								</td>
								<td class="text-left" height="35"><b>Order No. :</b> '.getPrefixNumber($jobData->jwo_prefix,$jobData->jwo_no).' </td>
							</tr>
							<tr>
								<td class="text-left" height="35"><b>Date :</b> '.date("d-m-Y",strtotime($jobData->created_at)).' </td>
							</tr>
						</table>';
		$itemList='<table class="table table-bordered jobChallanTable">
					<tr class="text-center bg-light-grey">
						<th>Material Description</th><th style="width:15%;">'.(($jobData->rate_per == 1)?"Pcs.":"Kg.").'</th><th style="width:15%;">Rate</th><th style="width:15%;">Amount</th>
					</tr>
					<tr>
						<td style="vertical-align:top;height:25px;padding:5px;">
							<b>Item Code : </b>'.$jobData->item_code.(($jobData->rate_per == 2)?' ('.sprintf('%0.0f', $jobData->qty).' Pcs.)':"").'
						</td>
						<td class="text-center" rowspan="4" style="vertical-align:top;padding:5px;">'.sprintf('%0.0f', ($jobData->rate_per == 1)?$jobData->qty:$jobData->qty_kg).'</td>
						<td class="text-center" rowspan="4" style="vertical-align:top;padding:5px;">'.sprintf('%0.2f', $jobData->rate).'</td>
						<td class="text-center" rowspan="4" style="vertical-align:top;padding:5px;">'.sprintf('%0.2f', $jobData->amount).'</td>
					</tr>
					<tr>
						<td style="vertical-align:top;height:25px;padding:5px;"><b>Delivery Date : </b>'.$delivery_date.'</td>
					</tr>
					<tr>
						<td style="vertical-align:top;height:25px;padding:5px;"><b>Process : </b>'.$jobData->process.'</b></td>
					</tr>
					<tr>
						<td style="vertical-align:top;height:150px;padding:5px;"><b>Remarks : </b>'.$jobData->remark.'</td>
					</tr>';
		$itemList.='<tr class="bg-light-grey">';
			$itemList.='<th class="text-right" style="font-size:14px;">Total</th>';
			$itemList.='<th class="text-center" style="font-size:14px;">'.sprintf('%0.0f', ($jobData->rate_per == 1)?$jobData->qty:$jobData->qty_kg).'</th>';
			$itemList.='<th class="text-center" style="font-size:14px;">'.sprintf('%0.2f', $jobData->rate).'</th>';
			$itemList.='<th class="text-center" style="font-size:14px;">'.sprintf('%0.2f', $jobData->amount).'</th>';
		$itemList.='</tr>';		
		$itemList.='</table>';
		
		$bottomTable='<table class="table table-bordered" style="width:100%;">';
			$bottomTable.='<tr>';
				$bottomTable.='<td class="text-center" style="width:50%;border:0px;"></td>';
				$bottomTable.='<td class="text-center" style="width:50%;font-size:1rem;border:0px;"><b>For, '.$companyData->company_name.'</b></td>';
			$bottomTable.='</tr>';
			$bottomTable.='<tr><td colspan="2" height="60" style="border:0px;"></td></tr>';
			$bottomTable.='<tr>';
				$bottomTable.='<td class="text-center" style="vertical-align:bottom !important;font-size:1rem;border:0px;">Received By</td>';
				$bottomTable.='<td class="text-center" style="font-size:1rem;border:0px;">Authorised Signatory</td>';
			$bottomTable.='</tr>';
		$bottomTable.='</table>';
		
		// $originalCopy = '<div style="width:210mm;height:140mm;">'.$topSectionO.$baseSection.$itemList.$bottomTable.'</div>';
		$originalCopy = '<div style="width:210mm;">'.$topSectionO.$baseSection.$itemList.$bottomTable.'</div>';
		$vendorCopy = '<div style="width:210mm;height:140mm;">'.$topSectionV.$baseSection.$itemList.$bottomTable.'</div>';
		
		$pdfData = $originalCopy;
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='JWO -REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	
	//Created By Karmi @16/05/2022
    function jobworkOrderChallanFull($id)
	{
		$this->data['jobData'] = $jobData = $this->jobWorkOrder->getJobWorkOrder($id);
		$this->data['vendorData'] = $this->party->getParty($jobData->vendor_id);
		$this->data['companyData'] = $this->jobWorkOrder->getCompanyInfo();
		$response="";$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');		
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';		
		$pdfData = $this->load->view('job_work_order/printjw',$this->data,true);		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='JWO -REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->AddPage('P','','','','',5,5,39,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	
	//Created By Karmi @16/05/2022
	public function closeOrder(){
        $id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->jobWorkOrder->closeOrder($id));
		endif;
    }
}
?>