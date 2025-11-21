<?php
class GateReceipt extends MY_Controller{
    private $indexPage = "gate_receipt/index";	
	private $inInspection = "gate_receipt/in_inspection";
    private $tc_inspection = "gate_receipt/tc_inspection";
	private $testReport = "gate_receipt/test_report";

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Inward QC";
		$this->data['headData']->controller = "gateReceipt";
        $this->data['headData']->pageUrl = "gateReceipt";
    }

    public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->gateReceipt->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;

            $row->tc_status = "";
            if($status == 0 && $row->item_stock_type == 1):
                $checkTc = $this->gateReceipt->checkTcStatus($row->batch_no);
                if(empty($checkTc)):
                    $row->tc_status = 1;
                endif;
            endif;

            $sendData[] = getGateReceiptData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function inInspection($id){
        $this->data['dataRow'] = $dataRow = $this->gateReceipt->getInInspectionMaterial($id);
		$this->data['inInspectData'] = $this->gateReceipt->getInInspection($id);
		$this->data['sampleSize'] =  $this->reactionPlan->getSampleSize($dataRow->qty,'IIR');
		$this->data['paramData'] =  $this->controlPlan->getCPDimenstion(['ref_item_id'=>$dataRow->item_id,'stage_type'=>1]);
		$this->load->view($this->inInspection,$this->data);
	}
	
	public function inInspectionData(){
		$data = $this->input->post();
		$paramData = $this->item->getInspParamById($data['item_id']);
		$inInspectData = $this->gateReceipt->getInInspection($data['grn_trans_id']);
		$tbodyData="";$i=1; 
		if(!empty($paramData)): 
			foreach($paramData as $row):
				$obj = New StdClass;
				if(!empty($inInspectData)):
					$obj = json_decode($inInspectData->observation_sample); 
				endif;
				$inspOption = '';
				$inspOption  = '<option value="Ok" '.((!empty($obj->{$row->id}[10]) && $obj->{$row->id}[10]=="OK")?'selected':"").' >Ok</option><option value="Not Ok" '.((!empty($obj->{$row->id}[10]) && $obj->{$row->id}[10]=="Not Ok")?'selected':"").'>Not Ok</option>';
				$tbodyData.= '<tr>
							<td style="text-align:center;">'.$i++.'</td>
							<td>'.$row->parameter.'</td>
							<td>'.$row->specification.'</td>
							<td>'.$row->lower_limit.'</td>
							<td>'.$row->measure_tech.'</td>';
							
				for($c=0;$c<10;$c++):
					if(!empty($obj->{$row->id})):
						$tbodyData .= '<td><input type="text" name="sample'.($c+1).'_'.$row->id.'" class="xl_input maxw-60 text-center parameter_limit" data-specification="'.$row->specification.'" data-lower_limit="'.preg_replace("/[^0-9.]/", "", $row->lower_limit).'" value="'.$obj->{$row->id}[$c].'"></td>';
					else:
						$tbodyData .= '<td><input type="text" name="sample'.($c+1).'_'.$row->id.'" class="xl_input maxw-60 text-center parameter_limit" data-specification="'.$row->specification.'" data-lower_limit="'.preg_replace("/[^0-9.]/", "", $row->lower_limit).'" value=""></td>';
					endif;
				endfor;
			
				if(!empty($obj->{$row->id})):
					$tbodyData .= '<td><select name="result_'.$row->id.'" id="result_'.$i.'" class="form-control" value="'.$obj->{$row->id}[10].'">'.$inspOption.'</select></td>';
				else:
					$tbodyData .= '<td><select name="result_'.$row->id.'" id="result_'.$i.'" class="form-control" value="">'.$inspOption.'</select></td>';
				endif;
			endforeach;
		endif;
		$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
	}

	public function saveInInspection(){
		$data = $this->input->post();
        $errorMessage = Array();

		if(empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";

        //$insParamData = $this->item->getPreInspectionParam($data['item_id']);
        // $insParamData = $this->item->getControlPlanData($data['item_id'],1);
		$insParamData =  $this->controlPlan->getCPDimenstion(['ref_item_id'=>$data['item_id'],'stage_type'=>1]);
        if(count($insParamData) <= 0)
            $errorMessage['general'] = "Item Parameter is required.";

        $pre_inspection = Array();$param_ids = Array();$data['observation_sample'] = '';
        if(!empty($insParamData)):
            foreach($insParamData as $row):
                $param = Array();
                for($j = 1; $j <= $data['sample_size']; $j++):
                    $param[] = $data['sample'.$j.'_'.$row->id];
                    unset($data['sample'.$j.'_'.$row->id]);
                endfor;
                $param[] = $data['result_'.$row->id];
                $pre_inspection[$row->id] = $param;
				$param_ids[] = $row->id;
                unset($data['result_'.$row->id]);
            endforeach;
        endif;
		unset($data['sample_size']);
		$data['parameter_ids'] = implode(',',$param_ids);
        $data['observation_sample'] = json_encode($pre_inspection);
        $data['param_count'] = count($insParamData);
		
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->gateReceipt->saveInInspection($data));
        endif;
	}
	
	public function acceptGI(){
		$postData = $this->input->post();
        $errorMessage = Array();
        //print_r($postData);exit;
		$this->printJson($this->gateInward->acceptGI($postData));
	}
	
	public function inInspection_pdf($id){
		$this->data['inInspectData'] = $this->gateReceipt->getInInspection($id);
		$paramData = [] ;$controlMethodArray = [];$prepareBy="";$approveBy="";
		if(!empty($this->data['inInspectData'])){
			$this->data['paramData'] =  $this->controlPlan->getCPDimenstion(['ref_item_id'=>$this->data['inInspectData']->item_id,'stage_type'=>1]);
			

			$inInspectData = $this->data['inInspectData'];
			$inInspectData->fgCode="";
			if(!empty($inInspectData->fgitem_id)):
				$fgId = explode(',', $inInspectData->fgitem_id); $i=1; 
				foreach($fgId as $key=>$value):
					$fgData = $this->grnModel->getFinishGoods($value);
					if($i==1){ $inInspectData->fgCode.=$fgData->item_code; }
					else{ $inInspectData->fgCode.= ', '.$fgData->item_code; } $i++;
				endforeach;
			endif;

			$prepare = $this->employee->getEmp($inInspectData->created_by);
			$prepareBy = $prepare->emp_name.' <br>('.formatDate($inInspectData->created_at).')'; 
			$approveBy = '';
		}
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		
		
		if(!empty($inInspectData->is_approve)){
			$approve = $this->employee->getEmp($inInspectData->is_approve);
			$approveBy .= $approve->emp_name.' <br>('.formatDate($inInspectData->approve_date).')'; 
		}
		$response="";
		$logo=base_url('assets/images/logo.png'); $unapproved = base_url('assets/images/unapproved.jpg');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('gate_receipt/printInInspection',$this->data,true);
		
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%">Incoming Inspection Report</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">R-QC-01 (00/01.10.17)</td>
							</tr>
						</table><hr>';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;">
						<tr>
							<!--<td style="width:25%;">PO No. & Date : </td>-->
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = $this->m_pdf->load();
		//$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		//$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		if(!empty($inInspectData->is_approve)){ $mpdf->SetWatermarkImage($logo,0.05,array(120,60));$mpdf->showWatermarkImage = true; }
		else{ $mpdf->SetWatermarkText('Not Approved Copy',0.1);$mpdf->showWatermarkText = true; }
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('L','','','','',5,5,30,5,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');		
	}

	//Updated By Meghavi @31/05/2023
	public function getTcInspectionParam(){
        $grn_trans_id = $this->input->post('grn_trans_id');     
		$gateReceipt = $this->gateReceipt->getGrnTrans($grn_trans_id); 
        $this->data['dataRow'] = $dataRow = $this->materialGrade->getMaterialGrade($gateReceipt->material_grade); //print_r($this->data['dataRow']);exit;
		$tcData = $this->gateReceipt->getTcInspectionParam(['grade_id'=>$dataRow->id,'grn_trans_id'=>$grn_trans_id]);
		if(empty($tcData)):
    	    $this->data['specificationData'] = $this->gateReceipt->getMaterialSpecification($dataRow->id);
		else: 
			$this->data['specificationData'] = $tcData;
		endif;
		$this->data['grn_trans_id'] = $grn_trans_id;
        $this->load->view($this->tc_inspection,$this->data);
    }

    public function saveTcInspectionParam(){
        $data = $this->input->post();  
        $errorMessage = array(); 

        if(empty($data['min_value']))
			$errorMessage['generalError'] = "Material Specification is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			$data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->gateReceipt->saveTcInspectionParam($data));
        endif;
    }

	public function materialInspection(){
        $mir_id = $this->input->post('id');
        $this->data['status'] = 3;
        $this->data['mir_id'] = $mir_id;
        $this->data['dataRow'] = $this->gateReceipt->getGateReceiptOtherData($mir_id);
        $this->load->view('gate_receipt/other_inspection',$this->data);
    }

    public function saveMaterialInspection(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['item_data'])):
            $errorMessage['item_data'] = "Item details is required.";
        else:
            foreach($data['item_data'] as $row):
                $row['ok_qty'] = (!empty($row['ok_qty']))?$row['ok_qty']:0;
                $row['short_qty'] = (!empty($row['short_qty']))?$row['short_qty']:0;
                $row['rej_qty'] = (!empty($row['rej_qty']))?$row['rej_qty']:0;

                if(empty($row['ok_qty']) && empty($row['short_qty']) && empty($row['rej_qty'])):
                    $errorMessage['qty'.$row['mir_trans_id']] = "OK Qty or Short Qty or Rej. Qty is required.";
                endif;

                if($row['qty'] < ($row['ok_qty'] + $row['short_qty'] + $row['rej_qty'])):
                    $errorMessage['qty'.$row['mir_trans_id']] = "Invalid Qty.";
                endif;
            endforeach;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->gateReceipt->saveMaterialInspection($data));
        endif;
    }

	public function migrateBatchNo(){
		$this->gateReceipt->migrateBatchNo();
		exit;
	}
	
	//Test Report * Created By NYN @11/01/2023
	public function getTestReport(){
        $grn_id = $this->input->post('id');
        $this->data['dataRow'] = $this->gateReceipt->getTestReport($grn_id);
        $this->data['grn_id'] = $grn_id;
		$this->data['supplierList'] = $this->party->getSupplierList();
		$this->data['tcReportData'] = $this->getTestReportTable($grn_id);
        $this->load->view($this->testReport,$this->data);
    }

    public function updateTestReport(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['agency_id']))
            $errorMessage['agency_id'] = "Agency Name is required.";
		if(empty($data['name_of_agency']))
            $errorMessage['name_of_agency'] = "Agency Name is required.";		
        if(empty($data['test_description']))
            $errorMessage['test_description'] = "Description is required.";
		if(empty($data['sample_qty']))
            $errorMessage['sample_qty'] = "Sample Qty is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if($_FILES['tc_file']['name'] != null || !empty($_FILES['tc_file']['name'])):
                $this->load->library('upload');
				$_FILES['userfile']['name']     = $_FILES['tc_file']['name'];
				$_FILES['userfile']['type']     = $_FILES['tc_file']['type'];
				$_FILES['userfile']['tmp_name'] = $_FILES['tc_file']['tmp_name'];
				$_FILES['userfile']['error']    = $_FILES['tc_file']['error'];
				$_FILES['userfile']['size']     = $_FILES['tc_file']['size'];
				
				$imagePath = realpath(APPPATH . '../assets/uploads/test_report/');
				$config = ['file_name' => "test_report".time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

				$this->upload->initialize($config);
				if (!$this->upload->do_upload()):
					$errorMessage['item_image'] = $this->upload->display_errors();
					$this->printJson(["status"=>0,"message"=>$errorMessage]);
				else:
					$uploadData = $this->upload->data();
					$data['tc_file'] = $uploadData['file_name'];
				endif;
			else:
				unset($data['tc_file']);
			endif;
			
            $data['created_by'] = $this->session->userdata('loginId');
			$this->gateReceipt->saveTestReport($data);
			$tcReportData = $this->getTestReportTable($data['id']);
            $this->printJson(['status'=>1,'tcReportData'=>$tcReportData]);
        endif;
    }

	public function deleteTestReport(){
		$data = $this->input->post();

		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
			$this->gateReceipt->deleteTestReport($data['id']);
			$tcReportData = $this->getTestReportTable($data['grn_trans_id']);
            $this->printJson(['status'=>1,'tcReportData'=>$tcReportData]);
        endif;
	}
	
	public function getTestReportTable($grn_trans_id){
		$result = $this->gateReceipt->getTestReportTrans($grn_trans_id);

		$i=1; $tbodyData = "";
		if (!empty($result)) :
			foreach ($result as $row) :
			    $tdDownload = '';
			    if(!empty($row->tc_file)) {  $tdDownload = '<a href="'.base_url('assets/uploads/test_report/'.$row->tc_file).'" target="_blank"><i class="fa fa-download"></i></a>'; } 
				$tbodyData .=  '<tr>
					<td>' . $i++ . '</td>
					<td>' . $row->name_of_agency . '</td>
					<td>' . $row->test_description . '</td>
					<td>' . $row->sample_qty . '</td>
					<td>' . $row->test_report_no . '</td>
					<td>' . $row->test_remark . '</td>
					<td>' . $row->test_result . '</td>
					<td>' . $row->inspector_name . '</td>
					<td>' . $row->mill_tc . '</td>
					<td>' . $tdDownload . '</td>
					<td class="text-center">
						<a class="btn btn-outline-danger btn-sm btn-delete" href="javascript:void(0)" onclick="trashTestReport('.$row->id.','.$row->grn_trans_id.');" datatip="Remove" flow="left"><i class="ti-trash"></i></a>
					</td>
				</tr>';
			endforeach;
		else :
			$tbodyData .= '<tr><td colspan="11" style="text-align:center;">No Data Found</td></tr>';
		endif;
		return $tbodyData;
	}
}
