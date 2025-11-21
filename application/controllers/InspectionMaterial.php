
<?php
class InspectionMaterial extends MY_Controller
{
    private $indexPage = "inspection_material/index";
    private $form = "inspection_material/form";
    private $inspection_trans_view="inspection_material/inspection_trans_view";
    private $pendingRegrinding="inspection_material/pending_regrinding";
    private $regrinding_challan_form ="inspection_material/regrinding_challan_form";
    private $regrinding_challan ="inspection_material/regrinding_challan";
    private $receive_challan ="inspection_material/receive_challan";
    private $receive_item_list ="inspection_material/receive_item_list";
    private $regrinding_inspection = "inspection_material/regrinding_inspection";
    private $inspection_form = "inspection_material/inspection_form";
    private $re_regrinding_index = "inspection_material/re_regrinding_index";
    private $dimension = ["0"=>'',"1"=>'Only Endface','2'=>'OD','3'=>'OD/Endface','4'=>'CR-0.5','5'=>'CR-2.0','6'=>'CR-4.0','7'=>'Regrinding & RGRC','8'=>'RGRC'];

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Store Inspection";
        $this->data['headData']->controller = "inspectionMaterial";
        $this->data['headData']->pageUrl = "inspectionMaterial";
    }

    public function index($status = 0)
    {
        $this->data['status'] = $status;
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->data['reasonList'] = $this->regrindingReason->getRegrindingReasonList();
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($status = 0)
    {
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->inspection->getDTRows($data);
        $sendData = array(); $i=1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->status = $data['status'];
            $row->controller = "inspectionMaterial";
            $row->return_qty = $row->qty;
            if($status == 2){
                $row->statusText = '<span class="badge badge-pill badge-success m-1"><b>Complete</b></span>';
            }
            $sendData[] = getInspectionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addInspection()
    {
        // $this->data['issueData'] = $this->issueRequisition->getIssueMaterialList();
        $issueData = $this->issueRequisition->getIssueMaterialList();
        $issueDataOptions = '<option>Select Issue No.</option>';
        if (!empty($issueData)) {
            foreach ($issueData as $row) {
                $inspData = $this->inspection->getInspectionData($row->id);
                $issueData = $this->issueRequisition->getIssueMaterialLog($row->id);
                $returnData = $this->inspection->getReturnMatrialData($row->id);
                $pendingQty = (($returnData->used_qty + $returnData->fresh_qty + $returnData->broken_qty + $returnData->miss_qty) - (!empty($inspData->qty) ? $inspData->qty : 0));
                if ($pendingQty > 0) {
                    $issueDataOptions .= '<option value="' . $row->id . '" data-item_id="' . $row->req_item_id . '" data-item_name="' . $row->full_name . '">' . sprintf("ISU%05d", $row->log_no) . ' [ Item : ' . $row->full_name . ' , Issue Qty : ' . $row->req_qty . ']</option>';
                }
            }
        }
        $this->data['issueDataOptions'] = $issueDataOptions;
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->load->view($this->form, $this->data);
    }

    public function getInspectionData($id='')
    {
        if(!empty($id)){$data['issue_no']=$id;}else{$data = $this->input->post();}
        $inspData = $this->inspection->getInspectionData($data['issue_no']);
        $issueData = $this->issueRequisition->getIssueMaterialLog($data['issue_no']);
        $returnData = $this->inspection->getReturnMatrialData($data['issue_no']);
        $pendingQty = (($returnData->used_qty + $returnData->fresh_qty + $returnData->broken_qty + $returnData->miss_qty) - (!empty($inspData->qty) ? $inspData->qty : 0));
        $html = '<table class="table jp-table text-center">
                        <tr class="lightbg">
                            <th>Issue Qty</th>
                            <th>Missed Qty</th>
                            <th>Broken Qty</th>
                            <th>Used Qty</th>
                            <th>Fresh Qty</th>
                            <th>Inspected Qty</th>
                            <th>Pending Qty</th>
                        </tr>
                        <tr>
                            <td>' . $issueData->req_qty . '</td>
                            <td>' . $returnData->miss_qty . '</td>
                            <td>' . $returnData->broken_qty . '</td>
                            <td>' . $returnData->used_qty . '</td>
                            <td>' . $returnData->fresh_qty . '</td>
                            <td>' . (!empty($inspData->qty) ? $inspData->qty : 0) . '</td>
                            <td>' . $pendingQty . '<input type="hidden" id="pending_qty" name="pending_qty" value="' . $pendingQty . '"></td>
                        </tr>
                   </table>';
        if(!empty($id)){ return $html;}else{ $this->printJson(['status' => 1, 'inspDataHtml' => $html]);}
       
    }

    public function save()
    {
        $data = $this->input->post();
        if (empty($data['ref_id'])) 
            $errorMessage['ref_id'] = "Issue No. is required.";

        if (empty($data['qty'][0]) || $data['qty'][0] == 0) :
            $errorMessage['genral_error'] = "Qty is required.";
        else :
            if (array_sum($data['qty']) > $data['pending_qty'])
                $errorMessage['genral_error'] = "Qty is Invalid.";
        endif;


        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');

            $this->printJson($this->inspection->save($data));
        endif;
    }

    public function delete()
    {
        $id = $this->input->post('id');
        $issue_id = $this->input->post('issue_id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Something went wrong...Please try again.']);
        else :
          
            $result=$this->inspection->delete($id);
            $inspectionSummary=$this->getInspectionData($issue_id);
            $inspectionTrans=$this->inspection->getInspectionTrans($issue_id);
            $inspectionTransHtml='';
            if (!empty($inspectionTrans)) {
                $i = 1;
                foreach ($inspectionTrans as $row) {
                    $inspection_status = "";
                    if ($row->inspection_status == 1) {
                        $inspection_status = 'Used Return';
                    } elseif ($row->inspection_status == 2) {
                        $inspection_status = ' Fresh Return';
                    } elseif ($row->inspection_status == 3) {
                        $inspection_status = 'Move to Scrap';
                    } elseif ($row->inspection_status == 4) {
                        $inspection_status = 'Send to Regrinding';
                    } elseif ($row->inspection_status == 5) {
                        $inspection_status = 'Convert to Other Item';
                    } elseif ($row->inspection_status == 6) {
                        $inspection_status = 'Accepted Missed Item';
                    } elseif ($row->inspection_status == 7) {
                        $inspection_status = 'Unaccepted Missed Item';
                    }
                   
                    $inspectionTransHtml.='<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->trans_date).'</td>
                    <td>'.$row->qty.'</td>
                    <td>'.$inspection_status.'</td>
                    <td>'. $row->reason.'</td>
                    <td> <a class="btn btn-outline-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trashTrans(' .$row->id . ',' .$row->ref_id . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a></td>
                </tr>';
           
                }
            }
            else{
                $inspectionTransHtml.='<tr><th colspam="6">No Data Available</th></tr>';
            }
            $this->printJson(['status'=>1,'inspectionSummary'=>$inspectionSummary,'inspectionTrans'=>$inspectionTransHtml]);
        endif;
    }

    public function inspectionView(){
        $id = $this->input->post('id');
        $this->data['inspData'] = $this->inspection->getInspectionDataById($id);
        
        $this->data['locationData'] = $this->stockTransac->getStoreLocationList(['store_type'=>'0,4,15,16','group_store_opt'=>1,'final_location'=>1])['storeGroupedArray']; 
        $this->load->view($this->inspection_trans_view, $this->data);
    }

    public function saveInspection(){
        $data = $this->input->post();
        $totalQty = $data['used_qty'] + $data['fresh_qty'] + $data['scrap_qty'] + $data['regranding_qty'] + $data['convert_qty'] + $data['broken_qty'] + $data['miss_qty'];
        if (empty($data['id'])) 
            $errorMessage['id'] = "Issue No. is required.";

        if (!empty($data['convert_qty'])):
            if(empty($data['convert_item_id']))
                $errorMessage['convert_qty'.$data['id']] = "Convert Item is required.";
        endif;
        if (!empty($data['regranding_qty']) && $data['regranding_qty'] !=0.00):
            if(empty($data['regrinding_reason']))
                $errorMessage['regrinding_reason'.$data['id']] = "Regrinding Reason is required.";
        endif;
        if (empty($data['id'])): 
            $errorMessage['id'] = "Issue No. is required.";
        endif;

        if (empty($totalQty) || $totalQty != $data['return_qty'])
            $errorMessage['genral_error'.$data['id']] = "Qty is Invalid.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->inspection->saveInspection($data));
        endif;
    }

    public function saveInspLocation(){
        $data = $this->input->post(); 
        if (!empty($data['used_qty']) AND empty($data['location_used']))
            $errorMessage['location_used'] = "Location is required.";
        if (!empty($data['fresh_qty']) AND empty($data['location_fresh']))
            $errorMessage['location_fresh'] = "Location is required.";
        if (!empty($data['scrap_qty']) AND empty($data['location_scrap']))
            $errorMessage['id'] = "Scrap Location is required.";
        if (!empty($data['regranding_qty']) AND empty($data['used_qty']))
            $errorMessage['id'] = "Location is required.";
        if (!empty($data['broken_qty']) AND empty($data['location_broken']))
            $errorMessage['location_broken'] = "Location is required.";
        if (!empty($data['convert_qty']) AND empty($data['location_convert']))
            $errorMessage['location_convert'] = "Location is required.";
        if (!empty($data['missed_qty'])){
            $accept = (!empty($data['accepted_qty'])) ? $data['accepted_qty'] : 0;  
            if ($data['missed_qty'] != $accept){
                $errorMessage['accepted_qty'] = "Invalid Qty.";
            }
        }

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->inspection->saveInspLocation($data));
        endif;
    }

    public function pendingRegrindingIndex(){
        $this->data['tableHeader'] = getStoreDtHeader("pendingRegrinding");
        $this->load->view($this->pendingRegrinding, $this->data);
    }

    public function getRegrindingDTRows($status = 0)
    {
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->inspection->getRegrindingDTRows($data);
        $sendData = array(); $i=1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->status = $data['status'];
            $row->controller = "inspectionMaterial";
            $row->return_qty = $row->qty;
            if($status == 2){
                $row->statusText = '<span class="badge badge-pill badge-success m-1"><b>Complete</b></span>';
            }
            $sendData[] = getRegrindingData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function createRegrindingChallan(){
        $data = $this->input->post();
        $this->data['partyList'] = $this->party->getPartyList('2,3');
        $this->data['dimension'] = $this->regrindingReason->getRegrindingReasonList();
        $this->data['trans_prefix'] = 'RC/'.$this->shortYear.'/';
        $this->data['trans_no'] = $this->transModel->nextTransNo(5);
        $this->data['itemData'] = $this->inspection->getRegrindingItemData($data['id']); 
        $this->load->view($this->regrinding_challan_form, $this->data);
    }

    public function saveChallan(){
        $data = $this->input->post();
        if (empty($data['party_id']))
            $errorMessage['party_id'] = "Party is required.";
        if (empty(array_sum($data['qty'])))
            $errorMessage['general_error'] = "Qty is required.";
        $i=1;
        foreach($data['trans_id'] as $key=>$value){
            if(empty($data['dimension'][$key]) && !empty($data['qty'][$key])){
                $errorMessage['dimension'.$i] = "Dimension is required.";
            }
            $i++;
        }
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $masterData = [ 
				'id' => $data['id'],
                'entry_type' => 5,
                'order_type' => 3,
				'trans_prefix' => $data['trans_prefix'],
				'trans_no' => $data['trans_no'],
				'ref_id' => implode(",",$data['ref_id']),
                'trans_number'=>getPrefixNumber($data['trans_prefix'],$data['trans_no']),
				'trans_date' => date('Y-m-d',strtotime($data['trans_date'])),
				'party_id' => $data['party_id'], 
                'created_by' => $this->session->userdata('loginId')
			];		
							
			$itemData = [
				'id' => $data['trans_id'],
				'ref_id' => $data['ref_id'],
				'item_id' => $data['item_id'],
				'item_name' => $data['item_name'],
				'item_code' => $data['item_code'],
				'qty' => $data['qty'],
				'batch_no' => $data['batch_no'],
				'quote_rev_no' => $data['dimension'],
				'length_dia' => $data['length_dia']
			];
            $this->printJson($this->inspection->saveChallan($masterData,$itemData));
        endif;
    }

    public function regrindingChallan($status =0){
        $this->data['status'] = $status;
        $this->data['tableHeader'] = getStoreDtHeader("regrindingChallan");
        $this->load->view($this->regrinding_challan, $this->data);
    }

    public function getRegrindingChallanDTRows($status = 0){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->inspection->getRegrindingChallanDTRows($data);
        $sendData = array(); $i=1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->status = $data['status'];
            $row->controller = "inspectionMaterial";
            $sendData[] = getRegrindingChallanData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function receiveChallan(){
        $data = $this->input->post();
        $this->data['challanData'] = $this->inspection->getChallanMasterData($data['id']);
        $this->data['itemData'] = $this->inspection->getChallanTransactions(['trans_main_id'=>$data['id']]);
        $this->data['locationData'] = $this->stockTransac->getStoreLocationList(['store_type'=>'0,15','group_store_opt'=>1,'final_location'=>1])['storeGroupedArray'];         $this->load->view($this->receive_challan, $this->data);
    }

    public function saveReceiveItem(){
        $data = $this->input->post(); 
        // print_r($data);exit;
        if (empty(array_sum($data['dispatch_qty'])))
            $errorMessage['general_error'] = "Qty is required.";
   
        $i=1;
        foreach($data['dispatch_qty'] as $key=>$value){
            if(!empty($value) && empty($data['location_id'][$key])){
                $errorMessage['location_id'.$i] = "Location is required.";
            }
            if(!empty($value) && empty($data['ref_date'][$key])){
                $errorMessage['ref_date'.$i] = "Date is required.";
            }
            $i++;
        }
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $this->printJson($this->inspection->saveReceiveItem($data));
        endif;
    }

    public function challanView(){
        $data = $this->input->post();
        $this->data['itemData'] = $this->inspection->getChallanTransactions(['trans_main_id'=>$data['id']]);
        $this->load->view($this->receive_item_list, $this->data);
    }

    public function regrindingChallanPrint($id)
	{
        $challanData = $this->inspection->getChallanMasterData($id);
        $itemData = $this->inspection->getChallanTransactions(['trans_main_id'=>$id]);		
        $companyData = $this->db->where('id', 1)->get('company_info')->row();
		$response = "";
		$logo = base_url('assets/images/logo.png');

		$topSectionO = '<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td style="width:30%;"><img src="' . $logo . '" style="height:40px;"></td>
							<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">REGRINDING CHALLAN</td>
							<td style="width:30%;" class="text-right"><span style="letter-spacing:1px;">GST No.:<span style="letter-spacing:1px;">' . $companyData->company_gst_no . '</span><br><b>Original Copy</b></span></td>
						</tr>
					</table>
					<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
						<tr><td class="org-address text-center" style="font-size:13px;">' . $companyData->company_address . '</td></tr>
					</table>';
		$topSectionV = '<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td style="width:30%;"><img src="' . $logo . '" style="height:40px;"></td>
							<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">REGRINDING CHALLAN</td>
							<td style="width:30%;" class="text-right"><span style="letter-spacing:1px;">GST No.:<span style="letter-spacing:1px;">' . $companyData->company_gst_no . '</span><br><b>Vendor Copy</b></span></td>
						</tr>
					</table>
					<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
						<tr><td class="org-address text-center" style="font-size:13px;">' . $companyData->company_address . '</td></tr>
					</table>';
		$baseSection = '<table class="vendor_challan_table">
							<tr style="">
								<td rowspan="2" style="width:70%;vertical-align:top;">
									<b>TO : ' . $challanData->party_name . '</b><br>
									<span style="font-size:12px;">' . $challanData->party_address . '<br>
									<b>GSTIN No. :</b> <span style="letter-spacing:2px;">' . $challanData->gstin . '</span>
								</td>
								<td class="text-left" height="25"><b>Challan No. :</b> ' . $challanData->trans_number . ' </td>
							</tr>
							<tr>
								<td class="text-left" height="25"><b>Challan Date :</b> ' . date("d-m-Y", strtotime($challanData->trans_date)) . ' </td>
							</tr>
						</table>';
		$itemList = '<table class="table table-bordered jobChallanTable">
					<tr class="text-center bg-light-grey">
						<th>#</th>
						<th>Part</th>
						<th>Serial No</th>
						<th style="width:15%;">Nos.</th>
					</tr>';

		$i = 1; $totalOut = 0; $blnkRow = 4;
		if (!empty($itemData)) {
			foreach ($itemData as $row) :
				$itemList .= '<tr>
                        <td style="vertical-align:top;padding:5px;">' . $i++ . '</td>
                        <td style="vertical-align:top;padding:5px;">' . $row->full_name . '<br><small>[Current Size : '.$row->rev_no.', Reason : '.$row->regrinding_reason.']</small></td>
                        <td style="vertical-align:top;padding:5px;">' . $row->batch_no . '</td>
                        <td class="text-center" style="vertical-align:top;padding:5px;">' . ((!empty($row->qty)) ? sprintf('%0.0f', $row->qty) : '') . '</td>
                    </tr>';
				$totalOut += sprintf('%0.0f', $row->qty);
			endforeach;
		}

		for ($j = $i; $j < $blnkRow; $j++) :
			$itemList .= '<tr>
    			<td style="vertical-align:top;padding:5px;" height="50px"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
			</tr>';
		endfor;

		$itemList .= '<tr class="bg-light-grey">';
		$itemList .= '<th class="text-right" style="font-size:14px;" colspan="3">Total</th>';
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

    public function regrindingInspection($status = 1){
        $this->data['status'] = $status;
        $this->data['tableHeader'] = getStoreDtHeader("regrindingInspection");
        $this->load->view($this->regrinding_inspection, $this->data);
    }

    public function getRegrindingInspectionDTRows($status = 0){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->inspection->getRegrindingInspectionDTRows($data);
        $sendData = array(); $i=1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->status = $data['status'];
            $row->controller = "inspectionMaterial";
            $sendData[] = getRegrindingInspectionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function inspectReceivedChallan(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->inspection->challanTransRow($data['id']);
        $this->load->view($this->inspection_form, $this->data);
    }

    public function saveInspectedChallanItem(){
        $data = $this->input->post();
        if (empty($data['trans_status'])){
            $errorMessage['trans_status'] = "Decision is required.";
        }else  if($data['trans_status'] == 3 OR $data['trans_status'] == 4){
            if(empty($data['item_remark'])){
                $errorMessage['item_remark'] = "Remark is required.";
            }
        }
        
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $this->printJson($this->inspection->saveInspectedChallanItem($data));
        endif;
    }

    public function reRegrindingItem(){
        $this->data['tableHeader'] = getStoreDtHeader("reRegrinding");
        $this->load->view($this->re_regrinding_index, $this->data);
    }

    public function getReRegrindingDTRows($status = 0)
    {
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->inspection->getReRegrindingDTRows($data);
        $sendData = array(); $i=1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->status = $data['status'];
            $row->controller = "inspectionMaterial";
            $row->return_qty = $row->qty;
            
            $sendData[] = getReRegrindingData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function createReRegrindingChallan(){
        $data = $this->input->post();
        $this->data['partyList'] = $this->party->getPartyList('2,3');
        $this->data['dimension'] = $this->regrindingReason->getRegrindingReasonList();
        $this->data['trans_prefix'] = 'RC/'.$this->shortYear.'/';//$this->transModel->getTransPrefix(5);
        $this->data['trans_no'] = $this->transModel->nextTransNo(5);
        $this->data['itemData'] = $this->inspection->getReRegrindingItemData($data['id']); 
        $this->load->view($this->regrinding_challan_form, $this->data);
    }
}
