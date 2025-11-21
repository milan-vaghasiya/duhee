<?php
class GateInward extends MY_Controller{
    private $indexPage = "gate_inward/index";
    private $form = "gate_inward/form";

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Goods Receipt Note";
		$this->data['headData']->controller = "gateInward";
        $this->data['headData']->pageUrl = "gateInward";
    }

    public function index($status = 0){        
        $header = ($status == 0)?"pendingGE":"gateInward";
        $this->data['tableHeader'] = getStoreDtHeader($header);
        $this->data['status'] = $status;
        $this->data['grn_type'] = 1;
        $this->data['fn_name'] = 'index';//GRN Reguler
		$this->load->view($this->indexPage,$this->data);
    }
    
     public function grnRM($status = 0){        
        $header = ($status == 0)?"pendingGE":"gateInward";
        $this->data['tableHeader'] = getStoreDtHeader($header);
        $this->data['status'] = $status;
        $this->data['grn_type'] = 2;
        $this->data['fn_name'] = 'grnRM';//GRN RM
		$this->load->view($this->indexPage,$this->data);
    }
    
    public function getDTRows($status = 0,$grn_type){
        $data = $this->input->post(); 
        $data['status'] = $status;
        $data['grn_type'] = $grn_type;
        $result = $this->gateInward->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;        
            $row->controller = $this->data['headData']->controller;
            $row->status = $status;
            $row->grn_type = $grn_type;
            $sendData[] = getGateInwardData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function createGI(){
        $data = $this->input->post();
        $gateEntryData = $this->gateEntry->getGateEntry($data['id']);
        $this->data['poList'] = $this->purchaseOrder->getPendingPartyWisePOItems(['party_id'=>$gateEntryData->party_id,'grn_type'=>$data['grn_type'],'group_by'=>'order_id']);
        $this->data['gateEntryData'] = $gateEntryData;
        $this->data['locationData'] = $this->stockTransac->getStoreLocationList(['store_type'=>'0,15','group_store_opt'=>1,'final_location'=>1])['storeGroupedArray']; 
        
        if($data['grn_type'] == 1):
            $this->data['itemList'] = $this->item->getItemLists('1,2');
        else:
            $this->data['itemList'] = $this->item->getItemLists('3');
        endif;

        $this->data['materialGradeList'] = (!empty($gateEntryData->material_grade))?$this->materialGrade->getItemWiseMaterialGrade(['material_grade'=>$gateEntryData->material_grade]):[];
        $this->data['ref_id'] = $data['id'];
        $this->data['grn_type'] = $data['grn_type'];
        $this->data['next_no'] = $this->gateInward->getNextNo();
        $this->data['trans_prefix'] = "GI/".n2y(date("Y"));
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post(); 
        $errorMessage = array();

        if(empty($data['item_id'][0])){
            $errorMessage['item_id'] = "Item is required.";
        }
        
        if(empty($data['location_id'][0]))
            $errorMessage['batch_details'] = "Batch Details is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->gateInward->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $gateInward = $this->gateInward->getGateInward($id);
        $this->data['gateInwardData'] = $dataRow = $this->gateInward->getGateInwardData(['trans_prefix'=>$gateInward->trans_prefix,'trans_no'=>$gateInward->trans_no]);
        $this->data['poList'] = $this->purchaseOrder->getPendingPartyWisePOItems(['party_id'=>$gateInward->party_id,'grn_type'=>$dataRow[0]->grn_type,'group_by'=>'order_id']);
        $this->data['locationData'] = $this->stockTransac->getStoreLocationList(['store_type'=>'0,15','group_store_opt'=>1,'final_location'=>1])['storeGroupedArray']; 
        if($dataRow[0]->grn_type == 1):
            $this->data['itemList'] = $this->item->getItemLists('1,2');
        else:
            $this->data['itemList'] = $this->item->getItemLists('3');
        endif;
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->gateInward->delete($id));
        endif;
    }
    
    public function pallate_print($id){ 
        $pData = $this->gateInward->getPallatePrintData($id);
        $countData = count($pData);
        $html = '';
        $logo=base_url('assets/images/logo.png');
        $i=1;
		foreach($pData as $row){
            $html .= '<style>.top-table-border th,.top-table-border td{font-size:12px;}</style><table class="table">
                        <tr>
                            <td><img src="'.$logo.'" style="max-height:40px;"></td>
                            <td class="org_title text-right" style="font-size:1.5rem;">Material Tag</td>
                        </tr>
                    </table>
                    <table class="table top-table-border">
                        <tr> 
                            <th>GI No</th>
                            <td>'.$row->trans_prefix.sprintf("%04d",$row->trans_no).'</td>
                            <th>GI Qty.</th>
                            <td>'.$row->gi_qty.'</td>
                        </tr>
                        <tr> 
                            <th>GI Date</th>
                            <td colspan="3">'.date("d-m-Y h:i:s", strtotime($row->trans_date)).'</td>
                        </tr>
                        <tr> 
                            <th>Part Name</th>
                            <td colspan="3">'.$row->full_name.'</td>
                        </tr>
                        <tr> 
                            <th>Batch Qty</th>
                            <td>'.$row->batch_qty.'</td>
                            <th>Batch No</th>
                            <td>'.$row->mBatch_no.'</td>
                        </tr>
                        <tr> 
                            <th>Pallate Qty</th>
                            <td>'.$row->qty.'</td>
                            <th>Pallate No.</th>
                            <td>'.$i.'/'.$countData.'</td>
                        </tr>
                        <tr> 
                            <th>Printed At</th>
                            <td colspan="3">'.date("d-m-Y h:i:s").'</td>
                        </tr>
                    </table>';	
            $i++;		
		}
        
		$pdfData = '<div style="width:100mm;height:25mm;">'.$html.'</div>';
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 60]]);
        $mpdf->SetDisplayMode('fullpage');
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->AddPage('P','','','','',2,2,2,2,2,2);
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output('tags_print.pdf','I');		
    }
    
    public function ir_print($id){
        $irData = $this->gateInward->getIrPrintData($id);  
        $countData = count($irData);
		$itemList="";$i=1;
		
        $logo=base_url('assets/images/logo.png');

        foreach($irData as $row):
			$itemList .='<style>.top-table-border th,.top-table-border td{font-size:12px;}</style><table class="table">
                            <tr>
                                <td><img src="'.$logo.'" style="max-height:40px;"></td>
                                <td class="org_title text-right" style="font-size:1.5rem;">IIR Tag</td>
                            </tr>
                        </table>
                        <table class="table top-table-border">
                            <tr> 
                                <th>GI No</th>
                                <td>'.$row->trans_prefix.sprintf("%04d",$row->trans_no).'</td>
                                <th>GI Date</th>
                                <td>'.date("d-m-Y h:i:s", strtotime($row->trans_date)).'</td>
                            </tr>
                            <tr> 
                                <th>Part Name</th>
                                <td colspan="3">'.$row->full_name.'</td>
                            </tr>
                            <tr> 
                                <th>Supplier</th>
                                <td colspan="3">'.$row->party_name.'</td>
                            </tr>
                            <tr> 
                                <th> Batch No</th>
                                <td>'.$row->batch_no.'  </td>
                                <th>Batch Qty</th>
                                <td>'.$row->qty.' </td>
                            </tr>
                            <tr> 
                                <th>Heat No </th>
                                <td>'.$row->heat_no.' </td>
                                <th>Pallate No.</th>
                                <td>'.$i.'/'.$countData.'</td>
                            </tr>
                            <tr> 
                                <th>Printed At</th>
                                <td colspan="3">'.date("d-m-Y h:i:s").'</td>
                            </tr>
                    </table>'; $i++;
        endforeach;
		
        $pdfData = '<div style="width:100mm;height:25mm;">'.$itemList.'</div>';

		
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 58]]);
		$pdfFileName='IR_PRINT.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('P','','','','',2,2,2,2,2,2);
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }

    public function getPoItemOptions(){
        $data = $this->input->post();
        $options = '<option value="">Select Item</option>';
        if(!empty($data['po_id'])){
            $poData = $this->purchaseOrder->getPendingPartyWisePOItems(['order_id'=>$data['po_id'],'po_trans_id'=>$data['po_trans_id'],'grn_type'=>$data['grn_type']]);
            if(!empty($poData)){
                foreach($poData as $row){
                    $selected = (!empty($data['po_trans_id']) && $data['po_trans_id'] == $row->id)?'selected':'';

                    $options .= '<option  data-item_name="'.$row->full_name.'" data-po_trans_id="'.$row->id.'"  data-item_stock_Type="'.$row->batch_stock.'"  data-item_type="'.$row->item_type.'"  value="'.$row->item_id.'" '.$selected.'>'.$row->full_name.', [Pending Qty : '.($row->qty - $row->rec_qty).' ]</option>';
                }
            }
        }else{
            if($data['grn_type'] == 1):
                $itemList = $this->item->getItemLists('1,2');
            else:
                $itemList = $this->item->getItemLists('3');
            endif;
            if(!empty($itemList)){
                foreach($itemList as $row){
                    $options .= '<option  data-item_name="'.$row->full_name.'" data-po_trans_id="0"  data-item_stock_Type="'.$row->batch_stock.'"  data-item_type="'.$row->item_type.'"  value="'.$row->id.'">'.$row->full_name.'</option>';
                }
            }
        }
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getPoOptions(){
        $data = $this->input->post();
        $options = '<option value="0" '.(empty($data['po_id'])?'selected':'').'>Without Purchase Order</option>';
        $poData = $this->purchaseOrder->getPendingPartyWisePOItems(['party_id'=>$data['party_id'],'group_by'=>'order_id','po_id'=>$data['po_id']]);
        if(!empty($poData)){
            foreach($poData as $row){
                $selected = (!empty($data['po_id']) && $data['po_id'] == $row->order_id)?'selected':'';
                $options .= '<option  data-po_no="'.($row->po_prefix.$row->po_no).'" value="'.$row->order_id.'" '.$selected.'>'.($row->po_prefix.$row->po_no).'</option>';
            }
        }
        $this->printJson(['status'=>1,'options'=>$options]);
    }
}
?>