<?php
class Payroll extends MY_Controller
{
    private $indexPage = "hr/payroll/index";
    private $payrollForm = "hr/payroll/form";
    private $editEmpSalaryForm = "hr/payroll/edit_emp_salary_form";
    private $payrollView = "hr/payroll/view";
    private $payrollDataPage = "hr/payroll/payroll_data";
	private $view_payroall = "hr/payroll/view_payroall";
	  
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Payroll";
		$this->data['headData']->controller = "hr/payroll";
		$this->data['headData']->pageUrl = "hr/payroll";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('payroll');
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows(){
        $result = $this->payroll->getDTRows($this->input->post());
		$sendData = array();$i=1;
        foreach($result['data'] as $row):      
			$row->sr_no = $i++;
			$row->salary_sum = $this->payroll->getSalarySumByMonth($row->sal_month )->salary_sum;
            $sendData[] = getPayrollData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	
    public function loadSalaryForm(){
        $start = new DateTime($this->startYearDate);
        $start->modify('first day of this month');
        $end = new DateTime($this->endYearDate);
        $end->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $monthList = array();
        foreach ($period as $dt): $monthList[] = $dt->format("Y-m-t"); endforeach;

        $this->data['monthList'] = (object) $monthList;
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->load->view($this->payrollForm,$this->data);
    }
    
    public function getPayrollData($month){
        $this->data['empData'] = $this->payroll->getPayrollData($month);
        $this->load->view($this->payrollDataPage,$this->data);
    }

    public function makeSalary(){
        $this->data['empData'] = $this->payroll->getEmpSalary();
        $this->load->view($this->payrollForm,$this->data);
    }
    
    public function save(){
        $data = $this->input->post(); 
        $errorMessage = array();
        if(empty($data['month']))
            $errorMessage['month'] = "Month is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->payroll->save($data));
        endif;
    }

    public function edit($month){
        $start = new DateTime($this->startYearDate);
        $start->modify('first day of this month');
        $end = new DateTime($this->endYearDate);
        $end->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $monthList = array();
        foreach ($period as $dt): $monthList[] = $dt->format("Y-m-t"); endforeach;

        $this->data['monthList'] = (object) $monthList;
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['ctcFormat'] = $this->salaryStructure->getCtcFormats();
        
        $salaryData = $this->payroll->getPayrollData($month);
        $ctcFormat = $this->salaryStructure->getCtcFromat($salaryData[0]->format_id);
        $this->data['earningHeads'] = $this->salaryStructure->getSalaryHeadList(['type'=>1,'ids'=>$ctcFormat->eh_ids]);
        $this->data['deductionHeads'] = $this->salaryStructure->getSalaryHeadList(['type'=>-1,'ids'=>$ctcFormat->dh_ids]);
        $this->data['salaryData'] = $salaryData;
        //print_r($salaryData);exit;
        $this->load->view($this->payrollForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->payroll->delete($id));
        endif;
    }

    /*************************** Load Salary Data***************************************/    
    public function getEmployeeSalaryData_old($dept_id="",$format_id="",$month="",$file_type="pdf"){
        if($_SERVER['REQUEST_METHOD'] === 'POST'):
            $data = $this->input->post();
        else:
            $data['dept_id'] = $dept_id;
            $data['format_id'] = $format_id;
            $data['month'] = $month;
            $data['file_type'] = $file_type;
            $data['view'] = 1;
        endif;

        $ctcFormat = $this->salaryStructure->getCtcFromat($data['format_id']);
        $earningHeads = $this->salaryStructure->getSalaryHeadList(['type'=>1,'ids'=>$ctcFormat->eh_ids]);
        $deductionHeads = $this->salaryStructure->getSalaryHeadList(['type'=>-1,'ids'=>$ctcFormat->dh_ids]);

        $headCount = (empty($data['view']))?12:11;
        $eth = '';$betd = '';
        foreach($earningHeads as $row):
            $eth .= '<th>'.$row->head_name.'</th>';
            $betd .= '<td>0</td>';
            $headCount++;
        endforeach;

        $dth = '';$bdtd = '';
        foreach($deductionHeads as $row):
            $dth .= '<th>'.$row->head_name.'</th>';
            $bdtd .= '<td>0</td>';
            $headCount++;
        endforeach;
        
        $thead = '<tr>
            <th>Emp Code</th>
            <th>Emp Name</th>
            <th>Total Days</th>
            <th>Present</th>
            <th>Absent</th>
            '.$eth.'
            <th>Gross Salary</th>
            '.$dth.'
            <th>Advance</th>
            <th>Loan</th>
            <th>Net Salary</th>
            <th>Actual Salary</th>
            <th>Difference</th>
            '.((empty($data['view']))?"<th>Action</th>":"").'
        </tr>';

        $empData = $this->payroll->getEmployeeListForSalary($data);
        
        
        $empAttendanceData = $this->biometric->getSalaryHours(['from_date'=>$data['month'],'dept_id'=>$data['dept_id'],'payroll'=>1]);
        $canteenCharges = $this->masterModel->getMasterOptions();
        $empAttendanceData['cl_charge'] = $canteenCharges->cl_charge;
        $empAttendanceData['cd_charge'] = $canteenCharges->cd_charge; 
        $empAttendanceData['month'] = $data['month'];
                
        $html = '';$sr_no=1; 
        $empAttendanceData['totalDays'] = date("t",strtotime($data['month'])); 
        $holiday = countDayInMonth("Wednesday",$data['month']);
        $empAttendanceData['totalDays'] -= $holiday; 
        if(!empty($empData)):
            foreach($empData as $row):  
                $empSalaryData =  $this->calculateEmpSalaryData($sr_no,$row,$empAttendanceData,$earningHeads,$deductionHeads);

                $empSalaryData['betd'] = $betd;
                $empSalaryData['bdtd'] = $bdtd;
                $empSalaryData['view'] = $data['view'];
                $rowHtml = $this->getEmployeeSalaryHtml($empSalaryData);

                $html .= "<tr id='".$sr_no."'>".$rowHtml."</tr>";

                $sr_no++;
            endforeach;
        else:
            if(empty($data['view'])):
                /*$html = '<tr>
                    <td id="noData" class="text-center" colspan="'.$headCount.'">No data available in table</td>
                </tr>';*/
            endif;
        endif;
        
        if($_SERVER['REQUEST_METHOD'] === 'POST')
		{
            $this->printJson(['status'=>1,'emp_salary_head'=>$thead,'emp_salary_html'=>$html]);
        }
		else
		{
            $response = '<table class="table-bordered jpExcelTable" border="1" repeat_header="1">';
            $response .= '<thead>'.$thead.'</thead><tbody>'.$html.'</tbody></table>';
            if($data['file_type'] == 'excel'):
				$xls_filename = 'payroll-'.date("m-Y",strtotime($data['month'])).'.xls';
				
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment; filename='.$xls_filename);
				header('Pragma: no-cache');
				header('Expires: 0');
				
				echo $response;
			else:
			    $companyData = $this->attendance->getCompanyInfo();
				$htmlHeader = '<div class="table-wrapper">
                    <table class="table txInvHead">
                        <tr class="txRow">
                            <td class="fs-17 text-left" style="letter-spacing: 1px;font-weight:bold;">'.$companyData->company_name.'</td>
                            <td class="text-right pad-right-10"><b>Report Month : '.date("F-Y",strtotime($data['month'])).'</td>
                        </tr>
                    </table>
                </div>';
				$htmlFooter = '<table style="border-top:1px solid #000;padding:3px;">
                    <tr>
                        <td style="width:50%;text-align:left;">Printed On {DATE j-m-Y}</td>
                        <td style="width:50%;text-align:right;">Page No :- {PAGENO}</td>
                    </tr>
                </table>';
				
				$mpdf = $this->m_pdf->load();
				$pdfFileName='payroll-'.date("m-Y",strtotime($data['month'])).'.xls';
				$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
				$mpdf->WriteHTML($stylesheet,1);
				$mpdf->SetDisplayMode('fullpage');
				$mpdf->SetProtection(array('print'));
				
				$mpdf->SetHTMLHeader($htmlHeader);
				$mpdf->SetHTMLFooter($htmlFooter);
				
				$mpdf->AddPage('L','','','','',5,5,17,10,5,0,'','','','','','','','','','A4-L');
				$mpdf->WriteHTML($response);
				$mpdf->Output($pdfFileName,'I');
			endif;
        }
    }
    
    public function viewSalary(){
        $start = new DateTime($this->startYearDate);
        $start->modify('first day of this month');
        $end = new DateTime($this->endYearDate);
        $end->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $monthList = array();
        foreach ($period as $dt): $monthList[] = $dt->format("Y-m-t"); endforeach;

        $this->data['monthList'] = (object) $monthList;
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['ctcFormat'] = $this->salaryStructure->getCtcFormats();
        $this->load->view($this->payrollView,$this->data);   
    }
    
    public function getEmployeeActualSalaryData($dept_id="",$format_id="",$month="",$file_type="pdf"){
        $data['dept_id'] = $dept_id;
        $data['format_id'] = $format_id;
        $data['month'] = $month;
        
        $ctcFormat = $this->salaryStructure->getCtcFromat($data['format_id']);
        $postData = ['type'=>1,'ids'=>$ctcFormat->eh_ids];
        //if($ctcFormat->salary_duration == "H"): $postData['is_system'] = 0; endif;
        $earningHeads = $this->salaryStructure->getSalaryHeadList($postData);

        $postData['type'] = -1;
        $postData['ids'] = $ctcFormat->dh_ids;
        $deductionHeads = $this->salaryStructure->getSalaryHeadList($postData);

        $headCount = 9;
        $eth = '';$betd = '';
        foreach($earningHeads as $row):
            $eth .= '<th>'.$row->head_name.'</th>';
            $betd .= '<td>0</td>';
            $headCount++;
        endforeach;

        $dth = '';$bdtd = '';
        foreach($deductionHeads as $row):
            $dth .= '<th>'.$row->head_name.'</th>';
            $bdtd .= '<td>0</td>';
            $headCount++;
        endforeach;

        
        $thead = '<tr>
            <th>#</th>
            <th>Employee Name</th>
            <th>'.(($ctcFormat->salary_duration == "H")?"Wage":"Total Days").'</th>
            <th>'.(($ctcFormat->salary_duration == "H")?"Rate Hour":"Present").'</th>
            <th>'.(($ctcFormat->salary_duration == "H")?"Working Hour":"Absent").'</th>
            '.$eth.'
            <th>Gross Salary</th>
            '.$dth.'
            <th>Advance</th>
            <th>Loan</th>
            <th>Actual Salary</th>
        </tr>';
        
        $empData = $this->payroll->getEmployeeListForSalary($data);
        $empAttendanceData = $this->biometric->getSalaryHours(['from_date'=>$data['month'],'dept_id'=>$data['dept_id'],'payroll'=>1]);
        $canteenCharges = $this->masterModel->getMasterOptions();
        $empAttendanceData['cl_charge'] = $canteenCharges->cl_charge;
        $empAttendanceData['cd_charge'] = $canteenCharges->cd_charge;      
        $empAttendanceData['month'] = $data['month'];  

        $empAttendanceData['totalDays'] = date("t",strtotime($data['month'])); 
        $holiday = countDayInMonth("Wednesday",$data['month']);
        $empAttendanceData['totalDays'] -= $holiday; $sr_no = 1;
        $html = "";
        if(!empty($empData)):
            foreach($empData as $row):
                $empSalaryData =  $this->calculateEmpSalaryData($sr_no,$row,$empAttendanceData,$earningHeads,$deductionHeads);

                $empSalaryData['betd'] = $betd;
                $empSalaryData['bdtd'] = $bdtd;

                $etd = "";
                foreach($empSalaryData['earning_data'] as $row):
                    $etd .= "<td>".$row['org_amount']."</td>";
                endforeach;

                $dtd = "";
                foreach($empSalaryData['deduction_data'] as $row):
                    $dtd .= "<td>".$row['org_amount']."</td>";
                endforeach;

                $empSalaryData['etd'] = $etd;
                $empSalaryData['dtd'] = $dtd;

                $row = (object) $empSalaryData;
                $html .= "<tr>
                    <td>".$row->sr_no."</td>
                    <td>
                        ".$row->emp_name."
                    </td>
                    <td>
                        ".(($row->salary_basis == "H")?$row->wage:$row->working_days)."
                    </td>                                                                    
                    <td>
                        ".(($row->salary_basis == "H")?$row->r_hr:$row->present_days)."
                    </td>
                    <td>
                        ".(($row->salary_basis == "H")?$row->total_wh:$row->absent_days)."
                    </td>
                    ".((!empty($row->etd))?$row->etd:$row->betd)."
                    <td>
                        ".$row->org_total_earning."            
                    </td>
                    ".((!empty($row->dtd))?$row->dtd:$row->bdtd)."
                    <td>".$row->org_advance_deduction."</td>
                    <td>".$row->org_emi_amount."</td>
                    <td>
                        ".$row->actual_sal."
                    </td>
                </tr>";
                $sr_no++;
            endforeach;
        else:
            $html = '<tr>
                <td id="noData" class="text-center" colspan="'.$headCount.'">No data available in table</td>
            </tr>';
        endif;
        
        $response = '<table class="table-bordered itemList" border="1" repeat_header="1">';
        $response .= '<thead>'.$thead.'</thead><tbody>'.$html.'</tbody></table>';
        $xls_filename = 'actual-payroll-'.date("m-Y",strtotime($data['month'])).'.xls';
				
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename='.$xls_filename);
		header('Pragma: no-cache');
		header('Expires: 0');
		
		echo $response;
    }

    public function editEmployeeSalaryData(){
        $data = $this->input->post();
        $salaryData = $data['salary_data'][$data['key_value']];
        $sr_no = $data['key_value'];
        $salaryData = (object) $salaryData;    
        $salaryData->earning_data = (!empty($salaryData->earning_data))?json_decode($salaryData->earning_data):array();
        $salaryData->deduction_data = (!empty($salaryData->deduction_data))?json_decode($salaryData->deduction_data):array();

        $ctcFormat = $this->salaryStructure->getCtcFromat($data['format_id']);
        $earningHeads = $this->salaryStructure->getSalaryHeadList(['type'=>1,'ids'=>$ctcFormat->eh_ids]);
        $deductionHeads = $this->salaryStructure->getSalaryHeadList(['type'=>-1,'ids'=>$ctcFormat->dh_ids]);

        $empData = $this->payroll->getEmployeeSalaryStructure($salaryData->structure_id);
        $empAttendanceData = $this->biometric->getSalaryHours(['from_date'=>$data['month'],'dept_id'=>$data['dept_id'],'emp_id'=>$salaryData->emp_id,'payroll'=>1]);
       
        $canteenCharges = $this->masterModel->getMasterOptions();
        $empAttendanceData['cl_charge'] = $canteenCharges->cl_charge;
        $empAttendanceData['cd_charge'] = $canteenCharges->cd_charge;      
        $empAttendanceData['month'] = $data['month'];  

        $empAttendanceData['totalDays'] = date("t",strtotime($data['month'])); 
        $holiday = countDayInMonth("Wednesday",$data['month']);
        $empAttendanceData['totalDays'] -= $holiday;      
        if(!empty($empData)):
            $empSalaryData = $this->calculateEmpSalaryData($sr_no,$empData,$empAttendanceData,$earningHeads,$deductionHeads,$salaryData);           
        endif;

        $this->data['salaryData'] = (object) $empSalaryData;
        $this->load->view($this->editEmpSalaryForm,$this->data);
    }

    public function saveEmployeeSalaryData(){
        $data = $this->input->post();
        $data['sr_no'] = $data['row_index'];
        $etd = "";
        foreach($data['earning_data'] as $row):
            $etd .= "<td>".$row['amount']."</td>";
        endforeach;

        $dtd = "";
        foreach($data['deduction_data'] as $row):
            $dtd .= "<td>".$row['amount']."</td>";
        endforeach;

        $data['etd'] = $etd;
        $data['dtd'] = $dtd;
        $data['view'] = 0;

        $html = $this->getEmployeeSalaryHtml($data);
        $this->printJson(['status'=>1,'salary_data'=>$html]);
    }

    public function calculateEmpSalaryData($sr_no,$empData,$empAttendanceData,$earningHeads,$deductionHeads,$salaryData = array()){
        $cl_charge = $empAttendanceData['cl_charge'];
        $cd_charge = $empAttendanceData['cd_charge'];  
        $empSalarayHeads = (!empty($empData->salary_head_json))?json_decode($empData->salary_head_json):array();

        $totalDays =  $empAttendanceData['totalDays'];
        $total_wh = (isset($empAttendanceData[$empData->emp_id]))?round(($empAttendanceData[$empData->emp_id]['twh']/3600),2):0;
        $tot = (isset($empAttendanceData[$empData->emp_id]))?round(($empAttendanceData[$empData->emp_id]['tot']/3600),2):0;
        $present = (isset($empAttendanceData[$empData->emp_id]))?($empAttendanceData[$empData->emp_id]['tpd']):0;
        $absent = $totalDays - $present; 

        $empEarningData = array();$empDeductionData = array();$etd = '';$dtd = '';
        $actual_wage=0;$r_hr = 0;$actual_salary = 0;
        $grossSalary = 0; $orgGrossSalary = 0;
        $totalDeduction = 0; $orgDeduction=0;
        $netSalary = 0; $orgNetSalary = 0; 

        if($empData->salary_duration == "H"):
            $actual_wage = ((!empty($empData->ctc_amount))?$empData->ctc_amount:0);
            $r_hr = ($actual_wage / 8);
            $actual_salary = round(($r_hr * $total_wh),0);
        endif;

        $basicAmount = 0;$hraAmount = 0;$orgBasicAmt = 0;$orgHraAmount = 0;
        
        if(!empty($salaryData->earning_data) && $salaryData->total_wh == $total_wh):
            $empEarningData = (array)$salaryData->earning_data;
            $grossSalary = $salaryData->total_earning;
            $orgGrossSalary = $salaryData->org_total_earning;
        else:
            foreach($earningHeads as $erow):
                $amount = 0;$value = 0;$orgAmount = 0;$orgValue = 0;
                if((!empty($empSalarayHeads->{$erow->id}->cal_method) && $empSalarayHeads->{$erow->id}->cal_method == 1)):
                    $value = ((!empty($empSalarayHeads->{$erow->id}->cal_value))?$empSalarayHeads->{$erow->id}->cal_value:0);
                    $amount = round((($value/$totalDays)*$present),0);  
                    
                    $orgValue = ((!empty($empSalarayHeads->{$erow->id}->org_cal_value))?$empSalarayHeads->{$erow->id}->org_cal_value:0);
                    $orgAmount = round((($orgValue/$totalDays)*$present),0);  
                else:
                    $value = ((!empty($empSalarayHeads->{$erow->id}->cal_value))?$empSalarayHeads->{$erow->id}->cal_value:0);
                    $value = round((($basicAmount * $value)/100),0);
                    $amount = round((($value/$totalDays)*$present),0);

                    $orgValue = ((!empty($empSalarayHeads->{$erow->id}->org_cal_value))?$empSalarayHeads->{$erow->id}->org_cal_value:0);
                    $orgValue = round((($orgBasicAmt * $orgValue)/100),0);
                    $orgAmount = round((($orgValue/$totalDays)*$present),0);
                endif;

                if($empData->salary_duration == "H" && $erow->system_code == "basic"):
                    $orgAmount = $actual_salary;
                endif;

                $cal_method = (!empty($empSalarayHeads->{$erow->id}->cal_method))?$empSalarayHeads->{$erow->id}->cal_method:0;
                $cal_value = (!empty($empSalarayHeads->{$erow->id}->cal_value))?$empSalarayHeads->{$erow->id}->cal_value:0;

                if($erow->system_code == "ca" && !empty($empData->traveling_charge)):
                    $orgAmount += round(($present * $empData->traveling_charge),0);
                endif;

                $amount = round($amount,0);
                $orgAmount = round($orgAmount,0);
                $empEarningData[$erow->id]['head_name'] = $erow->head_name;
                $empEarningData[$erow->id]['system_code'] = $erow->system_code;
                $empEarningData[$erow->id]['cal_method'] = $cal_method;
                $empEarningData[$erow->id]['cal_value'] = $cal_value;
                $empEarningData[$erow->id]['amount'] = $amount;
                $empEarningData[$erow->id]['org_amount'] = $orgAmount;

                if($erow->system_code == "basic"): $basicAmount = round($amount,0); $orgBasicAmt = round($orgAmount,0); endif;       
                if($erow->system_code == "hra"): $hraAmount = round($amount,0); $orgHraAmount = round($orgAmount,0); endif;    
                $grossSalary += $amount;
                $orgGrossSalary += $orgAmount;
                $etd .= '<td>'.$amount.'</td>';
            endforeach;
        endif;

        if(!empty($salaryData->deduction_data) && $salaryData->total_wh == $total_wh):
            $empDeductionData = (array)$salaryData->deduction_data;
            $totalDeduction = $salaryData->total_deduction;
            $orgDeduction = $salaryData->org_total_deduction;
        else:
            foreach($deductionHeads as $drow):
                $amount = 0;$value = 0;$orgAmount = 0;$orgValue = 0;

                if(in_array($drow->system_code,["pf","pt","lwf","ccl","ccd"])):
                    if($drow->system_code == "pf" && !empty($empSalarayHeads->{$drow->id}->cal_value) && $empData->pf_applicable == 1):
                        $pfValuation = ($grossSalary - $hraAmount);
                        if($pfValuation >= 15000):
                            $orgAmount = $amount = ((15000 * $empSalarayHeads->{$drow->id}->cal_value) / 100);
                        else:
                            $orgAmount = $amount = (($pfValuation * $empSalarayHeads->{$drow->id}->cal_value) / 100);
                        endif;

                        /*$orgPfValuation = ($orgGrossSalary - $orgHraAmount);
                        if($orgPfValuation >= 15000):
                            $orgAmount = ((15000 * $empSalarayHeads->{$drow->id}->cal_value) / 100);
                        else:
                            $orgAmount = (($orgPfValuation * $empSalarayHeads->{$drow->id}->cal_value) / 100);
                        endif;*/
                    endif;

                    if($drow->system_code == "pt" && !empty($empSalarayHeads->{$drow->id}->cal_value)):
                        if($grossSalary >= 12000):
                            $orgAmount = $amount = $empSalarayHeads->{$drow->id}->cal_value;
                        endif;
                        /*if($orgGrossSalary >= 12000):
                            $orgAmount = $empSalarayHeads->{$drow->id}->cal_value;
                        endif;*/
                    endif;

                    if($drow->system_code == "lwf" && !empty($empSalarayHeads->{$drow->id}->cal_value)):
                        if(in_array(date("m",strtotime($empAttendanceData['month'])),["06","12"])):
                            $orgAmount = $amount = $empSalarayHeads->{$drow->id}->cal_value;
                        endif;
                    endif;

                    if($drow->system_code == "ccl" && !empty($cl_charge)):
                        $orgAmount = $amount = round(($present * $cl_charge),0);
                    endif;

                    if($drow->system_code == "ccd" && !empty($cd_charge)):
                        $orgAmount = $amount = round(($present * $cd_charge),0);
                    endif;                          
                else:
                    if((!empty($empSalarayHeads->{$drow->id}->cal_method) && $empSalarayHeads->{$drow->id}->cal_method == 1)):
                        $value = ((!empty($empSalarayHeads->{$drow->id}->cal_value))?$empSalarayHeads->{$drow->id}->cal_value:0);
                        $amount = round((($value/$totalDays)*$present),0); 
                        
                        $orgValue = ((!empty($empSalarayHeads->{$erow->id}->org_cal_value))?$empSalarayHeads->{$erow->id}->org_cal_value:0);
                        $orgAmount = round((($orgValue/$totalDays)*$present),0);  
                    else:
                        $value = ((!empty($empSalarayHeads->{$drow->id}->cal_value))?$empSalarayHeads->{$drow->id}->cal_value:0);
                        $value = round((($grossSalary * $value)/100),0);
                        $amount = round((($value/$totalDays)*$present),0);

                        $orgValue = ((!empty($empSalarayHeads->{$erow->id}->org_cal_value))?$empSalarayHeads->{$erow->id}->org_cal_value:0);
                        $orgValue = round((($orgGrossSalary * $orgValue)/100),0);
                        $orgAmount = round((($orgValue/$totalDays)*$present),0);
                    endif;
                endif;

                $cal_method = (!empty($empSalarayHeads->{$drow->id}->cal_method))?$empSalarayHeads->{$drow->id}->cal_method:0;
                $cal_value = (!empty($empSalarayHeads->{$drow->id}->cal_value))?$empSalarayHeads->{$drow->id}->cal_value:0;

                $amount = round($amount,0);
                $orgAmount = round($orgAmount,0);
                $empDeductionData[$drow->id]['head_name'] = $drow->head_name;
                $empDeductionData[$drow->id]['system_code'] = $drow->system_code;
                $empDeductionData[$drow->id]['cal_method'] = $cal_method;
                $empDeductionData[$drow->id]['cal_value'] = $cal_value;
                $empDeductionData[$drow->id]['amount'] = $amount;
                $empDeductionData[$drow->id]['org_amount'] = $orgAmount;

                $totalDeduction += $amount;
                $orgDeduction += $orgAmount;
                $dtd .= '<td>'.$amount.'</td>';
            endforeach; 
        endif;

        // Advance Salary
        $adsData = (!empty($empAttendanceData[$empData->emp_id]['advance_data']))?$empAttendanceData[$empData->emp_id]['advance_data']:array();
        $adSalary=0;$orgAdSalary=0;
        $a=0;$adsHtml='';$adSalaryData=array();
        if(!empty($salaryData->advance_data)):
            $adSalaryData = $salaryData->advance_data;
            $adSalary = $salaryData->advance_deduction;
            $orgAdSalary = $salaryData->org_advance_deduction;
        else:
            foreach($adsData as $adsRow):
                $adSalaryData[$a] = [
                    'id'=>$adsRow->id,
                    'entry_date' => $adsRow->entry_date,
                    'payment_mode' => $adsRow->payment_mode,
                    'amount'=> ($adsRow->payment_mode != "CS")?$adsRow->pending_amount:0,
                    'org_amount' => ($adsRow->payment_mode == "CS")?$adsRow->pending_amount:0
                ];
                $adSalary += ($adsRow->payment_mode != "CS")?$adsRow->pending_amount:0;
                $orgAdSalary += ($adsRow->payment_mode == "CS")?$adsRow->pending_amount:0;
                $a++;
            endforeach;
        endif;

        // Employee Loans
        $l=0;$loanEmi=0;$orgLoanEmi=0;$pendingLoan=0;$emiAmount=0;$loanHtml = '';
        $loanData = (!empty($empAttendanceData[$empData->emp_id]['loan_data']))?$empAttendanceData[$empData->emp_id]['loan_data']:array();
        $loanDataRows = array();        
        if(!empty($salaryData->loan_data)):
            foreach($salaryData->loan_data as $loanRow):
                $loanRow = (object) $loanRow;
                $loanDataRows[$l] = [
                    'id'=>$loanRow->id,
                    'payment_mode' => $loanRow->payment_mode,
                    'loan_no'=>$loanRow->loan_no,
                    'amount'=> $loanRow->amount,
                    'org_amount'=> $loanRow->org_amount,
                    'loan_amount'=> $loanRow->loan_amount
                ];
                $loanEmi += $loanRow->amount;
                $orgLoanEmi += $loanRow->org_amount;
                $pendingLoan += ($loanRow->loan_amount - ($loanRow->amount + $loanRow->org_amount));
                $l++;
            endforeach;
        else:
            foreach($loanData as $loanRow):
                $emiAmount = ($loanRow->pending_amount > $loanRow->emi_amount)?$loanRow->emi_amount:$loanRow->pending_amount;
                
                $loanDataRows[$l] = [
                    'id'=>$loanRow->id,
                    'payment_mode' => $loanRow->payment_mode,
                    'loan_no'=>$loanRow->loan_no,
                    'amount'=> ($loanRow->payment_mode != "CS")?$emiAmount:0,
                    'org_amount'=> ($loanRow->payment_mode == "CS")?$emiAmount:0,
                    'loan_amount'=>$loanRow->pending_amount
                ];
                $loanEmi += ($loanRow->payment_mode != "CS")?$emiAmount:0;
                $orgLoanEmi += ($loanRow->payment_mode == "CS")?$emiAmount:0;
                $pendingLoan += ($loanRow->pending_amount - $emiAmount);
                $l++;
            endforeach;
        endif;
        

        $orgDeduction += $orgAdSalary;
        $orgDeduction += $orgLoanEmi;
        
        $totalDeduction += $adSalary;
        $totalDeduction += $loanEmi;

        $orgNetSalary = round($orgGrossSalary -  $orgDeduction,0); // Actual Net Pay
        $netSalary = round($grossSalary - $totalDeduction,0); // On Paper Net Pay
        $sal_diff = round($orgNetSalary - $netSalary,0); // Salary Difference [Actual - On Parer]
        
        $dataRow = [
            'sr_no' => $sr_no,
            'id' => (!empty($salaryData->id))?$salaryData->id:"",
            'structure_id' => $empData->id,
            'emp_id' => $empData->emp_id,
            'emp_code' => $empData->emp_code,
            'emp_name' => $empData->emp_name,
            'emp_type' => $empData->emp_type,
            'salary_code' => $empData->salary_code,
            'salary_basis' => $empData->salary_duration,
            'pf_applicable' => $empData->pf_applicable,
            'total_wh' => $total_wh,
            'tot' => $tot,
            'wage' => $actual_wage,
            'r_hr' => $r_hr,
            'actual_sal' => $orgNetSalary,
            'sal_diff' => $sal_diff,
            'present_days' => $present,
            'working_days' => $totalDays,
            'absent_days' => $absent,
            'etd' => $etd,
            'org_total_earning' => $orgGrossSalary,
            'total_earning' => $grossSalary,
            'earning_data' => $empEarningData,
            'dtd' => $dtd,
            'org_total_deduction' => $orgDeduction,
            'total_deduction' => $totalDeduction,
            'deduction_data' => $empDeductionData,
            'advance_deduction' => $adSalary,
            'org_advance_deduction' => $orgAdSalary,
            'advance_data' => $adSalaryData,
            'emi_amount' => $loanEmi,
            'org_emi_amount' => $orgLoanEmi,
            'loan_data' => $loanDataRows,
            'pending_loan' => $pendingLoan,
            'net_salary' => $netSalary
        ];

        return $dataRow;
    }

    public function getEmployeeSalaryHtml($row){
        $row = (object) $row;
        $salaryCode = '"'.$row->salary_code.'"';
        $editButton = "<button type='button' class='btn btn-outline-warning' title='Edit' onclick='Edit(".$row->sr_no.", ".$salaryCode.");'><i class='ti-pencil-alt'></i></button>";

        $hiddenInputs = "";
        if(empty($row->view)):
            $hiddenInputs = "<input type='hidden' name='salary_data[".$row->sr_no."][id]' value='".$row->id."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][structure_id]' value='".$row->structure_id."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][emp_id]' value='".$row->emp_id."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][emp_name]' value='".$row->emp_name."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][emp_type]' value='".$row->emp_type."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][pf_applicable]' value='".$row->pf_applicable."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][salary_code]' value='".$row->salary_code."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][salary_basis]' value='".$row->salary_basis."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][total_wh]' value='".$row->total_wh."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][tot]' value='".$row->tot."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][wage]' value='".$row->wage."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][r_hr]' value='".$row->r_hr."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][actual_sal]' value='".$row->actual_sal."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][sal_diff]' value='".$row->sal_diff."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][present_days]' value='".$row->present_days."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][working_days]' value='".$row->working_days."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][absent_days]' value='".$row->absent_days."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][total_earning]' value='".$row->total_earning."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][org_total_earning]' value='".$row->org_total_earning."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][earning_data]' value='".json_encode($row->earning_data)."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][net_salary]' value='".$row->net_salary."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][total_deduction]' value='".$row->total_deduction."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][org_total_deduction]' value='".$row->total_deduction."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][deduction_data]' value='".json_encode($row->deduction_data)."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][advance_deduction]' value='".$row->advance_deduction."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][org_advance_deduction]' value='".$row->org_advance_deduction."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][emi_amount]' value='".$row->emi_amount."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][org_emi_amount]' value='".$row->org_emi_amount."'>";

            $a=0;
            if(!empty($row->advance_data)):
                foreach($row->advance_data as $adsRow):
                    $adsRow = (object) $adsRow;
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][advance_data][".$a."][id]' value='".$adsRow->id."'>";
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][advance_data][".$a."][entry_date]' value='".$adsRow->entry_date."'>";
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][advance_data][".$a."][payment_mode]' value='".$adsRow->payment_mode."'>";
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][advance_data][".$a."][amount]' value='".$adsRow->amount."'>";
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][advance_data][".$a."][org_amount]' value='".$adsRow->org_amount."'>";
                    $a++;
                endforeach;
            endif;

            $l=0;
            if(!empty($row->loan_data)):
                foreach($row->loan_data as $loanRow):
                    $loanRow = (object) $loanRow;
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][loan_data][".$l."][id]' value='".$loanRow->id."'>";
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][loan_data][".$l."][payment_mode]' value='".$loanRow->payment_mode."'>";
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][loan_data][".$l."][loan_no]' value='".$loanRow->loan_no."'>";
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][loan_data][".$l."][amount]' value='".$loanRow->amount."'>";
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][loan_data][".$l."][org_amount]' value='".$loanRow->org_amount."'>";
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][loan_data][".$l."][loan_amount]' value='".$loanRow->loan_amount."'>";
                    $l++;
                endforeach;
            endif;
        endif;

        $html = "<td>".$row->emp_code."</td>
        <td>
            ".$row->emp_name."
            ".((empty($row->view))?$hiddenInputs:"")."
        </td>
        <td>
            ".$row->working_days."
        </td>                                                                    
        <td>
            ".$row->present_days."
        </td>
        <td>
            ".$row->absent_days."
        </td>
        ".((!empty($row->etd))?$row->etd:$row->betd)."
        <td>
            ".$row->total_earning."            
        </td>
        ".((!empty($row->dtd))?$row->dtd:$row->bdtd)."
        <td>".$row->advance_deduction."</td>
        <td>".$row->emi_amount."</td>
        <td>
            ".$row->net_salary."
        </td>
        <td>
            ".$row->actual_sal."
        </td>
        <td>
            ".$row->sal_diff."
        </td>";

        $html .= (empty($row->view))?"<td>".$editButton."</td>":"";
        return $html;
    }
	
	
	/******** Load Salary Data Created BY JP@29.08.2023 **********/    
    public function getEmployeeSalaryData($dept_id="",$format_id="",$month="",$file_type="pdf"){
        if($_SERVER['REQUEST_METHOD'] === 'POST'):
            $data = $this->input->post();
        else:
            $data['dept_id'] = $dept_id;
            $data['month'] = $month;
            $data['file_type'] = $file_type;
            $data['view'] = 1;
        endif;
		$data['dates'] = date('Y-m-01',strtotime($data['month'])) .'~'.$data['month'];
		$sal_month = $data['from_date'] = date('Y-m-01',strtotime($data['month']));
		$data['to_date'] = date('Y-m-t',strtotime($data['month']));
		
		
        $headCount = (empty($data['view']))?12:11;
        $eth = '';$betd = '';
        $dth = '';$bdtd = '';
        $thead = '<tr class="text-center">
						<th>Emp Code</th>
						<th>Emp Name</th>
						<th>Present<br>Days</th>
						<th>Week<br>Off</th>
						<th>Total<br>Days</th>
						<th>Working<br>Hours</th>
						<th>Basic</th>
						<th>HRA</th>
						<th class="bg-light-green">Gross Earnings</th>
						<th>P.F.</th>
						<th>E.S.I.</th>
						<th>Professional<br>Tax</th>
						<th>T.D.S.</th>
						<th>Advance</th>
						<th>Loan EMI</th>
						<th>Transport</th>
						<th>Food<br>Deduction</th>
						<th class="bg-light-green">Gross<br>Deduction</th>
						<th class="bg-warning">Net Salary</th>
						<th>Action</th>
					</tr>';
		
        //$empData = $this->employee->getEmpListForAttendance($data);
        //$empAttendanceData = $this->biometric->getSalaryHours(['from_date'=>$data['month'],'dept_id'=>$data['dept_id'],'payroll'=>1]);
        $empData = $this->biometric->getPunchLog($data);
		//print_r($empData);exit;
        $html = '';$sr_no=1; $empTable='';$hiddenInputs='';$saveButton='';
        $totalDays = date("t",strtotime($data['month'])); 
        if(!empty($empData))
		{
			//$masterOptions = $this->masterOption->getMasterOptions();
            foreach($empData as $row)
			{
				$row = (Array) $row;
				$row['total_days'] = $row['total_days'];
				$row['present'] = $row['present'];
				$row['week_off'] = $row['week_off'];
				$row['absent'] = $row['absent'];
				$row['sal_month'] = $sal_month;
				$row['advance_salary'] = 0;
				$row['loan_emi'] = 0;
				$row['ot_hrs'] = (!empty($row['tot'])) ? round((abs($row['tot'])/3600),2) : 0;
				$row['total_hrs'] = (!empty($row['twh'])) ? round((abs($row['twh'])/3600),2) : 0;
				$row['wh_hrs'] = (!empty($row['wh'])) ? round((abs($row['wh'])/3600),2) : 0;
				
				
				$salData = $this->countSalary($row);
				if(!empty($salData))
				{
					$empTable .= '<tr class="text-center emp_line'.$row['id'].'">';
						$empTable .= '<td style="text-align:center;">'.$salData['emp_code'].'</td>';
						$empTable .= '<td>'.$salData['emp_name'].'</td>';
						$empTable .= '<td>'.$salData['present'].'</td>';
						$empTable .= '<td>'.$salData['week_off'].'</td>';
						$empTable .= '<td>'.$salData['total_days'].'</td>';
						$empTable .= '<td>'.$salData['total_hrs'].'</td>';
						$empTable .= '<td>'.$salData['basic_salary'].'</td>';
						$empTable .= '<td>'.$salData['hra_amt'].'</td>';
						$empTable .= '<th id="gross_sal'.$salData['emp_id'].'" class="bg-light-green">'.$salData['gross_sal'].'</th>';
						$empTable .= '<td>'.$salData['emp_pf'].'</td>';
						$empTable .= '<td>'.$salData['emp_esic'].'</td>';
						$empTable .= '<td>'.$salData['pt'].'</td>';
						$empTable .= '<td>'.$salData['tds'].'</td>';
						$empTable .= '<td>'.$salData['advance_salary'].'</td>';
						$empTable .= '<td>'.$salData['loan_emi'].'</td>';
						$empTable .= '<td>'.$salData['transport_charge'].'</td>';
						$empTable .= '<td>'.$salData['food'].'</td>';
						$empTable .= '<th id="gross_deduction'.$salData['emp_id'].'" class="bg-light-green">'.$salData['gross_deduction'].'</th>';
						$empTable .= '<th id="net_salary'.$salData['emp_id'].'" class="bg-warning">'.$salData['net_salary'].'</th>';
						$empTable .= '<th><button type="button" class="btn btn-primary float-right reCalculatecSal" data-id="'.$row['id'].'" >Save</button></th>';
					$empTable .= '</tr>';
				}
				unset($salData['present'],$salData['week_off'],$salData['pl'],$salData['cl'],$salData['advance_salary'],$salData['food']);
				$hiddenInputs.="<div class='hiddenDiv".$row['id']."'>";
				foreach($salData as $key=>$val)
				{
					$name = 'salary_data['.$row['id'].']['.$key.']';
					$id = $key.$row['id'];
					$hiddenInputs.="<input type='hidden' name='".$name."' id='".$id."' class='salData".$row['id']."' alt='".$key."' value='".$val."'>";
				}
				$hiddenInputs.="</div>";
                $sr_no++;
            }
			$hiddenInputs.="<input type='hidden' name='salary_month' id='salary_month' value='".$sal_month."'>";
			$saveParam = "'savePayRoll','save'";
			$saveButton = '<div class="col-md-9"></div>
							<div class="col-md-3">
								<button type="button" class="btn btn-success btn-save btn-block float-right" onclick="saveSalary('.$saveParam.');"><i class="fa fa-check"></i> Save</button>
							</div>';
        }
		else
		{
            if(empty($data['view'])):
                /*$html = '<tr>
                    <td id="noData" class="text-center" colspan="'.$headCount.'">No data available in table</td>
                </tr>';*/
            endif;
        }
        
        if($_SERVER['REQUEST_METHOD'] === 'POST')
		{
            $this->printJson(['status'=>1,'emp_salary_head'=>$thead,'emp_salary_html'=>$empTable,'hidden_inputs'=>$hiddenInputs,"save_button"=>$saveButton]);
        }
		else
		{
            $response = '<table class="table-bordered jpExcelTable" border="1" repeat_header="1">';
            $response .= '<thead>'.$thead.'</thead><tbody>'.$empTable.'</tbody></table>';
            if($data['file_type'] == 'excel')
			{
				$xls_filename = 'payroll-'.date("m-Y",strtotime($data['month'])).'.xls';
				
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment; filename='.$xls_filename);
				header('Pragma: no-cache');
				header('Expires: 0');
				
				echo $response;
			}
			else
			{
			    $companyData = $this->attendance->getCompanyInfo();
				$htmlHeader = '<div class="table-wrapper">
                    <table class="table txInvHead">
                        <tr class="txRow">
                            <td class="fs-17 text-left" style="letter-spacing: 1px;font-weight:bold;">'.$companyData->company_name.'</td>
                            <td class="text-right pad-right-10"><b>Report Month : '.date("F-Y",strtotime($data['month'])).'</td>
                        </tr>
                    </table>
                </div>';
				$htmlFooter = '<table style="border-top:1px solid #000;padding:3px;">
                    <tr>
                        <td style="width:50%;text-align:left;">Printed On {DATE j-m-Y}</td>
                        <td style="width:50%;text-align:right;">Page No :- {PAGENO}</td>
                    </tr>
                </table>';
				
				$mpdf = new \Mpdf\Mpdf();
				$pdfFileName='payroll-'.date("m-Y",strtotime($data['month'])).'.xls';
				$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
				$mpdf->WriteHTML($stylesheet,1);
				$mpdf->SetDisplayMode('fullpage');
				$mpdf->SetProtection(array('print'));
				
				$mpdf->SetHTMLHeader($htmlHeader);
				$mpdf->SetHTMLFooter($htmlFooter);
				
				$mpdf->AddPage('L','','','','',5,5,17,10,5,0,'','','','','','','','','','A4-L');
				$mpdf->WriteHTML($response);
				$mpdf->Output($pdfFileName,'I');
			}
        }
    }
	
	// Load On Paper Salary Data
	public function getEmployeeSalaryDataOP($dept_id="",$format_id="",$month="",$file_type="pdf"){
        if($_SERVER['REQUEST_METHOD'] === 'POST'):
            $data = $this->input->post();
        else:
            $data['dept_id'] = $dept_id;
            $data['month'] = $month;
            $data['file_type'] = $file_type;
            $data['view'] = 1;
        endif;
		$data['dates'] = date('Y-m-01',strtotime($data['month'])) .'~'.$data['month'];
		$sal_month = $data['from_date'] = date('Y-m-01',strtotime($data['month']));
		$data['to_date'] = date('Y-m-t',strtotime($data['month']));
		
		
        $headCount = (empty($data['view']))?12:11;
        $eth = '';$betd = '';
        $dth = '';$bdtd = '';
        $thead = '<tr class="text-center">
						<th>Emp Code</th>
						<th>Actual Salary</th>
						<th>Emp Name</th>
						<th>Present off <br>Abs<br>C.L<br>P.H</th>
						<th>BasicConve. Allow<br>H.R.A<br>Madical<br>Child Educa.<br> Office wear all.<br> Grade Allow </th>
						<th>Total Pay</th>
						<th>Basic</th>
						<th>Food Allow</th>
						<th>H.R.A</th>
						<th>Other allow</th>
						<th>Gross Amount</th>
						<th>TDS</th>
						<th>Other</th>
						<th>Advance</th>
						<th>PF</th>
						<th>PT</th>
						<th>Total Ded.</th>
						<th class="bg-warning">Net  Payable</th>
						<th>Action</th>
					</tr>';
		
        //$empData = $this->employee->getEmpListForAttendance($data);
        //$empAttendanceData = $this->biometric->getSalaryHours(['from_date'=>$data['month'],'dept_id'=>$data['dept_id'],'payroll'=>1]);
        $empData = $this->biometric->getPunchLog($data);
		//print_r($empData);exit;
        $html = '';$sr_no=1; $empTable='';$hiddenInputs='';$saveButton='';
        $totalDays = date("t",strtotime($data['month'])); 
        if(!empty($empData))
		{
			//$masterOptions = $this->masterOption->getMasterOptions();
            foreach($empData as $row)
			{
				$row = (Array) $row;
				$row['total_days'] = $row['total_days'];
				$row['present'] = $row['present'];
				$row['week_off'] = $row['week_off'];
				$row['absent'] = $row['absent'];
				$row['sal_month'] = $sal_month;
				$row['advance_salary'] = 0;
				$row['loan_emi'] = 0;
				$row['ot_hrs'] = (!empty($row['tot'])) ? round((abs($row['tot'])/3600),2) : 0;
				$row['total_hrs'] = (!empty($row['twh'])) ? round((abs($row['twh'])/3600),2) : 0;
				$row['wh_hrs'] = (!empty($row['wh'])) ? round((abs($row['wh'])/3600),2) : 0;
				
				
				$salData = $this->countSalaryOP($row);
                // print_r($salData);
				if(!empty($salData))
				{
                    $gross_sal = $salData['basic_salary']+$salData['food']+$salData['hra_amt']+$salData['other_all'];
					$empTable .= '<tr class="text-center emp_line'.$row['id'].'">';
						$empTable .= '<td style="text-align:center;">'.$salData['emp_code'].'</td>';
                        $empTable .= '<td >
                                            <input type="text" name="salary_data['.$row['id'].'][salary]" id="salary'.$row['id'].'" value="'.$salData['gross_sal'].'" class="form-control " data-id="'.$row['id'].'" readonly>
                                        </td>';
						$empTable .= '<td>'.$salData['emp_name'].' 
                                            <input type="hidden" name="salary_data['.$row['id'].'][emp_id]" id="id'.$row['id'].'" data-id="'.$row['id'].'" class="floatOnly salData'.$row['id'].'" alt="emp_id" value="'.$row['id'].'" style="width:100px;">
                                        </td>';
						$empTable .= '<td>
                                        <input type="text" name="salary_data['.$row['id'].'][present]" id="present'.$row['id'].'" data-id="'.$row['id'].'" class="floatOnly  form-control calTotalPay" alt="present" value="'.$salData['present'].'" style="width:100px;">
                                        <input type="hidden" name="salary_data['.$row['id'].'][week_off]" id="week_off'.$row['id'].'" data-id="'.$row['id'].'" class="floatOnly salData'.$row['id'].'" alt="week_off" value="'.$salData['week_off'].'" style="width:100px;">
                                    </td>';
                                    
						$empTable .= '<td>
						                <input type="text" name="salary_data['.$row['id'].'][wages]" id="wages'.$row['id'].'" data-id="'.$row['id'].'" class="form-control floatOnly calTotalPay salData'.$row['id'].'" alt="week_off" value="'.$salData['wages'].'" style="width:100px;">
					               </td>';
						$empTable .= '<td>
						                <input type="text"  name="salary_data['.$row['id'].'][total_pay]" id="total_pay'.$row['id'].'" value="'.$salData['total_pay'].'" class="form-control calWages" data-id="'.$row['id'].'">
						              </td>';
						$empTable .= '<td>
                                        <input type="text" name="salary_data['.$row['id'].'][basic_salary]" id="basic_salary'.$row['id'].'" value="'.$salData['basic_salary'].'" class="form-control floatOnly   salData'.$row['id'].'">
                                      </td>';
						$empTable .= '<td>'.$salData['food'].'
                                        <input type="hidden"  name="salary_data['.$row['id'].'][food]" id="food'.$row['id'].'" value="'.$salData['food'].'">
                                    </td>';
						$empTable .= '<td>
                                        <input type="text" name="salary_data['.$row['id'].'][hra]"  id="hra'.$row['id'].'" value="'.$salData['hra_amt'].'" class=" form-control floatOnly" data-id="'.$row['id'].'">
                                    </td>';
						$empTable .= '<td>
                                        <input type="text" name="salary_data['.$row['id'].'][other_all]"  id="other_all'.$row['id'].'" value="'.$salData['other_all'].'" class=" form-control floatOnly" data-id="'.$row['id'].'">
                                    </td>';
                        
						$empTable .= '<td >
                                        <input type="text" readonly name="salary_data['.$row['id'].'][gross_sal]" id="gross_sal'.$row['id'].'" value="'.$salData['gross_sal'].'" class="form-control " data-id="'.$row['id'].'">
                                    </td>';
						$empTable .= '<td>'.$salData['tds'].'</td>';
						$empTable .= '<td></td>';
						$empTable .= '<td>
                                        <input type="text" name="salary_data['.$row['id'].'][advance_salary]" id="advance_salary'.$row['id'].'" value="'.$salData['advance_salary'].'" class="form-control floatOnly " data-id="'.$row['id'].'">
                                    </td>';
						$empTable .= '<td>'.$salData['emp_pf'].'
                                        <input type="hidden" id="emp_pf'.$row['id'].'" value="'.$salData['emp_pf'].'">
                                        </td>';
						$empTable .= '<td>'.$salData['pt'].'
                                        <input type="hidden" id="pt'.$row['id'].'" value="'.$salData['pt'].'">
                                        </td>';
                        $empTable .= '<th ><input type="text" readonly name="salary_data['.$row['id'].'][gross_deduction]"  id="gross_deduction'.$row['id'].'" value="'.$salData['gross_deduction'].'" class=" form-control" data-id="'.$row['id'].'"></th>';
						$empTable .= '<th " class="bg-warning"><input type="text" readonly name="salary_data['.$row['id'].'][net_salary]"  id="net_salary'.$row['id'].'" value="'.$salData['net_salary'].'" class=" form-control" data-id="'.$row['id'].'"></th>';
					    
					    $empTable .= '<th>
                					    <button type="button" class="btn btn-primary float-right reCalculatecSal" data-id="'.$row['id'].'" >Save</button><br>
                					    <button type="button" class="btn btn-outline-danger float-right " data-id="'.$row['id'].'" onclick="removeEmployee(this)"><i class="fas fa-trash-alt"></i></button>
                					  </th>';
					$empTable .= '</tr>';
				}
				unset($salData['present'],$salData['week_off'],$salData['pl'],$salData['cl'],$salData['advance_salary'],$salData['food'],$salData['other_all'],$salData['gross_deduction'],$salData['net_salary'],$salData['hra_amt'],$salData['gross_sal'],$salData['wages'],$salData['basic_salary'],$salData['total_pay']);
				$hiddenInputs.="<div class='hiddenDiv".$row['id']."'> ";
				foreach($salData as $key=>$val)
				{
					$name = 'salary_data['.$row['id'].']['.$key.']';
					$id = $key.$row['id'];
					$hiddenInputs.="<input type='hidden' name='".$name."' id='".$id."' class='salData".$row['id']."' alt='".$key."' value='".$val."'>";
				}
				$hiddenInputs.="</div>";
                $sr_no++;
            }
			$hiddenInputs.="<input type='hidden' name='salary_month' id='salary_month' value='".$sal_month."'>
                        <input type='hidden' name='salary_type' id='salary_type' value='2'>
                            ";
			$saveParam = "'savePayRoll','save'";
			$saveButton = '<div class="col-md-9"></div>
							<div class="col-md-3">
								<button type="button" class="btn btn-success btn-save btn-block float-right" onclick="saveSalary('.$saveParam.');"><i class="fa fa-check"></i> Save</button>
							</div>';
        }
		else
		{
            if(empty($data['view'])):
                /*$html = '<tr>
                    <td id="noData" class="text-center" colspan="'.$headCount.'">No data available in table</td>
                </tr>';*/
            endif;
        }
        
        if($_SERVER['REQUEST_METHOD'] === 'POST')
		{
            $this->printJson(['status'=>1,'emp_salary_head'=>$thead,'emp_salary_html'=>$empTable,'hidden_inputs'=>$hiddenInputs,"save_button"=>$saveButton]);
        }
		else
		{
            $response = '<table class="table-bordered jpExcelTable" border="1" repeat_header="1">';
            $response .= '<thead>'.$thead.'</thead><tbody>'.$empTable.'</tbody></table>';
            if($data['file_type'] == 'excel')
			{
				$xls_filename = 'payroll-'.date("m-Y",strtotime($data['month'])).'.xls';
				
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment; filename='.$xls_filename);
				header('Pragma: no-cache');
				header('Expires: 0');
				
				echo $response;
			}
			else
			{
			    $companyData = $this->attendance->getCompanyInfo();
				$htmlHeader = '<div class="table-wrapper">
                    <table class="table txInvHead">
                        <tr class="txRow">
                            <td class="fs-17 text-left" style="letter-spacing: 1px;font-weight:bold;">'.$companyData->company_name.'</td>
                            <td class="text-right pad-right-10"><b>Report Month : '.date("F-Y",strtotime($data['month'])).'</td>
                        </tr>
                    </table>
                </div>';
				$htmlFooter = '<table style="border-top:1px solid #000;padding:3px;">
                    <tr>
                        <td style="width:50%;text-align:left;">Printed On {DATE j-m-Y}</td>
                        <td style="width:50%;text-align:right;">Page No :- {PAGENO}</td>
                    </tr>
                </table>';
				
				$mpdf = new \Mpdf\Mpdf();
				$pdfFileName='payroll-'.date("m-Y",strtotime($data['month'])).'.xls';
				$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
				$mpdf->WriteHTML($stylesheet,1);
				$mpdf->SetDisplayMode('fullpage');
				$mpdf->SetProtection(array('print'));
				
				$mpdf->SetHTMLHeader($htmlHeader);
				$mpdf->SetHTMLFooter($htmlFooter);
				
				$mpdf->AddPage('L','','','','',5,5,17,10,5,0,'','','','','','','','','','A4-L');
				$mpdf->WriteHTML($response);
				$mpdf->Output($pdfFileName,'I');
			}
        }
    }
	
	
	public function countSalary($postData){
				
        $salData = Array();$html = '';$strData = '';
        if(!empty($postData['id']))
		{
			$salConfig = new stdClass();$oldStructureId = 0;
			$actStr = $this->employee->getActiveSalaryStructure($postData['id']);
			
			if(!empty($actStr))
			{
				$salConfig = $actStr;
			
				$salAmount = (!empty($salConfig->sal_amount)) ? $salConfig->sal_amount : 0;
				$basic_per = (!empty($salConfig->basic_per)) ? $salConfig->basic_per : 0;
				$basicSalary = (!empty($salConfig->basic_salary)) ? $salConfig->basic_salary : 0;
				$hra_per = (!empty($salConfig->hra_per)) ? $salConfig->hra_per : 0;
				$grossSal = (!empty($salConfig->gross_sal)) ? $salConfig->gross_sal : 0;
				$transport_charge = (!empty($actStr)) ? $salConfig->transport_charge : 0;
				$adv_bonus = (!empty($salConfig->adv_bonus)) ? $salConfig->adv_bonus : 0;
				$wh_day = (!empty($salConfig->wh_day)) ? $salConfig->wh_day : 0;
				$rate_hour = (!empty($salConfig->rate_hour)) ? $salConfig->rate_hour : 0;
				$food = (!empty($salConfig->food)) ? $salConfig->food : 0;
				$tds = (!empty($salConfig->tds)) ? $salConfig->tds : 0;			
				$salConfig->pt_limit = 12000; // IF GROSS SALARY > 12000 THEN 200 ELSE 0
				$ot_amt=0;$pl_used=0;$cl_used=0;
				$wages='';		
				if($salConfig->sal_format == 1)
				{
					$other_all = 0;$emp_pf = 0;$emp_esic = 0;$cmp_esic = 0;$pl_used=0;$cl_used=0;$bonus = 1800;
					if(empty($postData['attendance_type'])){$postData['present']=$postData['total_days'] - $postData['week_off'];}
					$workingDays = $postData['total_days'] - $postData['week_off'];
										
					$dailyBasic = (!empty($workingDays)) ? round((floor($basicSalary / $workingDays)),2) : 0 ;
					$basic_salary = ($workingDays == $postData['present']) ? $basicSalary : round($dailyBasic * $workingDays);
					
					$dailyGross = (!empty($workingDays)) ? round($grossSal / $workingDays) : 0 ;
					$gross_sal = ($workingDays == $postData['present']) ? $grossSal : round($dailyGross * $workingDays);
					
					$hra_amt = round((($basic_salary * $salConfig->hra_per)/100),0);
					$other_all = $gross_sal - $basic_salary - $hra_amt;
					$wages = $salConfig->rate_hour;
					// OT
					$ot_amt = round($rate_hour * $postData['ot_hrs']);
					
					$gross_sal += $ot_amt;
					
					// If PF LIMIT SET THEN CHECK
					$pfCalcOn = ($salConfig->pf_on == 1) ? ($basic_salary + $other_all) : $basic_salary;
					if(!empty($salConfig->pf_limit) AND $salConfig->pf_limit > 0)
					{
						$pfCalcOn = ($pfCalcOn < $salConfig->pf_limit) ? $pfCalcOn : $salConfig->pf_limit;
					}
					else
					{
						$pfCalcOn = $pfCalcOn;
					}
					if($salConfig->pf_status == 1){$emp_pf = round((($pfCalcOn * $salConfig->pf_per)/100),0);}
					$cmp_pf = $emp_pf;
					
					// Calc ESIC
					if(!empty($salConfig->esic_status)){$emp_esic = round((($gross_sal*$salConfig->emp_esic_per)/100),0);}
					if(!empty($salConfig->esic_status)){$cmp_esic = round((($gross_sal*$salConfig->cmp_esic_per)/100),0);}
					
					$pt = ($gross_sal > $salConfig->pt_limit)  ? 200 : 0;
					$gross_deduction = $emp_pf + $emp_esic + $pt + $tds + $salConfig->transport_charge + $postData['advance_salary'] + $postData['loan_emi'];				
					
					$gratuity = (!empty($salConfig->gratuity)) ? round((($basic_salary*$salConfig->gratuity)/100),0) : 0;
					
					$net_salary = $gross_sal - $gross_deduction +  $adv_bonus;
					
					
				}
				if(in_array($salConfig->sal_format,[2,3]))
				{
					$other_all = 0;$emp_pf = 0;$emp_esic = 0;$cmp_esic = 0;$bonus = 1800;$basic_salary = 0;$hra_amt = 0;
					if(empty($postData['attendance_type'])){$postData['present']=$postData['total_days'] - $postData['week_off'];}
					$workingDays = $postData['total_days'] - $postData['week_off'];
					$wages = $salConfig->rate_hour*$salConfig->wh_day;
					if($salConfig->sal_format == 3)
					{
						$workingDays = $postData['present'];
						$otherBasic = (!empty($workingDays)) ? round((floor($other_all / $workingDays)),2) : 0 ;
						$other_all = (!empty($salConfig->other_all)) ? $salConfig->other_all : 0;
						
						$dailyBasic = (!empty($workingDays)) ? round((floor($basicSalary / $workingDays)),2) : 0 ;
						$basic_salary = ($workingDays == $postData['present']) ? $basicSalary : round(($dailyBasic * $workingDays),2);
						
						$dailyHRA = (!empty($workingDays)) ? round($salConfig->hra_amt / $workingDays) : 0 ;
						$hra_amt = ($workingDays == $postData['present']) ? $salConfig->hra_amt : round($dailyHRA * $workingDays);
					}
					else
					{
						$monthSal = round(($rate_hour * $postData['total_hrs']));
						
						$basic_salary = round((($monthSal*$salConfig->basic_per)/100));
						$hra_amt = $monthSal - $basic_salary;
						
						$ot_amt = round($rate_hour * $postData['ot_hrs']);
					}
					
					
					$gross_sal = $basic_salary + $hra_amt + $other_all;
					
					// If PF LIMIT SET THEN CHECK
					$pfCalcOn = ($salConfig->pf_on == 1) ? ($basic_salary + $other_all) : $basic_salary;
					if(!empty($salConfig->pf_limit) AND $salConfig->pf_limit > 0)
					{
						$pfCalcOn = ($pfCalcOn < $salConfig->pf_limit) ? $pfCalcOn : $salConfig->pf_limit;
					}
					else
					{
						$pfCalcOn = $pfCalcOn;
					}
					if($salConfig->pf_status == 1){$emp_pf = round((($pfCalcOn * $salConfig->pf_per)/100),0);}
					$cmp_pf = $emp_pf;
					
					// Calc ESIC
					if(!empty($salConfig->esic_status)){$emp_esic = round((($gross_sal*$salConfig->emp_esic_per)/100),0);}
					if(!empty($salConfig->esic_status)){$cmp_esic = round((($gross_sal*$salConfig->cmp_esic_per)/100),0);}
					
					$pt = ($gross_sal > $salConfig->pt_limit)  ? 200 : 0;
					$gross_deduction = $emp_pf + $emp_esic + $pt + $tds + $salConfig->transport_charge + $postData['advance_salary'] + $postData['loan_emi'];				
					
					$gratuity = (!empty($salConfig->gratuity)) ? round((($basic_salary*$salConfig->gratuity)/100),0) : 0;
					
					$net_salary = $gross_sal - $gross_deduction +  $adv_bonus;
				}
				$salData['id'] = "";
				$salData['ss_id'] = $salConfig->id;
				$salData['sal_month'] = $postData['sal_month'];
				$salData['emp_id'] = $postData['id'];
				$salData['emp_name'] = $postData['emp_name'];
				$salData['emp_code'] = $postData['emp_code'];
				$salData['total_hrs'] = $postData['total_hrs'];
				$salData['wh_hrs'] = $postData['wh_hrs'];
				$salData['ot_hrs'] = $postData['ot_hrs'];
				$salData['total_days'] = $postData['total_days'];
				$salData['present'] = $postData['present'];
				$salData['week_off'] = $postData['week_off'];
				$salData['rate_hour'] = $rate_hour;
				$salData['sal_format'] = $salConfig->sal_format;
				$salData['basic_salary'] = $basic_salary;
				$salData['hra_amt'] = $hra_amt;
				$salData['other_all'] = $other_all;
				$salData['gross_sal'] = $gross_sal;
				$salData['emp_pf'] = $emp_pf;
				$salData['cmp_pf'] = $cmp_pf;
				$salData['emp_esic'] = $emp_esic;
				$salData['cmp_esic'] = $cmp_esic;
				$salData['tds'] = $tds;
				$salData['pt'] = $pt;
				$salData['transport_charge'] = $salConfig->transport_charge;
				$salData['food'] = $food;
				$salData['advance_salary'] = $postData['advance_salary'];
				$salData['loan_emi'] = $postData['loan_emi'];
				$salData['bonus'] = $bonus;
				$salData['gratuity'] = $gratuity;
				$salData['gross_deduction'] = $gross_deduction;
				$salData['net_salary'] = $net_salary;
				$salData['wages'] = $wages;
			}
		}
		return $salData;
    }

    
    /** Count Salary For On Paper*/
    public function countSalaryOP($postData){
				
        $salData = Array();$html = '';$strData = '';
        if(!empty($postData['id']))
		{
			$salConfig = new stdClass();$oldStructureId = 0;
			$actStr = $this->employee->getActiveSalaryStructure($postData['id']);
			
			if(!empty($actStr))
			{
				$salConfig = $actStr;
			
				$salAmount = (!empty($salConfig->sal_amount)) ? $salConfig->sal_amount : 0;
				$basic_per = (!empty($salConfig->basic_per)) ? $salConfig->basic_per : 0;
				$basicSalary = (!empty($salConfig->basic_salary)) ? $salConfig->basic_salary : 0;
				$hra_per = 40;//(!empty($salConfig->hra_per)) ? $salConfig->hra_per : 0;
				$grossSal = (!empty($salConfig->gross_sal)) ? $salConfig->gross_sal : 0;
				$transport_charge = (!empty($actStr)) ? $salConfig->transport_charge : 0;
				$adv_bonus = (!empty($salConfig->adv_bonus)) ? $salConfig->adv_bonus : 0;
				$wh_day = (!empty($salConfig->wh_day)) ? $salConfig->wh_day : 0;
				$rate_hour = (!empty($salConfig->rate_hour)) ? $salConfig->rate_hour : 0;
				$food = (!empty($salConfig->food)) ? $salConfig->food : 0;
				$tds = (!empty($salConfig->tds)) ? $salConfig->tds : 0;			
				$salConfig->pt_limit = 12000; // IF GROSS SALARY > 12000 THEN 200 ELSE 0
				$ot_amt=0;$pl_used=0;$cl_used=0;
				$wages='';		
				if($salConfig->sal_format == 1)
				{
					$other_all = 0;$emp_pf = 0;$emp_esic = 0;$cmp_esic = 0;$pl_used=0;$cl_used=0;$bonus = 1800;
					if(empty($postData['attendance_type'])){$postData['present']=$postData['total_days'] - $postData['week_off'];}
					$workingDays = $postData['total_days'] - $postData['week_off'];
										
					$dailyBasic = (!empty($workingDays)) ? round((floor($basicSalary / $workingDays)),2) : 0 ;
					$basic_salary = ($workingDays == $postData['present']) ? $basicSalary : round($dailyBasic * $workingDays);
					
					$dailyGross = (!empty($workingDays)) ? round($grossSal / $workingDays) : 0 ;
					$gross_sal = ($workingDays == $postData['present']) ? $grossSal : round($dailyGross * $workingDays);
					
					$hra_amt = round((($basic_salary * $salConfig->hra_per)/100),0);
					$other_all = $gross_sal - $basic_salary - $hra_amt;
					$wages = $salConfig->rate_hour;
					// OT
					$ot_amt = round($rate_hour * $postData['ot_hrs']);
					
					$gross_sal += $ot_amt;
					
					// If PF LIMIT SET THEN CHECK
					$pfCalcOn = ($salConfig->pf_on == 1) ? ($basic_salary + $other_all) : $basic_salary;
					if(!empty($salConfig->pf_limit) AND $salConfig->pf_limit > 0)
					{
						$pfCalcOn = ($pfCalcOn < $salConfig->pf_limit) ? $pfCalcOn : $salConfig->pf_limit;
					}
					else
					{
						$pfCalcOn = $pfCalcOn;
					}
					if($salConfig->pf_status == 1){$emp_pf = round((($pfCalcOn * $salConfig->pf_per)/100),0);}
					$cmp_pf = $emp_pf;
					
					// Calc ESIC
					if(!empty($salConfig->esic_status)){$emp_esic = round((($gross_sal*$salConfig->emp_esic_per)/100),0);}
					if(!empty($salConfig->esic_status)){$cmp_esic = round((($gross_sal*$salConfig->cmp_esic_per)/100),0);}
					
					$pt = ($gross_sal > $salConfig->pt_limit)  ? 200 : 0;
					$gross_deduction = $emp_pf + $emp_esic + $pt + $tds + $salConfig->transport_charge + $postData['advance_salary'] + $postData['loan_emi'];				
					
					$gratuity = (!empty($salConfig->gratuity)) ? round((($basic_salary*$salConfig->gratuity)/100),0) : 0;
					
					$net_salary = $gross_sal - $gross_deduction +  $adv_bonus;
					
					
				}
				if(in_array($salConfig->sal_format,[2,3]))
				{
					$other_all = 0;$emp_pf = 0;$emp_esic = 0;$cmp_esic = 0;$bonus = 1800;$basic_salary = 0;$hra_amt = 0;
					if(empty($postData['attendance_type'])){$postData['present']=$postData['total_days'] - $postData['week_off'];}
					$workingDays = $postData['total_days'] - $postData['week_off'];
					$wages = $salConfig->rate_hour*$salConfig->wh_day;
					if($salConfig->sal_format == 3)
					{
						$workingDays = $postData['present'];
						$otherBasic = (!empty($workingDays)) ? round((floor($other_all / $workingDays)),2) : 0 ;
						$other_all = (!empty($salConfig->other_all)) ? $salConfig->other_all : 0;
						
						$dailyBasic = (!empty($workingDays)) ? round((floor($basicSalary / $workingDays)),2) : 0 ;
						$basic_salary = ($workingDays == $postData['present']) ? $basicSalary : round(($dailyBasic * $workingDays),2);
						
						$dailyHRA = (!empty($workingDays)) ? round($salConfig->hra_amt / $workingDays) : 0 ;
						//$hra_amt = ($workingDays == $postData['present']) ? $salConfig->hra_amt : round($dailyHRA * $workingDays); // old Calculation
						$hra_amt = ($basic_salary*40)/100;
					}
					else
					{
						$monthSal = round(($rate_hour * $postData['total_hrs']));
						
						$basic_salary = round((($monthSal*$salConfig->basic_per)/100));
				// 		$hra_amt = $monthSal - $basic_salary; // Old calulation
				        $hra_amt = ($basic_salary*40)/100;
						
						$ot_amt = round($rate_hour * $postData['ot_hrs']);
					}
					
					
					$gross_sal = $basic_salary + $hra_amt + $other_all;
					
					// If PF LIMIT SET THEN CHECK
					$pfCalcOn = ($salConfig->pf_on == 1) ? ($basic_salary + $other_all) : $basic_salary;
					if(!empty($salConfig->pf_limit) AND $salConfig->pf_limit > 0)
					{
						$pfCalcOn = ($pfCalcOn < $salConfig->pf_limit) ? $pfCalcOn : $salConfig->pf_limit;
					}
					else
					{
						$pfCalcOn = $pfCalcOn;
					}
					if($salConfig->pf_status == 1){$emp_pf = round((($pfCalcOn * $salConfig->pf_per)/100),0);}
					$cmp_pf = $emp_pf;
					
					// Calc ESIC
					if(!empty($salConfig->esic_status)){$emp_esic = round((($gross_sal*$salConfig->emp_esic_per)/100),0);}
					if(!empty($salConfig->esic_status)){$cmp_esic = round((($gross_sal*$salConfig->cmp_esic_per)/100),0);}
					
					$pt = ($gross_sal > $salConfig->pt_limit)  ? 200 : 0;
					$gross_deduction = $emp_pf + $emp_esic + $pt + $tds + $salConfig->transport_charge + $postData['advance_salary'] + $postData['loan_emi'];				
					
					$gratuity = (!empty($salConfig->gratuity)) ? round((($basic_salary*$salConfig->gratuity)/100),0) : 0;
					
					$net_salary = $gross_sal - $gross_deduction +  $adv_bonus;
				}
				
				$totalPay = $postData['present']* $wages;
				$hra_amt = ($totalPay*40)/100;
				$basic_salary = $totalPay - $hra_amt;
				$gross_sal = $basic_salary + $hra_amt + $other_all+$food ;
				
				$gross_deduction = $emp_pf + $emp_esic + $pt + $tds + $salConfig->transport_charge + $postData['advance_salary'] + $postData['loan_emi'];			
				$net_salary = $gross_sal - $gross_deduction;
				$salData['id'] = "";
				$salData['ss_id'] = $salConfig->id;
				$salData['sal_month'] = $postData['sal_month'];
				$salData['emp_id'] = $postData['id'];
				$salData['emp_name'] = $postData['emp_name'];
				$salData['emp_code'] = $postData['emp_code'];
				$salData['total_hrs'] = $postData['total_hrs'];
				$salData['wh_hrs'] = $postData['wh_hrs'];
				$salData['ot_hrs'] = $postData['ot_hrs'];
				$salData['total_days'] = $postData['total_days'];
				$salData['present'] = $postData['present'];
				$salData['week_off'] = $postData['week_off'];
				$salData['rate_hour'] = $rate_hour;
				$salData['sal_format'] = $salConfig->sal_format;
				$salData['basic_salary'] = $basic_salary;
				$salData['hra_amt'] = $hra_amt;
				$salData['other_all'] = $other_all;
				$salData['gross_sal'] = $gross_sal;
				$salData['emp_pf'] = $emp_pf;
				$salData['cmp_pf'] = $cmp_pf;
				$salData['emp_esic'] = $emp_esic;
				$salData['cmp_esic'] = $cmp_esic;
				$salData['tds'] = $tds;
				$salData['pt'] = $pt;
				$salData['transport_charge'] = $salConfig->transport_charge;
				$salData['food'] = $food;
				$salData['advance_salary'] = $postData['advance_salary'];
				$salData['loan_emi'] = $postData['loan_emi'];
				$salData['bonus'] = $bonus;
				$salData['gratuity'] = $gratuity;
				$salData['gross_deduction'] = $gross_deduction;
				$salData['net_salary'] = $net_salary;
				$salData['wages'] = $wages;
				$salData['total_pay'] = $totalPay;
			}
		}
		return $salData;
    }
    
    public function reCalculatecSalOP(){
		$postData = $this->input->post(); 
		$hiddenInputs = '';$empTable ='';//print_r($postData);exit;
        $salData = Array();$html = '';$strData = '';//print_r($postData);exit;
        if(!empty($postData['id']))
		{
			$salConfig = new stdClass();$oldStructureId = 0;
			$actStr = $this->employee->getActiveSalaryStructure($postData['id']);
			
			if(!empty($actStr))
			{
				$salConfig = $actStr;
			
				$salAmount = (!empty($salConfig->sal_amount)) ? $salConfig->sal_amount : 0;
				$basic_per = (!empty($salConfig->basic_per)) ? $salConfig->basic_per : 0;
				$hra_per = (!empty($salConfig->hra_per)) ? $salConfig->hra_per : 0;
				$grossSal = (!empty($salConfig->gross_sal)) ? $salConfig->gross_sal : 0;
				$transport_charge = (!empty($actStr)) ? $salConfig->transport_charge : 0;
				$adv_bonus = (!empty($salConfig->adv_bonus)) ? $salConfig->adv_bonus : 0;
				$wh_day = (!empty($salConfig->wh_day)) ? $salConfig->wh_day : 0;
				$rate_hour = (!empty($salConfig->rate_hour)) ? $salConfig->rate_hour : 0;
				$food = (!empty($salConfig->food)) ? $salConfig->food : 0;
				$tds = (!empty($salConfig->tds)) ? $salConfig->tds : 0;			
				$salConfig->pt_limit = 12000; // IF GROSS SALARY > 12000 THEN 200 ELSE 0
				$ot_amt=0;$pl_used=0;$cl_used=0;
				$wages='';		
				if($salConfig->sal_format == 1)
				{
					$other_all = 0;$emp_pf = 0;$emp_esic = 0;$cmp_esic = 0;$pl_used=0;$cl_used=0;$bonus = 1800;
					// OT
					$ot_amt = round($rate_hour * $postData['ot_hrs']);
					
					$gross_sal += $ot_amt;
					
					// If PF LIMIT SET THEN CHECK
					$pfCalcOn = ($salConfig->pf_on == 1) ? ($basic_salary + $other_all) : $basic_salary;
					if(!empty($salConfig->pf_limit) AND $salConfig->pf_limit > 0)
					{
						$pfCalcOn = ($pfCalcOn < $salConfig->pf_limit) ? $pfCalcOn : $salConfig->pf_limit;
					}
					else
					{
						$pfCalcOn = $pfCalcOn;
					}
					if($salConfig->pf_status == 1){$emp_pf = round((($pfCalcOn * $salConfig->pf_per)/100),0);}
					$cmp_pf = $emp_pf;
					
					// Calc ESIC
					if(!empty($salConfig->esic_status)){$emp_esic = round((($gross_sal*$salConfig->emp_esic_per)/100),0);}
					if(!empty($salConfig->esic_status)){$cmp_esic = round((($gross_sal*$salConfig->cmp_esic_per)/100),0);}
					
					$pt = ($gross_sal > $salConfig->pt_limit)  ? 200 : 0;
					$gross_deduction = $emp_pf + $emp_esic + $pt + $tds + $salConfig->transport_charge + $postData['advance_salary'] + $postData['loan_emi'];				
					
					$gratuity = (!empty($salConfig->gratuity)) ? round((($basic_salary*$salConfig->gratuity)/100),0) : 0;
					
					$net_salary = $gross_sal - $gross_deduction +  $adv_bonus;
				}
				if(in_array($salConfig->sal_format,[2,3]))
				{
					$other_all = 0;$emp_pf = 0;$emp_esic = 0;$cmp_esic = 0;$bonus = 1800;$basic_salary = 0;$hra_amt = 0;
					$workingDays = $postData['total_days'] - $postData['week_off'];
					$wages = $postData['wages'];//$salConfig->rate_hour*$salConfig->wh_day;
					if($salConfig->sal_format == 3)
					{
					
					}
					else
					{
						$monthSal = round(($rate_hour * $postData['total_hrs']));
				
						$ot_amt = round($rate_hour * $postData['ot_hrs']);
					}
					
					
					$gross_sal = $basic_salary + $hra_amt + $other_all;
					
					// If PF LIMIT SET THEN CHECK
					$pfCalcOn = ($salConfig->pf_on == 1) ? ($basic_salary + $other_all) : $basic_salary;
					if(!empty($salConfig->pf_limit) AND $salConfig->pf_limit > 0)
					{
						$pfCalcOn = ($pfCalcOn < $salConfig->pf_limit) ? $pfCalcOn : $salConfig->pf_limit;
					}
					else
					{
						$pfCalcOn = $pfCalcOn;
					}
					if($salConfig->pf_status == 1){$emp_pf = round((($pfCalcOn * $salConfig->pf_per)/100),0);}
					$cmp_pf = $emp_pf;
					
					// Calc ESIC
					if(!empty($salConfig->esic_status)){$emp_esic = round((($gross_sal*$salConfig->emp_esic_per)/100),0);}
					if(!empty($salConfig->esic_status)){$cmp_esic = round((($gross_sal*$salConfig->cmp_esic_per)/100),0);}
					
					$pt = ($gross_sal > $salConfig->pt_limit)  ? 200 : 0;
					$gross_deduction = $emp_pf + $emp_esic + $pt + $tds + $salConfig->transport_charge + $postData['advance_salary'] + $postData['loan_emi'];				
					
					$gratuity = (!empty($salConfig->gratuity)) ? round((($basic_salary*$salConfig->gratuity)/100),0) : 0;
					
					$net_salary = $gross_sal - $gross_deduction +  $adv_bonus;
				}
				$totalPay = $postData['total_pay'];//$postData['present']* $wages;
				$hra_amt = round(($totalPay*40)/100);
				$basic_salary = $totalPay - $hra_amt;
				$gross_sal = $basic_salary + $hra_amt + $postData['other_all']+$postData['food'] ;
				
				$gross_deduction = $emp_pf + $emp_esic + $pt + $tds + $salConfig->transport_charge + $postData['advance_salary'] + $postData['loan_emi'];			
				$net_salary = $gross_sal - $gross_deduction;
				$salData['id'] = "";
				$salData['ss_id'] = $salConfig->id;
				$salData['sal_month'] = $postData['sal_month'];
				$salData['emp_id'] = $postData['id'];
				$salData['emp_name'] = $postData['emp_name'];
				$salData['emp_code'] = $postData['emp_code'];
				$salData['total_hrs'] = $postData['total_hrs'];
				$salData['wh_hrs'] = $postData['wh_hrs'];
				$salData['ot_hrs'] = $postData['ot_hrs'];
				$salData['total_days'] = $postData['total_days'];
				$salData['present'] = $postData['present'];
				$salData['week_off'] = $postData['week_off'];
				$salData['rate_hour'] = $rate_hour;
				$salData['sal_format'] = $salConfig->sal_format;
				$salData['basic_salary'] = $basic_salary;
				$salData['hra_amt'] = $hra_amt;
				$salData['other_all'] =$postData['other_all'];
				$salData['gross_sal'] = $gross_sal;
				$salData['emp_pf'] = $emp_pf;
				$salData['cmp_pf'] = $cmp_pf;
				$salData['emp_esic'] = $emp_esic;
				$salData['cmp_esic'] = $cmp_esic;
				$salData['tds'] = $tds;
				$salData['pt'] = $pt;
				$salData['transport_charge'] = $salConfig->transport_charge;
				$salData['food'] = $food;
				$salData['advance_salary'] = $postData['advance_salary'];
				$salData['loan_emi'] = $postData['loan_emi'];
				$salData['bonus'] = $bonus;
				$salData['gratuity'] = $gratuity;
				$salData['gross_deduction'] = $gross_deduction;
				$salData['net_salary'] = $net_salary;
				$salData['wages'] = $wages;
				$salData['total_pay'] = $postData['total_pay'];
			}
		}
	
		if(!empty($salData))
		{
			$gross_sal = $salData['basic_salary']+$salData['food']+$salData['hra_amt']+$salData['other_all'];
				$empTable .= '<td style="text-align:center;">'.$salData['emp_code'].'</td>';
                $empTable .= '<td >
                                <input type="text" name="salary_data['.$postData['id'].'][salary]" id="salary'.$postData['id'].'" value="'.$postData['salary'].'" class="form-control " data-id="'.$postData['id'].'" readonly>
                            </td>';
				$empTable .= '<td>'.$salData['emp_name'].' 
                    <input type="hidden" name="salary_data['.$postData['id'].'][emp_id]" id="id'.$postData['id'].'" data-id="'.$postData['id'].'" class="floatOnly salData'.$postData['id'].'" alt="emp_id" value="'.$postData['id'].'" style="width:100px;">
                </td>';
				$empTable .= '<td>
                                <input type="text" name="salary_data['.$postData['id'].'][present]" id="present'.$postData['id'].'" data-id="'.$postData['id'].'" class="floatOnly  form-control" alt="present" value="'.$postData['present'].'" style="width:100px;">
                                <input type="hidden" name="salary_data['.$postData['id'].'][week_off]" id="week_off'.$postData['id'].'" data-id="'.$postData['id'].'" class="floatOnly salData'.$postData['id'].'" alt="week_off" value="'.$salData['week_off'].'" style="width:100px;">
                            </td>';
				$empTable .= '<td>
				                <input type="text" name="salary_data['.$postData['id'].'][wages]" id="wages'.$postData['id'].'" data-id="'.$postData['id'].'" class="form-control calTotalPay floatOnly salData'.$postData['id'].'" alt="week_off" value="'.$salData['wages'].'" style="width:100px;">
			               </td>';
				$empTable .= '<td>
				                <input type="text"  name="salary_data['.$postData['id'].'][total_pay]" id="total_pay'.$postData['id'].'" value="'.$postData['total_pay'].'" class="form-control calWages " data-id="'.$postData['id'].'">
				              </td>';
				$empTable .= '<td>
                                <input type="text" name="salary_data['.$postData['id'].'][basic_salary]" id="basic_salary'.$postData['id'].'" value="'.$salData['basic_salary'].'" class="form-control floatOnly   salData'.$postData['id'].'" readonly>
                              </td>';
				$empTable .= '<td>'.$salData['food'].'
                                <input type="hidden"  name="salary_data['.$postData['id'].'][food]" id="food'.$postData['id'].'" value="'.$salData['food'].'">
                            </td>';
				$empTable .= '<td>
                                <input type="text" name="salary_data['.$postData['id'].'][hra]"  id="hra'.$postData['id'].'" value="'.$salData['hra_amt'].'" class=" form-control floatOnly" data-id="'.$postData['id'].'">
                            </td>';
				$empTable .= '<td>
                                <input type="text" name="salary_data['.$postData['id'].'][other_all]"  id="other_all'.$postData['id'].'" value="'.$salData['other_all'].'" class=" form-control floatOnly" data-id="'.$postData['id'].'">
                            </td>';
				$empTable .= '<td >
                                <input type="text" readonly name="salary_data['.$postData['id'].'][gross_sal]" id="gross_sal'.$postData['id'].'" value="'.$salData['gross_sal'].'" class="form-control " data-id="'.$postData['id'].'">
                            </td>';
				$empTable .= '<td>'.$salData['tds'].'</td>';
				$empTable .= '<td></td>';
				$empTable .= '<td>
                                <input type="text" name="salary_data['.$postData['id'].'][advance_salary]" id="advance_salary'.$postData['id'].'" value="'.$salData['advance_salary'].'" class="form-control floatOnly " data-id="'.$postData['id'].'">
                            </td>';
				$empTable .= '<td>'.$salData['emp_pf'].'
                                <input type="hidden" id="emp_pf'.$postData['id'].'" value="'.$salData['emp_pf'].'">
                                </td>';
				$empTable .= '<td>'.$salData['pt'].'
                                <input type="hidden" id="pt'.$postData['id'].'" value="'.$salData['pt'].'">
                                </td>';
                $empTable .= '<td ><input type="text" readonly name="salary_data['.$postData['id'].'][gross_deduction]"  id="gross_deduction'.$postData['id'].'" value="'.$salData['gross_deduction'].'" class=" form-control" data-id="'.$postData['id'].'"></td>';
				$empTable .= '<td " class="bg-warning"><input type="text" readonly name="salary_data['.$postData['id'].'][net_salary]"  id="net_salary'.$postData['id'].'" value="'.$salData['net_salary'].'" class=" form-control" data-id="'.$postData['id'].'"></td>';
			    
			    $empTable .= '<td>
            				        <button type="button" class="btn btn-primary float-right reCalculatecSal" data-id="'.$postData['id'].'" >Save</button>
            					    <button type="button" class="btn btn-outline-danger float-right" data-id="'.$postData['id'].'" onclick="removeEmployee(this)"><i class="fas fa-trash-alt"></i></button>
            				    </td>';

			unset($salData['present'],$salData['week_off'],$salData['pl'],$salData['cl'],$salData['advance_salary'],$salData['food'],$salData['other_all'],$salData['gross_deduction'],$salData['net_salary'],$salData['hra_amt'],$salData['gross_sal'],$salData['wages'],$salData['basic_salary']);
			foreach($salData as $key=>$val)
			{
				$name = 'salary_data['.$postData['id'].']['.$key.']';
				$id = $key.$postData['id'];
				$hiddenInputs.="<input type='hidden' name='".$name."' id='".$id."' class='salData".$postData['id']."' alt='".$key."' value='".$val."'>";
			}
		}
		$this->printJson(['status'=>1,'empLine'=>$empTable,'hidden_inputs'=>$hiddenInputs]);
    }
    
      // Created By Meghavi @20/12/2023   
    public function viewPayrollData_old($id){ 

        $payrollData = $this->payroll->getPayrollData($id);
        $i=1; $this->data['payrollData'] = ""; 
        foreach($payrollData as $row):
            
            $this->data['payrollData'] .= '<tr>
               
                <td>'.$row->emp_code.'</td>
                <td>'.$row->emp_name.'</td>
                <td>'.$row->present.'</td>
                <td>'.$row->week_off.'</td>
                <td>'.$row->total_days.'</td>
                <td>'.$row->wh_hrs.'</td>
                <td>'.$row->basic_salary.'</td>
                <td>'.$row->hra.'</td>
                <td>'.$row->gross_sal.'</td>
                <td>'.$row->emp_pf.'</td>
                <td>'.$row->emp_esic.'</td>
                <td>'.$row->pt.'</td>
                <td>'.$row->tds.'</td>
                <td>'.$row->advance_salary.'</td>
                <td>'.$row->loan_emi.'</td>
                <td>'.$row->transport_charge.'</td>
                <td>'.$row->food.'</td>
                <td>'.$row->gross_deduction.'</td>
                <td>'.$row->net_salary.'</td>
            </tr>';
        endforeach;
        $this->load->view($this->view_payroall,$this->data);
    }
    
    public function viewPayrollData($id){ 
        $payrollData = $this->payroll->getPayrollData($id);
        $i=1; $this->data['payrollData'] = ""; 
        foreach($payrollData as $row):
            
            $this->data['payrollData'] .= '<tr>        
                <td>'.$row->emp_code.'</td>
                <td>'.$row->emp_name.'</td>
                <td>'.$row->present.'</td>
                <td>'.$row->wages.'</td>
                <td>'.$row->total_pay.'</td>
                <td>'.$row->basic_salary.'</td>
                <td>'.$row->food.'</td>
                <td>'.$row->hra.'</td>
                <td>'.$row->other_all.'</td>
                <td>'.$row->gross_sal.'</td>
                <td>'.$row->tds.'</td>
                <td>'.$row->other.'</td>
                <td>'.$row->advance_salary.'</td>
                <td>'.$row->emp_pf.'</td>
                <td>'.$row->pt.'</td>
                <td>'.$row->gross_deduction.'</td>
                <td>'.$row->net_salary.'</td>
            </tr>';
        endforeach;
        $this->load->view($this->view_payroall,$this->data);
    }

    public function getEmpDetails(){
        $data = $this->input->post();
        $data['emp_code'] = trim($data['emp_code']);
        $empDetail = $this->payroll->getEmpData($data['emp_code']);
        $this->printJson(['status'=>1,'data'=>['empDetail'=>$empDetail]]);
    }
    
    public function savePayRoll(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['month']))
            $errorMessage['month'] = "Month is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->payroll->savePayRoll($data));
        endif;
    }
}
?>