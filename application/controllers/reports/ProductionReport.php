<?php
class ProductionReport extends MY_Controller
{
    private $production_report_page = "report/production/index";
    private $job_wise_production = "report/production/job_production";
    private $jobwork_register = "report/production/jobwork_register";
    private $production_analysis = "report/production/production_analysis";
    private $stage_production = "report/production/stage_production";
    private $jobcard_register = "report/production/jobcard_register";
    private $machinewise_production = "report/production/machinewise_production";
    private $general_oee = "report/production/general_oee";
    private $vendorScrap_register = "report/production/vendor_scrap";
    private $jobwork_report = "report/production/jobwork_report";  
    private $dept_wise_production = "report/production/dept_wise_production";
    private $rejection_monitoring = "report/production/rejection_monitoring";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Production Report";
		$this->data['headData']->controller = "reports/productionReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/production/floating_menu',[],true);
	}
	
	public function index(){
		$this->data['pageHeader'] = 'PRODUCTION REPORT';
        $this->load->view($this->production_report_page,$this->data);
    }

    /* Job Wise Production */    
    public function jobProduction($item_id=""){
        $this->data['pageHeader'] = 'JOB WISE PRODUCTION';
		$this->data['jobcardData'] = $this->productionReports->getJobcardList();
		$this->data['itemId'] = $item_id;
        $this->load->view($this->job_wise_production,$this->data);
    }

    public function getJobWiseProduction()
	{
		$data = $this->input->post();
        $result = $this->productionReports->getJobWiseProduction($data);
        $this->printJson($result);
    }

    /* Jobwork Register */
    public function jobworkRegister()
    {
        $this->data['pageHeader'] = 'JOB WORK OUTWARD-INWARD REGISTER';
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->load->view($this->jobwork_register, $this->data);
    }

    public function getJobworkRegister(){
        $data = $this->input->post();
        $jobOutData = $this->productionReports->getJobworkRegister($data);

        $blankInTd = '<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
        $i = 1;
        $tblData = "";
        foreach ($jobOutData as $row) :
            $outData = $this->productionReports->getJobOutwardData($row->id);
            
            $outCount = count($outData); 
            $tblData .= '<tr>
                            <td>' . $i++ . '</td>
                            <td>' . formatDate($row->entry_date) . '</td>
                            <td>' . $row->party_name. '</td>
                            <td>' . $row->challan_no. '</td>
                            <td>' . $row->job_number. '</td>
                            <td>' . $row->wo_no . '</td>
                            <td>' . $row->item_code . '</td>
                            <td>' . $row->process_name . '</td>
                            <td>' . $row->qty . '</td>';
            if ($outCount > 0) :
                $usedQty = 0; $j=1;
                foreach ($outData as $outRow) :
                    
					$outQty = $row->qty;
					$usedQty += $outRow->qty;
                    
					$balQty = floatVal($outQty) - floatVal($usedQty);
                    
					$tblData .= '<td>' . formatDate($outRow->entry_date) . '</td>
								<td>' . $row->party_name. '</td>
								<td>' . $outRow->in_challan_no . '</td>
								<td>' . $outRow->qty . '</td>
								<td>' . $balQty . '</td>';           
                    if ($j != $outCount) {
                        $tblData .= '</tr><tr><td>' . $i++ . '</td>' . $blankInTd;
                    }
                    $j++;
                endforeach;
            else :
                $tblData .= '<td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>';
            endif;
            $tblData .= '</tr>';
        endforeach;
        $this->printJson(['status' => 1, "tblData" => $tblData]);
    }

     /* Vendor Scrap Register  Created By Karmi @17/05/2022*/
    public function scrapRegister(){
        $this->data['pageHeader'] = 'VENDOR SCRAP REGISTER';
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->data['productList'] = $this->item->getItemList(3); 
        $this->load->view($this->vendorScrap_register,$this->data);
    }
    
    //changed By Karmi @19/05/2022
    public function getVendorScrap($jsonData=''){
        if(!empty($jsonData)){$postData = (Array) json_decode(urldecode(base64_decode($jsonData)));}
        else{$postData = $this->input->post();}       
        $ScrapData = $this->productionReports->getVendorScrap($postData);
        $i=1; $tblData = "";$thead ="";
        $thead .= '<tr text-align:center;>
                    <td style="min-width:100px;text-align:center;"><b>#</b></td>
                    <td style="min-width:100px;text-align:center;"><b>Challan No.</b></td>
                    <td style="min-width:100px;text-align:center;"><b>Challan Date</b></td>
                    <td style="min-width:100px;text-align:center;"><b>Vendor Name</b></td>
                    <td style="min-width:100px;text-align:center;"><b>Item</b></td>
                    <td style="min-width:100px;text-align:center;"><b>Process</b></td>
                    <td style="min-width:100px;text-align:center;"><b>Scrap (In Kgs)</b></td>
                </tr>';
        foreach($ScrapData as $row): 
            $tblData.='<tr>
                            <td  style="min-width:100px;text-align:center;">'.$i++.'</td>
                            <td  style="min-width:100px;text-align:center;">'.$row->trans_number.'</td>
                            <td  style="min-width:100px;text-align:center;">'.formatDate($row->entry_date).'</td>
                            <td  style="min-width:100px;text-align:center;">'.$row->party_name.'</td>
                            <td  style="min-width:100px;text-align:center;">'.$row->full_name.'</td>
                            <td  style="min-width:100px;text-align:center;">'.$row->process_name.'</td>
                            <td  style="min-width:100px;text-align:center;">'.$row->scrap_weight.'</td>';

            $tblData.='</tr>';
        endforeach;

        if(!empty($postData['pdf']))
		{
			$companyData = $this->jobWorkOrder->getCompanyInfo();
			$letter_head = base_url('assets/images/letterhead_top.png');		
		    $htmlHeader = '<img src="'.$letter_head .'" class="img">';	
			
			$pdfData = '<table id="commanTable" class="table item-list-bb">
								<thead id="theadData">'.$thead.'</thead>
								<tbody  id="tbodyData">'.$tblData.'</tbody>
							</table>';
			$htmlHeader = '<img src="' . $letter_head . '">';
			$htmlHeader .= '<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td class="org_title text-uppercase text-center" style="font-size:1rem;width:100%;padding-top:10px;">VENDOR SCRAP REGISTER</td>
						</tr>
					</table>';
				
			$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
							<td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
			
            $mpdf = $this->m_pdf->load();
    		$filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/vendorScrap.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
			
			$mpdf->showWatermarkImage = true;
			$mpdf->SetTitle("VENDOR SCRAP REGISTER");
			$mpdf->SetHTMLHeader($htmlHeader);
			$mpdf->SetHTMLFooter($htmlFooter);
            //$mpdf->SetProtection(array('print'));
    
    		$mpdf->AddPage('P','','','','',5,5,47,5,3,3,'','','','','','','','','','A4-P');
            $mpdf->WriteHTML($pdfData);
    		
    		ob_clean();
    		$mpdf->Output($pdfFileName, 'I');
		}
		else
		{
            $this->printJson(['status'=>1,"tblData"=>$tblData]);
        }

    }
    
    /* Production Analysis */
    public function productionAnalysis(){
        $this->data['pageHeader'] = 'PRODUCTION ANALYSIS';
        $this->load->view($this->production_analysis,$this->data);
    }

    public function getProductionAnalysis(){
        $data = $this->input->post();
        $errorMessage = array();

		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $productionData = $this->productionReports->getProductionAnalysis($data);
            $this->printJson($productionData);
        endif;
    }

    /* stage wise Production */
    public function stageProduction(){
        $this->data['pageHeader'] = 'STAGE WISE PRODUCTION';
        $this->data['itemList'] = $this->productionReports->getProductList(1);
        $this->data['processList'] = $this->process->getProcessList();
        $this->load->view($this->stage_production,$this->data);
    }

    public function getStageWiseProduction(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $stageData = $this->productionReports->getStageWiseProduction($data);
			$jobData = $stageData['jobData'];$processList = $stageData['processList'];
            $thead='';$tbody="";
			if(!empty($processList)):
				$thead = '<tr><th style="min-width:100px;">Job No.</th><th style="min-width:100px">Part No.</th>';
				$l=0;
				foreach($jobData as $row):
					$qtyTD = '';$qty = 0;
					foreach($processList as $pid):
						if($l==0){$thead .= '<th>'.$this->process->getProcess($pid)->process_name.'<br>(Ok Qty.)</th>';}
						if(in_array($pid,explode(',',$row->process))):							
							$qty = $this->productionReports->getProductionQty($row->id,$pid)->qty;
						endif;
						$qtyTD .= (!empty($qty)) ? '<td>'.floatVal($qty).'</td>' : '<td>-</td>';
					endforeach;
					$tbody .= '<tr class="text-center">
								<td>'.$row->job_number.'</td>
								<td>'.$row->item_code.'</td>
								'.$qtyTD.'
							</tr>';
					$l++;
				endforeach;
				// $thead .= '<th>Total<br>(Ok Qty.)</th></tr>';
			else:
				$thead = '<tr><th style="min-width:100px;">Job No.</th><th style="min-width:100px">Part No.</th><th style="min-width:100px;">Process List</th></tr>';
			endif;
            

            $this->printJson(['status'=>1, 'thead'=>$thead, 'tbody'=>$tbody]);
        
        endif;
    }

    /* Jobcard Register  */
     public function jobcardRegister()
     {
         $this->data['pageHeader'] = 'JOB CARD REGISTER';
         $jobCardData = $this->productionReports->getJobcardRegister();
         $html = '';
         $i = 1;
         foreach ($jobCardData as $row) :
             $cname = !empty($row->party_code) ? $row->party_code : "Self Stock";
             $html .= '<tr>
                 <td>' . $i++ . '</td>
                 <td>' . $row->job_number . '</td>
                 <td>' . formatDate($row->job_date) . '</td>
                 <td>' . $row->wo_no . '</td>
                 <td>' . $row->item_code.' '.$row->item_name. '</td>
                 <td></td>
                 <td>' . floatVal($row->qty) . '</td>
                 <td>' . floatVal($row->total_ok_qty) . '</td>
                 <td>' . floatVal($row->total_rej_qty) . '</td>
                 <td>' . $row->emp_name . '</td>
                 <td>' . $row->remark . '</td>
             </tr>';
         endforeach;
         $this->data['jobRegHtml'] = $html;
         $this->load->view($this->jobcard_register, $this->data);
     }

    	
	/* Machine Wise Production */
    public function machineWise(){
        $this->data['pageHeader'] = 'MACHINE WISE OEE REGISTER';
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->load->view($this->machinewise_production,$this->data);
    }

    public function getMachineWiseProduction(){
        $data = $this->input->post();
        $errorMessage = array();

		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $productionData = $this->productionReports->getMachineWiseProduction($data,$data['dept_id']);
            $this->printJson($productionData);
        endif;
    }

 	/* OEE Register */
    public function oeeRegister(){
        $this->data['pageHeader'] = 'GENERAL OEE REGISTER';
        $this->load->view($this->general_oee,$this->data);
    }
    
    /* Jobwork Report Created By Avruti @08/08/2022*/
    public function jobworkReport(){
        $this->data['pageHeader'] = 'JOB WORK REPORT';
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->load->view($this->jobwork_report,$this->data);
    }

 	//Created By Avruti @08/08/2022
    public function getJobworkReport(){ 
        $data = $this->input->post();
        $errorMessage = array();

		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $jobOutData = $this->productionReports->getJobworkReport($data);
            $i=1; $tblData = ""; 
            foreach($jobOutData as $row): 
            $recive_qty = $row->qty + $row->rej_qty + $row->wp_qty;
                $tblData.='<tr>
                                <td>'.$i++.'</td>
                                <td>'.$row->trans_number.'</td>
                                <td>'.formatDate($row->entry_date).'</td>
                                <td>'.$row->challan_no.'</td>
                                <td>'.$row->party_name.'</td>
                                <td>'.$row->full_name.'</td>
                                <td>'.$row->process_name.'</td>
                                <td>'.$recive_qty.'</td>
                                <td>'.$row->com_qty.'</td>
                                <td>'.$row->rej_qty.'</td>
                                <td>'.$row->wp_qty.'</td>
                                <td>'.$row->bill_qty.'</td>';
        
                    $tblData.='</tr>';
                endforeach;
                $this->printJson(['status'=>1,"tblData"=>$tblData]);
        endif;        
    }

    /* Created At: 03-12-2022 [ Milan Chauhan ] */
    public function productionMonitoring(){
        $this->data['pageHeader'] = 'PRODUCTION MONITORING REPORT';
        $this->data['jobcardData'] = $this->productionReports->getJobcardList();
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
        $this->load->view("report/production/production_monitoring", $this->data);
    }

    /* Created At: 03-12-2022 [ Milan Chauhan ] */
    public function getProcessListOnJobCard(){
        $job_id = $this->input->post('job_id');
        $jobData = $this->jobcard->getJobcard($job_id);
        $jobProcess = explode(',',$jobData->process);
        $processData = $this->process->getProcessList(['process_ids'=>$jobProcess]);

        $html = '<option value="">Select Process</option>';
        foreach($processData as $row):
            $html .= '<option value="'.$row->id.'">'.$row->process_name.'</option>';
        endforeach;

        $this->printJson(['status'=>1,'process_list'=>$html]);
    }

    /* Created At: 03-12-2022 [ Milan Chauhan ] */
    public function getProcessWiseMachineList(){
        $process_id = $this->input->post('process_id');
        $machineData = $this->machine->getProcessWiseMachine($process_id);

        $html = '<option value="0">Select Machine</option>';
        foreach($machineData as $row):
            $html .= '<option value="'.$row->id.'">['.$row->item_code.'] '.$row->item_name.'</option>';
        endforeach;

        $this->printJson(['status'=>1,'machine_list'=>$html]);
    }

    /* Created At: 03-12-2022 [ Milan Chauhan ] */
    public function getProductionMonitoringData($job_id="",$process_id="",$machine_id=""){
        if(!empty($job_id)):    
            $data['job_id'] = $job_id;
            $data['process_id'] = $process_id;
            $data['machine_id'] = $machine_id;
            $data['is_pdf'] = 1;
        else:
            $data = $this->input->post();
        endif;
        $productionData = $this->productionReports->getProductionMonitoringData($data);
        //$this->data['sapData'] = $this->controlPlan->getSarIprData(['job_card_id'=>$job_id,'process_id'=>$process_id,'machine_id'=>$machine_id,'limit'=>1,'single_row'=>1]);
        // print_r($this->db->last_query());exit;
        if($data['is_pdf'] == 0):
            $i = 1;
            $tbody = "";
            foreach ($productionData as $row) :
                if(!empty($row->job_card_id)):
                    $idleTimeData = $this->productionReports->getIdleTimeReasonForOee(['entry_date' => $row->entry_date, 'shift_id' => $row->shift_id, 'machine_id' => $row->machine_id, 'process_id' => $row->process_id, 'operator_id' => $row->operator_id, 'product_id' => $row->product_id, 'job_card_id' => $row->job_card_id ]);
                    $td = $idleTimeData['td'];
                    $row->idle_time = $idleTimeData['total_idle_time'];
    
                    $plan_time = !empty($row->shift_hour) ? $row->shift_hour : 660;
                    $performanceTime = $plan_time - $row->idle_time;
                    $ct = (!empty($row->m_ct)) ? ($row->m_ct / 60) : 0;
                    $total_load_unload_time=($row->total_load_unload_time*$row->production_qty)/60;
                    $runTime = $plan_time - $row->idle_time-$total_load_unload_time;
                    $plan_qty = (!empty($runTime) && !empty($ct)) ? ($runTime / $ct) : 0;
                    $availability = ($plan_time > 0 && !empty($runTime) && !empty($plan_time)) ? ($runTime * 100) / $plan_time : 0;
                    if(!empty($performanceTime)){
                        $performance = (!empty($row->cycle_time)) ? (((($row->cycle_time+$row->total_load_unload_time)*$row->production_qty)/($performanceTime))/60)*100 : 0;
                    }else{
                        $performance = 0;
                    }
                    $overall_performance = (!empty($row->cycle_time) && !empty($plan_time)) ? ((((($row->cycle_time+$row->total_load_unload_time)/60)*$row->production_qty) / $plan_time))*100 : 0;
                    $quality_rate=($row->production_qty > 0) ? $row->ok_qty*100/$row->production_qty : 0;
                    $oee = (($availability/100) * ($performance/100) * ($quality_rate/100))*100;
                    
                    $tbody .= '<tr class="text-center">
                        <td>' . $i++ . '</td>
                        <td>' . formatDate($row->entry_date) . '</td>
                        <td>' . $row->shift_name . '</td>
                        <td>' . $row->operator_name . '</td>
                        <td>' . $row->cycle_time . '</td>
                        <td>' . $row->total_load_unload_time . '</td>
                        <td>' . $row->production_qty . '</td>
                        <td>' . $row->rej_qty . '</td>
                        <td>' . $row->rw_qty . '</td>                    
                        <td>' . $plan_time . '</td>
                        <td>' . number_format($runTime,2) . '</td>
                        <td>' . (int)$plan_qty . '</td>
                        <td>' . $row->ok_qty . '</td>
                        <td>' . number_format($total_load_unload_time,2) . '</td>
                        <td>' . $row->idle_time . '</td>
                        ' . $td . '
                        <td>' . number_format($availability,2) . '%</td>
                        <td>' . number_format($overall_performance,2) . '%</td>
                        <td>' . number_format($performance,2) . '%</td>
                        <td>' . number_format($quality_rate,2) . '%</td>
                        <td>' . number_format($oee,2) . '%</td>
                    </tr>';
                endif;
            endforeach;
            $this->printJson(['status' => 1, 'tbody' => $tbody]);
        else:
            $this->data['pageHeader'] = 'PRODUCTION MONITORING REPORT';
            $this->data['productionData'] = $productionData;
            $this->data['jobData'] = $jobCardData = $this->jobcard->getJobcard($data['job_id']);
            $reqMaterials = $this->jobcard->getMaterialIssueData($jobCardData); 
            $this->data['reqMaterials'] = (!empty($reqMaterials['resultData']))?$reqMaterials['resultData']:'';
            $this->data['machineData'] = (!empty($data['machine_id']))?$this->machine->getMachine($data['machine_id']):"";
            $this->data['processData'] = $this->process->getProcess($data['process_id']);
            $this->data['idleReasonList'] = $this->comment->getIdleReason();
            $this->data['jobApprovalData'] =$jobApprovalData= $this->processMovement->getProcessWiseApprovalData($job_id,$process_id);
            $this->data['settingData'] = $this->jobcard->getSettingParamData(['job_approval_id'=>$jobApprovalData->id]);
            $pdfData = $this->load->view('report/production_new/production_monitoring_pdf', $this->data, true);
        
            //$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
            $htmlFooter = '';//'<img src="'.$this->data['letter_footer'].'" class="img">';

            $mpdf = new \Mpdf\Mpdf();
            $pdfFileName = $jobCardData->job_number . '.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetProtection(array('print'));
            //$mpdf->SetHTMLHeader($htmlHeader);
            //$mpdf->SetHTMLFooter($htmlFooter);

            $mpdf->AddPage('L', '', '', '', '', 5, 5, 5, 5, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-L');
            $mpdf->WriteHTML($pdfData);
            $mpdf->Output($pdfFileName, 'I');
        endif;
    }
    
    /* Created At: 04-12-2022 [ Milan Chauhan ] */
    public function dailyProductionLogSheet(){
        $this->data['pageHeader'] = 'DAILY PRODUCTION LOG SHEET';
        $this->load->view("report/production/production_log_sheet", $this->data);
    }

    /* Created At: 04-12-2022 [ Milan Chauhan ] */
    public function getDailyProductionLogSheet($log_date = ""){
        if(!empty($log_date)):
            $data['log_date'] = $log_date;
            $data['is_pdf'] = 1;
        else:
            $data = $this->input->post();
            $data['is_pdf'] = 0;
        endif;

        $productionData = $this->productionReports->getDailyProductionLogSheet($data['log_date']);

        $tbody = '';
        foreach($productionData as $row):
            
            $target_hr = 0; $target_10_hr = 0; $target_12_hr = 0;            
            if($row->cycle_time > 0){
                $target_hr = round((3600 / $row->cycle_time),2);
                $target_10_hr = round((36000 / $row->cycle_time),2);
                $target_12_hr = round((43200 / $row->cycle_time),2);
            }
            
            $tbody .= '<tr class="text-center">
                <td class="text-left">
                    '.$row->emp_name.'
                </td>
                <td class="text-left">
                    '.$row->machine_name.'
                </td>
                <td>
                    '.$row->shift_name.'
                </td>
                <td>
                    '.$row->product_code.'
                </td>
                <td>
                    '.$row->rm_grade.'
                </td>
                <td>
                    '.$row->job_number.'
                </td>
                <td>
                    '.$row->process_name.'
                </td>
                <td>
                    '.$row->cycle_time.'
                </td>
                <td>
                    '.$row->total_production_time.'
                </td>
                <td>
                    '.$row->total_ok_qty.'
                </td>
                <td>
                    '.$target_hr.'
                </td>
                <td>
                    '.$target_10_hr.'
                </td>
                <td>
                    '.$target_12_hr.'
                </td>
                <td>
                    '.$row->pre_finished_weight.'
                </td>
                <td>
                    '.$row->finished_weight.'
                </td>
                <td>
                    '.$row->total_rej_qty.'
                </td>
                <td>
                    '.$row->rej_reason.'
                </td>
                <td>
                    '.$row->total_rw_qty.'
                </td>
                <td>
                    '.$row->rw_reason.'
                </td>
                <td>
                    '.$row->total_idle_time.'
                </td>
                <td>
                    '.$row->idle_reason.'
                </td>
                <td>
                    '.$row->effecincy_per.'
                </td>
            </tr>';
        endforeach;

        if($data['is_pdf'] == 0):
            $this->printJson(['status'=>1,'tbody'=>$tbody]);
        else:
            $pdfData = '';
			$pdfData .= '<html>
				<head>
					<title>
                        DAILY PRODUCTION LOG SHEET
					</title>
				</head>
				<body style="padding:10px;">
					<table class="table table-bordered itemList">                        
						<tr>
							<th style="width:70%;">AKSHAR ENGINEERS</th>
							<th style="width:30%;">
								<img src="'.base_url("assets/images/logo_text.png").'" alt="logo" style="width:20%;">
							</th>
						</tr>
						<tr>
							<th>DAILY PRODUCTION LOG SHEET</th>
							<th>F/PRD/05 (00/01.01.16)</th>
						</tr>
						<tr>
							<th class="text-right" colspan="2">Date : '.formatDate($data['log_date']).'</th>
						</tr>
					</table>
					<table class="table table-bordered itemList">
						<thead class="thead-info" id="theadData">
                            <tr>
                                <th>Operator Name</th>
                                <th>M/C NO.</th>
                                <th>Day/ Night</th>
                                <th>Part Name</th>
                                <th>Metal</th>
                                <th>WO No</th>
                                <th>Set up</th>
                                <th>Cycle time<br>(Sec.)</th>
                                <th>Total time<br>(Min.)</th>
                                <th>Qty</th>
                                <th>Per HR Target</th>
                                <th>Per 10 HR Target</th>
                                <th>Per 12 HR Target</th>
                                <th>Before weight</th>
                                <th>After weight</th>
                                <th>Rejection qty.</th>
                                <th>Rejection reason</th>
                                <th>Rework qty.</th>
                                <th>Rework reason</th>
                                <th>Down time</th>
                                <th>Down time reason</th>
                                <th>Effciency (%)</th>
                            </tr>
						</thead>
						<tbody>
							'.$tbody.'
						</tbody>
					</table>
				</body>
			</html>';

			$mpdf = new \Mpdf\Mpdf();
            $pdfFileName = 'DAILY_PRODUCTION_LOG_SHEET_'.date("d_m_Y",strtotime($data['log_date'])).'.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetProtection(array('print'));

            $mpdf->AddPage('L', '', '', '', '', 5, 5, 5, 5, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-L');
            $mpdf->WriteHTML($pdfData);
            $mpdf->Output($pdfFileName, 'I');
        endif;
    }
    
    /* DEPARTMENT WISE PRODUCTION 
       Created By Meghavi  @21/11/2023  
    */
    public function departmentWiseProduction(){
        $this->data['pageHeader'] = 'DEPARTMENT WISE PRODUCTION';
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->load->view($this->dept_wise_production,$this->data);
    }

    public function getDeptWiseProduction(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $deptData = $this->productionReports->getDeptWiseProduction($data); 
            $tbody="";$i=1;
            foreach($deptData as $row):
                $row->inward_qty = (!empty($row->inward_qty)) ? $row->inward_qty : 0;
                $row->ch_qty = (!empty($row->ch_qty)) ? $row->ch_qty : 0;
                $row->in_qty = (!empty($row->in_qty)) ? $row->in_qty : 0;
                $row->ok_qty = (!empty($row->ok_qty)) ? $row->ok_qty : 0;
                $row->total_out_qty = (!empty($row->total_out_qty)) ? $row->total_out_qty : 0;
                
                $unaccepted_qty = ($row->inward_qty - ($row->in_qty - $row->ch_qty));
                $pending_prod_movement = ($row->ok_qty - $row->total_out_qty);
                
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->job_number.'</td>
                    <td>'.$row->item_code.'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->process_name.'</td>
                    <td>'.floatval($unaccepted_qty).'</td>
                    <td>'.floatval($row->pend_prod_qty).'</td>
                    <td>'.floatval($pending_prod_movement).'</td>
                </tr>';
            endforeach;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
       	/* Rejection Monitoring */
	public function rejectionMonitoring(){
		$this->data['pageHeader'] = 'REJECTION MONITORING REPORT';
		$this->data['itemDataList'] = $this->item->getItemList(1);
		$this->load->view($this->rejection_monitoring,$this->data);
	}

    public function getRejectionMonitoring(){
		$data = $this->input->post(); 
		$errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$data['rtype'] = 2;
 			$rejectionData = $this->productionReports->getRejectionMonitoring($data);
			 $tbody = ''; $tfoot = '';
			 if (!empty($rejectionData)) :
			 	$i = 1;
			 	$totalRejectCost = 0;$totalRejectQty = 0;
			 	foreach ($rejectionData as $row) :
						$totalRejectQty +=$row->qty;
                        $machine_code = (!empty($row->machine_code)) ? '['.$row->machine_code.'] '.$row->machine_name : $row->machine_name;

			 			$tbody .= '<tr>
			                 <td>' . $i++ . '</td>
			                 <td>' . formatDate($row->entry_date) . '</td>
			                 <td>' . $row->item_code . '</td>
			                 <td>' . $row->process_name . '</td>
			                 <td>' . $row->shift_name . '</td>
			                 <td>' . $machine_code . '</td>
			                 <td>' . $row->emp_name . '</td>';
			 			
			 			$tbody .= '<td>' . $row->qty . '</td>
			                 <td>' . $row->rejection_reason . '</td>
			                 <td>' . $row->rej_remark . '</td>
			                 <td>' . $row->rejection_stage . '</td>
			 				 <td>' . (!empty($row->vendor_name) ? $row->vendor_name : 'IN HOUSE') . '</td>
			 			</tr>';
			 		
			 	endforeach;
	 
			$tfoot .= '<tr class="thead-info">
				 <th colspan="7" class="text-right">Total Reject Qty.</th>
				 <th>' . $totalRejectQty . '</th>
			 	<th colspan="4" class="text-right"></th>
			 	</tr>';
			endif;
			 
			$this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
		endif;
	}
}
?>