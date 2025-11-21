<?php
class JobcardModel extends MasterModel{
    private $jobCard = "job_card";
    private $jobBom = "job_bom";
    private $productionApproval = "job_approval";

    private $transMain = "trans_main";
    private $productKit = "item_kit";
    private $productProcess = "product_process";    
    private $purchaseTrans = "purchase_invoice_transaction";
    private $jobMaterialDispatch = "job_material_dispatch";
    private $jobOutWard = "job_outward";
    private $itemMaster = "item_master";
    private $jobUsedMaterial = "job_used_material";
    private $jobReturnMaterial = "job_return_material";
    private $stockTrans = "stock_transaction";

    public function getNextJobNo($job_type = 0){
        $data['tableName'] = $this->jobCard;
        $data['select'] = "MAX(job_no) as job_no";
        $data['where']['job_category'] = $job_type;
        $maxNo = $this->specificRow($data)->job_no;
		$nextJobNo = (!empty($maxNo))?($maxNo + 1):1;
		return $nextJobNo;
    }

    public function getNextBatchNo(){
        $data['tableName'] = $this->jobCard;
        $data['select'] = "MAX(batch_no) as batch_no";
        $maxNo = $this->specificRow($data)->batch_no;
		$nextBatchNo = (!empty($maxNo))?($maxNo + 1):1;
		return $nextBatchNo;
    }

    public function getDTRows($data,$type=0){        
        $data['tableName'] = $this->jobCard;
        $data['select'] = "job_card.*,item_master.item_name,item_master.item_code,party_master.party_name,party_master.party_code";
        $data['join']['item_master'] = "item_master.id = job_card.product_id";
        $data['leftJoin']['party_master'] = "job_card.party_id = party_master.id";
        $data['where']['job_card.job_category'] = $type;
        $data['order_by']['job_card.job_date'] = "DESC";
        $data['order_by']['job_card.id'] = "DESC";

         $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";
        $data['searchCol'][] = "DATE_FORMAT(job_card.job_date,'%d-%m-%Y')";
        $data['searchCol'][] = "DATE_FORMAT(job_card.delivery_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_code";
        $data['searchCol'][] = "job_card.challan_no";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "job_card.qty";
        $data['searchCol'][] = "job_card.remark";

		$columns =array('','','job_card.job_no','job_card.job_date','job_card.delivery_date','','job_card.party_code','job_card.challan_no','item_master.item_code','job_card.qty','','job_card.remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
    }

    public function getCustomerList(){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.party_id,party_master.party_name,party_master.party_code";
        $data['join']['party_master'] = "party_master.id = trans_main.party_id";
        $data['where']['trans_main.trans_status'] = 0;
        $data['where']['trans_main.entry_type'] = 4;
        $data['group_by'][] = 'trans_main.party_id';
        return $this->rows($data);
    }

    public function getCustomerSalesOrder($party_id){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.id,trans_main.trans_no,trans_main.trans_prefix,trans_main.trans_date";
        $data['where']['party_id'] = $party_id;
        $data['where']['trans_status'] = 0;
        $data['where']['entry_type'] = 4;
        return $this->rows($data);
    }

    public function getProductList($data){
        $html = '<option value="">Select Product</option>';$trans_date = '';
        if(empty($data['sales_order_id'])):
            $productData = $this->item->getItemList(1);
            if(!empty($productData)):
                foreach($productData as $row):
                    $selected = (!empty($data['product_id']) && $data['product_id'] == $row->id)?"selected":"";
                    $html .= '<option value="'.$row->id.'" data-delivery_date="'.date("Y-m-d").'" data-order_type="0" '.$selected.'>'.$row->item_code.'</option>';
                endforeach;
            endif;
        else:
            //trans_child
            $queryData['select'] = "trans_child.item_id,trans_child.qty,trans_child.cod_date,trans_main.trans_date,trans_main.order_type,item_master.item_code, item_master.item_name";
            $queryData['join']['item_master'] = "trans_child.item_id = item_master.id";
            $queryData['join']['trans_main'] = "trans_child.trans_main_id = trans_main.id";
            $queryData['where']['trans_child.trans_main_id'] = $data['sales_order_id'];
            $queryData['where']['trans_child.trans_status'] = 0;
            $queryData['where']['trans_child.entry_type'] = 4;
            $queryData['tableName'] = "trans_child";
            $productData = $this->rows($queryData);
            if(!empty($productData)):
                foreach($productData as $row):
                    $selected = (!empty($data['product_id']) && $data['product_id'] == $row->item_id)?"selected":"";
                    $jobType = ($row->order_type == 1)?0:1;
                    $html .= '<option value="'.$row->item_id.'" data-delivery_date="'.((!empty($row->cod_date))?$row->cod_date:date("Y-m-d")).'" data-order_type="'.$jobType.'" '.$selected.'>'.$row->item_code.'(Ord. Qty. : '.$row->qty.')</option>';
					$trans_date = (!empty($row->trans_date)) ? $row->trans_date : '';
                endforeach;
            endif;
        endif;
        return ['status'=>1,'htmlData'=>$html,'productData'=>$productData,'trans_date'=>$trans_date];
    }

    public function getProductProcess($data,$id=""){
        $jobCardData = array();
        if(!empty($id)):
            $jobCardData = $this->jobcard->getJobcard($id);
        endif;

        $data['select'] = "product_process.process_id,process_master.process_name,product_process.sequence";
        $data['where']['product_process.item_id'] = $data['product_id'];
        $data['join']['process_master'] = "product_process.process_id = process_master.id";
        $data['order_by']['product_process.sequence']  = 'asc';
        $data['tableName'] = $this->productProcess;
        $processData = $this->rows($data);
        $html = "";
        if(!empty($processData)):
            $i=1;
            foreach($processData as $row):
                if(!empty($jobCardData)):
                    $process = explode(",",$jobCardData->process);
                    $checked = (in_array($row->process_id,$process))?"checked":"";
                    $html .= '<input type="checkbox" id="md_checkbox_'.$i.'" name="process[]" class="filled-in chk-col-success" value="'.$row->process_id.'" '.$checked.' ><label for="md_checkbox_'.$i.'" class="mr-3">'.$row->process_name.'</label>';
                else:
                    $html .= '<input type="checkbox" id="md_checkbox_'.$i.'" name="process[]" class="filled-in chk-col-success" value="'.$row->process_id.'" checked ><label for="md_checkbox_'.$i.'" class="mr-3">'.$row->process_name.'</label>';
                endif;
                $i++;
            endforeach;
        else:
            $html = '<div class="error">Product Process not found.</div>';
        endif;
        return ['htmlData'=>$html,'processData'=>$processData];
    } 

    public function save($data){  
        $jobCardData = array();  
        if(!empty($data['id'])):
            $jobCardData = $this->getJobCard($data['id']);
            if(!empty($jobCardData->md_status) && empty($jobCardData->ref_id)):
                return ['status'=>2,'message'=>"Production In-Process. You can't update this job card."];
            endif;

            if(!empty($jobCardData->ref_id) && !empty($jobCardData->order_status)):
                return ['status'=>2,'message'=>"Production In-Process. You can't update this job card."];
            endif;

        else:
            $data['job_prefix'] = ($data['job_category'] == 0) ? "JOB/".$this->shortYear.'/' : "JOBW/".$this->shortYear.'/';
            $data['job_no'] = $this->getNextJobNo($data['job_category']);
        endif;
        $data['process'] = implode(',',$data['process']);  
        $saveJobCard = $this->store($this->jobCard,$data,'Job Card');

        //set job bom
        if(empty($data['id'])):
            $queryData = array();
            $queryData['tableName'] = $this->productKit;
            $queryData['where']['item_kit.item_id'] = $data['product_id'];
            $queryData['where']['item_kit.kit_type'] = 0;
            $kitData = $this->rows($queryData);
            foreach($kitData as $kit):
                $kit->id = "";
                $kit->job_card_id = $saveJobCard['insert_id'];
                $kit->created_by = $data['created_by'];
                $jobBomArray = (Array) $kit;
                $this->store($this->jobBom,$jobBomArray,'Job BOM');
            endforeach;
        else:
            if($data['product_id'] != $jobCardData->product_id):
                $this->trash("job_bom",['job_card_id'=>$data['id']]);
                $queryData = array();
                $queryData['tableName'] = $this->productKit;
                $queryData['where']['item_kit.item_id'] = $data['product_id'];
                $queryData['where']['item_kit.kit_type'] = 0;
                $kitData = $this->rows($queryData);
                foreach($kitData as $kit):
                    $kit->id = "";
                    $kit->job_card_id = $data['id'];
                    $kit->created_by = $data['created_by'];
                    $jobBomArray = (Array) $kit;
                    $this->store($this->jobBom,$jobBomArray,'Job BOM');
                endforeach;
            endif;
        endif;
        
        return $saveJobCard;
    }

    public function getJobcard($id){
        $data['tableName'] = $this->jobCard;
        $data['select'] = 'job_card.*,item_master.item_code,item_master.item_name';
        $data['join']['item_master'] = 'item_master.id = job_card.product_id';
        $data['where']['job_card.id'] = $id;
        return $this->row($data); 
    }

    public function getJobcardList($order_status =[0,1,2]){
        $data['tableName'] = $this->jobCard;
        $data['where_in']['order_status'] = $order_status;
        return $this->rows($data); 
    }

    public function jobCardNoList(){
        $data['tableName'] = $this->jobCard;
        $data['select'] = 'job_card.id,job_card.job_no,job_card.job_prefix,job_card.job_date,item_master.item_code,item_master.item_name';
        $data['join']['item_master'] = 'item_master.id = job_card.product_id';
        $data['where']['job_card.order_status != '] = 5;
        return $this->rows($data); 
    }

    public function delete($id){
        $jobCardData = $this->getJobCard($id);
        if(!empty($jobCardData->md_status) && empty($jobCardData->ref_id)):
            return ['status'=>0,'message'=>"Production In-Process. You can't Delete this job card."];
        endif;

        if(!empty($jobCardData->ref_id) && !empty($jobCardData->order_status)):
            return ['status'=>0,'message'=>"Production In-Process. You can't Delete this job card."];
        endif;

        $this->trash($this->jobMaterialDispatch,['job_card_id'=>$id,'is_delete'=>0]);
        $this->trash($this->jobBom,['job_card_id'=>$id]);
        return $this->trash($this->jobCard,['id'=>$id],"Job Card");
    }

    public function getProcessWiseRequiredMaterial($data){
        $queryData['tableName'] = $this->jobBom;
        $queryData['select'] = "job_bom.id,job_bom.ref_item_id,job_bom.qty,job_bom.process_id,item_master.item_name,item_master.item_code,item_master.qty as stockQty,item_master.item_type";
        $queryData['join']['item_master'] = "job_bom.ref_item_id = item_master.id";
        $queryData['where']['item_id'] = $data->product_id;
        $queryData['where']['job_card_id'] = $data->id;
        $kitData = $this->rows($queryData);
            
        $resultData = array();
        if(!empty($kitData)):
          $i=1;$html="";
          foreach($kitData as $row):
            $issueQty=0;$inQty=0;
            $queryData = array();
            $queryData['tableName'] = $this->jobMaterialDispatch;
            $queryData['select'] = "SUM(dispatch_qty) as issue_qty";
            $queryData['where']['job_card_id'] = $data->id;
            $queryData['where']['dispatch_item_id'] = $row->ref_item_id;
            $queryData['where']['is_delete'] = 0;
            $issueQty = $this->row($queryData)->issue_qty;

            $queryData = array();
            $queryData['tableName'] = $this->jobReturnMaterial;
            $queryData['select'] = "SUM(qty) as return_qty";
            $queryData['where']['job_card_id'] = $data->id;
            $queryData['where']['item_id'] = $row->ref_item_id;
            $queryData['where']['type'] = 1;
            $queryData['where']['is_delete'] = 0;
            $returnQty = $this->row($queryData)->return_qty;
            
            $queryData = array();
            $queryData['tableName'] = $this->productionApproval;
            $queryData['select'] = "SUM(out_qty) as in_qty";
            $queryData['where']['job_card_id'] = $data->id;
            $queryData['where']['in_process_id'] = 0;
            $queryData['where']['trans_type'] = 0;
            $queryData['where']['is_delete'] = 0;
            $in_qty = $this->row($queryData)->in_qty;

            $issueQty = ((!empty($issueQty))?$issueQty:0);
            $inQty = ((!empty($in_qty))?($row->qty*$in_qty):0);
            $pendingQty = ($issueQty - $returnQty) - $inQty;
            $firstProcess = explode(",",$data->process)[0];
            $productName = $this->item->getItem($data->product_id)->item_name;
            $processName = $this->process->getProcess($firstProcess)->process_name;

            $button = "";
            if($issueQty > ($row->qty*$in_qty)):                
                $button = '<a class="btn btn-outline-warning getForward" href="javascript:void(0)" datatip="Retirn Material" flow="left" data-ref_id="0" data-product_id="'.$data->product_id.'" data-in_process_id="'.$firstProcess.'" data-job_card_id="'.$data->id.'" data-product_name="'.$productName.'" data-process_name="'.$processName.'" data-pending_qty="'.$pendingQty.'" data-item_name="'.$row->item_name.'" data-item_id="'.$row->ref_item_id.'" data-toggle="modal" data-target="#returnMaterial"><i class="fas fa-reply" ></i></a>';
            endif;

            $deleteBtn = "";
            if($data->job_order_status == 0 && $row->item_type == 3 && empty($issueQty)):
                $deleteBtn = '<a class="btn btn-outline-danger" href="javascript:void(0)" datatip="Delete" flow="left" onclick="removeBomItem('.$row->id.','.$data->id.')"><i class="ti-trash"></i></a>';
            endif;

            /* $action = getActionButton($button); */

            $html .= '<tr class="text-center">
                        <td>'.$i++.'</td>
                        <td class="text-left">'.$row->item_name.'</td>
                        <td>'.$row->qty.'</td>
                        <td>'.($row->qty*$data->qty).'</td>
                        <td>'.$issueQty.'</td>
                        <td>'.$inQty.'</td>
                        <td>'.$pendingQty.'</td>
                        <td>'.$button.$deleteBtn.'</td>
                      </tr>';
            $resultData[] = ['item_id'=>$row->ref_item_id,'item_name'=>$row->item_name,'bom_qty'=>$row->qty,'req_qty'=>($row->qty*$data->qty),'issue_qty'=>$issueQty,'pending_qty'=>$pendingQty];
          endforeach;
          $result = $html;
        else:
          $result = '<tr><td colspan="8" class="text-center">No result found.</td></tr>';
        endif;
    
        return ['status'=>1,'message'=>'Data Found.','result'=>$result,"resultData"=>$resultData];
    }

    public function getRequestItemData($id,$process_id = 0){
        $jobCardData = $this->getJobcard($id);

        $queryData['tableName'] = $this->jobBom;
        $queryData['select'] = "job_bom.*,item_master.item_name,item_master.item_type,item_master.qty as stock_qty,unit_master.unit_name";
        $queryData['join']['item_master'] = "job_bom.ref_item_id = item_master.id";
        $queryData['join']['unit_master'] = "unit_master.id = item_master.unit_id";
        $queryData['where']['job_bom.item_id'] = $jobCardData->product_id;
        $queryData['where']['job_bom.process_id'] = $process_id;
        $queryData['where']['job_bom.job_card_id'] = $id;
        $kitData = $this->rows($queryData);

        $dataRows = array();
        foreach($kitData as $row):            
            $row->request_qty = $row->qty * $jobCardData->qty;
            $dataRows[] = $row;
        endforeach;

        return $dataRows;
    }

    public function saveMaterialRequest($data){
        $jobCardData = $this->getJobcard($data['job_id']);
        $i=1;$wpcs = 0;$totalWeight = 0;
        foreach($data['bom_item_id'] as $key=>$value):  
            $materialDispatchData = [
				'id' => "",
				'material_type' => $data['material_type'][$key],
				'job_card_id' => $data['job_id'],
				'req_date' => formatDate($data['req_date'],'Y-m-d'),
				'req_item_id' => $value,
				'req_qty' => $data['request_qty'][$key],
                'process_id' => explode(",",$jobCardData->process)[0],
                'machine_id' => $data['machine_id'],
				'created_by' => $data['created_by']
			];
			$this->store($this->jobMaterialDispatch,$materialDispatchData);
            if($data['material_type'][$key] == 1):
                if($i == 1):
                    $wpcs = $data['bom_qty'][$key];
                    $totalWeight = $data['request_qty'][$key];
                    $i++;
                endif;
            endif;
        endforeach;
        $jobData = [
            'w_pcs' => $wpcs,
            'total_weight' => $totalWeight,
            'md_status'=>1
        ];
        $this->edit($this->jobCard,['id'=>$data['job_id']],$jobData); 
        return ['status'=>1,'message'=>'Material Request send successfully.'];
    }

    public function changeJobStatus($data){
        if($data['order_status'] == 1):
            $jobData = $this->getJobcard($data['id']);
            if($jobData->md_status != 2):
                return ['status'=>0,'message'=>"Required Material is not issued yet! Please Issue material before start"];
            endif;
        endif;
        $this->store($this->jobCard,$data);
        $msg ="";
        if($data['order_status'] == 1){
            $this->sendJobApproval($data['id']);
			$msg = "Start";
		}else if($data['order_status'] == 3){
			$msg = "Hold";
		}else if($data['order_status'] == 2){
			$msg = "Restart";
		}else if($data['order_status'] == 5){
			$msg = "Close";
		}else if($data['order_status'] == 4){
			$msg = "Reopen";
		}
        return ['status'=>1,'message'=>"Job Card ".$msg." successfully."];
    }
	
	public function sendJobApproval($id){
        $jobCardData = $this->getJobcard($id);
        $processIds = explode(",",$jobCardData->process);
        $counter = count($processIds);
        for($i=0;$i<=$counter;$i++):
            $approvalData = [
                'id' => "",
                'entry_date' => date("Y-m-d"),
                'job_card_id' => $jobCardData->id,
                'product_id' => $jobCardData->product_id,
                'in_process_id' => ($i == 0)?0:$processIds[($i - 1)],
                'in_qty' => ($i == 0)?$jobCardData->qty:0,
                'in_w_pcs' => ($i == 0)?$jobCardData->w_pcs:0,
                'in_total_weight' => ($i == 0)?$jobCardData->total_weight:0,
                'out_process_id' => (isset($processIds[$i]))?$processIds[$i]:0,
                'created_by' => $jobCardData->created_by
            ];
            $this->store($this->productionApproval,$approvalData);
        endfor;        
        return true;
    }

    public function saveJobBomItem($postData){
        $result = $this->store("job_bom",$postData,'Bom Item');
        $jobData = $this->getJobcard($postData['job_card_id']);
        $jobData->job_order_status = $jobData->order_status;
        $result['result'] = $this->getProcessWiseRequiredMaterial($jobData)['result'];
        return $result;
    }

    public function deleteBomItem($id,$job_card_id){
        $result = $this->trash("job_bom",['id'=>$id],'Bom Item');
        $jobData = $this->getJobcard($job_card_id);
        $jobData->job_order_status = $jobData->order_status;
        $result['result'] = $this->getProcessWiseRequiredMaterial($jobData)['result'];
        return $result;
    }

    public function addJobStage($data){ 
		$saveJobCard = Array();
        if(!empty($data['id'])):
            $jobCardData = $this->getJobCard($data['id']);
			$process = explode(",",$jobCardData->process);
			$process[] = $data['process_id'];
			$newProcesses = implode(',',$process); 
			
			$saveJobCard = $this->store($this->jobCard,['id'=>$data['id'],'process'=>$newProcesses],'Job Card');

            $queryData = array();
            $queryData['tableName'] = $this->productionApproval;
            $queryData['where']['trans_type'] = 0;
            $queryData['where']['job_card_id'] = $data['id'];
            $queryData['order_by']['id'] = "DESC";
            $approvalData = $this->row($queryData);
            if(!empty($approvalData)):
                $this->store($this->productionApproval,['id'=>$approvalData->id,'out_process_id'=>$data['process_id']]);            
                $this->store($this->productionApproval,['id'=>"",'entry_date'=>date("Y-m-d"),'job_card_id'=>$data['id'],'product_id'=>$approvalData->product_id,'in_process_id'=>$data['process_id'],'out_process_id'=>0,'created_by'=>$data['created_by']]);                        
            endif;
        endif;
        return $this->getJobStages($data['id']);
    }

	public function updateJobProcessSequance($data){
		$saveJobCard = array();
        if(!empty($data['id'])):
			$newProcesses = $data['process_id']; 
			if(!empty($data['rnstages'])){
                $newProcesses = $data['rnstages'] .','. $data['process_id'];
            }
			$saveJobCard = $this->store($this->jobCard,['id'=>$data['id'],'process'=>$newProcesses],'Job Card');

            $queryData = array();
            $queryData['tableName'] = $this->productionApproval;
            $queryData['where']['trans_type'] = 0;
            $queryData['where']['job_card_id'] = $data['id'];
            $approvalData = $this->rows($queryData);
            if(!empty($approvalData)):
                $rnStage = explode(",",$data['rnstages']);
                $newProcessesStage = explode(",",$data['process_id']);
                $countRnStage = count($rnStage);
                $i=0;$j=0;$previusSatge=0;$previusSatgeId=0;
                /* print_r($newProcessesStage); */
                foreach($approvalData as $row):
                    //print_r($row->in_process_id."-");
                    if($i > $previusSatge):                        
                        /* print_r(['id'=>$row->id,'in_process_id'=>$previusSatgeId,'out_process_id'=>(isset($newProcessesStage[$i]))?$newProcessesStage[$i]:0]);
                        print_r("---"); */
                        $this->store($this->productionApproval,['id'=>$row->id,'in_process_id'=>$previusSatgeId,'out_process_id'=>(isset($newProcessesStage[$i]))?$newProcessesStage[$i]:0]);
                        $previusSatgeId = (isset($newProcessesStage[$i]))?$newProcessesStage[$i]:0;
                        $previusSatge = $i;
                        $i++;
                    endif;
                    if($row->in_process_id == $rnStage[($countRnStage-1)]):
                        /* print_r(['id'=>$row->id,'out_process_id'=>$newProcessesStage[$i]]);
                        print_r("---"); */
                        $this->store($this->productionApproval,['id'=>$row->id,'out_process_id'=>$newProcessesStage[$i]]);  
                        $previusSatgeId = $newProcessesStage[$i];
                        $previusSatge = $i;                      
                        $i++;
                    endif;                    
                endforeach;
            endif;
        endif;
        return $this->getJobStages($data['id']);
	}

    public function removeJobStage($data){ 
		$saveJobCard = Array();
        if(!empty($data['id'])):
            $jobCardData = $this->getJobCard($data['id']);
			$process = explode(",",$jobCardData->process);
			$updateProcesses = Array();
			foreach ($process as $pid){if($pid != $data['process_id']){$updateProcesses[] = $pid;}}
			$newProcesses = implode(',',$updateProcesses); 
			
			$saveJobCard = $this->store($this->jobCard,['id'=>$data['id'],'process'=>$newProcesses],'Job Card');
        endif;
        return $this->getJobStages($data['id']);
    }
	
	public function getJobStages($job_id){
		$stageRows="";$pOptions='<option value="">Select Stage</option>';
		$jobCardData = $this->getJobCard($job_id);
		$process = explode(",",$jobCardData->process);
		
		if (!empty($process)) :
			$i = 0;$inQty = 0;
			foreach ($process as $pid) :
				$process_name = (!empty($pid))?$this->process->getProcess($pid)->process_name:"Initial Stage";
				$jobProcessData = $this->production->getProcessWiseProduction($job_id,$pid,0);
                $inQty = (!empty($jobProcessData))?$jobProcessData->in_qty:0;
				if($inQty <= 0 and $i > 0):
					$stageRows .= '<tr id="' . $pid . '">
									<td class="text-center">' . $i . '</td>
									<td>' . $process_name . '</td>
									<td class="text-center">' . ($i+1) . '</td>
									<td class="text-center">
										<button type="button" data-pid="'.$pid.'" class="btn btn-outline-danger waves-effect waves-light removeJobStage"><i class="ti-trash"></i></button>
									</td>
								  </tr>';
				endif;$i++;
			endforeach;
		else :
			$stageRows .= '<tr><td colspan="3" class="text-center">No Data Found.</td></tr>';
		endif;
		$processDataList = $this->process->getProcessList();
		foreach ($processDataList as $row):
			if(!empty($process) && (!in_array($row->id, $process))):
				$pOptions .= '<option value="' . $row->id . '">' . $row->process_name . '</option>';
			endif;
		endforeach;
		
		return [$stageRows,$pOptions];
	}

    public function getBatchNoForReturnMaterial($job_id,$item_id){
        $queryData = array();$dispatchIds = array();
        $queryData['tableName'] = $this->jobMaterialDispatch;
        $queryData['select'] = "id";
        $queryData['where']['dispatch_item_id'] = $item_id;
        $queryData['where']['job_card_id'] = $job_id;
        $dispatchIds = $this->rows($queryData);
        $issueIds=array();

        $options = '<option value="">Select Batch No.</option>';
        if(!empty($dispatchIds)):
            foreach($dispatchIds as $row):
                $issueIds[] = $row->id; 
            endforeach;

            $queryData = array();
            $queryData['tableName'] = "stock_transaction";
            $queryData['select'] = "batch_no";
            $queryData['where']['trans_type'] = 2;
            $queryData['where']['item_id'] = $item_id;
            $queryData['where']['ref_type'] = 3;
            $queryData['where_in']['ref_id'] = $issueIds;
            $queryData['group_by'][] = "batch_no";
            $batchNoList = $this->rows($queryData);

            
            foreach($batchNoList as $row):
                $options .= '<option value="'.$row->batch_no.'">'.$row->batch_no.'</option>';
            endforeach;
        endif;

        return ['status'=>1,'options'=>$options];
    }
    
    public function saveScrapWeight($data){
        return $this->store($this->jobCard, $data, 'Scrap Weight');
    }

    public function saveScrape($data){
        return $this->store($this->stockTrans,$data,'');
    }

    public function deleteScrape($id){
        return $this->remove($this->stockTrans,['ref_id'=>$id,'trans_type'=>1,'ref_type'=>13]);
    }

    public function materialReceived($data){
        $jobCardData = $this->getJobcard($data['id']);
        if($jobCardData->md_status != 2):
            return ['status'=>0,'message'=>'Job Material has been not dispatch from store.'];
        endif;

        $this->store($this->jobCard,$data);
        return ['status'=>1,'message'=>"Material received successfully."];
    }
}
?>