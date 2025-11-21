<?php
class Sar extends MY_Controller
{
    private $indexPage = "sar/index";
    private $formPage = "sar/form";
    private $approve_form = "sar/approve_form";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "SAR";
		$this->data['headData']->controller = "sar";
		$this->data['headData']->pageUrl = "sar";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->sar->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getSarData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addSar(){
        $this->data['jobCardList'] = $this->jobcard->getJobcardList(2);
        $this->data['machineList'] = $this->item->getItemList(5);
        $this->data['setterList'] = $this->employee->getSetterList();
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if (empty($data['trans_date'])){
            $errorMessage['trans_date'] = "Date is required.";
        }
		if (empty($data['job_card_id'])){
            $errorMessage['job_card_id'] = "Jobcard is required.";
        }
		if (empty($data['process_id'])){
            $errorMessage['process_id'] = "Process is required.";
        }
		if (empty($data['machine_id'])){
            $errorMessage['machine_id'] = "Machine is required.";
        }
		if (empty($data['setter_id'])){
            $errorMessage['setter_id'] = "Setter is required.";
        }
		if (empty($data['setting_time'])){
            $errorMessage['setting_time'] = "Setting Time is required.";
        }

        $approvalData = $this->processMovement->getJobApprovalDetail(['process_id'=>$data['process_id'], 'job_card_id'=>$data['job_card_id']]);
        $paramData =  $this->controlPlan->getCPDimenstion(['item_id'=>(!empty($approvalData->product_id)?$approvalData->product_id:''), 'pfc_id'=>(!empty($approvalData->pfc_ids)?$approvalData->pfc_ids:''), 'control_method'=>'SAR']);//, 'parameter_type'=>2]);

        if(empty($paramData)){
            $errorMessage['general_error'] = "Sample Parameter is required.";
        }
        
        $sar = Array();
        if(!empty($paramData)):
            if(count($paramData) <= 0)
                $errorMessage['general_error'] = "Sample Parameter is required.";
            foreach($paramData as $row):
                $param = Array();
                $param = $data['sample_'.$row->id];
                unset($data['sample_'.$row->id]);
                $sar[$row->id] = $param;
            endforeach;
        endif;
        $data['observation'] = json_encode($sar);

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $data['created_at'] = date("Y-m-d H:i:s");
            $this->printJson($this->sar->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $dataRow = $this->sar->getSarDetails($id);
        $this->data['jobCardList'] = $this->jobcard->getJobcardList(2);
        $this->data['machineList'] = $this->item->getItemList(5);
        $this->data['setterList'] = $this->employee->getSetterList();
        
        $processData = $this->sar->getJobcardProcessList(['job_card_id'=>$dataRow->job_card_id]);
        $options = '<option value="">Select Process</option>';
        if(!empty($processData)){
            foreach($processData as $row){
                $selected = (!empty($dataRow->process_id) && $dataRow->process_id == $row->id) ? "selected" : "";
                $options .= '<option value="'.$row->id.'" '.$selected.'>'.$row->process_name.'</option>';
            }
        }
        $this->data['options'] = $options;

        $approvalData = $this->processMovement->getJobApprovalDetail(['process_id'=>$dataRow->process_id, 'job_card_id'=>$dataRow->job_card_id]);
        $this->data['paramData'] =  $this->controlPlan->getCPDimenstion(['item_id'=>(!empty($approvalData->product_id)?$approvalData->product_id:''), 'pfc_id'=>(!empty($approvalData->pfc_ids)?$approvalData->pfc_ids:''), 'control_method'=>'SAR', 'parameter_type'=>2]);

        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->sar->delete($id));
        endif;
    }   

    public function getJobcardProcessList(){
        $data = $this->input->post();
        $processData = $this->sar->getJobcardProcessList($data);

        $options = '<option value="">Select Process</option>';
        if(!empty($processData)){
            foreach($processData as $row){
                $options .= '<option value="'.$row->id.'">'.$row->process_name.'</option>';
            }
        }
        $this->printJson(['status'=>1, 'options'=>$options]);
    }
    
    public function getProcessParam(){
        $data = $this->input->post();
        $approvalData = $this->processMovement->getJobApprovalDetail(['process_id'=>$data['process_id'], 'job_card_id'=>$data['job_card_id']]);
        $paramData =  $this->controlPlan->getCPDimenstion(['item_id'=>(!empty($approvalData->product_id)?$approvalData->product_id:''), 'pfc_id'=>(!empty($approvalData->pfc_ids)?$approvalData->pfc_ids:''), 'control_method'=>'SAR']); //, 'parameter_type'=>2

        $tbodyData = "";
        $i = 1;$tbcnt=1;

        if (!empty($paramData)) :
            foreach ($paramData as $row) :
                $obj = new StdClass;
                $cls = "";
                if (!empty($row->lower_limit) or !empty($row->upper_limit)) :
                    $cls = "floatOnly";
                endif;
                $diamention = '';
                if ($row->requirement == 1) {
                    $diamention = $row->min_req . '/' . $row->max_req;
                }
                if ($row->requirement == 2) {
                    $diamention = $row->min_req . ' ' . $row->other_req;
                }
                if ($row->requirement == 3) {
                    $diamention = $row->max_req . ' ' . $row->other_req;
                }
                if ($row->requirement == 4) {
                    $diamention = $row->other_req;
                }
                if (!empty($dataRow)) :
                    $obj = json_decode($dataRow->observation);
                endif;
                $char_class=''; if(!empty($row->char_class)){ $char_class='<img src="' . base_url('assets/images/symbols/'.$row->char_class.'.png') . '" style="width:20px;display:inline-block;vertical-align:middle;" />'; }

                $tbodyData .= '<tr>
                                <td style="text-align:center;">' . $i++ . '</td>
                                <td>' . $row->process_no.' '.$char_class . '</td>
                                <td>' . $row->parameter . '</td>
                                <td>' . $diamention . '</td>
                                <td>' . $row->category_name . '</td>
                                <td>' . $row->sev . '</td>
                                <td>' . $row->potential_cause . '</td>';
                                if (!empty($obj->{$row->id})) :
                                    $tbodyData .= '<td><input type="text" name="sample_'.$row->id.'" id="sample_'.$i.'" class="form-control text-center parameter_limit'.$cls.'" value="'.$obj->{$row->id}.'" data-min="'.$row->min_req.'" data-max="'.$row->max_req.'" data-requirement="'.$row->requirement.'" data-row_id="'.$i.'"></td>';
                                else :
                                    $tbodyData .= '<td><input type="text" name="sample_'.$row->id.'" id="sample_'.$i.'" class="form-control text-center parameter_limit'.$cls.'" value="" data-min="'.$row->min_req.'" data-max="'.$row->max_req.'" data-requirement="'.$row->requirement.'" data-row_id="'.$i.'"></td>';
                                endif;
                $tbodyData .= '</tr>';
            endforeach;
        else:
            $tbodyData = '<tr class="text-center"><td colspan="8">Data not available.</td></tr>';
        endif;
        $tbcnt++;

        $this->printJson(['status'=>1, 'tbodyData'=>$tbodyData]);
    }

    public function approveSar($id){
        $this->data['dataRow'] = $dataRow = $this->sar->getSarDetails($id);

        $approvalData = $this->processMovement->getJobApprovalDetail(['process_id'=>$dataRow->process_id, 'job_card_id'=>$dataRow->job_card_id]);
        $this->data['paramData'] =  $this->controlPlan->getCPDimenstion(['item_id'=>(!empty($approvalData->product_id)?$approvalData->product_id:''), 'pfc_id'=>(!empty($approvalData->pfc_ids)?$approvalData->pfc_ids:''), 'control_method'=>'SAR', 'parameter_type'=>1]);

        $this->load->view($this->approve_form,$this->data);
    }

    public function saveApproveSar(){
        $data = $this->input->post();
        $errorMessage = array();

        $dataRow = $this->sar->getSarDetails($data['id']);
        $approvalData = $this->processMovement->getJobApprovalDetail(['process_id'=>$dataRow->process_id, 'job_card_id'=>$dataRow->job_card_id]);
        $paramData =  $this->controlPlan->getCPDimenstion(['item_id'=>(!empty($approvalData->product_id)?$approvalData->product_id:''), 'pfc_id'=>(!empty($approvalData->pfc_ids)?$approvalData->pfc_ids:''), 'control_method'=>'SAR', 'parameter_type'=>1]);

        if(empty($paramData)){
            $errorMessage['general_error'] = "Sample Parameter is required.";
        }
        
        $sar = Array();
        if(!empty($paramData)):
            if(count($paramData) <= 0)
                $errorMessage['general_error'] = "Sample Parameter is required.";
            foreach($paramData as $row):
                $param = Array();
                for($j = 1; $j <=5; $j++):
                    $param[] = $data['sample'.$j.'_'.$row->id];
                    unset($data['sample'.$j.'_'.$row->id]);
                endfor;
                $sar[$row->id] = $param;
            endforeach;
        endif;
        $data['prod_observation'] = json_encode($sar);

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            if($data['status'] == 1){
                $data['approve_by'] = $this->session->userdata('loginId');
                $data['approve_at'] = date("Y-m-d H:i:s");
            }
            $data['updated_by'] = $this->session->userdata('loginId');
            $data['updated_at'] = date("Y-m-d H:i:s");
            $this->printJson($this->sar->save($data));
        endif;
    }

    public function printSar($id){
        $this->data['dataRow'] = $dataRow = $this->sar->getSarDetailsForPrint($id);

        $approvalData = $this->processMovement->getJobApprovalDetail(['process_id'=>$dataRow->process_id, 'job_card_id'=>$dataRow->job_card_id]);
        $this->data['paramData'] =  $this->controlPlan->getCPDimenstion(['item_id'=>(!empty($approvalData->product_id)?$approvalData->product_id:''), 'pfc_id'=>(!empty($approvalData->pfc_ids)?$approvalData->pfc_ids:''), 'control_method'=>'SAR', 'parameter_type'=>2]);
        $this->data['prodParamData'] =  $this->controlPlan->getCPDimenstion(['item_id'=>(!empty($approvalData->product_id)?$approvalData->product_id:''), 'pfc_id'=>(!empty($approvalData->pfc_ids)?$approvalData->pfc_ids:''), 'control_method'=>'SAR', 'parameter_type'=>1]);

        $logo = base_url('assets/images/logo.png');
        
		$pdfData = $this->load->view('sar/print',$this->data,true);

        $htmlHeader  = '<table class="table">
                    <tr>
                        <td style="width:25%;"><img src="' . $logo . '" style="max-height:40px;"></td>
                        <td class="org_title text-center" style="font-size:1.5rem;">Setup Approval Report</td>
                        <td style="width:25%;" class="text-center">F-P & M-10<br>(01 / 22.03.2024)</td>
                    </tr>
                </table>';

		$htmlFooter = '<table class="table" style="border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;" class="text-center">'.$dataRow->insp_by.'<br><b>Inspected By </b></td>
							<td style="width:50%;" class="text-center">'.$dataRow->approve_by.'<br><b>Approved By</b></td>
						</tr>
					</table>
                    <table class="table top-table">
                        <tr>
                            <td style="width:75%;"></td>
                            <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                        </tr>
                    </table>';
		
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='SAR-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->AddPage('P','','','','',5,5,20,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');	
    }
}
?>