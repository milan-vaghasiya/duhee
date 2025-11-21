<?php
class InstrumentModel extends MasterModel{
    private $itemMaster = "item_master";
    private $stockTrans = "stock_transaction";

    public function getDTRows($data){
        $data['tableName'] = $this->itemMaster;
        return $this->pagingRows($data);
    }
    
    public function getSerialWiseDTRows($data){
        $data['tableName'] = $this->stockTrans;
        $data['select'] = "stock_transaction.batch_no,item_master.id,item_master.item_name,calibration.make_brand,item_master.part_no,item_master.cal_reminder,calibration.cal_by,calibration.cal_date,calibration.next_cal_date";
        $data['join']['item_master'] = "item_master.id = stock_transaction.item_id";
        $data['leftJoin']['calibration'] = "stock_transaction.item_id = calibration.item_id AND stock_transaction.batch_no = calibration.batch_no AND calibration.is_active = 1 AND calibration.is_delete = 0";
        $data['where']['stock_transaction.item_id'] = $data['item_id'];
        $data['where']['item_master.item_type'] = $data['item_type'];
        $data['where']['item_master.is_delete'] = 0;
        $data['group_by'][] = 'stock_transaction.batch_no';
        $data['having'][] = 'SUM(stock_transaction.qty) > 0';

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "stock_transaction.batch_no";
        $data['searchCol'][] = "item_master.item_name";
        $data['serachCol'][] = "item_master.make_brand";
        $data['serachCol'][] = "item_master.part_no";

		$columns =array('','','stock_transaction.batch_no','item_master.item_name','item_master.make_brand','item_master.part_no');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getItem($id){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = 'item_master.*,item_category.category_name,item_category.tool_type as cat_code';
        $data['leftJoin']['item_category'] = 'item_category.id = item_master.category_id';
        $data['where']['item_master.id'] = $id;
        return $this->row($data);
    }

    public function save($data){
       
        $chkDuplicate = array();
        if($data['item_type'] == 6){
            $msg = 'Instrument';
            $chkDuplicate=[
                'id'=>$data['id'],
                'instrument_range'=>$data['instrument_range'],
                'least_count'=>$data['least_count'],
                'category_id'=>$data['category_id'],
                'item_type'=>$data['item_type'],
            ];
        }else{
            $msg = 'Gauge';
            $chkDuplicate=[
                'id'=>$data['id'],
                'size'=>$data['size'],
                'category_id'=>$data['category_id'],
                'item_type'=>$data['item_type'],
            ];
        }
        if($this->checkDuplicate($chkDuplicate) > 0):
            $errorMessage['item_name'] =  $msg."  is duplicate.";
            return ['status'=>0,'message'=>$errorMessage];
		else:
            if(empty($data['id'])){
                $data['store_id'] = $this->getNextItemNo(['item_type'=>$data['item_type'],'category_id'=>$data['category_id']]);
                $data['item_code'] = sprintf("%03d",$data['cat_code']).'-'.sprintf("%02d",$data['store_id']);
            }
            $data['item_name'] = $data['full_name'] = $data['item_code'].' '.$data['cat_name'].' '.(($data['item_type']==6)?$data['instrument_range']:$data['size']);unset($data['cat_name'],$data['cat_code']);

            return $this->store($this->itemMaster,$data);
        endif;
	}

    public function checkDuplicate($postData){
        $data['tableName'] = $this->itemMaster;
        if(!empty($postData['item_type'])){$data['where']['item_type'] = $postData['item_type'];}
        if(!empty($postData['category_id'])){$data['where']['category_id'] = $postData['category_id'];}
        if(!empty($postData['size'])){$data['where']['size'] = $postData['size'];}
        if(!empty($postData['instrument_range'])){$data['where']['instrument_range'] = $postData['instrument_range'];}
        if(!empty($postData['least_count'])){$data['where']['least_count'] = $postData['least_count'];}
        if(!empty($postData['id']))
            $data['where']['id !='] = $postData['id'];

        return $this->numRows($data);
    }

    public function delete($id){
		$itemData = $this->getItem($id);
        return $this->trash($this->itemMaster,['id'=>$id]);
    }

    public function getDataForGenerateCode($postData){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = 'item_master.item_code,MAX(store_id) as serial_no,item_master.least_count,item_master.instrument_range,item_category.category_name,item_master.item_type,item_master.instrument_range,item_master.size,item_category.tool_type as category_code';
        $data['leftJoin']['item_category'] = 'item_category.id = item_master.category_id';
        if(!empty($postData['category_id'])){ $data['where']['item_master.category_id'] =$postData['category_id']; }
        if(!empty($postData['instrument_range'])){ $data['where']['item_master.instrument_range'] =$postData['instrument_range']; }
        if(!empty($postData['least_count'])){ $data['where']['item_master.least_count'] =$postData['least_count']; }
        if(!empty($postData['item_type'])){ $data['where']['item_master.item_type'] =$postData['item_type']; }
        if(!empty($postData['size'])){ $data['where']['item_master.size'] =$postData['size']; }
        if(!empty($postData['category_code'])){ $data['where']['item_category.tool_type'] =$postData['category_code']; }
        if(!empty($postData['item_code'])){ $data['where']['item_master.item_code'] =$postData['item_code']; }
        return $this->row($data);
    }

    public function getNextItemNo($postData){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "MAX(store_id) as item_code";
        if(!empty($postData['item_type'])){ $data['where']['item_master.item_type'] =$postData['item_type']; }
        if(!empty($postData['category_id'])){ $data['where']['item_master.category_id'] =$postData['category_id']; }
        $maxNo = $this->specificRow($data)->item_code;
        $nextItemCode = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextItemCode;
    }

    public function getInstrumentByCode($item_code){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = 'item_master.*,item_category.category_name,item_category.tool_type as cat_code';
        $data['leftJoin']['item_category'] = 'item_category.id = item_master.category_id';
        $data['where_in']['item_master.part_no'] = $item_code;
        return $this->rows($data);
    }

    public function getInstrumentCodeWiseList(){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = 'item_master.item_code,MAX(store_id) as serial_no,item_master.least_count,item_master.instrument_range,item_category.category_name,item_master.item_type,item_master.instrument_range,item_master.size,item_category.tool_type as category_code';
        $data['leftJoin']['item_category'] = 'item_category.id = item_master.category_id';
        $data['where_in']['item_type'] = '6,7';
        $data['group_by'][]='category_id';
        $data['group_by'][] = 'item_code';
        return $this->rows($data);
    }

	
}