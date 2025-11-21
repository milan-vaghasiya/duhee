<?php
class fir extends MY_Controller
{
    private $indexPage = "fir/index";
    private $formPage = "fir/accept_form";
    private $firDimension = "fir/fir_dimension";
    private $pending_fir_index = "fir/pending_fir_index";
    private $fir_index = "fir/fir_index";
    private $confirmView = "fir/fir_view";
    private $lot_form = "fir/form";
    private $sequence_form = "fir/sequence_form";


    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "FIR Report";
        $this->data['headData']->controller = "fir";
    }

    public function index()
    {
        $this->data['tableHeader'] = getQualityDtHeader("firInward");
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($status=0)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->fir->getDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getFIRInwardData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function pendingFirIndex()
    {
        $this->data['tableHeader'] = getQualityDtHeader("pendingFir");
        $this->load->view($this->pending_fir_index, $this->data);
    }

    public function getPendingFirDTRows($status=0)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->fir->getPendingFirDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getPendingFirData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function acceptFIR(){ 
        $id= $this->input->post('id');
        $this->data['jobData'] = $this->processMovement->getJobTransMovement(['id'=>$id]);
        $this->load->view($this->formPage,$this->data);
    }

    public function addFirLot(){
        $id = $this->input->post('id');
        $this->data['firData'] =$firData = $this->processMovement->getJobTransMovement(['id'=>$id]);
        $this->data['movementList'] = $this->fir->getFIRPendingJobTrans(['job_card_id'=>$firData->job_card_id,'vendor_id'=>$firData->vendor_id]);
        $lot_no = $this->fir->getMaxLotNoJobcardWise(['job_card_id'=>$firData->job_card_id]);
        $fir_number="FIR/".$firData->job_number.'/'.$lot_no;
        $this->data['fir_prefix'] = "FIR/";
        $this->data['fir_no'] =$lot_no;
        $this->data['fir_number'] =$fir_number;
        $this->data['fg_no'] =$fg_no = $this->fir->getMaxFGNo(['item_id'=>$firData->product_id]);
        $year = n2y(date('Y'));
        $month = n2m(date('m'));
        $this->data['fg_batch_no'] =$year.$month.sprintf('%02d',$fg_no);

        // $prsData = $this->item->getPrdProcessDataProductProcessWise(['item_id'=>$firData->product_id,'process_id'=>$firData->in_process_id]);

        // $this->data['firDimensionData'] = $this->controlPlan->getCPDimenstion(['pfc_id'=>$prsData->pfc_process,'item_id'=>$firData->product_id,'control_method'=>'FIR']);
    
        // $this->data['empData'] = $this->employee->getEmpList();
        $this->load->view($this->lot_form,$this->data);
    }

    public function saveInward(){
        $data = $this->input->post();
        if($data['job_card_id'] == "")
            $errorMessage['job_card_id'] = "Jobcard is required.";
        if(empty($data['qty']) || $data['qty'] == "0.000"):
            $errorMessage['qty'] = "Quantity is required.";
        else:
            $fiStock = $this->processMovement->getJobTransMovement(['id'=>$data['job_trans_id']]);
            // print_r(($fiStock->inward_qty - $fiStock->in_qty));
            if($data['qty'] > ($fiStock->qty - $fiStock->accepted_qty)){
                $errorMessage['qty'] = "Quantity is not available.";
            }
        endif;
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->fir->saveInward($data));
        endif;
    }   

    /**** Pending FIR Report Save */
    public function save(){
        $data = $this->input->post(); //print_r($data);exit;
        if(empty($data['job_card_id']))
            $errorMessage['job_card_id'] = "Jobcard is required.";
        if(empty($data['total_ok_qty']) && empty($data['total_rej_qty']) && empty($data['total_rw_qty'])):
            $errorMessage['general_error'] = "OK Qty Or Rejection Qty. is required.";
        else:
            $totalQty = $data['total_ok_qty']+(!empty($data['total_rej_qty'])?$data['total_rej_qty']:0)+(!empty($data['total_rw_qty'])?$data['total_rw_qty']:0);
            if($totalQty != $data['qty']):
                $errorMessage['ok_qty'] = "Qty is not valid";
            endif;
       
          
                $i=1;
                
                $insqQtySum = array_sum($data['ok_qty'])+array_sum($data['ud_ok_qty'])+array_sum($data['rej_qty'])+array_sum($data['rw_qty']);
                if(empty($insqQtySum)){
                    $errorMessage['general_error'] = "Fill at least one parameter.";
                }else{
                    $totalRej = array_sum($data['rej_qty']);
                    if($data['total_rej_qty'] != $totalRej)
                        $errorMessage['general_error'] = "Rejection Qty does not match with Total Rejection Qty.";
                    
                    $totalRw = array_sum($data['rw_qty']);
                    if($data['total_rw_qty'] != $totalRw)
                        $errorMessage['general_error'] = "Rework Qty does not match with Total Rework Qty.";
                    foreach($data['dimension_id'] as $key=>$value){
                        $qty = $data['ok_qty'][$key]+$data['ud_ok_qty'][$key]+$data['rej_qty'][$key]+$data['rw_qty'][$key];
                    
                        if($qty > $data['qty']){
                            $errorMessage['insp_qty_'.$i] = "Quantity is invalid.";
                        }
                        $i++;
                    }
           
            }
        endif;
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->fir->save($data);
            $this->printJson($result);
        endif;
    }

    public function firIndex($status = 0)
    {
        $this->data['status'] = $status;
        $this->data['tableHeader'] = getQualityDtHeader("fir");
        $this->load->view($this->fir_index, $this->data);
    }

    public function getFirDTRows($status=0)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->fir->getFirDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getFirData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function edit($id){
        $this->data['dataRow'] =$firData = $this->fir->getFIRMasterDetail($id);
        $this->data['firData'] =  $this->processMovement->getApprovalData($this->data['dataRow']->job_approval_id);
        $paramData = $this->fir->getFIRDimensionData(['fir_id'=>$id]);
        
        // $controlMethodArray=[];
        // if(!empty($paramData)){
        //     foreach($paramData as $cm){
        //         if(!empty($cm->instrument_code)){
        //             $ins = explode(",",$cm->instrument_code);
        //             if(!empty($ins)){
        //                 $instrumentData1  = ''; $instrumentData2='';$specialChar='';
        //                 if(!empty($ins[0])){
        //                     $catData1 = $this->instrument->getDataForGenerateCode(['item_code'=>$ins[0]]);
        //                     $instrumentData1  = $catData1->category_name.'('.$ins[0].') '.(!empty($catData1->least_count)?'LC - '.$catData1->least_count:'');
        //                 }
        //                 if(!empty($ins[1])){
        //                     $catData2 = $this->instrument->getDataForGenerateCode(['item_code'=>$ins[1]]);
        //                     $instrumentData2  = $catData2->category_name.'('.$ins[1].') '.(!empty($catData2->least_count)?'LC - '.$catData2->least_count:'');
    
        //                     $specialChar = ($cm->detec == 1)?' & ':' / ';
        //                 }
        //                 $cm->category_name = $instrumentData1.$specialChar.$instrumentData2;
        //             }
        //         }else{
        //             $cm->category_name =  $cm->potential_effect;
        //         }
        //         $controlMethodArray[]=$cm;
        //     }
        // }
        $this->data['firDimensionData'] = $paramData;
        $this->data['empData'] = $this->employee->getFQCInspectorList();
        $this->data['pdiStock'] = $this->store->getItemStockBatchWise(['single_row'=>1,'stock_required'=>1,'item_id'=>$firData->item_id,'location_id'=>$this->PDI_STORE->id]);
        $this->load->view($this->firDimension,$this->data);
    }

    public function completeFirView(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->fir->getFIRMasterDetail($id);
        $this->data['firData']=$aproveData =  $this->processMovement->getApprovalData($this->data['dataRow']->job_approval_id);
        $this->data['firDimensionData'] =$paramData = $this->fir->getFIRDimensionData(['fir_id'=>$id]);
        $processArray = explode(",", '0,'.$aproveData->process);
        $in_process_key = array_keys($processArray, $aproveData->in_process_id)[0];
        $this->data['heatData'] = $this->processMovement->getHeatData(['job_card_id'=>$aproveData->job_card_id,'process_id'=>$processArray[($in_process_key-1)]]);
        $this->load->view($this->confirmView,$this->data);
    }

    public function completeFir(){
        $data = $this->input->post();
        if(empty($data['batch_no'])){  $errorMessage['batch_no'] = "Batch No is required."; }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $result = $this->fir->completeFir($data);
            $this->printJson($result);
        endif;
       
    }


     
    public function delete()
    {
          $id = $this->input->post('id');
          if (empty($id)) :
              $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
          else :
              $result = $this->fir->delete($id);
              $this->printJson($result);
          endif;
    }

    /**** Pending FIR Report Save */
    public function saveLot(){
        $data = $this->input->post(); //print_r($data);exit;
        if(empty($data['job_card_id']))
            $errorMessage['job_card_id'] = "Jobcard is required.";
       
        if (!isset($data['job_trans_id']))
            $errorMessage['orderError'] = "Please Check atleast one Transaction.";

        if (!empty($data['job_trans_id'][0])) :
            foreach ($data['job_trans_id'] as $key => $value) :
                if (empty($data['lot_qty'][$key])) :
                    $errorMessage['lotQty' . $value] = "Qty. is required.";
                endif;
            endforeach;
        endif;
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->fir->saveLot($data);
            $this->printJson($result);
        endif;
    }

    public function fir_pdf($id){
        $this->data['dataRow'] = $this->fir->getFIRMasterDetail($id);
        $this->data['firData'] =  $this->processMovement->getApprovalData($this->data['dataRow']->job_approval_id);
        $this->data['firDimensionData'] =$paramData = $this->fir->getFIRDimensionData(['fir_id'=>$id]);
        
        $controlMethodArray=[];
        if(!empty($paramData)){
            foreach($paramData as $cm){
                $rejData = $this->fir->getRejData(['fir_trans_id'=>$cm->id]);
               
                $cm->mc_rej_qty= !empty($rejData->mc_rej_qty)?$rejData->mc_rej_qty:0;
                $cm->rm_rej_qty= !empty($rejData->rm_rej_qty)?$rejData->rm_rej_qty:0;
               
                $controlMethodArray[]=$cm;
            }
        }
        $this->data['firDimensionData'] = $controlMethodArray;
      
		$logo=base_url('assets/images/logo.png'); $unapproved = base_url('assets/images/unapproved.jpg');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('fir/fir_print',$this->data,true); 
        
		$paramData = $this->data['paramData'];
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%">Final Inspection Report</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">R-QC-04 (00/01.10.17)</td>
							</tr>
						</table><hr>';
		$htmlFooter = '
					<table class="table top-table" style="margin-top:10px;border-top:1px solid #000000;">
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
		$mpdf->AddPage('L','','','','',5,5,30,20,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');	
    }

    public function saveDimension(){
        $data = $this->input->post(); //print_r($data);exit;
        if(empty($data['in_qty']) || $data['in_qty']==0.000):
            $errorMessage['inQty'.$data['id']] = "In Qty is required.";
        else:
            $data['inspected_qty'] = $data['ok_qty']+(!empty($data['ud_ok_qty'])?$data['ud_ok_qty']:0)+(!empty($data['rej_qty'])?$data['rej_qty']:0)+(!empty($data['rw_qty'])?$data['rw_qty']:0);
            $dimData = $this->fir->getFIRDimensionDetail(['id'=>$data['id']]);
            
            // if($dimData->lot_type == 2){
            //     if($data['inspected_qty'] < $dimData->sample_qty || $data['inspected_qty'] > $dimData->qty):
            //         $errorMessage['insp_qty_'.$data['id']] = "Qty is not valid";
            //     endif;   
                
            // }else{
            //     if($data['inspected_qty'] > $data['in_qty']):
            //         $errorMessage['insp_qty_'.$data['id']] = "Qty is not valid";
            //     endif;   
               
            // }
            if($data['inspected_qty'] > $data['in_qty']):
                $errorMessage['insp_qty_'.$data['id']] = "Qty is not valid";
            endif;  
            if(empty($data['inspector_id'])){
                $errorMessage['inspector_id_'.$data['id']] = "Inspector is not valid";
            }
        endif;
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->fir->saveDimension($data);
            $this->printJson($result);
        endif;
    }

    public function changeDimensionSequence(){
        $data = $this->input->post();
        $this->data['paramData'] = $this->fir->getFIRDimensionData(['fir_id'=>$data['id']]);
        $this->data['fir_id'] = $data['id'];
        $this->load->view($this->sequence_form,$this->data);
    }

    
    public function updateDimensionSequance(){
        $data = $this->input->post();
		$errorMessage = array();		
		if(empty($data['id']))
			$errorMessage['id'] = "Dimension is required.";
		
		if(empty($errorMessage)):
			$this->printJson($this->fir->updateDimensionSequance($data));			
		endif;
    }
}
