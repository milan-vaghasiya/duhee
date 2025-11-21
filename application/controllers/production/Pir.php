<?php
class Pir extends MY_Controller
{
    private $indexPage = "production/pir/index";
    private $penfingIndexPage = "production/pir/pending_pir_index";
    private $formPage = "production/pir/form";


    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "PIR Report";
        $this->data['headData']->controller = "production/pir";
    }

    public function index()
    {
        $this->data['tableHeader'] = getProductionHeader("pendingPir");
        $this->load->view($this->penfingIndexPage, $this->data);
    }
    public function getPendingPirDTRows($status=0)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->pir->getPendingPirDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $reports = $this->pir->getPIRReports(['job_card_id'=>$row->job_card_id,'process_id'=>$row->process_id,'machine_id'=>$row->machine_id,'item_id'=>$row->product_id]);
            $row->no_of_pir = count($reports);
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getPendingPIRData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function pirIndex()
    {
        $this->data['tableHeader'] = getProductionHeader("pir");
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($status=0)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->pir->getDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getPIRData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPirReport($job_card_id,$process_id,$machine_id)
    {
        $jobData = $this->jobcard->getJobcard($job_card_id);
        $prsData = $this->process->getProcess($process_id);
        $mcData = $this->item->getItem($machine_id);
        $this->data['job_card_id'] = $job_card_id;
        $this->data['process_id'] = $process_id;
        $this->data['process_name'] = $prsData->process_name;
        $this->data['machine_id'] = $machine_id;
        $this->data['machine_name'] = !empty($mcData->item_name)?$mcData->item_name:'';
        $this->data['machine_code'] = !empty($mcData->item_code)?$mcData->item_code:'';
        $this->data['jobData'] = $jobData;
        $pirData  = $this->pir->getPIRReports(['job_card_id'=>$job_card_id,'process_id'=>$process_id,'machine_id'=>$machine_id,'item_id'=>$jobData->product_id,'trans_date'=>date("Y-m-d"),'singleRow'=>1]);
        $approvalData = $this->processMovement->getJobApprovalDetail(['process_id'=>$process_id,'job_card_id'=>$job_card_id]);

        $this->data['job_approval_id'] = $approvalData->id;
        if(!empty($pirData)){
            $this->data['dataRow']=$pirData;	
        }
        $pfcProcess = $this->item->getPrdProcessDataProductProcessWise(['item_id' =>$jobData->product_id, 'process_id' => $process_id]);
        $this->data['paramData'] =  $this->controlPlan->getCPDimenstion(['item_id'=>$jobData->product_id,'control_method'=>'Production','pfc_id'=>$approvalData->pfc_ids,'responsibility'=>'INSP']);
		// print_r($this->db->last_query());
        $this->load->view($this->formPage, $this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = Array();

		if(empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";
        if(empty($data['mir_id']))
            $errorMessage['mir_id'] = "Jobcard is required.";
        if(empty($data['mir_trans_id']))
            $errorMessage['mir_trans_id'] = "Process No is required.";
        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Machine is required.";

        if(empty($data['report_time'][0]))
            $errorMessage['general'] = "Enter atleast one report time";
            
        $approvalData = $this->processMovement->getApprovalData($data['job_approval_id']);
        $insParamData =  $this->controlPlan->getCPDimenstion(['item_id'=>$data['item_id'],'pfc_id'=>$approvalData->pfc_ids,'control_method'=>'Production','responsibility'=>'INSP']);
        if(count($insParamData) <= 0)
            $errorMessage['general'] = "Item Parameter is required.";
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $insParamData =  $this->controlPlan->getCPDimenstion(['item_id'=>$data['item_id'],'pfc_id'=>$approvalData->pfc_ids,'control_method'=>'Production','responsibility'=>'INSP']);
            if(count($insParamData) <= 0)
                $errorMessage['general'] = "Item Parameter is required.";

            $pre_inspection = Array();$param_ids = Array();$data['observation_sample'] = '';$reportTime =[];
            if(!empty($insParamData)):
                $sample_size = $insParamData[0]->sev;
                foreach($insParamData as $row):
                    $param = Array();
                    for($j = 1; $j <=5; $j++):
                        $param[] = $data['sample'.$j.'_'.$row->id];
                        unset($data['sample'.$j.'_'.$row->id]);
                    endfor;
                    $pre_inspection[$row->id] = $param;
                    $param_ids[] = $row->id;
                endforeach;
                
                foreach($data['report_time'] as $row){
                    if(!empty($row)){
                        $reportTime[] = $row;
                    }
                }
            endif;
            unset($data['sample_size'],$data['report_time']);
            $data['parameter_ids'] = implode(',',$param_ids);
            $data['observation_sample'] = json_encode($pre_inspection);
            $data['param_count'] = count($insParamData);
            $data['sampling_qty'] = count($reportTime);
            $data['result'] = !empty($reportTime)?implode(',',$reportTime):'';
            $data['created_by'] = $this->session->userdata('loginId');
            // print_r($data);exit;
            $this->printJson($this->pir->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow']=$pirData = $this->pir->getPirData($id);	
        $approvalData = $this->processMovement->getApprovalData($pirData->job_approval_id);

        $this->data['paramData'] =  $this->controlPlan->getCPDimenstion(['item_id'=>$pirData->item_id,'pfc_id'=>$approvalData->pfc_ids,'control_method'=>'Production','responsibility'=>'INSP']);
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		
        $this->load->view($this->formPage, $this->data);
    }
    
    public function pir_pdf($id){
        $this->data['pirData']=$pirData = $this->pir->getPirData($id);		
        // $pfcProcess = $this->item->getPrdProcessDataProductProcessWise(['item_id' =>$pirData->item_id ,'process_id' =>$pirData->mir_trans_id]);

        $approvalData = $this->processMovement->getApprovalData($pirData->job_approval_id);

		$this->data['paramData'] =  $this->controlPlan->getCPDimenstion(['item_id'=>$pirData->item_id,'pfc_id'=>$approvalData->pfc_ids,'control_method'=>'Production','responsibility'=>'INSP']);
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$logo=base_url('assets/images/logo.png'); $unapproved = base_url('assets/images/unapproved.jpg');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('production/pir/pir_print',$this->data,true);
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%">Inprocess (Patrol) Inspection Report</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">R-PROD-16 (00/01.10.17)</td>
							</tr>
						</table><hr>';
		$htmlFooter = '<table class="table" style="border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center">'.$pirData->emp_name.'</td>
							<td style="width:25%;" class="text-center"></td>
						</tr>
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center"><b>Inspected By</b></td>
							<td style="width:25%;" class="text-center"><b>Verified By</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<!--<td style="width:25%;">PO No. & Date : </td>-->
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='pir'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('L','','','','',5,5,30,30,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');	
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->pir->delete($id));
        endif;
    }
    
    /* Created By :- Sweta @26-03-2024 */
    public function printPir($id){
        $this->data['dataRow'] = $dataRow = $this->pir->getPirData($id);	
        $approvalData = $this->processMovement->getApprovalData($dataRow->job_approval_id);

        $this->data['paramData'] = $this->controlPlan->getCPDimenstion(['item_id'=>$dataRow->item_id,'pfc_id'=>$approvalData->pfc_ids,'control_method'=>'Production','responsibility'=>'INSP']);

        $logo = base_url('assets/images/logo.png');

        $pdfData = $this->load->view('production/pir/pir_print_new',$this->data,true);
        
        $prepare = $this->employee->getEmp($approvalData->created_by);
        $prepareBy = $prepare->emp_name.' <br>('.formatDate($approvalData->created_at).')';

        $htmlHeader  = '<table class="table">
                    <tr>
                        <td style="width:15%;"><img src="' . $logo . '" style="max-height:40px;"></td>
                        <td class="org_title text-center" style="font-size:1.5rem;">Daily In-Process Inspection Report</td>
                        <td style="width:15%;" class="text-center">F QA 10<br>(01 / 22.03.2024)</td>
                    </tr>
                </table>';

        $htmlFooter = '<table class="table top-table" style="margin-top:10px;">
                <tr>
                    <td style="width:75%;"></td>
                    <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';

        $mpdf = new \Mpdf\Mpdf();  
        $pdfFileName = 'pir_' . $id . '.pdf';     
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetWatermarkImage($logo,0.03,array(120,60));
        $mpdf->showWatermarkImage = true;        
        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->AddPage('L','','','','',5,5,20,5,5,5,'','','','','','','','','','A4-L');
        $mpdf->WriteHTML($pdfData);        
        $mpdf->Output($pdfFileName,'I');
    }
}
