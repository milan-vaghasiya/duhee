<?php
class GateReceiptModel extends MasterModel{
    private $mir = "mir";
    private $mirTrans = "mir_transaction";
    private $IcInspection = "ic_inspection";
    private $tcInspect = "tc_inspection";
    private $materialSpeci = "material_specification";
    private $stockTrans = "stock_transaction";
	private $testReport = "grn_test_report";

    public function getDTRows($data){
        $data['tableName'] = $this->mirTrans;
        $data['select'] = "mir_transaction.*,mir.item_stock_type,mir.tc_id,mir.trans_date,mir.trans_prefix,mir.trans_no,party_master.party_name,item_master.full_name as item_name,item_master.item_type";
        $data['leftJoin']['mir'] = "mir.id = mir_transaction.mir_id";
        $data['leftJoin']['party_master'] = "party_master.id = mir.party_id";
        $data['leftJoin']['item_master'] = "item_master.id = mir.item_id";
        //$data['where']['item_master.item_type'] = 3;
        $data['where']['mir_transaction.type'] = 1;
        
        if($data['status'] == 0){$data['where']['mir_transaction.trans_status != '] = 3;}
        if($data['status'] == 1){$data['where']['mir_transaction.trans_status'] = 3;}
        
        $data['where']['mir.trans_type'] = 2;
        
        $data['order_by']['mir_transaction.id'] = "DESC";

        $data['searchCol'][] = ""; 
        $data['searchCol'][] = "";
        $data['searchCol'][] = "CONCAT(mir.trans_prefix,mir.trans_no)";
        $data['searchCol'][] = "DATE_FORMAT(mir.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "mir_transaction.batch_no";
        $data['searchCol'][] = "mir_transaction.mill_heat_no";
        $data['searchCol'][] = "mir_transaction.qty";

        $columns = array('', '', 'CONCAT(mir.trans_prefix,mir.trans_no)', 'mir.trans_date', 'party_master.party_name', 'item_master.item_name', 'mir_transaction.batch_no', 'mir_transaction.mill_heat_no', 'mir_transaction.qty');
        if(isset($data['order'])):
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        endif;

        return $this->pagingRows($data);
    }

    public function checkTcStatus($batch_no){
        $queryData = array();
        $queryData['tableName'] = $this->IcInspection;
        $queryData['where']['batch_no'] = $batch_no;
        $result = $this->numRows($queryData);
        return $result;
    }

    //Changed By Avruti @14/08/2022 
	public function getInInspectionMaterial($id){
		$data['tableName'] = $this->mirTrans;
		$data['select'] = "mir_transaction.id, mir_transaction.mir_id, mir_transaction.qty, mir.item_id, mir.trans_no, mir.trans_prefix, mir.trans_date, mir.party_id, item_master.full_name, party_master.party_name";
		$data['join']['mir'] = "mir.id = mir_transaction.mir_id";
		$data['join']['item_master'] = "item_master.id = mir.item_id";
		$data['join']['party_master'] = "party_master.id = mir.party_id";
		$data['where']['mir_transaction.id'] = $id;       
		return $this->row($data);
	}

	public function getInInspection($id){
		$data['tableName'] = $this->IcInspection;
		$data['select'] = "ic_inspection.*,ic_inspection.trans_no as iir_no, party_master.party_name, item_master.item_code, item_master.item_name, item_master.full_name, item_master.material_grade,item_master.rev_no, mir_transaction.batch_no,mir_transaction.heat_no,  mir_transaction.qty, mir.trans_date,, mir.trans_no, mir.trans_prefix,item_category.category_name,employee_master.emp_name";
		$data['leftJoin']['party_master'] = "party_master.id = ic_inspection.party_id";
		$data['leftJoin']['mir_transaction'] = "mir_transaction.id = ic_inspection.mir_trans_id";
		$data['leftJoin']['mir'] = "mir_transaction.mir_id = mir.id";
		$data['leftJoin']['item_master'] = "item_master.id = mir.item_id";
		$data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
		$data['leftJoin']['employee_master'] = "employee_master.id = ic_inspection.created_by";
		$data['where']['ic_inspection.mir_trans_id'] = $id;         
		$data['where']['ic_inspection.trans_type'] = 0;    
		return $this->row($data);
	}

	public function saveInInspection($data){
	    //print_r($data);exit;
		try{
			$this->db->trans_begin();
            if(empty($data['id'])){ $data['trans_no'] = $this->getNextIIRNo(0); }
            
			$result = $this->store($this->IcInspection,$data,'Incoming Inspection');
			
			/* if($result['status'] == 1)
			{
                $this->edit($this->stockTrans,['ref_type'=>1,'ref_id'=>$data['mir_id'],'trans_ref_id'=>$data['mir_trans_id']],['stock_effect' => 1]);
			} */
			//print_r($this->db->last_query());exit;
            if(empty($data['id'])){$this->store($this->mirTrans,['id'=>$data['mir_trans_id'],'iir_status' => 1]);}
			
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}
	
	public function getGateReceiptTrans($id){
        $queryData['tableName'] = $this->mirTrans;
        $queryData['select'] = "mir_transaction.*, mir.material_grade";
        $queryData['leftJoin']['mir'] = "mir.id = mir_transaction.mir_id";
        $queryData['where']['mir_transaction.id'] = $id;
        return $this->row($queryData);
    }
	
    //Replaced By Meghavi @31/05/23
    public function getTcInspectionParam($data){
        $data['tableName'] = $this->tcInspect;
        $data['where']['grade_id'] = $data['grade_id'];
        $data['where']['ref_id'] = $data['grn_trans_id'];
        return $this->rows($data);
    }

    //Replaced By Meghavi @31/05/23
    public function getMaterialSpecification($grade_id){
        $data['tableName'] = $this->materialSpeci;
        $data['select'] = 'grade_id,spec_type,param_name,sub_param,min_value,max_value';
        $data['where']['grade_id'] = $grade_id;
        return $this->rows($data);
    }

    // Replaced By Meghavi @31/05/23
	public function saveTcInspectionParam($data){ 
        try{  
            foreach($data['id'] as $key=>$value):
                $specification = [
                    'id' => $value,
                    'ref_id' => $data['grn_trans_id'],
                    'grade_id' => $data['grade_id'][$key],
                    'spec_type' => $data['spec_type'][$key],
                    'param_name' => $data['param_name'][$key],
                    'sub_param' => $data['sub_param'][$key],
                    'min_value' => $data['min_value'][$key],
                    'max_value' => $data['max_value'][$key],
                    'result' => $data['result'][$key],
                    'created_by' => $data['created_by']
                ];
                $this->store($this->tcInspect,$specification,'Material Specification');
            endforeach;

            $result = ['status'=>1,'message'=>'TC Parameter saved successfully.'];

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getNextIIRNo($trans_type = 0){
        $data['tableName'] = $this->IcInspection;
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['trans_type'] = $trans_type;
        $maxNo = $this->specificRow($data)->trans_no;
		$nextIIRNo = (!empty($maxNo))?($maxNo + 1):1;
		return $nextIIRNo;
    }

    public function getGateReceiptOtherData($id){
        $queryData = array();
        $queryData['tableName'] = $this->mirTrans;
        $queryData['select'] = "mir_transaction.*,item_master.full_name,item_master.item_type,mir.item_stock_type";
        $queryData['leftJoin']['mir'] = "mir.id = mir_transaction.mir_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = mir_transaction.item_id";
        $queryData['where']['mir_transaction.id'] = $id;
        //$queryData['where']['item_master.item_type != '] = 3;
        $queryData['where']['mir_transaction.type'] = 1;
        $mirTransData = $this->rows($queryData);
        return $mirTransData;
    }

    public function getMirTransRow($id){
        $queryData = array();
        $queryData['tableName'] = $this->mirTrans;
        $queryData['select'] = "mir_transaction.*,mir.item_stock_type,mir.trans_prefix,mir.trans_no,mir.trans_date,item_master.size,item_master.item_type";
        $queryData['leftJoin']['mir'] = "mir.id = mir_transaction.mir_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = mir_transaction.item_id";
        $queryData['where']['mir_transaction.id'] = $id;
        $mirTransData = $this->row($queryData);
        return $mirTransData;
    }

    public function saveMaterialInspection($data){
        try{
            $this->db->trans_begin();

            foreach($data['item_data'] as $row):                

                $transData = $this->getMirTransRow($row['mir_trans_id']);

                if($transData->item_stock_type == 0):
                    $acceptData['batch_no'] = $transData->batch_no;
                    $acceptData['serial_no'] = 0;
                    if($transData->item_type == 2){
                        $acceptData['batch_no'] = "General Batch~".date("Ymd",strtotime($transData->expire_date));
                    }
                    $stockQueryData['id']="";
                    $stockQueryData['location_id']=$transData->location_id;
                    $stockQueryData['batch_no'] = $acceptData['batch_no'];
                    $stockQueryData['trans_type']=1;
                    $stockQueryData['item_id']=$transData->item_id;
                    $stockQueryData['qty']=$row['ok_qty'];
                    $stockQueryData['ref_type']=1;
                    $stockQueryData['ref_id']=$row['mir_id'];
                    $stockQueryData['trans_ref_id']=$row['mir_trans_id'];
                    $stockQueryData['ref_no']=$transData->trans_prefix.sprintf("%03d",$transData->trans_no);
                    $stockQueryData['ref_date']=date("Y-m-d",strtotime($transData->trans_date));
                    $stockQueryData['ref_batch']=$transData->heat_no;
                    $stockQueryData['created_by']=$this->loginId;
                    $stockQueryData['stock_effect'] = 1;
                    $this->store($this->stockTrans,$stockQueryData);
                elseif($transData->item_stock_type == 1):
                    //$nextBatchNo = $this->getNextBatchOrSerialNo(['trans_id'=>"",'item_id'=>$transData->item_id,'heat_no'=>$transData->heat_no]);

                    $acceptData['batch_no'] = $transData->batch_no;//$nextBatchNo['batch_no'];                    
                    $acceptData['serial_no'] = $transData->serial_no;//$nextBatchNo['serial_no'];
                    if($transData->item_type == 2){
                        $acceptData['batch_no'] = $acceptData['batch_no']."~".date("Ymd",strtotime($transData->expire_date));
                    }
                    $stockQueryData['id']="";
                    $stockQueryData['location_id']=$transData->location_id;
                    $stockQueryData['batch_no'] = $acceptData['batch_no'];
                    $stockQueryData['trans_type']=1;
                    $stockQueryData['item_id']=$transData->item_id;
                    $stockQueryData['qty']=$row['ok_qty'];
                    $stockQueryData['ref_type']=1;
                    $stockQueryData['ref_id']=$row['mir_id'];
                    $stockQueryData['trans_ref_id']=$row['mir_trans_id'];
                    $stockQueryData['ref_no']=$transData->trans_prefix.sprintf("%03d",$transData->trans_no);
                    $stockQueryData['ref_date']=date("Y-m-d",strtotime($transData->trans_date));
                    $stockQueryData['ref_batch']=$transData->heat_no;
                    $stockQueryData['created_by']=$this->loginId;
                    $stockQueryData['stock_effect'] = 1;
                    $this->store($this->stockTrans,$stockQueryData);
                else:
                    $batchNo = "";
                    $seriaNo = 0;
                    for($i=1;$i<=$row['ok_qty'];$i++):
                        $nextBatchNo = $this->getNextBatchOrSerialNo(['trans_id'=>"",'item_id'=>$transData->item_id,'heat_no'=>$transData->heat_no]);
                        $this->store($this->mirTrans,['id'=>$row['mir_trans_id'],'serial_no'=>$nextBatchNo['serial_no']]);
                        if($i==1):
                            $batchNo = $nextBatchNo['batch_no'];                             
                        endif;
                        if($i==$row['ok_qty']):
                            $seriaNo = $nextBatchNo['serial_no']; 
                        endif;

                        if($transData->item_type == 2){
                            $nextBatchNo['batch_no'] = $nextBatchNo['batch_no']."~".date("Ymd",strtotime($transData->expire_date));
                        }

                        $stockQueryData['id']="";
                        $stockQueryData['location_id']=$transData->location_id;
                        $stockQueryData['batch_no'] = $nextBatchNo['batch_no'];
                        $stockQueryData['trans_type']=1;
                        $stockQueryData['item_id']=$transData->item_id;
                        $stockQueryData['qty']=1;
                        $stockQueryData['ref_type']=1;
                        $stockQueryData['ref_id']=$row['mir_id'];
                        $stockQueryData['trans_ref_id']=$row['mir_trans_id'];
                        $stockQueryData['ref_no']=$transData->trans_prefix.sprintf("%03d",$transData->trans_no);
                        $stockQueryData['ref_date']=date("Y-m-d",strtotime($transData->trans_date));
                        $stockQueryData['ref_batch']=$transData->heat_no;
                        if(!empty($transData->size)){$stockQueryData['size']=$transData->size;}
                        $stockQueryData['created_by']=$this->loginId;
                        $stockQueryData['stock_effect'] = 1;
                        $this->store($this->stockTrans,$stockQueryData);
                    endfor;

                    $acceptData['batch_no'] = $batchNo."-".$seriaNo;                    
                    $acceptData['serial_no'] = $seriaNo;
                endif;

                $acceptData['id'] = $row['mir_trans_id'];
                $acceptData['trans_status'] = $row['status'];
                $acceptData['inspection_data'] = json_encode($row);
                $this->store($this->mirTrans,$acceptData);

                if(!empty($row['short_qty'])):
                    $stockQueryData['id']="";
                    $stockQueryData['location_id']=$this->SCRAP_STORE->id;
                    $stockQueryData['batch_no'] = $transData->trans_prefix.sprintf("%03d",$transData->trans_no)." [ Short Qty ]";
                    $stockQueryData['trans_type']=1;
                    $stockQueryData['item_id']=$transData->item_id;
                    $stockQueryData['qty']=$row['short_qty'];
                    $stockQueryData['ref_type']=1;
                    $stockQueryData['ref_id']=$row['mir_id'];
                    $stockQueryData['trans_ref_id']=$row['mir_trans_id'];
                    $stockQueryData['ref_no']=$transData->trans_prefix.sprintf("%03d",$transData->trans_no);
                    $stockQueryData['ref_date']=date("Y-m-d",strtotime($transData->trans_date));
                    $stockQueryData['ref_batch']=$transData->heat_no;
                    $stockQueryData['created_by']=$this->loginId;
                    $stockQueryData['stock_effect'] = 1;
                    $this->store($this->stockTrans,$stockQueryData);
                endif;

                if(!empty($row['rej_qty'])):
                    $stockQueryData['id']="";
                    $stockQueryData['location_id']=$this->SCRAP_STORE->id;
                    $stockQueryData['batch_no'] = $transData->trans_prefix.sprintf("%03d",$transData->trans_no)." [ Reject Qty ]";
                    $stockQueryData['trans_type']=1;
                    $stockQueryData['item_id']=$transData->item_id;
                    $stockQueryData['qty']=$row['rej_qty'];
                    $stockQueryData['ref_type']=1;
                    $stockQueryData['ref_id']=$row['mir_id'];
                    $stockQueryData['trans_ref_id']=$row['mir_trans_id'];
                    $stockQueryData['ref_no']=$transData->trans_prefix.sprintf("%03d",$transData->trans_no);
                    $stockQueryData['ref_date']=date("Y-m-d",strtotime($transData->trans_date));
                    $stockQueryData['ref_batch']=$transData->heat_no;
                    $stockQueryData['created_by']=$this->loginId;
                    $stockQueryData['stock_effect'] = 1;
                    $this->store($this->stockTrans,$stockQueryData);
                endif;
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>"GI Accepted Successfully"];
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getNextBatchOrSerialNo($data){
		$result = array(); $code = "";

        $itemData = $this->item->getItem($data['item_id']);
        $code = (!empty($itemData->batch_stock) && $itemData->batch_stock == 2)?$itemData->item_code:"";
        
        $itemTypes = [5,6,7];
        
		if(!empty($data['trans_id'])):
            $queryData = array();
			$queryData['select'] = "serial_no,heat_no";
			$queryData['tableName'] = $this->mirTrans;
            $queryData['where']['type'] = 1;
			$queryData['where']['id'] = $data['trans_id'];
			$result = $this->row($queryData);

			if(!empty($result->serial_no) && $data['heat_no'] == $result->heat_no):
                //$code .= sprintf(n2y(date('Y'))."%03d",$result->serial_no);
                if(in_array($itemData->item_type,$itemTypes)):
			        $code .= sprintf("-%03d",$result->serial_no);
			    else:
			        $code .= sprintf(n2y(date('Y'))."%03d",$result->serial_no);    
			    endif;
				return ['status'=>1,'batch_no'=>$code,'serial_no'=>$result->serial_no];
			endif;			
		endif;
		
		if(!empty($itemData->batch_stock) && $itemData->batch_stock == 1):
            $queryData = array();
            $queryData['select'] = "serial_no,heat_no";
			$queryData['tableName'] = $this->mirTrans;
			$queryData['where']['item_id'] = $data['item_id'];
            $queryData['where']['type'] = 1;
			$queryData['where']['heat_no'] = $data['heat_no'];
			$result = $this->row($queryData);
			
			if(!empty($result->serial_no)):
                //$code .= sprintf(n2y(date('Y'))."%03d",$result->serial_no);
                if(in_array($itemData->item_type,$itemTypes)):
			        $code .= sprintf("-%03d",$result->serial_no);
			    else:
			        $code .= sprintf(n2y(date('Y'))."%03d",$result->serial_no);    
			    endif;
				return ['status'=>1,'batch_no'=>$code,'serial_no'=>$result->serial_no];
			endif;
		endif;

		$queryData = array();
		$queryData['select'] = "ifnull(MAX(serial_no) + 1,1) as serial_no";
		$queryData['tableName'] = $this->mirTrans;
        $queryData['where']['type'] = 1;
		$queryData['where']['item_id'] = $data['item_id'];
		$queryData['where']['is_delete'] = 0;
		$queryData['where']['YEAR(created_at)'] = date("Y");
		$serial_no = $this->specificRow($queryData)->serial_no;
		//$code .= sprintf(n2y(date('Y'))."%03d",$serial_no);
		if(in_array($itemData->item_type,$itemTypes)):
	        $code .= sprintf("-%03d",$serial_no);
	    else:
	        $code .= sprintf(n2y(date('Y'))."%03d",$serial_no);    
	    endif;
		return ['status'=>1,'batch_no'=>$code,'serial_no'=>$serial_no];
	}

    public function migrateBatchNo(){
        try{
            $this->db->trans_begin();

            $data['tableName'] = $this->mirTrans;
            $data['select'] = "mir_transaction.*,mir.item_stock_type,mir.trans_date,mir.trans_prefix,mir.trans_no,party_master.party_name,item_master.full_name as item_name,item_master.item_type,item_master.item_code";
            $data['leftJoin']['mir'] = "mir.id = mir_transaction.mir_id";
            $data['leftJoin']['party_master'] = "party_master.id = mir.party_id";
            $data['leftJoin']['item_master'] = "item_master.id = mir.item_id";
            $data['where']['mir_transaction.type'] = 1;
            $data['where']['mir_transaction.trans_status != '] = 3;
            $data['where']['mir.trans_type'] = 2;
            $data['customWhere'][] = 'mir_transaction.batch_no IS NULL';
            $data['order_by']['mir_transaction.id'] = "ASC";
            $result = $this->rows($data);

            $i=1;
            foreach($result as $row):
                /* print_r($i." = [".$row->id." -> ".$row->batch_no."]");
                print_r("<hr>"); */

                /*$batchData = array();
                $batchData['id'] = $row->id;
                if($row->item_stock_type == 1):
                    $nextBatchNo = $this->getNextBatchOrSerialNo(['trans_id'=>"",'item_id'=>$row->item_id,'heat_no'=>$row->heat_no]);

                    $batchData['batch_no'] = $nextBatchNo['batch_no'];                    
                    $batchData['serial_no'] = $nextBatchNo['serial_no'];
                elseif($row->item_stock_type == 2):
                    $batchData['batch_no'] = $row->item_code.sprintf(n2y(date('Y'))."%03d",$row->trans_no);
                else:
                    $batchData['batch_no'] = "GB";
                    $batchData['serial_no'] = 0;
                endif;

                $this->store($this->mirTrans,$batchData);*/

                $i++;
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo $i." Batch No. migrated successfully.";exit;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo "somthing is wrong. Error : ".$e->getMessage();exit;
        }	
    }
    
    // Created By Meghavi @31/05/23
    public function getGrnTrans($id){
        $data['tableName'] = $this->mirTrans; 
        $data['select'] = "mir_transaction.*,mir.trans_date as grn_date,item_master.material_grade";
		$data['leftJoin']['mir'] = "mir.id = mir_transaction.mir_id";   
		$data['leftJoin']['item_master'] = "item_master.id = mir_transaction.item_id";     
        $data['where']['mir_transaction.id'] = $id;
        return $this->row($data);
    }
    
    //Test Report Create By : Megghavi @10-08-2022
	public function saveTestReport($data){
        try{
            $this->db->trans_begin(); 
            $tc_file = $data['tc_file']; unset($data['tc_file']);
			$testData = [
				'id' => '',
				'grn_id' => $data['grn_id'],
				'grn_trans_id' => $data['id'],
				'agency_id' => $data['agency_id'],
				'name_of_agency' => $data['name_of_agency'],
				'test_description' => $data['test_description'],
				'sample_qty' => $data['sample_qty'],
				'test_report_no' => $data['test_report_no'],
				'test_remark' => $data['test_remark'],
				'test_result' => $data['test_result'],
				'inspector_name' => $data['inspector_name'],
				'mill_tc' => $data['mill_tc'],
				'tc_file' => $tc_file,
				'created_by' => $data['created_by']
			];
			$result = $this->store($this->testReport,$testData,'Test Report');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

	public function deleteTestReport($id){
		try{
            $this->db->trans_begin();
			
			$result = $this->trash($this->testReport,['id'=>$id],'Test Report');

			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	

	}

	//Test Report Create By : Megghavi @10-08-2022
	public function getTestReport($grn_id){
        $data['tableName'] = $this->mir;
        $data['where']['id'] = $grn_id;
        return $this->row($data);
    }

	public function getTestReportTrans($grn_trans_id){
		$data['tableName'] = $this->testReport;
        $data['where']['grn_trans_id'] = $grn_trans_id;
        return $this->rows($data);
	}
    
}
?>