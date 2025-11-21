<?php
class Rqc extends MY_Controller
{
    private $indexPage = "rqc/index";
    private $accept_form = "rqc/accept_form";
    private $rqc_index = "rqc/rqc_index";
    private $formPage = "rqc/form";


    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "RQC";
        $this->data['headData']->controller = "rqc";
    }

    public function index()
    {
        $this->data['tableHeader'] = getQualityDtHeader("inwardRqc");
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($status=0)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->rqc->getDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getRQCInwardData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function acceptRqc(){ 
        $data= $this->input->post();
        $this->data['job_card_id'] = $data['job_card_id'];
        $this->data['job_approval_id'] = $data['job_approval_id'];
        $this->data['job_trans_id'] = $data['id'];
        $this->data['product_id'] = $data['product_id'];
        $this->data['process_id'] = $data['in_process_id'];
        $this->load->view($this->accept_form,$this->data);
    }

    public function saveInward(){
        $data = $this->input->post();
        if($data['mir_id'] == "")
            $errorMessage['mir_id'] = "Jobcard is required.";
        if(empty($data['lot_qty']) || $data['lot_qty'] == "0.000"):
            $errorMessage['qty'] = "Quantity is required.";
        else:
            $movementData = $this->processMovement->getMovement($data['mir_trans_id']);
            // print_r(($fiStock->inward_qty - $fiStock->in_qty));
            if($data['lot_qty'] > ($movementData->qty -$movementData->total_weight)){
                $errorMessage['lot_qty'] = "Quantity is not available.";
            }
            $sample = $this->reactionPlan->getSampleSize($data['lot_qty'],'RQC');
            if(empty($sample->sample_size)){
                $errorMessage['lot_qty'] = "Sample Size is required.";
            }

        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->rqc->saveInward($data));
        endif;
    }

    public function rqcIndex($status = 0)
    {
        $this->data['status'] = $status;
        $this->data['tableHeader'] = getQualityDtHeader("rqc");
        $this->load->view($this->rqc_index, $this->data);
    }
    public function getRQCDTRows($status=0)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->rqc->getRQCDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getRQCData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function edit($id){
        $this->data['rqcData'] =$rqcData = $this->rqc->getRQCReport(['id'=>$id]); 
        $approvalData = $this->processMovement->getApprovalData($rqcData->job_approval_id);
        $pfcProcess = $this->item->getPrdProcessDataProductProcessWise(['item_id' =>$rqcData->item_id, 'process_id' => $approvalData->in_process_id]);
        $this->data['paramData'] =  $this->controlPlan->getCPDimenstion(['item_id'=>$rqcData->item_id,'stage_type'=>7,'pfc_id'=>$pfcProcess->pfc_process,'control_method'=>'RQC']);
        $this->load->view($this->formPage, $this->data);

    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = Array();

		if(empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";
       
        $rqcData = $this->rqc->getRQCReport(['id'=>$data['id']]); 
        $approvalData = $this->processMovement->getApprovalData($rqcData->job_approval_id);
        $pfcProcess = $this->item->getPrdProcessDataProductProcessWise(['item_id' =>$rqcData->item_id, 'process_id' => $approvalData->in_process_id]);
        $insParamData =  $this->controlPlan->getCPDimenstion(['item_id'=>$rqcData->item_id,'stage_type'=>7,'pfc_id'=>$pfcProcess->pfc_process,'control_method'=>'RQC']);
        if(count($insParamData) <= 0)
            $errorMessage['general'] = "Item Parameter is required.";
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
          
            if(count($insParamData) <= 0)
                $errorMessage['general'] = "Item Parameter is required.";

            $pre_inspection = Array();$param_ids = Array();$data['observation_sample'] = '';$reportTime =[];
            if(!empty($insParamData)):
                foreach($insParamData as $row):
                    $param = Array();
                    for($j = 1; $j <=$rqcData->sampling_qty; $j++):
                        $param[] = $data['sample'.$j.'_'.$row->id];
                        unset($data['sample'.$j.'_'.$row->id]);
                    endfor;
                    $pre_inspection[$row->id] = $param;
                    $param_ids[] = $row->id;
                endforeach;
                
            endif;
            
            $data['parameter_ids'] = implode(',',$param_ids);
            $data['observation_sample'] = json_encode($pre_inspection);
            $data['param_count'] = count($insParamData);
            $data['created_by'] = $this->session->userdata('loginId');
            // print_r($data);exit;
            $this->printJson($this->rqc->save($data));
        endif;
    }

    public function completeRQC(){
        $data = $this->input->post();
        $rqcData = $this->rqc->getRQCReport(['id'=>$data['id']]); 
        $approvalData = $this->processMovement->getApprovalData($rqcData->job_approval_id);

        $movementData = [
            'id' => '',
            'entry_date' => date("Y-m-d"),
            'trans_type' =>1,
            'entry_type' =>  9,
            'ref_id' => $rqcData->id,
            'vendor_id' =>  0,
            'job_card_id' => $rqcData->mir_id,
            'job_approval_id' => $rqcData->job_approval_id,
            'process_id' => $approvalData->in_process_id,
            'product_id' =>$approvalData->product_id,
            'qty' => $rqcData->lot_qty,
            'remark' => '',
            'cycle_time' => 0,
            'production_time' => 0,
            'send_to' => 0,
            'machine_id' => 0,
            'shift_id' => 0,
            'operator_id' =>0,
            'rej_qty' => 0,
            'rw_qty' => 0,
            'hold_qty' => 0,
            'in_challan_no' =>0,
            'created_by' => $this->session->userdata('loginId')
        ];
        $result = $this->processMovement->save($movementData);
        $this->printJson($result);
    }
    
    // Created By Meghavi @09/01/2023
    public function inInspection_pdf($id){
		$this->data['inInspectData'] =$rqcData = $this->rqc->getRQCReport(['id'=>$id]);  //print_r($this->data['inInspectData']);exit;
	    $approveBy="";
		if(!empty($this->data['inInspectData'])){

            $approvalData = $this->processMovement->getApprovalData($rqcData->job_approval_id);
            $pfcProcess = $this->item->getPrdProcessDataProductProcessWise(['item_id' =>$rqcData->item_id, 'process_id' => $approvalData->in_process_id]);
            $this->data['paramData'] =  $this->controlPlan->getCPDimenstion(['item_id'=>$rqcData->item_id,'stage_type'=>7,'pfc_id'=>$pfcProcess->pfc_process,'control_method'=>'RQC']);
			

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
			$approveBy = '';
		}
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		
		
		if(!empty($inInspectData->is_approve)){
			$approve = $this->employee->getEmp($inInspectData->is_approve);
			$approveBy .= $approve->emp_name.' <br>('.formatDate($inInspectData->approve_date).')'; 
		}
		$logo=base_url('assets/images/logo.png'); 
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('rqc/printInInspection',$this->data,true);
		
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%">receiving Quality Control</td>
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
}
