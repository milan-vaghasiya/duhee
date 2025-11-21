<?php
class ProductSetup extends MY_Controller
{
    private $indexPage = "production/product_setup/index";
    private $setterReportIndex = "production/product_setup/setter_report_index.php";
    private $setteReportForm = "production/product_setup/setter_report_form";
    private $asignInspectorIndex = "production/product_setup/asign_inspector_index";
    private $setupApproveIndex= "production/product_setup/setup_approve_index";
    private $setupApproveForm= "production/product_setup/setup_approve_form";
    private $pendingSetup= "production/product_setup/pending_setup";
    
    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Product Setup";
        $this->data['headData']->controller = "production/productSetup";
    }

    public function index()
    {
        $this->data['tableHeader'] = getProductionHeader("productSetup");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($setup_type = 1)
    {
        $data = $this->input->post();
        $data['setup_type'] = $setup_type;
        $result = $this->productSetup->getDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $row->status_label='';
            if($row->status == 0){ $row->status_label='Pending'; }
            if($row->status == 1){ $row->status_label=' In Process'; }
            if($row->status == 2){ $row->status_label=' Finish By Setter'; }
            if($row->status == 3){ $row->status_label='Approved'; }
            if($row->status == 4){ $row->status_label='Send For Reset up,'; }
            if($row->status == 5){ $row->status_label=' On Hold'; }
            if($row->status == 6){ $row->status_label='Accept By Inspector'; }
            $sendData[] = getProductSetupData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function acceptSetupRequest(){
        $data = $this->input->post();
        $this->printJson($this->productSetup->acceptSetupRequest($data));
    }

    public function setterReportIndex($id){
        $this->data['tableHeader'] = getProductionHeader("productSetterReport");
        $this->data['setup_id'] = $id;
        $this->data['setupData'] = $this->productSetup->getSetupRequestData($id);
        $this->load->view($this->setterReportIndex,$this->data);
    }

    public function getSetterReportDTRows($setup_id = 1)
    {
        $data = $this->input->post();
        $data['setup_id'] = $setup_id;
        $result = $this->productSetup->getSetterReportDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->setup_status_label='';
            if($row->setup_status == 0){ $row->setup_status_label='Pending'; }
            if($row->setup_status == 1){ $row->setup_status_label=' In Process'; }
            if($row->setup_status == 2){ $row->setup_status_label='Setup Failed'; }
            if($row->setup_status == 3){ $row->setup_status_label='Sent For Approval'; }
            if($row->setup_status == 4){ $row->setup_status_label='Accepted By QC'; }
            if($row->setup_status == 5){ $row->setup_status_label='Approved'; }
            if($row->setup_status == 6){ $row->setup_status_label='<span class="badge badge-pill badge-danger m-1">Reset up</span>'; }
            if($row->setup_status == 7){ $row->setup_status_label='On Hold'; }
            $sendData[] = getProductSetterReportData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addNewSetterRepoert(){
        $data = $this->input->post();
        $setupData = $this->productSetup->getSetupRequestData($data['setup_id']);
        $this->data['setup_id'] = $data['setup_id'];
        $this->data['setter_id'] = $setupData->setter_id;
        $pfcData = $this->item->getPrdProcessDataProductProcessWise(['item_id'=>$setupData->product_id,'process_id'=>$setupData->process_id]); //print_r($this->db->last_query());exit;
        if(!empty($pfcData->pfc_process)){
            $this->data['paramData'] =  $this->controlPlan->getCPDimenstion(['item_id'=>$setupData->product_id,'control_method'=>'SAR','pfc_id'=>$pfcData->pfc_process]);//print_r(array_column($paramData,'id') );
        }
        
        $this->load->view($this->setteReportForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = Array();
        $setupData = $this->productSetup->getSetupRequestData($data['setup_id']);
        $pfcData = $this->item->getPrdProcessDataProductProcessWise(['item_id'=>$setupData->product_id,'process_id'=>$setupData->process_id]); //print_r($this->db->last_query());exit;
        if(!empty($pfcData->pfc_process)){
            $insParamData =  $this->controlPlan->getCPDimenstion(['item_id'=>$setupData->product_id,'control_method'=>'SAR','pfc_id'=>$pfcData->pfc_process]);//print_r(array_column($paramData,'id') );
        }
        if(empty($insParamData))
            $errorMessage['general'] = "Sample Parameter is required.";

        $sar = Array();
        if(!empty($insParamData)):
            if(count($insParamData) <= 0)
                $errorMessage['general'] = "Sample Parameter is required.";
            foreach($insParamData as $row):
                $param = Array();
                $param = $data['sample_'.$row->id];
                unset($data['sample_'.$row->id]);
                $sar[$row->id] = $param;
            endforeach;
        endif;
        $data['dimension_report'] = json_encode($sar);
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['setup_status'] = 1;
            $data['created_by'] = $this->session->userdata('loginId');
            $data['created_at'] = date("Y-m-d H:i:s");
            $this->printJson($this->productSetup->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow']=$setupTrans = $this->productSetup->getSetupRequestTrans($data['id']);
        $setupData = $this->productSetup->getSetupRequestData($setupTrans->setup_id);
        $pfcData = $this->item->getPrdProcessDataProductProcessWise(['item_id'=>$setupData->product_id,'process_id'=>$setupData->process_id]); //print_r($this->db->last_query());exit;
        if(!empty($pfcData->pfc_process)){
            $this->data['paramData'] =  $this->controlPlan->getCPDimenstion(['item_id'=>$setupData->product_id,'control_method'=>'SAR','pfc_id'=>$pfcData->pfc_process]);//print_r(array_column($paramData,'id') );
        }
        
        $this->load->view($this->setteReportForm,$this->data);

    }

    public function delete(){
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->productSetup->delete($data['id']));
        endif;
    }
    public function completeReport(){
        $data = $this->input->post();
        $this->printJson($this->productSetup->completeReport($data));
    }
/***************************************************************************************************/
    public function asignInspector()
    {
        $this->data['tableHeader'] = getProductionHeader("asignSetupInspector");
        $this->data['inspectorList']=$this->employee->getSetterInspectorList();
        $this->load->view($this->asignInspectorIndex,$this->data);
    }

    public function getSetupDTRows($status = 0,$inspector = 0)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $data['inspector'] = $inspector;
        $result = $this->productSetup->getSetupDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $row->status_label='Pending';
            if($row->status == 0){ $row->status_label='Pending'; }
            if($row->status == 1){ $row->status_label=' In Process'; }
            if($row->status == 2){ $row->status_label=' Finish By Setter'; }
            if($row->status == 3){ $row->status_label='Approved'; }
            if($row->status == 4){ $row->status_label='Send For Reset up,'; }
            if($row->status == 5){ $row->status_label=' On Hold'; }
            if($row->status == 6){ $row->status_label='Accept By Inspector'; }
            $sendData[] = getAsignSetupInspData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function saveAsignedInspector(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['qci_id']))
            $errorMessage['qci_id'] = "Inspector is required.";

      
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['assign_by'] = $this->session->userdata('loginId');
            $data['assigned_at'] = date("Y-m-d H:i:s");
            $this->printJson($this->productSetup->saveAsignedInspector($data));
        endif;
    }
    /********************************************************************************/

    public function setupApproval(){
        $this->data['tableHeader'] = getProductionHeader("setupApproval");
        $this->load->view($this->setupApproveIndex,$this->data);
    }

    public function getSetupApprovalDTRows($status = 0){
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->productSetup->getSetupApprovalDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $row->setup_status_label='';
            if($row->setup_status == 0){ $row->setup_status_label='Pending'; }
            if($row->setup_status == 1){ $row->setup_status_label=' In Process'; }
            if($row->setup_status == 2){ $row->setup_status_label='Setup Failed'; }
            if($row->setup_status == 3){ $row->setup_status_label='Sent For Approval'; }
            if($row->setup_status == 4){ $row->setup_status_label='Accepted By QC'; }
            if($row->setup_status == 5){ $row->setup_status_label='Approved'; }
            if($row->setup_status == 6){ $row->setup_status_label='<span class="badge badge-pill badge-danger m-1">Reset up</span>'; }
            if($row->setup_status == 7){ $row->setup_status_label='On Hold'; }
            $sendData[] = getSetupApprovalData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function acceptSetupInspector(){
        $data = $this->input->post();
        $this->printJson($this->productSetup->acceptSetupInspector($data));
    }

    public function getSetupApproval(){
        $data = $this->input->post();
        $this->data['setupData']=$setupData = $this->productSetup->getSetupRequestTrans($data['id']);
        $pfcData = $this->item->getPrdProcessDataProductProcessWise(['item_id'=>$setupData->product_id,'process_id'=>$setupData->process_id]); //print_r($this->db->last_query());exit;
        if(!empty($pfcData->pfc_process)){
            $this->data['paramData'] =  $this->controlPlan->getCPDimenstion(['item_id'=>$setupData->product_id,'control_method'=>'SAR','pfc_id'=>$pfcData->pfc_process]);//print_r(array_column($paramData,'id') );
        }
        
        $this->load->view($this->setupApproveForm,$this->data);

    }

    public function saveSetupApprovalData(){
        $data = $this->input->post();
        $errorMessage = Array();
        // print_r($data);exit;
        if(empty($data['setup_status']))
            $errorMessage['setup_status'] = "Status is required.";

        
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $setupData = $this->productSetup->getSetupRequestData($data['setup_id']);
            $pfcData = $this->item->getPrdProcessDataProductProcessWise(['item_id'=>$setupData->product_id,'process_id'=>$setupData->process_id]);
            $insParamData =  $this->controlPlan->getCPDimenstion(['item_id'=>$setupData->product_id,'control_method'=>'SAR','pfc_id'=>$pfcData->pfc_process]);//print_r(array_column($paramData,'id') );
            $sar = Array();
            if(!empty($insParamData)):
                if(count($insParamData) <= 0)
                    $errorMessage['general'] = "Sample Parameter is required.";
                foreach($insParamData as $row):
                    $param = Array();
                    $param = $data['sample_'.$row->id];
                    unset($data['sample_'.$row->id]);
                    $sar[$row->id] = $param;
                endforeach;
            endif;
            $data['dimension_report'] = json_encode($sar);
            $this->printJson($this->productSetup->save($data));
        endif;
    }

    public function sar_pdf($id){
        $this->data['setupData']=$setupData = $this->productSetup->getSetupRequestData($id);
        $this->data['sampleReportData'] = $this->productSetup->getSetupRequestTransList($id); 
        $logo=base_url('assets/images/logo.png'); $unapproved = base_url('assets/images/unapproved.jpg');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		$pfcData = $this->item->getPrdProcessDataProductProcessWise(['item_id'=>$setupData->product_id,'process_id'=>$setupData->process_id]); //print_r($this->db->last_query());exit;
        if(!empty($pfcData->pfc_process)){
            $this->data['paramData'] =  $this->controlPlan->getCPDimenstion(['item_id'=>$setupData->product_id,'control_method'=>'SAR','pfc_id'=>$pfcData->pfc_process]);//print_r(array_column($paramData,'id') );
        }
        
		$pdfData = $this->load->view('production/product_setup/print_sar',$this->data,true);
		
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%">Setup Approval Report</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;"></td>
							</tr>
						</table><hr>';
		$htmlFooter = '<table class="table" style="border-bottom:1px solid #000000;">
						<tr>
							<td style="width:25%;" class="text-center">'.$setupData->setter_name.'<br><b>Setting By </b></td>
							<td style="width:25%;" class="text-center">'.(($setupData->status >2)?$setupData->qc_inspector:'').'<br><b>Inspected By</b></td>
							<td style="width:25%;" class="text-center"><br><b>Approved By</b></td>
						</tr>
					</table>
					';
		
		$mpdf = $this->m_pdf->load();
		//$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		//$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		// if(!empty($inInspectData->is_approve)){ $mpdf->SetWatermarkImage($logo,0.05,array(120,60));$mpdf->showWatermarkImage = true; }
		// else{ $mpdf->SetWatermarkText('Not Approved Copy',0.1);$mpdf->showWatermarkText = true; }
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('L','','','','',5,5,30,20,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');	
    }
    
    public function pendingSetupForInspector(){
        $this->data['tableHeader'] = getProductionHeader("asignSetupInspector");
        $this->load->view($this->pendingSetup,$this->data);
    }
}
