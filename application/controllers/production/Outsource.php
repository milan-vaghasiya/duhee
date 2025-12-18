<?php
class Outsource extends MY_Controller
{
	private $indexPage = "production/outsource/index";
	private $movementForm = "production/jobcard/production_form";
	private $challanIndex = "production/outsource/challan_index";
	private $challanForm = "production/outsource/challan_modal";
	private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);

	public function __construct()
	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Outsource";
		$this->data['headData']->controller = "production/outsource";
	}

	public function index($status = 0)
	{
		$this->data['tableHeader'] = getProductionHeader("outSource");
		$this->data['vendorData'] = $this->party->getVendorList();
		$this->data['status'] = 0;
		$this->load->view($this->indexPage, $this->data);
	}

	public function getDTRows($status = 0, $dates = '')
	{
		$data = $this->input->post();
		$data['status'] = $status;
		if (!empty($dates)) {
			$data['from_date'] = explode('~', $dates)[0];
			$data['to_date'] = explode('~', $dates)[1];
		}
		$sendData = array();
		$i = 1;

		$result = $this->outsource->getDTRows($data);
		foreach ($result['data'] as $row) :
			$row->sr_no = $i++;
			$row->controller = $this->data['headData']->controller;
			$sendData[] = getOutsourceData($row);
		endforeach;
		$result['data'] = $sendData;
		$this->printJson($result);
	}

	public function createOutsourceChallan()
	{
		$postData = $this->input->post();
		$this->data['vendor_id'] = $postData['party_id'];
		$this->data['resultData'] = $this->outsource->getPendingOSTransaction(['vendor_id' => $postData['party_id']]);
		$this->data['gstPercentage'] = $this->gstPercentage;
		$this->load->view($this->challanForm, $this->data);
	}

	public function getPendingOSTransaction($postData = [])
	{
		if (empty($postData)) {
			$postData = $this->input->post();
		}
		$resultData = $this->outsource->getPendingOSTransaction($postData);
		$transData = "";
		$i = 1;
		if (!empty($resultData)) :
			foreach ($resultData as $row) :
				$transData .= '<tr>
					<td class="text-center fs-12">
						<input type="checkbox" id="md_checkbox_' . $i . '" name="id[]" class="filled-in chk-col-success challanCheck" data-rowid="' . $i . '" value="' . $row->id . '"  ><label for="md_checkbox_' . $i . '" class="mr-3"></label>
					</td>
					<td class="text-center fs-12">' . $row->job_number . '</td>
					<td class="text-cente fs-12">' . formatDate($row->job_date) . '</td>
					<td class="text-center fs-12">' . $row->full_name . '</td>
					<td class="text-center fs-12">' . floatVal($row->qty) . '</td>
					<td class="text-center fs-12">' . $row->pending_qty . '</td>
					<td class="text-center fs-12">
						<input type="hidden" id="out_qty' . $i . '" value="' . floatVal($row->pending_qty) . '">                   
						<input type="text" id="ch_qty' . $i . '" name="ch_qty[]" data-rowid="' . $i . '" class="form-control challanQty floatOnly" value="0" disabled>
						<div class="error chQty' . $row->id . '"></div>
					</td>
				</tr>';
				$i++;
			endforeach;
		else :
			$transData .= '<tr><td colspan="7" class="text-center">No data available in table</td></tr>';
		endif;
		return $transData;
		$this->printJson(['status' => 1, 'transData' => $transData]);
	}

	public function save()
	{
		$data = $this->input->post();
		$errorMessage = array();
		
		$data['trans_prefix'] = (!empty($data['trans_prefix'])) ? $data['trans_prefix'] : 'VC/' . n2y(date('Y'));
		if (empty($data['challan_id']) && empty($data['trans_no'])) :
			$data['trans_no'] = $this->outsource->nextChallanNo();
		endif;

		if (!isset($data['id']))
			$errorMessage['orderError'] = "Please Check atleast one order.";
			
		if (!empty($data['id'])) :
			foreach ($data['id'] as $key => $value):
				if(empty($data['ch_qty'][$key]) && empty($data['challan_id'])) :
					$errorMessage['chQty' . $value] = "Qty. is required.";
				endif;
				
				$data['price'][$key] = floatval($data['price'][$key]);
				if (empty($data['price'][$key])) :
			        $errorMessage['chPrice' . $value] = "Price is required.";
			    endif;
			    
			    $data['gst_per'][$key] = floatval($data['gst_per'][$key]);
			    if(empty($data['gst_per'][$key])) :
			        $errorMessage['chGst' . $value] = "GST(%) is required.";
			    endif;
			endforeach;
		endif;

		if (!empty($errorMessage)) :
			$this->printJson(['status' => 0, 'message' => $errorMessage]);
		else :
			$data['created_by'] = $this->loginId; //print_r($data);exit;
			$this->printJson($this->outsource->save($data));
		endif;
	}

	public function vendorInward()
	{
		$data = $this->input->post();
		$id =  $data['id'];
		$outwardData = $this->processMovement->getApprovalData($data['id']);
		$transData = $this->processMovement->getOutwardTransPrint($data['job_trans_id']);
		// $outwardData->pqty = $transData->qty - $transData->outsource_qty;
		$outwardData->pqty = ($transData->qty * $outwardData->output_qty) - $transData->outsource_qty;
		$this->data['dataRow'] = $outwardData;
		$this->data['dataRow']->entry_type = 4;
		$this->data['dataRow']->ref_id = $data['job_trans_id'];
		$this->data['dataRow']->vendor_id = $transData->vendor_id;
		$this->data['dataRow']->mfg_by = $transData->mfg_by;
		$this->data['outwardTrans'] = $this->processMovement->getOutwardTrans($outwardData->id, 4)['htmlData'];
		$this->data['vendorList'] = $this->party->getVendorList();
		$this->data['operatorList'] = $this->employee->getMachineOperatorList();
		$this->data['idleReasonList'] = $this->comment->getIdleReason();
		$this->data['rejectionComments'] = $this->comment->getCommentList();
		$this->data['shiftData'] = $this->shiftModel->getShiftList();
		$this->data['masterOption'] = $this->processMovement->getMasterOptions();
		$prdPrsData = $this->item->getPrdProcessDataProductProcessWise(['item_id' => $outwardData->product_id, 'process_id' => $outwardData->in_process_id]);
		$this->data['cycle_time'] = $prdPrsData->cycle_time;
		$this->data['machineData'] = $this->item->getMachineTypeWiseMachine($prdPrsData->typeof_machine);
		$jobCardData = $this->jobcard->getJobcard($outwardData->job_card_id);
		$jobProcess = explode(",", $jobCardData->process);

		$stageHtml = '<option value="">Select Stage</option>
                    <option value="0" data-process_name="Row Material">Row Material</option>';
		if (!empty($outwardData->in_process_id)) {
			$in_process_key = array_keys($jobProcess, $outwardData->in_process_id)[0];
			foreach ($jobProcess as $key => $value) :
				if ($key <= $in_process_key) :
					$processData = $this->process->getProcess($value);
					$stageHtml .= '<option value="' . $processData->id . '" data-process_name="' . $processData->process_name . '">' . $processData->process_name . '</option>';
				endif;
			endforeach;
		}
		$this->data['dataRow']->stage = $stageHtml;
		$processArray = explode(",", '0,'.$jobCardData->process);
        $in_process_key = array_keys($processArray, $outwardData->in_process_id)[0];
        $this->data['heatData'] = $this->processMovement->getHeatData(['job_card_id'=>$outwardData->job_card_id,'process_id'=>$processArray[($in_process_key-1)]]);
		$this->load->view($this->movementForm, $this->data);
	}

	public function delete()
	{
		$id = $this->input->post('id');
		if (empty($id)) :
			$this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
		else :
			$this->printJson($this->outsource->delete($id));
		endif;
	}

	function jobworkOutChallan($id)
	{
		$jobData = $this->outsource->getVendorChallan($id);
		$companyData = $this->db->where('id', 1)->get('company_info')->row();
		$response = "";
		$logo = base_url('assets/images/logo.png');

		$topSectionO = '<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td style="width:30%;"><img src="' . $logo . '" style="height:40px;"></td>
							<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">JOB WORK CHALLAN</td>
							<td style="width:30%;" class="text-right"><span style="letter-spacing:1px;">GST No.:<span style="letter-spacing:1px;">' . $companyData->company_gst_no . '</span><br><b>Original Copy</b></span></td>
						</tr>
					</table>
					<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
						<tr><td class="org-address text-center" style="font-size:13px;">' . $companyData->company_address . '</td></tr>
					</table>';
		$topSectionV = '<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td style="width:30%;"><img src="' . $logo . '" style="height:40px;"></td>
							<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">JOB WORK CHALLAN</td>
							<td style="width:30%;" class="text-right"><span style="letter-spacing:1px;">GST No.:<span style="letter-spacing:1px;">' . $companyData->company_gst_no . '</span><br><b>Vendor Copy</b></span></td>
						</tr>
					</table>
					<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
						<tr><td class="org-address text-center" style="font-size:13px;">' . $companyData->company_address . '</td></tr>
					</table>';
		$baseSection = '<table class="vendor_challan_table">
							<tr style="">
								<td rowspan="2" style="width:70%;vertical-align:top;">
									<b>TO : ' . $jobData->party_name . '</b><br>
									<span style="font-size:12px;">' . $jobData->party_address . '<br>
									<b>GSTIN No. :</b> <span style="letter-spacing:2px;">' . $jobData->gstin . '</span>
								</td>
								<td class="text-left" height="25"><b>Challan No. :</b> ' . $jobData->trans_number . ' </td>
							</tr>
							<tr>
								<td class="text-left" height="25"><b>Challan Date :</b> ' . date("d-m-Y", strtotime($jobData->trans_date)) . ' </td>
							</tr>
						</table>';
		$itemList = '<table class="table table-bordered jobChallanTable">
					<tr class="text-center bg-light-grey">
						<th>#</th>
						<th>Part Code</th>
						<th>Job No.</th>
						<th>Process</th>
						<th style="width:15%;">Nos.</th>
					</tr>';

		$i = 1;
		$itemCode = "";
		$jobNo = "";
		$deliveryDate = "";
		$processName = "";
		$remark = "";
		$inQty = "";
		$weight = "";
		$totalOut = 0;
		$totalWeight = 0;
		$blnkRow = 4;
		$challanData = $this->outsource->getVendorChallanTransData($id);
		if (!empty($challanData)) {
			foreach ($challanData as $jobTransData) :
				$itemList .= '<tr>
                        <td style="vertical-align:top;padding:5px;">' . $i++ . '</td>
                        <td style="vertical-align:top;padding:5px;">' . $jobTransData->full_name . '</td>
                        <td style="vertical-align:top;padding:5px;">' . $jobTransData->job_number . '</td>
                        <td style="vertical-align:top;padding:5px;">' . $jobTransData->process_name . '</td>
                        <td class="text-center" style="vertical-align:top;padding:5px;">' . ((!empty($jobTransData->qty)) ? sprintf('%0.0f', $jobTransData->qty) : '') . '</td>
                    </tr>';
				$totalOut += sprintf('%0.0f', $jobTransData->qty);
			endforeach;
		}

		for ($j = $i; $j < $blnkRow; $j++) :
			$itemList .= '<tr>
    			<td style="vertical-align:top;padding:5px;" height="50px"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
			</tr>';
		endfor;

		$itemList .= '<tr class="bg-light-grey">';
		$itemList .= '<th class="text-right" style="font-size:14px;" colspan="4">Total</th>';
		$itemList .= '<th class="text-center" style="font-size:14px;">' . sprintf('%0.0f', $totalOut) . '</th>';
		$itemList .= '</tr></table>';

		$bottomTable = '<table class="table table-bordered" style="width:100%;">';
		$bottomTable .= '<tr>';
		$bottomTable .= '<td class="text-center" style="width:50%;border:0px;"></td>';
		$bottomTable .= '<td class="text-center" style="width:50%;font-size:1rem;border:0px;"><b>For, ' . $companyData->company_name . '</b></td>';
		$bottomTable .= '</tr>';
		$bottomTable .= '<tr><td colspan="2" height="60" style="border:0px;"></td></tr>';
		$bottomTable .= '<tr>';
		$bottomTable .= '<td class="text-center" style="vertical-align:bottom !important;font-size:1rem;border:0px;">Received By</td>';
		$bottomTable .= '<td class="text-center" style="font-size:1rem;border:0px;">Authorised Signatory</td>';
		$bottomTable .= '</tr>';
		$bottomTable .= '</table>';

		$originalCopy = '<div style="width:210mm;height:140mm;">' . $topSectionO . $baseSection . $itemList . $bottomTable . '</div>';
		$vendorCopy = '<div style="width:210mm;height:140mm;">' . $topSectionV . $baseSection . $itemList . $bottomTable . '</div>';

		$pdfData = $originalCopy . "<br>" . $vendorCopy;

		$mpdf = $this->m_pdf->load();
		$pdfFileName = 'DC-REG-' . $id . '.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet, 1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));

		$mpdf->AddPage('P', '', '', '', '', 5, 5, 5, 5, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName, 'I');
	}

	public function pendingForChallan()
	{
		$this->data['tableHeader'] = getProductionHeader("pendingChallan");
		$this->data['vendorData'] = $this->party->getVendorList();
		$this->load->view($this->challanIndex, $this->data);
	}

	public function getPendingChallanDTRows($status = 0, $dates = '')
	{
		$data = $this->input->post();
		$data['status'] = $status;
		if (!empty($dates)) {
			$data['from_date'] = explode('~', $dates)[0];
			$data['to_date'] = explode('~', $dates)[1];
		}
		$sendData = array();
		$i = 1;

		$result = $this->outsource->getPendingChallanDTRows($data);  //print_r($this->db->last_query());
		foreach ($result['data'] as $row) :
			$row->sr_no = $i++;
			$row->controller = $this->data['headData']->controller;
			$sendData[] = getPendingChallanData($row);
		endforeach;
		$result['data'] = $sendData;
		$this->printJson($result);
	}
	
	function jobworkOutsourceChallan($id){ 
		$jobData = $this->outsource->getVendorChallan($id);
		$challanData = $this->outsource->getVendorChallanTransForPrint($id);
		$getMaterialIssuaData = $this->jobcard->getIssueMaterialDetail($challanData->job_card_id, $challanData->ref_item_id);
		$companyData = $this->db->where('id', 1)->get('company_info')->row();
		$response = "";
		$logo = base_url('assets/images/logo.png');
		
		$gstHtml = "";$gst_per = $igst_per = $gst_price = $igst_price = $total_gst = $total_amount = 0;
		$gstin = (!empty($jobData->gstin) ? substr($jobData->gstin, 0, 2) : "");
		$challanPrice = (!empty($challanData->price) ? ($challanData->price*$challanData->qty) : 0);
		if(!empty($gstin) && $gstin == 24){
			$gst_per = ((!empty($challanData->gst_per) && $challanData->gst_per > 0) ? ($challanData->gst_per/2) : 0);
			$gst_price = ((!empty($challanPrice) && $gst_per > 0) ? (($challanPrice*$gst_per)/100) : 0);
			$total_gst = (!empty($gst_price) ? ($gst_price*2) : 0);
			$total_amount = (!empty($challanPrice) ? ($challanPrice + ($gst_price*2)) : 0);
		}else{
			$igst_per = ((!empty($challanData->gst_per) && $challanData->gst_per > 0) ? $challanData->gst_per : 0);
			$igst_price = ((!empty($challanPrice) && $igst_per > 0) ? (($challanPrice*$igst_per)/100) : 0);
			$total_gst = $igst_price;
			$total_amount = (!empty($challanPrice) ? ($challanPrice + $igst_price) : 0);
		}
		$pdfData = '<table class="vendor_challan_table">
		    <tr>
		        <td class="text-center" colspan="3">
		            For movement of goods under section 143 read with rule 55 of the cgst,2017 for jobwork from one factory for processing/operation and subsequent return to the parent factory.
		        </td>
		    </tr>
			<tr>
				<td style="width:50%;vertical-align:top;">
					<b>Name & Address of the Supplier/Manufacturer</b><br><br>
					<span style="font-size:20px;"><b>' . $companyData->company_name . '</b></span><br><br>
					<span style="font-size:12px;">' . $companyData->company_address . '<br>
					<b>GSTIN No. :</b> <span style="letter-spacing:2px;">' . $companyData->company_gst_no . '</span>
				</td>
				<td class="text-center" height="25"  colspan="2">Challan for movement of Inputs of partially processed goods under GST Rule</td>
			</tr>
			<tr>
				<td class="text-left" height="25">
					1. Description of goods: <b>'.(!empty($jobData->desc_goods)?$jobData->desc_goods:'').'</b>
				</td>
				<th class="text-right" colspan="2">
					Original
				</th>
			</tr>
			<tr>
				<td class="text-left" style="width:40%;vertical-align:top;">
					<b>2. Identification marks & number if any: <br></b>
					<b>Part Code: </b>'.$challanData->item_code.'<br/>
					<b>Lot No./Date: </b>'.$challanData->wo_no.' / '.formatDate($challanData->job_date).'<br/>
					<b>Grade: </b>'.$challanData->material_grade.'<br/>
					<b>Heat No.:</b> '.$getMaterialIssuaData->heat_no.'<br/>
					<b>Bar Dia:</b> '.$challanData->dia.'<br/>
				</td>
				<th class="text-center" style="width:40%;vertical-align:top;">
					To be filled by the processing unit in Original duplicate challans
				</th>
				<th class="text-right" style="width:20%;vertical-align:top;">
					Serial Number <br><br>
					'.$jobData->trans_number.'
				</th>
			</tr>
			<tr>
				<td class="text-left" height="25" style="vertical-align:top;">
					3. Quantity (Nos/Weight/Liter/Mwter etc.): <br><br>
					<b>Qty: '.$challanData->qty.'</b><br>
					<b>Weight/Pcs: '.$challanData->finished_weight.'</b><br>
					<b>Total Weight: '.round(($challanData->finished_weight * $challanData->qty),2).'</b>
				</td>
				<td class="text-left" colspan="2" style="vertical-align:top;">
					1. Date and time of Dispatch of finished goods to parents factory/
					another manufacturer Entry No & Date of receipt on the processing or 
					date & time of dispatch of finish goods without payments of duty for
					export under bond or on payment of duty for home consuption 
					G.P. No. and date Quantum of duty paid(Both figure & words) <br><br><br><br><br><br>
				</td>
			</tr>
			<tr>
				<td>
					<b>4.Value:</b> '.(!empty($challanPrice) ? 'Rs. '.$challanPrice.'/- (Appx.)' : "").'<br/>
					<b>CGST@'.$gst_per.'%</b> '.$gst_price.' <b>SGST@'.$gst_per.'%</b> '.$gst_price.'<br/>
					<b>IGST@'.$igst_per.'%</b> '.$igst_price.' <b>Total GST</b> '.$total_gst.'<br/>
					<b>Total Amount: </b> '.$total_amount.'
				</td>
				<td rowspan="3" colspan="2" style="vertical-align:top;">
					2. Quantity despatch (Nos/Weight/Liter/Meter etc.) as entered in account <br><br>
				</td>
			</tr>
			<tr>
				<td>5.Tarrif Classification:<br>
					HSN Code.: <b>'.$jobData->hsn_code.'</b>
				</td>
			</tr>
			<tr>
				<td>
					6.Date Time Of Issue: <br><br>
					Date Of Issue : <b>'.formatDate($jobData->trans_date).'</b><br>
					Time Of Issue : <b>'.formatDate($jobData->created_at, 'H:i:s').'</b>
				</td>
			</tr>
			<tr>
				<td style="vertical-align:top;">
					7. Nature of Processing/ Manufacturing: <br><b>'.$jobData->nature_process.'('.$challanData->process_name.')</b>
				</td>
				<td style="vertical-align:top;" colspan="2">
					3. Nature of Processing/ Manufacturing done: <br>
				</td>
			</tr>
			<tr>
				<td style="vertical-align:top;">
					8.Factory Place  of Processing / Manufacturing: <br><br>
					<b>'.$jobData->party_name.'</b><br>
					'.$jobData->party_address.'<br><br>
					<b>GST NO.: '.$jobData->gstin.'</b>
				</td>
				<td colspan="2"  style="vertical-align:top;" >
					4. Quantity  of waste material Returned to the parent factory:
				</td>
			</tr>
			<tr>
				<td class="text-left" style="vertical-align:top;">
					9. Expected duration of Processing/Manufacturing : 1 Year <br><br><br><br><br><br><br><br><br>
				</td>
				<th class="text-left" style="vertical-align:top;" colspan="2">
					Name & Address of the processor: <br><br><br><br><br><br><br><br><br>
				</th>
			</tr>
			<tr>
				<td class="text-right">
					For, '.$companyData->company_name.'<br><br><br>

					Authorised Signatory
				</td>
				<td class="text-left" colspan="2"> 
					
					<table class="top-table" style="width:100%;padding-top:0px">
						<tr>
							<td colspan="2" style="width:100%;padding-top:5px;padding-bottom:30px">Date: </td>
						</tr>
						<tr>
							<td>Place: </td>
							<td class="text-right" style="width:50%">Signature of processor</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
			    <td colspan="3">
			        Remarks : In light of provision of section 143 of CGST Act,2017,Inputs and/or capital goods send for job work and bringing back after completion of job work is not liable for payment of GST. Hence No ITC to be claimed of GST mentioned in this challan
			    </td>
			</tr>
		</table>';

		$htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
			<tr>
				<td style="width:30%;"><img src="' . $logo . '" style="height:60px;"></td>
				<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">JOB WORK CHALLAN</td>
				<td style="width:30%;" class="text-right"><span style="letter-spacing:1px;"></td>
			</tr>
		</table>';

		$htmlFooter = '<table class="table top-table" style="margin-top:10px;">
			<tr>
				<td style="width:50%;">CH. No. & Date : '.$jobData->trans_number.' ('.formatDate($jobData->created_at, 'd-m-Y H:i:s').')</td>
				<td style="width:50%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
			</tr>
		</table>';

		$mpdf = $this->m_pdf->load();
		$pdfFileName= $jobData->trans_number.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,45));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,25,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	
	function jobworkOutsourceChallan00($id){ 
		$jobData = $this->outsource->getVendorChallan($id);
		$challanData = $this->outsource->getVendorChallanTransForPrint($id);
		$companyData = $this->db->where('id', 1)->get('company_info')->row();
		$response = "";
		$logo = base_url('assets/images/logo.png');

		$pdfData = '<table class="vendor_challan_table">
			<tr>
				<td style="width:50%;vertical-align:top;">
					<b>Name & Address of the Supplier/Manufacturer</b><br><br>
					<span style="font-size:20px;"><b>' . $companyData->company_name . '</b></span><br><br>
					<span style="font-size:12px;">' . $companyData->company_address . '<br>
					<b>GSTIN No. :</b> <span style="letter-spacing:2px;">' . $companyData->company_gst_no . '</span>
				</td>
				<td class="text-center" height="25"  colspan="2">Challan for movement of Inputs of partially processed goods under GST Rule</td>
			</tr>
			<tr>
				<td class="text-left" height="25">
					1. Description of goods: <b>'.(!empty($jobData->desc_goods)?$jobData->desc_goods:'').'</b>
				</td>
				<th class="text-right" colspan="2">
					Original
				</th>
			</tr>
			<tr>
				<td class="text-left" style="width:40%;vertical-align:top;">
					2. Identification marks & number if any: <br>
					SAP No.: <b>'.(!empty($jobData->sap_no)?$jobData->sap_no:'').'</b>
					<b>'.$challanData->full_name.'</b> <br>
					<b>'.$challanData->category_name.'</b> <br><br>
				</td>
				<th class="text-center" style="width:40%;vertical-align:top;">
					To be filled by the processing unit in Original duplicate challans
				</th>
				<th class="text-right" style="width:20%;vertical-align:top;">
					Serial Number <br><br>
					'.$jobData->trans_number.'
				</th>
			</tr>
			<tr>
				<td class="text-left" height="25" style="vertical-align:top;">
					3. Quantity (Nos/Weight/Liter/Mwter etc.): <br><br>
					<b>Qty: '.$challanData->qty.'</b><br>
					<b>Weight/Pcs: '.$challanData->wt_pcs.'</b><br>
					<b>Total Weight: '.round(($challanData->wt_pcs * $challanData->qty),2).'</b>
				</td>
				<td class="text-left" colspan="2" style="vertical-align:top;">
					1. Date and time of Dispatch of finished goods to parents factory/
					another manufacturer Entry No & Date of receipt on the processing or 
					date & time of dispatch of finish goods without payments of duty for
					export under bond or on payment of duty for home consuption 
					G.P. No. and date Quantum of duty paid(Both figure & words) <br><br><br><br><br><br>
				</td>
			</tr>
			<tr>
				<td>4.Value: <b>'.$jobData->total_value.'</b></td>
				<td rowspan="3" colspan="2" style="vertical-align:top;">
					2. Quantity despatch (Nos/Weight/Liter/Meter etc.) as entered in account <br><br>
				</td>
			</tr>
			<tr>
				<td>5.Tarrif Classification:<br>
					HSN Code.: <b>'.$jobData->hsn_code.'</b>
				</td>
			</tr>
			<tr>
				<td>
					6.Date Time Of Issue: <br><br>
					Date Of Issue : <b>'.formatDate($jobData->trans_date).'</b><br>
					Time Of Issue : <b>'.formatDate($jobData->created_at, 'H:i:s').'</b>
				</td>
			</tr>
			<tr>
				<td style="vertical-align:top;">
					7. Nature of Processing/ Manufacturing: <br><b>'.$jobData->nature_process.'('.$challanData->process_name.')</b>
				</td>
				<td style="vertical-align:top;" colspan="2">
					3. Nature of Processing/ Manufacturing done: <br>
				</td>
			</tr>
			<tr>
				<td style="vertical-align:top;">
					8.Factory Place  of Processing / Manufacturing: <br><br>
					<b>'.$jobData->party_name.'</b><br>
					'.$jobData->party_address.'<br><br>
					<b>GST NO.: '.$jobData->gstin.'</b>
				</td>
				<td colspan="2"  style="vertical-align:top;" >
					4. Quantity  of waste material Returned to the parent factory:
				</td>
			</tr>
			<tr>
				<td class="text-left" style="vertical-align:top;">
					9. Expected duration of Processing/Manufacturing <br><br><br>
				</td>
				<th class="text-left" style="vertical-align:top;" colspan="2">
					Name & Address of the processor: <br><br><br>
				</th>
			</tr>
			<tr>
				<td class="text-right">
					For, '.$companyData->company_name.'<br><br><br>

					Authorised Signatory
				</td>
				<td class="text-left" colspan="2"> 
					
					<table class="top-table" style="width:100%;padding-top:0px">
						<tr>
							<td colspan="2" style="width:100%;padding-top:5px;padding-bottom:30px">Date: </td>
						</tr>
						<tr>
							<td>Place: </td>
							<td class="text-right" style="width:50%">Signature of processor</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>';

		$htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
			<tr>
				<td style="width:30%;"><img src="' . $logo . '" style="height:60px;"></td>
				<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">JOB WORK CHALLAN</td>
				<td style="width:30%;" class="text-right"><span style="letter-spacing:1px;"></td>
			</tr>
		</table>';

		$htmlFooter = '<table class="table top-table" style="margin-top:10px;">
			<tr>
				<td style="width:50%;">CH. No. & Date : '.$jobData->trans_number.' ('.formatDate($jobData->created_at, 'd-m-Y H:i:s').')</td>
				<td style="width:50%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
			</tr>
		</table>';

		$mpdf = $this->m_pdf->load();
		$pdfFileName= $jobData->trans_number.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,45));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,30,30,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
}
