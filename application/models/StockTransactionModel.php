<?php
class StockTransactionModel extends MasterModel{
    private $locationMaster = "location_master";
    private $stockTransaction = "stock_transaction";


    public function getStoreLocationList($postData){

        $queryData['tableName'] = $this->locationMaster;
        if(isset($postData['store_type'])){ $queryData['where_in']['location_master.store_type'] = $postData['store_type'];}
        if(isset($postData['final_location'])){ $queryData['where_in']['location_master.final_location'] = $postData['final_location'];}
        if(isset($postData['prd_movement'])){$queryData['where']['location_master.prd_movement'] = $postData['prd_movement'];}
        $result = $this->rows($queryData);

        $storeGroupedArray = array();
        if(isset($postData['group_store_opt'])){
            foreach($result as $store){
                $storeGroupedArray[$store->store_name][] = $store;
            }
        }
        return ['result'=>$result,'storeGroupedArray'=>$storeGroupedArray];
    }

    public function getCurrentSizeOfRegindingItems($postData){
        $queryData['tableName'] = "stock_transaction";
		$queryData['select'] = "stock_transaction.size";
        $queryData['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
		$queryData['where']['item_id'] = $postData['item_id'];
		if(!empty( $postData['batch_no'])){$queryData['where']['batch_no'] = $postData['batch_no'];}
		if(!empty( $postData['ref_type'])){$queryData['where']['ref_type'] = $postData['ref_type']; }
        $queryData['order_by']['stock_transaction.id']='DESC';
        $queryData['limit'][] = 1;

        $stockData = $this->row($queryData);
        return $stockData;
    }
   
}
?>