<?php
class ProcessApprovalModel extends MasterModel{
    private $jobCard = "job_card";
    private $jobBom = "job_bom";
    private $productionApproval = "job_approval";
    private $productionTrans = "job_transaction";
    private $jobUsedMaterial = "job_used_material";
    private $jobMaterialDispatch = "job_material_dispatch";
    private $setupRequest = "prod_setup_request";
    private $setupRequestTrans = "prod_setup_trans";
    private $itemMaster = "item_master";
    private $stockTransaction = "stock_transaction"; 
    private $firMaster = "fir_master";   

    public function nextJobWorkChNo(){
        $data['select'] = "MAX(challan_no) as jobChNo";
        $data['where']['vendor_id > '] = 0;
        $data['tableName'] = $this->productionTrans;
		$jobChNo = $this->specificRow($data)->jobChNo;
		$nextChNo = (!empty($jobChNo))?($jobChNo + 1):1;
		return $nextChNo;
    }    
    
	public function getBatchStock($job_card_id,$processId){
        $data['tableName'] = $this->jobUsedMaterial;
        $data['where']['job_card_id'] = $job_card_id;
        $data['where']['process_id'] = $processId;
        $data['where']['bom_item_type'] = 3;
        return $this->rows($data);
    }

    public function getBatchStockOnProductionTrans($job_card_id,$process_id,$id){
        $data['tableName'] = $this->productionTrans;
        $data['select'] = "SUM(issue_material_qty) as issue_qty,material_used_id as id,issue_batch_no as batch_no";
        $data['where']['job_card_id'] = $job_card_id;
        $data['where']['process_id'] = $process_id;
        $data['where']['entry_type'] = 1;
        $data['group_by'][] = "issue_batch_no";
        $data['group_by'][] = "material_used_id";
        $result = $this->rows($data);
        
        $resultData = array();
        foreach($result as $row):
            $juq['select'] = 'wp_qty';
            $juq['tableName'] = $this->jobUsedMaterial;
            $juq['where']['id'] = $row->id;
            $wp_qty = $this->row($juq)->wp_qty;
            $row->wp_qty = (!empty($wp_qty))?$wp_qty:0;

            $data['tableName'] = $this->productionTrans;
            $data['select'] = "SUM(issue_material_qty) as used_qty";
            $data['where']['job_card_id'] = $job_card_id;
            $data['where']['process_id'] = $process_id;
            $data['where']['entry_type'] = 2;
            $data['where']['job_approval_id'] = $id;
            $data['where']['issue_batch_no'] = $row->batch_no;
            $usedStock = $this->row($data);
            $row->used_qty = (!empty($usedStock->used_qty))?$usedStock->used_qty:0;
            $resultData[] = $row;
        endforeach;
        //print_r($resultData);exit;  
        return $resultData;
    }   

    public function getJobWorkOrderNoList($data){
        $data['tableName'] = "job_work_order";
        $data['where']['job_work_order.product_id'] = $data['product_id'];
        $data['where']['job_work_order.vendor_id'] = $data['vendor_id'];
        $data['where']['job_work_order.jwo_status'] = 0;
        $data['customWhere'][] = 'find_in_set("'.$data['process_id'].'", process_id)';
        $result = $this->rows($data);

        $options = '<option value="">Select Job Order No.</option>';
        foreach($result as $row):
            $options .= '<option value="'.$row->id.'">'.getPrefixNumber($row->jwo_prefix,$row->jwo_no).' [ Ord. Qty. : '.$row->qty.' ]</option>';
        endforeach;

        return ['status'=>1,'options'=>$options,'result'=>$result];
    }
    
    public function getJobWorkOrderProcessList($data){
        if(!empty($data['job_order_id'])):
            $queryData =  array();
            $queryData['tableName'] = "job_work_order";
            $queryData['where']['id'] = $data['job_order_id'];
            $result = $this->row($queryData);
            
            $options = '';$jobProcessList=array();
            if(!empty($result->process_id)): 
                $processList = explode(",",$result->process_id);

                $queryData =  array();
                $queryData['tableName'] = $this->jobCard;
                $queryData['select'] = "process";
                $queryData['where']['id'] = $data['job_card_id'];
                $jobProcess = $this->row($queryData)->process;
                $jobProcess = explode(",",$jobProcess);

                $a=0;$jwoProcessIds=array();
                foreach($jobProcess as $key=>$value):                
                    if(isset($processList[$a])):
                        $processKey = array_search($processList[$a],$jobProcess);
                        $jwoProcessIds[$processKey] = $processList[$a];
                        $a++;
                    endif;
                endforeach;
                ksort($jwoProcessIds);

                $processList = array();
                foreach($jwoProcessIds as $key=>$value):
                    $processList[] = $value;
                endforeach;
            
                $in_process_key = array_search($data['process_id'],$jobProcess);
                $i=0;            
                foreach($jobProcess as $key=>$value):
                    if($key >= $in_process_key):
                        if(isset($processList[$i]) && in_array($value,$processList)):
                            $processData = $this->process->getProcess($value);
                            $options .= '<option value="'. $processData->id.'" selected>'.$processData->process_name.'</option>';
                            $jobProcessList[] = $processData->id;
                            $i++;
                        endif;
                    endif;
                endforeach;
            endif;
        else:
            $vendorProcess = $this->party->getParty($data['vendor_id']);
            
            $options = '';$jobProcessList=array();
            if(!empty($vendorProcess->process_id)):
                $processList = explode(",",$vendorProcess->process_id);
                
                $queryData =  array();
                $queryData['tableName'] = $this->jobCard;
                $queryData['select'] = "process";
                $queryData['where']['id'] = $data['job_card_id'];
                $jobProcess = $this->row($queryData)->process;
                $jobProcess = explode(",",$jobProcess);

                $jwoProcessIds=array();
                $countVendorProcess = count($processList);
                for($a=0; $a<$countVendorProcess; $a++):
                    $processKey = array_search($processList[$a],$jobProcess);
                    if($processKey != ""):
                        $jwoProcessIds[$processKey] = $processList[$a];
                    endif;
                endfor;
                ksort($jwoProcessIds);
               
                $processList = array();
                foreach($jwoProcessIds as $key=>$value):
                    $processList[] = $value;
                endforeach;
            
                $in_process_key = array_search($data['process_id'],$jobProcess);
                $i=0; 
                foreach($jobProcess as $key=>$value):
                    if($key >= $in_process_key):
                        if(isset($processList[$i]) && in_array($value,$processList)):
                            $processData = $this->process->getProcess($value);
                            $options .= '<option value="'. $processData->id.'" selected>'.$processData->process_name.'</option>';
                            $jobProcessList[] = $processData->id;
                            $i++;
                        endif;
                    endif;
                endforeach;
            endif;
        endif;
        return ['status'=>1,'options'=>$options,'result'=>$jobProcessList,'job_process'=>implode(",",$jobProcessList)];
    }

    public function getOutward($id){
        $data['tableName'] = $this->productionApproval;
        $data['select'] = "job_approval.*,job_card.job_date,job_card.job_no,job_card.job_prefix,job_card.delivery_date,item_master.item_name as product_name,item_master.item_code as product_code";
        $data['join']['job_card'] = "job_approval.job_card_id = job_card.id";
        $data['join']['item_master'] = "job_approval.product_id = item_master.id";
        $data['where']['job_approval.id'] = $id;
        return $this->row($data);
    }

    public function save($data){
			
		$data['challan_prefix'] = '';$data['challan_no'] = 0;
		if(!empty($data['vendor_id'])):
			$data['challan_prefix'] = 'JO/'.$this->shortYear.'/';
			$data['challan_no'] = $this->nextJobWorkChNo();
		endif;
        $outwardData = $this->getOutward($data['ref_id']);
        $reworkProcess = "";
        if($outwardData->trans_type == 1):
            $queryData = array();
            $queryData['tableName'] = $this->productionTrans;
            $queryData['where']['id'] = $outwardData->ref_id;
            $refInwardData = $this->row($queryData);
            $reworkProcess = $refInwardData->rework_process_id;
        endif;
        $inwardData = [
            'id' => "",
            'entry_type' => 1,
            'entry_date' => $data['entry_date'],
            'job_card_id' => $data['job_card_id'],
            'job_approval_id' => $data['ref_id'],
            'job_order_id' => $data['job_order_id'],
            'vendor_id' => $data['vendor_id'],
            'process_id' => $data['out_process_id'],            
            'product_id' => $data['product_id'],            
            'in_qty' => $data['out_qty'],
            'in_w_pcs' => (!empty($data['in_qty_kg'])) ? ($data['in_qty_kg'] / $data['out_qty']) : 0,
            'in_total_weight' => $data['in_qty_kg'],
            'remark' => $data['remark'],
            'challan_prefix' => $data['challan_prefix'],
            'challan_no' => $data['challan_no'],
            'material_used_id' => $data['material_used_id'],
            'issue_batch_no' => $data['batch_no'],
            'issue_material_qty' => $data['req_qty'],
            'machine_id' => $data['machine_id'],
            'job_process_ids' => $data['job_process_ids'],  
            'rework_process_id' => $reworkProcess,
            'setup_status' => $data['setup_status'],
            'created_by' => $data['created_by']
        ];
        $saveInward = $this->store($this->productionTrans,$inwardData);		
        
        $this->edit($this->productionApproval,['id'=>$data['ref_id']],['out_qty'=>($data['out_qty'] + $outwardData->out_qty)]);
        		
		/*** If First Process then Maintain Batchwise Rowmaterial ***/
		$jqry['select'] = 'process';
		$jqry['where']['id'] = $data['job_card_id'];
        $jqry['tableName'] = $this->jobCard;
        $jobData = $this->row($jqry); 
		$jobProcesses = explode(",",$jobData->process);
		
		if($data['out_process_id'] == $jobProcesses[0]):
			/* Update Used Stock in Job Material Used */
			$setData = Array();
			$setData['tableName'] = $this->jobUsedMaterial;
			$setData['where']['id'] = $data['material_used_id'];
			$setData['set']['used_qty'] = 'used_qty, + '.$data['req_qty'];
			$qryresult = $this->setValue($setData);
		endif;
		
        if($data['material_request'] == 1):
            $queryData = array();
            $queryData['tableName'] = $this->jobBom;
            $queryData['where']['job_card_id'] = $data['job_card_id'];
            $queryData['where']['process_id'] = $data['out_process_id'];
            $jobBomData = $this->rows($queryData);
            if(!empty($jobBomData)):
                foreach($jobBomData as $row):
                    $itemData = $this->item->getItem($row->ref_item_id);
                    $materialDispatchData = [
                        'id' => "",
                        'job_card_id' => $data['job_card_id'],
                        'job_trans_id' => $saveInward['insert_id'],
                        'material_type' => $itemData->item_type,
                        'process_id' => $data['out_process_id'],
                        'req_date' => date("Y-m-d"),
                        'req_item_id' => $row->ref_item_id,
                        'req_qty' => $row->qty * $data['out_qty'],
                        'machine_id' => $data['machine_id'],
                        'created_by' => $data['created_by']
                    ];
                    $this->store($this->jobMaterialDispatch,$materialDispatchData);
                endforeach;
            endif;
        endif;

        if(empty($data['setup_status'])):
            $itemData = $this->item->getItem($data['product_id']);
            $setupReqData = [
                'id' => '',
                'job_card_id' => $data['job_card_id'],
                'job_trans_id' => $saveInward['insert_id'],
                'process_id' => $data['out_process_id'],
                'product_id' => $data['product_id'],
                'item_code' => $itemData->item_code,
                'request_by' => $data['created_by'],
                'request_date' => date("Y-m-d"),
                'machine_id' => $data['machine_id'],
                'setter_id' => $data['setter_id'],
                'remark' => $data['remark'],
                'created_by' => $data['created_by']
            ];
            $saveSetupReq = $this->store($this->setupRequest,$setupReqData);

            $setupReqTransData = [
                'id' => '',
                'setup_id' => $saveSetupReq['insert_id'],
                'setup_type' => 1,
                'setup_note' => $data['remark'],
                'setter_id' => $data['setter_id'],
                'created_by' => $data['created_by']
            ];
            $this->store($this->setupRequestTrans,$setupReqTransData);
        endif;

        return ['status'=>1,'message'=>'Outward saved successfully.','outwardTrans'=>$this->getOutwardTrans($data['ref_id'],$data['out_process_id'])['htmlData']];
    }

    public function delete($id){
        $data['tableName'] = $this->productionTrans;
        $data['where']['id'] = $id;
        $inwardData = $this->row($data);

        if(!empty($inwardData->accepted_by)):
            return ['status'=>0,'message'=>"You can't delete this outward because This outward accepted by next process."];
        endif;

        if(!empty($inwardData->material_issue_status)):
            return ['status'=>0,'message'=>"You can't delete this outward because Material Dispatched."];
        endif;

        $this->trash($this->jobMaterialDispatch,['job_card_id'=>$inwardData->job_card_id,'job_trans_id'=>$inwardData->id]);

        if(empty($inwardData->setup_status)):
            $queryData = array();
            $queryData['tableName'] = $this->setupRequest;
            $queryData['select'] = "id";
            $queryData['where']['job_card_id'] = $inwardData->job_card_id;
            $queryData['where']['job_trans_id'] = $inwardData->id;
            $reqData = $this->rows($queryData);

            $this->trash($this->setupRequest,['job_card_id'=>$inwardData->job_card_id,'job_trans_id'=>$inwardData->id]);

            foreach($reqData as $row):
                $this->trash($this->setupRequestTrans,['setup_id'=>$row->id]);
            endforeach;
        endif;

        $outwardData = $this->getOutward($inwardData->job_approval_id);
        $this->edit($this->productionApproval,['id'=>$inwardData->job_approval_id],['out_qty'=>($outwardData->out_qty - $inwardData->in_qty)]);

        /* Update Used Stock in Job Material Used */
        $setData = Array();
        $setData['tableName'] = $this->jobUsedMaterial;
        $setData['where']['id'] = $inwardData->material_used_id;
        $setData['set']['used_qty'] = 'used_qty, - '.$inwardData->issue_material_qty;
        $qryresult = $this->setValue($setData);
        
        
        $result = $this->trash($this->productionTrans,['id'=>$id],'Outward');
        $result['outwardTrans'] = $this->getOutwardTrans($inwardData->job_approval_id, $inwardData->process_id)['htmlData'];
        return $result;
    }

    public function getOutwardTrans($id,$processId){
        $data['tableName'] = $this->productionTrans;
        $data['select'] = "job_transaction.*,job_approval.trans_type";
        $data['leftJoin']['job_approval'] = "job_transaction.job_approval_id = job_approval.id";
        $data['where']['job_transaction.job_approval_id'] = $id;
        $data['where']['job_transaction.process_id'] = $processId;
        $data['where']['job_transaction.entry_type'] = 1;
        $result = $this->rows($data);

        $dataRow = array();$html = "";
        if(!empty($result)): 
            $i=1;   
            foreach($result as $row):
                $transDate = date("d-m-Y",strtotime($row->entry_date));
                $transType = ($row->trans_type == 0)?"Regular":"Rework";
                $deleteBtn = '';
                if(empty($row->accepted_by)):
                    $deleteBtn = '<button type="button" onclick="trashOutward('.$row->id.');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
                endif;
                $html .= '<tr>
                            <td>'.$i++.'</td>
                            <td>'.$transDate.'</td>
                            <td>'.$transType.'</td>
                            <td>'.$row->issue_batch_no.'</td>
                            <td>'.$row->in_qty.'</td>
                            <td class="text-center" style="width:10%;">
							    '.$deleteBtn.'
						    </td>
                        </tr>';
                $dataRow[] = $row;
            endforeach;
        else:
            $html = '<td colspan="6" class="text-center">No Data Found.</td>';
        endif;
        return ['htmlData'=>$html,'outwardTrans'=>$dataRow];
    }

    public function getStoreLocationTrans($id){
        $queryData['tableName'] = $this->stockTransaction;
        $queryData['select'] = "stock_transaction.*,location_master.store_name,location_master.location";
        $queryData['leftJoin']['location_master'] = "stock_transaction.location_id = location_master.id";
        $queryData['where']['ref_type'] = 7;
        $queryData['where']['ref_id'] = $id;
        $queryData['where']['trans_type'] = 1;
        $stockTrans = $this->rows($queryData);

        $html = '';
        if(!empty($stockTrans)):
            $i=1;
            foreach($stockTrans as $row):
                $deleteBtn = '<button type="button" onclick="trashStockTrans('.$row->id.');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
                $html .= '<tr>
                            <td>'.$i++.'</td>
                            <td>'.$row->batch_no.'</td>
                            <td>[ '.$row->store_name.' ] '.$row->location.'</td>
                            <td>'.$row->qty.'</td>
                            <td>'.$deleteBtn.'</td>
                        </tr>';
            endforeach;
        else:
            $html .= '<tr>
                        <td class="text-center" colspan="5">No Data Found.</td>
                    </tr>';
        endif;

        return ['status'=>1,'htmlData'=>$html,'result'=>$stockTrans];
    }

    public function saveStoreLocation($data){
		$jobData = $this->jobcard->getJobcard($data['job_id']);
        /* $productParam = $this->finalInspection->getInspectionParam($data['product_id']);
        if(empty($productParam)):
            return ['status'=>2,'message'=>"Product Paramiters not found.",];
        endif; */

        $stockTrans = [
            'id' => "",
            'location_id' => $data['location_id'],            
            'trans_type' => 1,
            'item_id' => $data['product_id'],
            'qty' => $data['qty'],
            'ref_type' => 7,
            'ref_id' => $data['job_id'],
            'ref_no' => $jobData->job_prefix.$jobData->job_no,
            'trans_ref_id' => $data['ref_id'],
            'ref_date' => $data['trans_date'],
            'created_by' => $data['created_by']
        ];
        if(!empty($data['batch_no']))
            $stockTrans['batch_no'] = $data['batch_no'];

        $stockTransSave = $this->store($this->stockTransaction,$stockTrans);

        $firData = [
            'id' => "",
            'fir_no' => $this->finalInspection->getNetxFirNo(),
            'fir_prefix' => $this->finalInspection->getFirPrefix(),
            'job_card_id' => $data['job_id'],
            'product_id' => $data['product_id'],
            'in_qty' => $data['qty'],
            'ref_id' => $stockTransSave['insert_id']
        ];
        $this->store($this->firMaster,$firData);

        $setData = array(); 
        $setData['tableName'] = $this->jobCard;
        $setData['where']['id'] = $data['job_id'];
        $setData['set']['unstored_qty'] = 'unstored_qty, - '.$data['qty'];
        $this->setValue($setData);
        
        $setData = array();
        $setData['tableName'] = $this->itemMaster;
        $setData['where']['id'] = $data['product_id'];
        $setData['set']['qty'] = 'qty, + '.$data['qty'];
        $this->setValue($setData);

        $setData = array();
        $setData['tableName'] = $this->productionApproval;
        $setData['where']['id'] = $data['ref_id'];
        $setData['set']['out_qty'] = 'out_qty, + '.$data['qty'];
        $this->setValue($setData);

        $unstored_qty = $this->jobcard->getJobCard($data['job_id'])->unstored_qty;
        return ['status'=>1,'message'=>"Stock Transfer successfully.",'htmlData'=>$this->getStoreLocationTrans($data['job_id'])['htmlData'],'unstored_qty'=>$unstored_qty];
    }

    public function deleteStoreLocationTrans($id){
        $queryData['tableName'] = $this->stockTransaction;
        $queryData['where']['id'] = $id;
        $stockTrans = $this->row($queryData);

        if(!empty($stockTrans)):
            $queryData = array();
            $queryData['tableName'] = $this->firMaster;
            $queryData['where']['ref_id'] = $id;
            $firData = $this->row($queryData);

            if(!empty($firData) && !empty($firData->accepted_by)):
                return ['status'=>0,'message'=>'Final Inspection in-process you can not delete it.'];
            endif;

            $setData = Array();
            $setData['tableName'] = $this->jobCard;
            $setData['where']['id'] = $stockTrans->ref_id;
            $setData['set']['unstored_qty'] = 'unstored_qty, + '.$stockTrans->qty;
            $this->setValue($setData);
            
            $setData = array();
            $setData['tableName'] = $this->itemMaster;
            $setData['where']['id'] = $stockTrans->item_id;
            $setData['set']['qty'] = 'qty, - '.$stockTrans->qty;
            $this->setValue($setData);

            $setData = array();
            $setData['tableName'] = $this->productionApproval;
            $setData['where']['id'] = $stockTrans->trans_ref_id;
            $setData['set']['out_qty'] = 'out_qty, - '.$stockTrans->qty;
            $this->setValue($setData);

            $unstored_qty = $this->jobcard->getJobCard($stockTrans->ref_id)->unstored_qty;
            $this->remove($this->stockTransaction,['id'=>$id]);
            $this->trash($this->firMaster,['ref_id'=>$id]);
            return ['status'=>1,'message'=>'Stock Transaction deleted successfully.','htmlData'=>$this->getStoreLocationTrans($stockTrans->ref_id)['htmlData'],'unstored_qty'=>$unstored_qty];
        else:
            return ['status'=>0,'message'=>'Stock transaction already deleted.'];
        endif;    
    }
}
?>