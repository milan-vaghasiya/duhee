<?php
class ItemModel extends MasterModel{
    private $itemMaster = "item_master";
	private $itemKit = "item_kit";
	private $productProcess = "product_process";
	private $processMaster = "process_master";
    private $unitMaster = "unit_master";
    private $itemCategory = "item_category";
    private $openingStockTrans = "stock_transaction";
    private $productionOperation = "production_operation";
    private $inspectionParam = "inspection_param";
	private $hsnMaster = "hsn_master";
	private $itemClass = "item_class";
	private $familyGroup = "family_group";
	private $production_output = "production_output";
	private $controlPlan = "control_plan";
    private $calibration = "calibration";
	private $prod_process_doc = "prod_process_doc";

    public function getDTRows($data,$type=0){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "item_master.*,unit_master.unit_name,item_category.category_name,hsn_master.description as hsnDetail,st.stock_qty";
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $data['leftJoin']['hsn_master'] = "hsn_master.hsn = item_master.hsn_code";
		$data['leftJoin']['( SELECT SUM(qty) as stock_qty,item_id FROM stock_transaction WHERE  is_delete = 0 AND stock_effect = 1 AND location_id !='.$this->SCRAP_STORE->id.' GROUP BY item_id) as st'] = "st.item_id = item_master.id";

        $data['where']['item_master.item_type'] = $type;
        //$data['order_by']['item_master.full_name'] = 'ASC';
        //$data['order_by']['item_category.category_name'] = 'ASC';
		
		$data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "item_master.hsn_code";
        $data['searchCol'][] = "unit_master.unit_name";
        $data['searchCol'][] = "item_master.qty";
        $data['searchCol'][] = "item_master.opening_qty";
		
		$columns = array('','','item_master.item_code','item_master.full_name','item_master.hsn_code','item_master.opening_qty','item_master.qty','');
		if(isset($data['order'])){ $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir']; }
        $result = $this->pagingRows($data);
		return $result;
    }

	public function getProdOptDTRows($data,$type=0){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "item_master.*,unit_master.unit_name,item_category.category_name,party_master.party_code";
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $data['leftJoin']['party_master'] = "party_master.id = item_master.party_id";
        $data['where_in']['item_master.item_type'] = $type;
        if($type == 1 && isset($data['is_child'])){
			$data['where']['item_master.prev_maint_req'] = $data['is_child'];
		}
		
        $columns = array();
        if($type == 1){
        	$data['searchCol'][] = "";
            $data['searchCol'][] = "item_master.item_code";
        	$data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
    		
        	$columns =array('','item_master.item_code','','','','','','');
        } else {
            $data['searchCol'][] = "";
            $data['searchCol'][] = "item_master.full_name";
        	$data['searchCol'][] = "";
    		
        	$columns =array('','item_master.full_name','');
        }
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

	public function getItemList($type=0){
		$data['tableName'] = $this->itemMaster;
	    $data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.full_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price,item_master.unit_id,item_master.qty,item_master.rev_no,item_master.app_rev_no,item_master.application_industry,item_master.drawing_no,item_master.part_no,unit_master.unit_name,item_master.wt_pcs,item_master.material_grade";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";

		if(!empty($type))
			$data['where']['item_master.item_type'] = $type;
		return $this->rows($data);
	}
	
    public function getItemLists($type="0",$category_id =""){
		$data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.*,unit_master.unit_name,item_category.auth_detail";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";

		if(!empty($type) and $type != "0")
			$data['where_in']['item_master.item_type'] = $type;

		if(!empty($tcategory_idype) and $category_id != "0")
			$data['where_in']['item_master.category_id'] = $category_id;
		return $this->rows($data);
	}
	
	public function getProductKitLists(){
	    $data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.*,unit_master.unit_name,item_category.auth_detail";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
		//$data['where_in']['item_master.item_type'] = '1,3';
	    $data['customWhere'][] = '((item_master.item_type = 1 AND item_master.fg_id = 1) OR item_master.item_type = 3 OR item_master.item_type = 10)';
		return $this->rows($data);
	}

	public function locationWiseBatchStock($item_id,$location_id){
		$data['tableName'] = "stock_transaction";
		$data['select'] = "SUM(qty) as qty,batch_no,ref_batch";
		$data['where']['item_id'] = $item_id;
		$data['where']['location_id'] = $location_id;
		$data['order_by']['id'] = "asc";
		$data['group_by'][] = "batch_no";
		$data['group_by'][] = "ref_batch";
		return $this->rows($data);
	}

    public function getItemById($id){
        $data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.*,item_category.category_name,unit_master.unit_name";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
		$data['where']['item_master.id'] = $id;
        return $this->row($data);
    }

    public function getItem($id){
        $data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.*,item_category.category_name,unit_master.unit_name,st.stock_qty";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
		$data['leftJoin']['( SELECT SUM(qty) as stock_qty,item_id FROM stock_transaction WHERE item_id = '.$id.' AND is_delete = 0 AND stock_effect = 1 GROUP BY item_id) as st'] = "st.item_id = item_master.id";
        $data['where']['item_master.id'] = $id;
        return $this->row($data);
    }

    public function getItemByPartNo($part_no){
        $data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.id,item_master.full_name,item_master.rev_no,party_master.party_name,party_master.vendor_code";
		$data['leftJoin']['party_master'] = "party_master.id = item_master.party_id";
		$data['where']['item_master.part_no'] = $part_no;
        return $this->row($data);
    }
	
    public function getItemBySelect($id,$select){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = $select;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function itemUnits(){
        $data['tableName'] = $this->unitMaster;
		return $this->rows($data);
	}

	public function itemUnit($id){
        $data['tableName'] = $this->unitMaster;
		$data['where']['id'] = $id;
		return $this->row($data);
	}
	
	public function getItemClass(){
        $data['tableName'] = $this->itemClass;
		return $this->rows($data);
	}

	public function getHsnList(){
        $data['tableName'] = $this->hsnMaster;
		return $this->rows($data);
	}

	public function getHsnData($hsnCode){
		$data['tableName'] = $this->hsnMaster;
		$data['where']['hsn'] = $hsnCode;
		return $this->row($data);
	}

	public function getOpeningRawMaterialList(){
		$data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.*,unit_master.unit_name";
        $data['join']['unit_master'] = "unit_master.id = item_master.unit_id";
		$data['where']['item_master.item_type'] = 1;
		$data['where']['item_master.opening_remaining_qty != '] = "0.000";
		return $this->rows($data);
	}

    public function save($data){
		$process = array();$itmId = 0;
		$msg = ($data['item_type'] == 0)?"Item":"Part";
        if($this->checkDuplicate($data['item_name'],$data['item_code'],$data['item_type'],$data['id']) > 0):
            $errorMessage['item_name'] =  $msg." Name is duplicate.";
            return ['status'=>0,'message'=>$errorMessage];
		else:
			if(!empty($data['process_id'])):
				$process = explode(',',$data['process_id']);
			endif;
			unset($data['process_id']);
            $mgsName = ($data['item_type'] == 0)?"Item":"Product";
			$result = $this->store($this->itemMaster,$data,$mgsName);
			$itmId = (empty($data['id'])) ? $result['insert_id'] : $data['id'];

			if(!empty($process) AND !empty($itmId)):
				$ppData = ["item_id"=>$itmId,"process"=>$process,"created_by"=>$data['created_by']];
            	$ppResult = $this->saveProductProcess($ppData);
			endif;	
			return $result;
        endif;
    }

    public function checkDuplicate($name,$item_code,$type,$id=""){
        $data['tableName'] = $this->itemMaster;
        $data['where']['item_name'] = $name;
        $data['where']['item_type'] = $type;
        if(!empty($item_code)){$data['where']['item_code'] = $item_code;}
        if(!empty($id))
            $data['where']['id !='] = $id;

        return $this->numRows($data);
    }

    public function delete($id){
		$itemData = $this->getItem($id);
		$mgsName = ($itemData->item_type == 0)?"Item":"Product";
        return $this->trash($this->itemMaster,['id'=>$id],$mgsName);
    }
	
	public function getCategoryList($type=0){
		$data['where']['category_type'] = $type;
		$data['where']['final_category'] = 1;
        $data['tableName'] = $this->itemCategory;
        return $this->rows($data);
    }

	public function getfamilyGroupList(){
        $data['tableName'] = $this->familyGroup;
		$data['where']['type'] = 1;
        return $this->rows($data);
    }
    
	public function getItemGroup(){
        $data['tableName'] = 'item_group';
        return $this->rows($data);
    }
    
    public function getItemGroupById($id){
        $data['tableName'] = 'item_group';
		$data['where']['id'] = $id;
        return $this->row($data);
    }

	public function getProductProcessForSelect($id){
		$data['select'] = "process_id";
		$data['where']['item_id'] = $id;
		$data['tableName'] = $this->productProcess;
		$result = $this->rows($data);
		$process = array();
		if($result){foreach($result as $row){$process[] = $row->process_id;}}
		return $process;
	}
	
	public function getProductOperationForSelect($id){
		$data['select'] = "operation";
		$data['where']['id'] = $id;
		$data['tableName'] = $this->productProcess;
		$result = $this->row($data);
		return $result->operation;
	}
	
	public function getProductProcess($id){
		$data['select'] = "process_id";
		$data['where']['item_id'] = $id;
		$data['tableName'] = $this->productProcess;
		return $this->rows($data);
	}

	public function saveProductProcess($data){
		$queryData['select'] = "process_id,id,sequence";
		$queryData['where']['item_id'] = $data['item_id'];
		$queryData['tableName'] = $this->productProcess;
		$process_ids =  $this->rows($queryData);

		$process = '';
		if(!empty($data['process_id'])):
			$process = explode(',',$data['process_id']);
		endif;
		$z=0;
		foreach($process_ids as $key=>$value):
			if(!in_array($value->process_id,$process)):
			
				$upProcess['tableName'] = $this->productProcess;
				$upProcess['where']['item_id']=$data['item_id'];
				$upProcess['where']['sequence > ']=($value->sequence - $z++);
				$upProcess['where']['is_delete']=0;
				$upProcess['set']['sequence']='sequence, - 1';
				$q = $this->setValue($upProcess);
				$this->remove($this->productProcess,['id'=>$value->id],'');
			endif;
		endforeach;
		foreach($process as $key=>$value):			
			if(!in_array($value,array_column($process_ids,'process_id'))):
				$queryData = array();
				$queryData['select'] = "MAX(sequence) as value";
				$queryData['where']['item_id'] = $data['item_id'];
				$queryData['where']['is_delete'] = 0;
				$queryData['tableName'] = $this->productProcess;
				$sequence = $this->specificRow($queryData)->value;
				
				$productProcessData = [
					'id'=>"",
					'item_id'=>$data['item_id'],
					'process_id'=>$value,
					'sequence'=>(!empty($sequence))?($sequence + 1):1,
					'created_by' => $this->session->userdata('loginId')
				];
				$this->store($this->productProcess,$productProcessData,'');
			endif;
		endforeach;


		return ['status'=>1,'message'=>'Product process saved successfully.'];
	}

	public function saveProductProcessCycleTime($data){ 
		foreach($data['id'] as $key=>$value):
			$process_time = (!empty($data['process_time'][$key])) ? $data['process_time'][$key] : 0;
			$load_unload_time = (!empty($data['load_unload_time'][$key])) ? $data['load_unload_time'][$key] : 0;
			$cycle_time =  $process_time  + $load_unload_time;
			
			$productProcessData = [
				'id'=>$value,
				'process_time'=> $process_time ,
				'load_unload_time'=> $load_unload_time,
				'cycle_time'=> $cycle_time,
				'finished_weight'=> (!empty($data['finished_weight'][$key])) ? $data['finished_weight'][$key] : 0
			];
			$this->store($this->productProcess,$productProcessData,'');
		endforeach;
		return ['status'=>1,'message'=>'Cycle Time Updated successfully.'];
	}

	public function getItemProcess($id){
		$data['tableName'] = $this->productProcess;
		$data['select'] = "product_process.*,process_master.process_name";
		$data['join']['process_master'] = "process_master.id = product_process.process_id";
		$data['where']['product_process.item_id'] = $id;
		$data['order_by']['product_process.id'] = "ASC";
		$data['order_by']['product_process.sequence'] = "ASC";
		return $this->rows($data);
	}

	public function getProductProcessBySequence($product_id,$sequence){
		$data['tableName'] = $this->productProcess;
		$data['select'] = "product_process.*,process_master.process_name";
		$data['join']['process_master'] = "process_master.id = product_process.process_id";
		$data['where']['product_process.item_id'] = $product_id;
		$data['where']['product_process.sequence'] = $sequence;
		return $this->row($data);
	}

	public function updateProductProcessSequance($data){
		$ids = explode(',', $data['id']);
		$i=1;
		foreach($ids as $pp_id):
			$seqData=Array("sequence"=>$i++);
			$this->edit($this->productProcess,['id'=>$pp_id],$seqData);
		endforeach;

		$queryData['tableName'] = $this->productProcess;
		$queryData['where']['id'] = $ids[0];
		$queryData['order_by']['sequence'] = "ASC";		
		$productProcessRow = $this->row($queryData);
		$this->edit($this->itemKit,['item_id'=>$productProcessRow->item_id,'kit_type'=>0],['process_id'=>$productProcessRow->process_id]);
		
		return ['status'=>1,'message'=>'Process Sequence updated successfully.'];
	}

	public function getProductKitData($id){
		$data['select'] = "item_kit.*,item_master.item_name,item_master.full_name,process_master.process_name,item_master.item_type";
		$data['leftJoin']['item_master'] = "item_master.id = item_kit.ref_item_id";
		$data['leftJoin']['process_master'] = "process_master.id = item_kit.process_id";
		$data['where']['item_kit.item_id'] = $id;
		$data['where']['item_kit.kit_type'] = 0;
		$data['tableName'] = $this->itemKit;
		return $this->rows($data);
	}

	public function getProductKitOnProcessData($id,$processId){
		$data['select'] = "item_kit.*,item_master.item_name";
		$data['join']['item_master'] = "item_master.id = item_kit.ref_item_id";
		$data['where']['item_kit.item_id'] = $id;
		$data['where']['item_kit.process_id'] = $processId;
		$data['where']['item_kit.kit_type'] = 0;
		$data['tableName'] = $this->itemKit;
		return $this->rows($data);
	}

	public function saveProductKit($data){
		$kitData = $this->getProductKitData($data['item_id']);
		foreach($data['ref_item_id'] as $key=>$value):
			if(empty($data['id'][$key])):
				$itemKitData = ['id'=>"",'item_id'=>$data['item_id'],'ref_item_id'=>$value,'qty'=>$data['qty'][$key],'process_id'=>$data['process_id'][$key]];
				$this->store($this->itemKit,$itemKitData);
			else:
				$where['process_id'] = $data['process_id'][$key];
				$where['item_id'] = $data['item_id'];
				$where['kit_type'] = 0;
				$where['id'] = $data['id'][$key];
				$this->edit($this->itemKit,$where,['qty'=>$data['qty'][$key]]);
			endif;
		endforeach;
		if(!empty($kitData)):
			foreach($kitData as $key=>$value):
				if(!in_array($value->id,$data['id'])){
					$this->trash($this->itemKit,['id'=>$value->id,'kit_type'=>0],'');
				}
			endforeach;
		endif;
		return ['status'=>1,'message'=>'Product Kit Item saved successfully.'];
	}

	public function getProductWiseProcessList($product_id){
		$data['tableName'] = $this->productProcess;
		$data['select'] = "process_master.id,process_master.process_name";
		$data['join']['process_master'] = "process_master.id = product_process.process_id";
		$data['where']['product_process.item_id'] = $product_id;
		return $this->rows($data);
	}
	
	public function getItemOpeningTrans($id){
		$queryData['tableName'] = $this->openingStockTrans;
		$queryData['select'] = "stock_transaction.*,location_master.store_name,location_master.location,ifnull(party_master.party_name,'') as party_name,item_master.item_type";
		$queryData['leftJoin']['location_master'] = "stock_transaction.location_id = location_master.id";
		$queryData['leftJoin']['party_master'] = "party_master.id = stock_transaction.ref_id";
		$queryData['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
		$queryData['where']['stock_transaction.ref_type'] = "-1";
		//$queryData['where']['stock_transaction.ref_id'] = 0;
		$queryData['where']['stock_transaction.trans_type'] = 1;
		$queryData['where']['stock_transaction.item_id']  = $id;
		$openingStockTrans = $this->rows($queryData);
        //print_r($this->db->last_query());exit;
		$html = '';
		if(!empty($openingStockTrans)):
			$i=1;
			foreach($openingStockTrans as $row):
			    $supplierTd= '';
			    if(!empty($row->item_type) AND $row->item_type==3)
			    {
			        $supplierTd= '<td>'.$row->party_name.'</td><td>'.$row->ref_batch.'</td>';
			    }
				$html .= '<tr>
							<td>'.$i++.'</td>
							<td>[ '.$row->store_name.' ] '.$row->location.'</td>
							'.$supplierTd.'
							<td>'.$row->batch_no.'</td>
							<td>'.$row->qty.'</td>
							<td class="text-center">
								<div class="btn-group">
									<a href="javascript:void(0)" class="btn btn-outline-danger waves-effect waves-light" onclick="deleteOpeningStock('.$row->id.');" ><i class="ti-trash"></i></a>
								</div>
							</td>
						</tr>';
			endforeach;
		endif;
		return ['status'=>1,'htmlData'=>$html,'result'=>$openingStockTrans];
	}

	public function saveOpeningStock($data){
	    if(empty($data['batch_no'])){
	        unset($data['batch_no']);
	    }
			
	    $itemData = $this->item->getItem( $data['item_id']);
	    if($itemData->item_type == 9){
	        $data['location_id'] = $this->PACK_MTR_STORE->id;
	        unset($data['batch_no']);
	    }
		$this->store($this->openingStockTrans,$data);

		$setData = Array();
		$setData['tableName'] = $this->itemMaster;
		$setData['where']['id'] = $data['item_id'];
		$setData['set']['qty'] = 'qty, + '.$data['qty'];
		$setData['set']['opening_qty'] = 'opening_qty, + '.$data['qty'];
		$this->setValue($setData);

		return ['status'=>1,'message'=>'Opening Stock saved successfully.','transData'=>$this->getItemOpeningTrans($data['item_id'])['htmlData']];
	}

	public function deleteOpeningStockTrans($id){
		$queryData['tableName'] = $this->openingStockTrans;
		$queryData['where']['id'] = $id;
		$transData = $this->row($queryData);

		$setData = Array();
		$setData['tableName'] = $this->itemMaster;
		$setData['where']['id'] = $transData->item_id;
		$setData['set']['qty'] = 'qty, - '.$transData->qty;
		$setData['set']['opening_qty'] = 'opening_qty, - '.$transData->qty;
		$this->setValue($setData);

		$this->remove($this->openingStockTrans,['id'=>$id],"Opening Stock");

		return ['status'=>1,'message'=>'Opening Stock deleted successfully.','transData'=>$this->getItemOpeningTrans($transData->item_id)['htmlData']];
	}
	
	public function getProcessWiseMachine($processId){
	    $data['where']['item_type'] = 5;
	    $data['customWhere'][] = 'find_in_set("'.$processId.'", process_id)';
        $data['tableName'] = $this->itemMaster;
        return $this->rows($data);
	}
	
	public function getBatchNoCurrentStock($item_id,$location_id,$batch_no){
		$data['tableName'] = "stock_transaction";
		$data['select'] = "SUM(qty) as stock_qty";
		$data['where']['item_id'] = $item_id;
		$data['where']['location_id'] = $location_id;
		$data['where']['batch_no'] = $batch_no;
		return $this->row($data);
	}

	public function saveToolConsumption($data){
		$toolData = $this->getToolConsumption($data['item_id']);

		if(isset($data['id'])):
			foreach($data['id'] as $key=>$value):

				if(!empty($data['ref_item_id'][$key])):	
					$toolConsumptionData = [
						'id'=>$value,
						'item_id'=>$data['item_id'],
						'ref_item_id'=>$data['ref_item_id'][$key],
						'tool_life'=>$data['tool_life'][$key],
						'operation'=>$data['operation'][$key],
						'process_id'=>$data['process_id'][$key],
						'created_by'=>$this->session->userdata('loginId')
					];
					$this->store('tool_consumption',$toolConsumptionData,'');
				endif;
			endforeach;
		endif;
			
		if(!empty($toolData)):
			foreach($toolData as $row):
				if(isset($data['id']) AND !in_array($row->id,$data['id'])):
					$this->trash('tool_consumption',['id'=>$row->id]);
				endif;
			endforeach;
		endif;

		return ['status'=>1,'message'=>'Tool Consumption Updated successfully.'];
	}

	public function getToolConsumption($id){
		$data['tableName'] = "tool_consumption";		
		$data['select'] = "tool_consumption.*,item_master.item_name,item_master.full_name,process_master.process_name";		
		$data['join']['	item_master'] = "item_master.id = tool_consumption.ref_item_id";
		$data['leftJoin']['	process_master'] = "process_master.id = tool_consumption.process_id";
		$data['where']['tool_consumption.item_id'] = $id;
		$result = $this->rows($data);
		$response = Array();
		if(!empty($result)):
			foreach($result as $row):
				$ops = $this->getToolConsumptionOperation($row->operation);
				$row->ops_name = '';$i=0;
				foreach($ops as $opValue):
					$row->ops_name .= ($i==0) ? $opValue->operation_name : ', '.$opValue->operation_name;$i++;
				endforeach;
				$response[] = $row;
			endforeach;
		endif;
		return $result;
	}
	
	public function getToolConsumptionOperation($operations){
		$data['tableName'] = "production_operation";
		$data['where_in']['id'] = $operations;
		return $this->rows($data);
	}
	
    public function getProductOperation($id){
        $data['where']['item_id'] = $id;
        $data['tableName'] = $this->productProcess;
        $result = $this->rows($data);
		$operations = Array();
		if(!empty($result)):
			foreach($result as $row)
			{
				if(!empty($row->operation)){
					$ops = explode(',',$row->operation);
					foreach($ops as $op){$operations[] = $op;}
				}
			}
		endif;
		$ops_id = array_unique($operations);$response = Array();
		if(!empty($ops_id)):
			$qData['tableName'] = $this->productionOperation;
			$qData['where_in']['id'] = implode(',',$ops_id);
			$response = $this->rows($qData);
		endif;
		return $response;
    }

	public function saveProductOperation($data){
	    $updateData = Array();
	    if(!empty($data['id'])):
	        $updateData['id'] = $data['id'];
    	    $updateData['pfc_process'] = $data['pfc_process'];
    	    $updateData['typeof_machine'] = $data['typeof_machine'];
    	    $updateData['noof_operation'] = $data['noof_operation'];
    		$this->store($this->productProcess,$updateData);
    	endif;
		return ['status'=>1,'message'=>'Process Operation Updated successfully.'];
	}

    public function getPartyItems($party_id){
		
		$queryData['tableName'] = $this->itemMaster;
	    $queryData['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price,item_master.unit_id,item_master.qty,unit_master.unit_name";
		$queryData['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$queryData['where']['item_master.item_type'] = 1;
        $queryData['where']['item_master.party_id'] = $party_id;
        $itemData = $this->rows($queryData);
        
        $partyItems='<option value="">Select Product Name</option>';
        if(!empty($itemData)):
			foreach ($itemData as $row):
				$partyItems .= "<option value='".$row->id."' data-row='".json_encode($row)."'>[".$row->item_code."] ".$row->item_name."</option>";
			endforeach;
        endif;
        return ['status'=>1,'partyItems'=>$partyItems];
    }
	
	public function getDynamicItemList($postData){
		
		if(empty($postData['party_id'])){$postData['party_id'] = 0;}
		
		$queryData['tableName'] = $this->itemMaster;
	    $queryData['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.part_no,item_master.full_name,item_master.category_id,item_master.family_id, item_master.description, item_master.item_type,item_master.hsn_code, item_master.gst_per, item_master.price, item_master.unit_id, item_master.qty, item_master.lead_time, item_master.min_qty, item_master.max_qty, item_master.item_image, item_master.size, item_master.least_count, item_master.instrument_range, unit_master.unit_name, item_category.category_type,item_category.auth_detail,item_category.is_return";
		$queryData['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$queryData['leftJoin']['item_category'] = "item_master.category_id = item_category.id";
		
		if(!empty($postData['searchTerm'])){$queryData['like']['item_master.full_name '] = str_replace(" ", "%", $postData['searchTerm']);}
		if(!empty($postData['searchTerm'])){$queryData['like']['item_master.item_code'] = str_replace(" ", "%", $postData['searchTerm']);}
		if(!empty($postData['item_type'])){$queryData['where_in']['item_master.item_type'] = $postData['item_type'];}
		if(!empty($postData['category_id'])){$queryData['where_in']['item_master.category_id'] = $postData['category_id'];}
		if(!empty($postData['family_id'])){$queryData['where_in']['item_master.family_id'] = $postData['family_id'];}
		if(!empty($postData['party_id'])){$queryData['where']['item_master.party_id'] = $postData['party_id'];}
		
		$queryData['order_by']['item_master.full_name'] = 'ASC';
        $itemData = $this->rows($queryData);
        
		$htmlOptions = Array();$i=0;
		$htmlOptions[] = ['id'=>"", 'text'=>"Select Item", 'row'=>json_encode(Array())];
        if(!empty($itemData)):
			foreach ($itemData as $row):
			    $selected = (!empty($dataRow->machine_id) && $dataRow->machine_id == $row->id) ? 'selected' : '';
				$itmName = (!empty($row->item_code)) ? "[".$row->item_code."] ".$row->item_name : $row->item_name;
				$itmName = (!empty($row->part_no)) ? $row->full_name.' '.$row->part_no : $itmName;
			    if(!empty($postData['default_val']) && $postData['default_val'] == $row->id):
				    $htmlOptions[] = ['id'=>$row->id, 'text'=>$row->full_name, 'row'=>json_encode($row), "selected"=>true];
				else:
				    $htmlOptions[] = ['id'=>$row->id, 'text'=>$row->full_name, 'row'=>json_encode($row)];
				endif;
				$i++;//if($i==250){break;}
			endforeach;
        endif;
		return $htmlOptions;
    }

	public function checkProductOptionStatus($id)
	{
		$result = new StdClass;$result->bom=0;$result->process=0;$result->cycleTime=0;$result->tool=0;
		$queryData = Array();
		$queryData['tableName'] = $this->itemKit;
		$queryData['where']['item_id'] = $id;
		$queryData['where']['kit_type'] = 0;
		$bomData = $this->rows($queryData);
		$result->bom=count($bomData);
		
		$queryData = Array();
		$queryData['tableName'] = $this->productProcess;
		$queryData['where']['item_id'] = $id;
		$processData = $this->rows($queryData);
		$result->process=count($processData);
		
		$queryData = Array();
		$queryData['tableName'] = $this->productProcess;
		$queryData['where']['item_id'] = $id;
		$queryData['where']['cycle_time >'] = 0;
		$ctData = $this->rows($queryData);
		$result->cycleTime=count($ctData);
		
		$queryData = Array();
		$queryData['tableName'] = 'tool_consumption';
		$queryData['where']['item_id'] = $id;
		$toolData = $this->rows($queryData);
		$result->tool=count($toolData);
		
		return $result;
	}
	
	public function getPreInspectionParam($item_id,$param_type=""){
		$data['tableName'] = $this->inspectionParam;
		$data['where']['param_type']=$param_type;
		$data['where']['item_id']=$item_id;
		return $this->rows($data);
	}

	public function savePreInspectionParam($data){
		return $this->store($this->inspectionParam,$data,'Inspection Parameter');
	}

	public function checkDuplicateParam($parameter,$id=""){
        $data['tableName'] = $this->inspectionParam;
        $data['where']['parameter'] = $parameter;
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

	public function deletePreInspection($id){
        return $this->trash($this->inspectionParam,['id'=>$id],"Record");
	}
	
    public function saveItemApproval($data){
        return $this->store($this->itemMaster,$data,'Item');
    }

	public function getMachineTypeForSelect($id){
		$data['tableName'] = $this->productProcess;
		$data['select'] = "typeof_machine";
		$data['where']['id'] = $id;
		$result = $this->row($data);
		return $result->typeof_machine;
	}
	
	public function saveItemDetails($data){
		$mgsName = ($data['item_type'] == 0)?"Item":"Product";
		$result = $this->store($this->itemMaster,$data,$mgsName);
		return $result;
    }
    
    //Created At 25/4/22
	public function getInspParamById($item_id){
		$data['tableName'] = $this->inspectionParam;
		$data['where']['item_id'] = $item_id;
		return $this->rows($data);
	}
	
	public function getPrdProcessDataProductProcessWise($data){
		$queryData['tableName'] = $this->productProcess;
		$queryData['where']['item_id'] = $data['item_id'];
		$queryData['where']['process_id'] = $data['process_id'];
		return  $this->row($queryData);
	}

	public function getMachineTypeWiseMachine($typeof_machine){
		$queryData['tableName'] = $this->itemMaster;
		$queryData['where']['item_type'] = 5;
		$customeWhere='';
		/*if(!empty($typeof_machine)){
			$machine_type=explode(',',$typeof_machine);
			for($i=0;$i<count($machine_type);$i++){
				if($i!=0){
					$customeWhere.=' OR ';
				}
				$customeWhere.='find_in_set('.$machine_type[$i].',typeof_machine)';
			}
			$queryData['customWhere'][] = $customeWhere;
		}*/
		
		return  $this->rows($queryData);
	}

	
	public function saveProductionOutput($data){
		$kitData = $this->getProductOutputData($data['item_id']);
		foreach($data['output_item_id'] as $key=>$value):
			if(empty($data['id'][$key])):
				$itemData = ['id'=>"",'item_id'=>$data['item_id'],'output_item_id'=>$value,'qty'=>$data['qty'][$key],'production_type'=>$data['production_type'][$key]];
				$this->store($this->production_output,$itemData);
			else:
				$where['item_id'] = $data['item_id'];
				$where['id'] = $data['id'][$key];
				$this->edit($this->production_output,$where,['qty'=>$data['qty'][$key]]);
			endif;
		endforeach;
		if(!empty($kitData)):
			foreach($kitData as $key=>$value):
				if(!in_array($value->id,$data['id'])){
					$this->trash($this->production_output,['id'=>$value->id]);
				}
			endforeach;
		endif;
		return ['status'=>1,'message'=>'Production Output saved successfully.'];
	}

	public function getProductOutputData($id){
		$data['select'] = "production_output.*,item_master.item_name,item_master.full_name";
		$data['leftJoin']['item_master'] = "item_master.id = production_output.output_item_id";
		$data['where']['production_output.item_id'] = $id;
		$data['tableName'] = $this->production_output;
		return $this->rows($data);
	}
	
	/***** Control Plan ******/
	public function getControlPlanData($item_id,$param_type=""){
		$data['tableName'] = $this->controlPlan;
		$data['select']="control_plan.*,process_master.process_name";
		$data['leftJoin']['process_master'] = 'process_master.id=control_plan.process_id';
		if(!empty($param_type)){ $data['where']['param_type']=$param_type; }
		$data['where']['item_id']=$item_id;
		return $this->rows($data);
	}

	public function saveControlPlanData($data){
		return $this->store($this->controlPlan,$data,'Control Plan');
	}

	public function deleteControlPlanData($id){
        return $this->trash($this->controlPlan,['id'=>$id],"Record");
	}
	
	//Created By NYN 05/10/2022
	public function saveProdProcess($data){
		try{
            $this->db->trans_begin();
			$data['id'] = '';
            $result = $this->store($this->productProcess,$data,'Product Process');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
	}

	//Created By NYN 05/10/2022
	public function deleteProdProcess($id){
		try{
            $this->db->trans_begin();
            $result = $this->trash($this->productProcess,['id'=>$id],"Record");

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
	}

    public function getItemFromCode($item_code,$item_type=''){
		$data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.*,unit_master.unit_name";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$data['where']['item_master.item_code'] = $item_code;
		if(!empty($item_type)){ $data['where']['item_master.item_type'] = $item_type; }
        return $this->row($data);
	}
	
	/*Created By : NYN @14-02-2023 */
	public function getCalibrationList($item_id,$batch_no=""){
		$data['tableName'] = $this->calibration;
		$data['select'] = "calibration.*,employee_master.emp_name";
        $data['leftJoin']['employee_master'] = "employee_master.id = calibration.created_by";
		$data['where']['item_id'] = $item_id;
		if(!empty($batch_no)){ $data['where']['calibration.batch_no'] = $batch_no; }
		return $this->rows($data);
	}

	public function saveCalibration($data){
		//$this->store($this->itemMaster,['id'=>$data['item_id'],'last_cal_date'=>$data['cal_date'],'cal_agency'=>$data['cal_by'],'next_cal_date'=>$data['next_cal_date']],'Instruments');
		$this->edit($this->calibration,['item_id'=>$data['item_id'],'batch_no'=>$data['batch_no']],['is_active'=>0],'Instruments');
		$data['is_active'] = 1;
		return $this->store($this->calibration,$data,'Calibration');
   	}

   	public function deleteCalibration($id,$item_id){
		$data = $this->getCalibration($id);
		$result = $this->trash($this->calibration,['id'=>$id],"Record");
		$calData = $this->getCalibrationData($data->item_id,$data->batch_no);
		$this->edit($this->calibration,['id'=>$calData->id],['is_active'=>1],'Instruments');
		return $result;
	}

	public function getCalibrationData($item_id,$batch_no){
        $data['tableName'] = $this->calibration;
        $data['where']['item_id'] = $item_id;
        $data['where']['batch_no'] = $batch_no;
        $data['order_by']['id'] = "DESC";
        $data['limit'] = 1;
        return $this->row($data);
	}
	
	public function getCalibration($id){
        $data['tableName'] = $this->calibration;
        $data['where']['id'] = $id;
        return $this->row($data);
	}
	
	// CREATED BY MEGHAVI @12/07/2023
	public function getCalibrationValue($id){
        $data['tableName'] = 'qc_challan_trans';
        $data['where']['challan_id'] = $id;
        return $this->rows($data);
	}

	public function getCalibrationItem($id){
        $data['tableName'] = $this->calibration;
		$data['select'] = "calibration.*,qc_instruments.item_name";
        $data['leftJoin']['qc_instruments'] = "qc_instruments.id = calibration.item_id";
        $data['where']['calibration.item_id '] = $id;
        return $this->rows($data);
	}

    /* Created By :- Avruti @06/11/2023 */
	public function saveProcessDocuments($data){
	    try{
            $this->db->trans_begin();
            $result = $this->store($this->prod_process_doc,$data,'Product Process Document');
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

	public function deleteProcessDocuments($data){
	    try{
            $this->db->trans_begin();
            $doc = $this->getProdProcessDocumentById($data);
            $deleteData = $this->trash($this->prod_process_doc,['id'=>$data['id'],'item_id'=>$data['item_id']],'Record');
            
            if(!empty($doc->file_upload)):
				$fileData = explode(',~',$doc->file_upload);
				foreach($fileData as $key=>$value):
            		$filePath = realpath(APPPATH . '../assets/uploads/prod_process_doc/'.$value);
            		unlink($filePath);
            	endforeach;
            endif;
    		
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $deleteData;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
	}

	public function getProdProcessTableData($data){
		$data['tableName'] = $this->prod_process_doc;
		$data['select'] = "prod_process_doc.*,process_master.process_name,employee_master.emp_name as approve_name";
		$data['leftJoin']['process_master'] = "process_master.id = prod_process_doc.process_id";
		$data['leftJoin']['employee_master'] = "employee_master.id = prod_process_doc.approve_by";
        $data['where']['prod_process_doc.item_id'] = $data['item_id'];
        return $this->rows($data);
	}

	public function getProdProcessDocumentById($data){
		$data['tableName'] = $this->prod_process_doc;
        $data['where']['id'] = $data['id'];
        $data['where']['item_id'] = $data['item_id'];
        return $this->row($data);
	}
	
	public function getItemDetail($postData){
	    $data['tableName'] = $this->itemMaster;
	   

		if(!empty($postData['id'])){
			$data['where_in']['item_master.id'] = $postData['id'];
		}
		return $this->rows($data);
	}
}
?>