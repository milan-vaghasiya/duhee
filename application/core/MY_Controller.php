<?php 
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class MY_Controller extends CI_Controller{
	
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData'] = new StdClass;
		$this->load->library('form_validation');
		
		$this->load->model('masterModel');
		$this->load->model('ProcessModel','process');
		$this->load->model('MachineModel','machine');
		$this->load->model('TermsModel','terms');
		$this->load->model('MasterOptionsModel', 'masterOption');
		$this->load->model('StoreModel','store');
		$this->load->model('PartyModel','party');
		$this->load->model('ItemModel','item');
		$this->load->model('ItemCategoryModel','itemCategory');
		$this->load->model('FamilyGroupModel','familyGroup');
		$this->load->model('PurchaseRequestModel','purchaseRequest');
		$this->load->model('PurchaseEnquiryModel','purchaseEnquiry');
		$this->load->model('PurchaseOrderModel','purchaseOrder');
		$this->load->model('PurchaseOrderScheduleModel','purchaseOrderSchedule');
		$this->load->model('GrnModel','grnModel');
		$this->load->model('GirModel','girModel');
		$this->load->model('PurchaseInvoiceModel','purchaseInvoice');
		$this->load->model('RejectionCommentModel','comment');
		//$this->load->model('JobcardModel','jobcard');
		//$this->load->model('JobMaterialDispatchModel','jobMaterial');
		//$this->load->model('ProcessApprovalModel','processApprove');
		$this->load->model('JobWorkModel','jobWork');
		//$this->load->model('ProductionModel','production');
		$this->load->model('InstrumentModel','instrument');
		$this->load->model('InChallanModel','inChallan');
		$this->load->model('OutChallanModel','outChallan');
		$this->load->model('PriceAmendmentModel','priceAmendment');
		
		/** Production Model */
		$this->load->model('production/JobcardModel','jobcard');
		$this->load->model('production/ProcessMovementModel','processMovement');
		$this->load->model('production/OutsourceModel','outsource');
		$this->load->model('JobMaterialDispatchModel','jobMaterial');
		$this->load->model('production/PrimaryCFTModel','primaryCFT');
		$this->load->model('production/FinalCFTModel','finalCFT');
		$this->load->model('production/ProductSetupModel','productSetup');
		$this->load->model('production/PirModel','pir');
		$this->load->model('FirModel','fir');
		$this->load->model('PdiModel','pdi');

		$this->load->model('SalesEnquiryModel','salesEnquiry');
		$this->load->model('SalesOrderModel','salesOrder');
		$this->load->model('ProductInspectionModel','productInspection');
		$this->load->model('DeliveryChallanModel','challan');
		$this->load->model('SalesInvoiceModel','salesInvoice');
		$this->load->model('LeadModel','leads');
		$this->load->model('SalesQuotationModel','salesQuotation');
		$this->load->model('ReportModel','reportModel');
		$this->load->model('ProductReporModel','productReporModel');
		$this->load->model('TransactionMainModel','transModel');
		$this->load->model('ProformaInvoiceModel','proformaInv');

		$this->load->model('MaterialRequestModel','jobMaretialRequest'); 
		$this->load->model('JobWorkOrderModel','jobWorkOrder');
		$this->load->model('StockVerificationModel', 'stockVerify');
		$this->load->model('ProductionOperationModel', 'operation');
		$this->load->model('MachineTicketModel', 'ticketModel');
		$this->load->model('ShiftModel', 'shiftModel');
		$this->load->model('MachineActivitiesModel', 'activities');
		$this->load->model('PackingModel', 'packings');
		$this->load->model('PreDispatchInspectModel', 'preDispatch');
		$this->load->model('TransportModel','transport');
		$this->load->model('BankingModel','banking');

		/***  Report Model ***/
		$this->load->model('report/ProductionReportModel','productionReports');
		$this->load->model('report/QualityReportModel','qualityReports');
		$this->load->model('report/StoreReportModel', 'storeReportModel');
		$this->load->model('report/SalesReportModel', 'salesReportModel');
		$this->load->model('report/PurchaseReportModel', 'purchaseReport');
		
		/*** HR Model ***/
		$this->load->model('hr/DepartmentModel','department');
		$this->load->model('hr/DesignationModel','designation');
		$this->load->model('hr/EmployeeModel','employee');
		$this->load->model('hr/AttendanceModel','attendance');
		$this->load->model('hr/LeaveModel','leave');
		$this->load->model('hr/LeaveSettingModel','leaveSetting');
		$this->load->model('hr/LeaveApproveModel','leaveApprove');
		$this->load->model('hr/PayrollModel','payroll');
		$this->load->model('CategoryModel', 'category');
		$this->load->model('HolidaysModel', 'holiday');
		$this->load->model('AttendancePolicyModel', 'policy');
		$this->load->model('hr/ManualAttendanceModel','manualAttendance');
		$this->load->model('hr/ExtraHoursModel','extraHours');
		$this->load->model('hr/AdvanceSalaryModel','advanceSalary');
		$this->load->model('hr/SalaryStructureModel', 'salaryStructure');
		$this->load->model('hr/EmpLoanModel','empLoan');
		$this->load->model('PermissionModel','permission');
		$this->load->model('hr/SkillMasterModel', 'skillMaster');
		$this->load->model('hr/EmployeeFacilityModel', 'employeefacility');
		$this->load->model('hr/LeaveAuthorityModel', 'leaveAuthorityModel');
		$this->load->model('hr/GatePassModel', 'gatePass');

		$this->load->model('LineInspectionModel','lineInspection');
		$this->load->model('AssignInspectorModel','assignInspector');
		$this->load->model('ProcessSetupModel','processSetup');
		$this->load->model('SetupInspectionModel','setupInspection');
		$this->load->model('hr/BiometricModel','biometric');
		$this->load->model('IssueRequisitionModel','issueRequisition');
		$this->load->model('PlanningTypesModel','planningTypes');
		$this->load->model('PurchaseIndentModel','purchaseIndent');
		$this->load->model('MasterDetailModel','masterDetail');
		$this->load->model('HsnMasterModel','hsnModel');
		$this->load->model('InspectionModel','inspection');
		$this->load->model('JobWorkInvoiceModel','jobWorkInvoice');
		$this->load->model('JobWorkScrapInvoiceModel','jobworkScrapInvoice');
		$this->load->model('MachineTypeModel','machineType');
		$this->load->model('MaterialGradeModel','materialGrade');
		$this->load->model('PreventiveMaintenanceModel','prevMaintenance');
		$this->load->model('VehicleTypeModel','vehicleType');
		$this->load->model('MeasurementTechniqueModel','measurementTechnique');


		/*** Account Model ***/
		$this->load->model('LedgerModel','ledger');
		$this->load->model('GroupModel','group');
		$this->load->model('ExpenseMasterModel','expenseMaster');
		$this->load->model('TaxMasterModel','taxMaster');
		/* $this->load->model('DebitNoteModel','debitNote');
		$this->load->model('CreditNoteModel','creditNote');
		$this->load->model('JournalEntryModel','journalEntry');
		$this->load->model('GstExpenseModel','gstExpense');
		$this->load->model('PaymentVoucherModel','paymentVoucher'); */

		$this->load->model('EwayBillModel','ewayBill');
		$this->load->model('GateEntryModel','gateEntry');
		$this->load->model('GateInwardModel','gateInward');
		$this->load->model('GateReceiptModel','gateReceipt');
		$this->load->model('GateReceiptOtherModel','gateReceiptOther');
		$this->load->model('ControlPlanModel','controlPlan');
		$this->load->model('ReactionPlanModel','reactionPlan');
		$this->load->model('ControlMethodModel','controlMethod');
		$this->load->model('RegrindingReasonModel','regrindingReason');
		$this->load->model('StockTransactionModel','stockTransac');
		$this->load->model('RqcModel','rqc');
		$this->load->model('MqsParamModel','mqs');
		$this->load->model('MaterialQcModel','materialQc');
		$this->load->model('ResponsibilityModel', 'responsibility');
		$this->load->model('RtsQuestionModel', 'rtsQuestion');
		$this->load->model('DispatchRequestModel','dispatchRequest');
		$this->load->model('FeasibilityReasonModel','feasibilityReason');
		
	    $this->load->model('QCIndentModel', 'qcIndent'); 
		$this->load->model('QCPurchaseModel', 'qcPurchase');
		$this->load->model('QcPRModel', 'qcPRModel');
		$this->load->model('QcInstrumentModel','qcInstrument');
		$this->load->model('QcChallanModel','qcChallan');
		$this->load->model('CommonFgModel','commonFg');
		$this->load->model('HeatTreatmentModel','heatTreatment');
		$this->load->model('FurnaceModel','furnaceModel');
		$this->load->model('ExternalHeatTreatmentModel','externalHeatTreatment');
		$this->load->model('SarModel','sar');
		
		$this->data['currentFormDate'] = $this->session->userdata("currentFormDate");
		$this->financialYearList = $this->getFinancialYearList($this->session->userdata('issueDate'));	

        $this->symbolArray = $this->data['symbolArray'] = [''=>'', 'operation'=>'Operation', 'oper_insp'=>'Oper. & Insp.', 'inspection'=>'Inspection', 'storage'=>'Storage', 'delay'=>'Delay', 'decision'=>'Decision', 'transport'=>'Transport', 'connector'=>'Connector'];	
        $this->classArray = $this->data['classArray'] = [''=>'', 'critical'=>'Critical Characteristic', 'major'=>'Major', 'minor'=>'Minor','pc'=>'process critical characteristics'];

		$this->setSessionVariables('process,machine,store,party,item,itemCategory,familyGroup,purchaseEnquiry,purchaseOrder,purchaseInvoice,comment,jobcard,jobMaterial,jobWork,processMovement,salesEnquiry,salesOrder,productInspection,challan,salesInvoice,leads,reportModel,department,employee,attendance,leave,leaveSetting,leaveApprove,payroll,jobMaretialRequest,grnModel,purchaseRequest,jobWorkOrder,fir,transModel,masterOption,productionReports,qualityReports,inChallan,outChallan,stockVerify,operation,ticketModel,shiftModel,proformaInv,storeReportModel,salesReportModel,activities,purchaseReport,permission,packings,preDispatch,category,holiday,policy,designation,manualAttendance,lineInspection,assignInspector,processSetup,setupInspection,biometric,extraHours,advanceSalary,salaryStructure,purchaseOrderSchedule,issueRequisition,planningTypes,purchaseIndent,masterDetail,hsnModel,inspection,jobWorkInvoice,ledger,group,expenseMaster,taxMaster,ewayBill,transport,banking,jobworkScrapInvoice,machineType,materialGrade,prevMaintenance,vehicleType,gateEntry,gateInward,gateReceipt,gateReceiptOther,measurementTechnique,skillMaster,outsource,controlPlan,reactionPlan,primaryCFT,finalCFT,controlMethod,productSetup,pir,pdi,regrindingReason,stockTransac,rqc,employeefacility,mqs,materialQc,responsibility,rtsQuestion,empLoan,dispatchRequest,feasibilityReason,qcIndent,qcPurchase,qcPRModel,qcInstrument,qcChallan,commonFg,heatTreatment,furnaceModel,gatePass,externalHeatTreatment,sar');
	}

	public function setSessionVariables($modelNames)
	{
		$this->data['dates'] = explode(' AND ',$this->session->userdata('financialYear'));
        $this->shortYear = $this->data['shortYear'] = date('y',strtotime($this->data['dates'][0])).'-'.date('y',strtotime($this->data['dates'][1]));
		$this->startYearDate =$this->data['startYearDate'] = date('Y-m-d',strtotime($this->data['dates'][0]));
		$this->endYearDate = $this->data['endYearDate'] = date('Y-m-d',strtotime($this->data['dates'][1]));
		$this->data['start_year'] = date('Y',strtotime($this->data['dates'][0]));
		$this->data['end_year'] = date('Y',strtotime($this->data['dates'][1]));
		$this->loginId = $this->session->userdata('loginId');
		$this->userRole = $this->session->userdata('role');
		$this->processAuth = $this->session->userdata('processAuth');
		
		$this->RTD_STORE = $this->session->userdata('RTD_STORE');
		$this->SCRAP_STORE = $this->session->userdata('SCRAP_STORE');
		$this->GIR_STORE = $this->session->userdata('GIR_STORE');
		$this->RM_ALLOT_STORE = $this->session->userdata('RM_ALLOT_STORE');
		$this->INSP_STORE = $this->session->userdata('INSP_STORE');
		$this->REGRIND_STORE = $this->session->userdata('REGRIND_STORE');
		$this->JOBW_STORE = $this->session->userdata('JOBW_STORE');
		$this->LOGIN_STORE = $this->session->userdata('LOGIN_STORE');
		$this->ALLOT_RM_STORE = $this->session->userdata('ALLOT_RM_STORE');
		$this->RCV_RM_STORE = $this->session->userdata('RCV_RM_STORE');
		$this->PROD_STORE = $this->session->userdata('PROD_STORE');
		$this->SEM_FG_STORE = $this->session->userdata('SEM_FG_STORE');
		$this->FI_STORE = $this->session->userdata('FI_STORE');
		$this->PDI_STORE = $this->session->userdata('PDI_STORE');
		$this->PACK_STORE = $this->session->userdata('PACK_STORE');
		$this->PACK_MTR_STORE = $this->session->userdata('PACK_MTR_STORE');
		$this->PRODUCTION_STORE = $this->session->userdata('PRODUCTION_STORE');
		$this->MISPLACED_STORE = $this->session->userdata('MISPLACED_STORE');
		$this->SUPLY_REJ_STORE = $this->session->userdata('SUPLY_REJ_STORE');
		$this->HEAT_TREAT_STORE = $this->session->userdata('HEAT_TREAT_STORE');
		
		// Process Type
		$this->FIR_PROCESS = $this->session->userdata('FIR_PROCESS');
		$this->PDI_PROCESS = $this->session->userdata('PDI_PROCESS');
		$this->DISP_PROCESS = $this->session->userdata('DISP_PROCESS');
		$this->PACK_PROCESS = $this->session->userdata('PACK_PROCESS');
		
		// Control Plan
		$this->CONTROL_PLAN = $this->session->userdata('CONTROL_PLAN');

		if($this->endYearDate <= date("Y-m-d")){$this->data['maxDate'] = $this->endYearDate;}else{$this->data['maxDate'] = date('Y-m-d');}
		$models = explode(',',$modelNames);
		foreach($models as $modelName):
			$modelName = trim($modelName);
			$this->{$modelName}->dates = $this->data['dates'];
			$this->{$modelName}->loginID = $this->session->userdata('loginId');
			$this->{$modelName}->userName = $this->session->userdata('user_name');
			$this->{$modelName}->userRole = $this->session->userdata('role');
			$this->{$modelName}->userRoleName = $this->session->userdata('roleName');
			$this->{$modelName}->processAuth = $this->session->userdata('processAuth');
			
			$this->{$modelName}->shortYear = date('y',strtotime($this->data['dates'][0])).'-'.date('y',strtotime($this->data['dates'][1]));
			$this->{$modelName}->startYear = date('Y',strtotime($this->data['dates'][0]));
			$this->{$modelName}->endYear = date('Y',strtotime($this->data['dates'][1]));
			$this->{$modelName}->startYearDate = date('Y-m-d',strtotime($this->data['dates'][0]));
			$this->{$modelName}->endYearDate = date('Y-m-d',strtotime($this->data['dates'][1]));
			$this->{$modelName}->RTD_STORE = $this->session->userdata('RTD_STORE');
			$this->{$modelName}->SCRAP_STORE = $this->session->userdata('SCRAP_STORE');
			$this->{$modelName}->GIR_STORE = $this->session->userdata('GIR_STORE');
			$this->{$modelName}->RM_ALLOT_STORE = $this->session->userdata('RM_ALLOT_STORE');
			$this->{$modelName}->INSP_STORE = $this->session->userdata('INSP_STORE');
			$this->{$modelName}->REGRIND_STORE = $this->session->userdata('REGRIND_STORE');
			$this->{$modelName}->JOBW_STORE = $this->session->userdata('JOBW_STORE');
			$this->{$modelName}->LOGIN_STORE = $this->session->userdata('LOGIN_STORE');
			$this->{$modelName}->ALLOT_RM_STORE = $this->session->userdata('ALLOT_RM_STORE');
			$this->{$modelName}->RCV_RM_STORE = $this->session->userdata('RCV_RM_STORE');
			$this->{$modelName}->PROD_STORE = $this->session->userdata('PROD_STORE');
			$this->{$modelName}->SEM_FG_STORE = $this->session->userdata('SEM_FG_STORE');
			$this->{$modelName}->FI_STORE = $this->session->userdata('FI_STORE');
			$this->{$modelName}->PDI_STORE = $this->session->userdata('PDI_STORE');
			$this->{$modelName}->PACK_STORE = $this->session->userdata('PACK_STORE');
			$this->{$modelName}->PACK_MTR_STORE = $this->session->userdata('PACK_MTR_STORE');
			$this->{$modelName}->PRODUCTION_STORE = $this->session->userdata('PRODUCTION_STORE');
			$this->{$modelName}->MISPLACED_STORE = $this->session->userdata('MISPLACED_STORE');
			$this->{$modelName}->SUPLY_REJ_STORE = $this->session->userdata('SUPLY_REJ_STORE');
			$this->{$modelName}->HEAT_TREAT_STORE = $this->session->userdata('HEAT_TREAT_STORE');
			
			// Process Type
			$this->{$modelName}->FIR_PROCESS = $this->session->userdata('FIR_PROCESS');
			$this->{$modelName}->PDI_PROCESS = $this->session->userdata('PDI_PROCESS');
			$this->{$modelName}->DISP_PROCESS = $this->session->userdata('DISP_PROCESS');
			$this->{$modelName}->PACK_PROCESS = $this->session->userdata('PACK_PROCESS');
			
			$this->{$modelName}->CONTROL_PLAN = $this->session->userdata('CONTROL_PLAN');
		endforeach;
		return true;
	}
	
	public function getFinancialYearList($issueDate){
		$startYear  = ((int)date("m",strtotime($issueDate)) >= 4) ? date("Y",strtotime($issueDate)) : (int)date("Y",strtotime($issueDate)) - 1;
		$endYear  = ((int)date("m") >= 4) ? date("Y") + 1 : (int)date("Y");
		
		$startDate = new DateTime($startYear."-04-01");
		$endDate = new DateTime($endYear."-03-31");
		$interval = new DateInterval('P1Y');
		$daterange = new DatePeriod($startDate, $interval ,$endDate);
		$fyList = array();$val="";$label="";
		foreach($daterange as $dates)
		{
			$start_date = date("Y-m-d H:i:s",strtotime("01-04-".$dates->format("Y")." 00:00:00"));
			$end_date = date("Y-m-d H:i:s",strtotime("31-03-".((int)$dates->format("Y") + 1)." 23:59:59"));
			
			$val = $start_date." AND ".$end_date;
			$label = 'Year '.date("Y",strtotime($start_date)).'-'.date("Y",strtotime($end_date));
			$fyList[] = ["label" => $label, "val" => $val];
		}
		return $fyList;
	}
	
	public function isLoggedin(){
		if(!$this->session->userdata("LoginOk")):
			//redirect( base_url() );
			echo '<script>window.location.href="'.base_url().'";</script>';
		endif;
		return true;
	}
	
	public function printJson($data){
		print json_encode($data);exit;
	}

	public function printDecimal($val){
		return number_format($val,0,'','');
	}
	
	public function checkGrants($url){
		$empPer = $this->session->userdata('emp_permission');
		if(!array_key_exists($url,$empPer)):
			redirect(base_url('error_403'));
		endif;
		return true;
	}
}
?>