<?php
class LineInspection extends MY_Controller{
    private $indexPage = "line_inspection/index";
    private $lineInsForm = "line_inspection/form";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Line Inspection";
		$this->data['headData']->controller = "lineInspection";
	}

	public function index(){
		$this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
		$this->data['jobNoList'] = $this->lineInspection->getJobList();
		$this->data['processList'] = $this->process->getProcessList();
        $this->load->view($this->indexPage,$this->data);
	}

	public function getDTRows(){
		$result = $this->lineInspection->getDTRows($this->input->post());
        
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->pending_qty = $row->rejection_qty + $row->rework_qty;           	
			$row->minDate = (!empty($row->entry_date)) ? $row->entry_date : "";			
            $row->controller = $this->data['headData']->controller;			
            $sendData[] = getLineInspectionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
	}

	public function lineInspection(){
		$data = $this->input->post();		
		$inwardData = $this->lineInspection->getProductionTransRow($data['ref_id']);
		$result['transData'] = $this->lineInspection->getInspectionData($data);

		$jobCardData = $this->jobcard->getJobcard($data['job_card_id']);
		$jobProcess = explode(",",$jobCardData->process);
        $in_process_key = array_keys($jobProcess,$data['process_id'])[0];

		$processOptions = '<option value="">Select Stage</option>';		
		foreach($jobProcess as $key=>$value):
            if($key <= $in_process_key):
				$selected = ($inwardData->rejection_stage == $value)?"selected":"";
				$processData = $this->process->getProcess($value);
				$processOptions .= '<option value="'. $processData->id.'" '.$selected.'>'.$processData->process_name.'</option>';
			endif;
		endforeach;
        $result['processOptions'] = $processOptions;
		
		$rejectionReason = $this->comment->getCommentsOnRejectionStage($inwardData->rejection_stage);
		$rrOptions = '<option value="">Select Reason</option>';		
		foreach($rejectionReason as $row):
			$selected = ($inwardData->rejection_reason == $row->id)?"selected":"";
			$rrOptions .= '<option value="'.$row->id.'" '.$selected.'>'.$row->remark.'</option>';
		endforeach;
        $result['rrOptions'] = $rrOptions;

        $process_html = '';	
		$reworkProcess = (!empty($inwardData->rework_process_id))?explode(",",$inwardData->rework_process_id):array();	
		$selectedRwProcess = array();
		foreach($jobProcess as $key=>$value):
            if($key <= $in_process_key):
				$selected = (!empty($reworkProcess) && in_array($value,$reworkProcess))?"selected":"";
				$processData = $this->process->getProcess($value);
				$process_html .= '<option value="'. $processData->id.'" '.$selected.'>'.$processData->process_name.'</option>';
				if(!empty($selected)):
					$selectedRwProcess[] = $processData->id;
				endif;
			endif;
		endforeach;

		$data['rework_qty'] = $inwardData->rework_qty;
		$data['rejection_qty'] = $inwardData->rejection_qty;
        $result['rewProcess'] = $process_html;
		$result['selectedRewProcess'] = (!empty($selectedRwProcess))?implode(",",$selectedRwProcess):"";
		$result['postData'] = $data;
		$this->data['dataRow'] = $result;
		$this->load->view($this->lineInsForm,$this->data);
	}

	public function rejectionReason(){
        $stageId = $this->input->post('stage_id');
        $rejectionReason = $this->comment->getCommentsOnRejectionStage($stageId);
		$rrOptions = '<option value="">Select Reason</option>';		
		foreach($rejectionReason as $row):
			$rrOptions .= '<option value="'.$row->id.'">'.$row->remark.'</option>';
		endforeach;
        $result['status'] = 1;
        $result['rrOptions'] = $rrOptions;
        $this->printJson($result);
    }

	public function save(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['out_qty']) && empty($data['rejection_qty']) && empty($data['rework_qty']) || $data['out_qty'] == "0.000" && $data['rejection_qty'] == "0.000" && $data['rework_qty'] == "0.000")
			$errorMessage['out_form_error'] = "OK or Rejection or Rework Qty is required.";

		if(!empty($data['rejection_qty']) && $data['rejection_qty'] != "0.000"):
			if(empty($data['rejection_stage']))
				$errorMessage['rejection_stage'] = "Rejection stage is required.";

			if(empty($data['rejection_reason']))
				$errorMessage['rejection_reason'] = "Rejection reason is required.";
		endif;

		if(!empty($data['rework_qty']) && $data['rework_qty'] != "0.000"):
			if(empty($data['rework_process_id']))
				$errorMessage['rework_process_id'] = "Rework Process is required.";
		endif;

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$data['created_by'] = $this->loginId;
			$this->printJson($this->lineInspection->save($data));
		endif;
	}

	public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->lineInspection->delete($id));
        endif;
	}
}
?>