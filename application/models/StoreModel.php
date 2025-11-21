<?php
class StoreModel extends MasterModel{
    private $locationMaster = "location_master";
    private $stockTransaction = "stock_transaction";
    private $itemMaster = "item_master";
    
    public function getDTRows($data){
        $data['tableName'] = $this->locationMaster;
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "store_name";
        $data['searchCol'][] = "location";
        $data['serachCol'][] = "remark";
		$columns =array('','','store_name','location','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }
    public function getParentStores(){
        $data['tableName'] = $this->locationMaster;
        $data['where']['final_location'] = 0;
        return $this->rows($data);
    }
    //Changed By Karmi @02/05/2022
    public function getStoreNames(){
        $data['tableName'] = $this->locationMaster;
        $data['where']['final_location'] = 1;
        return $this->rows($data);
    }
    //Created By Karmi @02/05/2022
    public function getNextStoreLevel($ref_id){
        $data['tableName'] = $this->locationMaster;
        $data['where']['ref_id'] = $ref_id;
        return $this->rows($data);
    }

	public function getStockDataByItemId($data){
        $locationList = array();
		$batch_qty=0;
		if(!empty($data['batch_qty'])){$batch_qty=$data['batch_qty'];}
        $squery['tableName'] = $this->stockTransaction;
        $squery['select'] = "stock_transaction.batch_no, stock_transaction.location_id, lm.store_name, lm.location";
		if(!empty($data['batch_no'])){
			$squery['select'] .= ", (
				CASE WHEN stock_transaction.batch_no = '".$data['batch_no']."' OR SUM(stock_transaction.qty) > 0 THEN 
				CASE WHEN stock_transaction.batch_no = '".$data['batch_no']."' THEN SUM(stock_transaction.qty) + ".$batch_qty." ELSE SUM(stock_transaction.qty) END ELSE 0 END ) as qty";
		}
		else{$squery['select'] .= ",SUM(stock_transaction.qty) as qty,";}
        $squery['join']['location_master as lm'] = 'lm.id = stock_transaction.location_id';
        if(!empty($data['item_id'])){$squery['where']['stock_transaction.item_id'] = $data['item_id'];}
        if(!empty($data['location_id'])){$squery['where']['stock_transaction.location_id'] = $data['location_id'];}
        $squery['group_by'][] = "batch_no";  
        //if(!empty($data['batch_no'])){$squery['having'][] = "qty > 0";}
		$squery['having'][] = "qty > 0";
        $locationList = $this->rows($squery); 
		//print_r($this->printQuery());exit;
        return $locationList;
    }

    public function getStoreLocationList($customQry="",$location_id=""){ 
        $locationList = array();
        $squery['tableName'] = $this->locationMaster;
        $squery['select'] = "DISTINCT(store_name)";
        $squery['where']['final_location'] = 1;   
        if(!empty($location_id)){$data['where']['id'] = $location_id;}
        if(!empty($customQry)){$squery['customWhere'][] = $customQry;}
        $storeList = $this->rows($squery); 
             
        if(!empty($storeList))
        {
            $i=0;
            foreach($storeList as $store)
            {
                $locationList[$i]['store_name'] = $store->store_name;
                $data['tableName'] = $this->locationMaster;
                $data['where']['store_name'] = $store->store_name;
                $locationList[$i++]['location'] =  $this->rows($data);
            }
        }
        return $locationList;
    }
    
    public function getFinalStoreList($main_store_id=""){ 
        $locationList = array();
        $squery['tableName'] = $this->locationMaster;
        $squery['select'] = "DISTINCT(store_name)";
        $squery['where']['final_location'] = 1;   
        if(!empty($main_store_id)){ $squery['where']['main_store_id'] = $main_store_id;}
        $storeList = $this->rows($squery); 
             
        if(!empty($storeList))
        {
            $i=0;
            foreach($storeList as $store)
            {
                $locationList[$i]['store_name'] = $store->store_name;
                $data['tableName'] = $this->locationMaster;
                $data['where']['store_name'] = $store->store_name;
                if(!empty($location_id)){$data['where']['id'] = $location_id;}
                $locationList[$i++]['location'] =  $this->rows($data);
            }
        }
        return $locationList;
    }

    public function getBatchStoreLocationList($batch_no){
        $locationList = array();
        $squery['tableName'] = $this->locationMaster;
        $squery['select'] = "DISTINCT(store_name)";
        $squery['where']['batch_no'] = $batch_no;
        $storeList = $this->rows($squery);
        
        if(!empty($storeList))
        {
            $i=0;
            foreach($storeList as $store)
            {
                $locationList[$i]['store_name'] = $store->store_name;
                $data['tableName'] = $this->locationMaster;
                $data['where']['store_name'] = $store->store_name;
                $locationList[$i++]['location'] =  $this->rows($data);
            }
        }
        return $locationList;
    }

    public function getStoreLocation($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->locationMaster;
        return $this->row($data);
    }

    public function save($data){
        $data['store_name'] = trim($data['store_name']);
        $data['location'] = trim($data['location']);
        if($this->checkDuplicate($data['store_name'],$data['location'],$data['id']) > 0):
            $errorMessage['location'] = "Location is duplicate.";
            return ['status'=>0,'message'=>$errorMessage];
        else:
            return $this->store($this->locationMaster,$data,'Store');
        endif;
    }

    public function checkDuplicate($storename,$location,$id=""){
        $data['tableName'] = $this->locationMaster;
        $data['where']['store_name'] = $storename;
        $data['where']['location'] = $location;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        return $this->trash($this->locationMaster,['id'=>$id],'Store');
    }

    public function getItemWiseStock($data)
    {

        $itmData = $this->item->getItem($data['item_id']);

        $thead = '<tr><th colspan="6">Product : (' . $itmData->item_code . ') ' . $itmData->item_name . '</th></tr>
					<tr>
                        <th style="width:5%;">Action</th>
						<th>#</th>
						<th style="text-align:left !important;">Store</th>
						<th>Location</th>
						<th>Batch</th>
						<th>Current Stock</th>
					</tr>';
        $tbody = '';
        $i = 1;
        $locationData = $this->store->getStoreLocationList();
        if (!empty($locationData)) {
            foreach ($locationData as $lData) {
                // $tbody = '<tr><th colspan="5">'.$lData['store_name'].'</th></tr>';
                foreach ($lData['location'] as $batch) :
                    $queryData['tableName'] = "stock_transaction";
                    $queryData['select'] = "SUM(qty) as qty,batch_no";
                    $queryData['where']['item_id'] = $data['item_id'];
                    $queryData['where']['location_id'] = $batch->id;
                    $queryData['order_by']['id'] = "asc";
                    $queryData['group_by'][] = "batch_no";
                    $result = $this->rows($queryData);
                    if (!empty($result)) {
                        foreach ($result as $row) {
                            $stfParam = "{'location_id':" . $batch->id . ",'item_id':" . $data['item_id'] . ",'stock_qty':" . floatVal($row->qty) . ",'batch_no':'" . $row->batch_no . "','modal_id' : 'modal-md', 'form_id' : 'stockTransfer', 'title' : 'Stock Transfer','fnSave' : 'saveStockTransfer'}";
                            $stfBtn = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Stock Transfer" flow="down" onclick="stockTransfer(' . $stfParam . ');"><i class="ti-control-shuffle" ></i></a>';
                            $actionBtn = getActionButton($stfBtn);
                            $tbody .= '<tr>';
                            $tbody .= '<td class="text-center">' . $actionBtn . '</td>';
                            $tbody .= '<td class="text-center">' . $i++ . '</td>';
                            $tbody .= '<td>' . $lData['store_name'] . '</td>';
                            $tbody .= '<td>' . $batch->location . '</td>';
                            $tbody .= '<td>' . $row->batch_no . '</td>';
                            $tbody .= '<td>' . floatVal($row->qty) . '</td>';
                            $tbody .= '</tr>';
                        }
                    }
                endforeach;
            }
        }
        return ['status' => 1, 'thead' => $thead, 'tbody' => $tbody];
    }

    public function checkBatchWiseStock($data)
    {
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(qty) as qty";
        $queryData['where']['item_id'] = $data['item_id'];
        $queryData['where']['location_id'] = $data['from_location_id'];
        $queryData['where']['batch_no'] = $data['batch_no'];
        $queryData['where']['is_delete'] = 0;
        return $this->row($queryData);
    }

    public function saveStockTransfer($data)
    {
        $fromTrans = [
            'id' => "",
            "location_id" => $data['from_location_id'],
            "batch_no" => $data['batch_no'],
            "trans_type" => 2,
            "item_id" => $data['item_id'],
            "qty" => "-" . $data['transfer_qty'],
            "ref_type" => "9",
            "ref_id" => $data['from_location_id'],
            "ref_date" => date("Y-m-d"),
            "created_by" => $data['created_by']
        ];
        $this->store('stock_transaction', $fromTrans);

        $toTrans = [
            'id' => "",
            "location_id" => $data['to_location_id'],
            "batch_no" => $data['batch_no'],
            "trans_type" => 1,
            "item_id" => $data['item_id'],
            "qty" => $data['transfer_qty'],
            "ref_type" => "9",
            "ref_id" => $data['from_location_id'],
            "ref_date" => date("Y-m-d"),
            "created_by" => $data['created_by']
        ];
        $this->store('stock_transaction', $toTrans);

        return ['status' => 1, 'message' => "Stock Transfer successfully."];
    }

    
    public function getStoreLocationWithoutProcess()
    {
        $locationList = array();
        $storeList = $this->getStoreNames();
        if (!empty($storeList)) {
            $i = 0;
            foreach ($storeList as $store) {

                $data['tableName'] = $this->locationMaster;
                $data['where']['store_name'] = $store->store_name;
                $data['where']['store_type !='] = 101;
                $locationData = $this->rows($data);
                if (!empty($locationData)) {
                    $locationList[$i]['store_name'] = $store->store_name;
                    $locationList[$i++]['location'] = $locationData;
                }
            }
        }
        return $locationList;
    }


    public function getStoreLocationWithoutProcessNDept()
    {
        $locationList = array();
        $storeList = $this->getStoreNames();
        if (!empty($storeList)) {
            $i = 0;
            foreach ($storeList as $store) {

                $data['tableName'] = $this->locationMaster;
                $data['where']['store_name'] = $store->store_name;
                $data['where']['store_type !='] = 101;
                $data['where']['store_type !='] = 102;
                $locationData = $this->rows($data);
                if (!empty($locationData)) {
                    $locationList[$i]['store_name'] = $store->store_name;
                    $locationList[$i++]['location'] = $locationData;
                }
            }
        }
        return $locationList;
    }
    
    public function getItemStockBatchWise($data)
    {
        $stock_effect = (isset($data['stock_effect']))?$data['stock_effect']:1;

        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "stock_transaction.item_id, item_master.item_code, item_master.item_name,item_master.item_type, item_master.full_name as item_full_name, SUM(stock_transaction.qty) as qty, stock_transaction.batch_no, stock_transaction.ref_batch, stock_transaction.stock_type, stock_transaction.location_id, lm.location, lm.store_name,mir_transaction.mill_heat_no,stock_transaction.ref_no,stock_transaction.ref_date";
		
		$queryData['leftJoin']['location_master as lm'] = "lm.id=stock_transaction.location_id";
        $queryData['leftJoin']['item_master'] = "stock_transaction.item_id = item_master.id";
        $queryData['leftJoin']['mir_transaction'] = "mir_transaction.batch_no = stock_transaction.batch_no AND mir_transaction.item_id = stock_transaction.item_id";

        $queryData['where']['stock_transaction.stock_effect'] = $stock_effect;

        if(!empty($data['item_id'])): 
            $queryData['where']['stock_transaction.item_id'] = $data['item_id'];           
        endif;

        if(!empty($data['location_id'])):
            $queryData['where']['stock_transaction.location_id'] = $data['location_id'];
        endif;

        if(!empty($data['batch_no'])):
            $queryData['where']['stock_transaction.batch_no'] = $data['batch_no'];
        endif;

        if(!empty($data['stock_type'])): 
            $queryData['where']['stock_transaction.stock_type'] = $data['stock_type']; 
        endif;        

        if(!empty($data['ref_type'])):
            $queryData['where']['stock_transaction.ref_type'] = $data['ref_type'];
        endif;

        if(!empty($data['ref_id'])):
            $queryData['where']['stock_transaction.ref_id'] = $data['ref_id'];
        endif;

        if(!empty($data['trans_ref_id'])):
            $queryData['where']['stock_transaction.trans_ref_id'] = $data['trans_ref_id'];
        endif;

        if(!empty($data['ref_no'])):
            $queryData['where']['stock_transaction.ref_no'] = $data['ref_no'];
        endif;
        
        if(!empty($data['customWhere'])):
            $queryData['customWhere'][] = $data['customWhere'];
        endif;

        if(!empty($data['stock_required'])):
            $queryData['having'][] = 'SUM(stock_transaction.qty) > 0';
        endif;
        if(!isset($data['location_ref_id'])){
            $queryData['where']['lm.other_ref'] = 0;
        }
        $queryData['group_by'][] = "stock_transaction.location_id";
		$queryData['group_by'][] = "stock_transaction.batch_no";
        $queryData['group_by'][] = "stock_transaction.item_id";
		$queryData['order_by']['lm.location'] = "ASC";

        if(isset($data['single_row']) && $data['single_row'] == 1):
            $stockData = $this->row($queryData);
        else:
		    $stockData = $this->rows($queryData);
        endif;
        return $stockData;
    }

    public function getStockTransRow($id){
        $queryData = array();
        $queryData['tableName'] = "stock_transaction";
        $queryData['where']['id'] = $id;
        return $this->row($queryData);
    }

    public function getStockDTRows($data){ 
        $data['tableName'] = $this->stockTransaction;
        $data['select'] = 'stock_transaction.*,SUM(stock_transaction.qty) as current_stock,location_master.store_name,location_master.location,item_master.item_name,item_master.item_code,item_category.category_name';
        $data['join']['location_master'] = 'location_master.id = stock_transaction.location_id';
        $data['join']['item_master'] = 'item_master.id = stock_transaction.item_id';
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        if(!empty($data['location_id']) && $data['location_id'] != ""){$data['where']['stock_transaction.location_id'] = $data['location_id'];}
        $data['group_by'][] = 'stock_transaction.item_id';
        $data['group_by'][] = 'stock_transaction.location_id';
        if($data['stock_type'] == 1){ $data['having'][] = 'current_stock > 0';}
        elseif($data['stock_type'] == 2){$data['having'][] = 'current_stock <= 0';}

        $data['searchCol'][] = '';
        $data['searchCol'][] = '';
        $data['searchCol'][] = 'item_master.item_code';
        $data['searchCol'][] = 'item_master.item_name';
		$data['searchCol'][] = 'item_category.category_name';
        $data['searchCol'][] = 'location_master.store_name';
        $data['searchCol'][] = 'location_master.location';
        $data['searchCol'][] = '';
        
		$columns =array('','','item_master.item_code','item_master.item_name','item_category.category_name','location_master.store_name','location_master.location','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }
    
    public function getStockDTRowsAll($data){ 
        $data['tableName'] = $this->itemMaster;
        $data['select'] = 'stock_transaction.*,SUM(stock_transaction.qty) as current_stock,item_master.item_name,item_master.item_code,item_category.category_name';
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $data['crossJoin']['stock_transaction'] = "stock_transaction.item_id = item_master.id";
        $data['group_by'][] = 'stock_transaction.item_id';
        $data['having'][] = 'current_stock <= 0';

        $data['searchCol'][] = '';
        $data['searchCol'][] = '';
        $data['searchCol'][] = 'item_master.item_code';
        $data['searchCol'][] = 'item_master.item_name';
		$data['searchCol'][] = 'item_category.category_name';
        $data['searchCol'][] = 'location_master.store_name';
        $data['searchCol'][] = 'location_master.location';
        $data['searchCol'][] = '';
        
		$columns =array('','','item_master.item_code','item_master.item_name','item_category.category_name','','','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }
    
    //Created By Karmi @02/05/2022
    public function getSubStore($id)
    {
        $data['where']['ref_id'] = $id;
        $data['tableName'] = $this->locationMaster;
        $result = $this->rows($data);
        return $result;
    }
    
    function getItemStockOnLocation($data){
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(qty) as qty";
        $queryData['where']['item_id'] = $data['item_id'];
        $queryData['where']['location_id'] = $data['location_id'];
        $queryData['where']['batch_no'] = $data['batch_no'];
        return $this->row($queryData);
    }
    
    public function getItemHistory($item_id, $from_date="", $to_date=""){
        $queryData['tableName'] = $this->stockTransaction;
        $queryData['select'] = 'stock_transaction.*,item_master.item_code,item_master.item_name,location_master.location';
        $queryData['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
        $queryData['leftJoin']['location_master'] = "location_master.id = stock_transaction.location_id";
        $queryData['where']['stock_transaction.item_id'] = $item_id;
        if(!empty($from_date) && !empty($to_date)){ $queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$from_date."' AND '".$to_date."'"; }
        $queryData['order_by']['stock_transaction.ref_date'] = 'ASC';
        $queryData['order_by']['stock_transaction.trans_type'] = 'ASC';
		$result = $this->rows($queryData);
		return $result;
    }
    
    public function getItemStock($item_id){
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(qty) as qty";
        $queryData['where']['item_id'] = $item_id;
		$queryData['where']['stock_effect'] = 1;
        if(!empty($stock_type)){$queryData['where']['stock_type'] = $stock_type;}
        return $this->row($queryData);
    }
    
	public function getStockByItem($postData)
    {
		$itmData = $this->item->getItem($postData['item_id']);
		
        $thead = '<tr><th colspan="6">Product : (' . $itmData->item_code . ') ' . $itmData->item_name . '</th></tr>
					<tr>
                        <th style="width:5%;">Action</th>
						<th>#</th>
						<th style="text-align:left !important;">Store</th>
						<th>Location</th>
						<th>Batch</th>
						<th>Current Stock</th>
					</tr>';
        $tbody = '';$i = 1;
		
		$queryData['tableName'] = "stock_transaction";
		$queryData['select'] = "SUM(stock_transaction.qty) as qty,stock_transaction.batch_no,stock_transaction.location_id, lm.location, lm.store_name,lm.store_type,stock_transaction.size";
		//$queryData['select'] .= "CASH WHEN (item_master.item_type!=2 AND stock_transaction.size IS NULL) THEN 'NO' ELSE stock_transaction.size END as size";
		//$queryData['join']['item_master'] = "item_master.id=stock_transaction.item_id";
		if($itmData->item_type == 3){
		    $queryData['select'] .= ",mir_transaction.mill_heat_no";
            $queryData['leftJoin']['mir_transaction'] = "mir_transaction.batch_no = stock_transaction.batch_no AND mir_transaction.item_id = stock_transaction.item_id AND mir_transaction.is_delete = 0";
		}
		$queryData['leftJoin']['location_master as lm'] = "lm.id=stock_transaction.location_id";
		$queryData['where']['stock_transaction.item_id'] = $postData['item_id'];
		if($postData['stock_type'] != 'WIP')
		{
			$queryData['where']['stock_transaction.stock_type'] = $postData['stock_type'];
			$queryData['where']['stock_effect'] = 1;
			//$queryData['where']['lm.other_ref'] = 0;
		}
		else
		{
			$queryData['where']['stock_transaction.stock_effect'] = 0;
			$queryData['where']['lm.other_ref > '] = 0;
		}
		$queryData['having'][] = "qty>0";
		$queryData['group_by'][] = "stock_transaction.location_id";
		$queryData['group_by'][] = "stock_transaction.batch_no";
		$queryData['order_by']['lm.location'] = "ASC";
		$stockData = $this->rows($queryData);
        //$this->printQuery();exit;
        if (!empty($stockData)) {
            foreach ($stockData as $row) {
				$stfParam = "{'location_id':" . $row->location_id . ",'item_id':" . $postData['item_id'] . ",'stock_qty':" . floatVal($row->qty) . ",'batch_no':'" . $row->batch_no . "','modal_id' : 'modal-md', 'form_id' : 'stockTransfer', 'title' : 'Stock Transfer','fnSave' : 'saveStockTransfer'}";
                $stfBtn="";
                if($row->store_type == 0){
				    $stfBtn = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Stock Transfer" flow="down" onclick="stockTransfer(' . $stfParam . ');"><i class="ti-control-shuffle" ></i></a>';
                }
				$size = (!empty($row->size)) ? " (".$row->size.")" : '';
				$actionBtn = getActionButton($stfBtn);
				$tbody .= '<tr>';
				$tbody .= '<td class="text-center">' . $actionBtn . '</td>';
				$tbody .= '<td class="text-center">' . $i++ . '</td>';
				$tbody .= '<td>' . $row->store_name . '</td>';
				$tbody .= '<td>' . $row->location . '</td>';
				$tbody .= '<td>' . $row->batch_no . $size .(($itmData->item_type == 3)?' [ Heat No : '.$row->mill_heat_no.']':''). '</td>';
				$tbody .= '<td>' . floatVal($row->qty) . '</td>';
				$tbody .= '</tr>';
            }
        }
        return ['status' => 1, 'thead' => $thead, 'tbody' => $tbody];
    }

    public function getBatchNoList($data){
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(qty) as qty,batch_no";
        $queryData['where']['item_id'] = $data['item_id'];
        $queryData['where']['location_id'] = $data['location_id'];
        $queryData['group_by'][] = "batch_no";
        return $this->rows($queryData);
    }

    public function getMainStoreIdByLevel($mainstore_level){
        $data['where']['store_level'] = $mainstore_level;
        $data['tableName'] = $this->locationMaster;
        $result = $this->row($data);
        return $result;
    }

    public function getPackingStoreList(){
        $queryData = array();
        $queryData['tableName'] = "location_master";
        $queryData['select']='( CASE WHEN( SUBSTRING(store_level, 1, 3) = "8.8" ) THEN id END)AS id,(CASE WHEN( SUBSTRING(store_level, 1, 3) = "8.8" ) THEN location END ) AS location, ( CASE WHEN( SUBSTRING(store_level, 1, 3) = "8.8" ) THEN store_name END ) AS store_name';
        $queryData['where']['location_master.final_location '] =1;
        $queryData['having'][]  ="id > 0";
        return $this->rows($queryData);
    }
    
    /* Use Only Delivery Challan, Sales Invoice and Credit Note*/
    public function batchWiseItemStock($data){
        $item_id = $data['item_id'];$location_id="(".$this->RTD_STORE->id.",".$this->SCRAP_STORE->id.")";

        if(!empty($data['batch_no'])):
            $where = "WHERE ((st.batch_no IN ('".implode("','",$data['batch_no'])."') AND st.location_id IN (".implode(",",$data['location_id']).")) OR  st.qty > 0)";
        else:
            $where = "WHERE st.qty > 0";
        endif;

        $result = $this->db->query("SELECT st.* FROM (
            SELECT SUM(stock_transaction.qty) AS qty, stock_transaction.batch_no, stock_transaction.trans_ref_id, stock_transaction.location_id, location_master.store_name, location_master.location
            FROM stock_transaction 
            LEFT JOIN location_master ON location_master.id = stock_transaction.location_id
            WHERE stock_transaction.item_id = $item_id
            AND stock_transaction.location_id IN $location_id
            AND stock_transaction.is_delete = 0
            GROUP BY stock_transaction.batch_no, stock_transaction.location_id
            ORDER BY stock_transaction.id ASC
        ) as st $where")->result();        
       // print_r($this->db->last_query());exit;
        $i=1;$tbody="";
        if(!empty($result)):
            $batch_no = array();$batch_qty = array();$location_id = array();
            $batch_no = (!is_array($data['batch_no']))?explode(",",$data['batch_no']):$data['batch_no'];
            $batch_qty = (!is_array($data['batch_qty']))?explode(",",$data['batch_qty']):$data['batch_qty'];
            $location_id = (!is_array($data['location_id']))?explode(",",$data['location_id']):$data['location_id'];

            foreach($result as $row):                
                if($row->qty > 0 || !empty($batch_no) && in_array($row->batch_no,$batch_no)):
                    if(!empty($batch_no) && in_array($row->batch_no,$batch_no) && in_array($row->location_id,$location_id)):
                        $qty = 0;
                        $qty = $batch_qty[array_search($row->batch_no,$batch_no)];
                        $cl_stock = (!empty($data['trans_id']))?floatVal($row->qty + $qty):floatVal($row->qty);
                    else:
                        $qty = "0";
                        $cl_stock = floatVal($row->qty);
                    endif;                                
                    
                    $tbody .= '<tr>';
                        $tbody .= '<td class="text-center">'.$i.'</td>';
                        $tbody .= '<td>['.$row->store_name.'] '.$row->location.'</td>';
                        $tbody .= '<td>'.$row->batch_no.'</td>';
                        $tbody .= '<td>'.floatVal($row->qty).'</td>';
                        $tbody .= '<td>
                            <input type="number" name="batch_quantity[]" class="form-control batchQty" data-rowid="'.$i.'" data-cl_stock="'.$cl_stock.'" min="0" value="'.$qty.'" />
                            <input type="hidden" name="tc_number[]" id="tc_no'.$i.'" value="" />
							<input type="hidden" name="batch_number[]" id="batch_number'.$i.'" value="'.$row->batch_no.'" />
                            <input type="hidden" name="location[]" id="location'.$i.'" value="'.$row->location_id.'" />
                            <input type="hidden" name="packing_transid[]" value="'.$row->trans_ref_id.'">
                            <div class="error batch_qty'.$i.'"></div>
                        </td>';
                    $tbody .= '</tr>';
                    $i++;
                endif;
            endforeach;
        else:
            $tbody = '<tr><td class="text-center" colspan="5">No Data Found.</td></tr>';
        endif;

        return ['status'=>1,'batchData'=>$tbody];
    }
    
        // Created By JP @ 09082022 11:15 AM
    public function getItemStockGeneral($postData){
        if(!empty($postData['item_id']))
        {
            $queryData['tableName'] = "stock_transaction";
            $queryData['select'] = "SUM(qty) as qty";
            $queryData['where']['item_id'] = $postData['item_id'];
            if(!empty($postData['location_id'])){$queryData['where']['location_id'] = $postData['location_id'];}
            if(!empty($postData['batch_no'])){$queryData['where']['batch_no'] = $postData['batch_no'];}
            return $this->row($queryData);
        }
    }
    
    /*Created By @Raj:- 25-09-2025*/
	public function getStockTransData($data = array()){
        $stock_effect = (isset($data['stock_effect']))?$data['stock_effect']:1;

        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "stock_transaction.id, stock_transaction.item_id, item_master.item_code, item_master.item_name,item_master.item_type, item_master.full_name as item_full_name, stock_transaction.qty, stock_transaction.batch_no, stock_transaction.ref_batch, stock_transaction.stock_type, stock_transaction.location_id, lm.location, lm.store_name,stock_transaction.ref_no,stock_transaction.ref_date";
		
		$queryData['leftJoin']['location_master as lm'] = "lm.id=stock_transaction.location_id";
        $queryData['leftJoin']['item_master'] = "stock_transaction.item_id = item_master.id";

        $queryData['where']['stock_transaction.stock_effect'] = $stock_effect;

        if(!empty($data['ref_type'])):
            $queryData['where']['stock_transaction.ref_type'] = $data['ref_type'];
        endif;

        if(!empty($data['ref_id'])):
            $queryData['where']['stock_transaction.ref_id'] = $data['ref_id'];
        endif;
        
        if(!empty($data['customWhere'])):
            $queryData['customWhere'][] = $data['customWhere'];
        endif;
		
		if(!empty($data['is_group'])){			
			$queryData['group_by'][] = "stock_transaction.location_id";
			$queryData['group_by'][] = "stock_transaction.batch_no";
			$queryData['group_by'][] = "stock_transaction.item_id";
		}
		$queryData['order_by']['lm.location'] = "ASC";

        if(isset($data['single_row']) && $data['single_row'] == 1):
            return $this->row($queryData);
        else:
		    return $this->rows($queryData);
        endif;
    }
}
?>