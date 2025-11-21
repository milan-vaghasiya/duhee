<?php
class DispatchRequestModel extends MasterModel {
    private $stockTransaction = "stock_transaction";
    private $transChild = "trans_child";
    private $dispatchRequest = "packing_request";
    private $itemMaster = "item_master";

    public function getDTRows($data){ 
        $data['tableName'] = $this->dispatchRequest;
        $data['select'] = "packing_request.*,party_master.party_code,party_master.party_name,item_master.item_code,item_master.item_name,(packing_request.req_qty - packing_request.dispatch_qty) as pending_qty, packing_request.packing_qty as packed_qty, trans_main.trans_prefix as so_prefix,trans_main.trans_no as so_no";
        $data['leftJoin']['item_master'] = "item_master.id = packing_request.item_id";
        $data['leftJoin']['party_master'] = "party_master.id = item_master.party_id";
        $data['leftJoin']['trans_main'] = "packing_request.trans_main_id = trans_main.id";
        if(empty($data['status'])){ $data['customWhere'][] = '(packing_request.req_qty - packing_request.dispatch_qty) > 0'; }
        else{ $data['customWhere'][] = '(packing_request.req_qty - packing_request.dispatch_qty) <= 0'; }
        if(!empty($data['party_id'])){$data['where']['item_master.party_id'] = $data['party_id'];}
        if(!empty($data['item_id'])){$data['where']['packing_request.item_id'] = $data['item_id'];}
		$data['order_by']['packing_request.req_date'] = "DESC";
        
        $data['searchCol'][] = "packing_request.req_date";
        $data['searchCol'][] = "packing_request.trans_no";
        $data['searchCol'][] = "CONCAT('/',trans_main.trans_no)";
        $data['searchCol'][] = "CONCAT(item_master.item_code,item_master.item_name)";
        $data['searchCol'][] = "CONCAT(party_master.party_code,party_master.party_name)";
        $data['searchCol'][] = "packing_request.req_qty";
		$columns =array('','','packing_request.entry_date','packing_request.trans_no','trans_main.trans_no','party_master.party_name','packing_request.item_name','packing_request.req_qty','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getDispatchReqRows($data){ 
        $data['tableName'] = $this->dispatchRequest;
        $data['select'] = "packing_request.*,party_master.party_code,party_master.party_name, item_master.item_code, item_master.item_name,(packing_request.req_qty - packing_request.dispatch_qty) as pending_qty, packing_request.packing_qty as packed_qty, trans_main.trans_prefix as so_prefix,trans_main.trans_no as so_no";
        $data['leftJoin']['item_master'] = "item_master.id = packing_request.item_id";
        $data['leftJoin']['party_master'] = "party_master.id = item_master.party_id";
        $data['leftJoin']['trans_main'] = "packing_request.trans_main_id = trans_main.id";
        
        if(empty($data['status'])){ $data['customWhere'][] = '(packing_request.req_qty - packing_request.dispatch_qty) > 0'; }
        else{ $data['customWhere'][] = '(packing_request.req_qty - packing_request.dispatch_qty) <= 0'; }
        
        if(!empty($data['party_id'])){$data['where']['item_master.party_id'] = $data['party_id'];}
        if(!empty($data['item_id'])){$data['where']['packing_request.item_id'] = $data['item_id'];}
		
		// if(!empty($data['country_id'])){ // Domestic
		// 	$data['where']['party_master.country_id'] = $data['country_id'];
			
		// }else{ // Export
		// 	$data['where']['party_master.country_id != '] = '101';
		// 	//$data['where_in']['packing_request.status']='0,1';
		// }
		$data['order_by']['packing_request.req_date'] = "DESC";
        
        $data['searchCol'][] = "packing_request.req_date";
        $data['searchCol'][] = "packing_request.trans_no";
        $data['searchCol'][] = "CONCAT('/',trans_main.trans_no)";
        $data['searchCol'][] = "CONCAT(item_master.item_code,item_master.item_name)";
        $data['searchCol'][] = "CONCAT(party_master.party_code,party_master.party_name)";
        $data['searchCol'][] = "packing_request.req_qty";
		$columns =array('','','packing_request.entry_date','packing_request.trans_no','trans_main.trans_no','party_master.party_name','packing_request.item_name','packing_request.req_qty','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }
	
    public function nextTransNo(){
        $data['tableName'] = $this->dispatchRequest;
        $data['select'] = "MAX(trans_no) as trans_no";
		$data['where']['req_date >= '] = $this->startYearDate;
        $data['where']['req_date <= '] = $this->endYearDate;
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo;
    }	

    public function getRequestEditData($trans_no){
        $data['tableName'] = $this->dispatchRequest;
        $data['select'] = "packing_request.*,item_master.item_code,item_master.item_name,trans_main.trans_prefix, trans_main.trans_no,trans_child.qty as order_qty";
        $data['leftJoin']['item_master'] = "item_master.id = packing_request.item_id";
        $data['leftJoin']['trans_main'] = "trans_main.id = packing_request.trans_main_id";
        $data['leftJoin']['trans_child'] = "trans_child.id = packing_request.trans_child_id";
        $data['where']['packing_request.trans_no'] = $trans_no;
        return $this->rows($data);
    }

    public function getSalesOrderList($party_id){  
        $data['tableName'] = $this->transChild;    
        $data['select'] = 'trans_child.id,trans_child.trans_main_id,trans_child.item_id,trans_child.item_code,trans_child.item_name,trans_child.item_alias,trans_child.trans_status,trans_child.qty,trans_main.trans_prefix,trans_main.trans_no,trans_child.cod_date as delivery_date,trans_child.dispatch_qty,ifnull(packing_request.req_qty,0) as req_qty';        
        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['leftJoin']['(SELECT trans_child_id,ifnull(SUM(req_qty),0) as req_qty FROM packing_request WHERE is_delete = 0 GROUP BY trans_child_id) as packing_request'] = "packing_request.trans_child_id = trans_child.id";
        $data['where']['trans_child.entry_type'] = 4;
		// $data['where']['trans_main.is_approve !='] = 0;
		$data['where']['trans_main.trans_status'] = 0;
		$data['where']['trans_child.trans_status'] = 0;
        $data['where']['trans_main.party_id'] = $party_id;
        $data['where']['(trans_child.qty - ifnull(packing_request.req_qty,0) - trans_child.dispatch_qty) >'] = 0;
        return $this->rows($data);
    }

    // Created By JP@25-03-2023
    public function getSalesOrder($postData){  
        $data['tableName'] = $this->transChild;
        $data['select'] = "trans_child.*,item_master.item_name,trans_main.trans_prefix,trans_main.trans_no, ifnull(total_request.req_qty,0) as req_qty, (trans_child.qty - ifnull(total_request.req_qty,0) - trans_child.dispatch_qty) as pending_qty, trans_child.qty as order_qty, trans_child.dispatch_qty, ifnull(total_request.id,'') as pr_id";
        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $data['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		$data['leftJoin']['(SELECT id,trans_child_id, ifnull(SUM(req_qty),0) as req_qty FROM packing_request WHERE is_delete = 0 GROUP BY trans_child_id) as total_request'] = "total_request.trans_child_id = trans_child.id";
		if(!empty($postData['id']))
		{
			$data['select'] .= ", ifnull(packing_request.req_qty,0) as edit_req_qty";
			$data['leftJoin']['packing_request'] = "packing_request.id = ".$postData['id'];
		}
        $data['where']['trans_main.party_id'] = $postData['party_id'];
        $data['where']['trans_child.entry_type'] = 4;
        $data['where']['trans_child.trans_status'] = 0;
        $data['where']['trans_main.trans_status'] = 0;
		// $data['where']['trans_main.is_approve !='] = 0;
		// $data['where']['trans_main.trans_date >='] = '2022-03-31';
        $data['where']['(trans_child.qty - ifnull(total_request.req_qty,0) - trans_child.dispatch_qty) >'] = 0;
        $result= $this->rows($data);
        return $result;
    }

    public function saveDispatchRequest($data){
        try{
            $this->db->trans_begin();  
          
            foreach($data['trans_child_id'] as $key=>$value):
                if($data['req_qty'][$key] > 0):
                    $transData = [
                        'id' => $data['id'][$key],
                        'trans_no' => $data['trans_no'],
                        'trans_prefix' => $data['trans_prefix'],
                        'req_date' => $data['req_date'],
                        'party_id' => $data['party_id'],
                        'trans_child_id' => $value,
                        'trans_main_id' => $data['trans_main_id'][$key],
                        'item_id' => $data['item_id'][$key],
                        'req_qty' => $data['req_qty'][$key],
                        'delivery_date' => $data['delivery_date'][$key],
                        'trans_way' => $data['trans_way'],
                        'remark' => $data['remark'],
                        'created_by'  => $this->session->userdata('loginId')
                    ];
                    $this->store($this->dispatchRequest,$transData);					
                else:
                    if(!empty($data['id'][$key])):
                        $this->trash($this->dispatchRequest,['id'=>$data['id'][$key]]);
                    endif;
                endif;
            endforeach;
          
            $result = ['status'=>1,'message'=>'Dispatch Request send successfully.'];
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getPackingReqData($id){
        $data['tableName'] = $this->dispatchRequest;
        $data['select'] = "packing_request.*";//,item_master.item_name,item_master.item_code";
        //$data['leftJoin']['item_master'] = "item_master.id = packing_request.item_id";
        $data['where']['packing_request.id'] = $id;
        return $this->row($data);
    }

    public function delete($id){ 
        try{
            $this->db->trans_begin();
          
            $result = $this->trash($this->dispatchRequest,['id'=>$id],'Request');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    // Created By Meghavi @26/12/22
    public function getItemData($id){ 
        $queryData = array();
        $queryData['tableName'] = $this->dispatchRequest;
        $queryData['select'] = "packing_master.trans_number,item_master.item_code,item_master.item_name,packing_request.packing_qty,packing_transaction.total_box_qty";
        $queryData['leftJoin']['packing_transaction'] = "find_in_set(packing_request.id,packing_transaction.req_ids) > 0";
        $queryData['leftJoin']['packing_master'] = "packing_master.id = packing_transaction.packing_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = packing_request.item_id";
        $queryData['where']['packing_request.id'] = $id;
        $resultData = $this->rows($queryData);

        $html="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):         
                $html .= '<tr>
                    <td class="text-center">'.$i++.'</td>
                    <td class="text-center">['.$row->item_code.'] '.$row->item_name.'</td>
                    <td class="text-center">'.$row->trans_number.'</td>	
                    <td class="text-center">'.$row->total_box_qty.'</td>	
                    <td class="text-center">'.$row->packing_qty.'</td>	
                    </tr>';
                
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="4">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }

    public function getRequestForChallan($data){
        $data['tableName'] = $this->dispatchRequest;
        $data['select'] = "packing_request.*,party_master.party_code,party_master.party_name, item_master.item_code,item_master.item_name,(packing_request.req_qty - packing_request.packing_qty) as pending_qty,trans_main.trans_prefix as so_prefix,trans_main.trans_no as so_no";
        $data['leftJoin']['item_master'] = "item_master.id = packing_request.item_id";
        $data['leftJoin']['party_master'] = "party_master.id = item_master.party_id";
        $data['leftJoin']['trans_main'] = "packing_request.trans_main_id = trans_main.id";
        $data['customWhere'][] = '(packing_request.req_qty - packing_request.packing_qty) > 0'; 
        $data['where']['item_master.party_id'] = $data['party_id'];
        if(!empty($data['ref_id'])){ $data['where_in']['packing_request.id'] = $data['ref_id']; }
        if(!empty($data['ref_no'])){ $data['where']['packing_request.trans_no'] = $data['ref_no']; }
		$data['customWhere'][] = 'packing_request.req_qty > packing_request.dispatch_qty';
        return $this->rows($data);
    }

    public function getOrderItemsForDC($transIds){
        $data['tableName'] = $this->transChild;    
        $data['select'] = "trans_child.*,packing_request.id as request_id, packing_request.packing_qty as packed_qty, packing_request.dispatch_qty as dispatched_qty,packing_request.req_qty";   
        $data['join']['packing_request'] = "packing_request.trans_child_id = trans_child.id";
        $data['where']['entry_type'] = 4;
        $data['where_in']['packing_request.id'] = $transIds;
        return $this->rows($data);
    }

    // Created By JP@25-03-2023
    public function getPendingRequest($postData=[]){  
        $data['tableName'] = $this->dispatchRequest;
        $data['select'] = "packing_request.id, item_master.item_code, item_master.item_name, trans_main.trans_prefix as so_prefix, trans_main.trans_no as so_no,trans_main.trans_date as so_date, party_master.party_code, party_master.party_name, trans_child.id as so_trans_id";
		$data['select'] .= ",SUM(packing_request.req_qty - packing_request.dispatch_qty) as req_qty";
		//$data['select'] .= ",SUM(trans_child.qty - trans_child.dispatch_qty) as pending_order_qty";
        $data['leftJoin']['item_master'] = "item_master.id = packing_request.item_id";
        $data['leftJoin']['trans_main'] = "trans_main.id = packing_request.trans_main_id";
        $data['leftJoin']['trans_child'] = "trans_child.id = packing_request.trans_child_id";
        $data['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		if(!empty($postData['item_id'])){$data['where']['packing_request.item_id'] = $postData['item_id'];}
        $data['group_by'][] = 'trans_child.id';
        $result = $this->rows($data);
		//$this->printQuery();
		return $result;
    }
}
?>