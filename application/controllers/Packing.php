<?php
class Packing extends MY_Controller{

    private $indexPage = "packing/index";
    private $formPage = "packing/form";
    private $dispatchIndex = "packing/dispatch_index";
    private $dispatchForm = "packing/dispatch_form";
    private $standardIndex = "packing_standard/index";
    private $standardForm = "packing_standard/form";
    private $exportForm = "packing/export_form";
    private $indexExportPage = "packing/export_packing_index";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Packing";
		$this->data['headData']->controller = "packing";
		// $this->data['headData']->pageUrl = "packing"; 
	}

    public function index(){
        $this->data['tableHeader'] = getDispatchDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data); 
    }

    public function getDTRows($status=0){
        $data = $this->input->post();$data['status'] = $status;
		$result = $this->packings->getDTRows($data); 
        $sendData = array();$i=$data['start']+1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getPackingData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPacking(){
        $this->data['trans_no'] = sprintf('%04d',$this->packings->getNetxNo());
        $this->data['trans_prefix'] = "PACK";
        $this->data['productData'] = $this->item->getItemList(1);
        //$this->data['packingMaterial'] =  $this->item->getItemList(2);
        $this->load->view($this->formPage,$this->data);
    }

    public function getStandardByfgid(){
        $fg_id = $this->input->post('fg_id'); $options='<option value="">Select Packing Material</option>';
        $packingData = $this->packingStandard->getStandardByfgid($fg_id);
        if(!empty($packingData)):
            foreach($packingData as $row):
                $options .= '<option value="'.$row->box_item_id.'" data-box_wt="'.$row->empty_box_wt.'">'.$row->item_name.'</option>';
            endforeach;
        endif;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getSalesOrderList(){
        $item_id = $this->input->post('fg_id'); $options='<option value="">Select Sales Order</option>';
        $soData = $this->salesOrder->pendingSoByItemId($item_id);
        if(!empty($soData)):
            foreach($soData as $row):
                $options .= '<option value="'.$row->id.'">'.$row.' ['.$row->item_code.'] '.$row->item_name.'</option>';
            endforeach;
        endif;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getBatchNo(){ 
        $item_id = $this->input->post('product_id');
        $batchData = $this->packings->batchWiseItemStock($item_id);
        $options = '<option value="" data-batch_no="" data-stock="">Select Batch No.</option>';
        foreach($batchData as $row):
			if($row->qty > 0):
				$options .= '<option value="'.$row->location_id.'" data-batch_no="'.$row->batch_no.'" data-stock="'.$row->qty.'">[ '.$row->store_name.' ]'.$row->batch_no.'</option>';
			endif;
        endforeach; 
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getProductBatchDetails(){
        $data = $this->input->post();
        $postData = ['item_id'=>$data['item_id'],'location_id'=>$this->PACK_STORE->id,'stock_required'=>1,'location_ref_id'=>1];
        $batchData = $this->store->getItemStockBatchWise($postData);//print_r($this->db->last_query());

        $i=1;$tbody = '';
        if(!empty($batchData)):
            foreach($batchData as $row):
                $batchId = trim(preg_replace('/[^A-Za-z0-9]/', '', $row->batch_no)).$row->location_id;
                $location_name = '['.$row->store_name.'] '.$row->location;
                $tbody .= '<tr id="'.$batchId.'">
                    <td>'.$i.'</td>
                    <td>['.$row->store_name.'] '.$row->location.'</td>
                    <td>'.$row->batch_no.'</td>
                    <td id="closing_stock_'.$i.'">'.floatval($row->qty).'</td>
                    <td>
                        <input type="text" name="batch_qty[]" id="batch_qty_'.$i.'" class="form-control floatOnly calculateBatchQty" data-srno="'.$i.'" value="">
                        <input type="hidden" name="location_id[]" id="location_id_'.$i.'" value="'.$row->location_id.'">
                        <input type="hidden" name="batch_no[]" id="batch_no_'.$i.'" value="'.$row->batch_no.'">
                        <input type="hidden" name="batch_id[]" id="batch_id_'.$i.'" value="'.$batchId.'">
                        <input type="hidden" name="location_name[]" id="location_name_'.$i.'" value="'.$location_name.'">
                        <input type="hidden" name="batch_stock[]" id="batch_stock_'.$i.'" value="'.floatVal($row->qty).'">
                        <div class="error batch_qty_'.$i.'"></div>
                    </td>
                </tr>';
                $i++;
            endforeach;
        else:
            $tbody .= '<tr id="batchNoData">
                <td colspan="5" class="text-center">
                    No data available in table
                </td>
            </tr>';
        endif;
        $boxData = $this->packings->getProductPackStandard(['item_id'=>$data['item_id']]);
        $boxOptions = '<option value="">Select Packing Material</option>';
        if(!empty($boxData)){
            foreach($boxData as $row){
                $boxOptions .= '<option value="'.$row->box_id.'" data-qty_box="'.$row->qty_per_box.'">'.$row->item_name.'</option>';
            }
        }
        $this->printJson(['status'=>1,'batchTbody'=>$tbody,'boxOptions'=>$boxOptions]);
    }

    public function save(){
        $data = $this->input->post();
        unset($data['batch_qty'],$data['location_id'],$data['batch_no'],$data['batch_id'],$data['location_name'],$data['batch_stock'],$data['so_no']);
        $errorMessage = array();

        if(empty($data['item_id']))
            $errorMessage['item_id'] = "Product Name is required.";
        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Packing Date is required.";
        if(empty($data['material_data'])):
            $errorMessage['material_error'] = "Packing Transaction is requried.";
        else:
            foreach($data['material_data'] as $key => $row):                
                $postData = ['item_id'=>$row['box_item_id'],'location_id'=>$this->PACK_MTR_STORE->id,'batch_no'=>"General Batch"];
                $currentStock = $this->packings->getItemCurrentStock($postData);
                $stockQty = (!empty($currentStock->qty))?$currentStock->qty:0;
                if(!empty($row['id'])):
                    $packingTrans = $this->packings->getPackingTransRow($row['id']);
                    $stockQty = $stockQty + $packingTrans->total_box;
                endif;
                if($row['total_box'] > $stockQty):
                    $errorMessage['total_box_'.$key] = "Stock not avalible.";
                endif;
            endforeach;
        endif;
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['id'])):
                $data['trans_no'] = sprintf("%04d",$this->packings->getNetxNo());
                $data['trans_prefix'] = "PACK";
                $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];
                $data['created_by'] = $this->loginId;
            else:
                $data['trans_number'] = $data['trans_prefix'].sprintf("%04d",$data['trans_no']);
                $data['updated_by'] = $this->loginId;
            endif;
            $data['total_box'] = array_sum(array_column($data['material_data'],'total_box'));
            $data['total_qty'] = array_sum(array_column($data['material_data'],'total_box_qty'));
            $this->printJson($this->packings->save($data));
        endif;
    }

    public function edit($id){
        $packingOrderData = $this->packings->getPacking($id);    
        $this->data['dataRow'] = $packingOrderData;
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['packingMaterial'] =  $this->item->getItemList(2);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->packings->delete($id));
		endif;
	}

    /* Created By NYN @16/11/2022 */
    public function packingStandard(){
        $this->data['tableHeader'] = getDispatchDtHeader('packingStandard');
        $this->load->view($this->standardIndex,$this->data);
    }

    /* Created By NYN @16/11/2022 */
    public function getStandardDTRows(){
        $data = $this->input->post();
		$result = $this->item->getProdOptDTRows($data,1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;

            $row->packing_standard = '';
            $standard = $this->packings->getProductPackStandard(['item_id'=>$row->id]);
            if(!empty($standard)){ $row->packing_standard = '<i class="fa fa-check text-primary"></i>'; }

            $sendData[] = getStandardData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    /* Created By NYN @16/11/2022 */
    public function updatePackingStandard(){
        $data = $this->input->post();
        $this->data['boxData'] =  $this->packings->getPackingMaterial();
        $this->data['standardData'] = $this->packingStandardTbl(['item_id'=>$data['item_id']])['tbody'];
        $this->load->view($this->standardForm,$this->data);   
    }

    /* Created By NYN @16/11/2022 */
    public function savePackingStandard(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['wt_pcs']))
            $errorMessage['wt_pcs'] = "Weight Per Pcs. is required.";
        if(empty($data['box_id']))
            $errorMessage['box_id'] = "Packing Material is required.";
        if($data['box_type'] == 0)
        {
            if(empty($data['qty_per_box']))
                $errorMessage['qty_per_box'] = "Qty Per Box is required.";
            if(empty($data['wt_per_box']))
                $errorMessage['wt_per_box'] = "Box Weight is required.";
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $result = $this->packings->savePackingStandard($data);
            $standardData = $this->packingStandardTbl(['item_id'=>$data['item_id']]);
            $result['tbody'] = $standardData['tbody'];
            $this->printJson($result);
        endif;
    }

    /* Created By NYN @16/11/2022 */
    public function deletePackingStandard(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $result = $this->packings->deletePackingStandard($data['id']);
            $standardData = $this->packingStandardTbl(['item_id'=>$data['item_id']]);
            $result['tbody'] = $standardData['tbody'];
            $this->printJson($result);
        endif;
    }

    /* Created By NYN @16/11/2022 */
    public function packingStandardTbl($data){
        $standardData = $this->packings->getProductPackStandard($data);
        $tbody='';$i=1;
        if(!empty($standardData)):
            foreach($standardData as $row):
                $deleteParam = $row->id.",".$row->item_id.",'Packing Standard'";
                $tbody.='<tr>
                    <td>'.$i++.'</td>
                    <td>'.(($row->box_type == 0)?'Box' : 'Pallet').'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->qty_per_box.'</td>
                    <td>'.$row->wt_per_box.'</td>
                    <td class="text-center">
                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-danger btn-delete permission-remove" onclick="trashPackingStandard('.$deleteParam.');" datatip="Remove" flow="left"><i class="ti-trash"></i></a>
                    </td>
                </tr>';
            endforeach;
        else:
            $tbody.='<tr>
                    <td colspan="6" class="text-center">No Data Found</td>
                </tr>';
        endif;

        return ['status'=>1,'tbody'=>$tbody];
    }

	/* Dispatch Domestic */
    public function dispatchDomestic(){
        $this->data['tableHeader'] = getSalesDtHeader('dispatchDomestic');
		$this->data['dt_rows'] = 'getDomesticRows';
		$this->data['title'] = 'Dispatch Domestic';
        $this->load->view($this->dispatchIndex,$this->data);
    }

    public function getDomesticRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
		$result = $this->challan->getChallanDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row): 
            $row->sr_no = $i++;
			$row->request_for = 'Challan';
            $sendData[] = getDispatchMaterialData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
	}

    public function addDispatchMaterial(){
        $data = $this->input->post();
        $this->data['dataRow'] = $dataRow = $this->salesOrder->getSalesOrderData($data['id']); 
        $this->data['batchData'] = $this->store->batchWiseItemStock(['item_id' => $dataRow->item_id]);
        $this->load->view($this->dispatchForm,$this->data);
    }
	
	public function batchWiseItemStock(){
		$data = $this->input->post(); 
        $result = $this->store->batchWiseItemStock($data)['result'];
        $i=1;$tbody="";
        if(!empty($result)):
            $batch_no = array();$batch_qty = array();$location_id = array();
            $batch_no = (!empty($data['batch_no']))?((!is_array($data['batch_no']))?explode(",",$data['batch_no']):$data['batch_no']):array();
            $batch_qty = (!empty($data['batch_qty']))?((!is_array($data['batch_qty']))?explode(",",$data['batch_qty']):$data['batch_qty']):array();
            $location_id = (!empty($data['location_id']))?((!is_array($data['location_id']))?explode(",",$data['location_id']):$data['location_id']):array();
            $size = (!empty($data['size']))?((!is_array($data['size']))?explode(",",$data['size']):$data['size']):array();
            foreach($result as $row):                
                if($row->qty > 0 || !empty($batch_no) && in_array($row->batch_no,$batch_no)  && (!empty($size) && in_array($row->size,$size))):
                    if((!empty($batch_no) && in_array($row->batch_no,$batch_no) && in_array($row->location_id,$location_id) && (!empty($size) && in_array($row->size,$size))) || ( (!empty($size) && in_array($row->size,$size) ) && empty($batch_no))):
                        $qty = 0;
                        $qty = $batch_qty[array_search($row->batch_no,$batch_no)];
                        $cl_stock = (!empty($data['trans_id']))?floatVal($row->qty + $qty):floatVal($row->qty);
                    else:
                        $qty = "0";
                        $cl_stock = floatVal($row->qty);
                    endif;                                
                    $totalBox = ($row->size>0) ? ($row->qty/$row->size) : 0;
                    $tbody .= '<tr>';
                        $tbody .= '<td class="text-center">'.$i.'</td>';
                        $tbody .= '<td class="disBatch">['.$row->store_name.'] '.$row->location.'</td>';
                        $tbody .= '<td class="disBatch">'.$row->batch_no.'</td>';
                        $tbody .= '<td class="disBatch">'.floatVal($row->qty).'</td>';
                        $tbody .= '<td class="text-center">'.floatVal($row->size).' x '.$totalBox.'</td>';
                        $tbody .= '<td>
                            <input type="text" name="batch_quantity[]" class="form-control batchQty numericOnly" data-rowid="'.$i.'" data-cl_stock="'.$cl_stock.'" min="0" value="'.$qty.'" />
                            <input type="hidden" name="batch_number[]" id="batch_number'.$i.'" value="'.$row->batch_no.'" />
                            <input type="hidden" name="location[]" id="location'.$i.'" value="'.$row->location_id.'" />
                            <input type="hidden" name="qty_per_box[]" id="qty_per_box'.$i.'" value="'.$row->size.'">
                            <div class="error qty_per_box'.$i.'"></div>
                        </td>';
                    $tbody .= '</tr>';
                    $i++;
                endif;
            endforeach;
        else:
            $tbody = '<tr><td class="text-center" colspan="6">No Data Found.</td></tr>';
        endif;
        $tentativePackData="";
        if($data['packing_type'] == 2){
            $tentativeData = $this->packings->getExportData(['item_id'=>$data['item_id'],'req_id'=>$data['req_id'],'packing_type'=>1]);
            if(!empty($tentativeData)){
                $tentativePackData.='<table class="table table-bordered">
                    <thead>
                    <tr>
                    <th colspan="5" class="text-center">Tentative Packing Detail</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>Package No</th>
                        <th>Qty/Box</th>
                        <th>Total Box</th>
                        <th>Total Qty</th>
                    </tr>
                    </thead><tbody>';
                foreach($tentativeData as $row)
                {
                    $tentativePackData.='<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->package_no.'</td>
                        <td>'.$row->qty_box.'</td>
                        <td>'.$row->total_box.'</td>
                        <td>'.$row->total_qty.'</td>
                    </tr>';
                }
                $tentativePackData.='</tbody></table>';
            }
        }
		$this->printJson(['status','batchData'=>$tbody,'tentativePackData'=>$tentativePackData]);
	}

    public function saveDispatchMaterial(){
        $data = $this->input->post();
        $errorMessage = array();

        if(floatVal($data['totalQty']) != floatVal($data['challan_qty']))
            $errorMessage['totalQty'] = "Invalid Qty.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $result = $this->packings->saveDispatchMaterial($data);
            $this->printJson($result);
        endif;
    }

	public function dispatchExport(){
        $this->data['headData']->pageUrl = "packing/dispatchExport"; 
        $this->data['tableHeader'] = getDispatchDtHeader('dispatchExport');
		$this->data['dt_rows'] = 'getExportRows';
		$this->data['title'] = 'Dispatch Export';
        $this->load->view($this->dispatchIndex,$this->data);
	}
	
	public function getExportRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
		$result = $this->dispatchRequest->getDispatchReqRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row): 
            $row->sr_no = $i++;
			$row->request_for = 'Challan';
			
            $sendData[] = getExportMaterialData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
	}

	public function packingExport($req_no=0,$party_id=0,$packing_type=1){
		$this->data['req_id'] = $req_no;
		$this->data['party_id'] = $party_id;
		$this->data['requestData'] = $this->dispatchRequest->getRequestForChallan(['party_id' => $party_id,'ref_no'=>$req_no]);
        $this->data['packing_type'] = $packing_type;
        $this->load->view($this->exportForm,$this->data);
	}	
	
    public function saveExportPacking(){
        $data = $this->input->post(); 
        unset($data['batch_qty'],$data['location_id'],$data['batch_no'],$data['batch_id'],$data['location_name'],$data['batch_stock'],$data['so_no']);
        $errorMessage = array();

        if(empty($data['item_data']))
            $errorMessage['item_id'] = "Product Name is required.";
        if(empty($data['packing_date']))
            $errorMessage['packing_date'] = "Packing Date is required.";
        if(empty($data['item_data'])):
            $errorMessage['item_id'] = "Product Name is required.";
        endif;
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['id'])):
                $data['trans_no'] = $this->packings->getNextExportNo($data['packing_type']);
                $data['trans_prefix'] = ($data['packing_type'] == 1)?'TEP':'FEP';
                $data['trans_number'] = $data['trans_prefix'].sprintf('%04d',$data['trans_no']);
                $data['created_by'] = $this->loginId;
            else:
                $data['trans_number'] = $data['trans_prefix'].sprintf('%04d',$data['trans_no']);
                $data['updated_by'] = $this->loginId;
            endif;
            $this->printJson($this->packings->saveExportPacking($data));
        endif;
    }

    public function exportPackingIndex($packing_type){
        $this->data['headData']->pageUrl = "packing/dispatchExport"; 
        $this->data['packing_type'] = $packing_type;
        $this->data['tableHeader'] = getDispatchDtHeader('exportPacking');
        $this->load->view($this->indexExportPage,$this->data); 
    }

    public function getExportDTRows($packing_type,$status=0){
        if($packing_type == 3){$packing_type=2;$status=1;}
        $data = $this->input->post();$data['status'] = $status;$data['packing_type'] = $packing_type;
		$result = $this->packings->getExportDTRows($data); 
        $sendData = array();$i=$data['start']+1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getExportPackingData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function editExportPacking($transNo,$packing_type){
        $this->data['packing_type'] = $packing_type;
        $this->data['exportData'] = $exportData= $this->packings->getExportData(['trans_no'=>$transNo,'packing_type'=>$packing_type]); 
        $this->data['requestData'] = $this->dispatchRequest->getRequestForChallan(['party_id' => $exportData[0]->party_id]);
        $this->load->view($this->exportForm,$this->data);
    }
   
    public function deleteExportPacking(){
        $data  =$this->input->post();
        $this->printJson($this->packings->deleteExportPacking($data));
    }

    public function printPackingTag(){
        $data = $this->input->post();
        $printFormat = $this->printFormat->getPrintFormat($data['format_id']);
        $packingData = $this->packings->getExportDetail($data['item_id']);  
        
        // $jobData = (array)json_decode($packingData->ref_json);
        $packingData->job_card_no = "";
        $packingData->job_card_no = $packingData->batch_no;
        $packingData->tag_remark = 'Total Box- '.$packingData->total_box.' ('.$packingData->total_qty.'Pcs)';
        $packingData->company_name = "JAY JALARAM PRECISION COMPONENT LLP";
        $packingData->dispatch_date = formatDate($data['dispatch_date']);
        $packingData->lr_no = $data['lr_no'];
        $packingData->trans_way = $data['trans_way'];
        $packingData->heat_no = $data['heat_no'];
        $packingData->inv_no = $data['inv_no'];
        $packingData->lot_qty = $data['lot_qty'];

        $fieldList = json_decode($printFormat->formate_field);
        $html = "";
        foreach($fieldList as $key=>$label):
            if($key == 'company_name'):
                $html .= '<tr><th colspan="2"><h4><u>'.$packingData->{$key}.'</u></h4></th></tr>';
            else:
                if($key=='qty_per_box'){$key = 'qty_box';}
                $html .= '<tr>
                    <th style="font-size:10px;text-align:left;">'.$label.'</th>
                    <td style="font-size:10px;">'.$packingData->{$key}.' </td>
                </tr>';
            endif;
        endforeach;

        $pdata = '';
        for($i=1;$i<=$data['print_qty'];$i++):
            $pdata .= '<div style="width:100mm;height:50mm;text-align:left;float:left;padding:1mm 1mm;">
                        <table style="width:100%;" class="table item-list-bb">
                            '.$html.'
                        </table>
                    </div>';
        endfor;
        // echo $pdata;exit;
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [$printFormat->width, $printFormat->height]]);
        $stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->setTitle($printFormat->format_name);
        $mpdf->AddPage('P','','','','',0,0,2,2,2,2);
        $mpdf->WriteHTML($pdata);
        $mpdf->Output('tag_print.pdf','I');
    }

    public function packingPdf($trans_no,$packing_type,$type=0,$p_or_m='P'){
        $packingData = $this->packings->getExportData(['trans_no'=>$trans_no,'packing_type'=>$packing_type]);
     
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		
		$logo=base_url('assets/images/logo.png?v='.time());
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png?v='.time());
        $packageData = $this->packings->packingTransGroupByPackage(['trans_no'=>$trans_no,'packing_type'=>$packing_type]);
        $dataArray=array();
        foreach($packageData as $row){
            $row->itemData=$this->packings->getExportDataForPrint(['trans_no'=>$trans_no,'packing_type'=>$packing_type,'package_no'=>$row->package_no]);
            $dataArray[]=$row;
        }
        // print_r($dataArray);
        $this->data['packingMasterData'] = $packingMasterData =$packingData[0];
        $this->data['packingData']=$dataArray;
        $this->data['pdf_type'] = $type;
		$pdfData = $this->load->view('packing/packing_print',$this->data,true);        
		// print_r($pdfData);exit;
		$mpdf = $this->m_pdf->load();
		$fileName= preg_replace('/[^A-Za-z0-9]/',"_",$packingMasterData->trans_prefix.sprintf("%04d",$packingMasterData->trans_no)).'.pdf';
		$filePath = realpath(APPPATH . '../assets/uploads/packing/');
		$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
        $mpdf->setTitle($packingMasterData->trans_prefix.sprintf("%04d",$packingMasterData->trans_no));
        if($packing_type == 1):
            $mpdf->SetWatermarkText('TENTATIVE',0.05);
            $mpdf->showWatermarkText = true;
        else:
		    $mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		    $mpdf->showWatermarkImage = true;
		endif;
		
		$mpdf->SetHTMLHeader("");
		$mpdf->SetHTMLFooter("");
		$mpdf->AddPage('P','','','','',5,5,15,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		//$mpdf->Output($fileName,'I');
		
		if($p_or_m == 'P'):
			$mpdf->Output($fileName,'I');
		else:
		    $packType = '';
		    // if(($packingMasterData->entry_type == 'Export') AND $packingMasterData->is_final == 0){$packType = 'Tentative';}
		    // if(($packingMasterData->entry_type == 'Export') AND $packingMasterData->is_final == 1){$packType = 'Final';}
        	$mpdf->Output($filePath.'/'.$fileName, 'F');
			return ['pdf_file'=>$filePath.'/'.$fileName,'packing_no'=>$packingMasterData->trans_number,'packType'=>(!empty($packing_type==1)?'Tentative':'Final')];
		endif;
    }

    public function printFormatList(){
        $printFormat = $this->printFormat->getAllPrintFormats();

        $options = '<option value="">Select Format</option>';
        foreach($printFormat as $row):
            $options .= '<option value="'.$row->id.'">'.$row->format_name.'</option>';
        endforeach;

        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getPackingItems(){
        $data=$this->input->post();
        $itemData=$this->packings->getExportData(['trans_no'=>$data['trans_no'],'packing_type'=>$data['packing_type']]);
        $options ='<option value="">Select Item</option>';
        if(!empty($itemData)){
            foreach($itemData as $row):
                $itemName=(!empty($row->item_code)?'['.$row->item_code.'] ':'').$row->item_name;
                $options .= '<option value="'.$row->id.'" >'.$itemName.'</option>';
            endforeach;
        }
        $this->printJson(['status'=>1,'options'=>$options]);
    }
}
?>