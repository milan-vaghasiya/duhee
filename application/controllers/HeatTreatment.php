<?php
class HeatTreatment extends MY_Controller{
    private $indexPage = "heat_treatment/index";
    private $formPage = "heat_treatment/lot_form";
    private $ms_output = "heat_treatment/ms_output";
    private $spArray = ["1"=>'0.80% C',"2"=>"0.50% C","3"=>"Carbides","4"=>"Decarb"];
    
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "SQF Heat Treatment";
		$this->data['headData']->controller = "heatTreatment";
	}
	
	public function index(){
        $this->data['headData']->pageUrl = "heatTreatment";
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->heatTreatment->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getHeatTreatmentData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addLot(){
        $this->data['furnaceList'] = $this->furnaceModel->getFurnaceList();
        $this->data['itemList'] = $this->heatTreatment->getItemList();
        $this->data['nextTransNo'] = $this->heatTreatment->getNextTransNo(['month'=>date('m'),'year'=>date('Y')]);
        $this->load->view($this->formPage,$this->data);
    }

    public function getBatchNo(){
        $data = $this->input->post();
        $stockData = $this->store->getItemStockBatchWise(['item_id'=>$data['item_id'],'location_id'=>$this->HEAT_TREAT_STORE->id,'stock_required'=>1]);
        $options='<option value="">Select Batch No</option>';
        if(!empty($stockData)){
            foreach($stockData as $row){
                $jobData = $this->jobcard->getJobcardOnJobNo($row->batch_no);
                
                $wo_no = (!empty($jobData->wo_no) ? $jobData->wo_no : $row->batch_no);
                
                $options.='<option value="'.$row->batch_no.'" data-stock_qty="'.$row->qty.'">'.$wo_no.' [Qty :'.floatval($row->qty).']</option>';
            }
        }
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function saveLot(){
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['furnace_id'])){ $errorMessage['furnace_id'] = "Furnace required."; }
        if (empty($data['trans_date'])){ $errorMessage['trans_date'] = "Date is required."; }
            
        if (!isset($data['item_id'])) {
            $errorMessage['general_error'] = "Add Item ";
        } else {
            $i=1;
            foreach($data['item_id'] as $key=>$item_id){
                if(empty($item_id)){
                    $errorMessage['item_id'.$i] = "Item is required";
                }
                if(empty($data['batch_no'][$key])){
                    $errorMessage['batch_no'.$i] = "Batch No. is required";
                }
                if(empty($data['qty'][$key])){
                    $errorMessage['qty'.$i] = "Qty is required ";
                }else{
                    $stockData = $this->store->getItemStockBatchWise(['item_id'=>$item_id,'location_id'=>$this->HEAT_TREAT_STORE->id,'batch_no'=>$data['batch_no'][$key],'single_row'=>1]); 
                    
                    $heatQty = (!empty($stockData->qty))?$stockData->qty:0;
                    
                    if(!empty($data['trans_id'][$key])){
                        $heatTransData = $this->heatTreatment->getHeatTreatData(['id'=>$data['trans_id'][$key], 'single_row' => 1]);
					    $heatQty = (!empty($heatTransData->qty)? ($heatTransData->qty + $stockData->qty) : 0);
                    }
					
                    if($data['qty'][$key] > $heatQty){
                        $errorMessage['qty'.$i] = "Qty is invalid.".$data['qty'][$key]." > ".$heatQty;
                    }
                }
                $i++;
            }
        }
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $this->printJson($this->heatTreatment->save($data));

        endif;
    }

    public function edit($id){
        $this->data['furnaceList'] = $this->furnaceModel->getFurnaceList();
        $this->data['itemList'] = $this->heatTreatment->getItemList();
        $this->data['dataRow'] = $this->heatTreatment->getHeatTreatMasterData(['id'=>$id]);
        $transData = $this->heatTreatment->getHeatTransData(['trans_main_id'=>$id]);
        $transArray = array();
        foreach($transData as $row){
            $batchData = $this->store->getItemStockBatchWise(['item_id'=>$row->item_id,'location_id'=>$this->HEAT_TREAT_STORE->id]);
            $row->batchList = json_encode($batchData);
            $transArray[]=$row;
        }
        $this->data['transData'] = $transArray;
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
		else:
			$this->printJson($this->heatTreatment->delete($id));
		endif;
	}

    public function completeFurnace(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
		else:
			$this->printJson($this->heatTreatment->completeFurnace($id));
		endif;
	}
	
    public function addMSResultOutput($id){
        $this->data['dataRow']= $dataRow = $this->heatTreatment->getMSOutPutData(['ht_id'=>$id]); 
        $this->data['spArray'] = $this->spArray;
        $this->data['htData'] = $this->heatTreatment->getMSHeatTreatData(['id'=>$id]);
        $this->load->view($this->ms_output, $this->data);
    }

    public function saveMsOutput(){
        $data = $this->input->post();
        $errorMessage = Array();
        if(empty($data['inspection_date']))
            $errorMessage['inspection_date'] = "Date is required.";
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $pre_inspection = Array();$param_ids = Array();$data['observation_sample'] = '';
            foreach($this->spArray as $key=>$value):
                $param = Array();
                    for($j = 1; $j <=10; $j++):
                        $param[] = $data['sample'.$j.'_'.$key];
                        unset($data['sample'.$j.'_'.$key]);
                    endfor;
                    $pre_inspection[$key] = $param;
                    $param_ids[] = $key;
            endforeach;
            $data['parameter_ids'] = implode(',',$param_ids);
            $data['observation_sample'] = json_encode($pre_inspection);
            $data['param_count'] = count($param_ids);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->heatTreatment->saveMsOutput($data));
        endif;
    }

    public function printMsOutput($id){
        $this->data['dataRow'] = $this->heatTreatment->getMSOutPutData(['ht_id'=>$id]);
        $this->data['spArray'] = $this->spArray;
        $this->data['htData'] = $this->heatTreatment->getMSHeatTreatData(['id'=>$id]);

		$logo=base_url('assets/images/logo.png'); $unapproved = base_url('assets/images/unapproved.jpg');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('heat_treatment/ms_output_print',$this->data,true);
        
		$dataRow = $this->data['dataRow'];
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%">MS & METALLURGICAL TEST REPORT</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">F-P&M-SQF-07(00 / 03.08.2020)</td>
							</tr>
						</table><hr>';
		$htmlFooter = '
					<table class="table top-table" style="margin-top:10px;border-top:1px solid #000000;">
						<tr>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='test'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('L','','','','',5,5,30,20,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');	
    }
    
    public function getTransNo(){
        $data = $this->input->post();
        $trans_no = $this->heatTreatment->getNextTransNo(['month'=>date('m'),'year'=>date('Y'),'furnace_id'=>$data['furnace_id']]);
        $this->printJson(['status'=>1,'trans_no'=>$trans_no]);
    }
    
    public function getMonthLetter(){
		$data = $this->input->post();
		$monthDate = !empty($data['month']) ? date('m',strtotime($data['month'])) : date("m");
		$this->printJson(n2m($monthDate));
	}
}
?>