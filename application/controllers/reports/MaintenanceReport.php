<?php
class MaintenanceReport extends MY_Controller
{
    private $indexPage = "report/maintenance_report/index";
    private $machine_report = "report/maintenance_report/machine_report";
    private $part_replacement = "report/maintenance_report/part_replacement";
    private $maintenanceLBReport = "report/maintenance_report/maintenanceLB_report";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Maintenance Report";
		$this->data['headData']->controller = "reports/maintenanceReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/maintenance_report/floating_menu',[],true);
	}
	
	public function index(){
		$this->data['pageHeader'] = 'MAINTENANCE REPORT';
        $this->load->view($this->indexPage,$this->data);
    }

	public function machineReport(){
        $this->data['pageHeader'] = 'MACHINES REPORT';
        $this->data['machineData'] = $this->machine->getMachineForReport();
        $this->load->view($this->machine_report,$this->data);
    }

    public function partReplacement(){
        $this->data['pageHeader'] = 'PART REPLACEMENT REPORT';
        $this->data['machineData'] = $this->machine->getMachineForReport();
        $this->load->view($this->part_replacement,$this->data);
    }

    public function getPartReplacementData(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $partData = $this->machine->getPartReplacementData($data);//print_r($partData);exit;
            $tbody="";$i=1;$totalQty=0;$tfoot="";
            foreach($partData as $row):
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.($row->trans_prefix.$row->trans_no).'</td>
                    <td>['.$row->itemCode.']'.$row->itemName.'</td>
                    <td>'.date("d-m-Y H:i:s",strtotime($row->issue_date)).'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->req_qty.'</td>
                </tr>';
                $totalQty+=abs($row->req_qty);
            endforeach;
            $tfoot = '<tr>
                <th colspan="5">Total</th>
                <th>'.round($totalQty).'</th>
            </tr>';
            $this->printJson(['status'=>1,'tbody'=>$tbody,'tfoot'=>$tfoot]);
        endif;
    }
    
    
      /* AAVRUTI */
    public function maintenanceLogReport(){
        $this->data['pageHeader'] = 'MAINTENANCE LOG BOOK REPORT';
        $this->load->view($this->maintenanceLBReport,$this->data);
    }

    public function getMachineTicketListByDate(){
        $data = $this->input->post();
        $errorMessage = array();

		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $rejectionData = $this->ticketModel->getMachineTicketListByDate($data);
            $this->printJson($rejectionData);
        endif;
    }

    /*Maintenance Log Print Data */
    public function printMaintenanceLog($pdate){
        $data['from_date']=explode('~',$pdate)[0];
        $data['to_date']=explode('~',$pdate)[1];

        $mlogData = $this->ticketModel->getMachineTicketListByDate($data); 
        
        $logo=base_url('assets/images/logo.png');
		
		$topSectionO ='<table class="table">
						<tr>
							<td style="width:20%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-center" style="font-size:1rem;width:60%">Maintenance Log Book Data</td>
						</tr>
					</table>';
        $itemList='<table id="reportTable" class="table table-bordered align-items-center itemList">
								<thead class="thead-info" id="theadData">
									<tr class="text-center">
										<th rowspan="2">#</th>
										<th rowspan="2">Date</th>
										<th rowspan="2">Ticket No</th>
										<th rowspan="2">Machine No.</th>
                                        <th colspan="3">Time</th>
                                        <th rowspan="2">Problem Description</th>
                                        <th rowspan="2">Solution/Action taken</th>
                                        <th rowspan="2">Solved By</th>
									</tr>
                                    <tr class="text-center">
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Total</th>
                                    </tr>
								</thead>
                                <tbody id="tbodyData">'; 
                               
        $itemList.=$mlogData['tbody'].'</tbody></table>';

	    $originalCopy = '<div style="">'.$topSectionO.$itemList.'</div>';
		
		$pdfData = $originalCopy;
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='Maintenance_Log_Book.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('L','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }
    
    public function printPrevMaintenance(){
        $logo=base_url('assets/images/logo.png');
		$topSectionO ='<table class="table">
						<tr>
							<td style="width:20%;"><img src="'.$logo.'" style="height:50px;"></td>
							<td class="org_title text-center" style="font-size:1.2rem;width:60%;text-align:center;">PREVENTIVE MAINTENANCE PLAN</td>
                            <td style="width:20%;"></td>
                        </tr>
					</table>';
        $itemList='';
        $machineData = $this->machine->getMachineList(); 
        foreach($machineData as $machine):    
            $prevData = $this->machine->getMachinePrevMaintenance($machine->id); $i=1;

            if(!empty($prevData)){
                $itemList.='<table class="table table-bordered align-items-center itemList">
                    <thead class="thead-info">
                        <tr class="text-left" style="font-size:1rem;"><th colspan="6">Machine: '.(!empty($machine->item_code)? '['.$machine->item_code.'] '.$machine->item_name:$machine->item_name).'</th></tr>
                        <tr class="text-center">
                            <th rowspan="2">#</th>
                            <th rowspan="2">Activities to be carried out</th>
                            <th colspan="4">Checking Frequency</th>
                        </tr>
                        <tr class="text-center">
                            <th>Yearly</th>
                            <th>Half Yearly</th>
                            <th>Quarterly</th>
                            <th>Daily</th>
                        </tr>
                    </thead>
                <tbody>';
                foreach($prevData as $row):
                    $daily=''; $yearly=''; $halfyearly=''; $quarterly='';
                    if(!empty($row->checking_frequancy) && $row->checking_frequancy == 'Yearly'){
                        $yearly = '<img src="'.base_url('assets/uploads/check/check.jpg').'" width="30" height="30" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
                    }
                    if(!empty($row->checking_frequancy) && $row->checking_frequancy == 'Half Yearly'){
                        $halfyearly = '<img src="'.base_url('assets/uploads/check/check.jpg').'" width="30" height="30" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
                    }
                    if(!empty($row->checking_frequancy) && $row->checking_frequancy == 'Quarterly'){
                        $quarterly = '<img src="'.base_url('assets/uploads/check/check.jpg').'" width="30" height="30" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
                    }
                    if(!empty($row->checking_frequancy) && $row->checking_frequancy == 'Daily'){
                        $daily = '<img src="'.base_url('assets/uploads/check/check.jpg').'" width="30" height="30" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
                    }

                    $itemList.='<tr class="text-center">
                        <td>'.$i++.'</td>
                        <td>'.$row->activities.'</td>
                        <td>'.$yearly.'</td>
                        <td>'.$halfyearly.'</td>
                        <td>'.$quarterly.'</td>
                        <td>'.$daily.'</td>
                    </tr>';
                endforeach;
                $itemList.= '</tbody></table>';
            }
        endforeach;

	    $originalCopy = '<div style="">'.$topSectionO.$itemList.'</div>';
		
		$pdfData = $originalCopy;
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='Maintenance_Log_Book.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('L','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }
}
?>